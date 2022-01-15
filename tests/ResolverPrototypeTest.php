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
 * ResolverPrototypeTest
 */
abstract class ResolverPrototypeTest extends TestCase
{   
    abstract protected function createResolver(): ResolverInterface;
    
    public function testReturnsNewInstance()
    {
        $r = $this->createResolver();
        
        $this->assertSame($r->get('stdClass'), $r->get('stdClass'));
        
        $r->set('stdClass')->prototype();
        
        $this->assertFalse($r->get('stdClass') === $r->get('stdClass'));
    }
    
    public function testReturnsNewInstanceWithClosureDefinition()
    {
        $r = $this->createResolver();
        
        $r->set('closure', function () {
            return new Foo();
        })->prototype();
        
        $this->assertFalse($r->get('closure') === $r->get('closure'));
    }
    
    public function testReturnsNewInstanceWithClassDefinition()
    {
        $r = $this->createResolver();
        
        $r->set(Foo::class)->prototype();
        
        $this->assertFalse($r->get(Foo::class) === $r->get(Foo::class));
    }
    
    public function testReturnsNewInstanceWithInterfaceDefinition()
    {
        $r = $this->createResolver();
        
        $r->set(FooInterface::class, Foo::class)->prototype();
        
        $this->assertFalse($r->get(FooInterface::class) === $r->get(FooInterface::class));
    }    
    
    public function testOnStringHasNoImpact()
    {
        $r = $this->createResolver();
        
        $r->set('id', 'value')->prototype();
        
        $this->assertSame('value', $r->get('id'));
    }    
}