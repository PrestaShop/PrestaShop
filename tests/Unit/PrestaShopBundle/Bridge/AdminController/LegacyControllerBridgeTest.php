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

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Bridge\AdminController;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use PrestaShopBundle\Bridge\AdminController\LegacyControllerBridge;

class LegacyControllerBridgeTest extends TestCase
{
    private const CONTROLLER_MAPPING = [
        'id' => 'controllerConfiguration.tabId',
        'className' => 'controllerConfiguration.objectModelClassName',
        'controller_name' => 'controllerConfiguration.legacyControllerName',
        'php_self' => 'controllerConfiguration.legacyControllerName',
        'current_index' => 'controllerConfiguration.legacyCurrentIndex',
        'position_identifier' => 'controllerConfiguration.positionIdentifier',
        'table' => 'controllerConfiguration.tableName',
        'token' => 'controllerConfiguration.token',
        'meta_title' => 'controllerConfiguration.metaTitle',
        'breadcrumbs' => 'controllerConfiguration.breadcrumbs',
        'lite_display' => 'controllerConfiguration.liteDisplay',
        'display' => 'controllerConfiguration.display',
        'show_page_header_toolbar' => 'controllerConfiguration.showPageHeaderToolbar',
        'page_header_toolbar_title' => 'controllerConfiguration.pageHeaderToolbarTitle',
        'toolbar_btn' => 'controllerConfiguration.pageHeaderToolbarButton',
        'toolbar_title' => 'controllerConfiguration.toolbarTitle',
        'display_header' => 'controllerConfiguration.displayHeader',
        'display_header_javascript' => 'controllerConfiguration.displayHeaderJavascript',
        'display_footer' => 'controllerConfiguration.displayFooter',
        'bootstrap' => 'controllerConfiguration.bootstrap',
        'css_files' => 'controllerConfiguration.cssFiles',
        'js_files' => 'controllerConfiguration.jsFiles',
        'tpl_folder' => 'controllerConfiguration.templateFolder',
        'errors' => 'controllerConfiguration.errors',
        'warnings' => 'controllerConfiguration.warnings',
        'confirmations' => 'controllerConfiguration.confirmations',
        'informations' => 'controllerConfiguration.informations',
        'json' => 'controllerConfiguration.json',
        'template' => 'controllerConfiguration.template',
        'tpl_vars' => 'controllerConfiguration.templateVars',
        'modals' => 'controllerConfiguration.modals',
        'multishop_context' => 'controllerConfiguration.multiShopContext',
        'multishop_context_group' => 'controllerConfiguration.multiShopContextGroup',
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
        $this->controllerConfiguration = new ControllerConfiguration();
        $this->controllerConfiguration->tabId = 42;
        $this->controllerConfiguration->objectModelClassName = 'ObjectModel';
        $this->controllerConfiguration->metaTitle = [1 => 'french title', 2 => 'english title'];

        $multistoreFeature = $this->getMockBuilder(FeatureInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $multistoreFeature
            ->method('isUsed')
            ->willReturn(false)
        ;

        $this->legacyBridge = new LegacyControllerBridge($this->controllerConfiguration, self::CONTROLLER_MAPPING, $multistoreFeature);
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

    public function magicGetterValues(): iterable
    {
        yield 'test tabId' => ['id', 42, 'tabId'];
        yield 'test objectModelClassName' => ['className', 'ObjectModel', 'objectModelClassName'];
        yield 'test metaTitle' => ['meta_title', [1 => 'french title', 2 => 'english title'], 'metaTitle'];
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
        yield 'test metaTitle' => ['meta_title', [1 => 'french title', 2 => 'english title'], [3 => 'ukrainian title', 4 => 'japan title'], 'metaTitle'];
    }

    public function testAssociativeArray(): void
    {
        $this->assertIsArray($this->legacyBridge->toolbar_btn);
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
        $this->assertEmpty($this->legacyBridge->toolbar_title);

        $this->legacyBridge->toolbar_title[] = 'Product';
        $this->assertEquals(['Product'], $this->legacyBridge->toolbar_title);
        $this->assertEquals(['Product'], $this->controllerConfiguration->toolbarTitle);

        $this->legacyBridge->toolbar_title[] = 'Edit';
        $this->assertEquals(['Product', 'Edit'], $this->legacyBridge->toolbar_title);
        $this->assertEquals(['Product', 'Edit'], $this->controllerConfiguration->toolbarTitle);
    }
}
