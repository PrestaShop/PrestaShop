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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        $this->expectExceptionMessage('Attempt to read property "precision" on null');

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

        Context::getContext()->currency = null;
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

        Context::getContext()->currency = null;
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
