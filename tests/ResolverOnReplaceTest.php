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
    GuestUser,
    AdminUser,
    EditorUser,
    User,
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
 * ResolverOnReplaceTest
 */
abstract class ResolverOnReplaceTest extends TestCase
{
    abstract protected function createResolver(): ResolverInterface;

    public function testReturnsOnRule()
    {        
        $rule = $this->createResolver()->on('name');
        
        $this->assertInstanceof(
            OnRule::class,
            $rule
        );
    }
    
    public function testWithClassname()
    {
        $resolver = $this->createResolver();
        
        $resolver->on(Bar::class, Baz::class);
        
        $this->assertInstanceof(
            Baz::class,
            $resolver->get(Bar::class)
        );
    }
    
    public function testWithClassnamePrototype()
    {
        $resolver = $this->createResolver();
        
        $resolver->set(Bar::class)->prototype();
        
        $resolver->on(Bar::class, Baz::class)->once(false)->prototype();
        
        $this->assertFalse(
            $resolver->get(Bar::class) === $resolver->get(Bar::class)
        );        
        
        $this->assertInstanceof(
            Baz::class,
            $resolver->get(Bar::class)
        );
        
        $this->assertInstanceof(
            Baz::class,
            $resolver->get(Bar::class)
        );        
    }    
    
    public function testWithObject()
    {
        $resolver = $this->createResolver();
        
        $baz = new Baz();
        
        $resolver->on(Bar::class, $baz);
        
        $this->assertSame(
            $baz,
            $resolver->get(Bar::class)
        );
    }
    
    public function testWithCallable()
    {
        $resolver = $this->createResolver();
        
        $resolver->on(Bar::class, function(Bar $bar) {
            return new Baz();
        });
        
        $this->assertInstanceof(
            Baz::class,
            $resolver->get(Bar::class)
        );
    }
    
    public function testWithCallableTypehinted()
    {
        $resolver = $this->createResolver();
        
        $resolver->on(Bar::class, function(Bar $bar, Baz $baz) {
            return $baz;
        });
        
        $this->assertInstanceof(
            Baz::class,
            $resolver->get(Bar::class)
        );
    }
    
    public function testWithInterface()
    {
        $resolver = $this->createResolver();
        
        $resolver->set(FooInterface::class, Foo::class);
        
        $resolver->on(FooInterface::class, Baz::class);
        
        $this->assertInstanceof(
            Baz::class,
            $resolver->get(FooInterface::class)
        );
    }
    
    public function testWithInstanceof()
    {
        $resolver = $this->createResolver();
        
        $resolver->set(UserInterface::class, GuestUser::class);
        
        $resolver->on(User::class, function($user) {
            return new AdminUser();
        })->instanceof();
        
        $this->assertInstanceof(
            AdminUser::class,
            $resolver->get(UserInterface::class)
        );
    }    
    
    public function testWithPriority()
    {
        $resolver = $this->createResolver();

        $resolver->on(AdminUser::class, EditorUser::class)
                 ->priority(500);

        $resolver->on(AdminUser::class, GuestUser::class)
                 ->priority(1000);
        
        $this->assertInstanceof(
            EditorUser::class,
            $resolver->get(AdminUser::class)
        );
    }    
}