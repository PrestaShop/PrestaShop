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

namespace PrestaShop\PrestaShop\Adapter\Country\QueryHandler;

use AddressFormat;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetAddressFormatData;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryHandler\GetAddressFormatDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\AddressFormatData;

/**
 * Provides legacy address layout modification data
 */
final class GetAddressFormatDataHandler implements GetAddressFormatDataHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetAddressFormatData $command): AddressFormatData
    {
        return new AddressFormatData(
            $this->getAvailableFields(),
            $this->getAddressFormat($command->getCountryId() ? $command->getCountryId()->getValue() : 0),
            $this->getDefaultFormat()
        );
    }

    /**
     * Gets legacy valid fields in array
     *
     * @return string[]
     */
    private function getAvailableFields(): array
    {
        $objectList = AddressFormat::getLiableClass('Address');
        $objectList['Address'] = null;

        $layoutData = [];

        foreach ($objectList as $className => $object) {
            $layoutTab = [];
            foreach (AddressFormat::getValidateFields($className) as $name) {
                $layoutValue = $className == 'Address' ? $name : $className . ':' . $name;
                $layoutTab[] = $layoutValue;
            }

            $layoutData[$className] = $layoutTab;
        }

        return $layoutData;
    }

    /**
     * Gets current country legacy address format layout
     *
     * @param int $countryId
     *
     * @return string
     */
    private function getAddressFormat(int $countryId): string
    {
        $addressLayout = AddressFormat::getAddressCountryFormat($countryId);

        return $addressLayout;
    }

    /**
     * Gets default legacy country address format
     *
     * @return string
     */
    private function getDefaultFormat(): string
    {
        $defaultLayout = '';

        $defaultLayoutTab = [
            ['firstname', 'lastname'],
            ['company'],
            ['vat_number'],
            ['address1'],
            ['address2'],
            ['postcode', 'city'],
            ['Country:name'],
            ['phone'],
        ];

        foreach ($defaultLayoutTab as $line) {
            $defaultLayout .= implode(' ', $line) . PHP_EOL;
        }

        return $defaultLayout;
    }
}
