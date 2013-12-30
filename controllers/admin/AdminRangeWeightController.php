<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminRangeWeightControllerCore extends AdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
	 	$this->table = 'range_weight';
	 	$this->className = 'RangeWeight';
	 	$this->lang = false;

		$this->addRowAction('edit');
		$this->addRowAction('delete');
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fields_list = array(
			'id_range_weight' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'carrier_name' => array('title' => $this->l('Carrier'), 'align' => 'left', 'width' => 'auto', 'filter_key' => 'ca!name'),
			'delimiter1' => array('title' => $this->l('From'), 'width' => 86, 'type' => 'float', 'suffix' => Configuration::get('PS_WEIGHT_UNIT'), 'align' => 'right'),
			'delimiter2' => array('title' => $this->l('To'), 'width' => 86, 'type' => 'float', 'suffix' => Configuration::get('PS_WEIGHT_UNIT'), 'align' => 'right'));

		$this->_join = 'LEFT JOIN '._DB_PREFIX_.'carrier ca ON (ca.`id_carrier` = a.`id_carrier`)';
		$this->_select = 'ca.`name` AS carrier_name';
		$this->_where = 'AND ca.`deleted` = 0';

		parent::__construct();
	}

	public function initPageHeaderToolbar()
	{
		$this->page_header_toolbar_title = $this->l('Weight ranges');
		$this->page_header_toolbar_btn['new_weight_range'] = array(
			'href' => self::$currentIndex.'&amp;addrange_weight&amp;token='.$this->token,
			'desc' => $this->l('Add new weight range'),
			'icon' => 'process-icon-new'
		);

		parent::initPageHeaderToolbar();
	}

	public function renderForm()
	{
		$carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
		foreach ($carriers as $key => $carrier)
			if ($carrier['is_free'])
				unset($carriers[$key]);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Weight ranges'),
				'icon' => 'icon-suitcase'
			),
			'input' => array(
				array(
					'type' => 'select',
					'label' => $this->l('Carrier:'),
					'name' => 'id_carrier',
					'required' => false,
					'hint' => $this->l('You can apply this range to a different carrier by selecting its name.'),
					'options' => array(
						'query' => $carriers,
						'id' => 'id_carrier',
						'name' => 'name'
					),
					'empty_message' => '<p class="alert alert-block">'.$this->l('There is no carrier available for this weight range.').'</p>'
				),
				array(
					'type' => 'text',
					'label' => $this->l('From:'),
					'name' => 'delimiter1',
					'required' => true,
					'suffix' => Configuration::get('PS_WEIGHT_UNIT'),
					'hint' => $this->l('Start range (included)'),
				),
				array(
					'type' => 'text',
					'label' => $this->l('To:'),
					'name' => 'delimiter2',
					'required' => true,
					'suffix' => Configuration::get('PS_WEIGHT_UNIT'),
					'hint' => $this->l('End range (excluded)'),
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'btn btn-default'
			)
		);

		return parent::renderForm();
	}

	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
		if ($this->_list && is_array($this->_list))
			foreach ($this->_list as $key => $list)
				if ($list['carrier_name'] == '0')
					$this->_list[$key]['carrier_name'] = Configuration::get('PS_SHOP_NAME');
	}

	public function postProcess()
	{
		$id = (int)Tools::getValue('id_'.$this->table);
		
		if (Tools::getValue('submitAdd'.$this->table))
		{
			if (Tools::getValue('delimiter1') >= Tools::getValue('delimiter2'))
				$this->errors[] = Tools::displayError('Invalid range');
			else if (!$id && RangeWeight::rangeExist((int)Tools::getValue('id_carrier'), (float)Tools::getValue('delimiter1'), (float)Tools::getValue('delimiter2')))
				$this->errors[] = Tools::displayError('The range already exists');
			else if (RangeWeight::isOverlapping((int)Tools::getValue('id_carrier'), (float)Tools::getValue('delimiter1'), (float)Tools::getValue('delimiter2'), ($id ? (int)$id : null)))
				$this->errors[] = Tools::displayError('Error: Ranges are overlapping');
			else if (!count($this->errors))
				parent::postProcess();
		}
		else
			parent::postProcess();
	}
}


