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

/**
 * ResolverRuleTest
 */
abstract class ResolverRuleTest extends TestCase
{   
    abstract protected function createResolver(): ResolverInterface;

    public function testReturnsRuleInterface()
    {
        $rule = new OnRule('name');
        
        $ruled = $this->createResolver()->rule($rule);

        $this->assertSame(
            $rule,
            $ruled
        );
        
        $this->assertInstanceof(
            RuleInterface::class,
            $ruled
        );
    }
}