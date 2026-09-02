<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
