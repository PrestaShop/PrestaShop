<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
     * @param $value
     *
     * @throws TaxConstraintException
     */
    private function assertIsBool($value)
    {
        if (!is_bool($value)) {
            throw new TaxConstraintException(
                sprintf('Status must be of type bool, but given %s', var_export($value, true)),
                TaxConstraintException::INVALID_STATUS
            );
        }
    }
}
