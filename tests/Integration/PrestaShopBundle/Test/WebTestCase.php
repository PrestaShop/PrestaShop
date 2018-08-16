<?php
/**
 * 2007-2018 PrestaShop.
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

namespace Tests\Integration\PrestaShopBundle\Test;

use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as TestCase;
use Tests\PrestaShopBundle\Utils\DatabaseCreator as Database;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Routing\Router;
use Psr\Log\NullLogger;

use Currency;
use Employee;
use Language;
use Context;
use Theme;
use Shop;

/**
 * Responsible of e2e and integration tests using Symfony.
 * Time-consuming, by default dump and restore the entire database.
 */
class WebTestCase extends TestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Translator
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

        $employeeMock = $this->getMockBuilder(Employee::class)
            ->getMock();
        $employeeMock->id_profile = 1;

        $contextMock = $this->getMockBuilder(Context::class)
            ->setMethods(array('getTranslator', 'getContext'))
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->method('getTranslator')
            ->will(self::returnValue($this->translator));

        $contextMock->method('getContext')
            ->will(self::returnValue($contextMock));

        $contextMock->employee = $employeeMock;

        $shopMock = $this->getMockBuilder(Shop::class)
            ->setMethods(array('getBaseURL'))
            ->disableOriginalConstructor()
            ->getMock();

        $shopMock->id = 1;
        $shopMock->method('getBaseURL')
            ->willReturn('my-awesome-url.com');

        $contextMock->shop = $shopMock;

        $themeMock = $this->getMockBuilder(Theme::class)
            ->setMethods(array('getName'))
            ->disableOriginalConstructor()
            ->getMock();

        $themeMock->method('getName')
            ->willReturn('classic');

        $contextMock->shop->theme = $themeMock;

        $countryMock = $this->getMockBuilder(Country::class)
            ->getMock();
        $countryMock->iso_code = 'en';

        $contextMock->country = $countryMock;

        $languageMock = $this->getMockBuilder(Language::class)
            ->disableAutoload()
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->language = $languageMock;

        $currencyMock = $this->getMockBuilder(Currency::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->currency = $currencyMock;

        $currencyDataProviderMock = $this->getMockBuilder(CurrencyDataProvider::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultCurrencyIsoCode'])
            ->getMock()
        ;

        $currencyDataProviderMock->method('getDefaultCurrencyIsoCode')
            ->will(self::returnValue('en'))
        ;

        $legacyContextMock = $this->getMockBuilder(LegacyContext::class)
            ->setMethods([
                'getContext',
                'getEmployeeLanguageIso',
                'getEmployeeCurrency',
                'getRootUrl',
                'getLanguages',
                'getLanguage',
            ])
            ->disableAutoload()
            ->disableOriginalConstructor()
            ->getMock();

        $legacyContextMock->method('getContext')
            ->willReturn($contextMock);

        $legacyContextMock->method('getLanguages')
            ->will(
                self::returnValue(
                    [
                        [
                            'id_lang' => '1',
                            'name' => 'English (English)',
                            'iso_code' => 'en',
                            'language_code' => 'en-us',
                            'locale' => 'en-US',
                        ],
                        [
                            'id_lang' => '2',
                            'name' => 'Français (French)',
                            'iso_code' => 'fr',
                            'language_code' => 'fr',
                            'locale' => 'fr-FR',
                        ],
                    ]
                )
            );

        $legacyContextMock->method('getLanguage')
            ->will(
                self::returnValue($languageMock)
            );

        self::$kernel->getContainer()->set('prestashop.adapter.data_provider.currency', $currencyDataProviderMock);
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
            array('_PS_MODE_DEMO_', null, true),
            array('_PS_MODULE_DIR_', null, __DIR__ . '/../../../resources/modules/'),
        );

        $configurationMock->method('get')
            ->will(self::returnValueMap($values));

        self::$kernel->getContainer()->set('prestashop.adapter.legacy.configuration', $configurationMock);
    }
}
