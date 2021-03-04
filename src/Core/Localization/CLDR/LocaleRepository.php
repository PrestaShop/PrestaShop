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

use PrestaShop\PrestaShop\Core\Localization\CLDR\Locale as CldrLocale;

/**
 * CLDR Locale Repository.
 *
 * Provides CLDR Locale objects
 */
class LocaleRepository
{
    /**
     * @var LocaleDataSource
     */
    protected $dataSource;

    public function __construct(LocaleDataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * Get a CLDR Locale by simplified IETF tag.
     *
     * @param string $localeCode e.g.: fr-FR, en-US...
     *
     * @return CldrLocale|null A CldrLocale object. Null if not found
     */
    public function getLocale($localeCode)
    {
        $localeData = $this->dataSource->getLocaleData($localeCode);

        if (null === $localeData) {
            return null;
        }

        return new CldrLocale($localeData);
    }
}
