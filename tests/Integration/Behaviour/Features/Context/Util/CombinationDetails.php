<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Util;

/**
 * Transfers combination details data
 */
class CombinationDetails
{
    /**
     * @var string
     */
    private $reference;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var float|null
     */
    private $price;

    /**
     * @param string $reference
     * @param int $quantity
     * @param string[] $attributes
     * @param float|null $price
     */
    public function __construct(string $reference, int $quantity, array $attributes, ?float $price = null)
    {
        $this->reference = $reference;
        $this->quantity = $quantity;
        $this->attributes = $attributes;
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }
}
