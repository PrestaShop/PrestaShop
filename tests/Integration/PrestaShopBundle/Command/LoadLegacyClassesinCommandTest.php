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
use PrestaShop\PrestaShop\Adapter\LegacyContextLoader;
use Product;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use TypeError;

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
        /*
         * Since PHP 8.0.0, error levels changed, that's why we need to check 2 different exception/warning.
         * Either way, the exception/warning comes from the fact that we try to get the property of the context currency
         * but the currency is null.
         *
         * @see https://wiki.php.net/rfc/engine_warnings
         */
        if (version_compare(phpversion(), '8.0', '>=')) {
            $this->expectWarning();
            $this->expectWarningMessage('Attempt to read property "precision" on null');
        } else {
            $this->expectException(TypeError::class);
            $this->expectExceptionMessageMatches('/Argument 1 passed to PrestaShop\\\PrestaShop\\\Core\\\Localization\\\CLDR\\\ComputingPrecision::getPrecision\(\) must be of the type int(:?eger)?, null given/');
        }

        $application = new Application(static::$kernel);
        $application->add(new class() extends Command {
            protected function configure()
            {
                $this->setName('prestashop-tests:load-legacy-classes');
            }

            protected function execute(InputInterface $input, OutputInterface $output)
            {
                $products = Product::getNewProducts(1);
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

            protected function execute(InputInterface $input, OutputInterface $output)
            {
                $contextLoader = new LegacyContextLoader(Context::getContext());
                $contextLoader->loadGenericContext();
                $products = Product::getNewProducts(1);
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
        self::$kernel->shutdown();
        error_reporting($this->previousErrorReportingLevel);
    }
}
