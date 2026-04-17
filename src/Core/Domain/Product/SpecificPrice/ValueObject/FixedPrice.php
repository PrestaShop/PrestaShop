<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;

class FixedPrice implements FixedPriceInterface
{
    /**
     * @var DecimalNumber
     */
    private $value;

    public function __construct(
        string $value
    ) {
        $this->setValue($value);
    }

    public function getValue(): DecimalNumber
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @throws SpecificPriceConstraintException
     */
    private function setValue(string $value): void
    {
        $decimalValue = new DecimalNumber($value);

        if (!$decimalValue->isNegative()) {
            $this->value = $decimalValue;

            return;
        }

        throw new SpecificPriceConstraintException(
            sprintf('Invalid fixed price "%s". It cannot be negative', $value),
            SpecificPriceConstraintException::INVALID_FIXED_PRICE
        );
    }
}
