<?php

function process_result($xml_array)
{
	$evaluations = array();
	//debug($xml_array, 'xml_array');
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
				$index = count($evaluations);
				$evaluations[$index] = $this->process_result_array($res);
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
			$index = count($evaluations);
			$evaluations[$index] = $this->process_result_array($res);
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
		$eval['refid'] = $transaction['attr']['refid'];
		$eval['eval'] = 'error';
		$eval['info'] = $transaction['detail']['value'];
		$eval['cid'] = '';
	}
	elseif ($transaction['attr']['avancement'] == 'encours')
	{
		$eval['refid'] = $transaction['attr']['refid'];
		$eval['eval'] = 'encours';
		$eval['info'] = $transaction['detail']['value'];
		$eval['cid'] = '';
	}
	elseif ($transaction['attr']['avancement'] == 'traitee')
	{
		$eval['refid'] = $transaction['attr']['refid'];
		$eval['eval'] = $transaction['analyse']['eval']['value'];
		$eval['info'] = $transaction['analyse']['eval']['attr']['info'];
		$eval['cid']  = $transaction['attr']['cid'];
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
	$this->send_fsock_stacking($data);
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
	if ($mode == 'production')
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
		//debug($result);
		return ($result);
	}
}

function process_result_nostack($xml_array)
{
	$evaluations = array();
	//debug($xml_array, 'xml_array');
	if (isset($xml_array['result']['transaction'][0]))
	{
		foreach ($xml_array['result']['transaction'] as $res)
		{
			$eval = $this->process_transaction_array($res);
			if ($eval['refid'] != null)
			{
				$evaluations[$eval['refid']] = $eval;
			} 
		}
	}
	else
	{
		$res = $xml_array['result']['transaction'];
		$eval = $this->process_transaction_array($res);
		if ($eval['refid'] != null)
		{
			$evaluations[$eval['refid']] = $eval;
		} 
	}
	return ($evaluations);
}

