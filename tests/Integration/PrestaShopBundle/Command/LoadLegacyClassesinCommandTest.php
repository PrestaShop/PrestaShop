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

use Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use TypeError;

class LoadLegacyClassesinCommandTest extends KernelTestCase
{
    private $previousErrorReportingLevel;

    public function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->previousErrorReportingLevel = error_reporting(E_WARNING);
    }

    public function testLoadLegacyCommandWithoutContextFails()
    {
        $this->expectException(TypeError::class, 'Not enough arguments (missing: "theme, locale").');

        $application = new Application(static::$kernel);
        $application->add(new class() extends ContainerAwareCommand {
            protected function configure()
            {
                $this->setName('prestashop-tests:load-legacy-classes');
            }

            protected function execute(InputInterface $input, OutputInterface $output)
            {
                $products = Product::getNewProducts(1);
            }
        });

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
        $application->add(new class() extends ContainerAwareCommand {
            protected function configure()
            {
                $this->setName('prestashop-tests:load-legacy-classes');
            }

            protected function execute(InputInterface $input, OutputInterface $output)
            {
                $this->getContainer()->get('prestashop.adapter.legacy_context_loader')->loadGenericContext();
                $products = Product::getNewProducts(1);
            }
        });

        $command = $application->find('prestashop-tests:load-legacy-classes');
        $this->assertNotNull($command);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    protected function tearDown()
    {
        self::$kernel->shutdown();
        error_reporting($this->previousErrorReportingLevel);
    }
}
