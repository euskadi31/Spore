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
namespace Acme\Client;

use Spore\ClientAbstract;
use Spore\HttpFoundation\Response;
use RuntimeException;

/**
 * @see http://dev.twitter.com/
 */
class Twitter extends ClientAbstract
{
    const VERSION = "0.2";

    /**
     * @var String $base
     */
    protected $base = "http://api.twitter.com/1";

    /**
     * 
     * 
     * @param Array $params
     * @return \Spore\HttpFoundation\Response
     * @throws \RuntimeException
     */
    public function getRetweetsOfMe(array $params)
    {
        $params = $this->verifyParameters(
            $params,
            array(
                "format",
            ),
            array(
                "since_id",
                "max_id",
                "count",
                "page",
                "trim_user",
                "include_entities",
                )
        );

        $response = $this->get($this->base . "/statuses/retweets_of_me.:format", $params);

        
        return $response;
    }

    /**
     * 
     * 
     * @param Array $params
     * @return \Spore\HttpFoundation\Response
     * @throws \RuntimeException
     */
    public function getFriendsTimeline(array $params)
    {
        $params = $this->verifyParameters(
            $params,
            array(
                "format",
            ),
            array(
                "since_id",
                "max_id",
                "count",
                "page",
                "trim_user",
                "include_rts",
                "include_entities",
                )
        );

        $response = $this->get($this->base . "/statuses/friends_timeline.:format", $params);

        
        return $response;
    }

    /**
     * 
     * 
     * @param Array $params
     * @return \Spore\HttpFoundation\Response
     * @throws \RuntimeException
     */
    public function getUserTimeline(array $params)
    {
        $params = $this->verifyParameters(
            $params,
            array(
                "format",
            ),
            array(
                "user_id",
                "screen_name",
                "since_id",
                "max_id",
                "count",
                "page",
                "trim_user",
                "include_rts",
                "include_entities",
                )
        );

        $response = $this->get($this->base . "/statuses/user_timeline.:format", $params);

        
        return $response;
    }

    /**
     * 
     * 
     * @param Array $params
     * @return \Spore\HttpFoundation\Response
     * @throws \RuntimeException
     */
    public function getPublicTimeline(array $params)
    {
        $params = $this->verifyParameters(
            $params,
            array(
                "format",
            ),
            array(
                "trim_user",
                "include_entities",
                )
        );

        $response = $this->get($this->base . "/statuses/public_timeline.:format", $params);

        
        return $response;
    }

    /**
     * 
     * 
     * @param Array $params
     * @return \Spore\HttpFoundation\Response
     * @throws \RuntimeException
     */
    public function getMentions(array $params)
    {
        $params = $this->verifyParameters(
            $params,
            array(
                "format",
            ),
            array(
                "since_id",
                "max_id",
                "count",
                "page",
                "trim_user",
                "include_rts",
                "include_entities",
                )
        );

        $response = $this->get($this->base . "/statuses/mentions.:format", $params);

        
        return $response;
    }

    /**
     * 
     * 
     * @param Array $params
     * @return \Spore\HttpFoundation\Response
     * @throws \RuntimeException
     */
    public function getHomeTimeline(array $params)
    {
        $params = $this->verifyParameters(
            $params,
            array(
                "format",
            ),
            array(
                "since_id",
                "max_id",
                "count",
                "page",
                "trim_user",
                "include_entities",
                )
        );

        $response = $this->get($this->base . "/statuses/home_timeline.:format", $params);

        
        return $response;
    }

    /**
     * 
     * 
     * @param Array $params
     * @return \Spore\HttpFoundation\Response
     * @throws \RuntimeException
     */
    public function getRetweetedByMe(array $params)
    {
        $params = $this->verifyParameters(
            $params,
            array(
                "format",
            ),
            array(
                "since_id",
                "max_id",
                "count",
                "page",
                "trim_user",
                "include_entities",
                )
        );

        $response = $this->get($this->base . "/statuses/retweeted_by_me.:format", $params);

        
        return $response;
    }

    /**
     * 
     * 
     * @param Array $params
     * @return \Spore\HttpFoundation\Response
     * @throws \RuntimeException
     */
    public function getRetweetedToMe(array $params)
    {
        $params = $this->verifyParameters(
            $params,
            array(
                "format",
            ),
            array(
                "since_id",
                "max_id",
                "count",
                "page",
                "trim_user",
                "include_entities",
                )
        );

        $response = $this->get($this->base . "/statuses/retweeted_to_me.:format", $params);

        
        return $response;
    }

    
}