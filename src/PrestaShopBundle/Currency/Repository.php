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

namespace PrestaShopBundle\Currency;

use Exception;
use InvalidArgumentException;
use PrestaShopBundle\Currency\DataSource\DataSourceInterface;

class Repository
{
    protected $dataSources = array();
    protected $currencies  = array();

    public function __construct(array $dataSources)
    {
        foreach ($dataSources as $dataSource) {
            if (!$dataSource instanceof DataSourceInterface) {
                throw new Exception('Passed data sources must implement DataSourceInterface');
            }
        }

        $this->setDataSources($dataSources);
    }

    /**
     * @return array
     */
    public function getDataSources()
    {
        return $this->dataSources;
    }

    /**
     * @param array|DataSourceInterface $dataSources Either a data source or an array of data sources (implementing
     *                                               DataSourceInterface)
     *
     * @return Repository
     */
    public function setDataSources($dataSources)
    {
        $this->dataSources = $dataSources;

        return $this;
    }

    public function addCurrency(Currency $currency)
    {
        $isoCode = $currency->getIsoCode();
        if ($isoCode) {
            $this->currencies[$isoCode] = $currency;
        }

        $id = $currency->getId();
        if ($id) {
            $this->currencies[$id] = $currency;
        }

        return $this;
    }

    /**
     * @param $id
     *
     * @return Currency|null
     */
    public function getCurrency($id)
    {
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
                $builder  = new Builder();
                $currency = $builder->setIsoCode($currencyData['isoCode'])
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
            throw new \InvalidArgumentException("Unknown currency id : $id");
        }

        return $currency;
    }

    /**
     * @param $currencyCode
     *
     * @return Currency|null
     */
    public function getCurrencyByIsoCode($currencyCode)
    {
        if (!empty($this->currencies[$currencyCode])) {
            return $this->currencies[$currencyCode];
        }

        foreach ($this->getDataSources() as $index => $dataSource) {
            /** @var DataSourceInterface $dataSource */
            $currencyData = $dataSource->getCurrencyByIsoCode($currencyCode);

            if (!empty($currencyData)) {
                $builder  = new Builder();
                $currency = $builder->setIsoCode($currencyData['isoCode'])
                    ->setNumericIsoCode($currencyData['numericIsoCode'])
                    ->setDecimalDigits($currencyData['decimalDigits'])
                    ->setDisplayName($currencyData['displayName'])
                    ->setSymbols($currencyData['symbol'])
                    ->build();
                $this->addCurrency($currency);

//                $this->refreshDataSources($index - 1, $currencyData);
                break;
            }
        }

        if (!isset($currency)) {
            throw new InvalidArgumentException("Unknown currency code : $currencyCode");
        }

        return $currency;
    }
}
