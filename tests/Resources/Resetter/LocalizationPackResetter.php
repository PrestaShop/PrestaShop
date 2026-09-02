<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\Resetter;

use Tests\Resources\DatabaseDump;

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
        LanguageResetter::resetLanguages();
        ConfigurationResetter::resetConfiguration();
    }
}
