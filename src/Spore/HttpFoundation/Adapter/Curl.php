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
        curl_setopt($ch, CURLOPT_USERAGENT, sprintf(
            'Spore\Client (version %s +http://github.com/euskadi31/Spore)', 
            \Spore\Client::VERSION
        ));
        
        if ($request->getHeaders()->count() > 0) {
            $headers = array();
            foreach ($$request->getHeaders() as $key => $value) {
                $headers[] = sprintf("%s: %s", $key, $value);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($request->getPort() != 80) {
            curl_setopt($ch, CURLOPT_PORT, $request->getPort());
        }

        if ($request->isAuth()) {
            curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, true);
            curl_setopt($ch, CURLOPT_USERPWD, $request->getUser() . ':' . $request->getPassword());
        }

        switch ($request->getMethod()) {
            case 'HEAD':
                curl_setopt($ch, CURLOPT_NOBODY, true);
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getContent());
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_PUT, true);
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                break;
            case 'TRACE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'TRACE');
                break;
            case 'OPTIONS':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
                break;
        }

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException(curl_error($ch));
        }

        $response = new Response($response, $statusCode);
        return $response;
    }
}