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
use Psr\Container\ContainerInterface;
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
 * ResolverTest
 */
abstract class ResolverTest extends TestCase
{   
    abstract protected function createResolver(): ResolverInterface;

    public function testContainerMethodIsPrs11()
    {
        $this->assertInstanceof(
            ContainerInterface::class,
            $this->createResolver()->container()
        );
    } 
    
    public function testThrowsNotFoundExceptionIfNotFound()
    {
        $this->expectException(NotFoundExceptionInterface::class);
        
        $this->createResolver()->get('bar');
    }
    
    public function testThrowsContainerExceptionIfNotResolvable()
    {
        $this->expectException(ContainerExceptionInterface::class);
        
        $this->createResolver()->get(WithBuildInParameter::class);
    }    
    
    public function testWithClassName()
    {
        $r = $this->createResolver();
        
        $this->assertInstanceOf('stdClass', $r->get('stdClass'));
    }
    
    public function testResolvesEntryOnce()
    {
        $r = $this->createResolver();
        
        $this->assertSame($r->get('stdClass'), $r->get('stdClass'));
    }    
        
    public function testWithParameters()
    {
        $r = $this->createResolver();
        
        $this->assertInstanceOf(
            WithParameters::class,
            $r->get(WithParameters::class)
        );
    }
    
    public function testWithUnionParameterResolvesFirstFound()
    {
        $r = $this->createResolver();
        
        $resolved = $r->get(WithUnionParameter::class);
            
        $this->assertInstanceOf(
            Foo::class,
            $resolved->getName()
        );
    }
    
    public function testWithUnionParameterResolvesFirstFoundIfAllowsNull()
    {
        $r = $this->createResolver();
        
        $resolved = $r->get(WithUnionParameterAllowsNull::class);
            
        $this->assertInstanceOf(
            Foo::class,
            $resolved->getName()
        );
    }
    
    public function testWithUnionParameterAllowsNullAddsNullIfNotFound()
    {
        $r = $this->createResolver();
        
        $resolved = $r->get(WithUnionParameterAllowsNullNotFound::class);
        
        $this->assertSame(
            null,
            $resolved->getName()
        );
    }
    
    public function testResolvesHasMethod()
    {
        $r = $this->createResolver();
        
        $this->assertTrue($r->has(Foo::class));
        
        $this->assertFalse($r->has('foo'));
        
        $r->set('foo', 'value');
            
        $this->assertTrue($r->has('foo'));
    }    
}