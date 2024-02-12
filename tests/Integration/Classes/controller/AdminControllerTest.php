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

namespace Tests\Integration\Classes\controller;

use Context;
use Controller;
use Cookie;
use Employee;
use Language;
use Link;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\EntityMapper;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container as LegacyContainer;
use PrestaShop\PrestaShop\Core\Image\AvifExtensionChecker;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberInterface;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberSymbolList;
use PrestaShopBundle\Controller\Admin\MultistoreController;
use PrestaShopBundle\Service\DataProvider\UserProvider;
use Shop;
use Smarty;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Tests\Integration\Utility\ContextMockerTrait;
use Tools;

class AdminControllerTest extends TestCase
{
    use ContextMockerTrait;

    /**
     * @var Container|null
     */
    private $savedContainer;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::declareRequiredConstants();
        self::requireAliasesFunctions();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        Tools::resetRequest();
    }

    protected function setUp(): void
    {
        self::mockContext();
        $this->adaptMockContext(self::getMockedContext());

        $this->savedContainer = ServiceLocator::getContainer();
        ServiceLocator::setServiceContainerInstance($this->getMockLegacyContainer());
    }

    protected function tearDown(): void
    {
        ServiceLocator::setServiceContainerInstance($this->savedContainer);
    }

    /**
     * Check if html in trans is not escaped by trans method but escaped with htmlspecialchars on parameters
     *
     * @dataProvider getControllersClasses
     *
     * @param string $controllerClass
     *
     * @return void
     */
    public function testTrans(string $controllerClass): void
    {
        $testedController = new $controllerClass();
        $transMethod = new \ReflectionMethod($testedController, 'trans');
        $transMethod->setAccessible(true);
        $trans = $transMethod->invoke($testedController, '<a href="test">%d Succesful deletion "%s"</a>', [10, '<b>stringTest</b>'], 'Admin.Notifications.Success');
        $this->assertEquals('<a href="test">10 Succesful deletion "<b>stringTest</b>"</a>', $trans);

        $trans = $transMethod->invoke($testedController, '<a href="test">%d Succesful deletion "%s"</a>', [10, htmlspecialchars('<b>stringTest</b>')], 'Admin.Notifications.Success');
        $this->assertEquals('<a href="test">10 Succesful deletion "&lt;b&gt;stringTest&lt;/b&gt;"</a>', $trans);
    }

    /**
     * @dataProvider getControllersClasses
     *
     * @param string $controllerClass
     *
     * @return void
     */
    public function testItShouldRunTheTestedController(string $controllerClass): void
    {
        /**
         * @var Controller $testedController
         */
        $testedController = new $controllerClass();
        $refController = new \ReflectionObject($testedController);
        $refProperty = $refController->getProperty('container');
        $refProperty->setAccessible(true);
        $refProperty->setValue($testedController, $this->getMockContainerBuilder());

        if (!defined('_PS_BASE_URL_')) {
            define('_PS_BASE_URL_', '');
            define('__PS_BASE_URI__', '');
            define('_PS_BASE_URL_SSL_', '');
        }

        if (!defined('PS_INSTALLATION_IN_PROGRESS')) {
            define('PS_INSTALLATION_IN_PROGRESS', true);
        }

        $this->assertNull($testedController->run());
    }

    /**
     * @return array list of all legacy controllers (Back Office)
     *               If you have migrated a page on Symfony, please remove the related test
     */
    public function getControllersClasses(): array
    {
        return [
            ['AdminCarriersController'],
            ['AdminLoginController'],
            ['AdminQuickAccessesController'],
            ['AdminCustomerThreadsController'],
            ['AdminReturnController'],
            ['AdminStoresController'],
            ['AdminSuppliersController'],
            ['AdminAttributesGroupsController'],
            ['AdminNotFoundController'],
            ['AdminTagsController'],
            ['AdminShopController'],
            ['AdminCartRulesController'],
            ['AdminGroupsController'],
            ['AdminShopGroupController'],
            ['AdminTaxRulesGroupController'],
            ['AdminImagesController'],
            ['AdminShopUrlController'],
            ['AdminStatsController'],
            ['AdminLegacyLayoutController'],
        ];
    }

    private static function declareRequiredConstants(): void
    {
        $configuration = require_once _PS_CACHE_DIR_ . 'appParameters.php';

        if (defined('_PS_BO_ALL_THEMES_DIR_')) {
            return;
        }

        define('_PS_BO_ALL_THEMES_DIR_', '');
        if (!defined('_PS_TAB_MODULE_LIST_URL_')) {
            define('_PS_TAB_MODULE_LIST_URL_', '');
        }
        if (!defined('_DB_SERVER_')) {
            define('_DB_SERVER_', 'localhost');
        }
        if (!defined('_DB_USER_')) {
            define('_DB_USER_', $configuration['parameters']['database_user']);
        }
        if (!defined('_DB_PASSWD_')) {
            define('_DB_PASSWD_', $configuration['parameters']['database_password']);
        }
        if (!defined('_DB_NAME_')) {
            define('_DB_NAME_', 'test_' . $configuration['parameters']['database_name']);
        }
        if (!defined('_DB_PREFIX_')) {
            define('_DB_PREFIX_', $configuration['parameters']['database_prefix']);
        }
        if (!defined('_COOKIE_KEY_')) {
            define('_COOKIE_KEY_', Tools::passwdGen(64));
        }
        if (!defined('_PS_VERSION_')) {
            define('_PS_VERSION_', '1.7');
        }
        if (!defined('_PS_ADMIN_DIR_')) {
            define('_PS_ADMIN_DIR_', '');
        }
    }

    private function getMockSmarty(): Smarty
    {
        $mockSmarty = $this->getMockBuilder(Smarty::class)->getMock();

        $mockSmarty->method('setTemplateDir')->willReturn(null);
        $mockSmarty->method('assign')->willReturn(null);
        $mockSmarty->method('fetch')->willReturn(null);
        $mockSmarty->method('getTemplateDir')->willReturn(null);
        $mockSmarty->method('fetch')->willReturn(null);
        $mockSmarty->method('createTemplate')->willReturn($mockSmarty);

        return $mockSmarty;
    }

    private function getMockEmployee(): Employee
    {
        $mockEmployee = $this->getMockBuilder(Employee::class)->getMock();
        $mockEmployee->method('isLoggedBack')->willReturn(true);
        $mockEmployee->method('hasAuthOnShop')->willReturn(true);
        $mockEmployee->id_profile = 1;

        return $mockEmployee;
    }

    private static function requireAliasesFunctions(): void
    {
        require_once dirname(__DIR__, 4) . '/config/alias.php';
    }

    private function getMockLanguage(): Language
    {
        $language = $this->getMockBuilder(Language::class)->getMock();
        $language->iso_code = 'en';
        $language->locale = 'en';

        return $language;
    }

    private function getMockShop(): Shop
    {
        return $this->getMockBuilder(Shop::class)->getMock();
    }

    private function getMockLegacyContainer(): LegacyContainer
    {
        $mockLegacyContainer = $this->getMockBuilder(LegacyContainer::class)->getMock();

        $mockEntityMapper = $this->getMockBuilder(EntityMapper::class)->getMock();
        $mockEntityMapper->method('load')->withAnyParameters()->willReturn(null);

        $mockLegacyContainer->method('make')->willReturn($mockEntityMapper);

        return $mockLegacyContainer;
    }

    private function getMockContainerBuilder(): ContainerBuilder
    {
        $mockContainerBuilder = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();
        $mockContainerBuilder->method('get')
            ->willReturnCallback(function (string $param) {
                if ($param === Controller::SERVICE_LOCALE_REPOSITORY) {
                    return $this->getMockLocaleRepository();
                }
                if ($param === 'prestashop.core.admin.multistore') {
                    return $this->getMockMultistoreController();
                }
                if ($param === 'prestashop.adapter.multistore_feature') {
                    return $this->getMockFeatureInterface();
                }
                if ($param === 'prestashop.user_provider') {
                    return $this->getMockedUserProvider();
                }
                if ($param === CsrfTokenManagerInterface::class) {
                    return $this->getMockedCsrfTokenManager();
                }
                if ($param === 'PrestaShop\PrestaShop\Core\Image\AvifExtensionChecker') {
                    return $this->getMockedAvifExtensionChecker();
                }
                if ($param === FeatureFlagStateCheckerInterface::class) {
                    return $this->getMockedFeatureFlagStateCheckerInterface();
                }
                if ($param === ImageFormatConfiguration::class) {
                    return $this->getMockedImageFormatConfiguration();
                }
            });

        return $mockContainerBuilder;
    }

    private function adaptMockContext(Context $mockContext): Context
    {
        $mockContext->currentLocale = $this->getMockLocale();
        $mockContext->smarty = $this->getMockSmarty();
        $mockContext->employee = $this->getMockEmployee();
        $mockContext->language = $this->getMockLanguage();
        $mockContext->shop = $this->getMockShop();
        $mockContext->cookie = $this->getMockCookie();
        $mockContext->link = $this->getMockLink();

        return $mockContext;
    }

    private function getMockCookie(): Cookie
    {
        return $this->getMockBuilder(Cookie::class)->disableOriginalConstructor()->getMock();
    }

    private function getMockLink(): Link
    {
        $mockLink = $this->getMockBuilder(Link::class)->getMock();
        $mockLink->method('getTabLink')->willReturn('/link');
        $mockLink->method('getAdminLink')->withAnyParameters()->willReturn('/link');

        return $mockLink;
    }

    private function getMockLocale(): Locale
    {
        $mockLocale = $this->getMockBuilder(Locale::class)->disableOriginalConstructor()->getMock();
        $mockLocale
            ->method('getPriceSpecification')
            ->withAnyParameters()
            ->willReturn($this->getMockNumberInterface());
        $mockLocale
            ->method('getNumberSpecification')
            ->withAnyParameters()
            ->willReturn($this->getMockNumberSpecification());

        return $mockLocale;
    }

    private function getMockLocaleRepository(): LocaleRepository
    {
        $mockLocaleRepository = $this->getMockBuilder(LocaleRepository::class)->disableOriginalConstructor()->getMock();
        $mockLocaleRepository
            ->method('getLocale')
            ->withAnyParameters()
            ->willReturn($this->getMockLocale());

        return $mockLocaleRepository;
    }

    private function getMockMultistoreController(): MultistoreController
    {
        $mockResponse = $this->getMockBuilder(Response::class)->getMock();
        $mockResponse->method('getContent')->willReturn('');

        $mockMultistoreController = $this
            ->getMockBuilder(MultistoreController::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockMultistoreController->method('header')->withAnyParameters()->willReturn($mockResponse);

        return $mockMultistoreController;
    }

    private function getMockedAvifExtensionChecker(): AvifExtensionChecker
    {
        $mockAvifExtensionChecker = $this->getMockBuilder(AvifExtensionChecker::class)
            ->getMock();

        $mockAvifExtensionChecker->method('isAvailable')->willReturn(true);

        return $mockAvifExtensionChecker;
    }

    private function getMockedFeatureFlagStateCheckerInterface(): FeatureFlagStateCheckerInterface
    {
        $mockFeatureFlagStateChecker = $this->getMockBuilder(FeatureFlagStateCheckerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockFeatureFlagStateChecker->method('isEnabled')->willReturn(false);

        return $mockFeatureFlagStateChecker;
    }

    private function getMockedImageFormatConfiguration(): ImageFormatConfiguration
    {
        $mockImageFormatConfiguration = $this->getMockBuilder(ImageFormatConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockImageFormatConfiguration->method('getGenerationFormats')->willReturn(['jpg']);

        return $mockImageFormatConfiguration;
    }

    private function getMockNumberSpecification(): NumberSpecification
    {
        $mockNumberSpecification = $this->getMockBuilder(NumberSpecification::class)->disableOriginalConstructor()->getMock();
        $mockNumberSpecification
            ->method('getSymbolsByNumberingSystem')
            ->withAnyParameters()
            ->willReturn($this->getMockNumberSymbolList());

        return $mockNumberSpecification;
    }

    private function getMockNumberSymbolList(): NumberSymbolList
    {
        $mockNumberSymbolList = $this->getMockBuilder(NumberSymbolList::class)->disableOriginalConstructor()->getMock();
        $mockNumberSymbolList
            ->method('toArray')
            ->withAnyParameters()
            ->willReturn([]);

        return $mockNumberSymbolList;
    }

    private function getMockNumberInterface(): NumberInterface
    {
        $mockNumberInterface = $this->getMockBuilder(NumberInterface::class)->disableOriginalConstructor()->getMock();
        $mockNumberInterface
            ->method('getSymbolsByNumberingSystem')
            ->withAnyParameters()
            ->willReturn($this->getMockNumberSymbolList());

        return $mockNumberInterface;
    }

    private function getMockFeatureInterface(): FeatureInterface
    {
        $mockMockFeatureInterface = $this->getMockBuilder(FeatureInterface::class)->getMock();
        $mockMockFeatureInterface->method('isUsed')->willReturn(false);

        return $mockMockFeatureInterface;
    }

    private function getMockedUserProvider(): UserProvider
    {
        $userProvider = $this->createMock(UserProvider::class);
        $userProvider->method('getUsername')->willReturn('testUser');

        return $userProvider;
    }

    private function getMockedCsrfTokenManager(): CsrfTokenManager
    {
        $mockedCrfToken = $this->createMock(CsrfToken::class);
        $mockedCrfToken->method('getValue')->willReturn('mockedToken');

        $tokenManager = $this->createMock(CsrfTokenManager::class);
        $tokenManager->method('getToken')->withAnyParameters()->willReturn($mockedCrfToken);

        return $tokenManager;
    }
}
