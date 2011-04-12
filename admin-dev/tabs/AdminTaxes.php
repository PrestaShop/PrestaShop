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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminTaxes extends AdminTab
{
	public function __construct()
	{
		global $cookie;
	 	$this->table = 'tax';
	 	$this->className = 'Tax';
	 	$this->lang = true;
	 	$this->edit = true;
	 	$this->delete = true;

		$this->fieldsDisplay = array(
		'id_tax' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'name' => array('title' => $this->l('Name'), 'width' => 140),
		'rate' => array('title' => $this->l('Rate'), 'align' => 'center', 'suffix' => '%', 'width' => 50),
		'active' => array('title' => $this->l('Enabled'), 'width' => 25, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false));

		$this->optionTitle = $this->l('Tax options');
		$this->_fieldsOptions = array(
		'PS_TAX' => array('title' => $this->l('Enable tax:'), 'desc' => $this->l('Select whether or not to include tax on purchases'), 'cast' => 'intval', 'type' => 'bool'),
		'PS_TAX_DISPLAY' => array('title' => $this->l('Display tax in cart:'), 'desc' => $this->l('Select whether or not to display tax on a distinct line in the cart'), 'cast' => 'intval', 'type' => 'bool'),
		'PS_TAX_ADDRESS_TYPE' => array('title' => $this->l('Base on:'), 'cast' => 'pSQL', 'type' => 'select', 'list' => array(array('name' => $this->l('Invoice Address'), 'id' => 'id_address_invoice'), array('name' => $this->l('Delivery Address'), 'id' => 'id_address_delivery')), 'identifier' => 'id'),
		'PS_USE_ECOTAX' => array('title' => $this->l('Use ecotax'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
		);

		if (Configuration::get('PS_USE_ECOTAX'))
			$this->_fieldsOptions['PS_ECOTAX_TAX_RULES_GROUP_ID'] = array('title' => $this->l('Ecotax:'), 'desc' => $this->l('The tax to apply on the ecotax (e.g., French ecotax: 19.6%).'),
			'cast' => 'intval', 'type' => 'select', 'identifier' => 'id_tax', 'identifier' => 'id_tax_rules_group', 'list' => TaxRulesGroup::getTaxRulesGroupsForOptions());

		parent::__construct();
	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;
		$zones = Zone::getZones(true);
		$states = State::getStates((int)$cookie->id_lang);

		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/dollar.gif" />'.$this->l('Taxes').'</legend>
				<label>'.$this->l('Name:').' </label>
				<div class="margin-form">';
				foreach ($this->_languages as $language)
					echo '
					<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="name_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'name', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>';
				$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'name', 'name');
		echo '	<p class="clear">'.$this->l('Tax name to display in cart and on invoice, e.g., VAT').'</p>
				</div>
				<label>'.$this->l('Rate:').' </label>
				<div class="margin-form">
					<input type="text" size="4" maxlength="6" name="rate" value="'.htmlentities($this->getFieldValue($obj, 'rate'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					<p class="clear">'.$this->l('Format: XX.XX or XX.XXX (e.g., 19.60 or 13.925)').'</p>
				</div>
				<label>'.$this->l('Enable:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.($this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.(!$this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}

	public function postProcess()
	{
		global $currentIndex;

		if(Tools::getValue('submitAdd'.$this->table))
		{
		 	/* Checking fields validity */
			$this->validateRules();
			if (!sizeof($this->_errors))
			{
				$id = (int)(Tools::getValue('id_'.$this->table));

				/* Object update */
				if (isset($id) AND !empty($id))
				{
					if ($this->tabAccess['edit'] === '1')
					{
						$object = new $this->className($id);
						if (Validate::isLoadedObject($object))
						{
							$this->copyFromPost($object, $this->table);
							$result = $object->update(false, false);

							if (!$result)
								$this->_errors[] = Tools::displayError('An error occurred while updating object.').' <b>'.$this->table.'</b>';
							elseif ($this->postImage($object->id))
								{
									Tools::redirectAdmin($currentIndex.'&id_'.$this->table.'='.$object->id.'&conf=4'.'&token='.$this->token);
								}
						}
						else
							$this->_errors[] = Tools::displayError('An error occurred while updating object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
					}
					else
						$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
				}

				/* Object creation */
				else
				{
					if ($this->tabAccess['add'] === '1')
					{
						$object = new $this->className();
						$this->copyFromPost($object, $this->table);
						if (!$object->add())
							$this->_errors[] = Tools::displayError('An error occurred while creating object.').' <b>'.$this->table.'</b>';
						elseif (($_POST['id_'.$this->table] = $object->id /* voluntary */) AND $this->postImage($object->id) AND $this->_redirect)
						{
							Tools::redirectAdmin($currentIndex.'&id_'.$this->table.'='.$object->id.'&conf=3'.'&token='.$this->token);
						}
					}
					else
						$this->_errors[] = Tools::displayError('You do not have permission to add here.');
				}
			}
		}
		else
			parent::postProcess();
	}

	protected function _displayDeleteLink($token = NULL, $id)
	{
	    global $currentIndex;

		$_cacheLang['Delete'] = $this->l('Delete', __CLASS__, TRUE, FALSE);

   		$_cacheLang['DeleteItem'] = $this->l('Delete item #', __CLASS__, TRUE, FALSE).$id.' ?)';
        if (TaxRule::isTaxInUse($id))
            $_cacheLang['DeleteItem'] = $this->l('This tax is currently in use in a tax rule. Are you sure?');

		echo '
			<a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&delete'.$this->table.'&token='.($token!=NULL ? $token : $this->token).'" onclick="return confirm(\''.$_cacheLang['DeleteItem'].'\');">
			<img src="../img/admin/delete.gif" alt="'.$_cacheLang['Delete'].'" title="'.$_cacheLang['Delete'].'" /></a>';
	}

	protected function _displayEnableLink($token, $id, $value, $active,  $id_category = NULL, $id_product = NULL)
	{
	    global $currentIndex;

        $confirm = ($value AND TaxRule::isTaxInUse($id)) ? 'onclick="return confirm(\''. $this->l('This tax is currently in use in a tax rule. If you continue this tax will be removed from the tax rule, are you sure?').'\')"' : '';

	    echo '<a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&'.$active.
	        ((int)$id_category AND (int)$id_product ? '&id_category='.$id_category : '').'&token='.($token!=NULL ? $token : $this->token).'" '.$confirm.'>
	        <img src="../img/admin/'.($value ? 'enabled.gif' : 'disabled.gif').'"
	        alt="'.($value ? $this->l('Enabled') : $this->l('Disabled')).'" title="'.($value ? $this->l('Enabled') : $this->l('Disabled')).'" /></a>';
	}
}

