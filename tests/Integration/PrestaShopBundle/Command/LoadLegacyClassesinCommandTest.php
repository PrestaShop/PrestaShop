<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Command;

use Context;
use Exception;
use PrestaShop\PrestaShop\Adapter\LegacyContextLoader;
use Product;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * These tests need to run a symfony command with and without the context helper, so it needs to be run isolated or
 * other tests may have already fixed or mocked the context.
 *
 * @group isolatedProcess
 */
class LoadLegacyClassesinCommandTest extends KernelTestCase
{
    private $previousErrorReportingLevel;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
        $this->previousErrorReportingLevel = error_reporting(E_WARNING);
    }

    public function testLoadLegacyCommandWithoutContextFails()
    {
        set_error_handler(
            static function ($errno, $errstr) {
                restore_error_handler();
                throw new Exception($errstr, $errno);
            },
            E_WARNING
        );
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Attempt to read property "controller_type" on null');

        $application = new Application(static::$kernel);
        $application->add(new class() extends Command {
            protected function configure()
            {
                $this->setName('prestashop-tests:load-legacy-classes');
            }

            /**
             * @param InputInterface $input
             * @param OutputInterface $output
             *
             * @return int
             */
            protected function execute(InputInterface $input, OutputInterface $output)
            {
                $products = Product::getNewProducts(1);

                return 0;
            }
        });

        Context::getContext()->controller = null;
        $command = $application->find('prestashop-tests:load-legacy-classes');
        $this->assertNotNull($command);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);
    }

    public function testLoadLegacyCommandWithContextWorks()
    {
        $application = new Application(static::$kernel);
        $application->add(new class() extends Command {
            protected function configure()
            {
                $this->setName('prestashop-tests:load-legacy-classes');
            }

            /**
             * @param InputInterface $input
             * @param OutputInterface $output
             *
             * @return int
             */
            protected function execute(InputInterface $input, OutputInterface $output)
            {
                $contextLoader = new LegacyContextLoader(Context::getContext());
                $contextLoader->loadGenericContext();
                $products = Product::getNewProducts(1);

                return 0;
            }
        });

        Context::getContext()->controller = null;
        $command = $application->find('prestashop-tests:load-legacy-classes');
        $this->assertNotNull($command);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        error_reporting($this->previousErrorReportingLevel);
    }
}
