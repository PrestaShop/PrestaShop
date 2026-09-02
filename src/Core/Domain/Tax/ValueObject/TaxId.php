<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxConstraintException;

/**
 * Provides tax id data
 */
class TaxId
{
    /**
     * @var int
     */
    private $taxId;

    /**
     * @param int $taxId
     *
     * @throws TaxConstraintException
     */
    public function __construct($taxId)
    {
        if (!is_int($taxId) || $taxId <= 0) {
            throw new TaxConstraintException(sprintf('Invalid Tax id: %s', var_export($taxId, true)), TaxConstraintException::INVALID_ID);
        }

        $this->taxId = $taxId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->taxId;
    }
}
