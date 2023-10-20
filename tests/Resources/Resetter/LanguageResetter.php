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

namespace Tests\Resources\Resetter;

use Configuration;
use Db;
use Language;
use Tests\Resources\DatabaseDump;

class LanguageResetter
{
    public static function resetLanguages(): void
    {
        // Removing Language manually includes cleaning all related lang tables, this cleaning is handled in
        // Language::delete in a more efficient way than relying on table restoration
        $langIds = Db::getInstance()->executeS(sprintf('SELECT id_lang FROM %slang;', _DB_PREFIX_));
        unset($langIds[0]);
        foreach ($langIds as $langId) {
            $lang = new Language($langId['id_lang']);
            $lang->delete();
        }

        // We still restore lang table to reset increment ID
        DatabaseDump::restoreTables(['lang', 'lang_shop']);

        // Reset default language
        Configuration::updateValue('PS_LANG_DEFAULT', 1);
    }
}
