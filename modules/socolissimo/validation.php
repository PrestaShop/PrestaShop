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

include('../../config/config.inc.php');
include('../../init.php');
include('../../header.php');

require_once(_PS_MODULE_DIR_ . 'socolissimo/socolissimo.php');

$validReturn = array('PUDOFOID','CECIVILITY','CENAME','CEFIRSTNAME', 'CECOMPANYNAME','CEEMAIL','CEPHONENUMBER', 'DELIVERYMODE','CEADRESS1','CEADRESS2','CEADRESS3','CEADRESS4',
				'CEZIPCODE','CEDOORCODE1','CEDOORCODE2','CEENTRYPHONE','DYPREPARATIONTIME','DYFORWARDINGCHARGES','ORDERID', 'SIGNATURE','ERRORCODE','TRPARAMPLUS','TRCLIENTNUMBER','PRID','PRNAME',
				'PRCOMPLADRESS','PRADRESS1','PRADRESS2','PRZIPCODE', 'PRTOWN','CETOWN','TRADERCOMPANYNAME', 'CEDELIVERYINFORMATION', 'CEDOORCODE1', 'CEDOORCODE2');
			
//list of non-blocking error	
$nonBlockingError = array(133, 131, 517, 516, 515, 514, 513, 512, 511, 510, 509, 508, 507, 506, 505, 504, 503, 502, 501);

$so = new Socolissimo();

$return = array();
foreach ($_POST AS $key => $val)
	if (in_array(strtoupper($key),$validReturn))
		$return[strtoupper($key)] = utf8_encode(urldecode(stripslashes($val)));
		
if (isset($return['SIGNATURE']) AND isset($return['CENAME']) AND isset($return['DYPREPARATIONTIME']) AND isset($return['DYFORWARDINGCHARGES']) AND isset($return['TRCLIENTNUMBER']) AND isset($return['ORDERID']) AND isset($return['TRCLIENTNUMBER']))
{
	if (!isset($return['ERRORCODE']) OR $return['ERRORCODE'] == NULL OR in_array($return['ERRORCODE'],$nonBlockingError))
	{	
	
		if ($return['SIGNATURE'] === socolissimo::make_key($return['CENAME'],(float)($return['DYPREPARATIONTIME']),$return['DYFORWARDINGCHARGES'],$return['TRCLIENTNUMBER'], $return['ORDERID']))
		{
			global $cookie ;	
			if (isset($cookie) OR is_object($cookie))
			{
			
				if (saveOrderShippingDetails((int)($cookie->id_cart),(int)($return['TRCLIENTNUMBER']),$return))
				{	
					global $cookie;
					$cart->id_carrier = (int)($_POST['TRPARAMPLUS']);
					if (!$cart->update())
						Tools::redirect();
					else
						Tools::redirect('order.php?step=3');
				}
				else
					echo '<div class="alert error"><img src="' . _PS_IMG_ . 'admin/forbbiden.gif" alt="nok" />&nbsp;'.$so->displaySoError('999').'
						 <p><br/><a href="'.Tools::getProtocol(true).htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'order.php" class="button_small" title="Retour">« Retour</a></p></div>';
			}
			else
				echo '<div class="alert error"><img src="' . _PS_IMG_ . 'admin/forbbiden.gif" alt="nok" />&nbsp;'.$so->displaySoError('999').'
						 <p><br/><a href="'.Tools::getProtocol(true).htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'order.php" class="button_small" title="Retour">« Retour</a></p></div>';
		}
		else
		{
			echo '<div class="alert error"><img src="' . _PS_IMG_ . 'admin/forbbiden.gif" alt="nok" />&nbsp;'.$so->displaySoError('998').'
				  <p><br/><a href="'.Tools::getProtocol(true).htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'order.php" class="button_small" title="Retour">« Retour</a></p></div>';
		}
	}
	else
	{
		echo '<div class="alert error"><img src="' . _PS_IMG_ . 'admin/forbbiden.gif" alt="nok" />&nbsp;'.$so->displaySoError('999').': ';
		$errors = explode(',', str_replace('+',',', $return['ERRORCODE']));
			 foreach($errors as $error)
			 	echo $so->displaySoError(rtrim($error));	
		echo '<p><br/>
			 <a href="'.Tools::getProtocol().htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'order.php" class="button_small" title="Retour">« Retour
			 </a></p></div>';	}
}
else
	Tools::redirect();

include('../../footer.php');

function saveOrderShippingDetails($idCart, $idCustomer, $soParams)
{

	$deliveryMode = array('DOM' => 'Livraison à domicile', 'BPR' => 'Livraison en Bureau de Poste',
						  'A2P' => 'Livraison Commerce de proximité', 'MRL' => 'Livraison Commerce de proximité',
						  'CIT' => 'Livraison en Cityssimo', 'ACP' => 'Agence ColiPoste', 'CDI' => 'Centre de distribution',
						  'RDV' => 'Livraison sur Rendez-vous');
				  
	$db = Db::getInstance();
	$db->ExecuteS('SELECT * FROM '._DB_PREFIX_.'socolissimo_delivery_info WHERE id_cart = '.(int)($idCart).' AND id_customer ='.(int)($idCustomer));
	$numRows = (int)($db->NumRows());
	if ($numRows == 0)
	{	
		$sql = 'INSERT INTO '._DB_PREFIX_.'socolissimo_delivery_info
										( `id_cart`, `id_customer`, `delivery_mode`, `prid`, `prname`, `prfirstname`, `prcompladress`, 
										`pradress1`, `pradress2`, `pradress3`, `pradress4`, `przipcode`, `prtown`, `cephonenumber`, `ceemail` , `cecompanyname`, `cedeliveryinformation`, `cedoorcode1`, `cedoorcode2`) 
										VALUES ('.(int)($idCart).','.(int)($idCustomer).',';
		if ($soParams['DELIVERYMODE'] != 'DOM' AND $soParams['DELIVERYMODE'] != 'RDV')
			$sql .= '\''.pSQL($soParams['DELIVERYMODE']).'\''.',
					'.(isset($soParams['PRID']) ? '\''.pSQL($soParams['PRID']).'\'' : '').',
					'.(isset($soParams['PRNAME']) ? '\''.ucfirst(pSQL($soParams['PRNAME'])).'\'' : '').',
					'.(isset($deliveryMode[$soParams['DELIVERYMODE']]) ? '\''.$deliveryMode[$soParams['DELIVERYMODE']].'\'' : 'So Colissimo').',
					'.(isset($soParams['PRCOMPLADRESS']) ? '\''.pSQL($soParams['PRCOMPLADRESS']).'\'' : '\'\'').',
					'.(isset($soParams['PRADRESS1']) ? '\''.pSQL($soParams['PRADRESS1']).'\'' : '\'\'').',
					'.(isset($soParams['PRADRESS2']) ? '\''.pSQL($soParams['PRADRESS2']).'\'' : '\'\'').',
					'.(isset($soParams['PRADRESS3']) ? '\''.pSQL($soParams['PRADRESS3']).'\'' : '\'\'').',
					'.(isset($soParams['PRADRESS4']) ? '\''.pSQL($soParams['PRADRESS4']).'\'' : '\'\'').',
					'.(isset($soParams['PRZIPCODE']) ? '\''.pSQL($soParams['PRZIPCODE']).'\'' : '\'\'').',
					'.(isset($soParams['PRTOWN']) ? '\''.pSQL($soParams['PRTOWN']).'\'' : '\'\'').',
					'.(isset($soParams['CEPHONENUMBER']) ? '\''.pSQL($soParams['CEPHONENUMBER']).'\'' : '\'\'').',
					'.(isset($soParams['CEEMAIL']) ? '\''.pSQL($soParams['CEEMAIL']).'\'' : '\'\'').',
					'.(isset($soParams['CECOMPANYNAME']) ? '\''.pSQL($soParams['CECOMPANYNAME']).'\'' : '\'\'').',
					'.(isset($soParams['CEDELIVERYINFORMATION']) ? '\''.pSQL($soParams['CEDELIVERYINFORMATION']).'\'' : '\'\'').',
					'.(isset($soParams['CEDOORCODE1']) ? '\''.pSQL($soParams['CEDOORCODE1']).'\'' : '\'\'').',
					'.(isset($soParams['CEDOORCODE2']) ? '\''.pSQL($soParams['CEDOORCODE2']).'\'' : '\'\'').')';
		else
			$sql .= '\''.pSQL($soParams['DELIVERYMODE']).'\',\'\',
					'.(isset($soParams['CENAME']) ? '\''.ucfirst(pSQL($soParams['CENAME'])).'\'' : '').',
					'.(isset($soParams['CEFIRSTNAME']) ? '\''.ucfirst(pSQL($soParams['CEFIRSTNAME'])).'\'' : '').',
					'.(isset($soParams['CECOMPLADRESS']) ? '\''.pSQL($soParams['CECOMPLADRESS']).'\'' : '\'\'').',
					'.(isset($soParams['CEADRESS1']) ? '\''.pSQL($soParams['CEADRESS1']).'\'' : '\'\'').',
					'.(isset($soParams['CEADRESS2']) ? '\''.pSQL($soParams['CEADRESS2']).'\'' : '\'\'').',
					'.(isset($soParams['CEADRESS3']) ? '\''.pSQL($soParams['CEADRESS3']).'\'' : '\'\'').',
					'.(isset($soParams['CEADRESS4']) ? '\''.pSQL($soParams['CEADRESS4']).'\'' : '\'\'').',
					'.(isset($soParams['CEZIPCODE']) ? '\''.pSQL($soParams['CEZIPCODE']).'\'' : '\'\'').',
					'.(isset($soParams['CETOWN']) ? '\''.pSQL($soParams['CETOWN']).'\'' : '\'\'').',
					'.(isset($soParams['CEPHONENUMBER']) ? '\''.pSQL($soParams['CEPHONENUMBER']).'\'' : '\'\'').',
					'.(isset($soParams['CEEMAIL']) ? '\''.pSQL($soParams['CEEMAIL']).'\'' : '\'\'').',
					'.(isset($soParams['CECOMPANYNAME']) ? '\''.pSQL($soParams['CECOMPANYNAME']).'\'' : '\'\'').',
					'.(isset($soParams['CEDELIVERYINFORMATION']) ? '\''.pSQL($soParams['CEDELIVERYINFORMATION']).'\'' : '\'\'').',
					'.(isset($soParams['CEDOORCODE1']) ? '\''.pSQL($soParams['CEDOORCODE1']).'\'' : '\'\'').',
					'.(isset($soParams['CEDOORCODE2']) ? '\''.pSQL($soParams['CEDOORCODE2']).'\'' : '\'\'').')';

	if (Db::getInstance()->Execute($sql))	
		return true;
	}
	else
	{	
		$table = _DB_PREFIX_.'socolissimo_delivery_info';
		$values = array();
		$values['delivery_mode'] = pSQL($soParams['DELIVERYMODE']);

		if (!in_array($soParams['DELIVERYMODE'], array('DOM', 'RDV')))
		{
			        (isset($soParams['PRID']) ? $values['prid'] = pSQL($soParams['PRID']) : '');
					(isset($soParams['PRNAME']) ? $values['prname'] = ucfirst(pSQL($soParams['PRNAME'])) : '');
					(isset($deliveryMode['DELIVERYMODE']) ? $values['prfirstname'] = $deliveryMode[$soParams['DELIVERYMODE']] : $values['prfirstname'] = 'So Colissimo');
					(isset($soParams['PRCOMPLADRESS']) ? $values['prcompladress'] = pSQL($soParams['PRCOMPLADRESS']) : '');
					(isset($soParams['PRADRESS1']) ? $values['pradress1'] = pSQL($soParams['PRADRESS1']) : '');
					(isset($soParams['PRADRESS2']) ? $values['pradress2'] = pSQL($soParams['PRADRESS2']) : '');
					(isset($soParams['PRADRESS3']) ? $values['pradress3'] = pSQL($soParams['PRADRESS3']) : '');
					(isset($soParams['PRADRESS4']) ? $values['pradress4'] = pSQL($soParams['PRADRESS4']) : '');
					(isset($soParams['PRZIPCODE']) ? $values['przipcode'] = pSQL($soParams['PRZIPCODE']) : '');
					(isset($soParams['CETOWN']) ? $values['prtown'] = pSQL($soParams['CETOWN']) : '');
					(isset($soParams['CEPHONENUMBER']) ? $values['cephonenumber'] = pSQL($soParams['CEPHONENUMBER']) : '');
					(isset($soParams['CEEMAIL']) ? $values['ceemail'] = pSQL($soParams['CEEMAIL']) : '');
					(isset($soParams['CEDELIVERYINFORMATION']) ? $values['cedeliveryinformation'] = pSQL($soParams['CEDELIVERYINFORMATION']) : '');
					(isset($soParams['CEDOORCODE1']) ? $values['cedoorcode1'] = pSQL($soParams['CEDOORCODE1']) : '');
					(isset($soParams['CEDOORCODE2']) ? $values['cedoorcode2'] = pSQL($soParams['CEDOORCODE2']) : '');
					(isset($soParams['CECOMPANYNAME']) ? $values['cecompanyname'] = pSQL($soParams['CECOMPANYNAME']) : '');
		}
		else
		{
					(isset($soParams['CENAME']) ? $values['prname'] = ucfirst(pSQL($soParams['CENAME'])) : '');
					(isset($soParams['CEFIRSTNAME']) ? $values['prfirstname'] = ucfirst(pSQL($soParams['CEFIRSTNAME'])) : '');
					(isset($soParams['CECOMPLADRESS']) ? $values['prcompladress'] = pSQL($soParams['CECOMPLADRESS']) : '');
					(isset($soParams['CEADRESS1']) ? $values['pradress1'] = pSQL($soParams['CEADRESS1']) : '');
					(isset($soParams['CEADRESS2']) ? $values['pradress2'] = pSQL($soParams['CEADRESS2']) : '');
					(isset($soParams['CEADRESS3']) ? $values['pradress3'] = pSQL($soParams['CEADRESS3']) : '');
					(isset($soParams['CEADRESS4']) ? $values['pradress4'] = pSQL($soParams['CEADRESS4']) : '');
					(isset($soParams['CEZIPCODE']) ? $values['przipcode'] = pSQL($soParams['CEZIPCODE']) : '');
					(isset($soParams['PRTOWN']) ? $values['prtown'] = pSQL($soParams['PRTOWN']) : '') ;
					(isset($soParams['CEEMAIL']) ? $values['ceemail'] = pSQL($soParams['CEEMAIL']) : '');
					(isset($soParams['CEPHONENUMBER']) ? $values['cephonenumber'] = pSQL($soParams['CEPHONENUMBER']) : '');
					(isset($soParams['CEDELIVERYINFORMATION']) ? $values['cedeliveryinformation'] = pSQL($soParams['CEDELIVERYINFORMATION']) : '');
					(isset($soParams['CEDOORCODE1']) ? $values['cedoorcode1'] = pSQL($soParams['CEDOORCODE1']) : '');
					(isset($soParams['CEDOORCODE2']) ? $values['cedoorcode2'] = pSQL($soParams['CEDOORCODE2']) : '');
					(isset($soParams['CECOMPANYNAME']) ? $values['cecompanyname'] = pSQL($soParams['CECOMPANYNAME']) : '');
		}
		$where = ' `id_cart` =\''.(int)($idCart).'\' AND `id_customer` =\''.(int)($idCustomer).'\'';
				
		if (Db::getInstance()->autoExecute($table, $values, 'UPDATE', $where))
			return true;
	}
}



