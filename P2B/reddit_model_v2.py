from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext

# IMPORT OTHER MODULES HERE
flag = 0
save = 0
from pyspark.sql.functions import *
from pyspark.sql.types import *
from pyspark.ml.feature import CountVectorizer, CountVectorizerModel
from pyspark.ml.classification import LogisticRegression
from pyspark.ml.tuning import CrossValidator, ParamGridBuilder, CrossValidatorModel
from pyspark.ml.evaluation import BinaryClassificationEvaluator
from pyspark.mllib.linalg import DenseVector

def join(comments, label):
	# task 2
	associated = comments.join(label, comments.id == label.Input_id, 'inner')
	return associated.select("id","body","labeldem","labelgop","labeldjt")

def makeNgrams(text):
    ngrams = cleantext.sanitize(text)
    concat = ngrams[1]+' '+ngrams[2]+' '+ngrams[3]
    return concat.split(' ')

def pLabel(raw):
	if(raw==1):
		return 1
	else:
		return 0

def nLabel(raw):
	if(raw==-1):
		return 1
	else:
		return 0

def getLinkid(text):
    if(len(text)>3):
        return text[3:]
    else:
        return text

def posTh(p):
    if(p[1]>0.2):
        return 1
    else:
        return 0

def negTh(p):
    if(p[1]>0.25):
        return 1
    else:
        return 0

makeNgrams_udf = udf(makeNgrams, ArrayType(StringType()))
pLabel_udf = udf(pLabel, IntegerType())
nLabel_udf = udf(nLabel, IntegerType())
getLinkid_udf = udf(getLinkid, StringType())
posTh_udf = udf(posTh, IntegerType())
negTh_udf = udf(negTh, IntegerType())

def main(sqlContext):
    """Main function takes a Spark SQL context."""
    # YOUR CODE HERE
    # YOU MAY ADD OTHER FUNCTIONS AS NEEDED

    # load files
    label = sqlContext.read.load("labeled_data.csv",format="csv", sep=",", inferSchema="true", header="true")
    if(flag):
        comments = sqlContext.read.json("comments-minimal.json.bz2")
        submissions = sqlContext.read.json("submissions.json.bz2")
        print("loading done")
        comments.write.parquet("comments_data")
        submissions.write.parquet("submissions_data")
        print("writing done")
    else:
        comments = sqlContext.read.parquet("comments_data")
        submissions = sqlContext.read.parquet("submissions_data")
        print("loading done")

    if(save):
        # task 7 starts here
        # join tables
        associated = join(comments, label)
        # process
        withngrams = associated.withColumn("ngrams", makeNgrams_udf(associated['body']))
        withplabels = withngrams.withColumn("poslabel", pLabel_udf(withngrams['labeldjt']))
        withpnlabels = withplabels.withColumn("neglabel", nLabel_udf(withplabels['labeldjt'])).select("id","ngrams","poslabel","neglabel")
        # withpnlabels.show()
        # vectorizer
        cv = CountVectorizer(binary=True, inputCol="ngrams", outputCol="features")
        model = cv.fit(withpnlabels)
        model.save("cv.model")
        # model.transform(withpnlabels).show()
        # specify models
        pos = model.transform(withpnlabels).select("id", col("poslabel").alias("label"), "features")
        neg = model.transform(withpnlabels).select("id", col("neglabel").alias("label"), "features")
        # pos.show()
        # neg.show()
        poslr = LogisticRegression(labelCol="label", featuresCol="features", maxIter=10)
        neglr = LogisticRegression(labelCol="label", featuresCol="features", maxIter=10)
        posEvaluator = BinaryClassificationEvaluator()
        negEvaluator = BinaryClassificationEvaluator()
        posParamGrid = ParamGridBuilder().addGrid(poslr.regParam, [1.0]).build()
        negParamGrid = ParamGridBuilder().addGrid(neglr.regParam, [1.0]).build()
        posCrossval = CrossValidator(
            estimator=poslr,
            evaluator=posEvaluator,
            estimatorParamMaps=posParamGrid,
            numFolds=5)
        negCrossval = CrossValidator(
            estimator=neglr,
            evaluator=negEvaluator,
            estimatorParamMaps=negParamGrid,
            numFolds=5)
        posTrain, posTest = pos.randomSplit([0.5, 0.5])
        negTrain, negTest = neg.randomSplit([0.5, 0.5])
        print("Training positive classifier...")
        posModel = posCrossval.fit(posTrain)
        print("Training negative classifier...")
        negModel = negCrossval.fit(negTrain)
        posModel.save("pos.model")
        negModel.save("neg.model")
    else:
        posModel = CrossValidatorModel.load("pos.model")
        negModel = CrossValidatorModel.load("neg.model")
        model = CountVectorizerModel.load("cv.model")
        print("model loaded")
        # task 8 starts here
        # comments.show()
        # submissions.show()
        temp_comments = comments.select("id", "link_id", "author_flair_text", "created_utc", "body")
        clean_comments = temp_comments.withColumn("true_id", getLinkid_udf(temp_comments['link_id']))
        clean_submissions = submissions.select(col("id").alias("sub_id"), "title")
        # clean_comments.show()
        # clean_submissions.show()
        com_sub = clean_comments.join(clean_submissions, clean_comments.true_id==clean_submissions.sub_id, "inner")

        # task 9 starts here
        filtered = com_sub.filter("body NOT LIKE '%/s%' and body NOT LIKE '&gt;%'")
        filtered_ngrams = filtered.withColumn("ngrams", makeNgrams_udf(filtered['body']))
        # get vectorizer or use the orginal one?
        # associated = join(comments, label)
        # withngrams = associated.withColumn("ngrams", makeNgrams_udf(associated['body']))
        # cv = CountVectorizer(binary=True, inputCol="ngrams", outputCol="features")
        # model = cv.fit(withngrams)
        # model.transform(filtered_ngrams).show()
        featuredata = model.transform(filtered_ngrams).select("id","author_flair_text","created_utc","sub_id","title","features")
        posResult = posModel.transform(featuredata)
        negResult = negModel.transform(featuredata)
        # posResult.select("probability").show(20,False)
        # negResult.show()
        poslabel = posResult.withColumn("positive",posTh_udf(posResult['probability'])).select("id", "author_flair_text", "created_utc", "title", "positive")
        poslabel.show()
        neglabel = negResult.withColumn("negtive",negTh_udf(negResult['probability'])).select(col("id").alias("nid"), "negtive")
        # how to combine these 2 tables???

        # task 10 starts here



if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    import cleantext
    # load csv file

    main(sqlContext)
