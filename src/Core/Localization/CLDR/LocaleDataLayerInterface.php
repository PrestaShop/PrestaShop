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

/**
 * CLDR Locale data layer classes interface.
 *
 * Describes the behavior of CldrLocaleDataLayer classes
 */
interface LocaleDataLayerInterface
{
    /**
     * Read CLDR locale data by locale code.
     *
     * @param string $localeCode The locale code (simplified IETF tag syntax)
     *                           Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *                           eg: fr-FR, en-US
     *
     * @return LocaleData|null The searched locale's CLDR data
     */
    public function read($localeCode);

    /**
     * Write a locale's CLDR data object into the data source.
     *
     * @param string $localeCode The locale code (simplified IETF tag syntax)
     *                           Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *                           eg: fr-FR, en-US
     * @param LocaleData $localeData The locale's CLDR data to write
     *
     * @return LocaleData
     *                    The locale's CLDR data to be written by the upper data layer
     */
    public function write($localeCode, $localeData);

    /**
     * Set the lower layer.
     * When reading data, if nothing is found then it will try to read in the lower data layer
     * When writing data, the data will also be written in the lower data layer.
     *
     * @param LocaleDataLayerInterface $lowerLayer The lower data layer
     *
     * @return self
     */
    public function setLowerLayer(LocaleDataLayerInterface $lowerLayer);
}
