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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Resources;

use Currency;

class LocalizationPackResetter
{
    public static function resetLocalizationPacks(): void
    {
        DatabaseDump::restoreTables([
            'country',
            'country_lang',
            'country_shop',
            'state',
            'zone',
            'zone_shop',
            'tax',
            'tax_lang',
            'tax_rule',
            'tax_rules_group',
            'tax_rules_group_shop',
            'currency',
            'currency_lang',
            'currency_shop',
            'module_currency',
        ]);
        Currency::resetStaticCache();
        LanguageResetter::resetLanguages();
        ConfigurationResetter::resetConfiguration();
    }
}
