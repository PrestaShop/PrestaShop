<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\TradeCredit\Repository;

use Module;
use PrestaShop\PrestaShop\Adapter\TradeCredit\Repository\TradeCreditPaymentModuleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class TradeCreditPaymentModuleRepositoryTest extends KernelTestCase
{
    private TradeCreditPaymentModuleRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(TradeCreditPaymentModuleRepository::class);
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreAllTables();
        parent::tearDown();
    }

    public function testSetAndGetForShop(): void
    {
        $this->repository->setTradeCreditModules(1, [3, 5]);

        $this->assertSame([3, 5], $this->repository->getTradeCreditModuleIds(1));
        $this->assertTrue($this->repository->isTradeCreditModule(5, 1));
        $this->assertFalse($this->repository->isTradeCreditModule(4, 1));
    }

    public function testSetReplacesPrevious(): void
    {
        $this->repository->setTradeCreditModules(1, [3, 5]);
        $this->repository->setTradeCreditModules(1, [3]);

        $this->assertSame([3], $this->repository->getTradeCreditModuleIds(1));
    }

    public function testFlagIsPerShop(): void
    {
        $this->repository->setTradeCreditModules(1, [3]);

        $this->assertFalse($this->repository->isTradeCreditModule(3, 2));
        $this->assertSame([], $this->repository->getTradeCreditModuleIds(2));
    }

    public function testUninstallRemovesTradeCreditFlag(): void
    {
        $module = Module::getInstanceByName('bankwire');
        $this->assertNotFalse($module, 'bankwire test payment module must be available');
        $moduleId = (int) $module->id;
        $this->repository->setTradeCreditModules(1, [$moduleId]);

        $this->assertTrue($module->uninstall());

        $this->assertFalse($this->repository->isTradeCreditModule($moduleId, 1));
    }
}
