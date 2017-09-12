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

#$lastday = date("Y-m-d", time() - 86400);
$lastday = date("Y-m-d", time());
$input_dir = "hdfs://namenodefd1v.qss.zzzc.qihoo.net:9000/home/hdp-btime/project/click/$lastday";
$output_dir="hdfs://namenodefd1v.qss.zzzc.qihoo.net:9000/home/hdp-btime/job/ssp_yr/$lastday";
system("$pwd/quickmr_day_cron.sh $input_dir $output_dir", $ret);
if (!$ret) {
    
    print_formatter("hadoop run success($lastday)");
} else {
    print_formatter("hadoop run fail($lastday)");
    mail("liuxiaohui-pd@360.cn", "rec_statistics run day", "Hadoop run error:$lastday"); 
}

