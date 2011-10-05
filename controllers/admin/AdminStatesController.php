<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminStatesControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'state';
	 	$this->className = 'State';
	 	$this->lang = false;
	 	$this->edit = true;
	 	$this->delete = true;
		$this->requiredDatabase = true;

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->_select = 'z.`name` AS zone';
	 	$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'zone` z ON (z.`id_zone` = a.`id_zone`)';

		$this->fieldsDisplay = array(
			'id_state' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'name' => array('title' => $this->l('Name'), 'width' => 140, 'filter_key' => 'a!name'),
			'iso_code' => array('title' => $this->l('ISO code'), 'align' => 'center', 'width' => 50),
			'zone' => array('title' => $this->l('Zone'), 'width' => 100, 'filter_key' => 'z!name')
		);

		$this->template = 'adminStates.tpl';

		parent::__construct();
	}

	public function postProcess()
	{
		if (!isset($this->table))
			return false;

		/* Delete object */
		if (isset($_GET['delete'.$this->table]))
		{
			// set token
			$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;

			// Sub included tab postProcessing
			$this->includeSubTab('postProcess', array('submitAdd1', 'submitDel', 'delete', 'submitFilter', 'submitReset'));

			if ($this->tabAccess['delete'] === '1')
			{

				if (Validate::isLoadedObject($object = $this->loadObject()) && isset($this->fieldImageSettings))
				{
					if (!$object->isUsed())
					{
						// check if request at least one object with noZeroObject
						if (isset($object->noZeroObject) && count($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1)
							$this->_errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
						else
						{
							if ($this->deleted)
							{
								$object->deleted = 1;
								if ($object->update())
									Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.$token);
							}
							else if ($object->delete())
								Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.$token);
							$this->_errors[] = Tools::displayError('An error occurred during deletion.');
						}
					}
					else
						$this->_errors[] = Tools::displayError('This state is currently in use');
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while deleting object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else
			parent::postProcess();
	}

	public function displayForm($is_main_tab = true)
	{
		$this->content = parent::displayForm($is_main_tab);

		if (!($obj = $this->loadObject(true)))
			return;
		$current_shop = Shop::initialize();

		$this->context->smarty->assign('tab_form', array(
			'current' => self::$currentIndex,
			'table' => $this->table,
			'token' => $this->token,
			'id' => $obj->id,
			'name' => $this->getFieldValue($obj, 'name'),
			'iso_code' => $this->getFieldValue($obj, 'iso_code'),
			'countries' => Country::getCountries($this->context->language->id, false, true),
			'id_country' => $this->getFieldValue($obj, 'id_country'),
			'zones' => Zone::getZones(),
			'id_zone' => $this->getFieldValue($obj, 'id_zone'),
			'active' => $this->getFieldValue($obj, 'active') ? true : false
		));
	}

	public function initContent()
	{
		if ($this->display != 'edit')
			$this->display = 'list';

		parent::initContent();
	}

}


