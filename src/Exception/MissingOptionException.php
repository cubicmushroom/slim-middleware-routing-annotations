<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 18/02/15
 * Time: 23:26
 */

namespace CubicMushroom\Slim\Middleware\Exception;

class MissingOptionException extends AbstractException
{

    /**
     * @var array
     */
    protected $missingOptions;


    protected static function getDefaultMessage(array $additionalProperties)
    {
        return 'Missing required options... ' . implode('\', \'', $additionalProperties['missingOptions']);
    }




    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return array
     */
    public function getMissingOptions()
    {
        return $this->missingOptions;
    }


    /**
     * @param array $missingOptions
     */
    public function setMissingOptions($missingOptions)
    {
        $this->missingOptions = $missingOptions;
    }
}