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
use Tobento\Service\Resolver\RuleInterface;
use Tobento\Service\Resolver\OnRule;
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

/**
 * ResolverOnModifyTest
 */
abstract class ResolverOnModifyTest extends TestCase
{
    abstract protected function createResolver(): ResolverInterface;
    
    public function testWithCallableReturningNone()
    {
        $resolver = $this->createResolver();
        
        $resolver->on(Methods::class, function(Methods $methods) {
            $methods->withBuildInParameter('foo');
        });
        
        $this->assertSame(
            [
                0 => 'withBuildInParameter',
            ],
            $resolver->get(Methods::class)->getCalled()
        );
    }
    
    public function testWithCallableReturningObject()
    {
        $resolver = $this->createResolver();
        
        $resolver->on(Methods::class, function(Methods $methods) {
            $methods->withBuildInParameter('foo');
            return $methods;    
        });
        
        $this->assertSame(
            [
                0 => 'withBuildInParameter',
            ],
            $resolver->get(Methods::class)->getCalled()
        );
    }    
    
    public function testWithCallableTypehinted()
    {
        $resolver = $this->createResolver();
        
        $resolver->on(Methods::class, function(Methods $methods, Bar $name) {
            $methods->withUnionParameter($name);
            return $methods;    
        });
        
        $this->assertSame(
            [
                0 => 'withUnionParameter',
            ],
            $resolver->get(Methods::class)->getCalled()
        );
    }
    
    public function testWithInterface()
    {
        $resolver = $this->createResolver();
        
        $resolver->set(FooInterface::class, Methods::class);
        
        $resolver->on(FooInterface::class, function(Methods $methods, Bar $name) {
            $methods->withUnionParameter($name);
            return $methods;    
        });
        
        $this->assertSame(
            [
                0 => 'withUnionParameter',
            ],
            $resolver->get(FooInterface::class)->getCalled()
        );
    }
    
    public function testWithPriority()
    {
        $resolver = $this->createResolver();
        
        $resolver->set(FooInterface::class, Methods::class);
        
        $resolver->on(FooInterface::class, function(Methods $methods, Bar $name) {
            $methods->withUnionParameter($name);
            return $methods;    
        })->priority(500);
        
        $resolver->on(FooInterface::class, function(Methods $methods, Foo $foo) {
            $methods->withParameter($foo);
            return $methods;    
        })->priority(1000);        
        
        $this->assertSame(
            [
                0 => 'withParameter',
                1 => 'withUnionParameter',
            ],
            $resolver->get(FooInterface::class)->getCalled()
        );
    }    
}