<?php
class Autoupgrade extends Module
{
		function __construct()
	{
		$this->name = 'autoupgrade';
		$this->tab = 'admin';
		$this->version = 0.1;

		if (!defined('_PS_ADMIN_DIR_'))
		{
			if (defined('PS_ADMIN_DIR'))
				define('_PS_ADMIN_DIR_',PS_ADMIN_DIR);
			else
			{
				$this->_errors[] = $this->l('This version of PrestaShop cannot be upgraded : PS_ADMIN_DIR constant is missing');
				$autoupgradeCanWork = false;
			}
		}

		parent::__construct();

		$this->displayName = $this->l('Autoupgrade module');
		$this->description = $this->l('Provides an automated method to upgrade your shop to the last PrestaShop version');

	}
	function install()
	{
		$autoupgradeCanWork = true;
		if (!$autoupgradeCanWork)
			return false;

		// How to remove an Admin Tab ?
		if (version_compare(_PS_VERSION_,'1.4.4.0','=') OR version_compare(_PS_VERSION_,'1.4.4.1','='))
		{
			$idTab = Tab::getIdFromClassName('AdminUpgrade');
			$tab = new Tab($idTab);
			$tab->delete();
		}
		else
		{
			$tab = new Tab();
			$tab->class_name = 'AdminSelfUpgrade';
			$tab->module = 'autoupgrade';
			$tab->id_parent = 9;
			$tab->name = array_fill(1,sizeof(Language::getLanguages(false)), 'Upgrade');
			$res = $tab->save();
			Configuration::updateValue('PS_AUTOUPDATE_MODULE_IDTAB',$tab->id);
		}

		$autoupgradeDir = _PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'autoupgrade';
		if(!file_exists($autoupgradeDir))
			$res &= @mkdir($autoupgradeDir);
		if(file_exists($autoupgradeDir.DIRECTORY_SEPARATOR.'ajax-upgradetab.php'))
			$res &= unlink($autoupgradeDir.DIRECTORY_SEPARATOR.'ajax-upgradetab.php');
		$path = dirname(__FILE__).'/';
		
		$res &= copy($path.'ajax-upgradetab.php',$autoupgradeDir . DIRECTORY_SEPARATOR . 'ajax-upgradetab.php');
		
		if (!$res 
			OR !(Hook::get('backOfficeHeader') AND !$this->registerHook('backOfficeHeader'))
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
			$tab = new Tab();
			$tab->class_name = 'AdminUpgrade';
			$tab->module = false;
			$tab->id_parent = 9;
			$tab->name = array_fill(1,sizeof(Language::getLanguages(false)), 'Upgrade');
			$res = $tab->save();
		}
		
		$res &= @unlink(_PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'autoupgrade'.DIRECTORY_SEPARATOR.'ajax-upgradetab.php');
		if($res OR !parent::uninstall())
			return false;

		return true;
	}

	public function hookBackOfficeHeader($params)
	{
		echo '<link type="text/css" rel="stylesheet" href="../modules/autoupgrade/autoupgrade.css" />';

	}

}
