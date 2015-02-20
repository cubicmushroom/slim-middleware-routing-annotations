<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 20/02/15
 * Time: 15:15
 */

namespace CubicMushroom\Slim\Middleware\Exception;

use CubicMushroom\Annotations\Routing\Annotation\AbstractAnnotation;

/**
 * Class MissingMethodsException
 *
 * Exception thrown when 'methods' is not defined on a route annotation
 *
 * @package CubicMushroom\Slim\Middleware\Exception
 */
class MissingMethodsException extends AbstractException
{

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var AbstractAnnotation
     */
    protected $annotation;


    /**
     * {@inheritdoc}
     */
    protected static function getDefaultMessage(array $additionalProperties)
    {
        return "No 'methods' property defined on the {$additionalProperties['class']}::" .
               "{$additionalProperties['method']}() route";
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }


    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }


    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }


    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }


    /**
     * @return AbstractAnnotation
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }


    /**
     * @param AbstractAnnotation $annotation
     */
    public function setAnnotation($annotation)
    {
        $this->annotation = $annotation;
    }
}