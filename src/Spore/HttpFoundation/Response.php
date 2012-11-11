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
namespace Spore\HttpFoundation;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Response extends HttpResponse
{
    /**
     * Sets the response content.
     *
     * @param Mixed $content
     *
     * @return \Spore\HttpFoundation\Response
     *
     * @api
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
}
