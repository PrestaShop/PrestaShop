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

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShopBundle\Currency\Manager as CurrencyManager;
use PrestaShopBundle\Localization\DataSource\DataSourceInterface;
use PrestaShopBundle\Localization\Exception\InvalidArgumentException;
use PrestaShopBundle\Localization\Formatter\NumberFactory as NumberFormatterFactory;

/**
 * Class Repository
 *
 * Combines data sources to read/save Locales
 *
 * @package PrestaShopBundle\Localization
 */
class Repository
{
    protected $dataSources = array();
    protected $locales     = array();

    /**
     * @var NumberFormatterFactory
     */
    protected $numberFormatterFactory;

    /**
     * @var CurrencyManager
     */
    protected $currencyManager;

    /**
     * @var Configuration
     */
    protected $configuration;

    public function __construct(
        array $dataSources,
        NumberFormatterFactory $numberFormatterFactory,
        CurrencyManager $currencyManager,
        Configuration $config
    ) {
        $this->setDataSources($dataSources);
        $this->numberFormatterFactory = $numberFormatterFactory;
        $this->currencyManager        = $currencyManager;
        $this->configuration          = $config;
    }

    /**
     * @return array
     */
    public function getDataSources()
    {
        return $this->dataSources;
    }

    /**
     * @param array $dataSources Array of data sources (implementing DataSourceInterface)
     *
     * @return Repository
     * @throws InvalidArgumentException
     */
    public function setDataSources($dataSources)
    {
        foreach ($dataSources as $dataSource) {
            if (!$dataSource instanceof DataSourceInterface) {
                throw new InvalidArgumentException('Passed data sources must implement DataSourceInterface');
            }
        }

        $this->dataSources = $dataSources;

        return $this;
    }

    protected function addLocale(Locale $locale)
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

        foreach ($this->getDataSources() as $dataSource) {
            /** @var DataSourceInterface $dataSource */
            $localeData = $dataSource->getLocaleById((int)$id);

            if (!empty($localeData)) {
                $locale = new Locale(
                    $localeData->localeCode,
                    $this->numberFormatterFactory,
                    $localeData,
                    $this->currencyManager,
                    $this->getConfiguration()
                );
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
                $locale = new Locale(
                    $localeData->localeCode,
                    $this->numberFormatterFactory,
                    $localeData,
                    $this->currencyManager,
                    $this->getConfiguration()
                );
                $this->addLocale($locale);

//                $this->refreshDataSources($index - 1, $currencyData);
                break;
            }
        }

        return isset($locale) ? $locale : null;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function saveLocale($locale)
    {
        /** @var DataSourceInterface $dataSource */
        foreach ($this->getDataSources() as $dataSource) {
            if ($locale->id) {
                $dataSource->updateLocale($locale);
                continue;
            }

            $dataSource->createLocale($locale);
        }
    }
}
