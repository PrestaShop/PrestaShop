<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\Resetter;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Tests\Resources\DatabaseDump;

/**
 * Puts the extra property feature back to the dump's state, whatever a test left behind: the
 * registry rows, the storage tables registrations created, and the definitions cached on disk.
 * Meant for tearDownAfterClass() / @AfterFeature hooks, so each test class does not have to know
 * the three moving parts.
 */
class ExtraPropertyResetter
{
    public static function resetExtraProperties(): void
    {
        // Registry: the definitions and their shop association (any future extra_property_definition_* table too)
        DatabaseDump::restoreMatchingTables('/^extra_property_definition/');

        // Storage: the {entity}_extra, {entity}_extra_lang and {entity}_extra_shop tables a registration
        // creates have no dump, so they are dropped rather than restored
        DatabaseDump::removeExtraTables();

        // The pool CachedExtraPropertyDefinitionRepository and ExtraPropertyDefinitionShopFilter share
        // (services/extra_property/common.yml); it survives the process, so a stale entry would
        // hand the next test definitions that no longer exist
        (new FilesystemAdapter('', 0, _PS_CACHE_DIR_ . 'extra_property_definition'))->clear();
    }
}
