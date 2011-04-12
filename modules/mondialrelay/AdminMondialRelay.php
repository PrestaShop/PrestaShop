<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once('MondialRelayClass.php');

@set_time_limit(0);

function _get($type, $host, $port = '80', $path = '/', $data = '') 
{
    $d = '';
	$_err = 'lib sockets::'.__FUNCTION__.'(): ';
    switch($type)
    {
    	case 'http': $type = '';
    	case 'ssl': continue;
    	default: die($_err.'bad $type');
    }
    if(!ctype_digit($port))
    	die($_err.'bad port');
   
    $fp = fsockopen($host, $port, $errno, $errstr, $timeout=60);
    if(!$fp)
    	die($_err.$errstr.$errno);
    else
    {
        fputs($fp, "POST $path HTTP/1.1\r\n");
        fputs($fp, "Host: $host\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: ".strlen($data)."\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $data."\r\n\r\n");
       
        while(!feof($fp)) $d .= fgets($fp,4096);
        fclose($fp);
    } 
	
	$ndata = explode(":THISTAG:",$d);
	return $ndata[1];
} 

function free_MR_chaine($mystring)
{

	$mystring = strip_tags($mystring);
	$mystring = htmlentities($mystring, ENT_NOQUOTES, 'utf-8');
	$mystring = preg_replace('#\&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring)\;#', '\1', $mystring);
	$mystring = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $mystring);
	$mystring = preg_replace('#\&[^;]+\;#', '', $mystring);


	$mystring = str_replace('\'', ' ', $mystring);
	$mystring = str_replace('"', ' ', $mystring);

	$OldPattern0 = array("�","�","�","�","�","�","�","�","�","�","\"","�","�",
		                "�","�","�","�","�","�","�","�","�","�","�","�","�",
						"�","�","�","�","�");
	$mystring = str_replace($OldPattern0 , ' ',$mystring);
	return $mystring;
}

class AdminMondialRelay extends AdminTab
{
	private $displayInfos = true;

	public function __construct()
	{
		$this->table = 'mr_selected';
		$this->className = 'MondialRelayClass';

		parent::__construct();
	}

	private function _delete_history()
	{
		unset($result);
		if (isset($_POST['history']))
			foreach($_POST['history'] as $field)
				if (isset($field['selected']) AND $field['selected'] == '1')
					$query = Db::getInstance()->Execute("DELETE FROM `" . _DB_PREFIX_ ."mr_historique` WHERE id='".(int)($field['id'])."';");
	}

	private function _generateetiquettes()
	{
		unset($result);
		$urla4 = '';
    	$urla5 = '';
		 
		include_once(dirname(__FILE__).'/mondialrelay.php');
		$mondialrelay = new MondialRelay();

		echo '<p>' . $mondialrelay->getL('List of orders recognized') . '</p>';

		$mr_Enseigne_WebService = Configuration::get('MR_ENSEIGNE_WEBSERVICE');
		$mr_code_marque = Configuration::get('MR_CODE_MARQUE');
		$mr_Key_WebService = Configuration::get('MR_KEY_WEBSERVICE');
		$mr_Langage = Configuration::get('MR_LANGUAGE');
		$mr_Ad1 = Configuration::get('PS_SHOP_NAME');
		$mr_Ad2 = '';
		$mr_Ad3 = Configuration::get('PS_SHOP_ADDR1');
		$mr_Ad4 = Configuration::get('PS_SHOP_ADDR2');
		$mr_Ville = Configuration::get('PS_SHOP_CITY');
		$mr_CP = Configuration::get('PS_SHOP_CODE');
		$mr_Pays = Db::getInstance()->getValue('SELECT c.`iso_code`
												FROM `'._DB_PREFIX_.'country` c
												JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country`)
												WHERE cl.`name` = "'.pSQL(Configuration::get('PS_SHOP_COUNTRY')).'"
												AND cl.`id_lang` = '.(int)(Configuration::get('PS_LANG_DEFAULT')));
		$mr_Tel1 = Configuration::get('PS_SHOP_PHONE');
		$mr_Tel2 = '';
		$mr_Mail = Configuration::get('PS_SHOP_EMAIL');

		$concatener_id_order = "";
		$concatener_exp_num = "";
		$concatener_error = "";
		if (!empty($mr_Ad1) AND !empty($mr_Ad3) AND !empty($mr_Ville) AND !empty($mr_CP) AND !empty($mr_Pays))
		{
			foreach($_POST['order'] as $field)
			{
				if (is_numeric($field['weight']) AND $field['weight'] > 0 AND isset($field['selected']) AND $field['selected'] == '1')
				{
					$customer = new Customer((int)($field['id_customer']));
					$addressliv = new Address((int)($field['id_address_delivery']));
					$country = new Country((int)($addressliv->id_country));
					$params = 'mr_Enseigne_WebService='.urlencode(free_MR_chaine($mr_Enseigne_WebService));
					$params .= '&mr_code_marque='.urlencode(free_MR_chaine($mr_code_marque));
					$params .= '&mr_Key_WebService='.urlencode(free_MR_chaine($mr_Key_WebService));
					$params .= '&ModeCol='.urlencode(free_MR_chaine($field['mr_ModeCol']));
					$params .= '&ModeLiv='.(urlencode(free_MR_chaine($field['mr_ModeLiv'])) == 'LD1' ? 'LDR' : urlencode(free_MR_chaine($field['mr_ModeLiv'])));
					$params .= '&NDossier='.urlencode(free_MR_chaine($field['id']));
					$params .= '&NClient='.urlencode(free_MR_chaine($field['id_customer']));
					$params .= '&Expe_Langage='.urlencode(free_MR_chaine($mr_Langage));
					$params .= '&Expe_Ad1='.urlencode(free_MR_chaine($mr_Ad1));
					$params .= '&Expe_Ad2='.urlencode(free_MR_chaine($mr_Ad2));
					$params .= '&Expe_Ad3='.urlencode(free_MR_chaine($mr_Ad3));
					$params .= '&Expe_Ad4='.urlencode(free_MR_chaine($mr_Ad4));
					$params .= '&Expe_Ville='.urlencode(free_MR_chaine($mr_Ville));
					$params .= '&Expe_CP='.urlencode(free_MR_chaine($mr_CP));
					$params .= '&Expe_Pays='.urlencode(free_MR_chaine($mr_Pays));
					$params .= '&Expe_Tel1='.urlencode(free_MR_chaine($mr_Tel1));
					$params .= '&Expe_Tel2='.urlencode(free_MR_chaine($mr_Tel2));
					$params .= '&Expe_Mail='.urlencode(free_MR_chaine($mr_Mail));
					$params .= '&Dest_Langage='.urlencode(free_MR_chaine($country->iso_code));
					$params .= '&Dest_Ad1='.urlencode(strtoupper(substr(free_MR_chaine($addressliv->lastname).' '.free_MR_chaine($addressliv->firstname), 0, 32)));
					$params .= '&Dest_Ad2='.urlencode(strtoupper(substr(free_MR_chaine($addressliv->address2), 0, 32)));
					$params .= '&Dest_Ad3='.urlencode(strtoupper(substr(free_MR_chaine($addressliv->address1), 0, 32)));
					$params .= '&Dest_Ad4='.urlencode(strtoupper(substr(substr(free_MR_chaine($addressliv->address1), 32), 0, 32)));
					$params .= '&Dest_Ville='.urlencode(strtoupper(substr(free_MR_chaine($addressliv->city), 0, 26)));
					$params .= '&Dest_CP='.urlencode(strtoupper(substr(free_MR_chaine($addressliv->postcode), 0, 5)));
					$params .= '&Dest_Pays='.urlencode($country->iso_code);
					$params .= '&Dest_Tel1='.urlencode(free_MR_chaine($addressliv->phone ? $addressliv->phone : ''));
					$params .= '&Dest_Tel2='.urlencode(free_MR_chaine($addressliv->phone_mobile ? $addressliv->phone_mobile : ''));
					$params .= '&Dest_Mail='.urlencode(free_MR_chaine(strtoupper(substr($customer->email, 0, 70))));
					while (strlen($field['weight']) < 3)
						$field['weight'] = '0'.$field['weight'];
					$params .= '&Poids='.urlencode(free_MR_chaine($field['weight']));
					$params .= '&Longueur=';
					$params .= '&Taille=';
					$params .= '&NbColis=1';
					$params .= '&CRT_Valeur=0';
					$params .= '&CRT_Devise=EUR';
					$params .= '&Exp_Valeur=';
					$params .= '&Exp_Devise=';
					if ($field['MR_Selected_Num'] == 'LD1' OR $field['MR_Selected_Num'] == 'LDS')
					{
						$params .= '&LIV_Rel_Pays=';
						$params .= '&LIV_Rel=';
					}
					else
					{
						$params .= '&LIV_Rel_Pays='.$field['MR_Selected_Pays'];
						$params .= '&LIV_Rel='.$field['MR_Selected_Num'];
					}
					$params .= '&Assurance='.urlencode(free_MR_chaine($field['mr_ModeAss']));
					$params .= '&Instructions=';
					$params .= '&Texte=';
					$result = _get('http', htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8'), '80', __PS_BASE_URI__ .'modules/mondialrelay/kit_mondialrelay/CreationEtiquettePointRelais_ajax.php', $params);
					$result = explode('|', $result);

					if ($result[0] == 'a')
						$concatener_error .= '<b>Order ID = '.$field['id'].'</b><br>'.$result[1].'<hr>'; 
					elseif ($result[0] != '0')
						$concatener_error .= '<b>Order ID = '.$field['id'].'</b><br>Error nb '.$result[0].'<br>'.$result[1].'<hr>';
					elseif ($result[0] == '0') 
		        	{
						$mondialrelayclass = new MondialRelayClass($field['id_mr_selected']);

						$mondialrelayclass->id_order = $field['id'];
						$mondialrelayclass->MR_poids = $field['weight'];

						$mondialrelayclass->exp_number = $result[1];
						$mondialrelayclass->url_etiquette = $result[2];
						$mondialrelayclass->url_suivi = $result[3];
						$mondialrelayclass->save();
						$concatener_id_order .= $field['id'].';';
					    $concatener_exp_num .= $result[1].';';
				
						$order = new Order((int)($field['id']));
						$order->shipping_number = $field['id_mr_selected'];
						$order->update();
				

						$templateVars = array('{followup}' => $result[3]);
					
						$history = new OrderHistory();
						$history->id_order = (int)($field['id']);
						$history->changeIdOrderState(_PS_OS_SHIPPING_, (int)($field['id'])); 
						$history->id_employee = (int)($cookie->id_employee);
						$history->addWithemail(true, $templateVars);	
				
						echo '<li>' . $mondialrelay->getL('Order number') . ':&nbsp;' . $field['id'];
						echo ' - ' . $mondialrelay->getL('Email send to') . ':&nbsp;' . $customer->email;
						echo "</li>\n";
					}
					unset($result);
				}
			}
		}
		else
			$concatener_error .= $mondialrelay->getL('Empty adress : Are you sure you\'ve set a validate address in the Contact page?');
		
		$concatener_id_order = substr($concatener_id_order, 0, -1);
		$concatener_exp_num = substr($concatener_exp_num, 0, -1);
		if (trim($concatener_exp_num) > 0)
		{
			$params = 'mr_Enseigne_WebService='.urlencode($mr_Enseigne_WebService);
			$params .= '&mr_Key_WebService='.urlencode($mr_Key_WebService);
			$params .= '&Langue='.urlencode($mr_Langage);
			$params .= '&Expeditions='.$concatener_exp_num;
			$result = _get('http', htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8'), '80', __PS_BASE_URI__ .'modules/mondialrelay/kit_mondialrelay/ImpressionEtiquettePointRelais_ajax.php', $params);
			$result = explode('|',$result);

			if ($result[0] == 'a') 
				$concatener_error .= '<b>Creation url etiquettes</b><br>'.$result[1].'<hr>';
			elseif ($result[0] != '0')
				$concatener_error .= '<b>Creation url etiquettes</b><br>Error nb '.$result[0].'<br>'.$result[1].'<hr>';
			elseif ($result[0] == '0')
			{
				$urla4 = $result[1];
				$urla5 = $result[2];	   
				$query = "INSERT INTO " . _DB_PREFIX_ ."mr_historique (`order` ,`exp` ,`url_a4` ,`url_a5`) 
						VALUES ( '".pSQL($concatener_id_order)."','".pSQL($concatener_exp_num)."', '".pSQL($result[1])."', '".pSQL($result[2])."');";
				$query = Db::getInstance()->Execute($query);
			}
//curl_close($ch);
		}
		if (trim($concatener_error) != '')
			echo '<div class="alert">'.$concatener_error.'</div>';
		else
			Tools::redirectAdmin('index.php?tab=AdminMondialRelay&updatesuccess&token='.$this->token);
		return true;
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('generate'))
			$this->_generateetiquettes();
		if (Tools::isSubmit('delete_h'))
			$this->_delete_history();
	}

	private function displayOrdersTable()
	{
		global $cookie;

		include_once(dirname(__FILE__).'/mondialrelay.php');
		$mondialrelay = new MondialRelay();
		$order_state = new OrderState((int)(Configuration::get('MONDIAL_RELAY_ORDER_STATE')), $cookie->id_lang);
		$mr_weight_coef = (int)(Configuration::get('MR_WEIGHT_COEF'));
		
		$html = '
		<script type="text/javascript">
				function checked_all() {
			var checkbox = document.getElementsByTagName(\'input\');
			for (var i=0; i<checkbox.length; i++)
			{
			  if (checkbox[i].type == "checkbox" && checkbox[i].getAttribute("mask_mr")==1) {checkbox[i].checked = true;}
			}
		}
			function un_checked_all() {
			var checkbox = document.getElementsByTagName(\'input\');
			for (var i=0; i<checkbox.length; i++)
			{
				if (checkbox[i].type == "checkbox" && checkbox[i].getAttribute("mask_mr")==1) {checkbox[i].checked = false;}
			}
		}
				function checked_all_h() {
			var checkbox = document.getElementsByTagName(\'input\');
			for (var i=0; i<checkbox.length; i++)
			{
			  if (checkbox[i].type == "checkbox" && checkbox[i].getAttribute("mask_mr_h")==1) {checkbox[i].checked = true;}
			}
		}
			function un_checked_all_h() {
			var checkbox = document.getElementsByTagName(\'input\');
			for (var i=0; i<checkbox.length; i++)
			{
				if (checkbox[i].type == "checkbox" && checkbox[i].getAttribute("mask_mr_h")==1) {checkbox[i].checked = false;}
			}
		}

		</script>
		';
		if (Tools::isSubmit('updatesuccess'))
			$html .= '<div class="conf confirm"><img src="'._PS_ADMIN_IMG_.'/ok.gif" /> '.$mondialrelay->getL('Settings updated succesfull').'</div>';
		
		$html .= $mondialrelay->getL('To generate sticks, you must have register a correct address of your store on').' <a href="index.php?tab=AdminContact&token='.Tools::getAdminToken('AdminContact'.(int)(Tab::getIdFromClassName('AdminContact')).(int)($cookie->id_employee)).'" class="green">'.$mondialrelay->getL('The contact page').'</a>';
		$html .= '<p>'.$mondialrelay->getL('All orders which have the state').' "<b>'.$order_state->name.'</b>"';
		$html .= '.&nbsp;<a href="index.php?tab=AdminModules&configure=mondialrelay&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'" class="green">' . $mondialrelay->getL('Change configuration') . '</a></p>';

		$orders = MondialRelayClass::getOrders();
		if (empty($orders))
		{
			$html.= '<h3>' . $mondialrelay->getL('No orders with this state.') . '</h3>';
		}
		else
		{
			$html.= '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';
			$html.= "\n<table class=\"table\">";
			$html.= '<tr>';
			$html.= '<th>' . $mondialrelay->getL('Order ID') . '</th>';
			$html.= '<th>' . $mondialrelay->getL('Customer') . '</th>';
			$html.= '<th>' . $mondialrelay->getL('Total price') . '</th>';
			$html.= '<th>' . $mondialrelay->getL('Total shipping') . '</th>';
			$html.= '<th>' . $mondialrelay->getL('Date') . '</th>';
			$html.= '<th>' . $mondialrelay->getL('Put a Weight (grams)') . '</th>';
			$html.= '<th>' . $mondialrelay->getL('Selected') . '<br><a href="#" onclick="checked_all(); return false;">' . $mondialrelay->getL('All') . '</a>
			 | <a href="#" onclick="un_checked_all(); return false;">' . $mondialrelay->getL('None') . '</a></th>';
			$html.= '<th>' . $mondialrelay->getL('MR_Selected_Num') . '</th>';
			$html.= '<th>' . $mondialrelay->getL('MR_Selected_Pays') . '</th>';
			$html.= '<th>' . $mondialrelay->getL('exp_number') . '</th>';
			$html.= '<th>' . $mondialrelay->getL('Detail') . '</th>';
			$html.= '</tr>';
			foreach ($orders as $order)
			{
				if ($order['weight'] == 0) 
				{
					$result_weight = Db::getInstance()->getRow('
					SELECT SUM(product_weight*product_quantity) as weight
					FROM '._DB_PREFIX_.'order_detail
					WHERE id_order = '.(int)($order['id_order']));
					$order['weight']=round($mr_weight_coef*$result_weight['weight']);
				}

				$html .= "\n<tr>";
				$html .= '<td>' . $order['id_order'] . '
				     <input type="hidden" name="order[' . $order['id_order'] . '][id]" id="id_order_' . $order['id_order'] . '" value="' . $order['id_order'] . '" />
					 <input type="hidden" name="order[' . $order['id_order'] . '][id_cart]" id="id_cart' . $order['id_order'] . '" value="' . $order['id_cart'] . '" />
					 <input type="hidden" name="order[' . $order['id_order'] . '][id_mr_selected]" id="id_mr_selected' . $order['id_order'] . '" value="' . $order['id_mr_selected'] . '" />
					 <input type="hidden" name="order[' . $order['id_order'] . '][id_address_delivery]" id="id_address_delivery' . $order['id_order'] . '" value="' . $order['id_address_delivery'] . '"/>
					 <input type="hidden" name="order[' . $order['id_order'] . '][mr_ModeCol]" id="mr_ModeCol' . $order['id_order'] . '" value="' . $order['mr_ModeCol'] . '" />
					 <input type="hidden" name="order[' . $order['id_order'] . '][mr_ModeLiv]" id="mr_ModeLiv' . $order['id_order'] . '" value="' . $order['mr_ModeLiv'] . '" />
					 <input type="hidden" name="order[' . $order['id_order'] . '][mr_ModeAss]" id="mr_ModeAss' . $order['id_order'] . '" value="' . $order['mr_ModeAss'] . '" />
					 
					 
					 </td>';
				$html .= '<td>' .  $order['customer'] . '
				      <input type="hidden" name="order[' . $order['id_order'] . '][id_customer]" id="id_customer_' . $order['id_order'] . '" value="' . $order['id_customer'] . '" /></td>';
				$html .= '<td>' . Tools::displayPrice($order['total'], new Currency($order['id_currency'])) . '</td>';
				$html .= '<td>' . Tools::displayPrice($order['shipping'], new Currency($order['id_currency'])) . '</td>';
				$html .= '<td>' . Tools::displayDate($order['date'], $order['id_lang']) . '</td>';
				
				$html .= '<td><input type="text" name="order[' . $order['id_order'] . '][weight]" id="weight_' . $order['id_order'] . '" size="7" value="' . $order['weight'] . '" onchange="document.getElementById(\'selected_' . $order['id_order'] . '\').checked=true;" /></td>';
				$html .= '<td><input type="checkbox" mask_mr=1 name="order[' . $order['id_order'] . '][selected]" id="selected_' . $order['id_order'] . '" value="1" /></td>';
	 
				$html .= '<td>' .  $order['MR_Selected_Num'] .'
				<input type="hidden" name="order[' . $order['id_order'] . '][MR_Selected_Num]" id="MR_Selected_Num' . $order['id_order'] . '" value="' . $order['MR_Selected_Num'] . '" /></td>';
				$html .= '<td>' .  $order['MR_Selected_Pays'] . '
				<input type="hidden" name="order[' . $order['id_order'] . '][MR_Selected_Pays]" id="id_customer_' . $order['id_order'] . '" value="' . $order['MR_Selected_Pays'] . '" /></td>';
				$html .= '<td>' .  $order['exp_number'] . '
				<input type="hidden" name="order[' . $order['id_order'] . '][exp_number]" id="id_customer_' . $order['id_order'] . '" value="' . $order['exp_number'] . '" /></td>'; 
							 
				$html .= '<td class="center">
					<a href="index.php?tab=AdminOrders&id_order='.$order['id_order'].'&vieworder&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)).'">
					<img border="0" title="'.$mondialrelay->getL('View').'" alt="'.$mondialrelay->getL('View').'" src="'._PS_IMG_.'admin/details.gif"/></a>
				</td>';
				$html .= '</tr>';
			}
			$html .= '</table>';
			$html .= '<input type="submit" name="generate" id="generate" value="' . $mondialrelay->getL('Generate') . '" class="button" />';
			$html .= '</form>';
		}
		return $html;
	}

	public function displayhistoriqueForm()
	{
		include_once(dirname(__FILE__).'/mondialrelay.php');
		$mondialrelay = new MondialRelay();
		$_html = '';
	    $query = "SELECT * FROM `" . _DB_PREFIX_ ."mr_historique` ORDER BY `id` DESC ;";
		$query = Db::getInstance()->ExecuteS($query);
		
		$_html.= '<fieldset>';
		$_html.= '<legend>' . $mondialrelay->getL('History of sticks creation') . '</legend>';
		$_html.= '<div style="overflow-x: auto;overflow-y: scroller; height: 300px; padding-top: 0.6em;" >';
		$_html.= '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';
		$_html.= '<table class=table><tbody><tr><th>' . $mondialrelay->getL('Selected') . '<br><a href="javascript:void(0);" onclick="checked_all_h();">' . $mondialrelay->getL('All') . '</a>
			 | <a href="javascript:void(0);" onclick="un_checked_all_h();">' . $mondialrelay->getL('None') . '</a></th><th>' . $mondialrelay->getL('Orders ID') . '</th><th>' . $mondialrelay->getL('Exps num') . '</th><th>' . $mondialrelay->getL('Print stick A4') . '</th><th>' . $mondialrelay->getL('Print stick A5') . '</th></tr>';
		foreach ($query AS $k => $row) 
	    {
			$_html.= '<tr>
			<td><input type="hidden" name="history[' . $row['id'] . '][id]" id="history_id_' . $row['id'] . '" value="' . $row['id'] . '" />
			<input type="checkbox" mask_mr_h=1 name=history[' . $row['id'] . '][selected] id="history_selected_' . $row['id']  . '" value="1" /></td>
			<td>'.str_replace(';', ', ', $row['order']) .'</td><td>'.str_replace(';', ', ', $row['exp']).'</td><td><a href="'.$row['url_a4'].'" target="a4">' . $mondialrelay->getL('Print stick A4') . '</a></td><td><a href="'.$row['url_a5'].'" target="a5">' . $mondialrelay->getL('Print stick A5') . '</a></td></tr>';
	    }
		$_html .= '</tbody></table>';
		$_html .= '<input type="submit" name="delete_h" id="delete_h" value="' . $mondialrelay->getL('Delete selected history') . '" class="button" />';
		$_html .= '</form></div>';
		$_html .= '</fieldset>';

		return $_html;
	}

	public function display()
	{	
		$html = '';

		if (!empty($_POST))
			$html .= $this->_postProcess();

		if ($this->displayInfos)
		{
			$html .= $this->displayOrdersTable();
			$html .= '<br/><br/>';
			$html .= $this->displayhistoriqueForm();
		}
		echo $html;
	}
}

?>
