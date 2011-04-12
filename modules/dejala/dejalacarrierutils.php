<?php

/**
 * Utility class to manipulate dejala Carrier data
 **/
class DejalaCarrierUtils
{
	/**
		* creates of a dejala carrier corresponding to $dejalaProduct
		*/
	public static function createDejalaCarrier($dejalaConfig)
	{
		// MFR091130 - get id zone from the country used in the module (if the store zones were customized) - default is 1 (Europe)
		$id_zone = 1;
		$moduleCountryIsoCode = strtoupper($dejalaConfig->country);
		$countryID = Country::getByIso($moduleCountryIsoCode);
		if ((int)($countryID))
		$id_zone = Country::getIdZone($countryID);

		//TODO Will have to review this and apply proper code.
		$trg_id = 1 ;
//		$vatRate = "19.6";
//		// MFR091130 - get or create the tax & attach it to our zone if needed
//		$id_tax = Tax::getTaxIdByRate((float)$vatRate);
//		$trg_id = 0;
//		if (!$id_tax)
//		{
//			$tax = new Tax();
//			$tax->rate = $vatRate;
//			$defaultLanguage = Configuration::get('PS_LANG_DEFAULT');
//			$tax->name[$defaultLanguage] = $tax->rate . '%';
//			$tax->add();
//			$id_tax = $tax->id;
//
//			$trg = new TaxRulesGroup();
//			$trg->name = 'Dejala '.$tax->name[$defaultLanguage];
//			$trg->active = 1;
//			if ($trg->save())
//			{
//				$trg_id = $trg->id;
//
//				$tr = new TaxRule();
//				$tr->id_tax_rules_group = $trg_id;
//				$tr->id_country = (int) $countryID;
//				$tr->id_state = 0;
//				$tr->id_tax = (int)$tax->id;
//				$tr->state_behavior = 0;
//				$tr->save();
//			}
//		}

		$carrier = new Carrier();
		$carrier->name = 'dejala';
		$carrier->id_tax_rules_group = $trg_id;
		$carrier->url = 'http://tracking.dejala.' . $dejalaConfig->country . '/tracker/@';
		$carrier->active = true;
		$carrier->deleted = 0;
		$carrier->shipping_handling = false;
		$carrier->range_behavior = 0;
		$carrier->is_module = 1;
		$carrier->external_module_name = 'dejala' ;
		$carrier->shipping_external = 1 ;
		$carrier->need_range = 0 ;

		$languages = Language::getLanguages(true);
		foreach ($languages as $language) {
			$carrier->delay[$language['id_lang']] = 'Dejala' ;
		}
		$carrier->add();

		$carrier->addZone($id_zone) ;
//		$sql = 'INSERT INTO `'._DB_PREFIX_.'carrier_zone` (`id_carrier` , `id_zone`) VALUES ('.(int)($carrier->id).', ' . (int)($id_zone) . ')';
//		Db::getInstance()->Execute($sql);

//		$rangeW = new RangeWeight();
//		$rangeW->id_carrier = $carrier->id;
//		$rangeW->delimiter1 = 0;
//		$rangeW->delimiter2 = $dejalaProduct['max_weight'];
//		$rangeW->add();
//		$vat_factor = (1+ ($dejalaProduct['vat'] / 100));
//		$priceTTC = round(($dejalaProduct['price']*$vat_factor) + $dejalaProduct['margin'], 2);
//		$priceHT = round($priceTTC/$vat_factor, 2);
//		$priceList = '(NULL'.','.$rangeW->id.','.$carrier->id.','.$id_zone.','.$priceHT.')';
//		$carrier->addDeliveryPrice($priceList);

		return true;
	}

	public static function getCarrierByName($name) {
		global $cookie ;

		$carriers = Carrier::getCarriers($cookie->id_lang, true, false, false, NULL, ALL_CARRIERS);
		foreach($carriers as $carrier) {
			if (!$carrier['deleted'] AND $carrier['external_module_name'] == $name) {
				return new Carrier($carrier['id_carrier']) ;
			}
		}
	}

}

