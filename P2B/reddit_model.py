from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext
from pyspark.sql import functions as F

from pyspark.ml.classification import LogisticRegression
from pyspark.ml.tuning import CrossValidator, ParamGridBuilder, CrossValidatorModel
from pyspark.ml.evaluation import BinaryClassificationEvaluator

from pyspark.sql.functions import udf, col
from pyspark.sql.types import StringType, ArrayType
from pyspark.ml.feature import CountVectorizer

training = False
read_raw = False

# IMPORT OTHER MODULES HERE
def associated(comments, label):
	# task 2
	return comments.join(label, comments.id == label.Input_id, 'inner')

def sanitize(text):
	ngrams = []
	cleantext = __import__("cleantext")
	tmpgrams = cleantext.sanitize(text)
	ngrams = tmpgrams[1] + ' ' + tmpgrams[2] + ' ' + tmpgrams[3]
	return ngrams.split(' ')

def process_id(test):
    return test[3:]

# define UDF
sanitize_udf = udf(sanitize, ArrayType(StringType()))
process_id_udf = udf(process_id, StringType())

def main(context):
    """Main function takes a Spark SQL context."""
    # YOUR CODE HERE
    # YOU MAY ADD OTHER FUNCTIONS AS NEEDED

    if(read_raw):
        comments = sqlContext.read.json('comments-minimal.json.bz2')
        submissions = sqlContext.read.json('submissions.json.bz2')
        label = sqlContext.read.load('labeled_data.csv', format = 'csv', sep = ',',header="true")

        comments.write.parquet('comments')
        submissions.write.parquet('submissions')
        label.write.parquet('label')
    else:
        comments = context.read.load('comments')
        submissions = context.read.load('submissions')
        label = context.read.load('label')

    associate = associated(comments, label).select(col('id'), col('body'), col('labeldjt'))

    # task 4   
    newColumn = associate.withColumn('ngrams', sanitize_udf(associate['body']))

    # task 6A
    cv = CountVectorizer(inputCol = 'ngrams', outputCol = "features", binary = True)

    model = cv.fit(newColumn)
    tmp = model.transform(newColumn)

    # task 6B
    result = tmp.withColumn('poslabel', F.when(col('labeldjt') == 1, 1).otherwise(0)) #result with new column of positive and negative
    result = result.withColumn('neglabel', F.when(col('labeldjt') == -1, 1).otherwise(0))
    pos = result.select(col('poslabel').alias('label'), col('features'))
    neg = result.select(col('neglabel').alias('label'), col('features'))

    #result.show()

    if(training):
        # task 7
        # Initialize two logistic regression models.
        # Replace labelCol with the column containing the label, and featuresCol with the column containing the features.
        poslr = LogisticRegression(labelCol = "label", featuresCol = "features", maxIter = 10)
        neglr = LogisticRegression(labelCol = "label", featuresCol = "features", maxIter = 10)
        # This is a binary classifier so we need an evaluator that knows how to deal with binary classifiers.
        posEvaluator = BinaryClassificationEvaluator()
        negEvaluator = BinaryClassificationEvaluator()
        # There are a few parameters associated with logistic regression. We do not know what they are a priori.
        # We do a grid search to find the best parameters. We can replace [1.0] with a list of values to try.
        # We will assume the parameter is 1.0. Grid search takes forever.
        posParamGrid = ParamGridBuilder().addGrid(poslr.regParam, [1.0]).build()
        negParamGrid = ParamGridBuilder().addGrid(neglr.regParam, [1.0]).build()
        # We initialize a 5 fold cross-validation pipeline.
        posCrossval = CrossValidator(
            estimator = poslr,
            evaluator = posEvaluator,
            estimatorParamMaps = posParamGrid,
            numFolds = 5)
        negCrossval = CrossValidator(
            estimator = neglr,
            evaluator = negEvaluator,
            estimatorParamMaps = negParamGrid,
            numFolds = 5)
        # Although crossvalidation creates its own train/test sets for
        # tuning, we still need a labeled test set, because it is not
        # accessible from the crossvalidator (argh!)
        # Split the data 50/50
        posTrain, posTest = pos.randomSplit([0.5, 0.5])
        negTrain, negTest = neg.randomSplit([0.5, 0.5])
        # Train the models
        print("Training positive classifier...")
        posModel = posCrossval.fit(posTrain)
        print("Training negative classifier...")
        negModel = negCrossval.fit(negTrain)

        # Once we train the models, we don't want to do it again. We can save the models and load them again later.
        posModel.save("pos.model")
        negModel.save("neg.model")
    else:
        posModel = CrossValidatorModel.load('pos.model')
        negModel = CrossValidatorModel.load('neg.model')
        # task 8
        comments_tmp = comments.select(col('id'), col('link_id'), col('created_utc'), col('body'), col('author_flair_text'))
        comments_full = comments_tmp.withColumn('link_id', process_id_udf(comments_tmp['link_id']))
        submissions_full = submissions.select(col('id').alias('sub_id'), col('title'))

        com_sub = comments_full.join(submissions_full, comments_full.link_id == submissions_full.sub_id, 'inner')
        com_sub = com_sub.select(col('id'), col('link_id'), col('created_utc'), col('body'), col('author_flair_text'), col('title'))
        #com_sub.show()
        


    



if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    main(sqlContext)

