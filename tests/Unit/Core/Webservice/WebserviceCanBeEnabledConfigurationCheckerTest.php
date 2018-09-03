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

use PrestaShop\PrestaShop\Core\Webservice\WebserviceCanBeEnabledConfigurationChecker;
use Symfony\Component\HttpFoundation\Request;

class WebserviceCanBeEnabledConfigurationCheckerTest extends \PHPUnit\Framework\TestCase
{
    private $translatorStub;
    private $configurationStub;

    public function setUp()
    {
        // mock translator
        $this->translatorStub = $this->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')
            ->getMock();
        $this->translatorStub
            ->method('trans')
            ->will($this->returnArgument(0));


        $this->configurationStub = $this->getMockBuilder('PrestaShop\PrestaShop\Adapter\Configuration')
            ->getMock();

        parent::setUp();
    }

    public function testCheckApacheRequest()
    {
        $request = new Request([], [], [], [], [], ['SERVER_SOFTWARE' => 'Apache/2.2.22 (Win64) PHP/5.3.13']);

        $checker = new WebserviceCanBeEnabledConfigurationChecker($this->translatorStub, $this->configurationStub);

        $warnings = $checker->getErrors($request);

        $this->assertFalse(in_array('To avoid operating problems, please use an Apache server.', $warnings));
    }

    public function testCheckMicrosoftServerRequest()
    {
        $request = new Request([], [], [], [], [], ['SERVER_SOFTWARE' => 'SERVER_SOFTWARE=Microsoft-IIS/4.0']);

        $checker = new WebserviceCanBeEnabledConfigurationChecker($this->translatorStub, $this->configurationStub);

        $warnings = $checker->getErrors($request);

        $this->assertTrue(in_array('To avoid operating problems, please use an Apache server.', $warnings));
    }

    public function testWithSSLEnabledConfiguration()
    {
        $this->configurationStub
            ->method('getBoolean')
            ->will($this->returnValue(true));

        $checker = new WebserviceCanBeEnabledConfigurationChecker($this->translatorStub, $this->configurationStub);

        $warnings = $checker->getErrors();

        $this->assertFalse(in_array('It is preferable to use SSL (https:) for webservice calls, as it avoids the "man in the middle" type security issues.', $warnings));
    }

    public function testWithNoSSLConfiguration()
    {
        $this->configurationStub
            ->method('getBoolean')
            ->will($this->returnValue(false));

        $checker = new WebserviceCanBeEnabledConfigurationChecker($this->translatorStub, $this->configurationStub);

        $warnings = $checker->getErrors();

        $this->assertTrue(in_array('It is preferable to use SSL (https:) for webservice calls, as it avoids the "man in the middle" type security issues.', $warnings));
    }
}
