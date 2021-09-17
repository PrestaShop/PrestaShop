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

/**
 * @since 1.5
 */
class HTMLTemplateSupplyOrderFormCore extends HTMLTemplate
{
    /**
     * @var SupplyOrder
     */
    public $supply_order;

    /**
     * @var Warehouse
     */
    public $warehouse;

    /**
     * @var Address
     */
    public $address_warehouse;

    /**
     * @var Address
     */
    public $address_supplier;

    /**
     * @var Context
     */
    public $context;

    /**
     * @param SupplyOrder $supply_order
     * @param Smarty $smarty
     *
     * @throws PrestaShopException
     */
    public function __construct(SupplyOrder $supply_order, Smarty $smarty)
    {
        $this->supply_order = $supply_order;
        $this->smarty = $smarty;
        $this->context = Context::getContext();
        $this->warehouse = new Warehouse((int) $supply_order->id_warehouse);
        $this->address_warehouse = new Address((int) $this->warehouse->id_address);
        $this->address_supplier = new Address(Address::getAddressIdBySupplierId((int) $supply_order->id_supplier));

        // header informations
        $this->date = Tools::displayDate($supply_order->date_add);

        $this->title = Context::getContext()->getTranslator()->trans('Supply order form', [], 'Shop.Pdf');

        $this->shop = Context::getContext()->shop;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        $supply_order_details = $this->supply_order->getEntriesCollection((int) $this->supply_order->id_lang);
        $this->roundSupplyOrderDetails($supply_order_details);

        $this->roundSupplyOrder($this->supply_order);

        $tax_order_summary = $this->getTaxOrderSummary();
        $currency = new Currency((int) $this->supply_order->id_currency);

        $this->smarty->assign([
            'warehouse' => $this->warehouse,
            'address_warehouse' => $this->address_warehouse,
            'address_supplier' => $this->address_supplier,
            'supply_order' => $this->supply_order,
            'supply_order_details' => $supply_order_details,
            'tax_order_summary' => $tax_order_summary,
            'currency' => $currency,
        ]);

        $tpls = [
            'style_tab' => $this->smarty->fetch($this->getTemplate('invoice.style-tab')),
            'addresses_tab' => $this->smarty->fetch($this->getTemplate('supply-order.addresses-tab')),
            'product_tab' => $this->smarty->fetch($this->getTemplate('supply-order.product-tab')),
            'tax_tab' => $this->smarty->fetch($this->getTemplate('supply-order.tax-tab')),
            'total_tab' => $this->smarty->fetch($this->getTemplate('supply-order.total-tab')),
        ];
        $this->smarty->assign($tpls);

        return $this->smarty->fetch($this->getTemplate('supply-order'));
    }

    /**
     * Returns the invoice logo.
     *
     * @return string Logo path
     */
    protected function getLogo()
    {
        $logo = '';

        if (Configuration::get('PS_LOGO_INVOICE', null, null, (int) Shop::getContextShopID()) != false && file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_INVOICE', null, null, (int) Shop::getContextShopID()))) {
            $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO_INVOICE', null, null, (int) Shop::getContextShopID());
        } elseif (Configuration::get('PS_LOGO', null, null, (int) Shop::getContextShopID()) != false && file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, (int) Shop::getContextShopID()))) {
            $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, (int) Shop::getContextShopID());
        }

        return $logo;
    }

    /**
     * {@inheritdoc}
     */
    public function getBulkFilename()
    {
        return 'supply_order.pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename()
    {
        return 'supply-order-form-' . sprintf('%06d', $this->supply_order->reference) . '.pdf';
    }

    /**
     * Get order taxes summary.
     *
     * @return array
     *
     * @throws PrestaShopDatabaseException
     */
    protected function getTaxOrderSummary()
    {
        $query = new DbQuery();
        $query->select('
            SUM(price_with_order_discount_te) as base_te,
            tax_rate,
            SUM(tax_value_with_order_discount) as total_tax_value
        ');
        $query->from('supply_order_detail');
        $query->where('id_supply_order = ' . (int) $this->supply_order->id);
        $query->groupBy('tax_rate');

        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        foreach ($results as &$result) {
            $result['base_te'] = Tools::ps_round($result['base_te'], 2);
            $result['tax_rate'] = Tools::ps_round($result['tax_rate'], 2);
            $result['total_tax_value'] = Tools::ps_round($result['total_tax_value'], 2);
        }
        unset($result);

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader()
    {
        $shop_name = Configuration::get('PS_SHOP_NAME');
        $path_logo = $this->getLogo();
        $width = $height = 0;

        if (!empty($path_logo)) {
            list($width, $height) = getimagesize($path_logo);
        }

        $this->smarty->assign([
            'logo_path' => $path_logo,
            'img_ps_dir' => Tools::getShopProtocol() . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_,
            'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
            'title' => $this->title,
            'reference' => $this->supply_order->reference,
            'date' => $this->date,
            'shop_name' => $shop_name,
            'width_logo' => $width,
            'height_logo' => $height,
        ]);

        return $this->smarty->fetch($this->getTemplate('supply-order-header'));
    }

    /**
     * {@inheritdoc}
     */
    public function getFooter()
    {
        $free_text = [];
        $free_text[] = Context::getContext()->getTranslator()->trans('TE: Tax excluded', [], 'Shop.Pdf');
        $free_text[] = Context::getContext()->getTranslator()->trans('TI: Tax included', [], 'Shop.Pdf');

        $this->smarty->assign([
            'shop_address' => $this->getShopAddress(),
            'shop_fax' => Configuration::get('PS_SHOP_FAX'),
            'shop_phone' => Configuration::get('PS_SHOP_PHONE'),
            'shop_details' => Configuration::get('PS_SHOP_DETAILS'),
            'free_text' => $free_text,
        ]);

        return $this->smarty->fetch($this->getTemplate('supply-order-footer'));
    }

    /**
     * Rounds values of a SupplyOrderDetail object.
     *
     * @param array|PrestaShopCollection $collection
     */
    protected function roundSupplyOrderDetails(&$collection)
    {
        foreach ($collection as $supply_order_detail) {
            /* @var SupplyOrderDetail $supply_order_detail */
            $supply_order_detail->unit_price_te = Tools::ps_round($supply_order_detail->unit_price_te, 2);
            $supply_order_detail->price_te = Tools::ps_round($supply_order_detail->price_te, 2);
            $supply_order_detail->discount_rate = Tools::ps_round($supply_order_detail->discount_rate, 2);
            $supply_order_detail->price_with_discount_te = Tools::ps_round($supply_order_detail->price_with_discount_te, 2);
            $supply_order_detail->tax_rate = Tools::ps_round($supply_order_detail->tax_rate, 2);
            $supply_order_detail->price_ti = Tools::ps_round($supply_order_detail->price_ti, 2);
        }
    }

    /**
     * Rounds values of a SupplyOrder object.
     *
     * @param SupplyOrder $supply_order
     */
    protected function roundSupplyOrder(SupplyOrder &$supply_order)
    {
        $supply_order->total_te = Tools::ps_round($supply_order->total_te, 2);
        $supply_order->discount_value_te = Tools::ps_round($supply_order->discount_value_te, 2);
        $supply_order->total_with_discount_te = Tools::ps_round($supply_order->total_with_discount_te, 2);
        $supply_order->total_tax = Tools::ps_round($supply_order->total_tax, 2);
        $supply_order->total_ti = Tools::ps_round($supply_order->total_ti, 2);
    }
}
