<?php
namespace Omega\Routing;


use Omega\DI\HTTPRequestDI;
use Symfony\Component\HttpFoundation\Request;

class ReflectionRegexRoute implements RouteInterface, HTTPRequestDI {

    /**
     * @var Request
     */
    private $_request;
    /**
     * @var string
     */
    private $_regex;
    /**
     * @var mixed
     */
    private $_controller;
    /**
     * @var string
     */
    private $_method;

    public function __construct($regularExpression, $controller, $methodName)
    {
        $this->_regex = $regularExpression;
        $this->_controller = $controller;
        $this->_method = $methodName;
    }

    /**
     * Sets HTTP Foundation Request
     *
     * @param Request $request
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * Returns HTTP Foundation Request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Returns true if current route can satisfy provided request
     *
     * @param Request $request
     * @return bool
     */
    public function isSatisfied(Request $request)
    {
        return preg_match($this->_regex, $request->getUri()) > 0;
    }

    /**
     * Runs execution
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        // Creating reflection instance
        $reflection = new \ReflectionClass($this->_controller);
        $method = $reflection->getMethod($this->_method);

        // Reading request
        $request = $this->getRequest();
        if ($request === null && $method->getNumberOfParameters() > 0) {
            throw new \BadMethodCallException(
                'HTTP Foundation Request not set'
            );
        }

        // Setting known DI instances
        if ($this->_controller instanceof HTTPRequestDI && $request !== null) {
            $this->_controller->setRequest($request);
        }

        // Iterating over parameters
        $invokeData = array();
        foreach ($method->getParameters() as $parameter) {
            $parameterName = $parameter->getName();

            // Reading from query
            if (!$request->query->has($parameterName)) {
                throw new \BadMethodCallException("Required parameter `{$parameterName}` not present");
            }
            $invokeData[] = $request->query->get($parameterName);
        }

        // Invoke
        $method->invokeArgs($this->_controller, $invokeData);
    }


} 