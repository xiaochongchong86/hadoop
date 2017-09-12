<?php
  $btime = "/btime.com/";

  while(($line = fgets(STDIN))!== false) {	
    if (empty($line)) {
       continue;
    }
    if (preg_match($btime, $line, $match)) {
        
    }
  }

