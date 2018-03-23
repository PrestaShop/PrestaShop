<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency;

use PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer\CurrencyInstalled as CurrencyInstalledDataLayer;

/**
 * Localization CurrencyData source
 * Uses a stack of middleware data layers to read / write CurrencyData objects
 */
class CurrencyDataSource implements DataSourceInterface
{
    /**
     * The top layer of the middleware stack
     *
     * @var CurrencyDataLayerInterface
     */
    protected $topLayer;

    /**
     * @var CurrencyInstalledDataLayer
     */
    protected $installedDataLayer;

    /**
     * CurrencyDataSource constructor needs CurrencyDataLayer objects.
     * This top layer might be chained with lower layers and will be the entry point of this middleware stack.
     *
     * @param CurrencyDataLayerInterface $topLayer
     */
    public function __construct(CurrencyDataLayerInterface $topLayer)
    {
        $this->topLayer = $topLayer;
    }

    /**
     * Get complete currency data by currency code
     *
     * @param string $currencyCode
     *
     * @return CurrencyData
     *  The currency data
     */
    public function getDataByCurrencyCode($currencyCode)
    {
        return $this->topLayer->read($currencyCode);
    }

    public function isCurrencyInstalled($currencyCode)
    {
        return $this->installedDataLayer->isInstalled($currencyCode);
    }

    public function getInstalledCurrencies()
    {
        // TODO
    }
}
