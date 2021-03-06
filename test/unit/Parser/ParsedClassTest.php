<?php

declare(strict_types=1);

namespace ParaTest\Parser;

class ParsedClassTest extends \TestBase
{
    protected $class;
    protected $methods;

    public function setUp()
    {
        $this->methods = [
            new ParsedFunction(
                '/**
              * @group group1
              */',
                'public', 'testFunction'
            ),
            new ParsedFunction(
                '/**
              * @group group2
              */',
                'public', 'testFunction2'
            ),
            new ParsedFunction('', 'public', 'testFunction3'),
        ];
        $this->class = new ParsedClass('', 'MyTestClass', '', $this->methods);
    }

    public function testGetMethodsReturnsMethods()
    {
        $this->assertEquals($this->methods, $this->class->getMethods());
    }

    public function testGetMethodsMultipleAnnotationsReturnsMethods()
    {
        $goodMethod = new ParsedFunction(
            '/**
              * @group group1
              */',
            'public', 'testFunction'
        );
        $goodMethod2 = new ParsedFunction(
            '/**
              * @group group2
              */',
            'public', 'testFunction2'
        );
        $badMethod = new ParsedFunction(
            '/**
              * @group group3
              */',
            'public', 'testFunction2'
        );
        $annotatedClass = new ParsedClass('', 'MyTestClass', '', [$goodMethod, $goodMethod2, $badMethod]);
        $methods = $annotatedClass->getMethods(['group' => 'group1,group2']);
        $this->assertEquals([$goodMethod, $goodMethod2], $methods);
    }

    public function testGetMethodsExceptsAdditionalAnnotationFilter()
    {
        $group1 = $this->class->getMethods(['group' => 'group1']);
        $this->assertEquals(1, count($group1));
        $this->assertEquals($this->methods[0], $group1[0]);
    }
}
