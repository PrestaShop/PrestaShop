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

namespace Tests\Unit\PrestaShopBundle\Routing\Converter;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Routing\Converter\LegacyParametersConverter;

class LegacyParametersConverterTest extends TestCase
{
    /**
     * @dataProvider getExpectedLegacyParameters
     *
     * @param array $requestAttributes
     * @param array $queryParameters
     * @param array|null $expectedLegacyParameters
     */
    public function testGetParameters(array $requestAttributes, array $queryParameters, ?array $expectedLegacyParameters)
    {
        $converter = new LegacyParametersConverter();
        $legacyParameters = $converter->getParameters($requestAttributes, $queryParameters);
        $this->assertEquals($expectedLegacyParameters, $legacyParameters);
    }

    public function getExpectedLegacyParameters()
    {
        return [
            [
                [],
                [],
                null,
            ],
            // Simple controller without action
            [
                ['_legacy_link' => 'AdminOrders'],
                [],
                ['controller' => 'AdminOrders'],
            ],
            // Controller and action
            [
                ['_legacy_link' => 'AdminOrders:vieworder'],
                [],
                ['controller' => 'AdminOrders', 'action' => 'vieworder'],
            ],
            // Multiple legacy links, the first one is used
            [
                ['_legacy_link' => ['AdminOrders', 'AdminUnknown:list', 'AdminOrders:index']],
                [],
                ['controller' => 'AdminOrders', 'action' => 'list'],
            ],
            // Multiple legacy links, the first one is used
            [
                ['_legacy_link' => ['AdminOrders', 'AdminUnknown:index', 'AdminOrders:list']],
                [],
                ['controller' => 'AdminOrders', 'action' => 'index'],
            ],
            // Legacy parameters defined and matches attribute
            [
                ['_legacy_link' => 'AdminOrders:vieworder', '_legacy_parameters' => ['id_order' => 'orderId'], 'orderId' => 42],
                [],
                ['controller' => 'AdminOrders', 'action' => 'vieworder', 'id_order' => 42],
            ],
            // Legacy parameters defined, attributes has priority over query parameter
            [
                ['_legacy_link' => 'AdminOrders:vieworder', '_legacy_parameters' => ['id_order' => 'orderId'], 'orderId' => 42],
                ['orderId' => 51],
                ['controller' => 'AdminOrders', 'action' => 'vieworder', 'id_order' => 42],
            ],
            [
                // Legacy parameters defined, query parameter is used as a fallback
                ['_legacy_link' => 'AdminOrders:vieworder', '_legacy_parameters' => ['id_order' => 'orderId']],
                ['orderId' => 51],
                ['controller' => 'AdminOrders', 'action' => 'vieworder', 'id_order' => 51],
            ],
        ];
    }
}
