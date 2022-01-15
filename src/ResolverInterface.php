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
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * ResolverInterface
 */
interface ResolverInterface extends ContainerInterface
{    
    /**
     * Sets an entry by its given identifier.
     *
     * @param string $id Identifier of the entry.
     * @param mixed Any value.
     * @return DefinitionInterface
     */
    public function set(string $id, mixed $value = null): DefinitionInterface;

    /**
     * Makes an entry by its identifier.
     *
     * @param string $id Identifier of the entry.
     * @param array<int|string, mixed> $parameters The parameters.
     *
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     *
     * @return mixed The value obtained from the identifier.
     */
    public function make(string $id, array $parameters = []): mixed;

    /**
     * Call the given callable.
     *
     * @param mixed $callable A callable.
     * @param array<int|string, mixed> $parameters The parameters.
     *
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     *     
     * @return mixed The called function result.
     */
    public function call(mixed $callable, array $parameters = []): mixed;
    
    /**
     * Add a rule.
     *
     * @param RuleInterface $rule
     * @return RuleInterface
     */
    public function rule(RuleInterface $rule): RuleInterface;
    
    /**
     * Resolve on.
     *
     * @param string $id Identifier of the entry.
     * @param mixed Any value.
     * @return OnRule
     */
    public function on(string $id, mixed $value = null): OnRule;

    /**
     * Get the container.
     * 
     * @return ContainerInterface
     */
    public function container(): ContainerInterface;
}