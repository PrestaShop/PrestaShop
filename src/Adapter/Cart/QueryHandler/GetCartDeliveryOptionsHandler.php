<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Cart\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartDeliveryOptions;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetCartDeliveryOptionsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartDeliveryOption;

/**
 * Handles GetCartDeliveryOptions query using legacy object model
 */
final class GetCartDeliveryOptionsHandler extends AbstractCartHandler implements GetCartDeliveryOptionsHandlerInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param int $contextLangId
     */
    public function __construct(int $contextLangId)
    {
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCartDeliveryOptions $query): array
    {
        $cartId = $query->getCartId();

        $cart = $this->getCart($cartId);
        $deliveryAddressId = (int) $cart->id_address_delivery;
        $deliveryOptionsByAddress = $cart->getDeliveryOptionList();

        //Check if there is any delivery options available for cart delivery address
        if (array_key_exists($deliveryAddressId, $deliveryOptionsByAddress)) {
            return [];
        }

        return $this->fetchCartDeliveryOptions($deliveryOptionsByAddress, $deliveryAddressId);
    }

    /**
     * Fetch CartDeliveryOption[] DTO's from legacy array
     *
     * @param array $deliveryOptionsByAddress
     * @param int $deliveryAddressId
     *
     * @return array
     */
    private function fetchCartDeliveryOptions(array $deliveryOptionsByAddress, int $deliveryAddressId)
    {
        $deliveryOptions = [];
        // legacy multishipping feature allowed to split cart shipping to multiple addresses.
        // now when the multishipping feature is removed
        // the list of carriers should be shared across whole cart for single delivery address
        foreach ($deliveryOptionsByAddress[$deliveryAddressId] as $deliveryOption) {
            foreach ($deliveryOption['carrier_list'] as $carrier) {
                $carrier = $carrier['instance'];
                // make sure there is no duplicate carrier
                $deliveryOptions[(int) $carrier->id] = new CartDeliveryOption(
                    (int) $carrier->id,
                    $carrier->name,
                    $carrier->delay[$this->contextLangId]
                );
            }
        }

        //make sure array is not associative
        return array_values($deliveryOptions);
    }
}
