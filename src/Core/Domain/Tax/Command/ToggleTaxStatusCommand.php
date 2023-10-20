<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Command;

use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;

/**
 * Toggles tax status
 */
class ToggleTaxStatusCommand
{
    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @var TaxId
     */
    private $taxId;

    /**
     * @param int $taxId
     * @param bool $expectedStatus
     *
     * @throws TaxException
     */
    public function __construct($taxId, $expectedStatus)
    {
        $this->assertIsBool($expectedStatus);
        $this->taxId = new TaxId($taxId);
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
     * @return TaxId
     */
    public function getTaxId()
    {
        return $this->taxId;
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
