#/bin/bash
base_dir='/home/hdp-selfcdn/cong/result'
output_base_dir='/home/hdp-selfcdn/cong/result_out28'
logtype=cdn
hadoop_bin=/usr/bin/hadoop/software/hadoop/bin/hadoop
mapperfile=/home/hdp-selfcdn/cong/map.py
reducerfile=/home/hdp-selfcdn/cong/reduce.py
streaming_jar=/usr/bin/hadoop/software/hadoop-0.20.2.1U11/contrib/streaming/hadoop-0.20.2.1U11-streaming.jar
log_file=/home/hdp-selfcdn/countbashlog/log.$now_date-$now_hour.$logtype
output_path=$output_base_dir/
#hadoop fs -test -e $output_path
#if [ $? -eq 0 ];then
#   hadoop fs -cat $output_path/* | awk '{sum1+=$1;sum2+=$2;sum3+=$3}END{print sum1,sum2,sum3}'
#   exit 0 
#fi
#hadoop fs -rm -r $output_path > /dev/null 2>&1
#-D mapred.reduce.tasks=0 \
#-D stream.non.zero.exit.is.failure=false \
hadoop dfs -rmr $output_base_dir
hadoop streaming \
-D mapred.job.name=congcong_countcdnlog-$logtype \
-D mapred.job.priority=HIGH \
-D mapred.reduce.tasks=1 \
-D mapred.reduce.max.attempts=1 \
-D mapred.job.max.reduce.running=30 \
-D mapred.tasktracker.expiry.interval=30 \
-D mapred.task.timeout=0 \
-file  "$mapperfile" \
-file  "$reducerfile" \
-input  /home/hdp-selfcdn/webcdn/edge/20170828/ \
-output $output_path \
-reducer "$reducerfile" \
-mapper "$mapperfile"  

