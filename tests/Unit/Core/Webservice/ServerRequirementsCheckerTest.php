<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Webservice;

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
    private $translator;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var HostingInformation
     */
    private $hostingInformation;

    /**
     * @var PhpExtensionCheckerInterface
     */
    private $phpExtensionChecker;

    public function setUp()
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->translator
            ->method('trans')
            ->will($this->returnArgument(0))
        ;

        $this->configuration = $this->createMock(Configuration::class);
        $this->hostingInformation = $this->createMock(HostingInformation::class);
        $this->phpExtensionChecker = $this->createMock(PhpExtensionCheckerInterface::class);
    }

    public function testErrorIsReturnedWhenNonApacheWebServerIsUsed()
    {
        $this->hostingInformation
            ->method('getServerInformation')
            ->willReturn(['version' => 'nginx'])
        ;

        $errors = $this->createNewServerRequirementsChecker()->checkForErrors();

        $this->assertContains('To avoid operating problems, please use an Apache server.', $errors);
    }

    public function testNoErrorsAreReturnedWhenUsingApacheWebServer()
    {
        $this->hostingInformation
            ->method('getServerInformation')
            ->willReturn(['version' => 'Apache/2.4.29 (Ubuntu)'])
        ;

        $errors = $this->createNewServerRequirementsChecker()->checkForErrors();

        $this->assertNotContains('To avoid operating problems, please use an Apache server.', $errors);
    }

    public function testNoErrorsAreReturnedWhenSslIsEnabled()
    {
        $this->configuration
            ->method('getBoolean')
            ->will($this->returnValue(true));

        $errors = $this->createNewServerRequirementsChecker()->checkForErrors();

        $this->assertNotContains('It is preferable to use SSL (https:) for webservice calls, as it avoids the "man in the middle" type security issues.', $errors);
    }

    public function testThatErrorIsReturnedWhenSslIsNotEnabled()
    {
        $this->configuration
            ->method('getBoolean')
            ->will($this->returnValue(false));

        $errors = $this->createNewServerRequirementsChecker()->checkForErrors();

        $this->assertContains('It is preferable to use SSL (https:) for webservice calls, as it avoids the "man in the middle" type security issues.', $errors);
    }

    private function createNewServerRequirementsChecker()
    {
        return new ServerRequirementsChecker(
            $this->translator,
            $this->configuration,
            $this->hostingInformation,
            $this->phpExtensionChecker
        );
    }
}
