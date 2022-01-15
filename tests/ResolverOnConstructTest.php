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
    UserInterface,
    UserServiceFoo,
    UserServiceBar,
    UserService
};

/**
 * ResolverOnConstructTest
 */
abstract class ResolverOnConstructTest extends TestCase
{
    abstract protected function createResolver(): ResolverInterface;
    
    public function testWithArray()
    {
        $resolver = $this->createResolver();
        
        $resolver->set(UserInterface::class, GuestUser::class);

        $resolver->on(UserServiceFoo::class, ['user' => AdminUser::class]);
        
        $this->assertInstanceof(
            GuestUser::class,
            $resolver->get(UserServiceBar::class)->getUser()
        );
        
        $this->assertInstanceof(
            AdminUser::class,
            $resolver->get(UserServiceFoo::class)->getUser()
        );        
    }
    
    public function testWithCallableReturningArray()
    {
        $resolver = $this->createResolver();
        
        $resolver->set(UserInterface::class, GuestUser::class);

        $resolver->on(UserServiceFoo::class, function($service, AdminUser $user) {
            return ['user' => $user];
        });
        
        $this->assertInstanceof(
            GuestUser::class,
            $resolver->get(UserServiceBar::class)->getUser()
        );
        
        $this->assertInstanceof(
            AdminUser::class,
            $resolver->get(UserServiceFoo::class)->getUser()
        );        
    }
    
    public function testWithInstanceof()
    {
        $resolver = $this->createResolver();
        
        $resolver->set(UserInterface::class, GuestUser::class);

        $resolver->on(UserService::class, function($service, AdminUser $user) {
            return ['user' => $user];
        })->instanceof();  
        
        $this->assertInstanceof(
            AdminUser::class,
            $resolver->get(UserServiceFoo::class)->getUser()
        );
    }    
    
    public function testWithPriority()
    {
        $resolver = $this->createResolver();
        
        $resolver->set(UserInterface::class, GuestUser::class);

        $resolver->on(UserServiceFoo::class, function($service, EditorUser $user) {
            return ['user' => $user];
        })->priority(500);
        
        $resolver->on(UserServiceFoo::class, function($service, AdminUser $user) {
            return ['user' => $user];
        })->priority(1000);
        
        $this->assertInstanceof(
            EditorUser::class,
            $resolver->get(UserServiceFoo::class)->getUser()
        );
    }    
}