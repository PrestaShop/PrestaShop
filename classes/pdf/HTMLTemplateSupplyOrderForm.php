<?php
/*
* 2007-2011 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5
 */
class HTMLTemplateSupplyOrderForm extends HTMLTemplate
{
	public $supply_order;
	public $warehouse;
	public $address_warehouse;
	public $context;

	public function __construct(SupplyOrder $supply_order, $smarty)
	{
		$this->supply_order = $supply_order;
        $this->smarty = $smarty;
        $this->context = Context::getContext();
        $this->warehouse = new Warehouse($supply_order->id_warehouse);
        $this->address_warehouse = new Address($this->warehouse->id_address);

   		// header informations
		$this->date = Tools::displayDate($supply_order->date_add, (int)$this->context->language->id);
		$this->title = self::l('Supply order form').sprintf(' #%s', $supply_order->reference);

		// footer informations : displays the address of the warehouse
		$this->address = $this->address_warehouse;
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
		$supply_order_details = $this->supply_order->getEntriesCollection($this->context->language->id);
		$tax_order_summary = $this->getTaxOrderSummary();
		$currency = new Currency($this->supply_order->id_currency);

		$this->smarty->assign(array(
			'warehouse' => $this->warehouse,
			'address_warehouse' => $this->address_warehouse,
			'supply_order' => $this->supply_order,
			'supply_order_details' => $supply_order_details,
			'tax_order_summary' => $tax_order_summary,
			'currency' => $currency,
		));
		return $this->smarty->fetch(_PS_THEME_DIR_.'/pdf/supply-order.tpl');
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
    public function getBulkFilename()
    {
        return 'supply_order.pdf';
    }

	/**
	 * Returns the template filename
	 * @return string filename
	 */
    public function getFilename()
    {
    	return ($this->l('SupplyOrderForm').sprintf('_%s', $this->supply_order->reference).'.pdf');
    }

    protected function getTaxOrderSummary()
    {
    	$query = new DbQuery();
    	$query->select('
    		SUM(price_with_order_discount_te) as base_te,
    		tax_rate,
    		SUM(tax_value_with_order_discount) as total_tax_value
    	');
    	$query->from('supply_order_detail');
    	$query->where('id_supply_order = '.(int)$this->supply_order->id);
    	$query->groupBy('tax_rate');

    	$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    	return $results;
    }
}
