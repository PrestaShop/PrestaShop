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
	
	public $fd = NULL;
	
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
		
	 	$this->pathAccountExportTpl = _PS_ADMIN_DIR_.'/themes/template/accounting_export/';
	 	$this->content = '';
	 	$this->downloadDir = _PS_ROOT_DIR_.'/download/';
	 	$this->exportSelected = 'global_export';
	 	
	 	$this->initExportFieldList();	
		parent::__construct();
	}
	
	/**
	 * Init the available fields by export type with associated translation 
	 *
	 */
	private function initExportFieldList()
	{
		$this->exportTypeList = array(
			'global_export' => array(
				'name' => $this->l('Global Export'),
				'type' => 'global_export',
				'file' => 'accounting_global_export.csv',
				'fields' => array(
					'invoice_date' => $this->l('Invoice Date'),
					'journal' => $this->l('Journal'),
					'account' => $this->l('Account'),
					'invoice_number' => $this->l('Invoice Number'),
					'credit' => $this->l('Credit (TTC)'),
					'debit' => $this->l('Debit (TVA+HT)'),
					'transaction_id' => $this->l('Transaction Number'),
					'payment_type' => $this->l('Payment Type'),
					'currency_code' => $this->l('Currency Code'),
					'wording' => $this->l('Wording')
				)
			),
			'reconciliation_export' => array(
				'name' => $this->l('Reconciliation Export'),
				'type' => 'reconciliation_export',
				'file' => 'accounting_reconciliation_export.csv',
				'fields' => array(
					'invoice_number' => $this->l('Invoice Number'),
					'wording' => $this->l('Wording'),
					'total_paid_real' => $this->l('Total TTC'),
					'invoice_date' => $this->l('Invoice Date'),
					'transaction_id' => $this->l('Transaction Number'),
					'account_client' => $this->l('Account client')
				)
			) 
	 	);
	}
	
	/**
	 * Init the block Menu 
	 *
	 */
	private function initMenu()
	{
		$this->context->smarty->assign(array(
			'exportTypeList' => $this->exportTypeList,
			'defaultType' => $this->defaultType,
			'preventList' => $this->prevent
			)
		);
		
		$this->content .= $this->context->smarty->fetch($this->pathAccountExportTpl.'menu.tpl');
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
	
	private function checkRights()
	{
		if (!is_writeable($this->downloadDir))
			$this->_errors[] = $this->l('The download folder doesn\'t have the sufficient right…');
		if (!($this->fd = fopen($this->downloadFile, 'w+')))
			$this->_errors[] = $this->l('The file can\'t be opened or created, please check the right');
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
			'pathAccountExportTpl' => $this->pathAccountExportTpl,
			'urlDownload' => Tools::getShopDomain().'/download/'
		));
		
		foreach($this->exportTypeList as $exportType)
		{
			$pathTpl = $this->pathAccountExportTpl.$exportType['type'].'.tpl';
			$pathExportedFile = $this->downloadDir.$exportType['file'];
			
			$this->context->smarty->assign(array(
				'title' => $exportType['name'],
				'type' => $exportType['type'],
				'existingExport' => file_exists($this->downloadDir.$exportType['file']) ? true : false
			));
			
			if (file_exists($pathTpl))
				$this->content .= $this->context->smarty->fetch($pathTpl);
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
			// Switch to ajax if any problem with time occured 
			ini_set('max_execution_time', 0);
			
			switch($this->exportSelected)
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
	private function writeExportToFile($list)
	{
		$this->checkRights();
		
		if (!count($this->_errors) && $this->fd !== NULL)
		{
			$buffer = '';
			foreach($this->exportTypeList[$this->exportSelected]['fields'] as $key => $translation)
				$buffer .= $translation.';';
			fwrite($this->fd, rtrim($buffer, ';')."\r\n");
			
			// Bufferize line by line and write it to the file
			// Todo :: Allow to configure the size of the buffer before flushing it
			foreach($list as $row)
			{
				$buffer = '';
				foreach ($row as $col => $val)
					$buffer .= $val.';';
				fwrite($this->fd, rtrim($buffer, ';')."\r\n");
			}
			$this->confirmations[] = $this->l('Export has been successfully done');
		}
	}
	
	/**
	 * Start the reconciliation export type 
	 *
	 */
	private function runReconciliationExport()
	{
		$query = '
			SELECT
				CONCAT(\''.Configuration::get('PS_INVOICE_PREFIX').'\', LPAD(o.`invoice_number`, 6, 0)) AS invoice_number,
				CASE 
 					WHEN (a.`company` != "" AND a.`company` IS NOT NULL) THEN a.`company`
					ELSE  a.`lastname`
				END AS wording,
				o.`total_paid_real`,
				o.`invoice_date`,
				pcc.`transaction_id`,
				CONCAT(\''.pSQL($this->clientPrefix).'\', LPAD(c.`id_customer`, 6, 0)) AS account_client
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'customer` c ON c.`id_customer` = o.`id_customer`
				LEFT JOIN `'._DB_PREFIX_.'address` a ON a.`id_customer` = o.`id_customer` 
				LEFT JOIN `'._DB_PREFIX_.'payment_cc` pcc ON pcc.`id_order` = o.`id_order`
				WHERE o.`valid` = 1
				AND o.`invoice_date` 
					BETWEEN \''.pSQL($this->date['begin']).'\' 
					AND \''.pSQL($this->date['end']).'\'';
		
		$list = Db::getInstance()->executeS($query);
		
		$this->writeExportToFile($list);
	}
	
	/**
	 * Generate a line for the CSV for the global export  
	 * @var array $row - Line from Database query
	 * @var int $line_number - Request line generation
	 */
	private function createLine($row, $line_number)
	{
		$line = array();
		
		// Default Values
		$line[0] = $row['invoice_date'];
		$line[1] = Tools::getValue('journal'); 
		$line[2] = ''; // account number
		$line[3] = $row['invoice_number'];
		$line[4] = 0.00; // Credit TTC
		$line[5] = 0.00; // Debit HT (used for tax too)
		$line[6] = $row['transaction_id'];
		$line[7] = $row['payment_type'];
		$line[8] = $row['currency_code'];
		$line[9] = $row['wording'];
		
		// Override case depending of the whished line
		switch($line_number)
		{
			case 0:
				$line[2] = $row['account_client'];
				$line[4] = 'Wait Franck Commit';
				break;
			case 1:
				$line[2] = !empty($row['account']) ? $row['account'] : 
					Configuration::get('default_account_number', NULL, NULL, $row['id_shop']);
				// Force an empty string if Configuration send false
				$line[2] = empty($line[2]) ? '' : $linep[2];
				$line[5] = $row['product_price_ht'];
				break;
			case 2:
				$line[2] = $row['tax_accounting_account_number'];
				$line[5] = 'Wait Franck Commit';
				break;
		}
		return $line;
	}
	
	/**
	 * Build an proper list to be written into the export file 
	 * @var array $db_details - Content from datatbase
	 */
	private function buildGlobalExportlist($db_details)
	{
		// List use to write data in csv file
		$list = array();
		
		// Cache list to merge easily the content with the same accounting for different invoice number
		$acc_invoice_list = array();
		$num = 0;
		foreach($db_details as $row)
		{
			// Init the list
			if (!array_key_exists($row['invoice_number'], $acc_invoice_list))
				$acc_invoice_list[$row['invoice_number']] = array();
			
			// Need to Generate 3 lines for a product
			for ($i = 0; $i < 3; ++$i)
				// Create line for the two first line and check if a tax exist for the last one
				if ($i < 2 || ($i == 2 && $row['id_tax'] !== NULL))
				{
					$tmp = $this->createLine($row, $i);
					// Check if the account number hadn't already be use for this invoice number
					if (!array_key_exists($tmp[2], $acc_invoice_list[$tmp[3]]))
					{
						// Create a new entry and cache the account number for this invoice number
						$acc_invoice_list[$tmp[3]][$tmp[2]] = $num;
						$list[$num] = $tmp;
						++$num;
					}
					else
					{
						// Merge amount retrieving the position in the list of the invoice number
						$pos = $acc_invoice_list[$tmp[3]][$tmp[2]];
						if (!$i)
							$list[$pos][4] += $tmp[4];
						else
							$list[$pos][5] += $tmp[5];
					}
				}
		}
		return $list;
	}
	
	/**
	 * Start the global export type 
	 *
	 */
	private function runGlobalExport()
	{
		$query = '
			SELECT 
				od.`id_order`,
				o.`invoice_date`,
				CASE 
 					WHEN (acc_pzs.`account_number` != "" AND acc_pzs.`account_number` IS NOT NULL) THEN acc_pzs.`account_number`
 					WHEN (acc_zs.`account_number` != "" AND acc_zs.`account_number` IS NOT NULL) THEN acc_zs.`account_number`
 					ELSE  ""
				END AS account,
				CONCAT(\''.Configuration::get('PS_INVOICE_PREFIX').'\', LPAD(o.`invoice_number`, 6, "0")) AS invoice_number,
				o.`total_paid_real`,
				od.`product_price` AS product_price_ht,
				pcc.`transaction_id`,
				o.`payment` AS payment_type,
				currency.`iso_code` as currency_code,
				CONCAT(\''.pSQL($this->clientPrefix).'\', LPAD(customer.`id_customer`, 6, 0)) AS account_client,
				CASE 
 					WHEN (a.`company` != "" AND a.`company` IS NOT NULL) THEN a.`company`
					ELSE  a.`lastname`
				END AS wording,
				t.account_number AS tax_accounting_account_number,
				t.id_tax,
				o.id_shop
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'customer` customer ON customer.`id_customer` = o.`id_customer`
				LEFT JOIN `'._DB_PREFIX_.'address` a ON a.`id_customer` = o.`id_customer` 
				LEFT JOIN `'._DB_PREFIX_.'payment_cc` pcc ON pcc.`id_order` = o.`id_order`
				LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON od.`id_order` = o.`id_order`
				LEFT JOIN `'._DB_PREFIX_.'currency` currency ON currency.`id_currency` = o.`id_currency`
				LEFT JOIN `'._DB_PREFIX_.'order_detail_tax` odt ON odt.`id_order_detail` = od.`id_order_detail`
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON t.`id_tax` = odt.`id_tax`
				LEFT JOIN `'._DB_PREFIX_.'country` country ON country.`id_country` = a.`id_country`
				LEFT JOIN `'._DB_PREFIX_.'accounting_product_zone_shop` acc_pzs 
					ON (acc_pzs.`id_shop` = o.`id_shop`
					AND acc_pzs.`id_zone` = country.`id_zone`
					AND acc_pzs.`id_product` = od.`product_id`)
				LEFT JOIN `'._DB_PREFIX_.'accounting_zone_shop` acc_zs 
					ON (acc_zs.`id_shop` = o.`id_shop`
					AND acc_zs.`id_zone` = country.`id_zone`)
				WHERE o.`valid` = 1
				AND o.`invoice_date` 
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
	private function downloadFile($fileName)
	{
		$path = $this->downloadDir.$fileName;
		header('Content-Type: application/csv'); 
		header('Content-length: ' . filesize($path)); 
		header('Content-Disposition: attachment; filename="'.$fileName.'"');
		
		// Flush data unproper data page before reading the file
		ob_clean();
    flush();
    
		@readfile($path);
		exit();  
	}
}
