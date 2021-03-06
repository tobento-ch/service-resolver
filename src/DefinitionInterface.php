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
 * DefinitionInterface
 */
interface DefinitionInterface
{
    /**
     * Get the id.
     *
     * @return string
     */
    public function getId(): string;
    
    /**
     * Get the value.
     *
     * @return mixed
     */
    public function getValue(): mixed;
    
    /**
     * Get the parameters.
     *
     * @return array<int|string, mixed>
     */
    public function getParameters(): array;  

    /**
     * Get the methods.
     *
     * @return array<int, array<int|string, mixed>>
     */
    public function getMethods(): array;
    
    /**
     * Returns true if to create new instance, otherwise false.
     *
     * @return bool
     */
    public function isPrototype(): bool;    
    
    /**
     * Set the parameters.
     *
     * @param array<int|string, mixed> $parameters The parameters.
     * @return static $this
     */
    public function with(array $parameters = []): static;
    
    /**
     * Set the parameters.
     *
     * @param mixed ...$parameters The parameters.
     * @return static $this
     */
    public function construct(...$parameters): static;
    
    /**
     * Set a method to call with parameters.
     *
     * @param string $method The name of the method
     * @param array<int|string, mixed> $parameters The parameters.
     * @return static $this
     */
    public function callMethod(string $method, array $parameters = []): static;
    
    /**
     * Set if it a prototype, meaning returning always new instance.
     *
     * @param bool $prototype
     * @return static $this
     */
    public function prototype(bool $prototype = true): static;
}