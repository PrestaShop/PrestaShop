<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;

class Dimension
{
    /**
     * @var DecimalNumber
     */
    private $decimalValue;

    /**
     * @param string $value
     *
     * @throws DomainConstraintException
     */
    public function __construct(
        string $value
    ) {
        $this->setDecimalValue($value);
    }

    /**
     * @return DecimalNumber
     */
    public function getDecimalValue(): DecimalNumber
    {
        return $this->decimalValue;
    }

    /**
     * @param string $rawValue
     *
     * @throws DomainConstraintException
     */
    private function setDecimalValue(string $rawValue): void
    {
        $decimalValue = new DecimalNumber($rawValue);

        if ($decimalValue->isLowerThanZero()) {
            throw new DomainConstraintException('Invalid dimension, it must be positive number or zero');
        }

        $this->decimalValue = $decimalValue;
    }
}
