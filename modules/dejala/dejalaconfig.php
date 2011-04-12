<?php


class DejalaConfig
{
	public $country = "fr";
	public $login = "";
	public $password = "";
	public $installed = 1;
	public $useSSL = 0;
	public $serviceURL = 'http://v1.services.dejala.fr/services';
	public $sandboxServiceURL = 'http://t1.services.dejala.fr/services';
	public $mode = 'TEST';
	public $trigerringStatuses = '2';
	// Faire apparaitre/disparaitre le module sur le front
//	public $active = 0;
	public $visibility_status = "invisible" ;
	public $visible_users_list = "";
	public $internal_version ;

	// Not stored in the local config. Only sent on shop creation
	public $storeUrl ;

	public function saveConfig()
	{
		if ( Configuration::updateValue('DJL_SERVICE_URL', $this->serviceURL) == false
		OR Configuration::updateValue('DJL_SANDBOX_SERVICE_URL', $this->sandboxServiceURL) == false
		OR Configuration::updateValue('DJL_USE_SSL', $this->useSSL) == false
		OR Configuration::updateValue('DJL_MODE', $this->mode) == false
		OR Configuration::updateValue('DJL_LOGIN', $this->login) == false
		OR Configuration::updateValue('DJL_PASSWORD', $this->password) == false
		OR Configuration::updateValue('DJL_INSTALLED', $this->installed) == false
		OR Configuration::updateValue('DJL_TRIGERRING_STATUSES', $this->trigerringStatuses) == false
		OR Configuration::updateValue('DJL_COUNTRY', $this->country) == false
		OR Configuration::updateValue('DJL_VISIBILITY_STATUS', $this->visibility_status) == false
		OR Configuration::updateValue('DJL_VISIBLE_USERS_LIST', $this->visible_users_list) == false
		OR Configuration::updateValue('DJL_VERSION', $this->internal_version) == false
		) {
			return (false);
		}

		// Backward compatibility. Garbage collect
		Configuration::deleteByName('DJL_ACTIVE');
		return (true);
	}
	
	public function uninstall() {
		Configuration::deleteByName('DJL_SERVICE_URL');
		Configuration::deleteByName('DJL_SANDBOX_SERVICE_URL');
		Configuration::deleteByName('DJL_USE_SSL');
		Configuration::deleteByName('DJL_MODE');
		Configuration::deleteByName('DJL_LOGIN');
		Configuration::deleteByName('DJL_PASSWORD');
		Configuration::deleteByName('DJL_INSTALLED');
		Configuration::deleteByName('DJL_TRIGERRING_STATUSES');
		Configuration::deleteByName('DJL_COUNTRY');
		Configuration::deleteByName('DJL_ACTIVE');
		Configuration::deleteByName('DJL_VISIBILITY_STATUS');
		Configuration::deleteByName('DJL_VISIBLE_USERS_LIST');
		// DO NOT DELETE DJL_VERSION AS WE LEAVE THE TABLE IN THE DB. 
	}

	// load configuration
	public function loadConfig()
	{
		$this->installed = html_entity_decode(Configuration::get('DJL_INSTALLED'), ENT_COMPAT, 'UTF-8');
		$this->useSSL = (int)(html_entity_decode(Configuration::get('DJL_USE_SSL'), ENT_COMPAT, 'UTF-8'));
		$this->serviceURL = html_entity_decode(Configuration::get('DJL_SERVICE_URL'), ENT_COMPAT, 'UTF-8');
		$this->sandboxServiceURL = html_entity_decode(Configuration::get('DJL_SANDBOX_SERVICE_URL'), ENT_COMPAT, 'UTF-8');
		$this->mode = html_entity_decode(Configuration::get('DJL_MODE'), ENT_COMPAT, 'UTF-8');
		$this->login = html_entity_decode(Configuration::get('DJL_LOGIN'), ENT_COMPAT, 'UTF-8');
		$this->password = html_entity_decode(Configuration::get('DJL_PASSWORD'), ENT_COMPAT, 'UTF-8');
		$this->trigerringStatuses = html_entity_decode(Configuration::get('DJL_TRIGERRING_STATUSES'), ENT_COMPAT, 'UTF-8');

		$this->shippingPrefix = html_entity_decode(Configuration::get('DJL_SHIPPING_PREFIX'), ENT_COMPAT, 'UTF-8');
		$this->country = html_entity_decode(Configuration::get('DJL_COUNTRY'), ENT_COMPAT, 'UTF-8');
		if (strlen($this->country) == 0)
			$this->country = 'fr';
		$this->visibility_status = html_entity_decode(Configuration::get('DJL_VISIBILITY_STATUS'), ENT_COMPAT, 'UTF-8');
		$this->visible_users_list = html_entity_decode(Configuration::get('DJL_VISIBLE_USERS_LIST'), ENT_COMPAT, 'UTF-8');
		$this->internal_version = Configuration::get('DJL_VERSION') ;

		// Backward compatibility
		$activeVal = html_entity_decode(Configuration::get('DJL_ACTIVE'), ENT_COMPAT, 'UTF-8') ;
		if ($activeVal != "") {
			$this->active = (int)($activeVal);
			$this->visibility_status = ($this->active == 0 ? "invisible" : "visible") ;
		}
		if ($this->visibility_status == "") {
			$this->visibility_status = "invisible" ;
		}
	}

	/**
	 * Renvoie l'URI de base à utiliser
	 * $forceMode TEST/PROD pour forcer l'utilisation d'une des 2 plateformes
	 **/
	public function getRootServiceURI($forceMode = NULL)
	{
		$l_serviceURL = NULL;
		$useMode = $this->mode;
		// mode forcé ?
		if ($forceMode && ($forceMode == 'PROD'))
			$useMode = 'PROD';
		else if ($forceMode)
			$useMode = 'TEST';

		if ($useMode === 'PROD')
			$l_serviceURL = $this->serviceURL;
		else
			$l_serviceURL = $this->sandboxServiceURL;
		if ($this->useSSL !== 0) {
			if (0 === strpos($l_serviceURL, 'http:'))
				$l_serviceURL = 'https'.substr($l_serviceURL, 4);
		}
		return ($l_serviceURL);
	}
}
