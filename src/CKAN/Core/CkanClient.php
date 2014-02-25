<?php

namespace CKAN\Core;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * @author Alex Perfilov
 * @date   2/24/14
 * Inspired by https://github.com/jeffreybarke/Ckan_client-PHP
 */
class CkanClient
{

    /**
     * @var string
     */
    private $api_url = '';

    /**
     * @var null|string
     */
    private $api_key = null;

    /**
     * cURL handler
     * @var resource
     */
    private $ch;


    /**
     * cURL headers
     * @var array
     */
    private $ch_headers;

    /**
     * HTTP status codes.
     * @var        array
     */
    private $http_status_codes = array(
        '200' => 'OK',
        '301' => 'Moved Permanently',
        '400' => 'Bad Request',
        '403' => 'Not Authorized',
        '404' => 'Not Found',
        '409' => 'Conflict (e.g. name already exists)',
        '500' => 'Service Error'
    );

    /**
     * @param $api_url
     * @param null $api_key
     */
    public function __construct($api_url, $api_key = null)
    {
        $this->api_url = $api_url;
        $this->api_key = $api_key;

        // Create cURL object.
        $this->ch = curl_init();
        // Follow any Location: headers that the server sends.
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        // However, don't follow more than five Location: headers.
        curl_setopt($this->ch, CURLOPT_MAXREDIRS, 5);
        // Automatically set the Referrer: field in requests
        // following a Location: redirect.
        curl_setopt($this->ch, CURLOPT_AUTOREFERER, true);
        // Return the transfer as a string instead of dumping to screen.
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        // If it takes more than 45 seconds, fail
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 45);
        // We don't want the header (use curl_getinfo())
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        // Track the handle's request string
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, true);
        // Attempt to retrieve the modification date of the remote document.
        curl_setopt($this->ch, CURLOPT_FILETIME, true);
        // Initialize cURL headers
        $this->set_headers();
    }

    /**
     * Sets the custom cURL headers.
     * @access    private
     * @return    void
     * @since     Version 0.1.0
     */
    private function set_headers()
    {
        $date             = new DateTime(null, new DateTimeZone('UTC'));
        $this->ch_headers = array(
            'Date: ' . $date->format('D, d M Y H:i:s') . ' GMT', // RFC 1123
            'Accept: application/json',
            'Accept-Charset: utf-8',
            'Accept-Encoding: gzip'
        );
    }

    /**
     * Searches for packages satisfying a given search criteria
     * @param $query
     * @param int $rows
     * @param int $start
     * @return mixed
     */
    public function package_search($query, $rows = 100, $start = 0)
    {
        $solr_request = array(
            'q'     => $query,
            'rows'  => $rows,
            'start' => $start,
        );
        $data         = json_encode($solr_request);

        return $this->make_request('POST',
            'action/package_search',
            $data);
    }

    /**
     * Update a dataset (package)
     * @param $data
     * @return mixed
     */
    public function package_update($data)
    {
        return $this->make_request('PUT',
            'action/package_update',
            $data);
    }

    /**
     * @param string $method // HTTP method (GET, PUT, POST)
     * @param string $uri    // URI fragment to CKAN resource
     * @param string $data   // Optional. String in JSON-format that will be in request body
     * @return mixed    // If success, either an array or object. Otherwise FALSE.
     * @throws Exception
     */
    private function make_request($method, $uri, $data = null)
    {
        // Set cURL method.
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        // Set cURL URI.
        curl_setopt($this->ch, CURLOPT_URL, $this->api_url . $uri);
        // If POST or PUT, add Authorization: header and request body
        if ($method === 'POST' || $method === 'PUT') {
            // We needs a key and some data, yo!
            if (!$data) {
                // throw exception
                throw new Exception('Missing POST data.');
            } else {
                if ($this->api_key) {
                    // Add Authorization: header.
                    $this->ch_headers[] = 'Authorization: ' . $this->api_key;
                }
                // Add data to request body.
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
            }
        } else {
            // Since we can't use HTTPS,
            // if it's in there, remove Authorization: header
            $key = array_search('Authorization: ' . $this->api_key,
                $this->ch_headers);
            if ($key !== false) {
                unset($this->ch_headers[$key]);
            }
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        }
        // Set headers.
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->ch_headers);
        // Execute request and get response headers.
        $response = curl_exec($this->ch);
        $info     = curl_getinfo($this->ch);
        // Check HTTP response code
        if ($info['http_code'] !== 200) {
            throw new Exception($info['http_code'] . ': ' .
                $this->http_status_codes[$info['http_code']]);
        }

        return $response;
    }

    /**
     * Since it's possible to leave cURL open, this is the last chance to close it
     */
    public function __destruct()
    {
        if ($this->ch) {
            curl_close($this->ch);
            unset($this->ch);
        }
    }
}