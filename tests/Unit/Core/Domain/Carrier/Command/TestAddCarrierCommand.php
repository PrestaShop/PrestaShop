<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Domain\Carrier\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\AddCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;

class TestAddCarrierCommand extends TestCase
{
    public function testCommandIsCreatedSuccessfullyWhenValidArgumentsAreGiven()
    {
        $this->createCommandFromArray($this->getValidDataForCommandCreation());
    }

    /**
     * @dataProvider getInvalidMeasures
     *
     * @param int $width
     * @param int $height
     * @param int $depth
     * @param float $weight
     */
    public function testItThrowsExceptionWhenInvalidMeasuresAreGiven(int $width, int $height, int $depth, float $weight)
    {
        $this->expectException(CarrierConstraintException::class);
        $this->expectExceptionCode(CarrierConstraintException::INVALID_PACKAGE_MEASURE);
        $data = $this->getValidDataForCommandCreation();
        $data['width'] = $width;
        $data['height'] = $height;
        $data['depth'] = $depth;
        $data['weight'] = $weight;

        $this->createCommandFromArray($data);
    }

    public function getInvalidMeasures()
    {
        yield [-1, 0, 0, 0.0];
        yield [0, -1, 0, 0.0];
        yield [0, 0, -1, 0.0];
        yield [0, 0, 0, -0.1];
        yield [-1, -2, -3, -5.5];
    }

    private function getValidDataForCommandCreation()
    {
        return [
            'localized_names' => [1 => 'My carrier'],
            'localized_delays' => [1 => 'pickup in store'],
            'speed_grade' => 0,
            'tracking_url' => 'http://example.com/track.php?num=@',
            'shipping_cost_included' => true,
            'shipping_method' => ShippingMethod::SHIPPING_METHOD_PRICE,
            'tax_rules_group' => 1,
            'out_of_range_behavior' => OutOfRangeBehavior::APPLY_HIGHEST_RANGE,
            'shipping_ranges' => [
                [
                    'from' => 1,
                    'to' => 2,
                    'prices_by_zone_id' => [
                        3 => 1,
                        4 => 2,
                    ],
                ],
                [
                    'from' => 2,
                    'to' => 3,
                    'prices_by_zone_id' => [
                        3 => 2,
                        5 => 1,
                    ],
                ],
            ],
            'width' => 15,
            'height' => 10,
            'depth' => 20,
            'weight' => 20.1,
            'associated_group_ids' => [1, 2],
            'associated_shop_ids' => [1],
        ];
    }

    private function createCommandFromArray(array $data)
    {
        return new AddCarrierCommand(
            $data['localized_names'],
            $data['localized_delays'],
            $data['speed_grade'],
            $data['tracking_url'],
            $data['shipping_cost_included'],
            $data['shipping_method'],
            $data['tax_rules_group'],
            $data['out_of_range_behavior'],
            $data['shipping_ranges'],
            $data['width'],
            $data['height'],
            $data['depth'],
            $data['weight'],
            $data['associated_group_ids'],
            $data['associated_shop_ids']
        );
    }
}
