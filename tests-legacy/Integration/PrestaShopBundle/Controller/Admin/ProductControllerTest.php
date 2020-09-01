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

namespace LegacyTests\Integration\PrestaShopBundle\Controller\Admin;

use LegacyTests\Integration\PrestaShopBundle\Test\WebTestCase;

/**
 * @group demo
 */
class ProductControllerTest extends WebTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->enableDemoMode();
    }

    /**
     * @dataProvider getUnitActions
     *
     * @param $action
     */
    public function testUnitAction($action)
    {
        $actionUrl = $this->router->generate('admin_product_unit_action', array(
            'action' => $action,
            'id' => 1,
        ));
        $this->client->request('POST', $actionUrl);
        $this->assertSessionFlagBagContainsFailureMessage();
    }

    public function getUnitActions()
    {
        return array(
            ['delete'],
            ['duplicate'],
            ['activate'],
            ['deactivate'],
        );
    }

    /**
     * @dataProvider getBulkActions
     *
     * @param $action
     */
    public function testBulkAction($action)
    {
        $actionUrl = $this->router->generate('admin_product_bulk_action', array(
            'action' => $action,
        ));
        $this->client->request('POST', $actionUrl);
        $this->assertSessionFlagBagContainsFailureMessage();
    }

    public function getBulkActions()
    {
        return array(
            ['activate_all'],
            ['deactivate_all'],
            ['duplicate_all'],
            ['delete_all'],
        );
    }

    protected function assertSessionFlagBagContainsFailureMessage()
    {
        $all = self::$kernel->getContainer()->get('session')->getFlashBag()->all();
        $this->assertArrayHasKey('failure', $all);
    }
}
