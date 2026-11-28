<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Module;

use Db;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * getInstalled() used to ask the database whether each module was active, one query per module, which
 * is the N+1 @kpodemski pointed at on #38950. The active flag comes from the join the query already had.
 */
class ModuleDataProviderTest extends KernelTestCase
{
    /**
     * @var ModuleDataProvider
     */
    private $moduleDataProvider;

    protected function setUp(): void
    {
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        $this->moduleDataProvider = self::$kernel->getContainer()->get(ModuleDataProvider::class);
    }

    public function testGetInstalledDoesNotQueryOncePerModule(): void
    {
        // warm anything the first call would lazily initialise, so only getInstalled is measured
        $this->moduleDataProvider->getInstalled();

        $before = $this->queriesRun();
        $modules = $this->moduleDataProvider->getInstalled();
        $after = $this->queriesRun();

        $this->assertGreaterThan(
            1,
            count($modules),
            'the shop must have several modules installed, or a per module query would be invisible here'
        );

        // one for the counter itself
        $this->assertSame(1, $after - $before - 1, 'getInstalled() must answer with a single query');
    }

    public function testGetInstalledStillReportsWhichModulesAreActive(): void
    {
        $modules = $this->moduleDataProvider->getInstalled();

        $this->assertNotEmpty($modules);

        foreach ($modules as $name => $module) {
            $this->assertIsBool($module['active'], sprintf('module %s', $name));
            $this->assertTrue($module['installed']);
            $this->assertSame($module['active'], $this->isAttachedToAShopOfTheContext((int) $module['id']));
        }

        $this->assertContains(true, array_column($modules, 'active'), 'no module is active, so the flag proves nothing');
    }

    private function isAttachedToAShopOfTheContext(int $moduleId): bool
    {
        $shopIds = (new \PrestaShop\PrestaShop\Adapter\Shop\Context())->getContextListShopID();

        if (0 === count($shopIds)) {
            return false;
        }

        $rows = Db::getInstance()->executeS(
            'SELECT 1 FROM `' . _DB_PREFIX_ . 'module_shop`'
            . ' WHERE `id_module` = ' . $moduleId
            . ' AND `id_shop` IN (' . implode(',', array_map('intval', $shopIds)) . ')'
        );

        return !empty($rows);
    }

    private function queriesRun(): int
    {
        $rows = Db::getInstance()->executeS("SHOW SESSION STATUS LIKE 'Questions'");

        return (int) $rows[0]['Value'];
    }
}
