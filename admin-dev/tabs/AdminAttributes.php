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

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');

class AdminAttributes extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'attribute';
	 	$this->className = 'Attribute';
	 	$this->lang = true;
	 	$this->edit = true;
	 	$this->delete = true;

		$this->fieldImageSettings = array('name' => 'texture', 'dir' => 'co');

		parent::__construct();
	}

	/**
	 * Display form
	 *
	 * @global string $currentIndex Current URL in order to keep current Tab
	 */
	public function displayForm($token = NULL)
	{
		global $currentIndex;
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;
		$color = ($obj->color ? $obj->color : 0);
		$attributes_groups = AttributeGroup::getAttributesGroups($this->_defaultFormLanguage);
		$strAttributesGroups = '';
		echo '
		<script type="text/javascript">
			var attributesGroups = {';
		foreach ($attributes_groups AS $attribute_group)
			$strAttributesGroups .= '"'.$attribute_group['id_attribute_group'].'" : '.$attribute_group['is_color_group'].',';
		echo $strAttributesGroups.'};
		</script>
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.($token ? $token : $this->token).'" method="post" enctype="multipart/form-data">
		'.($obj->id ? '<input type="hidden" name="id_attribute" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/asterisk.gif" />'.$this->l('Attribute').'</legend>
				<label>'.$this->l('Name:').' </label>
				<div class="margin-form">';
		foreach ($this->_languages as $language)
			echo '
					<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="name_'.$language['id_lang'].'" value="'.htmlspecialchars($this->getFieldValue($obj, 'name', (int)($language['id_lang']))).'" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>';
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'name', 'name');
		echo '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Group:').' </label>
				<div class="margin-form">
					<select name="id_attribute_group" id="id_attribute_group" onchange="showAttributeColorGroup(\'id_attribute_group\', \'colorAttributeProperties\')">';
		
		foreach ($attributes_groups AS $attribute_group)
			echo '<option value="'.$attribute_group['id_attribute_group'].'"'.($this->getFieldValue($obj, 'id_attribute_group') == $attribute_group['id_attribute_group'] ? ' selected="selected"' : '').'>'.$attribute_group['name'].'</option>';
		echo '
					</select><sup> *</sup>
				</div>
				<script type="text/javascript" src="../js/jquery/jquery-colorpicker.js"></script>
				<div id="colorAttributeProperties" style="'.((Validate::isLoadedObject($obj) AND $obj->isColorAttribute()) ? 'display: block;' : 'display: none;').'">
					<label>'.$this->l('Color').'</label>
					<div class="margin-form">
						<input type="color" data-hex="true" class="color mColorPickerInput" name="color" value="'.(Tools::getValue('color', $color) ? htmlentities(Tools::getValue('color', $color)) : '#000000').'" /> <sup>*</sup>
						<p class="clear">'.$this->l('HTML colors only (e.g.,').' "lightblue", "#CC6600")</p>
					</div>
					<label>'.$this->l('Texture:').' </label>
					<div class="margin-form">
						<input type="file" name="texture" />
						<p>'.$this->l('Upload color texture from your computer').'<br />'.$this->l('This will override the HTML color!').'</p>
					</div>
					<label>'.$this->l('Current texture:').' </label>
					<div class="margin-form">
						<p>'.(file_exists(_PS_IMG_DIR_.$this->fieldImageSettings['dir'].'/'.$obj->id.'.jpg')
							? '<img src="../img/'.$this->fieldImageSettings['dir'].'/'.$obj->id.'.jpg" alt="" title="" /> <a href="'.$_SERVER['REQUEST_URI'].'&deleteImage=1"><img src="../img/admin/delete.gif" alt="'.$this->l('delete').'" title="" /></a>'
							: $this->l('None')
						).'</p>
					</div>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAddattribute" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>
		<script>
			showAttributeColorGroup(\'id_attribute_group\', \'colorAttributeProperties\');
		</script>';
	}

	/**
	 * Manage page processing
	 *
	 * @global string $currentIndex Current URL in order to keep current Tab
	 */
	public function postProcess($token = NULL)
	{
		global $currentIndex;
		if (Tools::getValue('submitDel'.$this->table))
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


