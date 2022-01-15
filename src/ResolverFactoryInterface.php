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

namespace Tobento\Service\Resolver;

/**
 * ResolverFactoryInterface
 */
interface ResolverFactoryInterface
{
    /**
     * Create a new Resolver.
     *
     * @return ResolverInterface
     */
    public function createResolver(): ResolverInterface;
}