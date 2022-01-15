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
use Tobento\Service\Autowire\Autowire;
use Tobento\Service\Autowire\AutowireException;

/**
 * OnRule
 */
class OnRule implements RuleInterface
{
    /**
     * @var bool If to look for object wich are an instanceof
     */
    protected bool $isInstanceof = false;
    
    /**
     * @var bool If the name is a trait name.
     */
    protected bool $isTrait = false;

    /**
     * @var bool If to create new instances.
     */
    protected bool $prototype = false;    
    
    /**
     * @var array Array of methods to call.
     */
    protected array $methods = [];
    
    /**
     * @var array
     */
    protected array $calledTrait = [];    

    /**
     * @var bool If to call once.
     */
    protected bool $once = true;

    /**
     * @var bool If the rule is done.
     */
    protected bool $isDone = false;    
    
    /**
     * @var int The priority of handling the rule. Highest first
     */
    protected int $priority = 1000;    
    
    /**
     * Create a new OnRule.
     *
     * @param string $id Identifier of the entry.
     * @param mixed Any value.
     */        
    public function __construct(
        protected string $id,
        protected mixed $value = null,
    ) {}

    /**
     * If to look for object wich are an instanceof
     *
     * @return static $this
     */
    public function instanceof(): static
    {
        $this->isInstanceof = true;
        return $this;
    }
    
    /**
     * Set if it is a trait.
     *    
     * @param string The class name.
     * @return static $this
     */
    public function trait(): static
    {
        $this->isTrait = true;
        return $this;
    }

    /**
     * If to create new instances.
     *    
     * @param bool $prototype True create a new instances, otherwise false
     * @return static $this
     */
    public function prototype(bool $prototype = true): static
    {
        $this->prototype = $prototype;
        return $this;
    }    
    
    /**
     * Calls a method.
     *    
     * @param string $method The method name.
     * @param array<int|string, mixed> $parameters
     * @return static $this
     */
    public function callMethod(string $method, array $parameters = []): static
    {
        $this->methods[] = [$method, $parameters];
        return $this;
    }

    /**
     * The priority of handling the rule. Highest first
     *    
     * @param int $priority
     * @return static $this
     */
    public function priority(int $priority): static
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * If to call once.
     *    
     * @param bool $once True if to call once, otherwise false
     * @return static $this
     */
    public function once(bool $once = true): static
    {
        $this->once = $once;
        return $this;
    }

    /**
     * Get the priority of handling the rule. Highest first
     *    
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
    
    /**
     * If the rule is done.
     *    
     * @return bool
     */
    public function isDone(): bool
    {
        return $this->isDone;
    }

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
    ): object {
        
        if (is_null($container)) {
            return $object;
        }
        
        if ($this->isTrait) {
            return $this->handleTrait($object, $container);
        }
        
        // only on objects from the instanceof
        if ($this->isInstanceof && !$object instanceof $this->id) {
            return $object;
        }

        // only if matching the entry id
        if (! $this->isInstanceof && $this->id !== $entryId) {
            return $object;
        }
        
        if (!is_null($this->value))    
        {
            if (is_callable($this->value)) {
                $value = (new Autowire($container))->call($this->value, [$object]);
                
                if (is_array($value)) {
                    $value = (new Autowire($container))->resolve($object::class, $value);
                }  
            } elseif (is_object($this->value)) {
                $value = $this->value;
            } elseif (is_string($this->value)) {  
                if ($this->prototype) {
                    $value = (new Autowire($container))->resolve($this->value);
                } else {                    
                    $value = $container->get($this->value);
                }
            } elseif (is_array($this->value)) {
                foreach($this->value as $key => $value) {
                    if (is_string($value)) {
                        try {
                            $this->value[$key] = (new Autowire($container))->resolve($value);
                        } catch (AutowireException $e) {
                            // ignore
                        }
                    }
                }
                
                $value = (new Autowire($container))->resolve($object::class, $this->value);
            }
            
            if (isset($value) && is_object($value)) {
                $object = $value;
            }
        }    

        $this->handleMethods($object, $container);
        
        if ($this->once) {
            $this->isDone = true;
        }
        
        return $object;        
    }

    /**
     * Handle trait name.
     *
     * @param object $object
     * @param ContainerInterface $container
     * @return object
     */
    protected function handleTrait(object $object, ContainerInterface $container): object
    {
        $traits = class_uses($object);

        if (!is_array($traits))
        {
            return $object;
        }
        
        if (isset($this->calledTrait[$this->id]) && $this->calledTrait[$this->id] === $object)
        {
            return $object;
        }

        if (in_array($this->id, $traits))
        {
            $this->handleMethods($object, $container);
        }
        
        if ($this->once)
        {
            $this->isDone = true;
        }
        
        $this->calledTrait[$this->id] = $object;
            
        return $object;
    }
    
    /**
     * Handle the methods.
     *
     * @param object $object
     * @param ContainerInterface $container
     * @return void
     */
    protected function handleMethods(object $object, ContainerInterface $container): void
    {
        $autowire = new Autowire($container);
        
        foreach($this->methods as $method)
        {
            [$method, $parameters] = $method;
            
            // skip if method does not exist.
            if (!method_exists($object, $method)) {
                continue;
            }
            
            $autowire->call([$object, $method], $parameters);
        }    
    }   
}