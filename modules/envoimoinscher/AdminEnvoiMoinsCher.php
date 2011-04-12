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

include_once('envoimoinscher.php');

class AdminEnvoiMoinsCher extends AdminTab
{
	public $packaging = array('Pli' => 'Pli', 'Colis' => 'Colis', 'Encombrant' => 'Objet lourd', 'Palette' => 'Palette');
	public function __construct()
	{
		parent::__construct();
	}

	public function display()
	{
		global $cookie;
		$emc = new Envoimoinscher();
		echo '<h2>'.$emc->lang('List of orders to export').'</h2>';
		if (Tools::isSubmit('submitExport'))
		{
			$orderToExport = array();
			$idsOrdersToExport = Tools::getValue('ordersBox');
			if (!empty($idsOrdersToExport))
			{
				foreach($idsOrdersToExport as $id)
				{
					$orderToExport[] = self::getOrderDetails((int)($id));
				}
				echo '<form action="http://www.envoimoinscher.com/index.html" method="POST">
						<input type="hidden" name="url_renvoi" value="'.Tools::getProtocol().htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').$_SERVER['REQUEST_URI'].'">
						<input type="hidden" name="login" value="'.htmlspecialchars(Configuration::get('EMC_LOGIN'), ENT_COMPAT, 'UTF-8').'">
						<input type="hidden" name="tracking" value="prestashop_module_v1">';
				self::inputMaker($orderToExport);
					
				echo '</form>
					<script type="text/javascript">
					$(document).ready(function() {
						$("form").submit();
						});
					</script>';
			}
			else echo '<div class="alert error">
					   <img src="' . _PS_IMG_ . 'admin/forbbiden.gif" alt="nok" />
					   '.$emc->lang('No order to export').'</div>
					   <p><a class="button" href="'.Tools::getProtocol().htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').$_SERVER['REQUEST_URI'].'">Retour</a></p>';
		}
		else
		{
			if (Configuration::get('EMC_ORDER_STATE') AND Configuration::get('EMC_CARRIER') AND Configuration::get('EMC_FIRST_NAME') AND Configuration::get('EMC_LAST_NAME')
			 AND Configuration::get('EMC_ADDRESS') AND Configuration::get('EMC_ZIP_CODE') AND Configuration::get('EMC_CITY') AND Configuration::get('EMC_COUNTRY')
			 AND Configuration::get('EMC_PHONE') AND Configuration::get('EMC_EMAIL') AND Configuration::get('EMC_LOGIN'))
			{
				echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="POST">';
				$orders = self::getOrders();
				self::displayOrders($orders);
				echo '<p><input type="submit" value="'.$emc->lang('Send').'" name="submitExport" class="button" style="margin:10px 0px 0px 25px;"></p>
						</form>';
			}
			else
			echo '<h2 style="color:red">'.$emc->lang('Please configure this module in order').'</h2>';
		}
		echo '<br><p><a href="index.php?tab=AdminModules&configure=envoimoinscher&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'" class="button">
			 ' . $emc->lang('Change configuration') . '</a></p>';
	}
	
	private function getOrders()
	{
		$id_order_state = (int)(Configuration::get('EMC_ORDER_STATE'));
		$id_carrier = (int)(Configuration::get('EMC_CARRIER'));

		$sql = '
			SELECT o.id_order as id_order, o.`id_customer` as id_customer,
				CONCAT(c.`firstname`, \' \', c.`lastname`) AS `customer`,
				o.total_paid_real as total, o.total_shipping as shipping,
				o.date_add as date, o.id_currency as id_currency, o.id_lang as id_lang,
				SUM(od.product_weight * od.product_quantity) as weight
			FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (o.`id_order` = od.`id_order`)
				LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
				LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON (oh.`id_order` = o.`id_order`)
				LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
				LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = o.`id_lang`)
			WHERE  (SELECT moh.`id_order_state` FROM `'._DB_PREFIX_.'order_history` moh WHERE moh.`id_order` = o.`id_order` ORDER BY moh.date_add DESC LIMIT 1) = '.$id_order_state.'
			AND o.id_carrier = '.pSQL($id_carrier).'
			GROUP BY o.`id_order`, od.`id_order`
			ORDER BY o.`date_add` ASC';
		return Db::getInstance()->ExecuteS($sql);
	}	

	private function displayOrders($orders)
	{
		global $cookie;
		echo '<table cellspacing="0" cellpadding="0" class="table" align="center" style="margin:10px 0px 0px 25px;">
					<tr>
						<th><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'ordersBox[]\', this.checked)" /></th>
						<th>'.$emc->lang('ID').'</th>
						<th>'.$emc->lang('Name').'</th>
						<th>'.$emc->lang('Total Cost').'</th>
						<th>'.$emc->lang('Total shipment').'</th>
						<th>'.$emc->lang('Date').'</th>
						<th>'.$emc->lang('Packaging').'</th>
						<th>'.$emc->lang('Nature of contents').'</th>
						<th>'.$emc->lang('Detail').'</th>		
					</tr>';
		if (!empty($orders))
		{
			foreach($orders as $order)
			{
				$customer = new Customer((int)($order['id_customer']));
					echo   '<tr>
							  <td align="center">
								<input type="checkbox" name="ordersBox[]" class="ordersBox" value="'.(int)($order['id_order']).'" />
							  </td>
							  <td align="center">'.(int)($order['id_order']).'</td>
							  <td>'.htmlentities($customer->lastname, ENT_COMPAT, 'UTF-8').' '.htmlentities($customer->firstname, ENT_COMPAT, 'UTF-8').'</td>
							  <td align="center">'.Tools::displayPrice($order['total'], new Currency((int)($order['id_currency']))).'</td>
							  <td align="center">'.Tools::displayPrice($order['shipping'], new Currency((int)($order['id_currency']))).'</td>
							  <td align="center">'.Tools::displayDate($order['date'], (int)($order['id_lang'])).'</td>
							  <td align="center">
							  	<select name="packaging_'.$order['id_order'].'">';
							  		foreach($this->packaging as $package => $value)
										echo '<option '.(Configuration::get('EMC_PACKAGING_DEFAULT') == $value ? ' selected="selected" ' : '').' value="'.htmlentities($value, ENT_COMPAT, 'UTF-8').'">
										'.htmlentities($package, ENT_COMPAT, 'UTF-8').'</option>';
							  	echo '</select>
							  </td>
							  <td>'.envoimoinscher::selectNature(Configuration::get('EMC_CONTENT'),(int)($order['id_order'])).'</td>
							  <td align="center"><a href="index.php?tab=AdminOrders&id_order='.(int)($order['id_order']).'&vieworder&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)).'">
					<img border="0" title="'.$emc->lang('View').'" alt="'.$emc->lang('View').'" src="'._PS_IMG_.'admin/details.gif"/></a></td>
						  </tr>';
			}
		}
		else
		echo   '<tr><td colspan="4" align="center">'.$emc->lang('No order to export').'</td></tr>';
		
	echo '</table>';
	}

	private function getOrderDetails($id_order)
	{
		global $cookie;
		$confs = Configuration::getMultiple(array('EMC_LOGIN', 'PS_SHOP_NAME', 'EMC_GENDER', 'EMC_FIRST_NAME', 'EMC_LAST_NAME', 'EMC_ADDRESS',
												  'EMC_ZIP_CODE', 'EMC_CITY', 'EMC_COUNTRY', 'EMC_PHONE', 'EMC_EMAIL', 'EMC_EMAILS'));
		$orderDetails = array();
		$order = new Order((int)($id_order));
		$customer = new Customer((int)($order->id_customer));
		$adresseDelivery = new Address((int)($order->id_address_delivery));
		
		$genderTab = array(1 => 'M.', 2 => 'Mme', 9 => '');
		$orderDetails['url_suivi'] = Tools::getProtocol().htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/envoimoinscher/tracking.php?token='.$customer->secure_key;
		$orderDetails['infoexterne'] = str_replace('.','_',str_replace('www.','',$_SERVER['HTTP_HOST'])).'_'.(int)($id_order);
		$orderDetails['packaging'] =  Tools::getValue('packaging_'.(int)($id_order));
		$orderDetails['type_objet'] =  Tools::getValue('type_objet_'.(int)($id_order));
		$orderDetails['envoi_emailsconf'] = (int)($confs['EMC_EMAILS']);
		
		//products infos
		$productsDetails = $order->getProductsDetail();
		$tabDetails = array();
		foreach($productsDetails as $details)
		{
			$features = self::getFeatures((int)($details['product_id']));
			$tabDetailsProduct = array();
			$tabDetailsProduct['id'] = (int)($details['product_id']);
			$tabDetailsProduct['nb'] = (int)($details['product_quantity']);
			$tabDetailsProduct['poids'] = (float)($details['product_weight']);
			$tabDetailsProduct['description'] = htmlspecialchars($details['product_name'], ENT_COMPAT, 'UTF-8');
				foreach($features as $key => $value)
					$tabDetailsProduct[$key] = $value;
			$tabDetails[] = $tabDetailsProduct;
		}
		$orderDetails['products'] = $tabDetails;
			
		//sending infos
		$orderExpediteur['civilite'] = htmlspecialchars($genderTab[$confs['EMC_GENDER']], ENT_COMPAT, 'UTF-8');
		$orderExpediteur['collecte_type'] = 'entreprise';
		$orderExpediteur['societe'] = htmlspecialchars($confs['PS_SHOP_NAME'], ENT_COMPAT, 'UTF-8');
		$orderExpediteur['nom'] = htmlspecialchars($confs['EMC_LAST_NAME'], ENT_COMPAT, 'UTF-8');
		$orderExpediteur['prenom'] = htmlspecialchars($confs['EMC_FIRST_NAME'], ENT_COMPAT, 'UTF-8');
		$orderExpediteur['adresse'] = htmlspecialchars($confs['EMC_ADDRESS'], ENT_COMPAT, 'UTF-8');
		$orderExpediteur['codepostal'] = htmlspecialchars($confs['EMC_ZIP_CODE'], ENT_COMPAT, 'UTF-8');
		$orderExpediteur['ville'] = htmlspecialchars($confs['EMC_CITY'], ENT_COMPAT, 'UTF-8');
		
		$orderExpediteur['pz_id'] = htmlspecialchars($confs['EMC_COUNTRY'], ENT_COMPAT, 'UTF-8');
		$orderExpediteur['tel'] = htmlspecialchars($confs['EMC_PHONE'], ENT_COMPAT, 'UTF-8');
		$orderExpediteur['email'] = htmlspecialchars($confs['EMC_EMAIL'], ENT_COMPAT, 'UTF-8');
		$orderDetails['expediteur'] = $orderExpediteur;
				
		//delivery infos
		$orderDelivery['civilite'] = $genderTab[(int)($customer->id_gender)];
		if (isset($adresseDelivery->company))
		$orderDelivery['collecte_type'] = 'particulier';
			$orderDelivery['societe'] = htmlspecialchars($adresseDelivery->company, ENT_COMPAT, 'UTF-8');
		$orderDelivery['prenom'] = htmlspecialchars($adresseDelivery->firstname, ENT_COMPAT, 'UTF-8');
		$orderDelivery['nom'] = htmlspecialchars($adresseDelivery->lastname, ENT_COMPAT, 'UTF-8');
		$orderDelivery['adresse'] = htmlspecialchars($adresseDelivery->address1, ENT_COMPAT, 'UTF-8');
		$orderDelivery['codepostal'] = htmlspecialchars($adresseDelivery->postcode, ENT_COMPAT, 'UTF-8');
		$orderDelivery['ville'] = htmlspecialchars($adresseDelivery->city, ENT_COMPAT, 'UTF-8');
		$orderDelivery['pz_id'] = Country::getIsoById(Country::getIdByName((int)($cookie->id_lang),$adresseDelivery->country));
		if (isset($adresseDelivery->phone))
			$orderDelivery['tel'] = htmlspecialchars($adresseDelivery->phone, ENT_COMPAT, 'UTF-8');
		else
			$orderDelivery['tel'] = htmlspecialchars($adresseDelivery->phone_mobile, ENT_COMPAT, 'UTF-8');
		$orderDelivery['email'] = htmlspecialchars($customer->email, ENT_COMPAT, 'UTF-8');
		
		$orderDetails['destinataire'] = $orderDelivery;
		//d($orderDetails);
		return $orderDetails;
	}
	
	private function getFeatures($id)
	{
		global $cookie;
		$featuresTab = array();
		$confs = Configuration::getMultiple(array('EMC_WIDTH', 'EMC_HEIGHT', 'EMC_DEPTH'));
		$features = Product::getFeaturesStatic((int)$id);
		foreach($features as $feature)
		{
			switch ($feature['id_feature'])
			{
				case $confs['EMC_WIDTH'] :
					$featureValue = new FeatureValue((int)($feature['id_feature_value']));
					$featuresTab['largeur'] = $featureValue->value[(int)($cookie->id_lang)];
					break;
				case $confs['EMC_HEIGHT'] :
					$featureValue = new FeatureValue((int)($feature['id_feature_value']));
					$featuresTab['hauteur'] = $featureValue->value[(int)($cookie->id_lang)];
					break;
				case $confs['EMC_DEPTH'] :
					$featureValue = new FeatureValue((int)($feature['id_feature_value']));
					$featuresTab['longueur'] = $featureValue->value[(int)($cookie->id_lang)];
					break;
			}
		}
		return $featuresTab;
	}
		
	private function inputMaker($orderDetails)
	{
		$nbrOrder = count($orderDetails)-1;
		foreach($orderDetails as $details)
		{
			foreach($details as $detail => $values)
			{
				if ($detail == 'products')
				{
					if (count($values) > 1)
					{
						$weight = 0;
						foreach($values as $key => $features)
						{		
							$weight += (float)($features['poids']*$features['nb']);
						}
						echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.groupe_0.poids" value="'.$weight.'">';						
					}
					elseif($values[0]['nb'] > 1)
					{
						echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.groupe_0.poids" value="'.$values[0]['poids']*$values[0]['nb'].'">';
						echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.description" value="'.$values[0]['description'].'">';
					}
					else
					{
						echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.groupe_0.poids" value="'.(isset($values[0]['poids']) ? ((float)($values[0]['poids']*$values[0]['nb'])) : '').'">';
						echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.groupe_0.longueur" value="'.(isset($values[0]['longueur']) ? (float)($values[0]['longueur']) : '').'">';
						echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.groupe_0.hauteur" value="'.(isset($values[0]['hauteur']) ? (float)($values[0]['hauteur']) : '').'">';
						echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.groupe_0.largeur" value="'.(isset($values[0]['largeur']) ? (float)($values[0]['largeur']) : '').'">';								echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.description" value="'.(isset($values[0]['description']) ? htmlentities($values[0]['description'], ENT_COMPAT, 'UTF-8') : '').'">';
					}
				}	
				elseif ($detail == 'packaging')
					echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.type_envoi" value="'.htmlentities($values, ENT_COMPAT, 'UTF-8').'">';
				elseif ($detail == 'infoexterne')  
					echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.infoexterne" value="'.htmlentities($values, ENT_COMPAT, 'UTF-8').'">';
				elseif ($detail == 'url_suivi')
					echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.url_suivi" value="'.htmlentities($values, ENT_COMPAT, 'UTF-8').'">';
				elseif ($detail == 'infoexterne')
					echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.infoexterne" value="'.htmlentities($values, ENT_COMPAT, 'UTF-8').'">';
				elseif ($detail == 'envoi_emailsconf')
					echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.envoi_emailsconf" value="'.(int)($values).'">';
				elseif ($detail == 'type_objet')
					echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.type_objet" value="'.htmlentities($values, ENT_COMPAT, 'UTF-8').'">';   
				else
				{
					foreach($values as $key => $value)
						echo '<input type="hidden" name="envoi_'.htmlentities($nbrOrder, ENT_COMPAT, 'UTF-8').'.'.htmlentities($detail, ENT_COMPAT, 'UTF-8').'.'.htmlentities($key, ENT_COMPAT, 'UTF-8').'" value="'.htmlentities($value, ENT_COMPAT, 'UTF-8').'">';
				}
			}
			$nbrOrder -=1 ;
		}
	}
}


