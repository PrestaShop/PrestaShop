<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject;

class LowStockThreshold
{
    /**
     * Represents a value when low stock alert is considered disabled, therefore the threshold is irrelevant
     */
    public const DISABLED_VALUE = 0;

    /**
     * @var int
     */
    private $value;

    public function __construct(
        int $thresholdValue
    ) {
        $this->value = $thresholdValue;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    public function isEnabled(): bool
    {
        return $this::DISABLED_VALUE !== $this->value;
    }
}
