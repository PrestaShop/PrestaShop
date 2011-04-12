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

/*************************************************************************************************************************

Script destiner à traiter les analyses de secuvad qui ne sont pas renvoyées immédiatement (status 'en attente' ou erreur).

*************************************************************************************************************************/

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/secuvad.php');

if (!Tools::isSubmit('transaction_report'))
	exit;

$secuvad = new Secuvad();

if (in_array($secuvad->getRemoteIPaddress(), $secuvad->get_secuvad_ip()))
{
	$rep = stripslashes(urldecode(Tools::getValue('transaction_report')));
	if (preg_match('#<transaction_report status=.{0,10} idtransaction=.([0-9]+).><score>([0-9]*)<.score><advice>([^<]*)</advice><error>([^<]*)<.error><.transaction_report>#Ui', $rep, $regs))
	{
		$rep = preg_replace('#<transaction_report status=.OK. idtransaction=.'.$regs[1].'.><score>'.$regs[2].'<.score><advice>'.$regs[3].'</advice><error>'.$regs[4].'<.error><.transaction_report>#Ui', '', $rep);
    	$idtransaction = $regs[1];
    	$score = $regs[2];
    	$advice = $regs[3];
    	$erreur	= $regs[4];
    	
    	if (!empty($erreur))
    	{
    		$secuvad->secuvad_log('secuvad_response.php '."\n\t".' Error: '.$erreur);					
    		Db::getInstance()->Execute('
    		UPDATE `'._DB_PREFIX_.'secuvad_order` 
    		SET `secuvad_status` = 4, `error` = '.pSQL($erreur).' 
			WHERE `id_secuvad_order` = '.(int)($idtransaction));  
    	}
    	else
    	{
    		$secuvad->secuvad_log('secuvad_response.php '."\n\t".' Response: '.$rep);
    		  
    		if (preg_match('/[0-9]+/Ui', $score))
    			Db::getInstance()->Execute('
    			UPDATE `'._DB_PREFIX_.'secuvad_order` 
    			SET `secuvad_status` = 5, `score` =  '.(int)($score).' 
				WHERE `id_secuvad_order` = '.(int)($idtransaction));
    		else
    		{
    			if (strtoupper($advice) == 'INVALIDE')
	    			$secuvad_status	= 3;
	    		elseif (strtoupper($advice) == 'VALIDE')
	    			$secuvad_status	= 1;
	    		elseif (strtoupper($advice) == 'A EXPERTISER')
	    			$secuvad_status	= 7;
	    		elseif (strtoupper($advice) == 'A EXPERTISER C')
	    			$secuvad_status	= 6;
	    		elseif (strtoupper($advice) == 'EN ATTENTE')
	    			$secuvad_status	= 2;
	    		else
					$secuvad_status	= 4;
    			Db::getInstance()->Execute('
    			UPDATE `'._DB_PREFIX_.'secuvad_order` 
    			SET `secuvad_status` = '.(int)($secuvad_status).', `advice` = \''.pSQL($advice).'\' 
				WHERE `id_secuvad_order` = '.(int)($idtransaction));
    		}		
    	}	
    }
    if(preg_match('#<bulk_report><global_report>([^<]*)<.global_report><error>([^<]+)<.error><.bulk_report>#Ui', $rep, $regs))
		$secuvad->secuvad_log('secuvad_response.php '."\n\t".' Error '.$regs[2]); 
}


