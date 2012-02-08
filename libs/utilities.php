<?php 

/**
 * Generates a random string.
 */
function genRandomString($length = 5) 
{
    $retval = "";

    for ($i=0; $i < $length; $i++) 
    { 
        $retval .= chr(rand(97,122));
    }

    return $retval;
}

/**
 * Send a request without waiting for response.
 */
function async_post($url, $params) 
{
    foreach ($params as $key => &$val) 
    {
        if (is_array($val)) 
        {
            $val = implode(',', $val);
        }

        $post_params[] = $key.'='.urlencode($val);
    }

    $post_string = implode('&', $post_params);
    $parts = parse_url($url);

    $fp = fsockopen($parts['host'],
        isset($parts['port']) ? $parts['port'] : 80,
        $errno, $errstr, 30);

    $out = "POST ".$parts['path']." HTTP/1.1\r\n";
    $out.= "Host: ".$parts['host']."\r\n";
    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out.= "Content-Length: ".strlen($post_string)."\r\n";
    $out.= "Connection: Close\r\n\r\n";

    if (isset($post_string))
    {
        $out.= $post_string;
    }

    fwrite($fp, $out);
    fclose($fp);

    return $out;
}