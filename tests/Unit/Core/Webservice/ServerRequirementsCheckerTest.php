<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace tests\Unit\Core\Webservice;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Hosting\HostingInformation;
use PrestaShop\PrestaShop\Core\Configuration\PhpExtensionCheckerInterface;
use PrestaShop\PrestaShop\Core\Webservice\ServerRequirementsChecker;
use Symfony\Component\Translation\TranslatorInterface;

class ServerRequirementsCheckerTest extends TestCase
{
    /**
     * @var TranslatorInterface
     */
    private $mockedTranslator;

    /**
     * @var Configuration
     */
    private $mockedConfiguration;

    /**
     * @var HostingInformation
     */
    private $mockedHostingInformation;

    /**
     * @var PhpExtensionCheckerInterface
     */
    private $mockedPhpExtensionChecker;

    protected function setUp()
    {
        $this->mockedTranslator = $this->createMock(TranslatorInterface::class);
        $this->mockedTranslator
            ->method('trans')
            ->will($this->returnArgument(0));

        $this->mockedConfiguration = $this->createMock(Configuration::class);
        $this->mockedHostingInformation = $this->createMock(HostingInformation::class);
        $this->mockedPhpExtensionChecker = $this->createMock(PhpExtensionCheckerInterface::class);
    }

    public function testErrorIsReturnedWhenNonApacheWebServerIsUsed()
    {
        $this->mockedHostingInformation
            ->method('getServerInformation')
            ->willReturn(['version' => 'nginx']);

        $errors = $this->createNewServerRequirementsChecker()->checkForErrors();

        $this->assertContains('To avoid operating problems, please use an Apache server.', $errors);
    }

    public function testNoErrorsAreReturnedWhenUsingApacheWebServer()
    {
        $this->mockedHostingInformation
            ->method('getServerInformation')
            ->willReturn(['version' => 'Apache/2.4.29 (Ubuntu)']);

        $errors = $this->createNewServerRequirementsChecker()->checkForErrors();

        $this->assertNotContains('To avoid operating problems, please use an Apache server.', $errors);
    }

    public function testNoErrorsAreReturnedWhenSslIsEnabled()
    {
        $this->mockedConfiguration
            ->method('getBoolean')
            ->will($this->returnValue(true));

        $errors = $this->createNewServerRequirementsChecker()->checkForErrors();

        $this->assertNotContains('It is preferable to use SSL (https:) for webservice calls, as it avoids the "man in the middle" type security issues.', $errors);
    }

    public function testThatErrorIsReturnedWhenSslIsNotEnabled()
    {
        $this->mockedConfiguration
            ->method('getBoolean')
            ->will($this->returnValue(false));

        $errors = $this->createNewServerRequirementsChecker()->checkForErrors();

        $this->assertContains('It is preferable to use SSL (https:) for webservice calls, as it avoids the "man in the middle" type security issues.', $errors);
    }

    /**
     * @return ServerRequirementsChecker
     */
    private function createNewServerRequirementsChecker()
    {
        return new ServerRequirementsChecker(
            $this->mockedTranslator,
            $this->mockedConfiguration,
            $this->mockedHostingInformation,
            $this->mockedPhpExtensionChecker
        );
    }
}
