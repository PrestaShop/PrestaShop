<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cache;
use Db;
use Hook;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class HookNameLookupTest extends TestCase
{
    private const CACHE_IDS = ['hook_idsbyname', 'hook_idsbyname_withalias', 'active_hooks', 'hook_aliases', 'hook_canonical_names'];

    private int $disabledHookId = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clearLookups();
    }

    protected function tearDown(): void
    {
        if ($this->disabledHookId) {
            Db::getInstance()->update('hook', ['active' => 1], '`id_hook` = ' . $this->disabledHookId);
        }
        $this->clearLookups();
        parent::tearDown();
    }

    public function testLookupsMatchTheHookTables(): void
    {
        $db = Db::getInstance();
        $this->disabledHookId = (int) $db->getValue('SELECT MIN(`id_hook`) FROM `' . _DB_PREFIX_ . 'hook` WHERE `active` = 1');
        self::assertTrue($db->update('hook', ['active' => 0], '`id_hook` = ' . $this->disabledHookId));
        $expectedIds = [];
        foreach ($db->executeS('SELECT `id_hook`, `name` FROM `' . _DB_PREFIX_ . 'hook`') as $row) {
            $expectedIds[strtolower($row['name'])] = $row['id_hook'];
        }
        $expectedIdsWithAliases = [];
        foreach ($db->executeS(
            'SELECT `id_hook`, `name` FROM `' . _DB_PREFIX_ . 'hook`
            UNION
            SELECT `id_hook`, ha.`alias` as name FROM `' . _DB_PREFIX_ . 'hook_alias` ha
            INNER JOIN `' . _DB_PREFIX_ . 'hook` h ON ha.name = h.name'
        ) as $row) {
            $expectedIdsWithAliases[strtolower($row['name'])] = $row['id_hook'];
        }
        $expectedAliases = [];
        $expectedCanonicalNames = [];
        foreach ($db->executeS('SELECT `name`, `alias` FROM `' . _DB_PREFIX_ . 'hook_alias`') as $row) {
            $expectedAliases[strtolower($row['name'])][] = $row['alias'];
            $expectedCanonicalNames[strtolower($row['alias'])] = $row['name'];
        }
        $activeHooks = array_column(
            $db->executeS('SELECT lower(name) as name FROM `' . _DB_PREFIX_ . 'hook` h WHERE h.active = 1'),
            'name'
        );
        self::assertNotEmpty($expectedAliases);
        self::assertGreaterThan(count($expectedIds), count($expectedIdsWithAliases));
        self::assertCount(count($expectedIds) - 1, $activeHooks);

        // Lookups are read by key: the order of the keys does not matter.
        self::assertSame($this->sortByKey($expectedIds), $this->sortByKey($this->invokeHook('getAllHookIds', [false])));
        self::assertSame($this->sortByKey($expectedIdsWithAliases), $this->sortByKey($this->invokeHook('getAllHookIds', [true])));
        self::assertSame($this->sortByKey($expectedAliases), $this->sortByKey($this->invokeHook('getAllHookAliases')));
        self::assertSame($this->sortByKey($expectedCanonicalNames), $this->sortByKey($this->invokeHook('getCanonicalHookNames')));
        foreach (array_keys($expectedIds) as $hookName) {
            self::assertSame(in_array($hookName, $activeHooks), Hook::getHookStatusByName($hookName), $hookName);
        }
        self::assertFalse(Hook::getHookStatusByName('hookThatDoesNotExist'));
    }

    public function testLookupsAreLoadedWithTwoQueries(): void
    {
        $aliasRow = Db::getInstance()->getRow('SELECT `name`, `alias` FROM `' . _DB_PREFIX_ . 'hook_alias`');
        self::assertIsArray($aliasRow);

        $before = $this->countQuestions();
        Hook::getIdByName($aliasRow['name'], false);
        Hook::getIdByName($aliasRow['alias']);
        Hook::getHookStatusByName($aliasRow['name']);
        Hook::isAlias($aliasRow['alias']);
        Hook::getAllKnownNames($aliasRow['name']);
        $queries = $this->countQuestions() - $before - 1;

        self::assertSame(2, $queries);
    }

    /**
     * @return mixed
     */
    private function invokeHook(string $method, array $arguments = [])
    {
        $reflection = new ReflectionMethod(Hook::class, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs(null, $arguments);
    }

    private function sortByKey(array $lookup): array
    {
        ksort($lookup);

        return $lookup;
    }

    private function countQuestions(): int
    {
        return (int) Db::getInstance()->executeS('SHOW SESSION STATUS LIKE \'Questions\'', true, false)[0]['Value'];
    }

    private function clearLookups(): void
    {
        foreach (self::CACHE_IDS as $cacheId) {
            Cache::clean($cacheId);
        }
        Cache::clean('hook_aliases_*');
    }
}
