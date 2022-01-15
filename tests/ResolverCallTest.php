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
 * ResolverCallTest
 */
abstract class ResolverCallTest extends TestCase
{   
    abstract protected function createResolver(): ResolverInterface;

    public function testThrowsContainerExceptionIfNotCallable()
    {
        $this->expectException(ContainerExceptionInterface::class);
        
        $this->createResolver()->call(WithBuildInParameter::class);
    }

    public function testStringClassMethodSyntax()
    {
        $this->assertSame(
            'withoutParameters',
            $this->createResolver()->call(
                'Tobento\Service\Resolver\Test\Mock\Methods::withoutParameters'
            )
        );
    }
    
    public function testClosure()
    {
        $this->assertSame(
            'hellow',
            $this->createResolver()->call(
                function() {
                    return 'hellow';
                }
            )
        );
    }
    
    public function testClassGetsInvoked()
    {
        $this->assertSame(
            'invoked',
            $this->createResolver()->call(
                Invokable::class
            )
        );
    }    
    
    public function testClosureWithParametersGetsAutowired()
    {
        $this->assertInstanceof(
            Foo::class,
            $this->createResolver()->call(
                function(Foo $foo) {
                    return $foo;
                }
            )
        );
    }
    
    public function testClosureWithParametersUsesSetParamaters()
    {
        $foo = new Foo();
        
        $this->assertSame(
            $foo,
            $this->createResolver()->call(
                function(Foo $foo) {
                    return $foo;
                },
                [$foo]
            )
        );
    }
    
    public function testArrayWithClassInstance()
    {
        $this->assertSame(
            'withoutParameters',
            $this->createResolver()->call(
                [new Methods(new Baz()), 'withoutParameters']
            )
        );
    }
    
    public function testArrayWithClassName()
    {
        $this->assertSame(
            'withoutParameters',
            $this->createResolver()->call(
                [Methods::class, 'withoutParameters']
            )
        );
    }
    
    public function testThrowsContainerExceptionIfParameterIsNotResolvable()
    {
        $this->expectException(ContainerExceptionInterface::class);

        $this->createResolver()->call(
            [Methods::class, 'withBuildInParameter']
        );
    }
    
    public function testThrowsContainerExceptionIfMethodIsPrivate()
    {
        $this->expectException(ContainerExceptionInterface::class);

        $this->createResolver()->call(
            [Methods::class, 'withPrivateMethod']
        );
    }    
    
    public function testWithBuildInParameter()
    {        
        $this->assertSame(
            'welcome',
            $this->createResolver()->call(
                [Methods::class, 'withBuildInParameter'],
                ['welcome']
            )
        );
    }
    
    public function testWithBuildInParameterAllowsNull()
    {        
        $this->assertSame(
            null,
            $this->createResolver()->call(
                [Methods::class, 'withBuildInParameterAllowsNull'],
            )
        );
    } 
    
    public function testWithBuildInParameterAllowsNullButUsesParam()
    {        
        $this->assertSame(
            'welcome',
            $this->createResolver()->call(
                [Methods::class, 'withBuildInParameterAllowsNull'],
                ['welcome']
            )
        );
    }
    
    public function testWithBuildInParameterOptional()
    {        
        $this->assertSame(
            null,
            $this->createResolver()->call(
                [Methods::class, 'withBuildInParameterOptional']
            )
        );
    }
    
    public function testWithBuildInParameterOptionalButUsesParam()
    {        
        $this->assertSame(
            'welcome',
            $this->createResolver()->call(
                [Methods::class, 'withBuildInParameterOptional'],
                ['welcome']
            )
        );
    }
 
    public function testWithParameter()
    {
        $foo = new Foo();
        
        $this->assertSame(
            $foo,
            $this->createResolver()->call(
                [Methods::class, 'withParameter'],
                [$foo]
            )
        );
    } 
    
    public function testWithParameterGetsAutowired()
    {        
        $this->assertInstanceof(
            Foo::class,
            $this->createResolver()->call(
                [Methods::class, 'withParameter']
            )
        );
    }
}