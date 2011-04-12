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

class AdminTabs extends AdminTab
{
	public function __construct()
	{
		global $cookie;
		
	 	$this->table = 'tab';
	 	$this->className = 'Tab';
	 	$this->lang = true;
	 	$this->edit = true;
	 	$this->delete = true;
		$this->_select = '(SELECT stl.`name` FROM `'._DB_PREFIX_.'tab_lang` stl WHERE stl.`id_tab` = a.`id_parent` AND stl.`id_lang` = '.(int)($cookie->id_lang).' LIMIT 1) AS parent';
		
		$this->fieldImageSettings = array('name' => 'icon', 'dir' => 't');
		$this->imageType = 'gif';
		
		$tabs = array(0 => $this->l('Home'));
		foreach (Tab::getTabs((int)($cookie->id_lang), 0) AS $tab)
			$tabs[$tab['id_tab']] = $tab['name'];
		$this->fieldsDisplay = array(
		'id_tab' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'name' => array('title' => $this->l('Name'), 'width' => 200),
		'logo' => array('title' => $this->l('Icon'), 'align' => 'center', 'image' => 't', 'image_id' => 'class_name', 'orderby' => false, 'search' => false),
		'parent' => array('title' => $this->l('Parent'), 'width' => 200, 'type' => 'select', 'select' => $tabs, 'filter_key' => 'a!id_parent'),
		'module' => array('title' => $this->l('Module')));
	
		parent::__construct();
	}

	public function postProcess()
	{
		if (($id_tab = (int)(Tools::getValue('id_tab'))) AND ($direction = Tools::getValue('move')) AND Validate::isLoadedObject($tab = new Tab($id_tab)))
		{
			global $currentIndex;
			if ($tab->move($direction))
				Tools::redirectAdmin($currentIndex.'&token='.$this->token);
		}
		else
		{
			if (!Tools::getValue('position'))
				$_POST['position'] = Tab::getNbTabs(Tools::getValue('id_parent'));
			parent::postProcess();
		}
	}

	private function _posTabs($name, $arrayTabs)
	{
		global $currentIndex;
		
		if (sizeof($arrayTabs) > 1)
		{
			echo '
			<table class="table" cellspacing="0" cellpadding="0" style="margin-bottom: 5px;">
				<tr>';
			for ($i = 0; $i < sizeof($arrayTabs); $i++)
			{
				$tab = $arrayTabs[$i];
				echo '<th style="text-align:center;">'.stripslashes($tab['name']).'<br />';
				if ($i)
					echo '<a href="'.$currentIndex.'&id_tab='.$tab['id_tab'].'&move=l&token='.$this->token.'"><img src="../img/admin/previous.gif" /></a>&nbsp;';
				if ($i < sizeof($arrayTabs) - 1)
					echo '<a href="'.$currentIndex.'&id_tab='.$tab['id_tab'].'&move=r&token='.$this->token.'"><img src="../img/admin/next.gif" /></a></th>';
			}
			echo '
				</tr>
			</table>';
		}
	}
	
	public function displayList()
	{
		global $cookie, $currentIndex;
		
		parent::displayList();
		
		$tabs = Tab::getTabs((int)($cookie->id_lang), 0);
		echo '<br /><h2>'.$this->l('Positions').'</h2>
		<h3>'.$this->l('Level').' 1</h3>';
		$this->_posTabs($this->l('Main'), $tabs);
		echo '<h3>'.$this->l('Level').' 2</h3>';
		foreach ($tabs AS $t)
			$this->_posTabs(stripslashes($t['name']), Tab::getTabs((int)($cookie->id_lang), $t['id_tab']));
	}
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;

		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post" enctype="multipart/form-data">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
		'.($obj->position ? '<input type="hidden" name="position" value="'.$obj->position.'" />' : '').'
			<fieldset><legend><img src="../img/admin/tab.gif" />'.$this->l('Tabs').'</legend>
				<label>'.$this->l('Name:').' </label>
				<div class="margin-form">';
				foreach ($this->_languages as $language)
					echo '
					<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="name_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'name', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>';
				$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'name', 'name');
		echo '
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Class:').' </label>
				<div class="margin-form">
					<input type="text" name="class_name" value="'.htmlentities($this->getFieldValue($obj, 'class_name'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Module:').' </label>
				<div class="margin-form">
					<input type="text" name="module" value="'.htmlentities($this->getFieldValue($obj, 'module'), ENT_COMPAT, 'UTF-8').'" />
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Icon').'</label>
				<div class="margin-form">
					'.($obj->id ? '<img src="../img/t/'.$obj->class_name.'.gif" />&nbsp;/img/t/'.$obj->class_name.'.gif' : '').'
					<p><input type="file" name="icon" /></p>
					<p>'.$this->l('Upload logo from your computer').' (.gif, .jpg, .jpeg '.$this->l('or').' .png)</p>
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Parent').'</label>
				<div class="margin-form">
					<select name="id_parent">
						<option value="-1" '.(($this->getFieldValue($obj, 'id_parent') == -1) ? 'selected="selected"' : '').'>'.$this->l('None').'</option>
						<option value="0" '.(($this->getFieldValue($obj, 'id_parent') == 0) ? 'selected="selected"' : '').'>'.$this->l('Home').'</option>';
		foreach (Tab::getTabs((int)($cookie->id_lang), 0) AS $tab)
			echo '		<option value="'.$tab['id_tab'].'" '.($tab['id_tab'] == $this->getFieldValue($obj, 'id_parent') ? 'selected="selected"' : '').'>'.$tab['name'].'</option>';
		echo '		</select>
				</div>
				<div class="clear">&nbsp;</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}

	public function afterImageUpload()
	{
		if (!($obj = $this->loadObject(true)))
			return;
		@rename(_PS_IMG_DIR_.'t/'.$obj->id.'.gif', _PS_IMG_DIR_.'t/'.$obj->class_name.'.gif');
	}
}
