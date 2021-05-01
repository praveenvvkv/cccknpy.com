<?php

class Application_Model_Util
{

    public function valuePlusOne( $value ) {
        if ( $value > 0 )
            return $value + 1;

        else
            return $value;
    }

    /**
     * @param string $url : url to redirect
     */
    function redirect( $url ) {
        header ( "Location: {$url}" );
    }

    /**
     * @param string $__string : string to be cleaned
     * @return null|string
     */
    function clean_string( $__string ) {
        if ( $__string == null || $__string == "" )
            return null;

        $string1 = trim( strip_tags( $__string ) );

        $string1 = addslashes($string1);

        $string2 = htmlspecialchars( $string1, ENT_QUOTES );
        $string2 = str_replace( array( "<?php", "?>" ), "", $string2 );

        return $string2;
    }

    /**
     * check if a string is null, empty or the string NULL itself
     *
     * @param mixed $__string : string to check
     * @return bool : true or false
     */
    function is_nullstring( $__string ) {
        if ( $__string == "NULL" || $__string == "null" ||
            $__string == "" || $__string==NULL || $__string==null )
            return true;

        else
            return false;

    }

    /**
     * check whether a string is a valid email id
     *
     * @param string $__string : string to be checked
     * @return bool
     */
    function is_valid_email( $__string ) {
        if ( preg_match( "/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/",
            $__string ) )
            return true;

        else
            return false;

    }

    /**
     * find md2 hash equivalent of a string
     *
     * @param string $__string : string to be hashed
     * @return string : hashed string
     */
    function md2_hash( $__string ) {
        $_hash_value = hash( "md2", $__string, false );
        return $_hash_value;
    }

    /**
     * get a random string of specified length
     *
     * @param int $__ssize : size of random string
     * @return string : a random string
     */
    function get_random_string( $__ssize ) {
        $var_string = "abcdefghijklmnopqrstuvwxyz" .
            "ABCDEFGHIJKLMNOPQRSTUVWXYZ" .
            "0123456789" .
            "yourdocumenttotheoriginalontainedinyourcurrenttemplate";

        $shuffle_string     = str_shuffle( $var_string );
        $sub_string_shuffle = substr( $shuffle_string, 0, $__ssize );

        return $sub_string_shuffle;
    }

    function getRandomInteger( $digits ) {
        return rand(pow(10, $digits-1), pow(10, $digits)-1);
    }
    /**
     * sha1 hash a password string
     *
     * @param string $__password string : string to be hashed
     * @return string : hashed string
     */
    function prepare_sha1_password( $__password ) {
        $prepend_str = "@mRit@";
        $append_str  = "T!55";

        return sha1( $prepend_str . $__password . $append_str );
    }

    /**
     * get file size in bytes converted to GB/MB/KB
     *
     * @param int $__ssize : size in bytes
     * @return string : file size in GB/MB/KB
     */
    function get_fsize_string( $__ssize ) {
        if ( $__ssize >= 1000 && $__ssize < 1000000 )
            $file_size = (string) round( $__ssize/1000, 2 ) . " kB";

        elseif ( $__ssize >= 1000000 && $__ssize < 10000000 )
            $file_size = (string) round( $__ssize/1000000, 2 ) . " MB";

        elseif ( $__ssize >= 10000000 )
            $file_size = (string) round( $__ssize/1000000000, 2 ) . " GB";

        else
            $file_size = (string) round( $__ssize ) . " Bytes";

        return $file_size;
    }

    /**
     * @param $__table
     * @return string
     */
    public function getTableName ($__table,$table_prefix_flag=true) {
        if($table_prefix_flag)
        {
            if (isset($_SESSION["table_prefix"])) {
                return $_SESSION['table_prefix'] . "_" . $__table;
            } else
                return "dept1_" . $__table;
        }
        else
            return $__table;
    }

    /**
     * @param $__string
     * @return mixed|string
     */

    /**
     * @param $string
     * @return mixed|string
     */

    /**
     * @param $__table
     * @param $__array_attr
     * @param bool $__order
     * @return bool|string
     */


    /**
     * @param $date
     * @param $time
     * @return string
     */
    function toDateTime($date,$time)
    {
        return date("d F Y",strtotime($date)).', '.date('g:i:A', strtotime($time));
    }

    /**
     * @param $time
     * @return bool|string
     */
    function toTime($time)
    {
        return date('g:i A', strtotime($time));
    }

    /**
     * @param $date
     * @return bool|string
     */
    function toDate($date,$format=null)
    {

        $date1=explode(" ",$date);
        if(count($date1)<=1) {
            if(empty($format))
                $format="d-m-Y";
            return date($format, strtotime($date));
        }
        else{
            if(empty($format))
                $format="d-m-Y";
            return date($format." g:i a",strtotime($date));
        }
    }

    function addMonthToDate($date,$month)
    {
        return date('Y-m-d', strtotime("+".$month." months", strtotime($date)));
    }
    function addDaysToDate($date,$days)
    {
        return date('Y-m-d', strtotime("+".$days." day", strtotime($date)));
    }

    function subDaysFromDate($date,$days)
    {
        return date('Y-m-d', strtotime("-".$days." day", strtotime($date)));
    }

    function clean( $str ) {
        return $this->clean_string($str);
    }

    function getCurTime()
    {
        date_default_timezone_set ("Asia/Calcutta");
        return date("H:i:s");
    }

    function getCurDateTime()
    {
        date_default_timezone_set ("Asia/Calcutta");
        return date('Y-m-d H:i:s');
    }
    function dateDiff($start_date,$end_date)
    {
        return round(abs(strtotime($end_date)-strtotime($start_date))/86400);

    }

    function signedDateDiff($start_date,$end_date)
    {
        return ( strtotime( $end_date ) - strtotime( $start_date ) ) / 86400;

    }

    function datetimeDiff($datetime1, $datetime2)
    {
        $date1 = new DateTime($datetime1);
        $date2 = new DateTime($datetime2);

// The diff-methods returns a new DateInterval-object...
        $diff = $date2->diff($date1);

// Call the format method on the DateInterval-object
        if($diff->format('%a')>0)
            return $diff->format('%a Day and %h Hours');
        return $diff->format('%h Hours');
    }

    function addToDateTime($datetime,$val)
    {
        $time = strtotime($datetime);
        return $startTime = date("Y-m-d H:i:s", strtotime('+'.$val.' minutes', $time));
    }
    function subFromDateTime($datetime,$val)
    {
        $time = strtotime($datetime);
        return $endTime = date("Y-m-d H:i:s", strtotime('-'.$val.' minutes', $time));
    }

    /**
     * @param $datetime1
     * @param $datetime2
     * @return bool (if datetime2>datetime1)
     */
    function compareDateTime($datetime1, $datetime2)
    {
        $time1 = strtotime($datetime1);
        $time2 = strtotime($datetime2);
        if($time2>=$time1)
            return true;
        return false;
    }

    function createDateRangeArray($strDateFrom,$strDateTo)
    {
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.

        // could test validity of dates here but I'm already doing
        // that in the main script

        $aryRange=array();

        $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
        $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

        if ($iDateTo>=$iDateFrom)
        {
            array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
            while ($iDateFrom<$iDateTo)
            {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange,date('Y-m-d',$iDateFrom));
            }
        }
        return $aryRange;
    }

    function isValidateDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') == $date;
    }
    function humanTiming($time)
    {

        $time = time() - $time; // to get the time since that moment

        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }

    }
    function cleanString($string)
    {
        $string = str_replace(' ', '', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
    function minToHour($minutes)
    {
        // if($minutes==0)
        //    return 0;
        $hours = floor($minutes / 60);
        $min = $minutes - ($hours * 60);

        return $hours.":".round($min);
    }

    function getPriority($value)
    {
        if($value<10)
            return 'Low';
        else if($value<15 && $value>=10)
            return 'Normal';
        else if($value<20 && $value>=15)
            return 'Medium';
        else if($value>=20)
            return 'High';

    }

    function truncate($string, $length, $dots = "...") {
        return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
    }

    function toTime24HrTo12($hr,$min=0,$s=0)
    {
        return date("g:i A", strtotime("$hr:$min:$s"));
    }

    function fn_resize($image_resource_id,$width,$height,$target_width,$target_height) {
        $target_layer=imagecreatetruecolor($target_width,$target_height);
        imagecopyresampled($target_layer,$image_resource_id,0,0,0,0,$target_width,$target_height, $width,$height);
        return $target_layer;
    }


    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}

