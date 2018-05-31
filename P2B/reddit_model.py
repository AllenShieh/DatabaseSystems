from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext

# IMPORT OTHER MODULES HERE
flag = 0

def main(context):
    """Main function takes a Spark SQL context."""
    # YOUR CODE HERE
    # YOU MAY ADD OTHER FUNCTIONS AS NEEDED

if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    # load csv file
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
    main(sqlContext)
