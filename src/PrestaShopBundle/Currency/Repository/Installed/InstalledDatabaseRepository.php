<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Currency\Repository\Installed;

use PrestaShopBundle\Currency\Currency;

class InstalledRepository implements InstalledRepositoryInterface
{
    /**
     * Get currency data by internal database identifier
     *
     * @param int $id
     *
     * @return array The currency data
     */
    public function getCurrencyById($id){
        if ((int)$id != $id) {
            throw new InvalidArgumentException('$id must be an integer');
        }

        if (!empty($this->currencies[$id])) {
            return $this->currencies[$id];
        }

        foreach ($this->getDataSources() as $index => $dataSource) {
            /** @var DataSourceInterface $dataSource */
            $currencyData = $dataSource->getCurrencyById((int)$id);

            if (!empty($currencyData)) {
                $factory  = new CurrencyFactory();
                $currency = $factory->setIsoCode($currencyData['isoCode'])
                                    ->setNumericIsoCode($currencyData['numericIsoCode'])
                                    ->setDecimalDigits($currencyData['decimalDigits'])
                                    ->setDisplayName($currencyData['localizedNames'])
                                    ->setSymbols($currencyData['localizedSymbols'])
                                    ->build();
                $this->addCurrency($currency);

//                $this->refreshDataSources($index - 1, $currencyData);
                break;
            }
        }

        if (!isset($currency)) {
            throw new InvalidArgumentException("Unknown currency id : $id");
        }

        return $currency;

    }

    public function addInstalledCurrency(Currency $currency);

    public function updateInstalledCurrency(Currency $currency);

    public function deleteInstalledCurrency(Currency $currency);
}
