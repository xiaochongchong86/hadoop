<?php
date_default_timezone_set("Asia/Chongqing");
$pwd = dirname(__FILE__);
$hadoop = "/usr/bin/hadoop/software/hadoop/bin/hadoop";

function print_formatter($msg) {
    echo date("Y-m-d H:i:s", time()) . "\t$msg\n";
}

function post_data($timeline, $data, $id) 
{ 
    $url = "http://insight.zhoubian.360.cn:8360/api/savepool/".$id; 
    $json_array = array(); 
    $json_array["token"] = "o9gbx3dc3dgabxkmlgdx5l5r"; 
    $json_array["timeline"] = $timeline; 
    $json_array["data"] = implode(",", $data); 

    print_formatter("post_data:".json_encode($json_array));
    return post($url, json_encode($json_array)); 
}

function post($url, $data){ // 模拟提交数据函数
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址

    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 10); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        print_formatter('curl Errno:'.curl_error($curl));//捕抓异常
    }   
    //print_formatter("curl ret:$tmpInfo\t" . json_encode($data));
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据
}

$day = date("Y-m-d", time() - 3600);
$hour = date("Y-m-d-H", time() - 10*3600);
$hour_ts = strtotime(date("Ymd H:", time() - 10*3600) . "00:00");

$input_dir = "hdfs://namenodefd1v.qss.zzzc.qihoo.net:9000/home/hdp-btime/project/rec/statistics/$day/*$hour*";
$output_dir="hdfs://namenodefd1v.qss.zzzc.qihoo.net:9000/home/hdp-btime/job/rec_statistic_day/$hour";
system("$pwd/quickmr_hour.sh $input_dir $output_dir", $ret);
if (!$ret) {
    $shell_result = shell_exec("$hadoop fs -cat $output_dir/part*");
    $shell_result_arr = explode("\n", $shell_result);

    $output_arr = array();
    foreach ($shell_result_arr as $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }

        $line_arr = explode("\t", $line);
        if (count($line_arr) != 2) {
            continue;
        }

        $output_arr[$line_arr[0]] = $line_arr[1];
    }

    $result = array();
    $pv = isset($output_arr["pv"]) ? $output_arr["pv"] : 0;
    $result[] = $pv;
    $time_avg = isset($output_arr["time_avg"]) ? $output_arr["time_avg"] : 0;
    $result[] = $time_avg;
    $pv_less_10_percent = isset($output_arr["pv_less_10_percent"]) ? $output_arr["pv_less_10_percent"] : 0;
    $result[] = $pv_less_10_percent;
    $pv_10_100_percent = isset($output_arr["pv_10_100_percent"]) ? $output_arr["pv_10_100_percent"] : 0;
    $result[] = $pv_10_100_percent ;
    $pv_100_500_percent = isset($output_arr["pv_100_500_percent"]) ? $output_arr["pv_100_500_percent"] : 0;
    $result[] = $pv_100_500_percent ;
    $pv_more_500_percent = isset($output_arr["pv_more_500_percent"]) ? $output_arr["pv_more_500_percent"] : 0;
    $result[] = $pv_more_500_percent ;

    $ret = post_data($hour_ts, $result, 860);
    print_formatter("post data:".json_encode($ret));
    if (!$ret) {
        print_formatter("Callback error:$hour");
        mail("liuxiaohui-pd@360.cn", "rec_statistics run hour", "Callback error:$hour"); 
    }
} else {
    print_formatter("hadoop run fail($lastday)");
    mail("liuxiaohui-pd@360.cn", "rec_statistics run hour", "Hadoop run error:$hour"); 
}

