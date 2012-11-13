<?php

namespace Spore\Tests\Units;

require_once __DIR__ . '/../../vendor/autoload.php';

use Spore;
use mock;

class Client2 extends Spore\ClientAbstract
{
}

class Client extends Spore\Test\Unit
{
    public function testClient()
    {
        $client = new Spore\Client();

        $this->assert->object($client)
            ->isInstanceOf('\Spore\Client');

        $data = array();

        $cache = new mock\Spore\Cache\PhpArray();

        $cache->getMockController()->set = function($name, $data) use ($data) {
            $data[$name] = $data;
        };

        $cache->getMockController()->get = function($name) use ($data) {
            if (isset($data[$name])) {
                return $data[$name];
            }

            return false;
        };

        $client->setCache($cache);

        $this->assert->object($client->getCache())
            ->isInstanceOf('\Spore\Cache\PhpArray');

        $client->loadSpec(__DIR__ . '/github.json');

        $this->assert->exception(function() use ($client) {
            $client->loadSpec(__DIR__ . '/github1.json');
        })
        ->isInstanceOf('\InvalidArgumentException');

        $this->assert->exception(function() use ($client) {
            $client->loadSpec(__DIR__);
        })
        ->isInstanceOf('\InvalidArgumentException');

        $client->loadSpec(__DIR__ . '/github.json');

        $content = '{
            "type": "User",
            "company": "GitHub",
            "hireable": false,
            "public_repos": 3,
            "followers": 256,
            "created_at": "2011-01-25T18:44:36Z",
            "bio": null,
            "public_gists": 4,
            "html_url": "https://github.com/octocat",
            "following": 0,
            "email": "octocat@github.com",
            "location": "San Francisco",
            "name": "The Octocat",
            "blog": "http://www.github.com/blog",
            "url": "https://api.github.com/users/octocat",
            "gravatar_id": "7ad39074b0584bc555d0417ae3e7d974",
            "id": 583231,
            "avatar_url": "https://secure.gravatar.com/avatar/7ad39074b0584bc555d0417ae3e7d974?d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-user-420.png",
            "login": "octocat"
        }';

        $response = new mock\Spore\HttpFoundation\Response();

        $response->getMockController()->getStatusCode = function() {
            return 200;
        };

        $response->getMockController()->setContent = function($data) use ($content) {
            $content = $data;
        };

        $response->getMockController()->getContent = function() use ($content) {
            return $content;
        };

        $adapter = new mock\Spore\HttpFoundation\Adapter\Curl();
        
        $adapter->getMockController()->execute = function($request) use ($response) {
            return $response;
        };

        $client->setAdapter($adapter);

        $this->assert->object($client->getAdapter())
            ->isInstanceOf('\Spore\HttpFoundation\Adapter\Curl');

        $params = $client->verifyParameters(array(
            'foot'   => 'bar',
            'id'     => 123,
            'format' => 'json'
        ), array(
            'format',
            'id'
        ));

        $this->assert->array($params)
            ->isEqualTo(array(
                'foot'   => 'bar',
                'id'     => 123,
                'format' => 'json'
            ));

        $this->assert->exception(function() use ($client) {
            $client->verifyParameters(array(
                'foot' => 'bar'
            ), array(
                'foot1'
            ), array());
        })
        ->isInstanceOf('\InvalidArgumentException');

        $params = $client->verifyParameters(array(
            'foot'   => 'bar',
            'id'     => 123,
            'format' => 'xml',
            'user'   => 'euskadi31'
        ), array(
            'format',
            'id'
        ), array(
            'user'
        ));

        $this->assert->array($params)
            ->isEqualTo(array(
                'id'     => 123,
                'format' => 'json',
                'user'   => 'euskadi31'
            ));

        $this->assert->exception(function() use ($client) {
            $client->call('GET', 'test', array());
        })
        ->isInstanceOf('\InvalidArgumentException');

        $this->assert->exception(function() use ($client) {
            $client->call('POST', 'get_user', array());
        })
        ->isInstanceOf('\InvalidArgumentException');

        $response = $client->call('GET', 'get_user', array(
            'user' => 'euskadi31'
        ));

        $this->assert->integer($response->getStatusCode())
            ->isEqualTo(200);

        unset($response);

        $response = new mock\Spore\HttpFoundation\Response();

        $response->getMockController()->getStatusCode = function() {
            return 201;
        };

        $adapter = new mock\Spore\HttpFoundation\Adapter\AdapterInterface();
        
        $adapter->getMockController()->execute = function($request) use ($response) {
            return $response;
        };

        $client->setAdapter($adapter);

        $this->assert->exception(function() use ($client) {
            $client->call('GET', 'get_user', array(
                'user' => 'euskadi31'
            ));
        })
        ->isInstanceOf('\RuntimeException');


        $client = new Spore\Client();

        $this->assert->object($client->getCache())
            ->isInstanceOf('\Spore\Cache\PhpArray');

        $this->assert->object($client->getAdapter())
            ->isInstanceOf('\Spore\HttpFoundation\Adapter\Curl');

        $client->loadSpec(__DIR__ . '/twitter.json');

    }

    public function testClient2()
    {
        $client = new Client2();

        $content = '{
            "type": "User",
            "company": "GitHub",
            "hireable": false,
            "public_repos": 3,
            "followers": 256,
            "created_at": "2011-01-25T18:44:36Z",
            "bio": null,
            "public_gists": 4,
            "html_url": "https://github.com/octocat",
            "following": 0,
            "email": "octocat@github.com",
            "location": "San Francisco",
            "name": "The Octocat",
            "blog": "http://www.github.com/blog",
            "url": "https://api.github.com/users/octocat",
            "gravatar_id": "7ad39074b0584bc555d0417ae3e7d974",
            "id": 583231,
            "avatar_url": "https://secure.gravatar.com/avatar/7ad39074b0584bc555d0417ae3e7d974?d=https://a248.e.akamai.net/assets.github.com%2Fimages%2Fgravatars%2Fgravatar-user-420.png",
            "login": "octocat"
        }';

        $response = new mock\Spore\HttpFoundation\Response();

        $response->getMockController()->getStatusCode = function() {
            return 200;
        };

        $response->getMockController()->setContent = function($data) use ($content) {
            $content = $data;
        };

        $response->getMockController()->getContent = function() use ($content) {
            return $content;
        };

        $adapter = new mock\Spore\HttpFoundation\Adapter\Curl();
        
        $adapter->getMockController()->execute = function($request) use ($response) {
            return $response;
        };

        $client->setAdapter($adapter);

        $response = $client->call('GET', 'https://api.github.com/users/:user', array(
            'user' => 'euskadi31'
        ));

        $this->assert->integer($response->getStatusCode())
            ->isEqualTo(200);
    }
}