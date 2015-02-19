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
     * @return string
     */
    public function getMissingOption()
    {
        return $this->missingOption;
    }


    /**
     * @param string $missingOption
     */
    public function setMissingOption($missingOption)
    {
        $this->missingOption = $missingOption;
    }
}