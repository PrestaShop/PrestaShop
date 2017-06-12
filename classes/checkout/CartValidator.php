<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class CartValidatorCore
 */
class CartValidatorCore
{
    /** @var Context  */
    private $context;
    /** @var Cart  */
    private $cart;


    /**
     * CartValidatorCore constructor.
     * @param $context
     */
    public function __construct(Context $context = null)
    {
        $this->context = $context;
        $this->cart = $context->cart;
    }

    /**
     * Check the cart if has valid addresses
     * Return an array of invalid address ID in case invalid address
     *
     * @param Cart $cart
     * @return bool|array
     */
    public function hasValidAddresses($cart = null)
    {
        $cart = empty($cart) ? $this->cart : $cart;

        if (!($cart instanceof Cart)) {
            return false;
        }

        $addressesIds = array(
            $cart->id_address_delivery,
            $cart->id_address_invoice,
        );

        foreach ($addressesIds as $idAddress) {
            $address = new Address($idAddress);
            try {
                $address->getFields();
            } catch (PrestaShopException $e) {
                return array($idAddress);
            }
        }

        return true;
    }

    /**
     * Return the list of invalid customer addresses
     *
     * @param Context $context
     * @return array|bool
     */
    public function getInvalidAdressesIds($context = null)
    {
        $context = empty($context) ? $this->context : $context;

        if (!($context instanceof Context)) {
            return false;
        }

        $invalidAddresses = array();
        $addresses = $context->customer->getAddresses($context->language->id);

        if (is_array($addresses)) {
            foreach ($addresses as $address) {
                try {
                    $adressObject = new Address($address['id_address']);
                    $adressObject->getFields();
                } catch (PrestaShopException $e) {
                    $invalidAddresses[] = $address['id_address'];
                }
            }
        }

        return $invalidAddresses;
    }
}
