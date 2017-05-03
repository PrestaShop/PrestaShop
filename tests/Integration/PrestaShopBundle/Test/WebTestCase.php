<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Integration\PrestaShopBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as TestCase;

class WebTestCase extends TestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * @var \Symfony\Component\Routing\Router
     */
    protected $router;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    public function setUp()
    {
        $this->client = self::createClient();
        $this->router = self::$kernel->getContainer()->get('router');
        $this->translator = self::$kernel->getContainer()->get('translator');

        $employeeMock = $this->getMockBuilder('\Employee')
        ->getMock();

        $contextMock = $this->getMockBuilder('\Context')
            ->disableAutoload()
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->employee = $employeeMock;

        $languageMock = $this->getMockBuilder('\Language')
            ->disableAutoload()
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->language = $languageMock;

        $legacyContextMock = $this->getMockBuilder('\PrestaShop\PrestaShop\Adapter\LegacyContext')
            ->setMethods([
                'getContext',
                'getEmployeeLanguageIso',
                'getEmployeeCurrency',
                'getRootUrl'
            ])
            ->disableAutoload()
            ->disableOriginalConstructor()
            ->getMock();

        $legacyContextMock->method('getContext')
            ->will($this->returnValue($contextMock));

        self::$kernel->getContainer()->set('prestashop.adapter.legacy.context', $legacyContextMock);
    }

    protected function enableDemoMode()
    {
        $configurationMock = $this->getMockBuilder('\PrestaShop\PrestaShop\Adapter\Configuration')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->disableAutoload()
            ->getMock();

        $configurationMock->method('get')->with('_PS_MODE_DEMO_')
            ->will($this->returnValue(true));

        self::$kernel->getContainer()->set('prestashop.adapter.legacy.configuration', $configurationMock);
    }
}
