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
use Tobento\Service\Resolver\ResolverFactoryInterface;
use Tobento\Service\Resolver\ResolverInterface;

/**
 * ResolverFactoryTest
 */
abstract class ResolverFactoryTest extends TestCase
{   
    abstract protected function createResolverFactory(): ResolverFactoryInterface;
    
    public function testCreateResolver()
    {
        $this->assertInstanceof(
            ResolverInterface::class,
            $this->createResolverFactory()->createResolver()
        );
    }
}