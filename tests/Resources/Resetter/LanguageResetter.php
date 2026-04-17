<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\Resetter;

use Configuration;
use Db;
use Language;
use Tests\Resources\DatabaseDump;
use Tests\Resources\ResourceResetter;

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

        // Reset modules folder that are removed with the deleted languages
        (new ResourceResetter())->resetTestModules();
    }
}
