<?php
use App\Models\Setting;
use App\Models\User;

if(!function_exists('setting'))
{
    function setting($key)
    {
        $setting = Setting::select('value')->where("key",$key)->first();
        if(!empty($setting))
        {
            return $setting['value'];
        }else{ return "";}
    }
}
if(!function_exists('convert_date'))
{
    function convert_date($datetime,$format = "F d, Y, h:i A")
    {
        $tz_from     = "Asia/Kolkata"; 
        $newDateTime = new DateTime($datetime, new DateTimeZone($tz_from)); 
        $newDateTime->setTimezone(new DateTimeZone("Asia/Kolkata")); 
        $dateTimeutc = $newDateTime->format($format);
        return $dateTimeutc;
    }
}
if(!function_exists('convert_date_only'))
{
    function convert_date_only($datetime,$format = "F d, Y")
    {
        $tz_from     = "Asia/Kolkata"; 
        $newDateTime = new DateTime($datetime, new DateTimeZone($tz_from)); 
        $newDateTime->setTimezone(new DateTimeZone("Asia/Kolkata")); 
        $dateTimeutc = $newDateTime->format($format);
        return $dateTimeutc;
    }
}
if(!function_exists('convertdate'))
{
    function convertdate($date)
    {
        return date("F d, Y",strtotime($date));
    }
}
if(!function_exists('extract_time'))
{
    function extract_time($datetime,$format = "h:i A")
    {
        $tz_from     = "Asia/Kolkata"; 
        $newDateTime = new DateTime($datetime, new DateTimeZone($tz_from)); 
        $newDateTime->setTimezone(new DateTimeZone("Asia/Kolkata")); 
        $dateTimeutc = $newDateTime->format($format);
        return $dateTimeutc;
    }
}
if(!function_exists('extract_date'))
{
    function extract_date($datetime,$format = "F d, Y")
    {
        $tz_from     = "Asia/Kolkata"; 
        $newDateTime = new DateTime($datetime, new DateTimeZone($tz_from)); 
        $newDateTime->setTimezone(new DateTimeZone("Asia/Kolkata")); 
        $dateTimeutc = $newDateTime->format($format);
        return $dateTimeutc;
    }
}
if(!function_exists('generate_otp'))
{
    function generate_otp()
    {
        $otp = rand(1000,9999);
        return $otp;
    }
}
if(!function_exists('generate_fake_otp'))
{
    function generate_fake_otp()
    {
        $otp = 1234;
        return $otp;
    }
}
if(!function_exists('get_time_ago'))
{
    function get_time_ago($time)
    {
        $time_difference = time() - strtotime($time);

        if($time_difference < 1 ) { return ' 1 second ago'; }
        $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
        );

        foreach($condition as $secs => $str)
        {
            $d = $time_difference / $secs;

            if($d >= 1)
            {
                $t = round( $d );
                return  $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
            }
        }
    }
}

