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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;

/**
 * Toggles manufacturer status in bulk action
 */
class BulkToggleManufacturerStatusCommand
{
    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @var ManufacturerId[]
     */
    private $manufacturerIds;

    /**
     * @param int[] $manufacturerIds
     * @param bool $expectedStatus
     *
     * @throws ManufacturerConstraintException
     * @throws ManufacturerConstraintException
     */
    public function __construct(array $manufacturerIds, $expectedStatus)
    {
        $this->assertIsBool($expectedStatus);
        $this->expectedStatus = $expectedStatus;
        $this->setManufacturerIds($manufacturerIds);
    }

    /**
     * @return bool
     */
    public function getExpectedStatus()
    {
        return $this->expectedStatus;
    }

    /**
     * @return ManufacturerId[]
     */
    public function getManufacturerIds()
    {
        return $this->manufacturerIds;
    }

    /**
     * @param int[] $manufacturerIds
     *
     * @throws ManufacturerConstraintException
     */
    private function setManufacturerIds(array $manufacturerIds)
    {
        foreach ($manufacturerIds as $manufacturerId) {
            $this->manufacturerIds[] = new ManufacturerId($manufacturerId);
        }
    }

    /**
     * Validates that value is of type boolean
     *
     * @param $value
     *
     * @throws ManufacturerConstraintException
     */
    private function assertIsBool($value)
    {
        if (!is_bool($value)) {
            throw new ManufacturerConstraintException(sprintf('Status must be of type bool, but given %s', var_export($value, true)), ManufacturerConstraintException::INVALID_STATUS);
        }
    }
}
