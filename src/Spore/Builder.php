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
namespace Spore;

class Builder
{
    /**
     * 
     * @param Array $parameters
     * @return String
     */
    protected function render($parameters)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(
            __DIR__ . DIRECTORY_SEPARATOR . 'Builder'
        ), array(
            'debug'            => true,
            'cache'            => false,
            'strict_variables' => false,
            'autoescape'       => false,
        ));

        return $twig->render('Skeleton.php', $parameters);
    }

    /**
     *
     * @param String $target
     * @param Array $parameters
     * @return Boolean|Integer
     */
    public function renderFile($target, $parameters)
    {
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        return file_put_contents($target, $this->render($parameters));
    }
}