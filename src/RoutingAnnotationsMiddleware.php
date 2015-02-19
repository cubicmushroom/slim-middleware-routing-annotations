<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 18/02/15
 * Time: 22:26
 */

namespace CubicMushroom\Slim\Middleware;

use CubicMushroom\Annotations\Routing\Parser\DocumentationAnnotationParser;
use CubicMushroom\Exceptions\Exception\Defaults\MissingExceptionMessageException;
use CubicMushroom\Slim\Middleware\Exception\MissingOptionException;
use CubicMushroom\Slim\ServiceManager\ServiceManager;
use Slim\Middleware;

class RoutingAnnotationsMiddleware extends Middleware
{

    /**
     * @var string|ServiceManager
     */
    protected $serviceManager;

    /**
     * @var array
     */
    protected $classes;

    /**
     * @var DocumentationAnnotationParser
     */
    protected $annotationParser;


    /**
     * Stores the options
     *
     * @param DocumentationAnnotationParser|string $annotationParser Object used to parse class annotations, or a string
     *                                                               for the the parser service.
     *                                                               If you pass a service string name you must also
     *                                                               pass $options['serviceManager']
     * @param array                                $options          Array of options. Possible options...
     *                                                               - 'serviceManager' string [optional] String used
     *                                                               to
     *                                                               register service manager as service
     *                                                               You must supply this or 'classes'
     *                                                               - 'classes'        array  [optional] Array of
     *                                                               classes to parse You must supply this or
     *                                                               'serviceManager'
     *
     * @throws MissingExceptionMessageException
     */
    function __construct(DocumentationAnnotationParser $annotationParser, array $options)
    {
        if (empty($options['serviceManager']) && empty($options['classes'])) {
            throw MissingExceptionMessageException::build(
                ['message' => 'Missing \'classes\' and \'serviceManager\' option.  One of these is required to work.'],
                ['missingOptions' => ['classes', 'serviceManager']]
            );
        }

        if (!empty($options['serviceManager'])) {
            $this->setServiceManager($options['serviceManager']);
        }

        if (!empty($options['classes'])) {
            $this->setClasses($options['classes']);
        }
    }


    /**
     * Call
     *
     * Perform actions specific to this middleware and optionally
     * call the next downstream middleware.
     */
    public function call()
    {
        $this->addRoutes();

        $this->next->call();
    }


    /**
     * Gets classes with routes, parses them and adds the app routes
     */
    protected function addRoutes()
    {
        $classes     = $this->getRouteClasses();
        $annotations = $this->getAnnotationParser()->parse($classes);

        return $annotations;
    }


    /**
     * Gets the classes with route annotations
     *
     * @return array
     */
    protected function getRouteClasses()
    {
        $routingClasses = [];

        $serviceManager = $this->getServiceManager();
        if (!empty($serviceManager)) {
            if (true === $serviceManager) {
                $sm = $this->app->container->get('@' . ServiceManager::DEFAULT_SERVICE_NAME);
            } else {
                $sm = $this->app->container->get($serviceManager);
            }
            /** @var ServiceManager $sm */

            $routingServices = $sm->getTaggedServices('routes');
            foreach ($routingServices as $routingService) {
                $routingClasses[] = $routingService->getClass();
            }
        }

        $classes = $this->getClasses();
        if (!empty($classes)) {
            foreach ($classes as $class) {
                $routingClasses[] = $class;
            }
        }
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return ServiceManager
     *
     * @throws MissingOptionException if the serviceManager option was not passed
     */
    public function getServiceManager()
    {
        if (empty($this->serviceManager)) {
            throw MissingOptionException::build([], ['options' => ['serviceManager']]);
        }

        if (is_string($this->serviceManager)) {
            $this->serviceManager = $this->app->container->get($this->serviceManager);
        }

        return $this->serviceManager;
    }


    /**
     * @param string $serviceManager
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }


    /**
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }


    /**
     * @param array $classes
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;
    }


    /**
     * @return DocumentationAnnotationParser
     */
    public function getAnnotationParser()
    {
        if (is_string($this->annotationParser)) {
            $this->annotationParser = $this->getServiceManager()
                                           ->getService($this->annotationParser);
        }

        return $this->annotationParser;
    }


    /**
     * @param DocumentationAnnotationParser|string $annotationParser
     */
    public function setAnnotationParser($annotationParser)
    {
        $this->annotationParser = $annotationParser;
    }
}