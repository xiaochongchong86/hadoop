#!/bin/bash
#source ~/.bashrc
export HADOOP_OPTS=-Xmx1000m
lastday=`date -d last-day +%Y-%m-%d`
today=`date +%Y-%m-%d`
#echo "sh pwd:"$0
#rootdir=`dirname $PWD/${0}`
rootdir=`dirname $0`
echo $rootdir
cd $rootdir
 
if [ $# != 1 ]; then
    echo $0 "day"
    exit
else
input="/home/hdp-btime/project/click/$1"
output="/home/hdp-btime/job/ssp_yr/$1"
fi

mapper=rec_statistics_map.php
reducer=rec_statistics_reduce.php
jobname=liuxiaohui-pd_ssp_yr_$1
hadoop dfs -rmr $output
hadoop streaming \
-D mapred.reduce.tasks=1 \
-D mapred.job.priority=HIGH \
-D mapred.job.name=$jobname \
-D mapred.reduce.max.attempts=10 \
-D mapred.job.max.reduce.running=30 \
-D mapred.tasktracker.expiry.interval=30 \
-D mapred.task.timeout=0 \
-input $input \
-output $output \
-mapper "php ${mapper}" \
-reducer "php ${reducer}" \
-file "${mapper}" \
-file "${reducer}" \
#-input "/home/hdp-guanggao/project/theten/user_feedback/news_app_shixian/${today}/*" \
#-D map.output.key.field.separator="	" \
#-D mapred.text.key.partitioner.options=-k1,1 \
#-D mapred.output.key.comparator.class=org.apache.hadoop.mapred.lib.KeyFieldBasedComparator \
#-D stream.num.map.output.key.fields=2 \
#-D mapred.text.key.comparator.options="-k1,1 -k2,2" \
#-partitioner org.apache.hadoop.mapred.lib.KeyFieldBasedPartitioner \
