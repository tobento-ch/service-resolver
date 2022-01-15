<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Resolver\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Resolver\ResolverInterface;
use Tobento\Service\Resolver\DefinitionInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tobento\Service\Resolver\Test\Mock\{
    Foo,
    Bar,
    Baz,
    Methods,
    Invokable,
    FooInterface,
    WithBuildInParameter,
    WithBuildInParameterOptional,
    WithBuildInParameterAllowsNull,
    WithBuildInParameterAndClasses,
    WithParameter,
    WithParameters,
    WithoutParameters,
    WithUnionParameter,
    WithUnionParameterAllowsNull,
    WithUnionParameterAllowsNullNotFound
};
use stdClass;

/**
 * ResolverSetTest
 */
abstract class ResolverSetTest extends TestCase
{   
    abstract protected function createResolver(): ResolverInterface;

    public function testReturnsDefinitionInterface()
    {
        $this->assertInstanceof(
            DefinitionInterface::class,
            $this->createResolver()->set('id', 'value')
        );
    }

    public function testSetString()
    {
        $r = $this->createResolver();
        
        $r->set('id', 'value');
        
        $this->assertSame('value', $r->get('id'));
    }
    
    public function testSetClosure()
    {
        $r = $this->createResolver();
        
        $r->set('closure', function () {
            return 'value';
        });
        
        $this->assertSame('value', $r->get('closure'));
    }
    
    public function testSetClassName()
    {
        $r = $this->createResolver();
        
        $r->set('id', 'stdClass');
        
        $this->assertInstanceOf('stdClass', $r->get('id'));
    }
        
    public function testWithUnionParameterUsesSetParameter()
    {
        $r = $this->createResolver();
        
        $foo = new Foo();
        
        $r->set(WithUnionParameterAllowsNull::class)->construct($foo);
        
        $resolved = $r->get(WithUnionParameterAllowsNull::class);
                
        $this->assertSame(
            $foo,
            $resolved->getName()
        );
    }
    
    public function testWithUnionParameterUsesSetParameterWithPosition()
    {
        $r = $this->createResolver();
        
        $foo = new Foo();
        
        $r->set(WithUnionParameterAllowsNull::class)->with([0 => $foo]);
        
        $resolved = $r->get(WithUnionParameterAllowsNull::class);
                
        $this->assertSame(
            $foo,
            $resolved->getName()
        );
    }
    
    public function testWithUnionParameterUsesSetParameterWithNamed()
    {
        $r = $this->createResolver();
        
        $foo = new Foo();
        
        $r->set(WithUnionParameterAllowsNull::class)->with(['name' => $foo]);
        
        $resolved = $r->get(WithUnionParameterAllowsNull::class);
                
        $this->assertSame(
            $foo,
            $resolved->getName()
        );
    }    
    
    public function testUsesSetParameter()
    {
        $r = $this->createResolver();
        
        $foo = new Foo();
        
        $r->set(WithParameter::class)->construct($foo);
        
        $resolved = $r->get(WithParameter::class);
                
        $this->assertSame(
            $foo,
            $resolved->getName()
        );
    }

    public function testDefineNameUsesAutowiring()
    {
        $r = $this->createResolver();
        
        $r->set('withParameter', WithParameter::class);
        
        $resolved = $r->get('withParameter');
                
        $this->assertInstanceof(
            Foo::class,
            $resolved->getName()
        );
    }
    
    public function testDefineNameUsesSetParameter()
    {
        $r = $this->createResolver();
        
        $foo = new Foo();
        
        $r->set('withParameter', WithParameter::class)->construct($foo);
        
        $resolved = $r->get('withParameter');
                
        $this->assertSame(
            $foo,
            $resolved->getName()
        );
    }
    
    public function testDefineSameClass()
    {
        $r = $this->createResolver();
        
        $withParameter = new WithParameter(new Foo());
        
        $r->set(WithParameter::class, $withParameter);
        
        $resolved = $r->get(WithParameter::class);
                
        $this->assertSame(
            $withParameter,
            $resolved
        );
    }
    
    public function testCallMethod()
    {
        $r = $this->createResolver();
        
        $r->set(Methods::class)->callMethod('withoutParameters');

        $resolved = $r->get(Methods::class);
                
        $this->assertSame(
            [
                'withoutParameters',
            ],
            $resolved->getCalled()
        );
    }
    
    public function testCallSameMethodTwice()
    {
        $r = $this->createResolver();
        
        $r->set(Methods::class)->callMethod('withoutParameters')->callMethod('withoutParameters');

        $resolved = $r->get(Methods::class);
                
        $this->assertSame(
            [
                'withoutParameters',
                'withoutParameters',
            ],
            $resolved->getCalled()
        );
    }
    
    public function testCallMultipleMethods()
    {
        $r = $this->createResolver();
        
        $r->set(Methods::class)
          ->callMethod('withoutParameters')
          ->callMethod('withBuildInParameter', ['name' => 'foo']);

        $resolved = $r->get(Methods::class);
                
        $this->assertSame(
            [
                'withoutParameters',
                'withBuildInParameter',
            ],
            $resolved->getCalled()
        );
    }
    
    public function testCallMethodWithParameter()
    {
        $r = $this->createResolver();
        
        $r->set(Methods::class)
          ->callMethod('withParameter');

        $resolved = $r->get(Methods::class);
                
        $this->assertSame(
            [
                'withParameter',
            ],
            $resolved->getCalled()
        );
    }
    
    public function testCallMethodWithParameters()
    {
        $r = $this->createResolver();
        
        $r->set(Methods::class)
          ->callMethod('withParameters');

        $resolved = $r->get(Methods::class);
                
        $this->assertSame(
            [
                'withParameters',
            ],
            $resolved->getCalled()
        );
    }
    
    public function testCallMethodWithBuildInParameter()
    {
        $r = $this->createResolver();
        
        $r->set(Methods::class)
          ->callMethod('withBuildInParameter', ['name' => 'foo']);

        $resolved = $r->get(Methods::class);
                
        $this->assertSame(
            [
                'withBuildInParameter',
            ],
            $resolved->getCalled()
        );
    }
    
    public function testCallMethodWithBuildInParameterAllowsNull()
    {
        $r = $this->createResolver();
        
        $r->set(Methods::class)
          ->callMethod('withBuildInParameterAllowsNull');

        $resolved = $r->get(Methods::class);
                
        $this->assertSame(
            [
                'withBuildInParameterAllowsNull',
            ],
            $resolved->getCalled()
        );
    }
    
    public function testCallMethodWithBuildInParameterOptional()
    {
        $r = $this->createResolver();
        
        $r->set(Methods::class)
          ->callMethod('withBuildInParameterOptional');

        $resolved = $r->get(Methods::class);
                
        $this->assertSame(
            [
                'withBuildInParameterOptional',
            ],
            $resolved->getCalled()
        );
    }
    
    public function testCallMethodWithUnionParameter()
    {
        $r = $this->createResolver();
        
        $r->set(Methods::class)
          ->callMethod('withUnionParameter');

        $resolved = $r->get(Methods::class);
                
        $this->assertSame(
            [
                'withUnionParameter',
            ],
            $resolved->getCalled()
        );
    }
    
    public function testCallMethodWithBuildInParameterAndClasses()
    {
        $r = $this->createResolver();
        
        $r->set(Methods::class)
          ->callMethod('withBuildInParameterAndClasses', ['name' => 'foo']);

        $resolved = $r->get(Methods::class);
                
        $this->assertSame(
            [
                'withBuildInParameterAndClasses',
            ],
            $resolved->getCalled()
        );
    }
    
    public function testCallMethodPrivateThrowsContainerExceptionInterface()
    {
        $this->expectException(ContainerExceptionInterface::class);
        
        $r = $this->createResolver();
        
        $r->set(Methods::class)
          ->callMethod('withPrivateMethod', ['name' => 'foo']);

        $resolved = $r->get(Methods::class);
    }    
}