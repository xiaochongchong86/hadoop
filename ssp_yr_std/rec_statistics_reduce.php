<?php

$ad_info_arr = array();

while(($line = fgets(STDIN)) !== false)
{
    $line = trim($line);
    if (empty($line)) {
        continue;
    }

    $data = explode("\t", $line);
    if (count($data) != 3) {
        fwrite(STDERR,"reporter:counter:VerifyCounters,RColumnErr,1\n");
        continue;
    }

    list ($id_str, $action, $count) = $data;

    if (isset($ad_info_arr[$id_str][$action])) {
        $ad_info_arr[$id_str][$action] += $count;
    } else {
        $ad_info_arr[$id_str][$action] = $count;
    }
}

foreach ($ad_info_arr as $id_str => $count_arr) {
    $view_count = 0;
    $click_count = 0;
    $ctr = 0;
    foreach ($count_arr as $action => $count) {
        if ($action == "view") {
            $view_count = $count;
        } else if ($action == "click") {
            $click_count = $count;
        }
    }

    echo $id_str . "\t" . $view_count . "\t" . $click_count . "\t" . round($click_count / $view_count, 4) . "\n";
}

