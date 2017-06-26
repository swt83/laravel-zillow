<?php

namespace Travis;

use Travis\XML;

class Zillow
{
    /**
     * Run the desired method against the Zillow API (magic method).
     *
     * @param   string  $method
     * @param   array   $args
     * @return  array
     */
    public static function __callStatic($method, $args)
    {
        // capture arguments
        $args = isset($args[0]) ? $args[0] : [];

        // fix api key
        $args = array_merge(['zws-id' => ex($args, 'api_key')], $args);

        // build query
        $query = http_build_query($args);

        // build endpoint
        $endpoint = 'http://www.zillow.com/webservice/'.static::camelcase($method).'.htm?'.$query;

        // setup curl request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // catch error...
        if (curl_errno($ch))
        {
            #$errors = curl_error($ch);

            // set response
            $response = false;
        }
        else
        {
            // set response
            $response = XML::fromString($response)->toArray();
        }

        // close connection
        curl_close($ch);

        // return
        return $response;
    }

    /**
     * Convert a string to camelcase.
     *
     * @param   string  $str
     * @return  string
     */
    protected static function camelcase($str)
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9]+/i', ' ', $str);
        $str = trim($str);

        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        #$str = lcfirst($str);

        // return
        return $str;
    }
}