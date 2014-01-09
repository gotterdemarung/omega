<?php
namespace Omega\Routing;


use Omega\DI\HTTPRequestDI;
use Symfony\Component\HttpFoundation\Request;

class ReflectionRegexRoute implements RouteInterface
{
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
     * @param Request $request
     * @throws \BadMethodCallException
     * @return void
     */
    public function process(Request $request)
    {
        // Creating reflection instance
        $reflection = new \ReflectionClass($this->_controller);
        $method = $reflection->getMethod($this->_method);

        // Reading request
        if ($request === null && $method->getNumberOfParameters() > 0) {
            throw new \BadMethodCallException(
                'HTTP Foundation Request not set'
            );
        }

        // Iterating over parameters
        $invokeData = array();
        foreach ($method->getParameters() as $parameter) {
            $parameterName = $parameter->getName();

            // Reading from query
            if (!$request->query->has($parameterName)) {
                throw new \BadMethodCallException(
                    "Required parameter `{$parameterName}` not present"
                );
            }
            $invokeData[] = $request->query->get($parameterName);
        }

        // Invoke
        $method->invokeArgs($this->_controller, $invokeData);
    }


} 