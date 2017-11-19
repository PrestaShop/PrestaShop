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

namespace Tests\Integration\PrestaShopBundle\Test;

use Tests\PrestaShopBundle\Utils\Database;
use Psr\Log\NullLogger;
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

    public static function setUpBeforeClass()
    {
        Database::restoreTestDB();
    }

    public function setUp()
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->router = self::$kernel->getContainer()->get('router');
        $this->translator = self::$kernel->getContainer()->get('translator');

        $employeeMock = $this->getMockBuilder('\Employee')
            ->getMock();
        $employeeMock->id_profile = 1;

        $contextMock = $this->getMockBuilder('\Context')
            ->setMethods(array('getTranslator', 'getBaseURL'))
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->method('getTranslator')
            ->will($this->returnValue($this->translator));

        $contextMock->employee = $employeeMock;

        $shopMock = $this->getMockBuilder('\Shop')
            ->setMethods(array('getBaseURL'))
            ->getMock();
        $shopMock->id = 1;
        $shopMock->method('getBaseURL')
            ->willReturn('my-awesome-url.com');

        $contextMock->shop = $shopMock;

        $themeMock = $this->getMockBuilder('\Theme')
            ->setMethods(array('getName'))
            ->disableOriginalConstructor()
            ->getMock();

        $themeMock->method('getName')
            ->willReturn('classic');

        $contextMock->shop->theme = $themeMock;

        $languageMock = $this->getMockBuilder('\Language')
            ->disableAutoload()
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->language = $languageMock;

        $currencyMock = $this->getMockBuilder('\Currency')
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->currency = $currencyMock;

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
        self::$kernel->getContainer()->set('logger', new NullLogger());
    }

    protected function enableDemoMode()
    {
        $configurationMock = $this->getMockBuilder('\PrestaShop\PrestaShop\Adapter\Configuration')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->disableAutoload()
            ->getMock();

        $values = array(
            array('_PS_MODE_DEMO_', true),
            array('_PS_MODULE_DIR_', __DIR__.'/../../../resources/modules/'),
        );

        $configurationMock->method('get')
            ->will($this->returnValueMap($values));

        self::$kernel->getContainer()->set('prestashop.adapter.legacy.configuration', $configurationMock);
    }
}
