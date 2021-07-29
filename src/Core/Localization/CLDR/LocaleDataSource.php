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

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleDataLayerInterface as CldrLocaleDataLayerInterface;

/**
 * LocaleDataSource provides CLDR LocaleData objects.
 *
 * This class uses Locale data layers as middlewares stack to read CLDR data.
 */
class LocaleDataSource
{
    /**
     * @var CldrLocaleDataLayerInterface
     */
    private $topLayer;

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
     * @return LocaleData|null
     */
    public function getLocaleData($localeCode)
    {
        return $this->topLayer->read($localeCode);
    }
}
