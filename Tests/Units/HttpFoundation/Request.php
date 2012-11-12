<?php

namespace Spore\HttpFoundation\Tests\Units;

require_once __DIR__ . '/../../../vendor/autoload.php';

use Spore;

class Request extends Spore\Test\Unit
{
    public function testClass()
    {
        $request = new \Spore\HttpFoundation\Request();

        $this->assert->object($request)
            ->isInstanceOf('\Spore\HttpFoundation\Request');

        $request->setUri('http://username:passwd@www.google.com:8080/search?q=test');

        $this->assert->string($request->getUri())
            ->isEqualTo('http://username:passwd@www.google.com:8080/search?q=test');

        $request->setUri('http://username:passwd@www.google.com:80/search?q=test');

        $this->assert->string($request->getUri())
            ->isEqualTo('http://username:passwd@www.google.com/search?q=test');

        $request->setMethod('GET');

        $this->assert->exception(function() use ($request) {
            $request->setMethod('FOOT');
        })
        ->isInstanceOf('\InvalidArgumentException');

        $this->assert->string($request->getPath())
            ->isEqualTo('/search');

        $this->assert->string($request->getScheme())
            ->isEqualTo('http');

        $this->assert->string($request->getHost())
            ->isEqualTo('www.google.com');

        $this->assert->integer($request->getPort())
            ->isEqualTo(80);

        $request->setPort(8080);

        $this->assert->integer($request->getPort())
            ->isEqualTo(8080);

        $this->assert->string($request->getMethod())
            ->isEqualTo('GET');

        $this->assert->boolean($request->isGet())
            ->isTrue();

        $this->assert->boolean($request->isPost())
            ->isFalse();

        $this->assert->boolean($request->isDelete())
            ->isFalse();
        
        $this->assert->boolean($request->isPatch())
            ->isFalse();
        
        $this->assert->boolean($request->isOptions())
            ->isFalse();
        
        $this->assert->boolean($request->isTrace())
            ->isFalse();

        $this->assert->boolean($request->isConnect())
            ->isFalse();

        $this->assert->boolean($request->isPut())
            ->isFalse();

        $this->assert->boolean($request->isHead())
            ->isFalse();

        $this->assert->string($request->getUser())
            ->isEqualTo('username');

        $this->assert->string($request->getPassword())
            ->isEqualTo('passwd');


        $request->setUser('username1');

        $this->assert->string($request->getUser())
            ->isEqualTo('username1');

        $request->setPassword('passwd2');

        $this->assert->string($request->getPassword())
            ->isEqualTo('passwd2');

        $this->assert->boolean($request->isAuth())
            ->isTrue();

        $request = new \Spore\HttpFoundation\Request('http://www.google.com/search?q=test');
        
        $request->setQuery('q=toto&hl=fr');

        $this->assert->object($request->getQuery())
            ->isInstanceOf('\Symfony\Component\HttpFoundation\ParameterBag');

        $this->assert->array($request->getQuery()->all())
            ->isEqualTo(array(
                'q' => 'toto',
                'hl' => 'fr'
            ));

        $request->setQuery(array(
            'foot' => 'bar'
        ));

        $this->assert->array($request->getQuery()->all())
            ->isEqualTo(array(
                'q' => 'toto',
                'hl' => 'fr',
                'foot' => 'bar'
            ));

        $this->assert->exception(function() use ($request) {
            $request->setQuery(1);
        })
        ->isInstanceOf('\InvalidArgumentException');

        $request->setHeaders(array(
            'Accept' => 'application/json'
        ));

        $this->assert->object($request->getHeaders())
            ->isInstanceOf('\Symfony\Component\HttpFoundation\ParameterBag');
        
        $request = new \Spore\HttpFoundation\Request();

        $this->assert->object($request->getQuery())
            ->isInstanceOf('\Symfony\Component\HttpFoundation\ParameterBag');

        $this->assert->object($request->getHeaders())
            ->isInstanceOf('\Symfony\Component\HttpFoundation\ParameterBag');

        $request->setData('test');

        $this->assert->string($request->getData())
            ->isEqualTo('test');
    }
}