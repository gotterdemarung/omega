<?php

namespace OmegaTest\Type;

use Omega\Type\ChainNode;
use OmegaTest\Test;

class ChainNodeTest extends Test
{

    public function testConstructor()
    {
        // Null
        $x = new ChainNode();
        $this->assertTrue($x->isEmpty());
        $this->assertTrue($x->isNull());

        // Scalar
        $x = new ChainNode(5);
        $this->assertFalse($x->isEmpty());
        $this->assertFalse($x->isNull());
        $this->assertTrue($x->isInt());
        $x = new ChainNode(0);
        $this->assertTrue($x->isEmpty());
        $this->assertFalse($x->isNull());
        $this->assertTrue($x->isInt());
        $x = new ChainNode('string');
        $this->assertFalse($x->isEmpty());
        $this->assertFalse($x->isNull());
        $this->assertTrue($x->isString());

        // Array
        $x = new ChainNode(array());
        $this->assertTrue($x->isEmpty());
        $this->assertFalse($x->isNull());
        $this->assertTrue($x->isArrayAccess());
        $x = new ChainNode(array(1,2));
        $this->assertFalse($x->isEmpty());
        $this->assertFalse($x->isNull());
        $this->assertTrue($x->isArrayAccess());
    }

    public function testGetInt()
    {
        // Normal
        $x = new ChainNode(-5);
        $this->assertSame(-5, $x->getInt());

        // Casting
        $x = new ChainNode('-8');
        $this->assertSame(-8, $x->getInt());

        // Default
        $x = new ChainNode('string');
        $this->assertSame(-10, $x->getInt(-10));

        // Exception
        try {
            $x = new ChainNode('string');
            $x->getInt();
            $this->fail();
        } catch (\LogicException $e) {
            $this->assertTrue(true);
        }
        try {
            $x = new ChainNode('5.4');
            $x->getInt();
            $this->fail();
        } catch (\LogicException $e) {
            $this->assertTrue(true);
        }
    }

    public function testGetIterator()
    {
        $x = new ChainNode(array(1,2,4));
        $sum = 0;
        foreach ($x as $row) {
            /** @var ChainNode */
            $sum += $row->getInt();
        }
        $this->assertSame(7, $sum);
    }

    public function testGetString()
    {
        $x = new ChainNode('str');
        $this->assertSame('str', $x->getString());

        // Exception
        try {
            $x = new ChainNode(5);
            $x->getString();
            $this->fail();
        } catch(\LogicException $e) {
            $this->assertTrue(true);
        }
        try {
            $x = new ChainNode(array(2));
            $x->getString();
            $this->fail();
        } catch(\LogicException $e) {
            $this->assertTrue(true);
        }
    }

    public function testIsEmpty()
    {
        $x = new ChainNode();
        $this->assertTrue($x->isEmpty());
        $x = new ChainNode(0);
        $this->assertTrue($x->isEmpty());
        $x = new ChainNode('');
        $this->assertTrue($x->isEmpty());
        $x = new ChainNode(null);
        $this->assertTrue($x->isEmpty());
        $x = new ChainNode(false);
        $this->assertTrue($x->isEmpty());
        $x = new ChainNode(array());
        $this->assertTrue($x->isEmpty());
        $x = new ChainNode(1);
        $this->assertFalse($x->isEmpty());
    }

    public function testIsArrayAccess()
    {
        $x = new ChainNode();
        $this->assertFalse($x->isArrayAccess());
        $x = new ChainNode(4);
        $this->assertFalse($x->isArrayAccess());
        $x = new ChainNode('as');
        $this->assertFalse($x->isArrayAccess());
        $x = new ChainNode(array());
        $this->assertTrue($x->isArrayAccess());
        $x = new ChainNode(new ChainNode());
        $this->assertTrue($x->isArrayAccess());
    }

    public function testIsBool()
    {
        $x = new ChainNode();
        $this->assertFalse($x->isBool());
        $x = new ChainNode(0);
        $this->assertFalse($x->isBool());
        $x = new ChainNode(1);
        $this->assertFalse($x->isBool());
        $x = new ChainNode(true);
        $this->assertTrue($x->isBool());
        $x = new ChainNode(false);
        $this->assertTrue($x->isBool());
    }

    public function testIsInt()
    {
        $x = new ChainNode();
        $this->assertFalse($x->isInt());
        $x = new ChainNode(false);
        $this->assertFalse($x->isInt());
        $x = new ChainNode(null);
        $this->assertFalse($x->isInt());
        $x = new ChainNode(array(1));
        $this->assertFalse($x->isInt());
        $x = new ChainNode(1.3);
        $this->assertFalse($x->isInt());

        $x = new ChainNode('1');
        $this->assertTrue($x->isInt());
        $x = new ChainNode(1324);
        $this->assertTrue($x->isInt());
    }

    public function testIsNull()
    {
        $x = new ChainNode();
        $this->assertTrue($x->isNull());
        $x = new ChainNode(null);
        $this->assertTrue($x->isNull());

        $x = new ChainNode(false);
        $this->assertFalse($x->isNull());
        $x = new ChainNode(0);
        $this->assertFalse($x->isNull());
        $x = new ChainNode('');
        $this->assertFalse($x->isNull());
    }

    public function testIsString()
    {
        $x = new ChainNode('abc');
        $this->assertTrue($x->isString());
        $x = new ChainNode('123');
        $this->assertTrue($x->isString());

        $x = new ChainNode(123);
        $this->assertFalse($x->isString());
        $x = new ChainNode(true);
        $this->assertFalse($x->isString());
        $x = new ChainNode(null);
        $this->assertFalse($x->isString());
        $x = new ChainNode();
        $this->assertFalse($x->isString());
    }

    public function testIsTraversable()
    {
        $x = new ChainNode(array());
        $this->assertTrue($x->isTraversable());

        $x = new ChainNode(new ChainNode());
        $this->assertTrue($x->isTraversable());

        $x = new ChainNode();
        $this->assertFalse($x->isTraversable());
        $x = new ChainNode(1);
        $this->assertFalse($x->isTraversable());
        $x = new ChainNode(true);
        $this->assertFalse($x->isTraversable());
        $x = new ChainNode('bar');
        $this->assertFalse($x->isTraversable());
    }

    public function testIsTrue()
    {
        $x = new ChainNode(1);
        $this->assertFalse($x->isTrue());
        $x = new ChainNode(0);
        $this->assertFalse($x->isTrue());
        $x = new ChainNode(false);
        $this->assertFalse($x->isTrue());
        $x = new ChainNode(null);
        $this->assertFalse($x->isTrue());
        $x = new ChainNode('true');
        $this->assertFalse($x->isTrue());
        $x = new ChainNode(true);
        $this->assertTrue($x->isTrue());
    }

    public function testEquals()
    {
        $x = new ChainNode(10);
        $this->assertTrue($x->equals(10));
        $this->assertFalse($x->equals(9));
        $this->assertFalse($x->equals('10'));
        $this->assertTrue($x->equals(new ChainNode(10)));

        $x = new ChainNode('str');
        $this->assertTrue($x->equals('str'));
        $this->assertFalse($x->equals('str2'));
        $this->assertTrue($x->equals(new ChainNode('str')));
    }

    public function testArrayAccess()
    {
        $x = new ChainNode(array('foo'=>'bar', 'x'=>3));
        $this->assertCount(2, $x);

        $this->assertSame('bar', $x['foo']->getString());
        $this->assertSame('bar', $x->foo->getString());
        $this->assertSame(3, $x['x']->getInt());
        $this->assertSame(3, $x->x->getInt());

        $this->assertTrue(isset($x['foo']));
        $this->assertFalse(isset($x['foo2']));

        $x['foo2'] = 14;
        $this->assertTrue(isset($x['foo2']));
        $this->assertSame(14, $x->foo2->getInt());
        $this->assertCount(3, $x);

        unset($x['foo2']);
        $x->foo = 'baz';
        $this->assertCount(2, $x);
        $this->assertSame('baz', $x->foo->getString());
        $this->assertSame(3, $x['x']->getInt());
    }

    public function testTree()
    {
        $tree = new ChainNode(array(
            'base' => array(
                'foo' => 'bar',
                'child' => array(
                    'one' => 1,
                    'two' => 2,
                    'sub' => array(
                        'three' => 3,
                        'four' => 4
                    )
                )
            )
        ));

        $this->assertCount(1, $tree);
        $this->assertTrue($tree->isTraversable());
        $this->assertTrue($tree->base->isTraversable());
        $this->assertSame('bar', $tree->base->foo->getString());
        $this->assertSame(3, $tree->base->child->sub->three->getInt());
        $this->assertSame(4, $tree->path('base.child.sub.four')->getInt());
        $this->assertTrue($tree->base->notExists->isEmpty());
        $this->assertTrue($tree->path('base.notvalid')->isNull());
    }



}