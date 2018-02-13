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

class CartChecksumCore implements ChecksumInterface
{
    public $addressChecksum = null;
    private $separator = '_';
    private $subseparator = '-';

    public function __construct(AddressChecksum $addressChecksum)
    {
        $this->addressChecksum = $addressChecksum;
    }

    public function generateChecksum($cart)
    {
        $uniq_id = '';
        $uniq_id .= $cart->id_shop;
        $uniq_id .= $this->separator;
        $uniq_id .= $cart->id_customer;
        $uniq_id .= $this->separator;
        $uniq_id .= $cart->id_guest;
        $uniq_id .= $this->separator;
        $uniq_id .= $cart->id_currency;
        $uniq_id .= $this->separator;
        $uniq_id .= $cart->id_lang;
        $uniq_id .= $this->separator;

        $uniq_id .= $this->addressChecksum->generateChecksum(new Address($cart->id_address_delivery));
        $uniq_id .= $this->separator;
        $uniq_id .= $this->addressChecksum->generateChecksum(new Address($cart->id_address_invoice));
        $uniq_id .= $this->separator;

        $products = $cart->getProducts($refresh = true);
        foreach ($products as $product) {
            $uniq_id .= $product['id_shop']
                .$this->subseparator
                .$product['id_product']
                .$this->subseparator
                .$product['id_product_attribute']
                .$this->subseparator
                .$product['cart_quantity']
                .$this->subseparator
                .$product['total_wt'];
            $uniq_id .= $this->separator;
        }

        $uniq_id = rtrim($uniq_id, $this->separator);
        $uniq_id = rtrim($uniq_id, $this->subseparator);

        return sha1($uniq_id);
    }
}
