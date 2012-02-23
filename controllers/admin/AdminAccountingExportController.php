<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 9841 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminAccountingExportControllerCore extends AdminController
{
	public $exportTypeList = array();
	
	public $pathTheme = '';
	
	public $defaultType = 'block_global_export';
	
	public $downloadDir = '';
	
	public $downloadFile = '';
	
	public $exportSelected = '';
	
	public $fd = null;
	
	public $date = array(
		'begin' => '',
		'end' => ''
	);
	
	public $prevent = array(
		'errors' => array(),
		'warns' => array(),
		'hints' => array());
	
	public $exportedFilePath = '';
	
	public $clientPrefix = '';
	
	public function __construct()
	{
		$this->className = 'Accounting';

	 	$this->content = '';
	 	$this->downloadDir = _PS_ADMIN_DIR_.'/export/';
	 	$this->exportSelected = 'global_export';

	 	$this->initExportFieldList();	
		parent::__construct();
	}
	
	/**
	 * Init the available fields by export type with associated translation
	 */
	protected function initExportFieldList()
	{
		$this->exportTypeList = array(
			'global_export' => array(
				'name' => $this->l('Global Export'),
				'type' => 'global_export',
				'file' => 'accounting_global_export.csv',
				'fields' => array(
					'invoice_date' => $this->l('Invoice Date', 'AdminTab', false, false),
					'journal' => $this->l('Journal', 'AdminTab', false, false),
					'account' => $this->l('Account', 'AdminTab', false, false),
					'invoice_number' => $this->l('Invoice Number', 'AdminTab', false, false),
					'credit' => $this->l('Credit (TTC)', 'AdminTab', false, false),
					'debit' => $this->l('Debit (TVA+HT)', 'AdminTab', false, false),
					'transaction_id' => $this->l('Transaction Number', 'AdminTab', false, false),
					'payment_type' => $this->l('Payment Type', 'AdminTab', false, false),
					'currency_code' => $this->l('Currency Code', 'AdminTab', false, false),
					'wording' => $this->l('Wording', 'AdminTab', false, false)
				)
			),
			'reconciliation_export' => array(
				'name' => $this->l('Reconciliation Export'),
				'type' => 'reconciliation_export',
				'file' => 'accounting_reconciliation_export.csv',
				'fields' => array(
					'invoice_number' => $this->l('Invoice Number', 'AdminTab', false, false),
					'wording' => $this->l('Wording', 'AdminTab', false, false),
					'total_paid_real' => $this->l('Total TTC', 'AdminTab', false, false),
					'invoice_date' => $this->l('Invoice Date', 'AdminTab', false, false),
					'transaction_id' => $this->l('Transaction Number', 'AdminTab', false, false),
					'account_client' => $this->l('Account client', 'AdminTab', false, false)
				)
			) 
	 	);
	}
	
	/**
	 * Init the block Menu
	 */
	protected function initMenu()
	{
		$this->context->smarty->assign(array(
			'exportTypeList' => $this->exportTypeList,
			'defaultType' => $this->defaultType,
			'preventList' => $this->prevent
			)
		);

		$this->content .= $this->createTemplate('menu.tpl')->fetch();
	}
	
	/**
	 * AdminController::setMedia() override
	 * @see AdminController::setMedia()
	 */
	public function setMedia()
	{
		parent::setMedia();
		
		$this->addJqueryUi('ui.datepicker');
	}
	
	protected function checkRights()
	{
		if (!is_writeable($this->downloadDir))
			$this->errors[] = $this->l('The download folder doesn\'t have the sufficient right…');
		if (!($this->fd = fopen($this->downloadFile, 'w+')))
			$this->errors[] = $this->l('The file can\'t be opened or created, please check the right');
		@chmod($this->downloadFile, 0777);
	}
	
	/**
	 * AdminController::init() override
	 * @see AdminController::init()
	 */
	public function initContent()
	{	
		$this->initMenu();
		
		$this->context->smarty->assign(array(
			'clientPrefix' => Configuration::get('ACCOUNTING_CLIENT_PREFIX_EXPORT'),
			'journal' => Configuration::get('ACCOUNTING_JOURNAL_EXPORT'),
			'begin_date' => Tools::getValue('beginDate'),
			'end_date' => Tools::getValue('endDate'),
			'urlDownload' => Tools::getShopDomain().'/download/'
		));
		
		foreach ($this->exportTypeList as $exportType)
		{
			$this->context->smarty->assign(array(
				'title' => $exportType['name'],
				'type' => $exportType['type'],
				'existingExport' => file_exists($this->downloadDir.$exportType['file']) ? true : false
			));

			$this->content .= $this->createTemplate($exportType['type'].'.tpl')->fetch();
		}
		parent::initContent();
	}
	
	/**
	 * AdminController::postProcess() override
	 * @see AdminController::postProcess()
	 */
	public function postProcess()
	{
		if (Tools::isSubmit('submitAccountingExportType'))
		{
			$this->date['begin'] = Tools::getValue('beginDate');
			$this->date['end'] = Tools::getValue('endDate');
			$this->clientPrefix = Tools::getValue('clientPrefix');
			$this->exportSelected = Tools::getValue('exportType');
			$this->downloadFile = $this->downloadDir.'accounting_'.$this->exportSelected.'.csv';
			
			Configuration::updateValue('ACCOUNTING_CLIENT_PREFIX_EXPORT', $this->clientPrefix);
			// Depends of the number of order and the range dates
			// Switch to ajax if there is any problems with time
			ini_set('max_execution_time', 0);
			
			switch ($this->exportSelected)
			{
				case 'reconciliation_export':
					$this->runReconciliationExport();
					break;
				case 'global_export':
					$this->runGlobalExport();
					Configuration::updateValue('ACCOUNTING_JOURNAL_EXPORT', Tools::getValue('journal'));
					break;
				default:
					// If not defined, set export type to default
					$this->exportSelected = 'global_export';
			}
			
			// Set back the default block type to display
			$this->defaultType = 'block_'.$this->exportSelected.'';
		}
		else if (($type = Tools::getValue('download')) && array_key_exists($type, $this->exportTypeList))
			$this->downloadFile($this->exportTypeList[$type]['file']);
	}
	
	/**
	 * Write the exported content tout a file
	 * @var array $list Result of the SQL query
	 */
	protected function writeExportToFile($list)
	{
		$this->checkRights();
		
		if (!count($this->errors) && $this->fd !== null)
		{
			$buffer = '';
			foreach ($this->exportTypeList[$this->exportSelected]['fields'] as $key => $translation)
				$buffer .= '"'.$translation.'";';
			fwrite($this->fd, mb_convert_encoding(rtrim($buffer, ';')."\r\n", 'UTF-16LE'));
			
			// Bufferize line by line and write it to the file
			// Todo :: Allow to configure the size of the buffer before flushing it
			foreach ($list as $row)
			{
				$buffer = '';
				foreach ($row as $col => $val)
					$buffer .= '"'.$val.'";';
				fwrite($this->fd, mb_convert_encoding(rtrim($buffer, ';')."\r\n", 'UTF-16LE'));
			}
			$this->confirmations[] = $this->l('Export has been successfully done');
		}
	}
	
	/**
	 * Start the reconciliation export type
	 */
	protected function runReconciliationExport()
	{
		$query = '
			SELECT
				CONCAT(\''.Configuration::get('PS_INVOICE_PREFIX').'\', LPAD(oi.`number`, 6, "0")) AS invoice_number,
				CASE 
 					WHEN (a.`company` != "" AND a.`company` IS NOT NULL) THEN a.`company`
					ELSE  a.`lastname`
				END AS wording,
				o.`total_paid_real`,
				oi.`date_add` as invoice_date,
				pcc.`transaction_id`,
				CONCAT(\''.pSQL($this->clientPrefix).'\', LPAD(c.`id_customer`, 6, "0")) AS account_client
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'customer` c ON c.`id_customer` = o.`id_customer`
				LEFT JOIN `'._DB_PREFIX_.'address` a ON a.`id_customer` = o.`id_customer` 
				LEFT JOIN `'._DB_PREFIX_.'order_payment` pcc ON pcc.`id_order` = o.`id_order`
				LEFT JOIN `'._DB_PREFIX_.'order_invoice` oi ON oi.`id_order` = o.`id_order`
				WHERE o.`valid` = 1
				AND oi.`date_add`
					BETWEEN \''.pSQL($this->date['begin']).'\' 
					AND \''.pSQL($this->date['end']).'\'';
		
		$list = Db::getInstance()->executeS($query);
		
		$this->writeExportToFile($list);
	}

	/**
	 * Generate a line for the CSV for the global export
	 *
	 * @param $row
	 * @param $line_number
	 * @return array
	 */
	protected function createLine($row, $line_number)
	{
		$line = array();

		// Default Values
		$line[0] = $row['invoice_date'];
		$line[1] = Tools::getValue('journal');
		$line[2] = ''; // account number
		$line[3] = $row['invoice_number'];
		$line[4] = 0.00; // Credit TTC (Total for first csv line, 0 for others)
		$line[5] = 0.00; // Debit HT (0 For the first line, used for tax too)
		$line[6] = $row['transaction_id'];
		$line[7] = $row['payment_type'];
		$line[8] = $row['currency_code'];
		$line[9] = $row['wording'];
		
		// Override case depending of the whished line
		switch ($line_number)
		{
			case 0:
				$line[2] = $row['account_client'];
				$line[4] = $row['total_price_tax_incl'];
				break;
			case 1:
				$line[2] = !empty($row['account']) ? $row['account'] :
					Configuration::get('default_account_number', null, null, $row['id_shop']);
				// Force an empty string if Configuration send false
				$line[2] = empty($line[2]) ? '' : $line[2];
				$line[5] = $row['product_price_ht'];
				break;
			case 2:
				$line[2] = $row['tax_accounting_account_number'];
				$line[5] = $row['tax_total_amount'];
				break;
		}
		return $line;
	}
	
	/**
	 * Build an proper list to be written into the export file
	 *
	 * @param $db_details
	 * @return array
	 */
	protected function buildGlobalExportlist($db_details)
	{
		// List use to write data in csv file
		$list = array();
		
		// Cache list to merge easily the content with the same accounting for different invoice number
		$cache_list = array();
		$num = 0;
		foreach ($db_details as $row)
		{
			// Init the list for the current invoice number
			if (!array_key_exists($row['invoice_number'], $cache_list))
				$cache_list[$row['invoice_number']] = array();
			
			// Need to Generate 3 lines for a product
			for ($i = 0; $i < 3; ++$i)
				// Create the two first line and check if a tax exist for the last one
				if ($i < 2 || ($i == 2 && $row['id_tax'] !== null))
				{
					// Generate a product line
					$line = $this->createLine($row, $i);
					if ($i == 0)
						$list[$num++] = $line;
					else
					{
						// Check if the account number hadn't already be used for this invoice number
						// $line[3] = invoice_number, $line[2] = account_number
						if (!array_key_exists($line[2], $cache_list[$line[3]]))
							$cache_list[$line[3]][$line[2]] = array();

						// If this id_product doesn't exist for this invoice number, then we create it as a cache
						if (!in_array($row['id_product'], $cache_list[$line[3]][$line[2]]))
						{
							$cache_list[$line[3]][$line[2]][$row['id_product']] = array(
								'position' => $num,
								'id_order' => $row['id_order'],
								'id_product_attribute' => $row['id_product_attribute'],
								'quantity' => 1,
								'advanced_stock_management' => $row['advanced_stock_management']);
							$list[$num++] = $line;
						}
						else
						{
							// Merge amount retrieving the position in the list of the invoice number
							// Some information could change (quantity) for the Stock movement price calculation
							$pos = $cache_list[$line[3]][$line[2]][$row['id_product']]['position'];
							$cache_list[$line[3]][$line[2]][$row['id_product']]['quantity'] += 1;
							if (!$i)
								$list[$pos][4] += $line[4];
							else
								$list[$pos][5] += $line[5];
						}
					}
				}
		}

		// If advanced stock management enable then we foreach the cache_list to know
		// if a product use the system to store back the movement price.
		if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
			foreach ($cache_list as $invoice_list)
				foreach ($invoice_list as $product_list)
					foreach ($product_list as $id_product => $product_detail)
						if ($product_detail['advanced_stock_management'])
						{
							// Get stock product stock movement detail
							$stock_mvt =	StockMvt::getNegativeStockMvts(
								$product_detail['id_order'],
								$id_product,
								$product_detail['id_product_attribute'],
								$product_detail['quantity']);

							// Store new price
							$list[$product_detail['position']] = $stock_mvt['price_te'];
						}
		return $list;
	}
	
	/**
	 * Start the global export type 
	 *
	 */
	protected function runGlobalExport()
	{
		$query = '
			SELECT 
				od.`id_order`,
				oi.`date_add` as invoice_date,
				CASE 
 					WHEN (acc_pzs.`account_number` != "" AND acc_pzs.`account_number` IS NOT NULL) THEN acc_pzs.`account_number`
 					WHEN (acc_zs.`account_number` != "" AND acc_zs.`account_number` IS NOT NULL) THEN acc_zs.`account_number`
 					ELSE  ""
				END AS account,
				CONCAT(\''.Configuration::get('PS_INVOICE_PREFIX').'\', LPAD(oi.`number`, 6, "0")) AS invoice_number,
				od.`total_price_tax_incl`,
				od.`product_price` AS product_price_ht,
				pcc.`transaction_id`,
				o.`payment` AS payment_type,
				currency.`iso_code` AS currency_code,
				CONCAT(\''.pSQL($this->clientPrefix).'\', LPAD(customer.`id_customer`, 6, "0")) AS account_client,
				CASE 
 					WHEN (a.`company` != "" AND a.`company` IS NOT NULL) THEN a.`company`
					ELSE  a.`lastname`
				END AS wording,
				t.`account_number` AS tax_accounting_account_number,
				t.`id_tax`,
				o.`id_shop`,
				odt.`total_amount` AS tax_total_amount,
				od.`product_id` AS id_product,
				od.`product_attribute_id` as id_product_attribute,
				p.`advanced_stock_management`
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'customer` customer ON customer.`id_customer` = o.`id_customer`
				LEFT JOIN `'._DB_PREFIX_.'address` a ON a.`id_customer` = o.`id_customer` 
				LEFT JOIN `'._DB_PREFIX_.'order_payment` pcc ON pcc.`id_order` = o.`id_order`
				LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON od.`id_order` = o.`id_order`
				LEFT JOIN `'._DB_PREFIX_.'currency` currency ON currency.`id_currency` = o.`id_currency`
				LEFT JOIN `'._DB_PREFIX_.'order_detail_tax` odt ON odt.`id_order_detail` = od.`id_order_detail`
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON t.`id_tax` = odt.`id_tax`
				LEFT JOIN `'._DB_PREFIX_.'country` country ON country.`id_country` = a.`id_country`
				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = od.`product_id`
				LEFT JOIN `'._DB_PREFIX_.'accounting_product_zone_shop` acc_pzs
					ON (acc_pzs.`id_shop` = o.`id_shop`
					AND acc_pzs.`id_zone` = country.`id_zone`
					AND acc_pzs.`id_product` = od.`product_id`)
				LEFT JOIN `'._DB_PREFIX_.'accounting_zone_shop` acc_zs 
					ON (acc_zs.`id_shop` = o.`id_shop`
					AND acc_zs.`id_zone` = country.`id_zone`)
				LEFT JOIN `'._DB_PREFIX_.'order_invoice` oi ON oi.id_order = o.id_order
				WHERE o.`valid` = 1
				AND oi.`date_add`
					BETWEEN \''.pSQL($this->date['begin']).'\' 
					AND \''.pSQL($this->date['end']).'\'
				ORDER BY o.`id_order` ASC';

		$list = $this->buildGlobalExportlist(Db::getInstance()->executeS($query));
		$this->writeExportToFile($list);
	}
	
	/**
	* Allow to download the last export file
	* @var string File name
	*/
	protected function downloadFile($fileName)
	{
		$path = $this->downloadDir.$fileName;
		header('Content-length: '.filesize($path));
		header('Content-Disposition: attachment; filename="'.$fileName.'"');
		
		// Flush buffered data before reading the file
		ob_clean();
    flush();
    
		@readfile($path);
		exit();  
	}
}
