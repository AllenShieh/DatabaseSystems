from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext

# IMPORT OTHER MODULES HERE

def main(context):
    """Main function takes a Spark SQL context."""
    # YOUR CODE HERE
    # YOU MAY ADD OTHER FUNCTIONS AS NEEDED
    def associated(comments, label):
    	#task 2
    	associated = comments.join(label, comments.id == label.Input_id, 'inner')
    	return associated
    	#associated.show()

    comments = context.read.load('comments')
    submissions = context.read.load('submissions')
    label = context.read.load('label')

    associated = associated(comments, label)



if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    main(sqlContext)
