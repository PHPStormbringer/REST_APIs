<?php
// ***********************************************************
// name: random_tools.php
// date: 21 aug 2018
// auth:  vvenning
//
// desc: random tools
// ***********************************************************
// 2018-08-26 xxxx VVenning - Added formattedNow function
// 2018-09-11 xxxx VVenning - Added fucntion spilt_name
// 2018-12-11 0918 VVenning - new function 'removeAccents()'


/*
 * Create a random string
 * @author  XEWeb <>
 * @param $length the length of the string to create
 * @return $str the string
 *
 */	function randomString($length = 6) 
	{
		$str = "";
		$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
		$max = count($characters) - 1;
		for ($i = 0; $i < $length; $i++) {
			$rand = mt_rand(0, $max);
			$str .= $characters[$rand];
		}
		return $str;
	}

/*
 *  name: formattedNow
 *  desc: Returns now in YYYY-mm-dd hh:mm:ss format
 *  
 *  @return formatted date 
 *
 */	function formattedNow()
	{
		date_default_timezone_set('EST');
		return date('Y-m-d H:m:s', time());
	}
                             
/*
 *  name: checkLoadedFile
 *  desc: Checks if the file is already loaded thru require, require_once and included
 * 
 *  @param $file_name
 *
 *  @return boolean
 *
 */ function checkLoadedFile($file_name)
    {
        $required_files = get_required_files();
        $included_files = get_included_files();

        $files = array_unique(array_merge ($required_files, $included_files));

        foreach ($files as $file){
            if(strpos($file, $file_name) !== false ){
                return true;
            }
        }

        return false;
    }                            


/*
 * name: split_name
 * desc: spit string into two as if a name
 * 
 * @param name string 
 *
 * @return array
 *
 */	function split_name($name) 
    {
        $name = trim($name);
        $first_name = null;
        $last_name = null;
    
    //    If space found in name, assume order is firstname then lastname
        if(strpos($name, ' ') === false)
        {
        //    If comma found in name, assume order is last name then first
            if(strpos($name, ',') === false)
            {
            
            }
            else
            {
                $last_name = substr($name, 0, strpos($name, ' '));
                $first_name  = substr($name, strpos($name, ' '));
            }
        
        }
        else
        {
            $first_name = substr($name, 0, strpos($name, ' '));
            $last_name  = substr($name, strpos($name, ' '));
        }

        return array($first_name, $last_name);
    }
    
/*
 *  name: isValidJSON
 *  desc: Checks if Json is valid
 *
 *  @param $string
 *
 *  @return boolean
 *
 */ function isValidJSON($string) {
    $decoded = json_decode($string);
    if ( !is_object($decoded) && !is_array($decoded) ) {
        return false;
    }
    return (json_last_error() == JSON_ERROR_NONE);
}


/*
 *  name: removeAccents
 *  desc: Remove accents and umlauts from strings
 *  
 *  @param $str string
 *  
 *  @return string
 *
 *  2018-12-11 0918 VVenning - new function 'removeAccents()'
 *
 */	function removeAccents($str) {
  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');

//  return str_replace($a, $b, $str);
return str_replace($a, $b, $str);

}

/*
 * name: executeQuery
 * desc: run query
 * 
 *  @param $sql_statement string sql statement
 *  @param $type
 *  @param $params
 *  @param $close
 *
 *  @return array
 *
 */ function executeQuery($sql_statement, $type, $params, $close)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        try
        {
            $stmt = $cxn->prepare($sql_statement);

            if(!empty($type) && !empty($params))
            {
                $stmt->bind_param($type, ...$params);
            }
		
            $stmt->execute();

            if($stmt)
            {
                if($close)
                {
                    $result = $cxn->affected_rows;
                    if(strpos($sql_statement, 'INSERT') !== false)
                    {
                        $this->last_inserted_id = $cxn->insert_id;
                    }
                }
                else 
                {
                    $res = $stmt->get_result();

                    if($res->num_rows > 1)
                    {
                        $result = array();

                        while ($obj = $res->fetch_assoc()) 
                        {
                            array_push($result, $obj);
                        }
                    }
                    else
                    {
                    //  $result = $res->fetch_object('m_clients');
                        $result = $res->fetch_assoc();
                    }
                }
                $stmt->close();
                return  $result;
            }
        } 
        catch (mysqli_sql_exception $e) 
        {
            if(isset($cxn->log_file_path) && !empty($cxn->log_file_path))
            {
                $file = $_SERVER['DOCUMENT_ROOT'].'/'.$cxn->log_file_path;
            }
            else
            {
                $file = $_SERVER['DOCUMENT_ROOT'].REST_API_DATABASE_ERROR_LOG_PATH;
            }

            universal_log_it($file, "m_clients - ".$e->getMessage());

            return null;
        }
    }
