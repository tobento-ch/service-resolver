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

use Psr\Container\ContainerInterface;

/**
 * RuleInterface
 */
interface RuleInterface
{
    /**
     * Get the priority of handling the rule. Highest first
     *    
     * @return int
     */
    public function getPriority(): int;
    
    /**
     * If the rule is done.
     *    
     * @return bool
     */
    public function isDone(): bool;
    
    /**
     * Handle the rule.
     *
     * @param object $object
     * @param null|string $entryId
     * @param null|ContainerInterface $container
     * @return object
     */
    public function handle(
        object $object,
        null|string $entryId = null,
        null|ContainerInterface $container = null
    ): object;    
}