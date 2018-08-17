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

namespace Tests\Integration\PrestaShopBundle\Controller\Admin;

use Tests\Integration\PrestaShopBundle\Test\WebTestCase;

/**
 * @group demo
 *
 * To execute these tests: use "./vendor/bin/phpunit -c tests/phpunit-admin.xml --filter=SurvivalTest" command.
 */
class SurvivalTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        // Do not reset the Database.
    }

    /**
     * @dataProvider getDataProvider
     * @param $pageName
     * @param $route
     */
    public function testPagesAreAvailable($pageName, $route)
    {
        $this->client->request(
            'GET',
            $this->router->generate(
                $route
            )
        );

        $response = $this->client->getResponse();

        /**
         * If you need to debug these HTTP calls, you can use:
         * if ($response->isServerError()) {
         *    $content = $response->getContent();
         * }
         */
        self::assertTrue(
            $response->isSuccessful(),
            sprintf(
                '%s page should be available, but status code is %s',
                $pageName,
                $response->getStatusCode()
            )
        );
    }

    /**
     * @return array contains data to be tested:
     * - the overridden pages
     * - with the pages content
     * - and the route name for each page.
     */
    public function getDataProvider()
    {
        return [
            'administration_page' => ['Administration', 'admin_administration'],
            'admin_performance' => ['Performance', 'admin_performance'],
            'admin_import' => ['Import', 'admin_import'],
            'admin_preferences' => ['Preferences', 'admin_preferences'],
            'admin_order_preferences' => ['Order Preferences', 'admin_order_preferences'],
            'admin_maintenance' => ['Maintenance', 'admin_maintenance'],
            'admin_product_preferences' => ['Product Preferences', 'admin_product_preferences'],
            'admin_customer_preferences' => ['Customer Preferences', 'admin_customer_preferences'],
            'admin_order_delivery_slip' => ['Delivery Slips', 'admin_order_delivery_slip'],
            // @todo: why these tests are failing when pages are available?
            // 'admin_system_information' => ['Information', 'admin_system_information'],
            // 'admin_international_translation_overview' => ['Translations', 'admin_international_translation_overview'],
            // 'admin_theme_catalog' => ['Themes Catalog', 'admin_theme_catalog'],
            'admin_module_catalog' => ['Module selection', 'admin_module_catalog'],
            'admin_module_notification' => ['Module notifications', 'admin_module_notification'],
            'admin_module_manage' => ['Manage installed modules', 'admin_module_manage'],
            'admin_shipping_preferences' => ['Shipping Preferences', 'admin_shipping_preferences'],
            'admin_payment_methods' => ['Payment Methods', 'admin_payment_methods'],
            'admin_geolocation' => ['Geolocation', 'admin_geolocation'],
            'admin_localization_show_settings' => ['Localization', 'admin_localization_show_settings'],
            'admin_payment_preferences' => ['Payment preferences', 'admin_payment_preferences'],
        ];
    }
}
