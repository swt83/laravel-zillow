<?php

/**
 * A package for working w/ the Zillow API.
 *
 * @package    Zillow
 * @author     Scott Travis <scott.w.travis@gmail.com>
 * @link       http://github.com/swt83/laravel-zillow
 * @license    MIT License
 */

class Zillow {

    /**
     * Run the desired method query against Zillow API (magic method).
     *
     * @param   string  $method
     * @param   array   $args
     * @return  array
     */
    public static function __callStatic($method, $args)
    {
        // build query
        $arguments = isset($args[0]) ? $args[0] : array();
        $query = http_build_query(array_merge(array('zws-id' => Config::get('zillow.zwsid')), $arguments));

        // build endpoint
        $endpoint = 'http://www.zillow.com/webservice/'.static::camelcase($method).'.htm?'.$query;
        
        // setup curl request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        // catch errors
        if (curl_errno($ch))
        {
            #$errors = curl_error($ch);
            curl_close($ch);
            
            // return false
            return false;
        }
        else
        {
            curl_close($ch);
            
            // return array
            return XML::from_string($response)->to_array();
        }
    }

    /**
     * Convert a string to camelcase.
     *
     * @param   string  $str
     * @return  string
     */
    protected static function camelcase($str)
    {
        return ucfirst(preg_replace('/(^|_)(.)/e', "strtoupper('\\2')", strval($str)));
    }

}