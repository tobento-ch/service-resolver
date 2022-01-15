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

trait HasUserTrait
{
    protected array $called = [];
    
    public function set(string $value)
    {
        $this->called[] = $value;
    }
    
    public function getCalled(): array
    {
        return $this->called;
    }
}