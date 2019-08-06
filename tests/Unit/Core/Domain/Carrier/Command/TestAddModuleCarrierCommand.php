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
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\AddModuleCarrierCommand;

class TestAddModuleCarrierCommand extends TestCase
{
    public function testAddModuleCarrierCommandWithCoreShippingPriceCanBeCreated()
    {
        $this->createCommandWithCoreShippingPriceFromArray(ValidDataProviderForCommandCreation::getData());
    }

    public function testAddModuleCarrierCommandWithModuleShippingPriceCanBeCreated()
    {
        $this->createCommandWithModuleShippingPriceFromArray(ValidDataProviderForCommandCreation::getData());
    }

    public function testAddModuleCarrierCommandWithFreeShippingCanBeCreated()
    {
        $this->createCommandWithFreeShippingFromArray(ValidDataProviderForCommandCreation::getData());
    }

    private function createCommandWithCoreShippingPriceFromArray(array $data)
    {
        AddModuleCarrierCommand::withCoreShippingPrice(
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
            $data['associated_shop_ids'],
            $data['module_name']
        );
    }

    private function createCommandWithModuleShippingPriceFromArray(array $data)
    {
        AddModuleCarrierCommand::withModuleShippingPrice(
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
            $data['associated_shop_ids'],
            $data['module_name'],
            $data['needs_core_shipping_price']
        );
    }

    private function createCommandWithFreeShippingFromArray(array $data)
    {
        AddModuleCarrierCommand::withFreeShipping(
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
            $data['associated_shop_ids'],
            $data['module_name']
        );
    }
}
