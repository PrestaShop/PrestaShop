<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminAccessControllerCore extends AdminController
{
	/* @var array : Black list of id_tab that do not have access */
	public $accesses_black_list = array();

	public function __construct()
	{
		$this->table = 'access';
		$this->className = 'Profile';
		$this->multishop_context = Shop::CONTEXT_ALL;
		$this->lang = false;
		$this->context = Context::getContext();

		// Blacklist AdminLogin
		$this->accesses_black_list[] = Tab::getIdFromClassName('AdminLogin');

		parent::__construct();
	}

	/**
	 * AdminController::renderForm() override
	 * @see AdminController::renderForm()
	 */
	public function renderForm()
	{
		$current_profile = (int)$this->getCurrentProfileId();
		$profiles = Profile::getProfiles($this->context->language->id);
		$tabs = Tab::getTabs($this->context->language->id);
		$accesses = array();
		foreach ($profiles as $profile)
			$accesses[$profile['id_profile']] = Profile::getProfileAccesses($profile['id_profile']);

		// Deleted id_tab that do not have access
		foreach ($tabs as $key => $tab)
		{
			// Don't allow permissions for unnamed tabs (ie. AdminLogin)
			if (empty($tab['name']))
				unset($tabs[$key]);

			foreach ($this->accesses_black_list as $id_tab)
				if ($tab['id_tab'] == (int)$id_tab)
					unset($tabs[$key]);
		}

		$modules = array();
		foreach ($profiles as $profile)
		{
			$modules[$profile['id_profile']] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT ma.`id_module`, m.`name`, ma.`view`, ma.`configure`
				FROM '._DB_PREFIX_.'module_access ma
				LEFT JOIN '._DB_PREFIX_.'module m
					ON ma.id_module = m.id_module
				WHERE id_profile = '.(int)$profile['id_profile'].'
				ORDER BY m.name
			');
			foreach ($modules[$profile['id_profile']] as &$module)
			{
				$m = Module::getInstanceById($module['id_module']);
				// the following condition handles invalid modules
				if ($m)
					$module['name'] = $m->displayName;
				else
					$this->warnings[] = sprintf($this->l('%s: module is installed in database, but its files are missing or incompatible.'), '<b>'.$module['name'].'</b>');
			}
		}

		$this->fields_form = array('');
		$this->tpl_form_vars = array(
			'profiles' => $profiles,
			'accesses' => $accesses,
			'tabs' => $tabs,
			'current_profile' => (int)$current_profile,
			'admin_profile' => (int)_PS_ADMIN_PROFILE_,
			'access_edit' => $this->tabAccess['edit'],
			'perms' => array('view', 'add', 'edit', 'delete'),
			'modules' => $modules,
			'link' => $this->context->link
		);

		return parent::renderForm();
	}

	/**
	 * AdminController::initContent() override
	 * @see AdminController::initContent()
	 */
	public function initContent()
	{
		$this->display = 'edit';
		// toolbar (save, cancel, new, ..)
		$this->initToolbar();
		unset($this->toolbar_btn['save']);
		unset($this->toolbar_btn['cancel']);

		if (!$this->loadObject(true))
			return;

		$this->content .= $this->renderForm();

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}

	public function ajaxProcessUpdateAccess()
	{
		if (_PS_MODE_DEMO_)
			throw new PrestaShopException(Tools::displayError('This functionality has been disabled.'));
		if ($this->tabAccess['edit'] != '1')
			throw new PrestaShopException(Tools::displayError('You do not have permission to edit here.'));

		if (Tools::isSubmit('submitAddAccess'))
		{
			$perm = Tools::getValue('perm');
			if (!in_array($perm, array('view', 'add', 'edit', 'delete', 'all')))
				throw new PrestaShopException('permission not exists');

			$enabled = (int)Tools::getValue('enabled');
			$id_tab = (int)Tools::getValue('id_tab');
			$id_profile = (int)Tools::getValue('id_profile');
			$where = '`id_tab`';
			$join = '';
			if (Tools::isSubmit('addFromParent'))
			{
				$where = 't.`id_parent`';
				$join = 'LEFT JOIN `'._DB_PREFIX_.'tab` t ON (t.`id_tab` = a.`id_tab`)';
			}

			if ($id_tab == -1 && $perm == 'all' && $enabled == 0)
				$sql = '
					UPDATE `'._DB_PREFIX_.'access` a
					SET `view` = '.(int)$enabled.', `add` = '.(int)$enabled.', `edit` = '.(int)$enabled.', `delete` = '.(int)$enabled.'
					WHERE `id_profile` = '.(int)$id_profile.' AND `id_tab` != '.(int)$this->id_tab_access;
			else if ($id_tab == -1 && $perm == 'all')
				$sql = '
					UPDATE `'._DB_PREFIX_.'access` a
					SET `view` = '.(int)$enabled.', `add` = '.(int)$enabled.', `edit` = '.(int)$enabled.', `delete` = '.(int)$enabled.'
					WHERE `id_profile` = '.(int)$id_profile;
			else if ($id_tab == -1)
				$sql = '
					UPDATE `'._DB_PREFIX_.'access` a
					SET `'.bqSQL($perm).'` = '.(int)$enabled.'
					WHERE `id_profile` = '.(int)$id_profile;
			else if ($perm == 'all')
				$sql = '
					UPDATE `'._DB_PREFIX_.'access` a
					'.$join.'
					SET `view` = '.(int)$enabled.', `add` = '.(int)$enabled.', `edit` = '.(int)$enabled.', `delete` = '.(int)$enabled.'
					WHERE '.$where.'  = '.(int)$id_tab.'
						AND `id_profile` = '.(int)$id_profile;
			else
				$sql = '
					UPDATE `'._DB_PREFIX_.'access` a
					'.$join.'
					SET `'.bqSQL($perm).'` = '.(int)$enabled.'
					WHERE '.$where.' = '.(int)$id_tab.'
						AND `id_profile` = '.(int)$id_profile;

			$res = Db::getInstance()->execute($sql) ? 'ok' : 'error';

			die($res);
		}
	}

	public function ajaxProcessUpdateModuleAccess()
	{
		if (_PS_MODE_DEMO_)
			throw new PrestaShopException(Tools::displayError('This functionality has been disabled.'));
		if ($this->tabAccess['edit'] != '1')
			throw new PrestaShopException(Tools::displayError('You do not have permission to edit here.'));

		if (Tools::isSubmit('changeModuleAccess'))
		{
			$perm = Tools::getValue('perm');
			$enabled = (int)Tools::getValue('enabled');
			$id_module = (int)Tools::getValue('id_module');
			$id_profile = (int)Tools::getValue('id_profile');

			if (!in_array($perm, array('view', 'configure')))
				throw new PrestaShopException('permission not exists');

			if ($id_module == -1)
				$sql = '
					UPDATE `'._DB_PREFIX_.'module_access`
					SET `'.bqSQL($perm).'` = '.(int)$enabled.'
					WHERE `id_profile` = '.(int)$id_profile;
			else
				$sql = '
					UPDATE `'._DB_PREFIX_.'module_access`
					SET `'.bqSQL($perm).'` = '.(int)$enabled.'
					WHERE `id_module` = '.(int)$id_module.'
						AND `id_profile` = '.(int)$id_profile;

			$res = Db::getInstance()->execute($sql) ? 'ok' : 'error';

			die($res);
		}
	}

	/**
	* Get the current profile id
	*
	* @return the $_GET['profile'] if valid, else 1 (the first profile id)
	*/
	public function getCurrentProfileId()
	{
		return (isset($_GET['id_profile']) && !empty($_GET['id_profile']) && is_numeric($_GET['id_profile'])) ? (int)$_GET['id_profile'] : 1;
	}
}
