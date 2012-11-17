<?php
/**
 * @package     Spore
 * @author      Axel Etcheverry <axel@etcheverry.biz>
 * @copyright   Copyright (c) 2012 Axel Etcheverry (http://www.axel-etcheverry.com)
 * @license     MIT
 */

/**
 * @namespace
 */
namespace Spore\HttpFoundation\Adapter;

use Spore\HttpFoundation\Request;
use Spore\HttpFoundation\Response;
use RuntimeException;

class Curl implements AdapterInterface
{
    /**
     * @var String
     */
    protected $sslcert;

    /**
     * @var String
     */
    protected $sslpassphrase;

    /**
     * @var Boolean
     */
    protected $isSecure = false;

    /**
     * 
     * @throws \RuntimeException
     */
    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new RuntimeException("Missing ext/curl");
        }
    }

    /**
     *
     * @param String $cert
     * @param String $passphrase
     * @return \Spore\HttpFoundation\Adapter\Curl
     */
    public function setSsl($cert, $passphrase = null)
    {
        $this->sslcert = $cert;

        if (!empty($passphrase)) {
            $this->passphrase = $passphrase;
        }

        $this->isSecure = true;

        return $this;
    }

    /**
     * 
     * @param \Spore\HttpFoundation\Request $request
     * @return \Spore\HttpFoundation\Response
     * @throws \RuntimeException
     */
    public function execute(Request $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request->getUri());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, sprintf(
            'Spore\Client (version %s +http://github.com/euskadi31/Spore)', 
            \Spore\Client::VERSION
        ));
        
        if ($request->headers->count() > 0) {
            $headers = array();
            foreach ($request->headers->allPreserveCase() as $key => $value) {
                $headers[] = $key . ': ' . $value[0];
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($request->getPort() != 80) {
            curl_setopt($ch, CURLOPT_PORT, $request->getPort());
        }

        /*if ($request->isAuth()) {
            curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, true);
            curl_setopt($ch, CURLOPT_USERPWD, $request->getUser() . ':' . $request->getPassword());
        }*/

        if ($this->isSecure) {
            curl_setopt($ch, CURLOPT_SSLCERT, $this->sslcert);

            if (!empty($this->passphrase)) {
                curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->passphrase);
            }
        }

        $curlValue = true;

        $method = $request->getMethod();

        switch ($method) {
            case 'GET':
                $curlMethod = CURLOPT_HTTPGET;
                break;

            case 'HEAD':
                $curlMethod = CURLOPT_CUSTOMREQUEST;
                $curlValue = "HEAD";
                break;

            case 'POST':
                $data = $request->getData();

                if (!is_string($data)) {
                    $data = http_build_query($data, '', '&');
                }

                $curlMethod = CURLOPT_POST;
                break;

            case 'PUT':
                $data = $request->getData();

                if (!is_string($data)) {
                    $data = http_build_query($data, '', '&');
                }

                $length = strlen($data);
                $fh = fopen('php://memory', 'rw');  
                fwrite($fh, $data);  
                rewind($fh);  
                
                $data = array(
                    CURLOPT_INFILE      => $fh,
                    CURLOPT_INFILESIZE  => $length
                );

                $curlMethod = CURLOPT_UPLOAD;
                //$curlMethod = CURLOPT_CUSTOMREQUEST;
                //$curlValue = "PUT";
                break;

            case 'DELETE':
                $curlMethod = CURLOPT_CUSTOMREQUEST;
                $curlValue = "DELETE";
                break;

            case 'PATCH':
                $curlMethod = CURLOPT_CUSTOMREQUEST;
                $curlValue = "PATCH";
                break;

            case 'TRACE':
                $curlMethod = CURLOPT_CUSTOMREQUEST;
                $curlValue = "TRACE";
                break;

            case 'OPTIONS':
                $curlMethod = CURLOPT_CUSTOMREQUEST;
                $curlValue = "OPTIONS";
                break;
        }

        // mark as HTTP request and set HTTP method
        curl_setopt($ch, $curlMethod, $curlValue);

        /**
         * Make sure POSTFIELDS is set after $curlMethod is set:
         * @link http://de2.php.net/manual/en/function.curl-setopt.php#81161
         */
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } elseif ($curlMethod == CURLOPT_UPLOAD) {
            // this covers a PUT by file-handle:
            // Make the setting of this options explicit (rather than setting it through the loop following a bit lower)
            // to group common functionality together.
            foreach ($data as $key => $value) {
                curl_setopt($ch, $key, $value);
            }
        } elseif ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } elseif ($method == 'PATCH') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        
        unset($data);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException($error);
        }

        $info = curl_getinfo($ch);
        curl_close($ch);
        
        // close resource
        if (isset($fh)) {
            fclose($fh);
        }

        // Eliminate multiple HTTP responses.
        do {
            $parts  = preg_split('|(?:\r?\n){2}|m', $response, 2);
            $again  = false;

            if (isset($parts[1]) && preg_match("|^HTTP/1\.[01](.*?)\r\n|mi", $parts[1])) {
                $response    = $parts[1];
                $again       = true;
            }
        } while ($again);

        // cURL automatically handles Proxy rewrites, remove the "HTTP/1.0 200 Connection established" string:
        if (stripos($response, "HTTP/1.0 200 Connection established\r\n\r\n") !== false) {
            $response = str_ireplace("HTTP/1.0 200 Connection established\r\n\r\n", '', $response);
        }
        
        list($header, $body) = explode("\r\n\r\n", $response);
        
        unset($response);

        $_headers = explode("\r\n", $header);
        unset($_headers[0], $header);

        $headers = array();

        foreach ($_headers as $value) {
            if (strpos($value, ': ') !== false) {
                list($key, $val) = explode(': ', $value);
                $headers[strtolower($key)] = $val;
            }
        }
        
        unset($_headers);

        if (isset($headers['status'])) {
            unset($headers['status']);
        }

        return new Response($body, $info['http_code'], $headers);
    }
}