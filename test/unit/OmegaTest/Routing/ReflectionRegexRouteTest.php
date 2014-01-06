<?php

namespace OmegaTest\Routing;

use Omega\DI\HTTPRequestDI;
use Omega\Routing\ReflectionRegexRoute;
use OmegaTest\Test;
use Symfony\Component\HttpFoundation\Request;

class ReflectionRegexRouteTest extends Test {

    public function testIsSatisfied()
    {
        $r = new ReflectionRegexRoute('%x{2,3}%', null, null);

        $this->assertTrue($r->isSatisfied(Request::create(
            'http://localhost/xxx.rss',
            'GET'
        )));
        $this->assertTrue($r->isSatisfied(Request::create(
            'http://localhost/xx.rss',
            'GET'
        )));
        $this->assertFalse($r->isSatisfied(Request::create(
            'http://localhost/x.rss',
            'GET'
        )));
        $this->assertFalse($r->isSatisfied(Request::create(
            'http://localhost/yyy.rss',
            'GET'
        )));
    }

    public function testZeroArguments()
    {
        $marker = new ReflectionRegexRouteTestController();
        $r = new ReflectionRegexRoute(null, $marker, 'zeroArguments');
        $r->run();
        $this->assertSame('zero', $marker->value);
    }

    public function testTwoArguments()
    {
        $marker = new ReflectionRegexRouteTestController();
        $r = new ReflectionRegexRoute(null, $marker, 'twoArguments');
        $r->setRequest(Request::create(
            'http://localhost/?one=11!!&two=Tw0',
            'GET'
        ));
        $r->run();
        $this->assertSame('11!!|Tw0', $marker->value);
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage HTTP Foundation Request not set
     */
    public function testTwoArgumentsWithNoRequest()
    {
        $marker = new ReflectionRegexRouteTestController();
        $r = new ReflectionRegexRoute(null, $marker, 'twoArguments');
        $r->run();
        $this->fail();
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Required parameter `two` not present
     */
    public function testTwoArgumentsWithOnlyOne()
    {
        $marker = new ReflectionRegexRouteTestController();
        $r = new ReflectionRegexRoute(null, $marker, 'twoArguments');
        $r->setRequest(Request::create(
            'http://localhost/?one=11!!',
            'GET'
        ));
        $r->run();
        $this->fail();
    }

    public function testRequestDI()
    {
        $marker = new ReflectionRegexRouteTestController();
        $r = new ReflectionRegexRoute(null, $marker, 'zeroArguments');
        $r->run();
        $this->assertNull($marker->getRequest());

        $r->setRequest(Request::create(
            'http://localhost/?helloWorld',
            'GET'
        ));
        $r->run();
        $this->assertNotNull($marker->getRequest());
        $this->assertSame('http://localhost/?helloWorld', $marker->getRequest()->getUri());
    }

}

class ReflectionRegexRouteTestController implements HTTPRequestDI
{
    public $value;
    private $_request;

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


    public function zeroArguments()
    {
        $this->value = 'zero';
    }

    public function twoArguments($two, $one)
    {
        $this->value = $one . '|' . $two;
    }
}