<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

abstract class AbstractCollection implements IteratorAggregate
{
    protected $values;

    public function toArray(): array
    {
        return $this->values;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->values);
    }
}
