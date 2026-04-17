<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

    /**
     * @param Cart $cart
     *
     * @return string cart SHA1
     */
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
                . $this->subseparator
                . $product['id_product']
                . $this->subseparator
                . $product['id_product_attribute']
                . $this->subseparator
                . $product['cart_quantity']
                . $this->subseparator
                . $product['total_wt'];
            $uniq_id .= $this->separator;
        }

        $uniq_id = rtrim($uniq_id, $this->separator);
        $uniq_id = rtrim($uniq_id, $this->subseparator);

        return sha1($uniq_id);
    }
}
