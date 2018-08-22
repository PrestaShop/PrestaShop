<?php
/**
 * 2007-2018 PrestaShop.
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
class OrderCarrierCore extends ObjectModel
{
    /** @var int */
    public $id_order_carrier;

    /** @var int */
    public $id_order;

    /** @var int */
    public $id_carrier;

    /** @var int */
    public $id_order_invoice;

    /** @var float */
    public $weight;

    /** @var float */
    public $shipping_cost_tax_excl;

    /** @var float */
    public $shipping_cost_tax_incl;

    /** @var string */
    public $tracking_number;

    /** @var string Object creation date */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_carrier',
        'primary' => 'id_order_carrier',
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_invoice' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'weight' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'shipping_cost_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'shipping_cost_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'tracking_number' => array('type' => self::TYPE_STRING, 'validate' => 'isTrackingNumber'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    protected $webserviceParameters = array(
        'objectMethods' => array('update' => 'updateWs'),
        'fields' => array(
            'id_order' => array('xlink_resource' => 'orders'),
            'id_carrier' => array('xlink_resource' => 'carriers'),
        ),
    );

    /**
     * @param Order $order Required
     *
     * @return bool
     */
    public function sendInTransitEmail($order)
    {
        $customer = new Customer((int) $order->id_customer);
        $carrier = new Carrier((int) $order->id_carrier, $order->id_lang);
        $address = new Address((int) $order->id_address_delivery);

        if (!Validate::isLoadedObject($customer)) {
            throw new PrestaShopException('Can\'t load Customer object');
        }
        if (!Validate::isLoadedObject($carrier)) {
            throw new PrestaShopException('Can\'t load Carrier object');
        }
        if (!Validate::isLoadedObject($address)) {
            throw new PrestaShopException('Can\'t load Address object');
        }

        $products = $order->getCartProducts();
        $link = Context::getContext()->link;

        $metadata = '';
        foreach ($products as $product) {
            $prod_obj = new Product((int) $product['product_id']);

            //try to get the first image for the purchased combination
            $img = $prod_obj->getCombinationImages($order->id_lang);
            $link_rewrite = $prod_obj->link_rewrite[$order->id_lang];
            $combination_img = $img[$product['product_attribute_id']][0]['id_image'];
            if ($combination_img != null) {
                $img_url = $link->getImageLink($link_rewrite, $combination_img, 'large_default');
            } else {
                //if there is no combination image, then get the product cover instead
                $img = $prod_obj->getCover($prod_obj->id);
                $img_url = $link->getImageLink($link_rewrite, $img['id_image']);
            }
            $prod_url = $prod_obj->getLink();

            $metadata .= "\n" . '<div itemprop="itemShipped" itemscope itemtype="http://schema.org/Product">';
            $metadata .= "\n" . '   <meta itemprop="name" content="' . htmlspecialchars($product['product_name']) . '"/>';
            $metadata .= "\n" . '   <link itemprop="image" href="' . $img_url . '"/>';
            $metadata .= "\n" . '   <link itemprop="url" href="' . $prod_url . '"/>';
            $metadata .= "\n" . '</div>';
        }

        $orderLanguage = new Language((int) $order->id_lang);
        $templateVars = array(
            '{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{id_order}' => $order->id,
            '{shipping_number}' => $this->tracking_number,
            '{order_name}' => $order->getUniqReference(),
            '{carrier}' => $carrier->name,
            '{address1}' => $address->address1,
            '{country}' => $address->country,
            '{postcode}' => $address->postcode,
            '{city}' => $address->city,
            '{meta_products}' => $metadata,
        );

        if (@Mail::Send(
            (int) $order->id_lang,
            'in_transit',
            $this->trans(
                'Package in transit',
                array(),
                'Emails.Subject',
                $orderLanguage->locale
            ),
            $templateVars,
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname,
            null,
            null,
            null,
            null,
            _PS_MAIL_DIR_,
            true,
            (int) $order->id_shop
        )) {
            return true;
        } else {
            return false;
        }
    }

    public function updateWs()
    {
        if (!parent::update()) {
            return false;
        }

        $sendemail = (bool) Tools::getValue('sendemail', false);

        if ($sendemail) {
            $order = new Order((int) $this->id_order);
            if (!Validate::isLoadedObject($order)) {
                throw new PrestaShopException('Can\'t load Order object');
            }

            if (!$this->sendInTransitEmail($order)) {
                return false;
            }
        }

        return true;
    }
}
