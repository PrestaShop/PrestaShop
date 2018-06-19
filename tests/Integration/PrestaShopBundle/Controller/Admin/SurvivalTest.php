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
 */
class SurvivalTest extends WebTestCase
{
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
        self::assertTrue($response->isSuccessful(),
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
            'admin_customer_preferences' => ['Customer preferences', 'admin_customer_preferences'],
        ];
    }
}
