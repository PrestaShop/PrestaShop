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

namespace Tests\PrestaShopBundle\Controller;

use Context;
use Controller;
use Prophecy\Argument;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PHPUnit\Framework\TestCase;
use Tools;

/**
 * @group controller
 */
class ControllerTest extends TestCase
{
    private $context;

    public function setUp()
    {
        $this->declareRequiredConstants();
        $this->requireAliasesFunctions();

        $contextProphecy = $this->prophesizeContext();

        $this->context = Context::getContext();
        Context::setInstanceForTesting($contextProphecy->reveal());

        $containerProphecy = $this->prophesizeContainer();
        ServiceLocator::setServiceContainerInstance($containerProphecy->reveal());
    }

    public function tearDown()
    {
        Context::setInstanceForTesting($this->context);
    }

    /**
     * @test
     * @dataProvider getControllersClasses
     *
     * @param $controllerClass
     * @return mixed
     */
    public function itShouldRunTheTestedController($controllerClass)
    {
        /**
         * @var Controller $testedController
         */
        $testedController = new $controllerClass();

        if (!defined('_PS_BASE_URL_')) {
            define('_PS_BASE_URL_', '');
            define('__PS_BASE_URI__', '');
            define('_PS_BASE_URL_SSL_', '');
        }

        if (!defined('PS_INSTALLATION_IN_PROGRESS')) {
            define('PS_INSTALLATION_IN_PROGRESS', true);
        }

        $this->prophesizeRequest($testedController);
        $testedController->run();
    }

    public function getControllersClasses()
    {
        return array(
            array('AdminCarriersController'),
            array('AdminPreferencesController'),
            array('AdminStatusesController'),
            array('AdminZonesController'),
            array('AdminCurrenciesController'),
            array('AdminLoginController'),
            array('AdminCustomersController'),
            array('AdminCustomerPreferencesController'),
            array('AdminLogsController'),
            array('AdminProfilesController'),
            array('AdminCustomersController'),
            array('AdminMaintenanceController'),
            array('AdminQuickAccessesController'),
            array('AdminCustomerThreadsController'),
            array('AdminManufacturersController'),
            array('AdminReferrersController'),
            array('AdminAdminPreferencesController'),
            array('AdminMetaController'),
            array('AdminAttachmentsController'),
            array('AdminReturnController'),
            array('AdminStoresController'),
            array('AdminEmailsController'),
            array('AdminSuppliersController'),
            array('AdminAttributesGroupsController'),
            array('AdminEmployeesController'),
            array('AdminNotFoundController'),
            array('AdminFeaturesController'),
            array('AdminOrderMessageController'),
            array('AdminSearchEnginesController'),
            array('AdminGendersController'),
            array('AdminOrderPreferencesController'),
            array('AdminShippingController'),
            array('AdminTagsController'),
            array('AdminGeolocationController'),
            array('AdminOrdersController'),
            array('AdminShopController'),
            array('AdminTaxesController'),
            array('AdminCartRulesController'),
            array('AdminGroupsController'),
            array('AdminOutstandingController'),
            array('AdminShopGroupController'),
            array('AdminTaxRulesGroupController'),
            array('AdminCartsController'),
            array('AdminImagesController'),
            array('AdminShopUrlController'),
            array('AdminThemesCatalogController'),
            array('AdminThemesController'),
            array('AdminStatesController'),
            array('AdminLanguagesController'),
            array('AdminStatsController'),
            array('AdminContactsController'),
            array('AdminLegacyLayoutController'),
            array('AdminPPreferencesController'),
        );
    }

    protected function declareRequiredConstants()
    {
        $configDirectory = __DIR__ . '/../../../app/config';
        $configuration = require_once($configDirectory . '/parameters.php');

        if (defined('_PS_BO_ALL_THEMES_DIR_')) {
            return;
        }

        define('_PS_BO_ALL_THEMES_DIR_', '');
        define('_PS_TAB_MODULE_LIST_URL_', '');
        define('_DB_SERVER_', 'localhost');
        define('_DB_USER_', $configuration['parameters']['database_user']);
        define('_DB_PASSWD_', $configuration['parameters']['database_password']);
        define('_DB_NAME_', 'test_'.$configuration['parameters']['database_name']);
        define('_DB_PREFIX_', $configuration['parameters']['database_prefix']);
        define('_COOKIE_KEY_', Tools::passwdGen(56));
        define('_PS_VERSION_', '1.7');
        define('_PS_ADMIN_DIR_', '');
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function prophesizeTranslator()
    {
        return $this->prophesize('\Symfony\Component\Translation\Translator');
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function prophesizeTemplateEngine()
    {
        $templateEngineProphecy = $this->prophesize('\Smarty');

        $templateEngineProphecy->setTemplateDir(Argument::type('array'))->willReturn(null);
        $templateEngineProphecy->assign(Argument::any(), Argument::cetera())->willReturn(null);
        $templateEngineProphecy->fetch(Argument::type('string'), Argument::cetera())->willReturn(null);
        $templateEngineProphecy->getTemplateDir(Argument::any())->willReturn(null);
        $templateEngineProphecy->fetch()->willReturn(null);
        $templateEngineProphecy->createTemplate(Argument::any(), Argument::cetera())->willReturn($templateEngineProphecy);

        return $templateEngineProphecy;
    }

    protected function prophesizeEmployee()
    {
        $employeeProphecy = $this->prophesize('\Employee');
        $employeeProphecy->isLoggedBack()->willReturn(true);
        $employeeProphecy->hasAuthOnShop(Argument::type('string'))->willReturn(true);
        $employeeProphecy->id_profile = 1;

        return $employeeProphecy;
    }

    protected function requireAliasesFunctions()
    {
        require_once(__DIR__ . '/../../../config/alias.php');
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function prophesizeLanguage()
    {
        return $this->prophesize('\Language');
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function prophesizeShop()
    {
        return $this->prophesize('\Shop');
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function prophesizeContainer()
    {
        $containerProphecy = $this->prophesize('\PrestaShop\PrestaShop\Core\Foundation\IoC\Container');

        $entityMapperProphecy = $this->prophesize('\PrestaShop\PrestaShop\Adapter\EntityMapper');
        $entityMapperProphecy->load(Argument::any(), Argument::cetera())->willReturn(null);

        $containerProphecy->make(Argument::type('string'))->willReturn($entityMapperProphecy->reveal());

        return $containerProphecy;
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function prophesizeContext()
    {
        $contextProphecy = $this->prophesize('\Context');

        $translatorProphecy = $this->prophesizeTranslator();
        $contextProphecy->getTranslator()->willReturn($translatorProphecy->reveal());
        $contextProphecy->getDevice()->willReturn(null);

        $templateEngineProphecy = $this->prophesizeTemplateEngine();

        $contextProphecy->smarty = $templateEngineProphecy->reveal();
        $contextProphecy->employee = $this->prophesizeEmployee()->reveal();
        $contextProphecy->language = $this->prophesizeLanguage()->reveal();
        $contextProphecy->shop = $this->prophesizeShop()->reveal();
        $contextProphecy->cookie = $this->prophesizeCookie()->reveal();
        $contextProphecy->link = $this->prophesizeLink()->reveal();

        return $contextProphecy;
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function prophesizeCookie()
    {
        return $this->prophesize('\Cookie');
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function prophesizeLink()
    {
        $linkProphecy = $this->prophesize('\Link');
        $linkProphecy->getAdminLink(Argument::any(), Argument::cetera())->willReturn('/link');

        return $linkProphecy;
    }

    /**
     * @param Controller $testedController
     * @return Tools
     */
    protected function prophesizeRequest(Controller $testedController)
    {
        $requestParameterBagProphecy = $this->prophesize('\Symfony\Component\HttpFoundation\ParameterBag');
        $requestParameterBagProphecy->get(Argument::any(), Argument::cetera())->willReturn($testedController->token);

        $queryParameterBagProphecy = $this->prophesize('\Symfony\Component\HttpFoundation\ParameterBag');
        $queryParameterBagProphecy->get(Argument::any(), Argument::cetera())->willReturn('');

        $requestProphecy = $this->prophesize('\Symfony\Component\HttpFoundation\Request');
        $requestProphecy->request = $requestParameterBagProphecy->reveal();
        $requestProphecy->query = $queryParameterBagProphecy->reveal();

        return new Tools($requestProphecy->reveal());
    }

    public static function tearDownAfterClass() {
        Tools::resetRequest();
    }
}
