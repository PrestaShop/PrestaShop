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

namespace PrestaShop\PrestaShop\Adapter\Manufacturer;

use Manufacturer;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShopException;

/**
 * Provides reusable methods for manufacturer command/query handlers
 */
abstract class AbstractManufacturerHandler extends AbstractObjectModelHandler
{
    /**
     * Gets legacy Manufacturer
     *
     * @param ManufacturerId $manufacturerId
     *
     * @return Manufacturer
     *
     * @throws ManufacturerException
     */
    protected function getManufacturer(ManufacturerId $manufacturerId)
    {
        try {
            $manufacturer = new Manufacturer($manufacturerId->getValue());
        } catch (PrestaShopException $e) {
            throw new ManufacturerException('Failed to create new manufacturer', 0, $e);
        }

        if ($manufacturer->id !== $manufacturerId->getValue()) {
            throw new ManufacturerNotFoundException(sprintf('Manufacturer with id "%s" was not found.', $manufacturerId->getValue()));
        }

        return $manufacturer;
    }
}
