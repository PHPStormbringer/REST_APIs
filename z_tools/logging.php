<?php

/*  
 *  name: universal_log_it
 *  desc:
 *  
 *
 *  @param string
 *
 */ function universal_log_it($filepath, $msg)
    {
        date_default_timezone_set('US/Eastern');
        $the_time = date("Y-m-d h:i:s", time())." - ";
    
        $logPointer = fopen($filepath, "a+");
        fwrite($logPointer, $the_time.$msg.PHP_EOL);
        fclose($logPointer);
    }
