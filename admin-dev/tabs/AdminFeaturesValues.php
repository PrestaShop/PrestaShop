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

class AdminFeaturesValues extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'feature_value';
	 	$this->className = 'FeatureValue';
	 	$this->lang = true;
	 	$this->edit = true;
	 	$this->delete = true;

		parent::__construct();
	}

	/**
	 * Display form
	 *
	 * @global string $currentIndex Current URL in order to keep current Tab
	 */
	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;

		echo '
		<h2>'.$this->l('Add a new feature value').'</h2>
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.Tools::getValue('token').'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_feature_value" value="'.$obj->id.'" />' : '').'
			<fieldset class="width2">
				<legend><img src="../img/t/AdminFeatures.gif" />'.$this->l('Add a new feature value').'</legend>
				<label>'.$this->l('Value:').' </label>
				<div class="margin-form">';
		foreach ($this->_languages as $language)
			echo '
					<div id="value_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="value_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'value', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>';
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'value', 'value');
		echo '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Feature:').' </label>
				<div class="margin-form">
					<select name="id_feature">';
		$features = Feature::getFeatures($this->_defaultFormLanguage);
		foreach ($features AS $feature)
			echo '<option value="'.$feature['id_feature'].'"'.($this->getFieldValue($obj, 'id_feature') == $feature['id_feature']? ' selected="selected"' : '').'>'.$feature['name'].'</option>';
		echo '
					</select><sup> *</sup>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}

	/**
	 * Manage page processing
	 *
	 * @global string $currentIndex Current URL in order to keep current Tab
	 */
	public function postProcess($token = NULL)
	{
		global $currentIndex;

		if(Tools::getValue('submitDel'.$this->table))
		{
		 	if ($this->tabAccess['delete'] === '1')
		 	{
			 	if (isset($_POST[$this->table.$_POST['groupid'].'Box']))
			 	{
					$object = new $this->className();
					if ($object->deleteSelection($_POST[$this->table.$_POST['groupid'].'Box']))
						Tools::redirectAdmin($currentIndex.'&conf=2'.'&token='.($token ? $token : $this->token));
					$this->_errors[] = Tools::displayError('An error occurred while deleting selection.');
				}
				else
					$this->_errors[] = Tools::displayError('You must select at least one element to delete.');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else
			parent::postProcess();
	}
}