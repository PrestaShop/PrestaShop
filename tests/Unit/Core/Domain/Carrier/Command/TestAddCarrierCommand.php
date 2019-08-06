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

class TestAddCarrierCommand extends TestCase
{
    public function testAddCarrierCommandWithPricedShippingCanBeCreated()
    {
        $this->createCommandFromArray(ValidDataProviderForCommandCreation::getData(), false);
    }

    public function testAddCarrierCommandWithFreeShippingCanBeCreated()
    {
        $this->createCommandFromArray(ValidDataProviderForCommandCreation::getData(), true);
    }

    /**
     * @dataProvider getInvalidRanges
     */
    public function testItThrowsExceptionWhenInvalidRangesAreGivenForCommandCreationWithPricedShipping($invalidRange)
    {
        $this->expectException(CarrierConstraintException::class);
        $this->expectExceptionCode(CarrierConstraintException::INVALID_SHIPPING_RANGE);
        $data = ValidDataProviderForCommandCreation::getData();
        $data['shipping_ranges'] = $invalidRange;

        $this->createCommandFromArray($data, false);
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

        $data = ValidDataProviderForCommandCreation::getData();
        $data['width'] = $width;
        $data['height'] = $height;
        $data['depth'] = $depth;
        $data['weight'] = $weight;

        $this->createCommandFromArray($data, true);
    }

    public function getInvalidMeasures()
    {
        yield [-1, 0, 0, 0.0];
        yield [0, -1, 0, 0.0];
        yield [0, 0, -1, 0.0];
        yield [0, 0, 0, -0.1];
        yield [-1, -2, -3, -5.5];
    }

    public function getInvalidRanges()
    {
        yield [[]];
        yield [[
            'assoc' => [
                'from' => 1,
                'to' => 2,
                'prices_by_zone_id' => [
                    3 => 1,
                    4 => 2,
                ],
            ],
        ]];
        yield [[
            [
                'to' => 2,
                'prices_by_zone_id' => [
                    3 => 1,
                    4 => 2,
                ],
            ],
        ]];
        yield [[
            [
                'from' => 2,
                'prices_by_zone_id' => [
                    3 => 2,
                    5 => 1,
                ],
            ],
        ]];
        yield [[
            [
                'from' => 1,
                'to' => 2,
            ],
        ]];
        yield [[
            [
                'from' => 2,
                'to' => 3,
                'prices_by_zone_id' => [],
            ],
        ]];
        yield [[
            [
                'from' => 1,
                'to' => 3,
                'prices_by_zone_id' => [
                    1 => 3,
                    4 => 5,
                ],
            ],
            [
                'from' => 2,
                'to' => 5,
                'prices_by_zone_id' => [
                    1 => 2,
                    4 => 5,
                ],
            ],
        ]];
    }

    private function createCommandFromArray(array $data, bool $freeShipping)
    {
        if ($freeShipping) {
            $command = AddCarrierCommand::withFreeShipping(
                $data['localized_names'],
                $data['localized_delays'],
                $data['speed_grade'],
                $data['tracking_url'],
                $data['tax_rules_group'],
                $data['width'],
                $data['height'],
                $data['depth'],
                $data['weight'],
                $data['associated_group_ids'],
                $data['associated_shop_ids']
            );
        } else {
            $command = AddCarrierCommand::withPricedShipping(
                $data['localized_names'],
                $data['localized_delays'],
                $data['speed_grade'],
                $data['tracking_url'],
                $data['shipping_cost_included'],
                $data['billing'],
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

        return $command;
    }
}
