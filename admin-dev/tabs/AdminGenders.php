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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminGenders extends AdminTab
{
	public function __construct()
	{
		$this->table = 'gender';
		$this->className = 'Gender';
		$this->lang = true;
		$this->edit = true;
		$this->delete = true;

		$this->fieldImageSettings = array('name' => 'image', 'dir' => 'genders');
		$this->fieldsDisplay = array(
			'id_gender' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'name' => array('title' => $this->l('Name'), 'width' => 150, 'filter_key' => 'b!name'),
			'type' => array(
				'title' => $this->l('Type'),
				'width' => 100,
				'orderby' => false,
				'type' => 'select',
				'select' => array(0 => $this->l('Male'), 1 => $this->l('Female'), 2 => $this->l('Neutral')),
				'filter_key' => 'a!type',
				'callback' => 'displayGenderType',
				'callback_object' => $this,
			),
			'image' => array('title' => $this->l('Image'), 'align' => 'center', 'image' => 'genders', 'orderby' => false, 'search' => false),
		);

		parent::__construct();
	}

	public function displayGenderType($value, $tr)
	{
		return $this->fieldsDisplay['type']['select'][$value];
	}

	public function displayForm($isMainTab = true)
	{
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;

		echo '
		<form action="'.self::$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post" enctype="multipart/form-data">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/tab-genders.gif" />'.$this->l('Gender').'</legend>
				<label>'.$this->l('Name:').' </label>
				<div class="margin-form">';
				foreach ($this->_languages as $language)
					echo '
					<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="name_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'name', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:<span class="hint-pointer">&nbsp;</span></span>
					</div>';
				$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'name', 'name');
			echo '</div><div class="clear">&nbsp;</div>';

			echo '<label>'.$this->l('Type:').' </label>
				<div class="margin-form">
					<input type="radio" name="type" id="type_male" value="0" '.($this->getFieldValue($obj, 'type') == 0 ? 'checked="checked" ' : '').'/>
					<label class="t" for="type_male"> '.$this->l('Male').'</label>
					<input type="radio" name="type" id="type_female" value="1" '.($this->getFieldValue($obj, 'type') == 1 ? 'checked="checked" ' : '').'/>
					<label class="t" for="type_female"> '.$this->l('Female').'</label>
					<input type="radio" name="type" id="type_neutral" value="2" '.($this->getFieldValue($obj, 'type') == 2 ? 'checked="checked" ' : '').'/>
					<label class="t" for="type_neutral"> '.$this->l('Neutral').'</label>
				</div>
				<div class="clear"></div>';

			echo '<label>'.$this->l('Image:').' </label>
					<div class="margin-form">';
			echo '	<input type="file" name="image" /> ';
			if ($obj->getImage())
				echo '<img src="'.$obj->getImage().'" />';
			echo '</div>
				<div class="clear"></div>';

			echo '<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form><br />';
	}
}
