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

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\Billing;

final class ValidDataProviderForCommandCreation
{
    public static function getData(): array
    {
        return [
            'localized_names' => [1 => 'My carrier'],
            'localized_delays' => [1 => 'pickup in store'],
            'speed_grade' => 0,
            'tracking_url' => 'http://example.com/track.php?num=@',
            'shipping_cost_included' => true,
            'billing' => Billing::ACCORDING_TO_PRICE,
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
            'module_name' => 'myCarrierModule',
            'needs_core_shipping_price' => true,
        ];
    }
}
