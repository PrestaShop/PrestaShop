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

include_once('../../config/config.inc.php');
include_once('mondialrelay.php');

if (Tools::getValue('secure_key') != Configuration::get('MONDIAL_RELAY_SECURE_KEY'))
	exit;

$expeditions = Db::getInstance()->ExecuteS('
SELECT ms.`exp_number`, ms.`id_cart`, o.`id_order`
FROM `'._DB_PREFIX_.'mr_selected` ms
LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_cart` = ms.`id_cart`) 
WHERE `exp_number` != 0');

if (empty($expeditions))
	exit;

$params = array(
'Enseigne' => Configuration::get('MR_ENSEIGNE_WEBSERVICE'),
'Langue' => 'FR'
);

require_once('kit_mondialrelay/tools/nusoap/lib/nusoap.php');
$client_mr = new nusoap_client("http://www.mondialrelay.fr/webservice/Web_Services.asmx?WSDL", true);
$client_mr->soap_defencoding = 'UTF-8';
$client_mr->decode_utf8 = false;

foreach ($expeditions as $expedition)
{
	if ($expedition['id_order'] == NULL)
		continue;
	if (OrderHistory::getLastOrderState((int)($expedition['id_order']))->id == _PS_OS_DELIVERED_)
		continue;
	$params['Expedition'] = $expedition['exp_number'];
	$params['Security'] = strtoupper(md5($params['Enseigne'].$params['Expedition'].'FR'.Configuration::get('MR_KEY_WEBSERVICE')));
	
	$is_delivered = 0;
	$result_mr = $client_mr->call('WSI2_TracingColisDetaille', $params, 'http://www.mondialrelay.fr/webservice/', 'http://www.mondialrelay.fr/webservice/WSI2_TracingColisDetaille');
	if (isset($result_mr['WSI2_TracingColisDetailleResult']['Tracing']['ret_WSI2_sub_TracingColisDetaille']))
		foreach ($result_mr['WSI2_TracingColisDetailleResult']['Tracing']['ret_WSI2_sub_TracingColisDetaille'] as $result)
			if (isset($result['Libelle']) AND $result['Libelle'] == 'COLIS LIVRÉ')
				$is_delivered = 1;
	
	if ($is_delivered == 1)
	{
		$history = new OrderHistory();
		$history->id_order = (int)($expedition['id_order']);
		$history->changeIdOrderState((int)(_PS_OS_DELIVERED_), (int)($expedition['id_order']));
		$history->addWithemail();
	}
}
