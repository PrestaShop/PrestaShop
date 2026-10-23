<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\EventListener;

use Configuration;
use Db;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Console\PrestaShopApplication;
use PrestaShopBundle\Entity\Shop as ShopEntity;
use PrestaShopBundle\Entity\ShopGroup;
use PrestaShopException;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\Resources\DatabaseDump;

class MultishopCommandListenerTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['configuration', 'shop'];

    /**
     * @var Context
     */
    public $multishopContext;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        Configuration::resetStaticCache();
        Shop::resetStaticCache();
        self::bootKernel();

        $entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $shopGroup = $entityManager->find(ShopGroup::class, 1);
        $shop = new ShopEntity();
        $shop->setActive(true);
        $shop->setIdCategory(2);
        $shop->setName('test_shop_multishop_command_listener');
        $shop->setShopGroup($shopGroup);
        $shop->setColor('red');
        $shop->setThemeName(Theme::getDefaultTheme());
        $shop->setDeleted(false);
        $entityManager->persist($shop);
        $entityManager->flush();

        self::setMultishopFeatureActive(true);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        Shop::resetStaticCache();
        Configuration::resetStaticCache();
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        self::setMultishopFeatureActive(true);
        Shop::resetContext();

        $this->multishopContext = self::$kernel->getContainer()->get('prestashop.adapter.shop.context');
    }

    public function testDefaultMultishopContext(): void
    {
        Shop::resetContext();
        $this->assertFalse($this->multishopContext->isShopContext(), 'isShopContext');
        $this->assertFalse($this->multishopContext->isGroupShopContext(), 'isGroupShopContext');
        $this->assertFalse($this->multishopContext->isAllShopContext(), 'isAllShopContext');
    }

    public function testSetShopID(): void
    {
        [$status] = $this->runApplication('multishop:probe --id_shop=1');

        $this->assertSame(Command::SUCCESS, $status);
        $this->assertTrue($this->multishopContext->isShopContext(), 'isShopContext');
    }

    public function testSetShopGroupID(): void
    {
        [$status] = $this->runApplication('multishop:probe --id_shop_group=1');

        $this->assertSame(Command::SUCCESS, $status);
        $this->assertTrue($this->multishopContext->isGroupShopContext());
    }

    public function testShopOptionBeforeCommandName(): void
    {
        [$status] = $this->runApplication('--id_shop=1 multishop:probe');

        $this->assertSame(Command::SUCCESS, $status);
        $this->assertTrue($this->multishopContext->isShopContext());
    }

    public function testDefaultContextIsNotChanged(): void
    {
        Shop::setContext(Shop::CONTEXT_ALL);

        [$status] = $this->runApplication('multishop:probe');

        $this->assertSame(Command::SUCCESS, $status);
        $this->assertTrue($this->multishopContext->isAllShopContext());
    }

    public function testOptionsAreShownInCommandAndListHelp(): void
    {
        [$commandStatus, $commandOutput] = $this->runApplication('multishop:probe --help');
        [$listStatus, $listOutput] = $this->runApplication('list --help');

        $this->assertSame(Command::SUCCESS, $commandStatus);
        $this->assertSame(Command::SUCCESS, $listStatus);
        $this->assertStringContainsString('--id_shop[=ID_SHOP]', $commandOutput);
        $this->assertStringContainsString('--id_shop_group[=ID_SHOP_GROUP]', $commandOutput);
        $this->assertStringContainsString('--id_shop[=ID_SHOP]', $listOutput);
        $this->assertStringContainsString('--id_shop_group[=ID_SHOP_GROUP]', $listOutput);
    }

    public function testOptionsAreNotShownWhenMultishopIsDisabled(): void
    {
        self::setMultishopFeatureActive(false);

        [$commandStatus, $commandOutput] = $this->runApplication('multishop:probe --help');
        [$listStatus, $listOutput] = $this->runApplication('list --help');

        $this->assertSame(Command::SUCCESS, $commandStatus);
        $this->assertSame(Command::SUCCESS, $listStatus);
        $this->assertStringNotContainsString('--id_shop', $commandOutput);
        $this->assertStringNotContainsString('--id_shop_group', $commandOutput);
        $this->assertStringNotContainsString('--id_shop', $listOutput);
        $this->assertStringNotContainsString('--id_shop_group', $listOutput);
    }

    public function testCommandRunsWithoutMultishopOptionsWhenDisabled(): void
    {
        self::setMultishopFeatureActive(false);
        Shop::setContext(Shop::CONTEXT_ALL);

        [$status] = $this->runApplication('multishop:probe');

        $this->assertSame(Command::SUCCESS, $status);
        $this->assertTrue($this->multishopContext->isAllShopContext());
    }

    public function testMultishopOptionsAreRejectedWhenDisabled(): void
    {
        self::setMultishopFeatureActive(false);

        foreach (['id_shop', 'id_shop_group'] as $option) {
            [$status, $output] = $this->runApplication(sprintf('multishop:probe --%s=1', $option));

            $this->assertSame(Command::FAILURE, $status);
            $this->assertStringContainsString('--' . $option, $output);
        }
    }

    public function testOptionsAreNotRegisteredWhenLegacyConfigurationIsNotLoaded(): void
    {
        Configuration::resetStaticCache();
        $this->assertFalse(Configuration::configurationIsLoaded());

        try {
            $definition = $this->createApplication()->getDefinition();

            $this->assertFalse($definition->hasOption('id_shop'));
            $this->assertFalse($definition->hasOption('id_shop_group'));
        } finally {
            Configuration::loadConfiguration();
            Shop::resetStaticCache();
        }
    }

    public function testCommandRunsWhenMultishopCheckFails(): void
    {
        $database = Db::getInstance();
        $unavailableDatabase = $this->createMock(Db::class);
        $unavailableDatabase->expects($this->atLeastOnce())->method('getValue')->willThrowException(
            new PrestaShopException('Database unavailable')
        );
        Shop::resetStaticCache();
        Db::setInstanceForTesting($unavailableDatabase);

        try {
            $this->assertTrue(Configuration::configurationIsLoaded());
            $definition = $this->createApplication()->getDefinition();
            [$status] = $this->runApplication('multishop:probe');

            $this->assertFalse($definition->hasOption('id_shop'));
            $this->assertFalse($definition->hasOption('id_shop_group'));
            $this->assertSame(Command::SUCCESS, $status);
        } finally {
            Db::setInstanceForTesting($database);
            Shop::resetStaticCache();
        }
    }

    public function testShopContextWorksWithOtherGlobalOptions(): void
    {
        [$status] = $this->runApplication('--app-id=admin --no-interaction --id_shop=1 multishop:probe');

        $this->assertSame(Command::SUCCESS, $status);
        $this->assertTrue($this->multishopContext->isShopContext());
    }

    public function testListCommandAcceptsShopOption(): void
    {
        [$status] = $this->runApplication('list --id_shop=1');

        $this->assertSame(Command::SUCCESS, $status);
        $this->assertTrue($this->multishopContext->isShopContext());
    }

    public function testConflictingCommandOptionFails(): void
    {
        $command = new Command('multishop:conflicting-probe');
        $command->addOption('id_shop', null, InputOption::VALUE_REQUIRED);
        $command->setCode(static function (): int {
            return Command::SUCCESS;
        });

        [$status, $output] = $this->runApplication('multishop:conflicting-probe --id_shop=1', $command);

        $this->assertSame(Command::FAILURE, $status);
        $this->assertStringContainsString('An option named "id_shop" already exists.', $output);
    }

    public function testExceptionWhenIdShopAndIdShopGroupSet(): void
    {
        [$status, $output] = $this->runApplication('multishop:probe --id_shop=1 --id_shop_group=1');

        $this->assertSame(Command::FAILURE, $status);
        $this->assertStringContainsString('Do not specify an ID shop and an ID group shop at the same time.', $output);
    }

    /**
     * @return array{int, string}
     */
    private function runApplication(string $input, ?Command $command = null): array
    {
        $application = $this->createApplication();

        if ($command !== null) {
            $application->add($command);
        } else {
            $command = new Command('multishop:probe');
            $command->setCode(static function (): int {
                return Command::SUCCESS;
            });
            $application->add($command);
        }

        $output = new BufferedOutput();
        $status = $application->run(new StringInput($input), $output);

        return [$status, $output->fetch()];
    }

    private static function setMultishopFeatureActive(bool $active): void
    {
        Configuration::updateGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE', (int) $active);
        Configuration::resetStaticCache();
        Configuration::loadConfiguration();
        Shop::resetStaticCache();
    }

    private function createApplication(): PrestaShopApplication
    {
        $application = new PrestaShopApplication(self::$kernel);
        $application->setAutoExit(false);

        return $application;
    }
}
