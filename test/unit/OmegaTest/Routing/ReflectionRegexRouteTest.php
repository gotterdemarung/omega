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
        $r->process(null);
        $this->assertSame('zero', $marker->value);
    }

    public function testTwoArguments()
    {
        $marker = new ReflectionRegexRouteTestController();
        $r = new ReflectionRegexRoute(null, $marker, 'twoArguments');
        $r->process(Request::create(
            'http://localhost/?one=11!!&two=Tw0',
            'GET'
        ));
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
        $r->process(null);
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
        $r->process(Request::create(
            'http://localhost/?one=11!!',
            'GET'
        ));
        $this->fail();
    }
}

class ReflectionRegexRouteTestController
{
    public $value;

    public function zeroArguments()
    {
        $this->value = 'zero';
    }

    public function twoArguments($two, $one)
    {
        $this->value = $one . '|' . $two;
    }
}