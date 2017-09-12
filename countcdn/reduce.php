<?php

$ad_info_arr = array();

while(($line = fgets(STDIN)) !== false)
{
    $line = trim($line);
    if (empty($line)) {
        continue;
    }

    $data = explode("\t", $line);
    if (count($data) != 2) {
        fwrite(STDERR,"reporter:counter:VerifyCounters,RColumnErr,1\n");
        continue;
    }

    list ($id_str, $count) = $data;

    if (isset($ad_info_arr[$id_str])) {
        $ad_info_arr[$id_str] += $count;
    } else {
        $ad_info_arr[$id_str] = $count;
    }
}

foreach ($ad_info_arr as $id_str => $count_arr) { 
    echo $id_str . "\t" . $count_arr . "\n";
}


