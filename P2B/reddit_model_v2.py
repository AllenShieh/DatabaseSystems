from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext

# IMPORT OTHER MODULES HERE
flag = 0
save = 0
from pyspark.sql.functions import *
from pyspark.sql.types import *
from pyspark.ml.feature import CountVectorizer
from pyspark.ml.classification import LogisticRegression
from pyspark.ml.tuning import CrossValidator, ParamGridBuilder, CrossValidatorModel
from pyspark.ml.evaluation import BinaryClassificationEvaluator

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

makeNgrams_udf = udf(makeNgrams, ArrayType(StringType()))
pLabel_udf = udf(pLabel, IntegerType())
nLabel_udf = udf(nLabel, IntegerType())

def main(sqlContext):
    """Main function takes a Spark SQL context."""
    # YOUR CODE HERE
    # YOU MAY ADD OTHER FUNCTIONS AS NEEDED

    if(save):
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
        model.transform(withpnlabels).show()

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
        print("model loaded")



if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    import cleantext
    # load csv file

    main(sqlContext)
