#!/bin/bash
#source ~/.bashrc
export HADOOP_OPTS=-Xmx1000m
#lasthour=`date -d last-hour +%Y-%m-%d-%H`
lasthour=`date -d "-10 hours" +%Y-%m-%d-%H`
#lasthour=`date -d last-hour +%Y-%m-%d-%H`
day=`date -d last-hour +%Y-%m-%d`
rootdir=`dirname $0`
cd $rootdir

if [ $# != 2 ]; then
input="/home/hdp-btime/project/rec/statistics/$day/*$lasthour*"
output="/home/hdp-btime/job/rec_statistic_hour/${lasthour}"
else
input=$1
output=$2
fi

mapper=rec_statistics_map.php
reducer=rec_statistics_reduce.php
jobname=liuxiaohui-pd_rec_statistic_hour
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
-file "${rootdir}/${mapper}" \
-file "${rootdir}/${reducer}" \
#-input "/home/hdp-guanggao/project/theten/user_feedback/news_app_shixian/${today}/*" \
#-D map.output.key.field.separator="	" \
#-D mapred.text.key.partitioner.options=-k1,1 \
#-D mapred.output.key.comparator.class=org.apache.hadoop.mapred.lib.KeyFieldBasedComparator \
#-D stream.num.map.output.key.fields=2 \
#-D mapred.text.key.comparator.options="-k1,1 -k2,2" \
#-partitioner org.apache.hadoop.mapred.lib.KeyFieldBasedPartitioner \
