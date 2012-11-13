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
namespace {{ namespace }};

use Spore\ClientAbstract;
use Spore\HttpFoundation\Response;
use RuntimeException;

{% if meta.documentation %}
/**
 * @see {{ meta.documentation }}
 */
{% endif %}
class {{ name }} extends ClientAbstract
{
    const VERSION = "{{ version }}";

    /**
     * @var String $base
     */
    protected $base = "{{ base_url }}";

    {% if 'json' in formats == false -%}
    /**
     * @var String
     */
    protected $format = "{{ formats[0] }}";
    
    {% endif -%}

    {% for method in methods -%}
    /**
     * {{ method.documentation }}
     * 
     * @param Array $params
     * @return \Spore\HttpFoundation\Response
     * @throws \RuntimeException
     */
    public function {{ method.name }}(array $params)
    {
        $params = $this->verifyParameters(
            $params,
            {% if method.required_params -%}
            array(
                {% for param in method.required_params -%}
                "{{ param }}",
            {% endfor -%}
            ),
            {% else -%}
            array(),
            {% endif -%}
            {% if method.optional_params -%}
            array(
                {% for param in method.optional_params -%}
                "{{ param }}",
                {% endfor -%}
            )
        {% else -%}
            array()
        {% endif -%}
        );

        $response = $this->{{ method.method }}($this->base . "{{ method.path }}", $params);

        {% if method.expected -%}
        if (!in_array($response->getStatusCode(), array(
            {% for code in method.expected -%}
            {{ code }},
        {% endfor -%}
        ))) {
            throw new RuntimeException(Response::$statusTexts[$response->getStatusCode()]);
        }
        {% endif %}

        return $response;
    }

    {% endfor %}

}