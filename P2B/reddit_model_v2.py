from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext

# IMPORT OTHER MODULES HERE
flag = 0
from pyspark.sql.functions import udf
from pyspark.sql.types import *
from pyspark.ml.feature import CountVectorizer

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
    associated = join(comments, label)
    withngrams = associated.withColumn("ngrams", makeNgrams_udf(associated['body']))
    withplabels = withngrams.withColumn("plabel", pLabel_udf(withngrams['labeldjt']))
    withpnlabels = withplabels.withColumn("nlabel", nLabel_udf(withplabels['labeldjt'])).select("id","ngrams","plabel","nlabel")
    withpnlabels.show()
    '''
    cv = CountVectorizer(binary=True, inputCol="ngrams", outputCol="vectors")
    model = cv.fit(withngrams)
    model.transform(withngrams).show()
	'''



if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    import cleantext
    # load csv file

    main(sqlContext)
