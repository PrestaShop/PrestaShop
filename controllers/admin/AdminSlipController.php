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

class AdminSlipControllerCore extends AdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
	 	$this->table = 'order_slip';
		$this->className = 'OrderSlip';
		$this->fields_list = array(
			'id_order_slip' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'class' => 'fixed-width-xs'
 			),
			'id_order' => array(
				'title' => $this->l('ID Order'),
				'align' => 'left',
				'class' => 'fixed-width-md'
 			),
			'date_add' => array(
				'title' => $this->l('Date issued'),
				'type' => 'date',
				'align' => 'right'
 			),
 			'id_pdf' => array(
				'title' => $this->l('PDF'),
				'align' => 'center',
				'callback' => 'printPDFIcons',
				'orderby' => false,
				'search' => false,
				'remove_onclick' => true)
		);

		$this->_select = 'a.id_order_slip AS id_pdf';
		$this->optionTitle = $this->l('Slip');

		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('Credit slip options'),
				'fields' =>	array(
					'PS_CREDIT_SLIP_PREFIX' => array(
						'title' => $this->l('Credit slip prefix:'),
						'desc' => $this->l('Prefix used for credit slips'),
						'size' => 6,
						'type' => 'textLang'
					)
				),
				'submit' => array('title' => $this->l('Save'))
			)
		);

		parent::__construct();
	}

	public function initPageHeaderToolbar()
	{
		$this->page_header_toolbar_btn['generate_pdf'] = array(
			'href' => self::$currentIndex.'&amp;token='.$this->token,
			'desc' => $this->l('Generate PDF'),
			'icon' => 'process-icon-save-date'
		);

		parent::initPageHeaderToolbar();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Print a PDF'),
				'icon' => 'icon-print'
			),
			'input' => array(
				array(
					'type' => 'date',
					'label' => $this->l('From:'),
					'name' => 'date_from',
					'maxlength' => 10,
					'required' => true,
					'hint' => $this->l('Format: 2011-12-31 (inclusive)')
				),
				array(
					'type' => 'date',
					'label' => $this->l('To:'),
					'name' => 'date_to',
					'maxlength' => 10,
					'required' => true,
					'hint' => $this->l('Format: 2012-12-31 (inclusive)')
				)
			),
			'submit' => array(
				'title' => $this->l('Generate PDF file'),
				'id' => 'submitPrint',
				'icon' => 'process-icon-download-alt'
			)
		);

		$this->fields_value = array(
			'date_from' => date('Y-m-d'),
			'date_to' => date('Y-m-d')
		);

		$this->show_toolbar = false;
		return parent::renderForm();
	}

	public function postProcess()
	{
		if (Tools::getValue('submitAddorder_slip'))
		{
			if (!Validate::isDate(Tools::getValue('date_from')))
				$this->errors[] = $this->l('Invalid "From" date');
			if (!Validate::isDate(Tools::getValue('date_to')))
				$this->errors[] = $this->l('Invalid "To" date');
			if (!count($this->errors))
			{
				$order_slips = OrderSlip::getSlipsIdByDate(Tools::getValue('date_from'), Tools::getValue('date_to'));
				if (count($order_slips))
					Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf').'&submitAction=generateOrderSlipsPDF&date_from='.urlencode(Tools::getValue('date_from')).'&date_to='.urlencode(Tools::getValue('date_to')));
				$this->errors[] = $this->l('No order slips were found for this period.');
			}
		}
		else
			return parent::postProcess();
	}

	public function initContent()
	{
		$this->initTabModuleList();
		$this->initToolbar();
		$this->initPageHeaderToolbar();
		$this->content .= $this->renderList();
		$this->content .= $this->renderForm();
		$this->content .= $this->renderOptions();

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
			'show_page_header_toolbar' => $this->show_page_header_toolbar,
			'page_header_toolbar_title' => $this->page_header_toolbar_title,
			'page_header_toolbar_btn' => $this->page_header_toolbar_btn
		));
	}

	public function initToolbar()
	{
		$this->toolbar_btn['save-date'] = array(
			'href' => '#',
			'desc' => $this->l('Generate PDF file')
		);
	}
	
	public function printPDFIcons($id_order_slip, $tr)
	{
		$order_slip = new OrderSlip((int)$id_order_slip);
		if (!Validate::isLoadedObject($order_slip))
			return '';

		$this->context->smarty->assign(array(
			'order_slip' => $order_slip,
			'tr' => $tr
		));

		return $this->createTemplate('_print_pdf_icon.tpl')->fetch();
	}
}

