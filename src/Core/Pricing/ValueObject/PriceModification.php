<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\ValueObject;

/**
 * Debug record of a single price property change, capturing which calculator made the modification.
 */
class PriceModification
{
    public function __construct(
        protected readonly string $callerClass,
        protected readonly int $callerLine,
        protected readonly string $property,
        protected readonly string $previousValue,
        protected readonly string $newValue,
    ) {
    }

    public function getCallerClass(): string
    {
        return $this->callerClass;
    }

    public function getCallerLine(): int
    {
        return $this->callerLine;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getPreviousValue(): string
    {
        return $this->previousValue;
    }

    public function getNewValue(): string
    {
        return $this->newValue;
    }
}
