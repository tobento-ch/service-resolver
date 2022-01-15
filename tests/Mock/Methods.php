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

namespace Tobento\Service\Resolver\Test\Mock;

class Methods
{
    protected array $called = [];
    
    public function __construct(
        protected Baz $baz
    ) {}

    public function getCalled(): array
    {
        return $this->called;
    }
    
    public function withoutParameters(): string
    {
        $this->called[] = __FUNCTION__;
        return 'withoutParameters';
    }
    
    public function withBuildInParameter(string $name): string
    {
        $this->called[] = __FUNCTION__;
        return $name;
    }
    
    public function withBuildInParameterAllowsNull(null|string $name): null|string
    {
        $this->called[] = __FUNCTION__;
        return $name;
    }
    
    public function withBuildInParameterOptional(null|string $name = null): null|string
    {
        $this->called[] = __FUNCTION__;
        return $name;
    }
    
    public function withParameter(Foo $foo): Foo
    {
        $this->called[] = __FUNCTION__;
        return $foo;
    }
    
    public function withParameters(Foo $foo, Bar $bar): string
    {
        $this->called[] = __FUNCTION__;
        return 'called';
    }
    
    public function withUnionParameter(Inexistence|Foo|Bar $name): mixed
    {
        $this->called[] = __FUNCTION__;
        return $name;
    }
    
    public function withUnionParameterAllowsNull(null|Inexistence|Foo|Bar $name): mixed
    {
        $this->called[] = __FUNCTION__;
        return $name;
    }
    
    public function withUnionParameterAllowsNullNotFound(null|Inexistence|NotFound $name): mixed
    {
        $this->called[] = __FUNCTION__;
        return $name;
    }     
    
    public function withBuildInParameterAndClasses(
        Foo $foo,
        string $name,
        Bar $bar
    ): string {
        $this->called[] = __FUNCTION__;
        return $name;
    }    
    
    private function withPrivateMethod(string $name = 'index')
    {
        $this->called[] = __FUNCTION__;
        return $name;
    }
}