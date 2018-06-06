from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext
from pyspark.sql import functions as F

from pyspark.ml.classification import LogisticRegression
from pyspark.ml.tuning import CrossValidator, ParamGridBuilder, CrossValidatorModel
from pyspark.ml.evaluation import BinaryClassificationEvaluator

from pyspark.sql.functions import udf, col, unix_timestamp
from pyspark.sql.types import StringType, ArrayType, IntegerType, DateType
from pyspark.ml.feature import CountVectorizer, CountVectorizerModel

from sklearn.metrics import roc_curve, auc
from pyspark.mllib.evaluation import BinaryClassificationMetrics as metric
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt

import time

training = 0
read_raw = 0
joinFull = 0

states = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', \
          'California', 'Colorado', 'Connecticut', 'Delaware', \
          'District of Columbia', 'Florida', 'Georgia', 'Hawaii', \
          'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', \
          'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', \
          'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', \
          'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', \
          'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', \
          'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', \
          'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', \
          'West Virginia', 'Wisconsin', 'Wyoming']


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

def threshold_pos(vector):
    if(vector[1] > 0.2):
        return 1
    else:
        return 0

def threshold_neg(vector):
    if(vector[1] > 0.25):
        return 1
    else:
        return 0

def plot_ROC(results_list, title):
    fpr = dict()
    tpr = dict()
    roc_auc = dict()

    y_test = [i[1] for i in results_list]
    y_score = [i[0] for i in results_list]

    fpr, tpr, _ = roc_curve(y_test, y_score)
    roc_auc = auc(fpr, tpr)

    # %matplotlib inline
    plt.figure()
    plt.plot(fpr, tpr, label='ROC curve (area = %0.2f)' % roc_auc)
    plt.plot([0, 1], [0, 1], 'k--')
    plt.xlim([0.0, 1.0])
    plt.ylim([0.0, 1.05])
    plt.xlabel('False Positive Rate')
    plt.ylabel('True Positive Rate')
    plt.title('ROC of ' + title)
    plt.legend(loc="lower right")
    plt.show()
    plt.savefig(title+'.png')


# define UDF
sanitize_udf = udf(sanitize, ArrayType(StringType()))
process_id_udf = udf(process_id, StringType())
threshold_pos_udf = udf(threshold_pos, IntegerType())
threshold_neg_udf = udf(threshold_neg, IntegerType())

def main(context):
    """Main function takes a Spark SQL context."""
    # YOUR CODE HERE
    # YOU MAY ADD OTHER FUNCTIONS AS NEEDED



    start = time.time()
    if(read_raw):
        comments = sqlContext.read.json('comments-minimal.json.bz2')
        submissions = sqlContext.read.json('submissions.json.bz2')
        label = sqlContext.read.load('labeled_data.csv', format = 'csv', sep = ',',header="true")
        print("load done")
        comments.write.parquet('comments')
        submissions.write.parquet('submissions')
        label.write.parquet('label')
    else:
        comments = context.read.load('comments')
        submissions = context.read.load('submissions')
        label = context.read.load('label')
    print("read data")
    #result.show()

    if(training):
        associate = associated(comments, label).select(col('id'), col('body'), col('labeldjt'))

        # task 4
        newColumn = associate.withColumn('ngrams', sanitize_udf(associate['body']))

        # task 6A
        cv = CountVectorizer(inputCol = 'ngrams', outputCol = "features", binary = True)
        model = cv.fit(newColumn)
        tmp = model.transform(newColumn)
        print("cv model")

        # task 6B
        result = tmp.withColumn('poslabel', F.when(col('labeldjt') == 1, 1).otherwise(0)) #result with new column of positive and negative
        result = result.withColumn('neglabel', F.when(col('labeldjt') == -1, 1).otherwise(0))
        pos = result.select(col('poslabel').alias('label'), col('features'))
        neg = result.select(col('neglabel').alias('label'), col('features'))

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
        model.save("cv.model")

        # posModel = CrossValidatorModel.load('pos.model')
        # negModel = CrossValidatorModel.load('neg.model')

        print("ROC")
        # ROC
        pos_trans = posModel.transform(posTest)
        neg_trans = negModel.transform(negTest)


        pos_results = pos_trans.select(['probability', 'label'])
        pos_trans_collect = pos_results.collect()
        pos_trans_results_list = [(float(i[0][0]), 1.0-float(i[1])) for i in pos_trans_collect]
        pos_scoreAndLabels = sc.parallelize(pos_trans_results_list)

        pos_metrics = metric(pos_scoreAndLabels)
        print("The ROC score of positive results is: ", pos_metrics.areaUnderROC)

        neg_results = neg_trans.select(['probability', 'label'])
        neg_trans_collect = neg_results.collect()
        neg_trans_results_list = [(float(i[0][0]), 1.0-float(i[1])) for i in neg_trans_collect]
        neg_scoreAndLabels = sc.parallelize(neg_trans_results_list)

        neg_metrics = metric(neg_scoreAndLabels)
        print("The ROC score of negative results is: ", neg_metrics.areaUnderROC)

        plot_ROC(pos_trans_results_list, 'positive_results')
        plot_ROC(neg_trans_results_list, 'negative_results')


    else:
        model = CountVectorizerModel.load('cv.model')
        posModel = CrossValidatorModel.load('pos.model')
        negModel = CrossValidatorModel.load('neg.model')
        print("model loaded")
        # task 8
        comments_tmp = comments.select(col('id'), col('link_id'), col('created_utc'), col('body'), col('author_flair_text'), col('score').alias('com_score'))
        comments_full = comments_tmp.withColumn('link_id', process_id_udf(comments_tmp['link_id']))
        submissions_full = submissions.select(col('id').alias('sub_id'), col('title'), col('score').alias('sub_score'))

        if(joinFull):
            com_sub = comments_full.join(submissions_full, comments_full.link_id == submissions_full.sub_id, 'inner')
            com_sub = com_sub.select(col('id'), col('title'), col('link_id'), col('created_utc'), col('body'), col('author_flair_text'), col('com_score'), col('sub_score'))
            com_sub.write.parquet('com_sub')
        else:
            com_sub = context.read.load('com_sub')# .sample(False, 0.01, None)
        # com_sub = com_sub.sample(False, 0.02, None)
        print('finish com_sub')
        # task 9
        filtered = com_sub.filter("body NOT LIKE '%/s%' and body NOT LIKE '&gt;%'")
        filtered_result = filtered.withColumn('ngrams', sanitize_udf(filtered['body']))

        feaResult = model.transform(filtered_result).select(col('id'), col('link_id'), col('created_utc'), \
                                    col('features'), col('author_flair_text'), col('com_score'), col('sub_score'), col('title'))

        posResult = posModel.transform(feaResult)
        negResult = negModel.transform(feaResult)
        print("transformed")

        pos = posResult.withColumn('pos', threshold_pos_udf(posResult['probability'])).select('id', 'created_utc', 'author_flair_text', 'pos', 'com_score', 'sub_score', 'title')
        neg = negResult.withColumn('neg', threshold_neg_udf(negResult['probability'])).select('id', 'created_utc', 'author_flair_text', 'neg', 'com_score', 'sub_score', 'title')
        #final_probs = pos.join(neg, pos.id == neg.id_neg, 'inner').select('id', 'created_utc', 'author_flair_text', 'title', 'pos', 'neg')
        #final_probs.show()
        #pos.write.parquet('pos')
        #neg.write.parquet('neg')
        print('finish task 9')

        # task 10

        # compute 1
        num_rows = pos.count()
        pos_filtered = pos.filter(pos.pos == 1)
        neg_filtered = neg.filter(neg.neg == 1)
        num_pos = pos_filtered.count()
        num_neg = neg_filtered.count()
        print('finish counting rows')

        print('Percentage of positive comments: {}'.format(num_pos / num_rows))
        print('Percentage of negative comments: {}'.format(num_neg / num_rows))
        print('finish compute 1')

        # compute 2
        pos_time = pos.withColumn('time', F.from_unixtime(col('created_utc')).cast(DateType()))
        neg_time = neg.withColumn('time', F.from_unixtime(col('created_utc')).cast(DateType()))

        num_pos_time = pos_time.groupBy('time').agg((F.sum('pos') / F.count('pos')).alias('Percentage of positive')).orderBy('time')
        num_neg_time = neg_time.groupBy('time').agg((F.sum('neg') / F.count('neg')).alias('Percentage of negative')).orderBy('time')

        num_pos_time.coalesce(1).write.mode("overwrite").format("com.databricks.spark.csv").option("header", "true").csv('num_pos_time')
        num_neg_time.coalesce(1).write.mode("overwrite").format("com.databricks.spark.csv").option("header", "true").csv('num_neg_time')
        print('finish compute 2')
        #print(num_pos_time)

        # compute 3
        state = sqlContext.createDataFrame(states, StringType())
        pos_state = pos.groupBy('author_flair_text').agg((F.sum('pos') / F.count('pos')).alias('Percentage of positive'))
        neg_state = neg.groupBy('author_flair_text').agg((F.sum('neg') / F.count('neg')).alias('Percentage of negative'))

        pos_state = pos_state.join(state, pos_state.author_flair_text == state.value, 'inner')
        pos_state = pos_state.na.drop(subset=['value'])
        pos_state = pos_state.select(col('author_flair_text').alias('state'), col('Percentage of positive').alias('Positive'))

        neg_state = neg_state.join(state, neg_state.author_flair_text == state.value, 'inner')
        neg_state = neg_state.na.drop(subset=['value'])
        neg_state = neg_state.select(col('author_flair_text').alias('state'), col('Percentage of negative').alias('Negative'))

        pos_state.coalesce(1).write.mode("overwrite").format("com.databricks.spark.csv").option("header", "true").csv('pos_state')
        neg_state.coalesce(1).write.mode("overwrite").format("com.databricks.spark.csv").option("header", "true").csv('neg_state')
        print('finish compute 3')
        #print(pos_state)

        # compute 4
        pos_com_score = pos.groupBy('com_score').agg((F.sum('pos') / F.count('pos')).alias('Percentage of positive')).orderBy('com_score')
        pos_sub_score = pos.groupBy('sub_score').agg((F.sum('pos') / F.count('pos')).alias('Percentage of positive')).orderBy('sub_score')
        neg_com_score = neg.groupBy('com_score').agg((F.sum('neg') / F.count('neg')).alias('Percentage of negative')).orderBy('com_score')
        neg_sub_score = neg.groupBy('sub_score').agg((F.sum('neg') / F.count('neg')).alias('Percentage of negative')).orderBy('sub_score')

        pos_com_score.coalesce(1).write.mode("overwrite").format("com.databricks.spark.csv").option("header", "true").csv('pos_com_score')
        pos_sub_score.coalesce(1).write.mode("overwrite").format("com.databricks.spark.csv").option("header", "true").csv('pos_sub_score')
        neg_com_score.coalesce(1).write.mode("overwrite").format("com.databricks.spark.csv").option("header", "true").csv('neg_com_score')
        neg_sub_score.coalesce(1).write.mode("overwrite").format("com.databricks.spark.csv").option("header", "true").csv('neg_sub_score')
        print('finish compute 4')

        # compute 5
        pos_story = pos.groupBy('title').agg((F.sum('pos') / F.count('pos')).alias('Percentage of positive')).orderBy(F.desc('Percentage of positive')).limit(10)
        neg_story = neg.groupBy('title').agg((F.sum('neg') / F.count('neg')).alias('Percentage of negative')).orderBy(F.desc('Percentage of negative')).limit(10)

        pos_story.coalesce(1).write.mode("overwrite").format("com.databricks.spark.csv").option("header", "true").csv('pos_story')
        neg_story.coalesce(1).write.mode("overwrite").format("com.databricks.spark.csv").option("header", "true").csv('neg_story')

        end = time.time()
        print('time consumed: {}'.format(end - start))






if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    main(sqlContext)
