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

class AdminStatesControllerCore extends AdminController
{
	public function __construct()
	{
		$this->table = 'state';
		$this->className = 'State';
		$this->lang = false;
		$this->requiredDatabase = true;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;
		
		$this->bulk_actions = array(
			'delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')),
			'enableSelection' => array('text' => $this->l('Enable selection')),
			'disableSelection' => array('text' => $this->l('Disable selection')),
			'affectzone' => array('text' => $this->l('Affect a new zone'))
			);
		
		$this->fields_list = array(
			'id_state' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name'),
				'filter_key' => 'a!name'
			),
			'iso_code' => array(
				'title' => $this->l('ISO code'),
				'align' => 'center',
				'width' => 75
			),
			'zone' => array(
				'title' => $this->l('Zone'),
				'width' => 100,
				'filter_key' => 'z!name'
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'width' => 70,
				'active' => 'status',
				'filter_key' => 'a!active',
				'align' => 'center',
				'type' => 'bool',
				'orderby' => false
			)
		);

		parent::__construct();
	}

	public function renderList()
	{
		$this->_select = 'z.`name` AS zone';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'zone` z ON (z.`id_zone` = a.`id_zone`)';

				$this->tpl_list_vars['zones'] = Zone::getZones();
		return parent::renderList();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('States'),
				'image' => '../img/admin/world.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 30,
					'maxlength' => 32,
					'required' => true,
					'desc' => $this->l('State name to display in addresses and on invoices')
				),
				array(
					'type' => 'text',
					'label' => $this->l('ISO code:'),
					'name' => 'iso_code',
					'size' => 7,
					'maxlength' => 7,
					'required' => true,
					'class' => 'uppercase',
					'desc' => $this->l('1 to 4 letter ISO code')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Country:'),
					'name' => 'id_country',
					'required' => false,
					'default_value' => (int)$this->context->country->id,
					'options' => array(
						'query' => Country::getCountries($this->context->language->id, false, true),
						'id' => 'id_country',
						'name' => 'name',
					),
					'desc' => $this->l('Country where state, region or city is located')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Zone:'),
					'name' => 'id_zone',
					'required' => false,
					'options' => array(
						'query' => Zone::getZones(),
						'id' => 'id_zone',
						'name' => 'name'
					),
					'desc' => array(
						$this->l('Geographical zone where this state is located'),
						$this->l('Used for shipping')
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Status:'),
					'name' => 'active',
					'required' => false,
					'class' => 't',
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" />'
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" />'
						)
					),
					'desc' => $this->l('Enabled or disabled')
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		return parent::renderForm();
	}

	public function postProcess()
	{
		if (Tools::isSubmit($this->table.'Orderby') || Tools::isSubmit($this->table.'Orderway'))
			$this->filter = true;
		
		if (!isset($this->table))
			return false;
			
		if (!Tools::getValue('id_'.$this->table))
		{
			if (Validate::isStateIsoCode(Tools::getValue('iso_code')) && State::getIdByIso(Tools::getValue('iso_code'), Tools::getValue('id_country')))
				$this->errors[] = Tools::displayError('This ISO code already exists, you cannot create two states with the same ISO code in the same country');
		}
		else if (Validate::isStateIsoCode(Tools::getValue('iso_code')))
		{
			$id_state = State::getIdByIso(Tools::getValue('iso_code'), Tools::getValue('id_country'));
			if ($id_state && $id_state != Tools::getValue('id_'.$this->table))
				$this->errors[] = Tools::displayError('This ISO code already exists, you cannot create two states with the same ISO code in the same country');
		}

		/* Delete object */
		if (isset($_GET['delete'.$this->table]))
		{
			// set token
			$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;

			if ($this->tabAccess['delete'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()) && isset($this->fieldImageSettings))
				{
					if (!$object->isUsed())
					{
						// check if request at least one object with noZeroObject
						if (isset($object->noZeroObject) && count($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1)
							$this->errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
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
							$this->errors[] = Tools::displayError('An error occurred during deletion.');
						}
					}
					else
						$this->errors[] = Tools::displayError('This state was used in at least one address, it cannot be removed');
				}
				else
					$this->errors[] = Tools::displayError('An error occurred while deleting object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else
			parent::postProcess();
	}
}


