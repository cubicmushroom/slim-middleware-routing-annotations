<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 18/02/15
 * Time: 22:26
 */

namespace CubicMushroom\Slim\Middleware;

use CubicMushroom\Annotations\Routing\Annotation\Route as RouteAnnotation;
use CubicMushroom\Annotations\Routing\Annotation\Route;
use CubicMushroom\Annotations\Routing\Parser\DocumentationAnnotationParser as Parser;
use CubicMushroom\Exceptions\Exception\Defaults\MissingExceptionMessageException;
use CubicMushroom\Slim\Middleware\Exception\MissingMethodsException;
use CubicMushroom\Slim\Middleware\Exception\MissingOptionException;
use CubicMushroom\Slim\Middleware\Exception\MissingServiceManagerException;
use CubicMushroom\Slim\ServiceManager\ServiceManager;
use Slim\Middleware;

class RoutingAnnotationsMiddleware extends Middleware
{

    /**
     * @var string|ServiceManager
     */
    protected $serviceManager;

    /**
     * @var string|Parser
     */
    protected $annotationParser;


    /**
     * Stores the options
     *
     * @param Parser|string         $annotationParser                Object used to parse class annotations, or a string
     *                                                               for the the parser service.
     * @param string|ServiceManager $serviceManager                  Service manager service name or object
     *
     * @throws MissingExceptionMessageException
     */
    function __construct($annotationParser, $serviceManager)
    {
        $this->setAnnotationParser($annotationParser);
        $this->setServiceManager($serviceManager);
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
        $annotations = $this->getAnnotationParser()->parse(array_keys($classes));

        foreach ($annotations as $class => $methodAnnotations) {
            $service = $classes[$class];
            foreach ($methodAnnotations as $method => $typeAnnotations) {
                foreach ($typeAnnotations as $annotationType => $annotationsArray) {
                    foreach ($annotationsArray as $annotation) {
                        if ($annotation instanceof Route) {
                            $this->addRoute($annotation, $service, $method, $class);
                        }
                    }
                }
            }
        }
    }


    /**
     * Gets the classes with route annotations
     *
     * @return array
     *
     * @throws MissingServiceManagerException if the ServiceManager service is not available
     */
    protected function getRouteClasses()
    {
        $routingClasses = [];

        $serviceManager  = $this->getServiceManager();

        if (empty($serviceManager)) {
            throw MissingServiceManagerException::build();
        }

        $routingServices = $serviceManager->getTaggedServices('routes');
        foreach ($routingServices as $serviceName => $routingService) {
            $routingClasses[$routingService->getClass()] = $serviceName;
        }

        return $routingClasses;
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
        if (is_string($this->serviceManager)) {
            $this->serviceManager = $this->app->container->get(ServiceManager::getServiceName($this->serviceManager));
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
     * @return Parser
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
     * @param Parser|string $annotationParser
     */
    public function setAnnotationParser($annotationParser)
    {
        $this->annotationParser = $annotationParser;
    }


    /**
     * @param RouteAnnotation $annotation Annotation object containing route details
     * @param string          $service    Name of the service that will handle this request
     * @param string          $method     Name of the method within the service that will handle the request
     *
     * @throws MissingMethodsException if the 'methods' part of the annotation is missing
     */
    protected function addRoute(RouteAnnotation $annotation, $service, $method)
    {
        $serviceManager = $this->getServiceManager();

        $route = $this->app->map(
            $annotation->getPattern(),
            function () use ($serviceManager, $service, $method) {
                $service = $serviceManager->getService($service);

                return call_user_func_array([$service, $method], func_get_args());
            }
        );

        $methods = $annotation->getMethods();
        if (empty($methods)) {
            throw MissingMethodsException::build(
                [],
                [
                    'service'    => $service,
                    'method'     => $method,
                    'annotation' => $annotation
                ]
            );
        }
        call_user_func_array([$route, 'via'], $methods);

        $routeName = $annotation->getName();
        if (!empty($routeName)) {
            $route->name($routeName);
        }
    }
}