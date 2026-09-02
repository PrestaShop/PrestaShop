<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Command;

use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;

/**
 * Toggles taxes status on bulk action
 */
class BulkToggleTaxStatusCommand
{
    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @var TaxId[]
     */
    private $taxIds;

    /**
     * @param int[] $taxIds
     * @param bool $expectedStatus
     *
     * @throws TaxException
     */
    public function __construct(array $taxIds, $expectedStatus)
    {
        $this->assertIsBool($expectedStatus);
        $this->setTaxIds($taxIds);
        $this->expectedStatus = $expectedStatus;
    }

    /**
     * @return bool
     */
    public function getExpectedStatus()
    {
        return $this->expectedStatus;
    }

    /**
     * @return TaxId[]
     */
    public function getTaxIds()
    {
        return $this->taxIds;
    }

    /**
     * @param array $taxIds
     *
     * @throws TaxException
     */
    private function setTaxIds(array $taxIds)
    {
        foreach ($taxIds as $taxId) {
            $this->taxIds[] = new TaxId((int) $taxId);
        }
    }

    /**
     * Validates that value is of type boolean
     *
     * @param mixed $value
     *
     * @throws TaxConstraintException
     */
    private function assertIsBool($value)
    {
        if (!is_bool($value)) {
            throw new TaxConstraintException(sprintf('Status must be of type bool, but given %s', var_export($value, true)), TaxConstraintException::INVALID_STATUS);
        }
    }
}
