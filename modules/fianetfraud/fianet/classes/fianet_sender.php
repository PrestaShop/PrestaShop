<?php

class fianet_sender
{
	var $mode = null;
	var $orders = array();
	
	function fianet_sender()
	{
	}
	
	function add_order($order)
	{
		if (!is_array($order))
		{
			if (var_is_object_of_class($order, 'fianet_order_xml'))
			{
				$this->orders[] = $order;
			}
		}
		else
		{
			foreach ($order as $o)
			{
				if (var_is_object_of_class($o, 'fianet_order_xml'))
				{
					$this->orders[] = $o;
				}
			}
		}
	}
	
	//Envoie toutes les commandes une par une
	function send_orders_singet()
	{
		if (!count($this->orders) > 0)
		{
			fianet_insert_log("fianet_sender.php - send_orders_singet() <br />\nNo order to send.");
			return;
		}
		if (!$this->check_order_data())
		{
			fianet_insert_log("fianet_sender.php - send_orders_singet() <br />\nOrders list is not valid.");
			return;
		}
		
		foreach ($this->orders as $order)
		{
			$xml = '<?xml version="1.0" encoding="'. FIANET_ENCODING . '" ?>' . "\n";
			$xml .= $order->get_xml();
			$data['siteid'] = $order->info_commande->siteid;
			$data['controlcallback'] = clean_xml($xml);
			$this->send_fsock_singet($data);
		}
	}
	
	//Envoie les commandes par lot de 25
	function send_orders_stacking()
	{
		if (!count($this->orders) > 0)
		{
			fianet_insert_log("fianet_sender.php - send_orders_stackfast() <br />\nNo order to send.");
			return;
		}
		if (!$this->check_order_data())
		{
			fianet_insert_log("fianet_sender.php - send_orders_stackfast() <br />\nOrders list is not valid.");
			return;
		}
		$stack = array();
		$count = 0;
		$results = array();
		foreach ($this->orders as $order)
		{
			if ($count < 25)
			{
				$count++;
				$stack[] = $order;
			}
			elseif ($count == 25)
			{
				
				$results = array_merge($results, $this->process_stack($stack));
				$stack = array();
				$stack[] = $order;
				$count = 1;
			}
		}
		$results = array_merge($results, $this->process_stack($stack));
		return ($results);
	}
	
	//Recupère une liste d'évaluations
	function get_evaluation($order_list, $Separ = ',')
	{
		$evaluations = array();
		if (count($order_list) > 0)
		{
			$order_list_by_stack = array_chunk($order_list, 50, true);
			foreach ($order_list_by_stack as $stack)
			{
				$evaluations = array_merge($this->get_evaluation_by_stack($stack, $Separ), $evaluations);
			}
		}
		return ($evaluations);
	}

	
	function get_evaluation_by_stack($order_list, $Separ = ',')
	{
		if (count($order_list) > 0)
		{
			if ($this->mode == 'production')
			{
				$url = URL_SAC_PROD;
			}
			else
			{
				$url = URL_SAC_TEST;
			}
			$siteid = FIANET_SAC_SITE_ID;
			$pwd = FIANET_SAC_PWD;
			$mode = 'mini';
			$repFT = '0';
			$listID = '';
			foreach ($order_list as $id_order)
			{
				if ($listID != '')
				{
					$listID .= $Separ . $id_order;
				}
				else
				{
					$listID .= $id_order;
				}
			}
			$data['SiteID'] = $siteid;
			$data['Pwd'] = $pwd;
			$data['Mode'] = $mode;
			$data['RepFT'] = $repFT;
			$data['ListID'] = $listID;
			$data['Separ'] = $Separ;
			
			$s = new fianet_socket($url, URl_SAC_GETVALIDSTACK, 'POST', $data);
			$result = $s->send();
			$evaluations = array();
			if ($result === false)
			{
				fianet_insert_log("fianet_sender.php - get_evaluation() <br />\nError when opening file : <br />\n".$url);
			}
			else
			{
				$xmldata = $result['data'];
				$xml_array = xml2array($xmldata);
				//debug($xml_array);
				$evaluations = $this->process_result($xml_array);
				return ($evaluations);
			}
			return ($evaluations);
		}
	}
	
	/*
	méthodes privées
	*/

	function process_result($xml_array)
	{
		$evaluations = array();
		//debug($xml_array, 'xml_array');
		if (isset($xml_array['stack']['result']))
		{
			if (isset($xml_array['stack']['result'][0]))
			{
				foreach ($xml_array['stack']['result'] as $res)
				{
					if (eregi("error", $res['attr']['retour']))
					{
						fianet_insert_log("fianet_sender.php - process_result() <br />\nErreur : <br />\n".$res['attr']['message']);
					}
					else
					{
						//$index = count($evaluations);
						//$evaluations[$index] = $this->process_result_array($res);
						$eval = $this->process_result_array($res);
						if ($eval['refid'] != null)
						{
							$evaluations[$eval['refid']] = $eval;
						}
					}
				}
			}
			else
			{
			$res = $xml_array['stack']['result'];
			if (eregi("error", $res['attr']['retour']))
			{
				fianet_insert_log("fianet_sender.php - process_result() <br />\nErreur : <br />\n".$res['attr']['message']);
			}
			else
			{
				//$index = count($evaluations);
				$eval = $this->process_result_array($res);
				if ($eval['refid'] != null)
				{
					$evaluations[$eval['refid']] = $eval;
				}
			}
		}
		}
		else if (isset($xml_array['result']['transaction']))
		{
			if (isset($xml_array['result']['transaction'][0]))
			{
				foreach ($xml_array['result']['transaction'] as $transaction)
				{
					$eval = $this->process_transaction_array($transaction);
					//debug($eval);
					if ($eval['refid'] != null)
					{
						$evaluations[$eval['refid']] = $eval;
					}
				}
			}
			else
			{
				$transaction = $xml_array['result']['transaction'];
				$eval = $this->process_transaction_array($transaction);
				if ($eval['refid'] != null)
				{
					$evaluations[$eval['refid']] = $eval;
				}
			}
		}
		
		return ($evaluations);
	}

	function process_result_array($res)
	{
		//debug($res, 'Result');
		$eval['refid'] = $res['attr']['refid'];
		if ( $res['attr']['retour'] == 'absente')
		{
			$eval['info'] = 'absente';
		}
		else
		{
			if (isset($res['transaction'][0]))
			{
				$transaction = end($res['transaction']);
			}
			else
			{
				$transaction = $res['transaction'];
			}
			$etat = $this->process_transaction_array($transaction);
			$eval['eval'] = $etat['eval'];
			$eval['info'] = $etat['info'];
			$eval['cid'] = $etat['cid'];
		}
		return ($eval);
	}

	function process_transaction_array($transaction)
	{
		$eval = array();
		//debug($transaction);
		
		if ($transaction['attr']['avancement'] == 'error')
		{
			$eval['eval'] = 'error';
			$eval['info'] = $transaction['detail']['value'];
			$eval['cid'] = '';
			$eval['refid'] = $transaction['attr']['refid'];
		}
		elseif ($transaction['attr']['avancement'] == 'encours')
		{
			$eval['eval'] = 'encours';
			$eval['info'] = $transaction['detail']['value'];
			$eval['cid'] = '';
			$eval['refid'] = $transaction['attr']['refid'];
		}
		elseif ($transaction['attr']['avancement'] == 'traitee')
		{
			$eval['eval'] = $transaction['analyse']['eval']['value'];
			$eval['info'] = $transaction['analyse']['eval']['attr']['info'];
			$eval['cid']  = $transaction['attr']['cid'];
			$eval['refid'] = $transaction['attr']['refid'];
		}
		
		return ($eval);
	}

	function process_stack($stack)
	{
		$xml = '<?xml version="1.0" encoding="'. FIANET_ENCODING . '" ?>' . "\n";
		$xml .= '<stack>' . "\n";
		foreach ($stack as $order)
		{
			$xml .= $order->get_xml();
			if (!isset($siteid))
			{
				$siteid = $order->info_commande->siteid;
			}	
		}
		$xml .= '</stack>';
		$data['siteid'] = $siteid;
		$data['controlcallback'] = clean_xml($xml);
		$result = array();
		$result = $this->send_fsock_stacking($data);
		return ($result);
	}

	function process_result_stacking($xml_data)
	{
		$result = array();
		$xml_array = xml2array($xml_data);
		//debug($xml_array);
		if (isset($xml_array['validstack']['unluck']))
		{
			fianet_insert_log("fianet_sender.php - process_result_stacking() <br />\nError : <br />\n".$xml_array['validstack']['unluck']['value']);
			return ($result);
		}
		elseif (isset($xml_array['validstack']['result']))
		{
			$xml_array = $xml_array['validstack']['result'];
			//debug($xml_array);
			if (isset($xml_array[0]))
			{
				foreach ($xml_array as $transaction_result)
				{
					$index = count($result);
					$result[$index]['refid'] = $transaction_result['attr']['refid'];
					$result[$index]['etat'] = $transaction_result['attr']['avancement'];
					$result[$index]['details'] = $transaction_result['detail']['value'];
				}
			}
			else
			{
				$index = count($result);
				$result[$index]['refid'] = $xml_array['attr']['refid'];
				$result[$index]['etat'] = $xml_array['attr']['avancement'];
				$result[$index]['details'] = $xml_array['detail']['value'];
			}
		}
		return ($result);
	}

	function check_order_data()
	{
		$res = true;
		foreach ($this->orders as $order)
		{
			if (!var_is_object_of_class($order, 'fianet_order_xml'))
			{
				$res = false;
			}
		}
		return ($res);
	}

	function send_fsock_singet($data)
	{
		if ($this->mode == 'production')
		{
			$url_action = URL_SAC_PROD;
		}
		else
		{
			$url_action = URL_SAC_TEST;
		}
		$path = URl_SAC_SINGET;
		
		$s = new fianet_socket($url_action, $path, 'GET', $data);
		$res = $s->send();
		if ($res === false)
		{
			fianet_insert_log("fianet_sender.php - send_fsock_post() <br />\nError when connecting : <br />\n".$url_action . $path);
			return (false);
		}
		return (true);
	}

	function send_fsock_stacking($data)
	{
		if ($this->mode == 'production')
		{
			$url_action = URL_SAC_PROD;
		}
		else
		{
			$url_action = URL_SAC_TEST;
		}
		$path = URl_SAC_STACKING;
		
		$s = new fianet_socket($url_action, $path, 'POST', $data);
		$res = $s->send();
		if ($res === false)
		{
			fianet_insert_log("fianet_sender.php - send_fsock_post() <br />\nError when connecting : <br />\n".$url_action . $path);
			return (false);
		}
		else
		{
			$result = $this->process_result_stacking($res['data']);
			return ($result);
		}
	}
	
	function get_reevaluated_order()
	{
		if ($this->mode == 'production')
		{
			$url_action = URL_SAC_PROD;
		}
		else
		{
			$url_action = URL_SAC_TEST;
		}
		$path = URl_SAC_GETALERT;
		$mode = 'new';
		$output = 'mini';
		$repFT = '0';

		$data['SiteID'] = FIANET_SAC_SITE_ID;
		$data['Pwd'] = FIANET_SAC_PWD;
		$data['Mode'] = $mode;
		$data['Output'] = $output;
		$data['RepFT'] = $repFT;
		
		$s = new fianet_socket($url_action, $path, "POST", $data);
		$res = $s->send();
		$evaluations = array();
		if ($res === false)
		{
			fianet_insert_log("fianet_sender.php - get_reevaluated_order() <br />\nError when opening file : <br />\n".$url);
		}
		else
		{
			$xmldata = $res['data'];
			//Le code commenté suivant permet de tester la réception d'une réevaluation
			/*$xmldata = '<?xml version="1.0" encoding="ISO-8859-1" ?> 
					<result version="3.1" site="10" retour="trouvee" count="1">
					<transaction avancement="traitee" cid="95458898" refid="Refresh04">
					<detail>Paiement validé</detail> 
					<analyse>
					<eval date="20/05/2008 10:27:46" critere="16" validation="Acheteur connu" info="acheteur certifié">100</eval> 
					<classement id="1">Transactions à traiter/Nouvelles/Contrôle visuel</classement> 
					</analyse>
					</transaction>
					<transaction avancement="traitee" cid="15458898" refid="Refresh05">
					<detail>Paiement validé</detail> 
					<analyse>
					<eval date="20/05/2008 10:27:46" critere="16" validation="Acheteur connu" info="acheteur certifié">100</eval> 
					<classement id="1">Transactions à traiter/Nouvelles/Contrôle visuel</classement> 
					</analyse>
					</transaction>
					</result>';*/
			$xml_array = xml2array($xmldata);
			if (isset($xml_array['result']['attr']['retour']))
			{
				if ($xml_array['result']['attr']['retour'] == "param_error")
				{
					fianet_insert_log("fianet_sender.php - get_reevaluated_order() <br />\nParam_error : <br />\n".$xml_array['result']['attr']['message']."\n<br>$url_action");
				}
			}
			$evaluations = $this->process_result($xml_array);
		}
		return ($evaluations);
	}
}

