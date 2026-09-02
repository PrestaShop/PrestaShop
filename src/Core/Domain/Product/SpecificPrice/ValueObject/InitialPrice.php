<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Represents "leave initial product price" option
 */
class InitialPrice implements FixedPriceInterface
{
    /**
     * Inherited from legacy.
     * When SpecificPrice->price has this value, it means that product initial price is used.
     */
    public const INITIAL_PRICE_VALUE = '-1';

    /**
     * @var DecimalNumber
     */
    private $value;

    public function __construct()
    {
        $this->value = new DecimalNumber(self::INITIAL_PRICE_VALUE);
    }

    /**
     * @return DecimalNumber
     */
    public function getValue(): DecimalNumber
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function isInitialPriceValue(string $value): bool
    {
        $initialPrice = new DecimalNumber(self::INITIAL_PRICE_VALUE);

        return $initialPrice->equals(new DecimalNumber($value));
    }
}
