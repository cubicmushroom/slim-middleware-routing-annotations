<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 20/02/15
 * Time: 15:37
 */

namespace CubicMushroom\Slim\Middleware\Exception;

use CubicMushroom\Exceptions\Exception\Defaults\MissingExceptionMessageException;

/**
 * Class MissingServiceManagerException
 *
 * Exception thrown when the ServiceManager is not available
 *
 * @package CubicMushroom\Slim\Middleware\Exception
 */
class MissingServiceManagerException extends AbstractException
{

    /**
     * @param array $additionalProperties Additional properties passed to the build() method
     *
     * @return string Should return a string on child classes.  This class throws an exception, as there is no default
     *                message for the class
     *
     * @throws MissingExceptionMessageException
     */
    protected static function getDefaultMessage(array $additionalProperties)
    {
        return 'ServiceManager is not available as a service';
    }
}