<?php

class Autoupgrade extends Module
{
		function __construct()
	{
		$this->name = 'autoupgrade';
		$this->tab = 'administration';
		$this->version = 0.1;

		if (!defined('_PS_ADMIN_DIR_'))
		{
			if (defined('_PS_ADMIN_DIR_'))
				define('_PS_ADMIN_DIR_', _PS_ADMIN_DIR_);
			else
			{
				$this->_errors[] = $this->l('This version of PrestaShop cannot be upgraded :_PS_ADMIN_DIR_ constant is missing');
				$autoupgradeCanWork = false;
			}
		}

		parent::__construct();

		$this->displayName = $this->l('Autoupgrade module');
		$this->description = $this->l('Provides an automated method to upgrade your shop to the last PrestaShop version');

	}
	function install()
	{

		$res = true;
		// before adding AdminSelfUpgrade, we should remove AdminUpgrade
			$idTab = Tab::getIdFromClassName('AdminUpgrade');

			if ($idTab)
			{
			$tab = new Tab($idTab);
				$res &= $tab->delete();
		}

		$idTab = Tab::getIdFromClassName('AdminSelfUpgrade');
		// Then we add AdminSelfUpgrade only if not exists
		if (!$idTab)
		{
			$tab = new Tab();
			$tab->class_name = 'AdminSelfUpgrade';
			$tab->module = 'autoupgrade';
			$tab->id_parent = 9;
			$tab->name = array_fill(1,sizeof(Language::getLanguages(false)), 'Upgrade');
			$res &= $tab->save();
		}
		else
			$tab = new Tab($idTab);
			Configuration::updateValue('PS_AUTOUPDATE_MODULE_IDTAB',$tab->id);

		$autoupgradeDir = _PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'autoupgrade';
		if(!file_exists($autoupgradeDir))
			$res &= @mkdir($autoupgradeDir);
		if(file_exists($autoupgradeDir.DIRECTORY_SEPARATOR.'ajax-upgradetab.php'))
			$res &= unlink($autoupgradeDir.DIRECTORY_SEPARATOR.'ajax-upgradetab.php');
		if (!defined('_PS_MODULE_DIR_'))
		{
			define('_PS_MODULE_DIR_', _PS_ROOT_DIR_.'/modules/');
		}

		$res &= copy(_PS_MODULE_DIR_.'autoupgrade/ajax-upgradetab.php',$autoupgradeDir . DIRECTORY_SEPARATOR . 'ajax-upgradetab.php');
		$res &= copy(_PS_MODULE_DIR_.'autoupgrade/logo.gif',_PS_ROOT_DIR_. DIRECTORY_SEPARATOR . 'img/t/AdminSelfUpgrade.gif');

		if (!$res
			OR !Tab::getIdFromClassName('AdminSelfUpgrade')
			OR !parent::install()
		)
			return false;

		return true;
	}
	public function uninstall()
	{
		$idtab = Configuration::get('PS_AUTOUPDATE_MODULE_IDTAB');
		$tab = new Tab($idtab,1);
		$res = $tab->delete();
		if(file_exists(_PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'tabs'.'AdminUpgrade.php'))
		{
			// Should we create the correct AdminUpgrade tab (not the module)
			if($idOldTab = Tab::getIdFromClassName('AdminUpgrade'))
			{
				$tab = new Tab($idOldTab);
				$res &= $tab->delete();
		}
			$res &= unlink(_PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'tabs'.'AdminUpgrade.php');
		}

		if (file_exists(_PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'autoupgrade'.DIRECTORY_SEPARATOR.'ajax-upgradetab.php'))
		$res &= @unlink(_PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'autoupgrade'.DIRECTORY_SEPARATOR.'ajax-upgradetab.php');
		if (!$res OR !parent::uninstall())
			return false;

		return true;
	}


	}
