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

class Secuvad_connection
{
	public $data;
	public $idsecuvad;
	public $url;
	public $secuvad_orders;
	private $secuvad_h;
	
	public function __construct($data, $idsecuvad, $url, &$secuvad_h)
	{
		$this->data = $data;
		$this->idsecuvad = $idsecuvad;
		$this->url = $url;
		$this->secuvad_h = $secuvad_h;
	}

	private function send($key)
	{
		$toReturn = 'false';
		$url = $this->url;
		if ($key == 'bulk_transactions')
			$post = 'siteid='.(int)(Configuration::get('SECUVAD_ID')).'&bulk_transactions='.$this->data;
		else
			$post = $key.'='.$this->data;
		$ci = curl_init($url);
		if ($ci === false)
		{
			$error = curl_error($ci);
			$this->secuvad_h->secuvad_log("secuvad_connection.php::send() \n\t Error curl_init() : ".$error);
		}
		else
		{
			curl_setopt($ci, CURLOPT_POST, 1);
			curl_setopt($ci, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
			
			$headers = array('Content-type: text/xml;charset="utf-8"');
			$rep = curl_exec($ci);
			
			if ($rep === false)
			{
				$toReturn = 'false';
				$error = curl_error($ci);
				$this->secuvad_h->secuvad_log('secuvad_connection.php::send() '."\n\t".' Error: curl_exec() : '.$error);
			}
			else 
			{	
				$toReturn = $rep;
				$this->secuvad_h->secuvad_log('secuvad_connection.php::send() '."\n\t".' curl_exec() returned : '.$toReturn);
			}
			curl_close($ci);
		}
		return $toReturn;
	}

	public function report_fraud($post, $balise)
	{
		$response = str_replace("\n", "", $this->send($post));
		$response = str_replace("\r", "", $response);
		if (preg_match('#<'.$balise.'>.{0,4}<transaction_report idtransaction=.([0-9]+).>([^<]*)<.transaction_report>.{0,4}<.'.$balise.'>#Ui', $response, $regs))
		{
			$response = preg_replace('#<'.$balise.'>.{0,4}<transaction_report idtransaction=.'.$regs[1].'.>'.$regs[2].'<.transaction_report>.{0,4}<.'.$balise.'>#Ui','', $response);
			$idtransaction 	= $regs[1];
    		$feedback	= $regs[2];
    		if ($feedback == 'OK' AND ($balise == 'fraude_report' OR $balise == 'impaye_report'))
    		{
    			if ($balise == 'fraude_report')
    				$this->secuvad_h->secuvad_log('secuvad_connection.php::report_fraud() '."\n\t".' Fraud: '.(int)($idtransaction).'/'.$feedback);
    			elseif ($balise == 'impaye_report')
    				$this->secuvad_h->secuvad_log('secuvad_connection.php::report_fraud() '."\n\t".' Unpaid: '.(int)($idtransaction).'/'.$feedback);
				Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'secuvad_order` 
				SET `is_fraud` = 1 
				WHERE `id_secuvad_order` = '.(int)($idtransaction));
				return 'true';
    		}
    		else
    		{
   				$this->secuvad_h->secuvad_log('secuvad_connection.php::report_fraud() '."\n\t".' Error: '.(int)($idtransaction).'/'.$feedback);
    			return $feedback;
    		}
    	}
    	else
    		return 'Erreur de connexion';
	}
	

	public function send_transaction()
	{
		global $currentIndex, $cookie;
		
		$flag_rep = false;
		$response = str_replace("\n", "", $this->send('bulk_transactions'));
		$response = str_replace("\r", "", $response);
		
		if (preg_match('#<transaction_report status=.{0,10} idtransaction=.([0-9]+).><score>([0-9]*)<.score><advice>([^<]*)</advice><error>([^<]*)<.error><.transaction_report>#Ui', $response, $regs))
		{
			$flag_rep = true;
			$response = preg_replace("#<transaction_report status=.OK. idtransaction=.".$regs[1].".><score>".$regs[2]."<.score><advice>".$regs[3]."</advice><error>".$regs[4]."<.error><.transaction_report>#Ui","", $response);
    		
			$idtransaction 	= $regs[1];
    		$score = $regs[2];
    		$advice	= $regs[3];
    		$erreur	= $regs[4];
    			
   			if (!empty($erreur))
    		{
    			$this->secuvad_h->secuvad_log('secuvad_connection.php::send_transaction() '."\n\t".' Error: '.$erreur);	
				Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'secuvad_order` 
				SET `secuvad_status` = 4, `error` = \''.pSQL($erreur).'\' 
				WHERE `id_secuvad_order` = '.(int)($idtransaction)); 
    		}
    		else
    		{
    			$this->secuvad_h->secuvad_log('secuvad_connection.php::send_transaction() '."\n\t".' Response: '.(int)($idtransaction).'/'.(int)($score).'/'.$advice);    				    				  
    			if(preg_match('/[0-9]+/', $score))
					Db::getInstance()->Execute('
					UPDATE `'._DB_PREFIX_.'secuvad_order` 
					SET `secuvad_status` = 5, `score` = '.(int)($score).', error = \'\' 
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
					SET `advice` = \''.pSQL($advice).'\', `error` = \'\', `secuvad_status` = '.(int)($secuvad_status).' 
					WHERE `id_secuvad_order` = '.(int)($idtransaction));
    			}		
    		}	
    	}
    	if (preg_match('#<bulk_report><global_report>([^<]*)<.global_report><error>([^<]+)<.error><.bulk_report>#Ui', $response, $regs))
    	{
			$flag_rep = true;
    		$this->secuvad_h->secuvad_log('secuvad_connection.php::send_transaction() '."\n\t".' Error: '.$regs[2]);
    	}
    	if (!$flag_rep)
    		$this->secuvad_h->secuvad_log('secuvad_connection.php::send_transaction() '."\n\t".' Error: '.$rep);
	}
}

