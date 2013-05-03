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
        // build arguments
        $arguments = isset($args[0]) ? (array) $args[0] : array();
        
        // detect error
        if (!is_array($arguments)) trigger_error('Arguments need to be an array.');
        
        // sort arguments
        ksort($arguments);
        
        // build query
        $query = http_build_query(array_merge(array('zws-id' => Config::get('zillow.zwsid')), $arguments));

        // build endpoint
        $endpoint = 'http://www.zillow.com/webservice/'.static::camelcase($method).'.htm?'.$query;
        
        // attempt to retrieve from table...
        $hash = md5($endpoint.$query);
        $check = DB::table('zillow')->where('hash', '=', $hash)->first();
        
        // if cache found...
        if ($check)
        {
            // calculate age
            $age = time() - strtotime($check->created_at);

            // if stale (over a year old)...
            if ($age > 31557600)
            {
                // delete
                $check->delete();
            }

            // else if NOT stale...
            else
            {
                // return cached response
                return $check->is_success ? unserialize($check->response) : false;
            }
        }
        
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
            
            // set response
            $response = false;
        }
        else
        {
            curl_close($ch);
            
            // set response
            $response = XML::from_string($response)->to_array();
        }

        // save response
        DB::table('zillow')->insert(array(
            'created_at' => strftime('%F', time()),
            'updated_at' => strftime('%F', time()),
            'hash' => $hash,
            'response' => serialize($response),
            'is_success' => $response ? 1 : 0,
        ));
        
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
        return ucfirst(preg_replace('/(^|_)(.)/e', "strtoupper('\\2')", strval($str)));
    }

}