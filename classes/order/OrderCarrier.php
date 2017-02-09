<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
            'id_order' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_carrier' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_invoice' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'weight' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'shipping_cost_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'shipping_cost_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'tracking_number' =>        array('type' => self::TYPE_STRING, 'validate' => 'isTrackingNumber'),
            'date_add' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
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
     * @return bool
     */
    public function sendInTransitEmail($order)
    {
        $customer = new Customer((int)$order->id_customer);
        $carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
        if (!Validate::isLoadedObject($customer)) {
            throw new PrestaShopException('Can\'t load Customer object');
        }
        if (!Validate::isLoadedObject($carrier)) {
            throw new PrestaShopException('Can\'t load Carrier object');
        }
        $orderLanguage = new Language((int) $order->id_lang);
        $templateVars = array(
            '{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{id_order}' => $order->id,
            '{shipping_number}' => $this->tracking_number,
            '{order_name}' => $order->getUniqReference()
        );

        if (@Mail::Send(
            (int)$order->id_lang,
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
            (int)$order->id_shop
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

        $sendemail = (bool)Tools::getValue('sendemail', false);

        if ($sendemail) {
            $order = new Order((int)$this->id_order);
            if (!Validate::isLoadedObject($order)) {
                throw new PrestaShopException('Can\'t load Order object');
            }

            if (!$this->sendInTransitEmail($order))
            {
                return false;
            }
        }

        return true;
    }

}
