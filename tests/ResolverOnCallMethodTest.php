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
    Methods,
    HasUserTrait,
    AdminUser,
    User
};

/**
 * ResolverOnCallMethodTest
 */
abstract class ResolverOnCallMethodTest extends TestCase
{
    abstract protected function createResolver(): ResolverInterface;
    
    public function testWithBuildInParameter()
    {
        $resolver = $this->createResolver();
        
        $resolver->on(Methods::class)
                 ->callMethod('withBuildInParameter', ['name' => 'foo']);
        
        $this->assertSame(
            [
                0 => 'withBuildInParameter',
            ],
            $resolver->get(Methods::class)->getCalled()
        );
    }
    
    public function testWithBuildInParameterAndDefinition()
    {
        $resolver = $this->createResolver();
        
        $resolver->set(Methods::class, Methods::class);
        
        $resolver->on(Methods::class)
                 ->callMethod('withBuildInParameter', ['name' => 'foo']);
        
        $this->assertSame(
            [
                0 => 'withBuildInParameter',
            ],
            $resolver->get(Methods::class)->getCalled()
        );
    }    
    
    public function testWithBuildInParameterAllowsNull()
    {
        $resolver = $this->createResolver();
        
        $resolver->on(Methods::class)
                 ->callMethod('withBuildInParameterAllowsNull');
        
        $this->assertSame(
            [
                0 => 'withBuildInParameterAllowsNull',
            ],
            $resolver->get(Methods::class)->getCalled()
        );
    }
    
    public function testWithParameter()
    {
        $resolver = $this->createResolver();
        
        $resolver->on(Methods::class)
                 ->callMethod('withParameter');
        
        $this->assertSame(
            [
                0 => 'withParameter',
            ],
            $resolver->get(Methods::class)->getCalled()
        );
    }
    
    public function testWithUnionParameter()
    {
        $resolver = $this->createResolver();
        
        $resolver->on(Methods::class)
                 ->callMethod('withUnionParameter');
        
        $this->assertSame(
            [
                0 => 'withUnionParameter',
            ],
            $resolver->get(Methods::class)->getCalled()
        );
    }
    
    public function testWithBuildInParameterAndClasses()
    {
        $resolver = $this->createResolver();
        
        $resolver->on(Methods::class)
                 ->callMethod('withBuildInParameterAndClasses', ['name' => 'foo']);
        
        $this->assertSame(
            [
                0 => 'withBuildInParameterAndClasses',
            ],
            $resolver->get(Methods::class)->getCalled()
        );
    }
    
    public function testTrait()
    {
        $resolver = $this->createResolver();

        $resolver->on(HasUserTrait::class)
                 ->trait()
                 ->callMethod('set', ['value' => 'foo']);
        
        $this->assertSame(
            [
                0 => 'foo',
            ],
            $resolver->get(AdminUser::class)->getCalled()
        );
    }
    
    public function testTraitGetsCalledOnceOnSameInstance()
    {
        $resolver = $this->createResolver();

        $resolver->on(HasUserTrait::class)
                 ->trait()
                 ->callMethod('set', ['value' => 'foo']);
        
        $user = $resolver->get(AdminUser::class);
            
        $this->assertSame(
            [
                0 => 'foo',
            ],
            $resolver->get(AdminUser::class)->getCalled()
        );
    }
    
    public function testTraitGetsCalledOnceWithDefinition()
    {
        $resolver = $this->createResolver();
        
        $resolver->set(AdminUser::class, AdminUser::class);
        
        $resolver->on(HasUserTrait::class)
                 ->trait()
                 ->callMethod('set', ['value' => 'foo']);

        $this->assertSame(
            [
                0 => 'foo',
            ],
            $resolver->get(AdminUser::class)->getCalled()
        );
        
        $this->assertSame(
            [
                0 => 'foo',
            ],
            $resolver->get(AdminUser::class)->getCalled()
        );        
    }
    
    public function testTraitGetsCalledOnceWithPrototypeDefiniton()
    {
        $resolver = $this->createResolver();
        
        $resolver->set(AdminUser::class)->prototype();
        
        $resolver->on(HasUserTrait::class)
                 ->trait()
                 ->once(false)
                 ->callMethod('set', ['value' => 'bar'])
                 ->callMethod('set', ['value' => 'foo']);

        $this->assertSame(
            [
                0 => 'bar',
                1 => 'foo',
            ],
            $resolver->get(AdminUser::class)->getCalled()
        );
        
        $this->assertSame(
            [
                0 => 'bar',
                1 => 'foo',
            ],
            $resolver->get(AdminUser::class)->getCalled()
        );
    }
    
    public function testWithPriority()
    {
        $resolver = $this->createResolver();

        $resolver->on(HasUserTrait::class)
                 ->trait()
                 ->priority(500)
                 ->callMethod('set', ['value' => 'foo']);
        
        $resolver->on(HasUserTrait::class)
                 ->trait()
                 ->priority(1000)
                 ->callMethod('set', ['value' => 'bar']);        
        
        $this->assertSame(
            [
                0 => 'bar',
                1 => 'foo',
            ],
            $resolver->get(AdminUser::class)->getCalled()
        );
    }
    
    public function testWithInstanceof()
    {
        $resolver = $this->createResolver();

        $resolver->on(User::class)
                 ->instanceof()
                 ->callMethod('set', ['value' => 'foo']);      
        
        $this->assertSame(
            [
                0 => 'foo',
            ],
            $resolver->get(AdminUser::class)->getCalled()
        );
    }    
}