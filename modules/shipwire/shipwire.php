<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/* Security */
if (!defined('_PS_VERSION_'))
	exit;

spl_autoload_register('shipwireAutoload');

class Shipwire extends Module
{
	/** @var _cipherTool Helper Object to encrypt API KEY */
	private $_cipherTool;

	/** @var dParams Array replacing smarty (display parameters) */
	private $dParams = array();

	/** @const _SHIPWIRE_SERVER Shipwire server url */
	private $_configVars = array(
		'SHIPWIRE_API_SERVER' => '',
		'SHIPWIRE_API_USER' => '',
		'SHIPWIRE_API_PASSWD' => '',
		'SHIPWIRE_API_CONNECTED' => '',
		'SHIPWIRE_API_MODE' => '',
		'SHIPWIRE_WAREHOUSE' => '',
		'SHIPWIRE_ACCOUNT_NAME' => '',
		'SHIPWIRE_TRACKING_LAST_DATE' => '',
		'SHIPWIRE_COMMIT_ID' => '',
		'SHIPWIRE_SENT_ID' => '',
		'SHIPWIRE_DELIVERED_ID' => '',
	);

	/** @var _shipWireInventoryUpdate Shipwire Api Object */
	private $_shipWireInventoryUpdate;

	/******************************************************************/
	/** Construct Method **********************************************/
	/******************************************************************/
	public function __construct()
	{
		$this->_initContext();

		$this->name = 'shipwire';
		$this->tab = 'administration';
		$this->version = '1.1.3';
		$this->author = 'PrestaShop';

		parent::__construct();

		$this->displayName = $this->l('Shipwire');
		$this->description = $this->l('Enterprise logistics for everyone.');

		if (Configuration::get('PS_CIPHER_ALGORITHM'))
			$this->_cipherTool = new Rijndael(_RIJNDAEL_KEY_, _RIJNDAEL_IV_);
		else
			$this->_cipherTool = new Blowfish(_COOKIE_KEY_, _COOKIE_IV_);

		$this->_shipWireInventoryUpdate = new ShipwireInventoryUpdate(Configuration::get('SHIPWIRE_API_USER'), $this->_cipherTool->decrypt(Configuration::get('SHIPWIRE_API_PASSWD')));
		$this->_shipWireOrder = new ShipwireOrder();
	}

	/**
	 * @brief Make the module compatible 1.4/1.5
	 */
	private function _initContext()
	{
		$this->dParams['base_dir'] = __PS_BASE_URI__;

		if (class_exists('Context') && function_exists('getContent'))
			$this->context = Context::getContent();
		else
		{
			global $smarty, $cookie;

			$this->context = new StdClass();
			$this->context->smarty = $smarty;
			$this->context->cookie = $cookie;
			$this->context->shop = new StdClass();
			$this->context->shop->id = 1;
			$this->context->shop->id_group_shop = 1;
		}

		$this->_loadConfiguration();
	}

	private function _loadConfiguration()
	{
		foreach ($this->_configVars as $key => $v)
			$this->_configVars[$key] = Configuration::get($key);
	}

	/**
	 * @brief Install/Uninstall Configuration variables
	 *
	 * @param install True for installation, false for uninstall
	 *
	 * @return Success or failure
	 */
	private function _setupConfigVariables($install = true)
	{
		$configVars = array(
			'SHIPWIRE_API_SERVER' => '',
			'SHIPWIRE_API_USER' => '',
			'SHIPWIRE_API_PASSWD' => '',
			'SHIPWIRE_API_CONNECTED' => 0,
			'SHIPWIRE_ACCOUNT_NAME' => '',
			'SHIPWIRE_API_MODE' => 'Test',
			'SHIPWIRE_WAREHOUSE' => '00',
			'SHIPWIRE_TRACKING_LAST_DATE' => 0,
			'SHIPWIRE_COMMIT_ID' => 2,
			'SHIPWIRE_SENT_ID' => 4,
			'SHIPWIRE_DELIVERED_ID' => 5,
			'SHIPWIRE_LOG_LIFE' => 10,
		);

		$error = 0;
		foreach ($configVars as $varName => $value)
			if ($install)
				$error += Configuration::updateValue($varName, $value) ? 0 : 1;
			else
				$error += Configuration::deleteByName($varName) ? 0 : 1;

		return !$error;
	}

	/******************************************************************/
	/** Install / Uninstall Methods ***********************************/
	/******************************************************************/
	public function install()
	{
		// Setup config variable with 'install' flag on
		if (!$this->_setupConfigVariables(true))
			return false;

		if (!parent::install() || !$this->registerHook('backOfficeTop') || !$this->registerHook('updateOrderStatus'))
			return false;

		// Perform the sql install
		include(dirname(__FILE__).'/sql/sql-install.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->Execute($s))
				return false;

		return true;
	}

	/**
	 * @brief Uninstall function
	 *
	 * @return Success or failure
	 */
	public function uninstall()
	{
		// Uninstall parent and unregister Configuration
		if (!parent::uninstall())
			return false;

		// Unregister hook
		if (!$this->unregisterHook('backOfficeTop') || !$this->unregisterHook('updateOrderStatus'))
			return false;

		// Remove configuration variable with 'install' flag off
		if (!$this->_setupConfigVariables(false))
			return false;

		// Uninstall SQL
		include(dirname(__FILE__).'/sql/sql-uninstall.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->Execute($s))
				return false;

		return true;
	}

	/**
	 * @brief hookBackOfficeTop Implementation.
	 *
	 * Hook that allow to add script anywhere in the backoffice.
	 *
	 * @return Render to display
	 */
	public function hookBackOfficeTop()
	{
		$r = array();
		if (isset($_GET['id_order']))
			$r = Db::getInstance()->ExecuteS('SELECT `id_order`, `transaction_ref`, `order_ref`, `status`
									FROM `'._DB_PREFIX_.'shipwire_order`
									WHERE `id_order` = '.(int)$_GET['id_order']);

		$status = isset($r[0]['status']) && !empty($r[0]['status']) ? ucfirst($r[0]['status']) : $this->l('Not sent to Shipwire.');

		echo '
		<script type="text/javascript">
			$(function(){
				/* Add Shipwire fieldset to Order Details page */
				html = \'<br /><br /><fieldset>\' +
						\'<legend><img src="'.$this->_path.'logo.gif" alt="">'.$this->l('Shipwire Status').'</legend>\' +
							\'<b>Status:</b> '.Tools::safeOutput($status).'\' +
						\'</fieldset>\';

				if ($(\'#content select[name="id_order_state"]\').size())
					$(\'#content div:eq(3)\').append(html);

				$(\'.link-submit-form\').click(function(){
					$(this).parent(\'form\').submit();
				});

				/* jExcerpt v1.1 */
				length = 20;
				$(\'.jexcerpt\').each(function(){
					if ($(this).text().length > length)
					{
						// Create the .jexcerpt-long
						$(\'<div class="jexcerpt-long">\' + $(this).text() + \'</div>\').appendTo($(this).parent());

						excerpt = $(this).text().substring(0, length);
						$(this).text(excerpt + \'...\');
					}
				});

				$(\'.jexcerpt\').mouseover(function(){
					$(\'.jexcerpt-long\').hide();
					$(this).parent().attr(\'width\', $(this).parent().width());
					$(this).parent().find(\'.jexcerpt-long\')
						.css(\'left\', $(this).parent().offset().left)
						.css(\'top\', $(this).parent().offset().top)
						.show();
				});

				$(\'.jexcerpt-long\').live(\'mouseout\', function(){
					$(this).parent().find(\'.jexcerpt\').show();
					$(this).hide();
				});
			});
		</script>';
	}

	/**
	 * @brief hookUpdateOrderStatus Implementation.
	 *
	 * @return ?
	 */
	public function hookUpdateOrderStatus($params)
	{
		$orderStatusId = $params['newOrderStatus']->id;

		// We check the orderstatus
		if ($orderStatusId != $this->_configVars['SHIPWIRE_COMMIT_ID'])
			return false;

		// Check that the order was not already commited to Shipwire
		$r = Db::getInstance()->ExecuteS('SELECT `id_order`, `transaction_ref`, `order_ref`, `status`
									FROM `'._DB_PREFIX_.'shipwire_order`
									WHERE `id_order` = '.(int)$params['id_order']);
		if (!(isset($r[0]['transaction_ref']) && !empty($r[0]['transaction_ref'])))
		{
			$this->updateOrderStatus($params['id_order']);
			ShipwireTracking::updateTracking(true);
		}

		return true;
	}

	public function updateOrderStatus($idOrder, $refresh = false)
	{
		$order = new Order($idOrder);
		if (!$order->id)
			return false;

		$address = new Address($order->id_address_delivery);
		$customer = new Customer($order->id_customer);
		$carrier = new Carrier($order->id_carrier);
		$cart = new Cart($order->id_cart);

		$products = $cart->getProducts();

		$packageList = array();
		foreach ($products as $product)
		{
			$packageList[] = array(
				'code' => (!empty($product['reference']) ? pSQL($product['reference']) : (int)$product['id_product_attribute']), // SKU
				'quantity' => (int)$product['cart_quantity'],
			);
		}

		switch ($order->id_carrier)
		{
			default:
			case Configuration::get('SHIPWIRE_GD') :
				$shippingType = 'GD';
				break;
			case Configuration::get('SHIPWIRE_1D') :
				$shippingType = '1D';
				break;
			case Configuration::get('SHIPWIRE_2D') :
				$shippingType = '2D';
				break;
			case Configuration::get('SHIPWIRE_INTL') :
				$shippingType = 'INTL';
				break;
		}

		$this->_shipWireOrder->addOrder(array(
				'orderId' => $order->id,
				'name' => $customer->firstname.'.'.$customer->lastname,
				'address1' => $address->address1,
				'address2' => $address->address2,
				'city' => $address->city,
				'country' => $address->country,
				'zip' => $address->postcode,
				'phone' => $address->phone,
				'mail' => $customer->email,
				'shippingType' => $shippingType,
				'packageList' => $packageList,
			), $refresh);

		$r = $this->_shipWireOrder->sendData();

		if ($r['Status'])
			$this->_displayConfirmation($this->l('An error occured on the remote server: ').$r['ErrorMessage'], 'error');

		foreach ($r['OrderInformation'] as $o)
		{
			if ($o['@attributes']['number'] != $order->id)
				$this->_displayConfirmation($this->l('An unkown error occured with order Id.'), 'error');
			//$val = Db::getInstance()->getValue('SELECT `transaction_ref` FROM `'._DB_PREFIX_.'shipwire_order` WHERE `id_order` = '.(int)$order->id);

			$orderExists = Db::getInstance()->ExecuteS('SELECT `id_order` FROM `'._DB_PREFIX_.'shipwire_order` WHERE `id_order` = '.(int)$order->id.' LIMIT 1');
			if (isset($orderExists[0]['id_order']) && !empty($orderExists[0]['id_order']))
			{
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'shipwire_order`
									SET `transaction_ref` = \''.pSQL($o['@attributes']['id']).'\',
									`order_ref` = \''.pSQL($o['@attributes']['number']).'\',
									`status` = \''.pSQL($o['@attributes']['status']).'\'
									WHERE `id_order` = '.(int)$order->id);
			}
			else
			{
				Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'shipwire_order`
				(`id_order`, `transaction_ref`, `status`)
				VALUES (
				\''.pSQL($order->id).'\''
				.(isset($o['id']) ? ',\''.pSQL($o['id']).'\'' : ',\'\'')
				.(isset($o['status']) ? ',\''.pSQL($o['status']).'\'' : ',\'\'')
				.')');
			}
				$this->log((int)$order->id, $o['@attributes']['id']);
		}

		return true;
	}

	/**
	 * @brief Empty all tables of the module.
	 */
	private function _clearTables()
	{
		/// @todo : check if this method is used
		if (!Db::getInstance()->Execute('TRUNCATE `'._DB_PREFIX_.'shipwire_order`') ||
			Db::getInstance()->Execute('TRUNCATE `'._DB_PREFIX_.'shipwire_stock`'))
			return false;
		return true;
	}

	/**
	 * @brief Main Form Method
	 *
	 * @return Rendered form
	 */
	public function getContent()
	{
		if (Tools::isSubmit('SubmitShipwireSettings'))
		{
			if (Validate::isEmail(Tools::getValue('shipwire_api_user')))
			{
				Configuration::updateValue('SHIPWIRE_API_CONNECTED', 0);
				Configuration::updateValue('SHIPWIRE_API_USER',
					Tools::getValue('shipwire_api_user'));
				Configuration::updateValue('SHIPWIRE_API_PASSWD',
					$this->_cipherTool->encrypt(Tools::getValue('shipwire_api_passwd')));

				$this->dParams['confirmMessage'] = $this->_displayConfirmation();
				Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'shipwire_order`');
				Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'shipwire_stock`');
			}
			else
				$this->dParams['confirmMessage'] = $this->_displayConfirmation($this->l('Please enter a valid email address.'), 'error');
		}
		elseif (Tools::isSubmit('SubmitShipwireTestConnection'))
		{
			$connectionTestResult = $this->_testConnection();
		}
		elseif (Tools::isSubmit('SubmitShipwireOptions'))
		{
			$duplicates = 0;

			Configuration::updateValue('SHIPWIRE_COMMIT_ID', (int)Tools::getValue('shipwire_commit_id'));
			Configuration::updateValue('SHIPWIRE_SENT_ID', (int)Tools::getValue('shipwire_sent_id'));
			Configuration::updateValue('SHIPWIRE_DELIVERED_ID', (int)Tools::getValue('shipwire_delivered_id'));
			Configuration::updateValue('SHIPWIRE_LOG_LIFE', ((int)Tools::getValue('shipwire_log_life') < 1 ? 1 : (int)Tools::getValue('shipwire_log_life')));
			// Carrier selection
			//GD
			if ((Tools::getValue('shipwire_gd') != Tools::getValue('shipwire_1d')
				&& Tools::getValue('shipwire_gd') != Tools::getValue('shipwire_2d')
				&& Tools::getValue('shipwire_gd') != Tools::getValue('shipwire_intl'))
				|| Tools::getValue('shipwire_gd') == 0)
					Configuration::updateValue('SHIPWIRE_GD', (int)Tools::getValue('shipwire_gd'));
			else
				$duplicates = 1;

			//1d
			if ((Tools::getValue('shipwire_1d') != Tools::getValue('shipwire_gd')
				&& Tools::getValue('shipwire_1d') != Tools::getValue('shipwire_2d')
				&& Tools::getValue('shipwire_1d') != Tools::getValue('shipwire_intl'))
				|| Tools::getValue('shipwire_1d') == 0)
					Configuration::updateValue('SHIPWIRE_1D', (int)Tools::getValue('shipwire_1d'));
			else
				$duplicates = 1;

			//2d
			if ((Tools::getValue('shipwire_2d') != Tools::getValue('shipwire_gd')
				&& Tools::getValue('shipwire_2d') != Tools::getValue('shipwire_1d')
				&& Tools::getValue('shipwire_2d') != Tools::getValue('shipwire_intl'))
				|| Tools::getValue('shipwire_2d') == 0)
					Configuration::updateValue('SHIPWIRE_2D', (int)Tools::getValue('shipwire_2d'));
			else
				$duplicates = 1;

			//INTL
			if ((Tools::getValue('shipwire_intl') != Tools::getValue('shipwire_gd')
				&& Tools::getValue('shipwire_intl') != Tools::getValue('shipwire_1d')
				&& Tools::getValue('shipwire_intl') != Tools::getValue('shipwire_2d'))
				|| Tools::getValue('shipwire_intl') == 0)
					Configuration::updateValue('SHIPWIRE_INTL', (int)Tools::getValue('shipwire_intl'));
			else
				$duplicates = 1;

			//Check for errors
			if ($duplicates)
				$this->dParams['confirmMessage'] = $this->_displayConfirmation($this->l('The shipping methods were not saved
				because duplicate values are not allowed.'), 'warn');
			else
				$this->dParams['confirmMessage'] = $this->_displayConfirmation();
		}
		elseif (Tools::isSubmit('SubmitUpdateStock'))
		{
			if ($this->_shipWireInventoryUpdate->getInventory())
				$this->dParams['confirmMessage'] = $this->_displayConfirmation($this->l('Stock updated.'));
			else
				$this->dParams['confirmMessage'] = $this->_displayConfirmation($this->l('Stock update failed'), 'error');
		}
		elseif (Tools::isSubmit('SubmitUpdateTracking'))
		{
			if (ShipwireTracking::updateTracking(true))
				$this->dParams['confirmMessage'] = $this->_displayConfirmation($this->l('Transactions updated.'));
			else
				$this->dParams['confirmMessage'] = $this->_displayConfirmation($this->l('Transactions update failed.'), 'error');
		}
		elseif (Tools::isSubmit('resend_id_order'))
		{
			if ($this->updateOrderStatus($_POST['resend_id_order']))
				$this->dParams['confirmMessage'] = $this->_displayConfirmation($this->l('Order successfully resent.'));
			else
				$this->dParams['confirmMessage'] = $this->_displayConfirmation($this->l('Error while sending. Try again later.'), 'error');
		}
		elseif (Tools::isSubmit('SubmitResendFailedTransactions'))
		{
			$d = Db::getInstance()->ExecuteS('SELECT `id_order`
				FROM `'._DB_PREFIX_.'shipwire_order`
				WHERE `transaction_ref` IS NULL OR `transaction_ref` = \'\'');
			foreach ($d as $line)
				$this->updateOrderStatus($line['id_order']);

			if (ShipwireTracking::updateTracking(true))
				$this->dParams['confirmMessage'] = $this->_displayConfirmation($this->l('Transactions successfully resent.'));
			else
				$this->dParams['confirmMessage'] = $this->_displayConfirmation($this->l('Error resending transactions.'), 'error');
		}
		elseif (Tools::isSubmit('SubmitCleanLogs'))
		{
			if (Db::getInstance()->Execute('TRUNCATE `'._DB_PREFIX_.'shipwire_log`'))
				$this->dParams['confirmMessage'] = $this->_displayConfirmation($this->l('Logs empty.'));
			else
				$this->dParams['confirmMessage'] = $this->_displayConfirmation($this->l('Error deleting logs.'), 'error');
		}

		$confValues = Configuration::getMultiple(array(
										'SHIPWIRE_API_USER',
										'SHIPWIRE_API_PASSWD'));

		/// @todo : check if these assigns are necessary
		$this->dParams['defaultOriginServerURL'] = 'http://'.Configuration::get('PS_SHOP_DOMAIN').__PS_BASE_URI__;

		$this->dParams['serverRequestUri'] = Tools::safeOutput($_SERVER['REQUEST_URI']);
		$this->dParams['displayName'] = Tools::safeOutput($this->displayName);

		if (isset($connectionTestResult))
			$this->dParams['connectionTestResult'] = $connectionTestResult;

		if (isset($confValues['SHIPWIRE_API_USER']))
			$this->dParams['apiEmail'] = Tools::safeOutput($confValues['SHIPWIRE_API_USER']);
		if (isset($confValues['SHIPWIRE_API_PASSWD']))
			$this->dParams['apiPasswd'] = Tools::safeOutput($this->_cipherTool->decrypt($confValues['SHIPWIRE_API_PASSWD']));
		return $this->_displayContent();
	}

	/**
	 * @brief Test the conenction to Shipwire and the credentials.
	 *
	 * In order to test that, we just try to get the pullzones amd we check if
	 * the server reply the errorCode 0. If can't connect or other errorCode, then
	 * there is something wrong.
	 *
	 * @return True if the connection is OK, false otherwise
	 */
	private function _testConnection()
	{

		if ($this->_shipWireInventoryUpdate->getInventory('Status') == 'Error')
			return array('<img src="../img/admin/forbbiden.gif" alt="" />
			<b style="color: #CC0000;">'.$this->l('Connection Test Failed.').'</b>',
				'#FFD8D8', false);

		Configuration::updateValue('SHIPWIRE_API_CONNECTED', 1);
		return array('<img src="../img/admin/ok.gif" alt="" />
			<b style="color: green;">'.$this->l('Congratulations! Your store is now linked to Shipwire.').'
			<img src="http://www.prestashop.com/modules/'.$this->name.'.png?api_user='.urlencode(Configuration::get('SHIPWIRE_API_USER')).
			'" style="display: none;" />
			</b>',
			'#D6F5D6', true);

	}

	/*
	** Display a custom message for settings update
	** $text string Text to be displayed in the message
	** $type string (confirm|warn|error) Decides what color will the
	** message have (green|yellow)
	*/
	private function _displayConfirmation($text = '', $type = 'confirm')
	{
		switch ($type)
		{
			case 'confirm':
				$img = 'ok.gif';
				break ;
			case 'warn':
				$img = 'warn2.png';
				break ;
			case 'error':
				$img = 'disabled.gif';
				break ;
			default:
				die('Invalid type.');
		}

		return array(
			'class' => Tools::safeOutput($type),
			'img' => Tools::safeOutput($img),
			'text' => (empty($text) ? $this->l('Settings updated') : $text)
		);
	}

	/******************************************************************/
	/** Web-service methods *******************************************/
	/******************************************************************/

	/******************************************************************/
	/** Tools methods *************************************************/
	/******************************************************************/
	public function getCurrentURL($htmlEntities = false)
	{
		$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		return (!empty($_SERVER['HTTPS']) ? 'https' : 'http').
			'://'.($htmlEntities ? preg_replace('/&/', '&amp;', $url): $url);
	}

	public function log($orderId, $transactionId)
	{
		return Db::getInstance()->Execute('REPLACE INTO `'._DB_PREFIX_.'shipwire_log`
					(`transaction_ref`, `id_order`, `date_added`)
					VALUES (\''.pSQL($transactionId).'\',
					'.(int)$orderId.',
					\''.date('Y-m-d H:i:s').'\')');
	}

	/******************************************************************/
	/** Display methods *************************************************/
	/******************************************************************/
	private function _displayContent()
	{
		$cookie = $this->context->cookie;

		$buffer = '
		<link rel="stylesheet" type="text/css" href="'.$this->dParams['base_dir'].'modules/shipwire/style.css" />';

		if (isset($this->dParams['confirmMessage']))
			$buffer .= '
			<div class="conf '.$this->dParams['confirmMessage']['class'].'">
				<img src="../img/admin/'.$this->dParams['confirmMessage']['img'].'" alt="" title="" />
				'.$this->dParams['confirmMessage']['text'].'
			</div>';
		$buffer .= '
			<img src="http://www.prestashop.com/modules/'.$this->name.'.png?url_site='.Tools::safeOutput($_SERVER['SERVER_NAME']).'&id_lang='.(int)$this->context->cookie->id_lang.'" alt="" style="display:none"/>
			<h2>'.$this->dParams['displayName'].'</h2>
			<fieldset>
				<legend><img src="../img/admin/help.png" alt="" />'.$this->l('Help').'</legend>
					<a style="float: right;" target="_blank" href="http://www.prestashop.com/en/industry-partners/shipping/shipwire"><img alt="" src="../modules/shipwire/shipwire_logo.png"></a>
					<h3>'.$this->l('How to configure Shipwire Module:').'</h3>
					- '.$this->l('Fill the Shipwire Email and Password fields with those provided by Shipwire.').'<br />
					- '.$this->l('Click on the "Save Settings" button and then Test your connection.').'<br /><br />
					<h3>'.$this->l('Module purpose:').'</h3>
					'.$this->l('Shipwire\'s enterprise logistics platform will help lower your cost, grow your sales, and deliver a great experience for your customers. Shipwire ensures your orders ship faster — at a lower cost.').'<br />'.
			$this->l('Easily connect your sales channels and expand into emerging ones like Social Commerce. Use Shipwire\'s global warehouses to reach more customers faster — at a lower cost.').'<br />'.
			$this->l('Whatever the volume, Shipwire\'s automated warehouses process your orders quickly and accurately.').'<br /><br />
					<br />
			</fieldset>
			<br />
			<form action="'.$this->dParams['serverRequestUri'].'" method="post">
				<fieldset class="width2 shipwire_fieldset">
					<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Shipwire Credentials').'</legend>';

		if (isset($this->dParams['connectionTestResult']))
			$buffer .= '
					<div id="test_connection" style="background: '.Tools::safeOutput($this->dParams['connectionTestResult'][1]).';">
					'.Tools::safeOutput($this->dParams['connectionTestResult'][0]).'
					</div>';

		$buffer .= '
					<table border="0" cellspacing="5">
						<tr>
							<td class="shipwire_column">'.$this->l('Email').'</td>
							<td><input type="text" name="shipwire_api_user" value="'.(isset($this->dParams['apiEmail']) ? Tools::safeOutput($this->dParams['apiEmail']) : '').'" /></td>
						</tr>
						<tr>
							<td class="shipwire_column">'.$this->l('Password').'</td>
							<td>
								<input type="password" name="shipwire_api_passwd" value="'.(isset($this->dParams['apiPasswd']) ? Tools::safeOutput($this->dParams['apiPasswd']) : '').'"/>
							</td>
						</tr>
					</table>
					<center><input type="submit" class="button shipwire_button" name="SubmitShipwireSettings" value="'.$this->l('Save Settings').'" /></center>
					<hr size="1" style="margin: 14px auto;" noshade />
					<center><img src="../img/admin/exchangesrate.gif" alt="" /> <input type="submit" id="shipwire_test_connection" class="button shipwire_button" name="SubmitShipwireTestConnection" value="'.$this->l('Click here to Test Connection').'" style="margin-top: 0;" /></center>
				</fieldset>
			<br />
			<fieldset class="width2 shipwire_fieldset">
			<legend><img src="../img/admin/statsettings.gif" alt="" />'.$this->l('Shipwire and PrestaShop').'</legend>
				<p><a href="http://www.prestashop.com/en/industry-partners/shipping/shipwire" target="_blank">'.$this->l('Learn more about Shipwire at PrestaShop.com').'</a></p>
			</fieldset>
			</form>';

		$buffer .= '
		<br />
			<form action="'.htmlspecialchars_decode(Tools::safeOutput($this->dParams['serverRequestUri'])).'" method="post">
			<fieldset class="width2 shipwire_fieldset">
				<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Options').'</legend>

				<h3 style="margin: 0px;">'.$this->l('Order statuses that will interact with Shipwire').'</h3>
				<span style="font-style: italic; font-size: 11px; color: #888;">'.$this->l('When an order status is updated in Shipwire, the following statuses will be applied to the order in your store.').'</span><br /><br />';

				/* Check if the order status exist */
				$orderStatus = Db::getInstance()->ExecuteS('SELECT `id_order_state`, `name` FROM '._DB_PREFIX_.'order_state_lang WHERE `id_lang` = '.(int)$cookie->id_lang);
				$orderStatusList = array();
				foreach ($orderStatus as $v)
					$orderStatusList[$v['id_order_state']] = Tools::safeOutput($v['name']);
				$buffer .= '
				<table>
					<th style="text-align: right; padding-right: 65px; border: 1px solid #000;">'.$this->l('Action').'</th>
					<th style="text-align: left; border: 1px solid #000; padding: 0px 15px;">'.$this->l('Order status in your store').'</th>
					<tr>
						<td class="shipwire_column">'.$this->l('Commit order to Shipwire').':</td>
						<td>
							<select id="shipwire_commit_id" name="shipwire_commit_id">';
							foreach ($orderStatusList as $k => $name)
								$buffer .= '
								<option'.(isset($orderStatusList[Configuration::get('SHIPWIRE_COMMIT_ID')]) && Configuration::get('SHIPWIRE_COMMIT_ID') == $k ? ' selected="selected"' : '' ).' value="'.Tools::safeOutput($k).'">'.Tools::safeOutput(html_entity_decode($name, ENT_COMPAT, 'utf-8')).'</option>';
							$buffer .= '
							</select>
						</td>
					</tr>
					<tr>
						<td class="shipwire_column">'.$this->l('Set order as SHIPPED in your store').':</td>
						<td>
							<select id="shipwire_sent_id" name="shipwire_sent_id">';
							foreach ($orderStatusList as $k => $name)
								$buffer .= '
								<option'.(isset($orderStatusList[Configuration::get('SHIPWIRE_SENT_ID')]) && Configuration::get('SHIPWIRE_SENT_ID') == $k ? ' selected="selected"' : '' ).' value="'.Tools::safeOutput($k).'">'.Tools::safeOutput(html_entity_decode($name, ENT_COMPAT, 'utf-8')).'</option>';
							$buffer .= '
							</select>
						</td>
					</tr>';
					/// There's no DELIVERED status at the moment thru SHIPWIRE API
					/*
					<tr>
						<td class="shipwire_column">'.$this->l('Set order as DELIVERED in your store').':</td>
						<td>
							<select id="shipwire_delivered_id" name="shipwire_delivered_id">';
							foreach ($orderStatusList as $k => $name)
								$buffer .= '
								<option'.(isset($orderStatusList[Configuration::get('SHIPWIRE_DELIVERED_ID')]) && Configuration::get('SHIPWIRE_DELIVERED_ID') == $k ? ' selected="selected"' : '' ).' value="'.$k.'">'.$name.'</option>';
							$buffer .= '
							</select>
						</td>
					</tr>
					*/
				$buffer .= '
				</table>
				<br />
				<div style="border-top: 1px solid #000; margin-bottom: 10px;"></div>
				<h3>'.$this->l('Logs life').'</h3>
				<table>
					<tr>
						<td class="shipwire_column" style="width: 230px;">'.$this->l('Delete transaction logs every').':</td>
						<td>
							<input type="text" name="shipwire_log_life" id="shipwire_log_life" style="width: 50px;" value="'.
							(int)Configuration::get('SHIPWIRE_LOG_LIFE').'"/>
							<span style="font-style: italic; font-size: 11px; color: #888;">'.$this->l('days (recommended: 30 days)').'</span>
						</td>
					</tr>
				</table>';

				// Get Carriers
				$carriers = Db::getInstance()->ExecuteS('SELECT `id_carrier`, `name` FROM `'._DB_PREFIX_.'carrier` WHERE `deleted` = 0');
				array_unshift($carriers, array('id_carrier' => 0, 'name' => $this->l('--Select one--')));

				foreach ($carriers as $k => $c)
					if (empty($c['name']))
						unset($carriers[$k]);

				$buffer .= '
				<br />
				<div style="border-top: 1px solid #000; margin-bottom: 10px;"></div>
				<h3 style="margin-bottom: 0px;">'.$this->l('Shipping Methods').'</h3>
				<div style="font-style: italic; font-size: 11px; color: #888; margin-bottom: 10px;">'.
				$this->l('Select the shipping methods that you will accept (They have to be unique for each.)').'
				</div>

				<table id="shipping_methods">
					<tr>
						<td class="shipwire_column" style="width: 230px;">'.$this->l('Ground shipping').':</td>
						<td>
							<select name="shipwire_gd" id="shipwire_gd">';
							foreach ($carriers as $carrier)
								$buffer .= '
								<option value="'.Tools::safeOutput($carrier['id_carrier']).'"'.($carrier['id_carrier'] == Configuration::get('SHIPWIRE_GD') ?
								' selected="selected"' : '').'>'.htmlspecialchars_decode(Tools::safeOutput($carrier['name'])).'</option>';
							$buffer .=
							'</select>
						</td>
					</tr>
					<tr>
						<td class="shipwire_column" style="width: 230px;">'.$this->l('Next day delivery').':</td>
						<td>
							<select name="shipwire_1d" id="shipwire_1d">';
							foreach ($carriers as $carrier)
								$buffer .= '
								<option value="'.(int)$carrier['id_carrier'].'"'.($carrier['id_carrier'] == Configuration::get('SHIPWIRE_1D') ?
								' selected="selected"' : '').'>'.htmlspecialchars_decode(Tools::safeOutput($carrier['name'])).'</option>';
							$buffer .=
							'</select>
						</td>
					</tr>
					<tr>
						<td class="shipwire_column" style="width: 230px;">'.$this->l('Second day delivery').':</td>
						<td>
							<select name="shipwire_2d" id="shipwire_2d">';
							foreach ($carriers as $carrier)
								$buffer .= '
								<option value="'.(int)$carrier['id_carrier'].'"'.($carrier['id_carrier'] == Configuration::get('SHIPWIRE_2D') ?
								' selected="selected"' : '').'>'.htmlspecialchars_decode(Tools::safeOutput($carrier['name'])).'</option>';
							$buffer .=
							'</select>
						</td>
					</tr>
					<tr>
						<td class="shipwire_column" style="width: 230px;">'.$this->l('International delivery').':</td>
						<td>
							<select name="shipwire_intl" id="shipwire_intl">';
							foreach ($carriers as $carrier)
								$buffer .= '
								<option value="'.(int)$carrier['id_carrier'].'"'.($carrier['id_carrier'] == Configuration::get('SHIPWIRE_INTL') ?
								' selected="selected"' : '').'>'.htmlspecialchars_decode(Tools::safeOutput($carrier['name'])).'</option>';
							$buffer .=
							'</select>
						</td>
					</tr>
				</table>

				<div class="clear"></div>
				<br />
				<center><input type="submit" id="SubmitShipwireOptions" name="SubmitShipwireOptions" value="'.$this->l('Save Options').'" class="button MR10"/><input type="submit" id="SubmitCleanLogs" name="SubmitCleanLogs" value="'.$this->l('Clean Logs').'" class="button"/></center>
			</fieldset>
		';

		// Product list from Shipwire
		if (Configuration::get('SHIPWIRE_API_CONNECTED'))
		{
			$products = $this->_shipWireInventoryUpdate->getInventory('Product');

			$buffer .= '
				<br /><br />
				<input type="submit" id="SubmitUpdateStock" name="SubmitUpdateStock" value="'.$this->l('Update Stock').'" class="button R" />
				<fieldset>
					<legend><img src="../img/admin/statsettings.gif" alt="" />'.$this->l('Shipwire Stock').'</legend>
					<div style="overflow-x: scroll; width: 870px; padding-bottom: 20px;">';


					$buffer .= '
						<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" class="spaced-table2">
							<tr align="left" valign="top">
								<th>Code (SKU)</th>
								<th>Quantity</th>
								<th>Pending</th>
								<th>Good</th>
								<th>Backordered</th>
								<th>Reserved</th>
								<th>Shipping</th>
								<th>Shipped</th>
								<th>Consuming</th>
								<th>Creating</th>
								<th>Consumed</th>
								<th>Created</th>
								<th>Available Date</th>
								<th>Shipped Last Day</th>
								<th>Shipped Last Week</th>
								<th>Shipped Last 4 Weeks</th>
								<th>Ordered Last Day</th>
								<th>Ordered Last Week</th>
								<th>Ordered Last 4 Weeks</th>
							</tr>';

			if (is_array($products))
				foreach ($products as $product)
				{
					$buffer .= '
							<tr align="left" valign="top">';

					if (isset($product['@attributes']) && count($product['@attributes']) && is_array($product['@attributes']))
						foreach ($product['@attributes'] as $k => $p)
							$buffer .= '
								<td>'.Tools::safeOutput($p).'</td>';
						$buffer .= '
							</tr>';
				}

			$buffer .= '</table>';

			$buffer .= '';

			$buffer .='
					</div>
				</fieldset>
				</form>';

			$buffer .= '
				<br /><br />
				<form action="'.$this->dParams['serverRequestUri'].'" method="post">
				<input type="submit" id="SubmitUpdateTracking" name="SubmitUpdateTracking" value="'.$this->l('Refresh Transactions').'" class="button R ML10" />
				<input type="submit" id="SubmitResendFailedTransactions" name="SubmitResendFailedTransactions" value="'.$this->l('Resend Failed Transactions').'" class="button R" />
				</form>
				<fieldset>
					<legend><img src="../img/admin/statsettings.gif" alt="" />'.$this->l('Shipwire Transactions').'</legend>
					<div style="overflow-x: scroll; width: 870px; padding-bottom: 20px;">';

			$r = Db::getInstance()->ExecuteS('SELECT `id_order`, `transaction_ref`, `tracking_number`,
											`status`, `shipped`, `shipper`, `shipDate`, `expectedDeliveryDate`,
											`href`, `shipperFullName`
									FROM `'._DB_PREFIX_.'shipwire_order`');
			if (count($r))
			{
				$buffer .= '

						<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" class="spaced-table3">
							<tr align="left" valign="top">
								<th class="small"></th>
								<th class="medium">Order Id</th>
								<th>Transaction Ref</th>
								<th>Tracking Number</th>
								<th>Status</th>
								<th>Shipped</th>
								<th>Shipper</th>
								<th>Ship Date</th>
								<th>Expected Delivery Date</th>
								<th>Tracking URL</th>
								<th>Shipper Full Name</th>
							</tr>';

				foreach ($r as $k => $d)
				{
					$buffer .= '<tr><td class="small"><form action="'.$this->dParams['serverRequestUri'].'" method="post">'.(empty($r[$k]['transaction_ref']) ? '<input type="hidden" value="'.$r[$k]['id_order'].'" name="resend_id_order" /><a href="Javascript:void(0)" class="link-submit-form"><img src="../img/admin/warning.gif" alt="" title="'.$this->l('Click here to resend the order to Shipwire.').'" /></a>' : '<img src="../img/admin/enabled-2.gif" alt="" />').'</form></td>';
					foreach ($d as $key => $value)
							$buffer .= '<td'.($key == 'id_order' ? ' class="medium"' : '').'><div class="jexcerpt">'.($key == 'id_order' ? '<a href="?tab=AdminOrders&id_order='.Tools::safeOutput($value).'&vieworder&token='.Tools::getAdminTokenLite('AdminOrders').'">'.Tools::safeOutput($value).'</a>' : (empty($value) ? 'N/A' : Tools::safeOutput($value))).'</div></td>';
					$buffer .= '</tr>';
				}

				$buffer .= '</table>
						</div>';
			}
			else
				$buffer .= '<div style="color: #777">'.$this->l('No transactions available.').'</div>';

			$buffer .= '
				</fieldset>
				<br /><br /><br />
				<fieldset>
					<legend><img src="../img/admin/tab-tools.gif" alt="" />'.$this->l('Shipwire Cronjob').'</legend>
					'.$this->l('Use PrestaShop\'s web-service to update your stock and order statuses. Place this URL in crontab (suggested frequency: every 5 hrs.) or access it manually daily:').'<br />
					<b><a href="'.(Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').Configuration::get('PS_SHOP_DOMAIN').'/modules/shipwire/cronjob_update.php?secure_key='.md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME')).'" target="_blank">
					'.(Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').Configuration::get('PS_SHOP_DOMAIN').'/modules/shipwire/cronjob_update.php?secure_key='.md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME')).'</a></b>
				</fieldset>
				';
		}

		return $buffer;
	}

}

function shipwireAutoload($className)
{
	$className = str_replace(chr(0), '', $className);
	if (!preg_match('/^\w+$/', $className))
		die('In2valid classname.'.$className);

	$moduleDir = dirname(__FILE__).'/';

	if (file_exists($moduleDir.'lib/'.$className.'.php'))
		require_once($moduleDir.'lib/'.$className.'.php');
	else
		__autoload($className);
}
