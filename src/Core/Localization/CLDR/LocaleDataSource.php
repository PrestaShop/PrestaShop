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

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleDataLayerInterface as CldrLocaleDataLayerInterface;

/**
 * LocaleDataSource provides CLDR LocaleData objects
 *
 * This class uses Locale data layers as middlewares stack to read CLDR data.
 */
class LocaleDataSource
{
    /**
     * @var CldrLocaleDataLayerInterface
     */
    protected $topLayer;

    /**
     * LocaleDataSource constructor needs a CldrLocaleDataLayerInterface layer object.
     * This top layer might be chained with lower layers and will be the entry point of this middleware stack.
     *
     * @param CldrLocaleDataLayerInterface $topLayer
     */
    public function __construct(CldrLocaleDataLayerInterface $topLayer)
    {
        $this->topLayer = $topLayer;
    }

    /**
     * @param string $localeCode
     *
     * @return LocaleData
     */
    public function getLocaleData($localeCode)
    {
        return $this->topLayer->read($localeCode);
    }
}
