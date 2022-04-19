<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
class OrderPaymentCore extends ObjectModel
{
    public $order_reference;
    public $id_currency;
    /**
     * @var float
     */
    public $amount;
    public $payment_method;
    public $conversion_rate;
    public $transaction_id;
    public $card_number;
    public $card_brand;
    public $card_expiration;
    public $card_holder;
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'order_payment',
        'primary' => 'id_order_payment',
        'fields' => [
            'order_reference' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 9],
            'id_currency' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'amount' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'payment_method' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'conversion_rate' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat'],
            'transaction_id' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254],
            'card_number' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254],
            'card_brand' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254],
            'card_expiration' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254],
            'card_holder' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    public function add($autodate = true, $nullValues = false)
    {
        if (parent::add($autodate, $nullValues)) {
            Hook::exec('actionPaymentCCAdd', ['paymentCC' => $this]);

            return true;
        }

        return false;
    }

    /**
     * Get the detailed payment of an order.
     *
     * @param string $order_reference
     *
     * @return array
     *
     * @since 1.5.0.13
     */
    public static function getByOrderReference($order_reference)
    {
        return ObjectModel::hydrateCollection(
            'OrderPayment',
            Db::getInstance()->executeS(
                'SELECT *
			    FROM `' . _DB_PREFIX_ . 'order_payment`
			    WHERE `order_reference` = \'' . pSQL($order_reference) . '\''
            )
        );
    }

    /**
     * Get Order Payments By Invoice ID.
     *
     * @param int $id_invoice Invoice ID
     *
     * @return PrestaShopCollection|array Collection of OrderPayment
     */
    public static function getByInvoiceId($id_invoice)
    {
        $payments = Db::getInstance()->executeS('SELECT id_order_payment FROM `' . _DB_PREFIX_ . 'order_invoice_payment` WHERE id_order_invoice = ' . (int) $id_invoice);
        if (!$payments) {
            return [];
        }

        $payment_list = [];
        foreach ($payments as $payment) {
            $payment_list[] = $payment['id_order_payment'];
        }

        $payments = new PrestaShopCollection('OrderPayment');
        $payments->where('id_order_payment', 'IN', $payment_list);

        return $payments;
    }

    /**
     * Return order invoice object linked to the payment.
     *
     * @param int $id_order Order Id
     *
     * @since 1.5.0.13
     */
    public function getOrderInvoice($id_order)
    {
        $res = Db::getInstance()->getValue('
		SELECT id_order_invoice
		FROM `' . _DB_PREFIX_ . 'order_invoice_payment`
		WHERE id_order_payment = ' . (int) $this->id . '
		AND id_order = ' . (int) $id_order);

        if (!$res) {
            return false;
        }

        return new OrderInvoice((int) $res);
    }
}
