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
*  @version  Release: $Revision: 7465 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(_PS_ADMIN_DIR_.'/../classes/AdminTab.php');
include_once(_PS_ADMIN_DIR_.'/tabs/AdminAttributes.php');

class AdminAttributesGroups extends AdminTab
{
	/** @var object AdminAttributes() instance */
	private $adminAttributes;

	public function __construct()
	{
		$this->adminAttributes = new AdminAttributes();
	 	$this->table = 'attribute_group';
	 	$this->className = 'AttributeGroup';
	 	$this->lang = true;
	 	$this->edit = true;
	 	$this->delete = true;

		$this->fieldsDisplay = array(
			'name' => array('title' => $this->l('Name'), 'width' => 140, 'filter_key' => 'b!name'),
			'attribute' => array('title' => $this->l('Attributes'), 'width' => 240, 'orderby' => false, 'search' => false),
			'position' => array('title' => $this->l('Position'), 'width' => 40,'filter_key' => 'cp!position', 'align' => 'center', 'position' => 'position'));
		parent::__construct();
	}

	public function display()
	{
		if (Combination::isFeatureActive())
		{
			if ((isset($_POST['submitAddattribute']) AND sizeof($this->adminAttributes->_errors))
				OR isset($_GET['updateattribute']) OR isset($_GET['addattribute']))
			{
				$this->adminAttributes->displayForm($this->token);
				echo '<br /><br /><a href="'.self::$currentIndex.'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" /> '.$this->l('Back to list').'</a><br />';
			}
			else
				parent::display();
		}
		else
			$this->displayWarning($this->l('This feature has been disabled, you can active this feature at this page:').' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
	}
	
	public function postProcess()
	{	
		if (!Combination::isFeatureActive())
			return;
		
		$this->adminAttributes->tabAccess = Profile::getProfileAccess(Context::getContext()->employee->id_profile, $this->id);
		if (Tools::isSubmit('submitAddattribute') || Tools::isSubmit('submitDelattribute'))
			$this->adminAttributes->postProcess($this->token);

		if(Tools::getValue('submitDel'.$this->table))
		{
		 	if ($this->tabAccess['delete'] === '1')
			{
			 	if (isset($_POST[$this->table.'Box']))
			 	{
					$object = new $this->className();
					if ($object->deleteSelection($_POST[$this->table.'Box']))
						Tools::redirectAdmin(self::$currentIndex.'&conf=2'.'&token='.$this->token);
					$this->_errors[] = Tools::displayError('An error occurred while deleting selection.');
				}
				else
					$this->_errors[] = Tools::displayError('You must select at least one element to delete.');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
			// clean position after delete
			AttributeGroup::cleanPositions();
		}
		else if (Tools::isSubmit('submitAdd'.$this->table))
		{
			$id_attribute_group = (int)Tools::getValue('id_attribute_group');
			// Adding last position to the attribute if not exist
			if ($id_attribute_group <= 0)
			{
				$sql = 'SELECT `position`+1
						FROM `'._DB_PREFIX_.'attribute_group` 
						ORDER BY position DESC';
			// set the position of the new group attribute in $_POST for postProcess() method
				$_POST['position'] = DB::getInstance()->getValue($sql);
			}
			// clean \n\r characters
			foreach ($_POST as $key => $value)
				if (preg_match('/^name_/Ui', $key))
					$_POST[$key] = str_replace ('\n', '', str_replace('\r', '', $value));
			parent::postProcess();
		}
		else
			parent::postProcess();
	}

	public function displayErrors()
	{
		$this->adminAttributes->displayErrors();
		parent::displayErrors();
	}

	/* Report to AdminTab::displayList() for more details */
	public function displayList()
	{
		echo '<br /><a href="'.self::$currentIndex.'&add'.$this->table.'&token='.$this->token.'"><img src="../img/admin/add.gif" border="0" /> <b>'.$this->l('Add attributes group').'</b></a><br />
		<a href="'.self::$currentIndex.'&addattribute&token='.$this->token.'"><img src="../img/admin/add.gif" border="0" /> '.$this->l('Add attribute').'</a><br /><br />
		'.$this->l('Click on the group name to view its attributes. Click again to hide them.').'<br /><br />';
		if ($this->_list === false)
			Tools::displayError('No elements found');

		$this->displayListHeader($this->token);
		echo '<input type="hidden" name="groupid" value="0">';

		if (!sizeof($this->_list))
			echo '<tr><td class="center" colspan="'.sizeof($this->_list).'">'.$this->l('No elements found').'</td></tr>';
			
		//$this->displayListContent($this->token);


		echo '
		<script type="text/javascript" src="../js/jquery/jquery.tablednd_0_5.js"></script>
		<script type="text/javascript">
			var token = \''.$this->token.'\';
			var come_from = \''.$this->table.'\';
			var alternate = \''.($this->_orderWay == 'DESC' ? '1' : '0' ).'\';
		</script>
		<script type="text/javascript" src="../js/admin-dnd.js"></script>
		';

		$irow = 0;
		if ($this->_list AND isset($this->fieldsDisplay['position']))
		{
			$positions = array_map(create_function('$elem', 'return (int)$elem[\'position\'];'), $this->_list);
			sort($positions);
		}
		foreach ($this->_list AS $tr)
		{
			$id = (int)$tr['id_'.$this->table];
		 	echo '
			<tr'.($irow++ % 2 ? ' class="alt_row"' : '').' id="tr_'.$id.'_'.$tr['position'].'">
				<td style="vertical-align: top; padding: 4px 0 4px 0" class="center"><input type="checkbox" name="'.$this->table.'Box[]" value="'.$id.'" class="noborder" /></td>
				<td style="width: 140px; vertical-align: top; padding: 4px 0 4px 0; cursor: pointer" onclick="$(\'#attributes_'.$id.'\').slideToggle();">'.$tr['name'].'</td>
				<td style="vertical-align: top; padding: 4px 0 4px 0; width: 340px">';
				$this->displayListAttributes($id, $irow, $tr);
			echo '
				</td>';

			echo '
				<td style="width: 140px; vertical-align: top; padding: 4px 0 4px 0; cursor: pointer" class="dragHandle">';
				
			if ($this->_orderBy == 'position' AND $this->_orderWay != 'DESC')
			{
				echo '<a'.(!($tr['position'] != $positions[sizeof($positions) - 1]) ? ' style="display: none;"' : '').' href="'.self::$currentIndex.
						'&'.$this->identifiersDnd[$this->identifier].'='.$id.'
						&way=1&position='.((int)$tr['position'] + 1).'&token='.$this->token.'">
						<img src="../img/admin/'.($this->_orderWay == 'ASC' ? 'down' : 'up').'.gif"
						alt="'.$this->l('Down').'" title="'.$this->l('Down').'" /></a>';

				echo '<a'.(!($tr['position'] != $positions[0]) ? ' style="display: none;"' : '').' href="'.self::$currentIndex.
						'&'.$this->identifiersDnd[$this->identifier].'='.$id.'
						&way=0&position='.((int)$tr['position'] - 1).'&token='.$this->token.'">
						<img src="../img/admin/'.($this->_orderWay == 'ASC' ? 'up' : 'down').'.gif"
						alt="'.$this->l('Up').'" title="'.$this->l('Up').'" /></a>';
			}
			else
				echo (int)($tr['position'] + 1);

			echo '
				</td>
				<td style="vertical-align: top; padding: 4px 0 4px 0" class="center">
					<a href="'.self::$currentIndex.'&id_'.$this->table.'='.$id.'&update'.$this->table.'&token='.$this->token.'">
					<img src="../img/admin/edit.gif" border="0" alt="'.$this->l('Edit').'" title="'.$this->l('Edit').'" /></a>&nbsp;
					<a href="'.self::$currentIndex.'&id_'.$this->table.'='.$id.'&delete'.$this->table.'&token='.$this->token.'" onclick="return confirm(\''.$this->l('Delete item', __CLASS__, true, false).' : '.$tr['name'].'?\');">
					<img src="../img/admin/delete.gif" border="0" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a>
				</td>
			</tr>';
		}

		$this->displayListFooter($this->token);
	}

	public function displayForm($isMainTab = true)
	{
		if (!Combination::isFeatureActive())
		{
			$this->displayWarning($this->l('This feature has been disabled, you can active this feature at this page:').' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
			return;
		}
		
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;

		echo '
		<form action="'.self::$currentIndex.'&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/asterisk.gif" />'.$this->l('Attributes group').'</legend>
				<label>'.$this->l('Name:').' </label>
				<div class="margin-form">';
		foreach ($this->_languages as $language)
			echo '
					<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="name_'.$language['id_lang'].'" value="'.htmlspecialchars($this->getFieldValue($obj, 'name', (int)($language['id_lang']))).'" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>';
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'name¤public_name', 'name');
		echo '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Public name:').' </label>
				<div class="margin-form">';
		foreach ($this->_languages as $language)
			echo '
					<div id="public_name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="public_name_'.$language['id_lang'].'" value="'.htmlspecialchars($this->getFieldValue($obj, 'public_name', (int)($language['id_lang']))).'" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
						<p style="clear: both">'.$this->l('Term or phrase displayed to the customer').'</p>
					</div>';
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'name¤public_name', 'public_name');
		echo '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Color group:').' </label>
				<div class="margin-form">
					<input type="radio" name="is_color_group" id="is_color_group_on" value="1" '.($this->getFieldValue($obj, 'is_color_group') ? 'checked="checked" ' : '').'/>
					<label class="t" for="is_color_group_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Yes').'" /></label>
					<input type="radio" name="is_color_group" id="is_color_group_off" value="0" '.(!$this->getFieldValue($obj, 'is_color_group') ? 'checked="checked" ' : '').'/>
					<label class="t" for="is_color_group_off"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('No').'" /></label>
					<p>'.$this->l('This is a color group').'</p>
				</div>';
				if (Shop::isMultiShopActivated())
				{
					echo '<label>'.$this->l('GroupShop association:').'</label><div class="margin-form">';
					$this->displayAssoShop('group_shop');
					echo '</div>';
				}
				echo '
				'.Module::hookExec('attributeGroupForm', array('id_attribute_group' => $obj->id)).'
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
	
	/**
	 * displayListAttributes
	 * 
	 * Display a list of attributes from a group
	 */
	public function displayListAttributes($id, $irow, $tr)
	{
		echo '
				<div id="attributes_'.$id.'" style="display: none">
					<table class="attribute table tableDnD" cellpadding="0" cellspacing="0" id="attribute_'.$id.'">
						<thead>
						<tr class="nodrag nodrop">
							<th><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'attribute'.$id.'Box[]\', this.checked)" /></th>
							<th width="100%">'.$this->l('Attribute').'</th>
							<th>'.$this->l('Position').'</th>
							<th>'.$this->l('Actions').'</th>
						</tr>
						</thead>
						<tbody>';
			$attributes = AttributeGroup::getAttributes(Context::getContext()->language->id, $id);
			$nbrow = 0;
			foreach ($attributes AS $attribute)
			{
				$class = ($irow % 2) ? '': 'not_';
				echo '
						<tr'.($nbrow++ % 2 ? ' class="'.$class.'alt_row"' : '').' id="tr_'.$attribute['id_attribute_group'].'_'.$attribute['id_attribute'].'_'.$attribute['position'].'">
							<td class="center"><input type="checkbox" name="attribute'.$id.'Box[]" value="'.$attribute['id_attribute'].'" class="noborder" /></td>
							<td>
								'.($tr['is_color_group'] ? '<div style="float: left; width: 18px; height: 12px; border: 1px solid #996633; background-color: '.$attribute['color'].'; margin-right: 4px;"></div>' : '')
								.$attribute['name'].'
							</td>
							<td class="dragHandle">';

				echo '<a'.(!($attribute['position'] != $attributes[sizeof($attributes) - 1]['position']) ? ' style="display: none;"' : '').' href="'.self::$currentIndex.
						'&id_attribute_group='.(int)($attribute['id_attribute_group']).'&id_attribute='.$attribute['id_attribute'].'
						&way=1&position='.(int)($attribute['position'] + 1).'&token='.$this->token.'">
						<img src="../img/admin/down.gif"
						alt="'.$this->l('Down').'" title="'.$this->l('Down').'" /></a>';

				echo '<a'.(!($attribute['position'] != $attributes[0]['position']) ? ' style="display: none;"' : '').' href="'.self::$currentIndex.
						'&id_attribute_group='.(int)($attribute['id_attribute_group']).'&&id_attribute='.$attribute['id_attribute'].'
						&way=0&position='.(int)($attribute['position'] - 1).'&token='.$this->token.'">
						<img src="../img/admin/up.gif"
						alt="'.$this->l('Up').'" title="'.$this->l('Up').'" /></a>';
				echo'
							</td>
							<td class="center">
								<a href="'.self::$currentIndex.'&id_attribute='.$attribute['id_attribute'].'&updateattribute&token='.$this->token.'">
								<img src="../img/admin/edit.gif" border="0" alt="'.$this->l('Edit').'" title="'.$this->l('Edit').'" /></a>&nbsp;
								<a href="'.self::$currentIndex.'&id_attribute='.$attribute['id_attribute'].'&deleteattribute&token='.$this->token.'"
								onclick="return confirm(\''.$this->l('Delete attribute', __CLASS__, true, false).' : '.$attribute['name'].'?\');">
								<img src="../img/admin/delete.gif" border="0" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a>
							</td>
						</tr>';
			}
			echo '
							</tbody>
							</table>
					<p><input type="Submit" class="button" name="submitDelattribute" value="'.$this->l('Delete selection').'"
					onclick="changeFormParam(this.form, \''.self::$currentIndex.'\', '.$id.'); return confirm(\''.$this->l('Delete selected items?', __CLASS__, true, false).'\');" /></p>
					</div>';
	}
}


