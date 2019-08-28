<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\PrestaShopBundle\Console;

use PrestaShop\PrestaShop\Core\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;
use PHPUnit\Framework\TestCase;
use AppKernel;
use Tests\Integration\PrestaShopBundle\Fixtures\LegacyClassesUsedInSymfonyCommand;

/**
 * Tests the PrestaShop Console and Commands using legacy classes from the PrestaShop autoloader
 *
 * @doc ./vendor/bin/phpunit -c tests/Integration/phpunit.xml --filter="PrestaShopConsoleTest"
 */
class PrestaShopConsoleTest extends TestCase
{
    /** @var string */
    private $consoleDisplay;

    protected function setUp()
    {
        $app = $this->getConsoleApplication();
        $app->run([], ['capture_stderr_separately' => true]);

        $this->consoleDisplay = $app->getDisplay();
    }

    public function testTheConsoleIsDisplayedWithoutErrors()
    {
        $app = $this->getConsoleApplication();
        $app->run([], ['capture_stderr_separately' => true]);

        self::assertEmpty($app->getErrorOutput());
    }

    public function testTheConsoleShouldDisplayPrestaShopVersion()
    {
        self::assertRegExp('/PrestaShop ' . AppKernel::VERSION . ' \(kernel: app, env: test, debug: false\)/', $this->consoleDisplay);
    }

    public function testTheConsoleShouldDisplayPrestaShopOptions()
    {
        self::assertRegExp('/--id_shop\[=ID_SHOP\]/', $this->consoleDisplay);
        self::assertRegExp('/--id_shop_group\[=ID_SHOP_GROUP\]/', $this->consoleDisplay);
        self::assertRegExp('/-em, --employee=EMPLOYEE/', $this->consoleDisplay);
    }

    public function testTheConsoleShouldBeAbleToLoadLegacyClassesInCommand()
    {
        $app = $this->getConsoleApplication([
            new LegacyClassesUsedInSymfonyCommand(),
        ]);

        $app->run(
            ['prestashop:test:shop-status'],
            ['capture_stderr_separately' => true]
        );

        self::assertSame(0, $app->getStatusCode());
        self::assertEmpty($app->getErrorOutput());

        self::assertRegExp('/Information for shop/', $app->getDisplay());
    }

    /**
     * @param array $commands the list of commands to register
     *
     * @return ApplicationTester
     */
    private function getConsoleApplication(array $commands = [])
    {
        $kernel = new AppKernel('test', false);
        $application = new Application($kernel);
        $application->setAutoExit(false);

        if (count($commands) > 1) {
            foreach ($commands as $command) {
                $application->add($command);
            }
        }

        return new ApplicationTester($application);
    }
}
