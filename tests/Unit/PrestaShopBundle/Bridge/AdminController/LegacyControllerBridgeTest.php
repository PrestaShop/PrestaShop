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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Bridge\AdminController;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use PrestaShopBundle\Bridge\AdminController\LegacyControllerBridge;
use PrestaShopBundle\Security\Admin\Employee as SecurityEmployee;

class LegacyControllerBridgeTest extends TestCase
{
    private const DEFAULT_CSS_FILES_VALUE = [
        'css1.css' => 'all',
        'css2.css' => 'all',
    ];

    private const DEFAULT_JS_FILES_VALUE = [
        'js1.js',
        'js2.js',
    ];

    /**
     * @var ControllerConfiguration
     */
    private $controllerConfiguration;

    /**
     * @var LegacyControllerBridge
     */
    private $legacyBridge;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controllerConfiguration = new ControllerConfiguration(
            $this->mockSecurityUser(),
            42,
            'ObjectModel',
            'AdminController',
            'object',
            '/templates'
        );
        $this->controllerConfiguration->legacyCurrentIndex = 'index.php?controller=AdminFoo';
        $this->controllerConfiguration->positionIdentifierKey = 'id_object';
        $this->controllerConfiguration->token = 'tokenFooBar';
        $this->controllerConfiguration->metaTitle = [1 => 'french title', 2 => 'english title'];
        $this->controllerConfiguration->breadcrumbs = [1 => 'foo', 2 => 'bar'];
        $this->controllerConfiguration->liteDisplay = true;
        $this->controllerConfiguration->displayType = 'edit';
        $this->controllerConfiguration->showPageHeaderToolbar = true;
        $this->controllerConfiguration->pageHeaderToolbarTitle = 'Foo Header';
        $this->controllerConfiguration->pageHeaderToolbarActions = [
            'add_new_foo' => [
                'href' => 'prestashop.com/admin-dev/foo/new',
                'desc' => 'Add new foo',
                'icon' => 'process-icon-new',
            ],
        ];
        $this->controllerConfiguration->toolbarTitle = ['Foo Title'];
        $this->controllerConfiguration->displayHeader = false;
        $this->controllerConfiguration->displayHeaderJavascript = false;
        $this->controllerConfiguration->displayFooter = false;
        $this->controllerConfiguration->bootstrap = false;
        $this->controllerConfiguration->cssFiles = self::DEFAULT_CSS_FILES_VALUE;
        $this->controllerConfiguration->jsFiles = self::DEFAULT_JS_FILES_VALUE;
        $this->controllerConfiguration->errors = [
            'error1',
            'error2',
        ];
        $this->controllerConfiguration->warnings = [
            'warning1',
            'warning2',
        ];
        $this->controllerConfiguration->confirmations = [
            'confirmation1',
            'confirmation2',
        ];
        $this->controllerConfiguration->informations = [
            'information1',
            'information2',
        ];
        $this->controllerConfiguration->json = true;
        $this->controllerConfiguration->template = 'foo_template.tpl';
        $this->controllerConfiguration->templateVars = [
            'foo' => 'var1',
            'bar' => 2,
        ];
        $this->controllerConfiguration->modals = [
            [
                'modal_id' => 'importProgress',
                'modal_class' => 'modal-md',
                'modal_title' => 'Importing your data...',
                'modal_content' => '<div>some html</div>',
            ],
        ];
        $this->controllerConfiguration->multiShopContext = 1;
        $this->controllerConfiguration->multiShopContextGroup = false;

        $multistoreFeature = $this->getMockBuilder(FeatureInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $multistoreFeature
            ->method('isUsed')
            ->willReturn(false)
        ;

        $this->legacyBridge = new LegacyControllerBridge(
            $this->controllerConfiguration,
            $multistoreFeature,
            $this->mockLegacyContext(),
            $this->mockHookDispatcher()
        );
    }

    /**
     * @dataProvider magicGetterValues
     *
     * @param string $propertyName
     * @param mixed $expectedValue
     * @param string $controllerPropertyName
     */
    public function testMagicGetter(string $propertyName, $expectedValue, string $controllerPropertyName): void
    {
        $bridgeValue = $this->legacyBridge->$propertyName;
        $this->assertEquals($expectedValue, $bridgeValue);

        $configurationValue = $this->controllerConfiguration->$controllerPropertyName;
        $this->assertEquals($expectedValue, $configurationValue);
    }

    /**
     * @return iterable
     */
    public function magicGetterValues(): iterable
    {
        yield 'test tabId' => ['id', 42, 'tabId'];
        yield 'test objectModelClassName' => ['className', 'ObjectModel', 'objectModelClassName'];
        yield 'test legacyControllerName' => ['controller_name', 'AdminController', 'legacyControllerName'];
        yield 'test legacyControllerName2' => ['php_self', 'AdminController', 'legacyControllerName'];
        yield 'test metaTitle' => ['meta_title', [1 => 'french title', 2 => 'english title'], 'metaTitle'];
        yield 'test legacyCurrentIndex' => ['current_index', 'index.php?controller=AdminFoo', 'legacyCurrentIndex'];
        yield 'test positionIdentifierKey' => ['position_identifier', 'id_object', 'positionIdentifierKey'];
        yield 'test tableName' => ['table', 'object', 'tableName'];
        yield 'test token' => ['token', 'tokenFooBar', 'token'];
        yield 'test breadcrumbs' => ['breadcrumbs', [1 => 'foo', 2 => 'bar'], 'breadcrumbs'];
        yield 'test liteDisplay' => ['lite_display', true, 'liteDisplay'];
        yield 'test displayType' => ['display', 'edit', 'displayType'];
        yield 'test showPageHeaderToolbar' => ['show_page_header_toolbar', true, 'showPageHeaderToolbar'];
        yield 'test pageHeaderToolbarTitle' => ['page_header_toolbar_title', 'Foo Header', 'pageHeaderToolbarTitle'];
        yield 'test pageHeaderToolbarActions' => [
            'page_header_toolbar_btn',
            [
                'add_new_foo' => [
                    'href' => 'prestashop.com/admin-dev/foo/new',
                    'desc' => 'Add new foo',
                    'icon' => 'process-icon-new',
                ],
            ],
            'pageHeaderToolbarActions',
        ];
        // toolbar_btn and page_header_toolbar_btn seems to be always set the same
        yield 'test toolbar_btn gets pageHeaderToolbarActions' => [
            'toolbar_btn',
            [
                'add_new_foo' => [
                    'href' => 'prestashop.com/admin-dev/foo/new',
                    'desc' => 'Add new foo',
                    'icon' => 'process-icon-new',
                ],
            ],
            'pageHeaderToolbarActions',
        ];
        yield 'test displayHeader' => ['display_header', false, 'displayHeader'];
        yield 'test displayHeaderJavascript' => ['display_header_javascript', false, 'displayHeaderJavascript'];
        yield 'test displayFooter' => ['display_footer', false, 'displayFooter'];
        yield 'test bootstrap' => ['bootstrap', false, 'bootstrap'];
        yield 'test cssFiles' => ['css_files', self::DEFAULT_CSS_FILES_VALUE, 'cssFiles'];
        yield 'test jsFiles' => ['js_files', self::DEFAULT_JS_FILES_VALUE, 'jsFiles'];
        yield 'test templateFolder' => ['tpl_folder', '/templates', 'templateFolder'];
        yield 'test errors' => [
            'errors',
            [
                'error1',
                'error2',
            ],
            'errors',
        ];
        yield 'test warnings' => [
            'warnings',
            [
                'warning1',
                'warning2',
            ],
            'warnings',
        ];
        yield 'test confirmations' => [
            'confirmations',
            [
                'confirmation1',
                'confirmation2',
            ],
            'confirmations',
        ];
        yield 'test informations' => [
            'informations',
            [
                'information1',
                'information2',
            ],
            'informations',
        ];
        yield 'test json' => ['json', true, 'json'];
        yield 'test template' => ['template', 'foo_template.tpl', 'template'];
        yield 'test templateVars' => [
            'tpl_vars',
            [
                'foo' => 'var1',
                'bar' => 2,
            ],
            'templateVars',
        ];
        yield 'test modals' => [
            'modals',
            [
                [
                    'modal_id' => 'importProgress',
                    'modal_class' => 'modal-md',
                    'modal_title' => 'Importing your data...',
                    'modal_content' => '<div>some html</div>',
                ],
            ],
            'modals',
        ];
        yield 'test multiShopContext' => ['multishop_context', 1, 'multiShopContext'];
        yield 'test multiShopContextGroup' => ['multishop_context_group', false, 'multiShopContextGroup'];
    }

    /**
     * @dataProvider magicSetterValues
     *
     * @param string $propertyName
     * @param mixed $expectedValue
     * @param mixed $modifiedValue
     */
    public function testMagicSetter(string $propertyName, $expectedValue, $modifiedValue, string $controllerPropertyName): void
    {
        // Initial value is the right one
        $bridgeValue = $this->legacyBridge->$propertyName;
        $this->assertEquals($expectedValue, $bridgeValue);

        // We modify the value and check that it was correctly updated in bridge
        $this->legacyBridge->$propertyName = $modifiedValue;
        $modifiedBridgeValue = $this->legacyBridge->$propertyName;
        $this->assertEquals($modifiedValue, $modifiedBridgeValue);

        // It must be updated in configuration as well
        $configurationValue = $this->controllerConfiguration->$controllerPropertyName;
        $this->assertEquals($modifiedValue, $configurationValue);
    }

    public function magicSetterValues(): iterable
    {
        yield 'test tabId' => ['id', 42, 51, 'tabId'];
        yield 'test objectModelClassName' => ['className', 'ObjectModel', 'CustomObjectModel', 'objectModelClassName'];
        yield 'test legacyControllerName' => ['controller_name', 'AdminController', 'AdminFooController', 'legacyControllerName'];
        yield 'test legacyControllerName2' => ['php_self', 'AdminController', 'AdminBarController', 'legacyControllerName'];
        yield 'test metaTitle' => ['meta_title', [1 => 'french title', 2 => 'english title'], [3 => 'ukrainian title', 4 => 'japan title'], 'metaTitle'];
        yield 'test legacyCurrentIndex' => ['current_index', 'index.php?controller=AdminFoo', 'index.php?controller=AdminBar', 'legacyCurrentIndex'];
        yield 'test positionIdentifierKey' => ['position_identifier', 'id_object', 'id', 'positionIdentifierKey'];
        yield 'test tableName' => ['table', 'object', 'foo', 'tableName'];
        yield 'test token' => ['token', 'tokenFooBar', 'tokenBarBar', 'token'];
        yield 'test breadcrumbs' => ['breadcrumbs', [1 => 'foo', 2 => 'bar'], [1 => 'Home', 2 => 'Accessories'], 'breadcrumbs'];
        yield 'test liteDisplay' => ['lite_display', true, false, 'liteDisplay'];
        yield 'test displayType' => ['display', 'edit', 'view', 'displayType'];
        yield 'test showPageHeaderToolbar' => ['show_page_header_toolbar', true, false, 'showPageHeaderToolbar'];
        yield 'test pageHeaderToolbarTitle' => ['page_header_toolbar_title', 'Foo Header', 'bar header', 'pageHeaderToolbarTitle'];
        yield 'test pageHeaderToolbarActions' => [
            'page_header_toolbar_btn',
            [
                'add_new_foo' => [
                    'href' => 'prestashop.com/admin-dev/foo/new',
                    'desc' => 'Add new foo',
                    'icon' => 'process-icon-new',
                ],
            ],
            [
                'delete_foo' => [
                    'href' => 'prestashop.com/admin-dev/foo/delete',
                    'desc' => 'Delete foo',
                    'icon' => 'icon-trash',
                ],
            ],
            'pageHeaderToolbarActions',
        ];
        yield 'test toolbarButtons' => [
            'toolbar_btn',
            [
                'add_new_foo' => [
                    'href' => 'prestashop.com/admin-dev/foo/new',
                    'desc' => 'Add new foo',
                    'icon' => 'process-icon-new',
                ],
            ],
            [
                'delete_foo' => [
                    'href' => 'prestashop.com/admin-dev/foo/delete',
                    'desc' => 'Delete foo',
                    'icon' => 'icon-trash',
                ],
            ],
            'pageHeaderToolbarActions',
        ];
        yield 'test displayHeader' => ['display_header', false, true, 'displayHeader'];
        yield 'test displayHeaderJavascript' => ['display_header_javascript', false, true, 'displayHeaderJavascript'];
        yield 'test displayFooter' => ['display_footer', false, true, 'displayFooter'];
        yield 'test bootstrap' => ['bootstrap', false, true, 'bootstrap'];
        yield 'test cssFiles' => [
            'css_files',
            self::DEFAULT_CSS_FILES_VALUE,
            [
                'css3.css',
                'css4.css',
            ],
            'cssFiles',
        ];
        yield 'test jsFiles' => [
            'js_files',
            self::DEFAULT_JS_FILES_VALUE,
            [
                'js3.js',
                'js4.js',
            ],
            'jsFiles',
        ];
        yield 'test templateFolder' => ['tpl_folder', '/templates', 'custom/templates', 'templateFolder'];
        yield 'test errors' => [
            'errors',
            [
                'error1',
                'error2',
            ],
            [
                'error3',
                'error4',
            ],
            'errors',
        ];
        yield 'test warnings' => [
            'warnings',
            [
                'warning1',
                'warning2',
            ],
            [
                'warning3',
                'warning4',
            ],
            'warnings',
        ];
        yield 'test confirmations' => [
            'confirmations',
            [
                'confirmation1',
                'confirmation2',
            ],
            [
                'confirmation3',
                'confirmation4',
            ],
            'confirmations',
        ];
        yield 'test informations' => [
            'informations',
            [
                'information1',
                'information2',
            ],
            [
                'information3',
                'information4',
            ],
            'informations',
        ];
        yield 'test json' => ['json', true, false, 'json'];
        yield 'test template' => ['template', 'foo_template.tpl', 'bar_tpl.tpl', 'template'];
        yield 'test templateVars' => [
            'tpl_vars',
            [
                'foo' => 'var1',
                'bar' => 2,
            ],
            [
                'value' => 'value1',
            ],
            'templateVars',
        ];
        yield 'test modals' => [
            'modals',
            [
                [
                    'modal_id' => 'importProgress',
                    'modal_class' => 'modal-md',
                    'modal_title' => 'Importing your data...',
                    'modal_content' => '<div>some html</div>',
                ],
            ],
            [
                [
                    'modal_id' => 'importProgress',
                    'modal_class' => 'modal-md',
                    'modal_title' => 'Importing your data...',
                    'modal_content' => '<div>some html</div>',
                ],
                [
                    'modal_id' => 'importError',
                    'modal_class' => 'modal-md',
                    'modal_title' => 'Error occurred',
                    'modal_content' => '<div>Error</div>',
                ],
            ],
            'modals',
        ];
        yield 'test multiShopContext' => ['multishop_context', 1, 2, 'multiShopContext'];
        yield 'test multiShopContextGroup' => ['multishop_context_group', false, true, 'multiShopContextGroup'];
    }

    public function testAssociativeArray(): void
    {
        $this->assertIsArray($this->legacyBridge->toolbar_btn);
        // Reset to an empty array to have clean state for both values before checking
        $this->legacyBridge->toolbar_btn = [];
        $this->assertEmpty($this->legacyBridge->toolbar_btn);

        // First init the bridge with same array value, so both values should be equal
        $newProduct = ['href' => 'http://product.com', 'text' => 'New Product'];
        $toolbarButtons = ['new_product' => $newProduct];
        $this->legacyBridge->toolbar_btn = $toolbarButtons;
        $this->assertEquals($toolbarButtons, $this->legacyBridge->toolbar_btn);

        // Then we access a sub element from both arrays
        $this->assertEquals($newProduct, $toolbarButtons['new_product']);
        $this->assertEquals($newProduct, $this->legacyBridge->toolbar_btn['new_product']);

        // Add the same element at the same key in both local array and bridge magic array
        $newCategory = ['href' => 'http://category.com', 'text' => 'New Category'];
        $toolbarButtons['new_category'] = $newCategory;
        $this->legacyBridge->toolbar_btn['new_category'] = $newCategory;
        $this->assertEquals($toolbarButtons, $this->legacyBridge->toolbar_btn);

        // We can even modify an element in a sub element
        $toolbarButtons['new_category']['href'] = 'http://category.org';
        $this->legacyBridge->toolbar_btn['new_category']['href'] = 'http://category.org';
        $this->assertEquals($toolbarButtons, $this->legacyBridge->toolbar_btn);
    }

    public function testIntegerArray(): void
    {
        // Reset to an empty array to have clean state for both values before checking
        $this->legacyBridge->toolbar_title = [];

        $this->legacyBridge->toolbar_title[] = 'Product';
        $this->assertEquals(['Product'], $this->legacyBridge->toolbar_title);
        $this->assertEquals(['Product'], $this->controllerConfiguration->toolbarTitle);

        $this->legacyBridge->toolbar_title[] = 'Edit';
        $this->assertEquals(['Product', 'Edit'], $this->legacyBridge->toolbar_title);
        $this->assertEquals(['Product', 'Edit'], $this->controllerConfiguration->toolbarTitle);
    }

    public function testAddCss(): void
    {
        $this->assertSame(self::DEFAULT_CSS_FILES_VALUE, $this->legacyBridge->css_files);
        // checkPath = false to avoid loading Media::getCssPath inside
        $this->legacyBridge->addCSS('/themes/css/custom.css', 'all', null, false);

        $expectedValue = array_merge(self::DEFAULT_CSS_FILES_VALUE, ['/themes/css/custom.css' => 'all']);
        $this->assertSame(
            $expectedValue,
            $this->legacyBridge->css_files
        );

        $this->legacyBridge->addCSS('/themes/css/custom1.css', 'all', 0, false);
        // provided offset 0, so we expect that new file is added to the top of the array
        $this->assertSame(
            array_merge(['/themes/css/custom1.css' => 'all'], $expectedValue),
            $this->legacyBridge->css_files
        );
    }

    public function testAddJs(): void
    {
        $this->assertSame(self::DEFAULT_JS_FILES_VALUE, $this->legacyBridge->js_files);
        // checkPath = false to avoid loading Media::getJsPath inside
        $this->legacyBridge->addJS('/themes/js/custom.js', false);

        $this->assertSame(
            array_merge(self::DEFAULT_JS_FILES_VALUE, ['/themes/js/custom.js']),
            $this->legacyBridge->js_files
        );
    }

    /**
     * @return SecurityEmployee
     */
    private function mockSecurityUser(): SecurityEmployee
    {
        return $this
            ->getMockBuilder(SecurityEmployee::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    /**
     * @return LegacyContext
     */
    private function mockLegacyContext(): LegacyContext
    {
        return $this
            ->getMockBuilder(LegacyContext::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    /**
     * @return HookDispatcherInterface
     */
    private function mockHookDispatcher(): HookDispatcherInterface
    {
        return $this->getMockBuilder(HookDispatcherInterface::class)->getMock();
    }
}
