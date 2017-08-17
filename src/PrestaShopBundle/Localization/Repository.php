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

namespace PrestaShopBundle\Localization;

use Exception;
use InvalidArgumentException;
use PrestaShopBundle\Currency\CurrencyCollection;
use PrestaShopBundle\Localization\DataSource\DataSourceInterface;
use PrestaShopBundle\Localization\Formatter\NumberFactory as NumberFormatterFactory;

class Repository
{
    protected $currencyCollection;
    protected $dataSources = array();
    protected $locales     = array();
    protected $numberFormatterFactory;

    public function __construct(
        array $dataSources,
        NumberFormatterFactory $numberFormatterFactory,
        CurrencyCollection $currencyCollection
    ) {
        foreach ($dataSources as $dataSource) {
            if (!$dataSource instanceof DataSourceInterface) {
                throw new Exception('Passed data sources must implement DataSourceInterface');
            }
        }

        $this->setDataSources($dataSources);
        $this->numberFormatterFactory = $numberFormatterFactory;
        $this->currencyCollection     = $currencyCollection;
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

    public function addLocale(Locale $locale)
    {
        $localeCode = $locale->getLocaleCode();
        if ($localeCode) {
            $this->locales[$localeCode] = $locale;
        }

        $id = $locale->getId();
        if ($id) {
            $this->locales[$id] = $locale;
        }

        return $this;
    }

    public function getLocale($id)
    {
        if ((int)$id != $id) {
            throw new InvalidArgumentException('$id must be an integer');
        }

        if (!empty($this->locales[$id])) {
            return $this->locales[$id];
        }

        foreach ($this->getDataSources() as $index => $dataSource) {
            /** @var DataSourceInterface $dataSource */
            $localeData = $dataSource->getLocaleById((int)$id);

            if (!empty($localeData)) {
                $locale = new Locale($localeData->localeCode, $this->numberFormatterFactory, $localeData, $this);
                $this->addLocale($locale);

//                $this->refreshDataSources($index - 1, $currencyData);
                break;
            }
        }

        return isset($locale) ? $locale : null;
    }

    public function getLocaleByCode($localeCode)
    {
        if (!empty($this->locales[$localeCode])) {
            return $this->locales[$localeCode];
        }

        foreach ($this->getDataSources() as $index => $dataSource) {
            /** @var DataSourceInterface $dataSource */
            $localeData = $dataSource->getLocaleByCode($localeCode);

            if (!empty($localeData)) {
                $locale = new Locale($localeData->localeCode, $this->numberFormatterFactory, $localeData, $this);
                $this->addLocale($locale);

//                $this->refreshDataSources($index - 1, $currencyData);
                break;
            }
        }

        return isset($locale) ? $locale : null;
    }
}
