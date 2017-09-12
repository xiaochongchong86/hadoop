<?php

$ad_info_arr = array();

while(($line = fgets(STDIN)) !== false)
{
    $line = trim($line);
    if (empty($line)) {
        continue;
    }

    $data = explode("\t", $line);
    if (count($data) < 4) {
        //fwrite(STDERR,"reporter:counter:VerifyCounters,ColumnErr,1\n");
        continue;
    }

    $hit = false;
    $action = "";
    $ad_id = "";
    $adp_id = "";
    $advertiser = "";
    $invalid_click = false;
    for ($i = 3; $i < count($data); $i++) {
        $tmp = explode(":", $data[$i]);
        if (count($tmp) != 2) {
            //fwrite(STDERR,"reporter:counter:VerifyCounters,ColumnErr,1\n");
            continue;
        }

        list($k, $v) = $tmp;
        if ($k == "action") {
            if ($v == "ad_view") {
                $action = "view";
            } else if ($v == "ad_click") {
                $action = "click";
            }
        } else if ($k == "ssp_src" && $v == "yr") {
            $hit = true;
        } else if ($k == "adp_id") {
            $adp_id = $v;
        } else if ($k == "ad_id") {
            $ad_id = $v;
        } else if ($k == "advertiser") {
            $advertiser = $v;
        } else if ($k == "link_list") {
            if ($v == "[null]") {
                $invalid_click = true;
            }
        }
    }

    if ($hit) {
        if ($ad_id != "" && $adp_id != "" && $action != "") {
            if ($action == "click" && $invalid_click) {
                fwrite(STDERR,"reporter:counter:VerifyCounters,InvalidClick,1\n");
                continue;
            }

            if (isset($ad_info_arr[$adp_id.":".$ad_id.":".$advertiser][$action])) {
                $ad_info_arr[$adp_id.":".$ad_id.":".$advertiser][$action]++;
            } else {
                $ad_info_arr[$adp_id.":".$ad_id.":".$advertiser][$action] = 1;
            }

        } else {
            //echo "$ad_id,$adp_id,$action,$line\n";
            fwrite(STDERR,"reporter:counter:VerifyCounters,AdLogDataErr,1\n");
        }
    }

}

foreach ($ad_info_arr as $id_str => $count_arr) {
    foreach ($count_arr as $action => $count) {
        echo "$id_str\t$action\t$count\n";
    }
}
