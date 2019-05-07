<?php
// ***********************************************************
// name: validation.php
// date: 17 feb 2011
// auth:  vvenning
//
// Proj: Casio.com refresh
// ***********************************************************
// Proj: Medicine Show Theatre Company
// date: 12 jan 2012
//
// ***********************************************************
// Proj: trustsecurenow API
// date: 27 may 2018
// **********************************************************************************************************
// Validation Functions  
// **********************************************************************************************************
// NOTE:  I'm not happy with this class.  Look into c_types on php.net
//
// 2012-05-27	VAV - Will this work though?  Richard enters some wierd stuff
// 2018-05-29   VAV - Six year old code.  Looks solid, will save me time
//  2019-04-05 1617 VVenning - new function 'validate_gmdate()'

class validation
{
    public $salt;

//  name: clean_email_address
//  date: 2018-05-29
//  auth: VVenning
//  desc: encapsulat function to clean bad chars out of email addresses
//  param: string   
    public static function clean_email_address($email_address)
    {
        return filter_var($email_address, FILTER_SANITIZE_EMAIL);
    }

//  name: validate_email
//  date: 2018-06-06
//  auth: VVenning
//  desc: encapsulat function to validate email addresses
//  param: string   
    public static function validate_email_address($email_address)
    {
        return filter_var($email_address, FILTER_VALIDATE_EMAIL);
    }


//  name: clean_string_input
//  date: 2018-05-29
//  auth: VVenning
//  desc: encapsulat function to clean html out of strings
//  param: string
    public static function clean_string_input($str)
    {
        return filter_var($str, FILTER_SANITIZE_STRING);
    }

//  name: check_alphanum
//  date: 2018-05-29
//  auth: VVenning
//  desc: return true/false for alphanum
//  param: string
    public static function check_alphanum($txt)
    {
        $pattern = '/^[a-zA-Z]{1,25}$/';
        
        if (ctype_alnum($txt))
        { 
            return true;
        }
        else
        {
            return false;
        }
    }

//  name: check_alpha
//  date: 2018-06-06
//  auth: VVenning
//  desc: return true/false for alpha
//  param: string
    public static function check_alpha($txt)
    {
        if (ctype_alpha($txt))
        { 
            return true;
        }
        else
        {
            return false;
        }
    }

//  name: check_numeric
//  date: 2018-06-06
//  auth: VVenning
//  desc: return true/false for numeric
//  param: 
    public static function check_numeric($x)
    {
        if (is_numeric($x))
        { 
            return true;
        }
        else
        {
            return false;
        }
    }

//  name: check_name
//  date: 2018-06-06
//  auth: VVenning
//  desc: return true/false for numeric
//  param: 
    public static function check_name($txt)
    {
        $pattern = "/^[A-Z][-' a-zA-Z]( [a-zA-Z])*$/";

        if (preg_match($pattern,$txt))
        { 
            return true;
        }
        else
        {
            return false;
        }
    }



//  name: clean_POST_Array
//  date: 2018-05-29
//  auth: VVenning
//  desc: filter $_POST array 
	public static function clean_POST_Array()
	{
	    return filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING); 
	}

//  name: clean_POST_Array
//  date: 2018-05-29
//  auth: VVenning
//  desc: filter $_GET array
    public static function clean_GET_Array()
    {
        return filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING); 
    }

//  name: encode_HTML_text_for_db_storage
//  date: 2018-05-29
//  auth: VVenning
//  desc: 
//  param: string
//  2012-05-27	VAV		Will this work though?  Richard enters some wierd stuff
//  2018-05-29 1110 VAV - Won't be using for trustsecurenow
	public static function encode_HTML_text_for_db_storage($txt_HTML)
	{
        return filter_var($txt_HTML, FILTER_SANITIZE_SPECIAL_CHARS);
	}

//  name: decode_db_storage_for_HTML_display
//  date: 2018-05-29
//  auth: VVenning
//  desc: 
//  param: string
//  2012-05-27 VAV - Won't be using for trustsecurenow
	public static function decode_db_storage_for_HTML_display($txt_HTML)
	{
        return html_entity_decode($txt_HTML);
	}


	
//  name: encrypt
//  date: 2018-05-29
//  auth: VVenning
//  desc: encryption  todo
    public static function encrypt($txt)
    {
		return $txt;
    }

//  name: stop_sql_injection_v1
//  date: 2018-05-29
//  auth: VVenning
//  desc: probably won't be used.  Going with prepared statements
    public static function stop_sql_injection_v1($txt)
    {

        

    }
	
//  name: fnEncrypt
//  date: 2018-05-29
//  auth: VVenning
//  desc: I never used this.  Needs testing
    private function fnEncrypt($sValue, $sSecretKey)
    {
        return trim(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    $sSecretKey, $sValue, 
                    MCRYPT_MODE_ECB, 
                    mcrypt_create_iv(
                        mcrypt_get_iv_size(
                            MCRYPT_RIJNDAEL_256, 
                            MCRYPT_MODE_ECB
                        ), 
                        MCRYPT_RAND)
                    )
                )
            );
    }

//  name: fnDecrypt
//  date: 2018-05-29
//  auth: VVenning
//  desc: I never used this.  Needs testing
    private function fnDecrypt($sValue, $sSecretKey)
    {
        return trim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256, 
                $sSecretKey, 
                base64_decode($sValue), 
                MCRYPT_MODE_ECB,
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256,
                        MCRYPT_MODE_ECB
                    ), 
                    MCRYPT_RAND
                )
            )
        );
    }

//  name: hash
//  date: 2018-06-04
//  auth: VVenning
//

//  desc: This is a straight steal/excerpt from cakePHP 2.5*.  
//  File: \hsn-core\core\Cake\Utility\Security.php
// 
//  We're going to tell them to change this password, so the method of encryption does not have to match cakePHP.
//
//  @param string $string String to hash
//  @param string $type Method to use (sha1/sha256/md5/blowfish)
//  @param mixed $salt If true, automatically prepends the application's salt value to $string (Security.salt).
//  @return string Hash

    public static function hash($string, $type = null, $salt = false) {
        
        if ($salt) {
            if (!is_string($salt)) {

                $salt = PEPPER;
            }
            $string = $salt . $string;
        }

        if (!$type) 
        {
             $type = 'sha256';
        }

        if ($type === 'sha256' && function_exists('mhash')) {
            return bin2hex(mhash(MHASH_SHA256, $string));
        }

        if (function_exists('hash')) {
            return hash($type, $string);
        }
        return md5($string);
    }

/*  name: validate_gmdate
//  date: 2018-04-05
//  auth: VVenning
//  2019-04-05 1617 VVenning - new function 'validate_gmdate()'
*/  public static function validate_gmdate($str, $between = null) 
    {
        if ($between == null)
        {
            preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $str, $matches);

            if (count($matches) != 7)
                return false;
        }
        elseif ($between == 'T')
        {
            preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $str, $matches);

            if (count($matches) != 7)
                return false;
        }


        $valid_year   = range(2000, 2050); // your range
        $valid_month  = range(1, 12);
        $valid_day    = range(1, 31);
        $valid_hour   = range(0, 24);
        $valid_minute = range(0, 59);
        $valid_second = range(0, 59);
    
        list($str, $year, $month, $day, $hour, $minute, $second) = $matches;
    
        foreach(array('year', 'month', 'day', 'hour', 'minute', 'second') as $part)
        {
            if (!in_array($$part, ${'valid_'.$part}))
                return false;
        }
    
        return checkdate($month, $day, $year); // this will reject absurd values like February 30 or April 31
    }

}

?>