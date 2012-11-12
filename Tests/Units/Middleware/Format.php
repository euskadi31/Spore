<?php

namespace Spore\Middleware\Tests\Units;

require_once __DIR__ . '/../../../vendor/autoload.php';

use Spore;
use mock;

class Format extends Spore\Test\Unit
{
    public function testClass()
    {
        $this->assert->class("\Spore\Middleware\Format")
            ->hasInterface('\Spore\Middleware\MiddlewareInterface');

        $middleware = new Spore\Middleware\Format();

        $this->assert->object($middleware)
            ->isInstanceOf('\Spore\Middleware\Format');

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
        
        $object = json_decode($content);

        $response = new mock\Spore\HttpFoundation\Response();

        $response->getMockController()->isSuccessful = function() {
            return true;
        };

        $response->getMockController()->isEmpty = function() {
            return false;
        };

        $response->getMockController()->setContent = function($data) use (&$content) {
            $content = $data;
        };

        $response->getMockController()->getContent = function() use (&$content) {
            return $content;
        };

        $env = new \ArrayObject(array(
            'format' => 'json'
        ));

        $middleware->processResponse($response, $env);

        $this->assert->object($response->getContent())
            ->isEqualTo($object);

        $this->assert->exception(function() use ($middleware, $response) {

            $env = new \ArrayObject(array(
                'format' => 'test'
            ));

            $middleware->processResponse($response, $env);
        })
        ->isInstanceOf('\RuntimeException');

        $this->assert->exception(function() use ($middleware, $response) {

            $env = new \ArrayObject();

            $middleware->processResponse($response, $env);
        })
        ->isInstanceOf('\InvalidArgumentException');
    }
}