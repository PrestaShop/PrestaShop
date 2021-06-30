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
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container as LegacyContainer;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShopBundle\Controller\Admin\MultistoreController;
use Shop;
use Smarty;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Tools;

class AdminControllerTest extends TestCase
{
    /**
     * @var Context|null
     */
    private $context;

    protected function setUp(): void
    {
        $this->declareRequiredConstants();
        $this->requireAliasesFunctions();

        $this->context = Context::getContext();
        Context::setInstanceForTesting($this->getMockContext());

        ServiceLocator::setServiceContainerInstance($this->getMockLegacyContainer());
    }

    protected function tearDown(): void
    {
        Context::setInstanceForTesting($this->context);
    }

    public static function tearDownAfterClass(): void
    {
        Tools::resetRequest();
    }

    /**
     * @test
     * @dataProvider getControllersClasses
     *
     * @param $controllerClass
     *
     * @return mixed
     */
    public function itShouldRunTheTestedController($controllerClass): void
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
            ['AdminStatusesController'],
            ['AdminLoginController'],
            ['AdminQuickAccessesController'],
            ['AdminCustomerThreadsController'],
            ['AdminReferrersController'],
            ['AdminReturnController'],
            ['AdminStoresController'],
            ['AdminSuppliersController'],
            ['AdminAttributesGroupsController'],
            ['AdminNotFoundController'],
            ['AdminFeaturesController'],
            ['AdminGendersController'],
            ['AdminTagsController'],
            ['AdminShopController'],
            ['AdminCartRulesController'],
            ['AdminGroupsController'],
            ['AdminShopGroupController'],
            ['AdminTaxRulesGroupController'],
            ['AdminCartsController'],
            ['AdminImagesController'],
            ['AdminShopUrlController'],
            ['AdminStatesController'],
            ['AdminStatsController'],
            ['AdminLegacyLayoutController'],
        ];
    }

    private function declareRequiredConstants(): void
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

    private function getMockTranslator(): Translator
    {
        return $this->getMockBuilder(Translator::class)->disableOriginalConstructor()->getMock();
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

    private function requireAliasesFunctions(): void
    {
        require_once dirname(__DIR__, 4) . '/config/alias.php';
    }

    private function getMockLanguage(): Language
    {
        return $this->getMockBuilder(Language::class)->getMock();
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
        $mockContainerBuilder = $this->getMockBuilder(ContainerBuilder::class)->getMock();
        $mockContainerBuilder->method('get')
            ->willReturnCallback(function (string $param) {
                if ($param == Controller::SERVICE_LOCALE_REPOSITORY) {
                    return $this->getMockLocaleRepository();
                }
                if ($param == 'prestashop.core.admin.multistore') {
                    return $this->getMockMultistoreController();
                }
                if ($param == 'prestashop.adapter.multistore_feature') {
                    return $this->getMockFeatureInterface();
                }
            });

        return $mockContainerBuilder;
    }

    private function getMockContext(): Context
    {
        $mockContext = $this->getMockBuilder(Context::class)->getMock();

        $mockContext->method('getTranslator')->willReturn(
            $this->getMockTranslator()
        );
        $mockContext->method('getDevice')->willReturn(null);
        $mockContext->method('getCurrentLocale')->willReturn(
            $this->getMockLocale()
        );

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
        return $this->getMockBuilder(Locale::class)->disableOriginalConstructor()->getMock();
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

        $mockMultistoreController = $this->getMockBuilder(MultistoreController::class)->getMock();
        $mockMultistoreController->method('header')->withAnyParameters()->willReturn($mockResponse);

        return $mockMultistoreController;
    }

    private function getMockFeatureInterface(): FeatureInterface
    {
        $mockMockFeatureInterface = $this->getMockBuilder(FeatureInterface::class)->getMock();
        $mockMockFeatureInterface->method('isUsed')->willReturn(false);

        return $mockMockFeatureInterface;
    }
}
