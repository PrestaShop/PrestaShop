<?php
	include_once(dirname(__FILE__).'/../../config/config.inc.php');
	require_once(_PS_MODULE_DIR_ . "dejala/dejalaconfig.php");
	require_once(_PS_MODULE_DIR_. "dejala/dejalautils.php");

	function doubleQuoteArray($array) {
		$l_array = array();
		foreach ($array as $key=>$val) {
			if (is_array($val))
				$l_array[$key] = doubleQuoteArray($val);
			else
				$l_array[$key] = ereg_replace('"', '""', $val);
		}
		return ($l_array);
	}

	global $smarty;
	$dejalaConfig = new DejalaConfig();
	$dejalaConfig->loadConfig();

	$from = Tools::getValue('datepickerFrom');
	$to = Tools::getValue('datepickerTo');
	if (!is_null($from) && !is_null($to) && (strlen($from) == 10) && (strlen($to) == 10) )
	{
				$dateFrom = mktime(0, 0, 1, (int)(substr($from, 3, 2)), (int)(substr($from, 0, 2)), (int)(substr($from, 6, 4)));
				$dateTo = mktime(23, 59, 59, (int)(substr($to, 3, 2)), (int)(substr($to, 0, 2)), (int)(substr($to, 6, 4)));
				if ($dateFrom > $dateTo) {
					$tmp = $dateTo;
					$dateTo = $dateFrom;
					$dateFrom = $tmp;
				}

				$djlUtil = new DejalaUtils();
				$deliveries = array();
				$responseArray = $djlUtil->getStoreDeliveries($dejalaConfig, $deliveries, array('from_utc'=>$dateFrom, 'to_utc'=>$dateTo));
				if ($responseArray['status']='200')
				{
					$l_deliveries = array();
					header("Content-type: text/csv");
					header("Content-disposition: attachment; filename=\"deliveries.csv\"");

					foreach ($deliveries as $key=>$delivery) {
						$l_delivery = doubleQuoteArray($delivery);
						$l_delivery['price']=ereg_replace('\.', ',', $l_delivery['price']);
						$l_delivery['creation_date'] = date('d/m/Y', $delivery['creation_utc']);
						$l_delivery['creation_time'] = date('H\hi', $delivery['creation_utc']);
						if (isset($delivery['shipping_start_utc'])) { 
							$l_delivery['shipping_date'] = date('d/m/Y', $delivery['shipping_start_utc']);
							$l_delivery['shipping_start'] = date('H\hi', $delivery['shipping_start_utc']);
							$l_delivery['shipping_stop'] = date('H\hi', (int)($delivery['shipping_start_utc']) + 3600*(int)($delivery['timelimit']) );
						}
						else {
							$delivery['shipping_date'] = '';
							$delivery['shipping_start'] = '';
							$delivery['shipping_stop'] = '';
						}
						if (isset($l_delivery['delivery_utc']))
						{
							$l_delivery['delivery_date'] = date('d/m/Y', $delivery['delivery_utc']);
							$l_delivery['delivery_time'] = date('H\hi', $delivery['delivery_utc']);
						}
						$l_deliveries[$key] = $l_delivery;
					}
					$smarty->assign('deliveries', $l_deliveries);
					$smarty->display(dirname(__FILE__).'/dejala_deliveries_csv.tpl');
				}
		}
