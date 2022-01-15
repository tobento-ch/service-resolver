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
 * ResolverMakeTest
 */
abstract class ResolverMakeTest extends TestCase
{   
    abstract protected function createResolver(): ResolverInterface;

    public function testThrowsContainerExceptionIfNotResolvable()
    {
        $this->expectException(ContainerExceptionInterface::class);
        
        $this->createResolver()->make(WithBuildInParameter::class);
    }    
    
    public function testWithClassName()
    {
        $r = $this->createResolver();
        
        $this->assertInstanceOf('stdClass', $r->make('stdClass'));
    }
    
    public function testReturnsNewInstances()
    {
        $r = $this->createResolver();
        
        $this->assertFalse($r->make(Foo::class) === $r->make(Foo::class));
    }
    
    public function testUsesParameters()
    {
        $r = $this->createResolver();
        
        $foo = new Foo();
        
        $r->set(WithParameter::class)->construct(new Foo());
        
        $resolved = $r->make(WithParameter::class, ['name' => $foo]);
                
        $this->assertSame(
            $foo,
            $resolved->getName()
        );
    }
    
    public function testUsesParametersPosition()
    {
        $r = $this->createResolver();
        
        $foo = new Foo();
        
        $r->set(WithParameter::class)->construct(new Foo());
        
        $resolved = $r->make(WithParameter::class, [0 => $foo]);
                
        $this->assertSame(
            $foo,
            $resolved->getName()
        );
    } 
    
    public function testWithParameters()
    {
        $r = $this->createResolver();
                
        $this->assertInstanceOf(
            WithParameters::class,
            $r->make(WithParameters::class)
        );
    }
}