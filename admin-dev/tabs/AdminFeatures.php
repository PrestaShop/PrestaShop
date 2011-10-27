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
*  @version  Release: $Revision: 7331 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(_PS_ADMIN_DIR_.'/tabs/AdminFeaturesValues.php');

class AdminFeatures extends AdminTab
{
	public function __construct()
	{
		$this->adminFeaturesValues = new AdminFeaturesValues();
	 	$this->table = 'feature';
	 	$this->className = 'Feature';
	 	$this->lang = true;
	 	$this->edit = true;
	 	$this->delete = true;

		$this->fieldsDisplay = array(
			'name' => array('title' => $this->l('Name'), 'width' => 128, 'filter_key' => 'b!name'),
			'value' => array('title' => $this->l('Values'), 'width' => 255, 'orderby' => false, 'search' => false),
			'position' => array('title' => $this->l('Position'), 'width' => 40,'filter_key' => 'cp!position', 'align' => 'center', 'position' => 'position'));

		parent::__construct();
	}

	public function display()
	{
		if (Feature::isFeatureActive())
		{
			if ((isset($_POST['submitAddfeature_value']) AND sizeof($this->adminFeaturesValues->_errors))
				OR isset($_GET['updatefeature_value']) OR isset($_GET['addfeature_value']))
			{
				$this->adminFeaturesValues->displayForm($this->token);
				echo '<br /><br /><a href="'.self::$currentIndex.'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" alt="" /> '.$this->l('Back to the features list').'</a><br />';
			}
			else
				parent::display();
		}
		else
			$this->displayWarning($this->l('This feature has been disabled, you can active this feature at this page:').' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
	}

	/* Report to AdminTab::displayList() for more details */
	public function displayList()
	{
		echo '<br />
			<a href="'.self::$currentIndex.'&add'.$this->table.'&token='.$this->token.'"><img src="../img/admin/add.gif" border="0" /> <b>'.$this->l('Add a new feature').'</b></a><br />
			<a href="'.self::$currentIndex.'&addfeature_value&token='.$this->token.'"><img src="../img/admin/add.gif" border="0" /> '.$this->l('Add a new feature value').'</a><br /><br />
		'.$this->l('Click on a feature name to view its values and then click again if you want to hide them.').'<br /><br />';

		$this->displayListHeader();
		echo '<input type="hidden" name="groupid" value="0">';

		if (!sizeof($this->_list))
			echo '<tr><td class="center" colspan="'.sizeof($this->_list).'">'.$this->l('No features found.').'</td></tr>';


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
			$id = (int)($tr['id_'.$this->table]);
		 	echo '
			<tr'.($irow++ % 2 ? ' class="alt_row"' : '').' id="tr_'.$tr['id_feature'].'_'.$tr['position'].'">
				<td style="vertical-align: top; padding: 4px 0 4px 0" class="center"><input type="checkbox" name="'.$this->table.'Box[]" value="'.$id.'" class="noborder" /></td>
				<td style="width: 140px; vertical-align: top; padding: 4px 0 4px 0; cursor: pointer" onclick="$(\'#features_values_'.$id.'\').slideToggle();">'.$tr['name'].'</td>
				<td style="vertical-align: top; padding: 4px 0 4px 0; width: 340px">
					<div id="features_values_'.$id.'" style="display: none">
					<table class="table" cellpadding="0" cellspacing="0">
						<tr>
							<th><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'feature_value'.$id.'Box[]\', this.checked)" /></th>
							<th width="100%">'.$this->l('Value').'</th>
							<th>'.$this->l('Actions').'</th>
						</tr>';
			$features = FeatureValue::getFeatureValuesWithLang((int)(Configuration::get('PS_LANG_DEFAULT')), $id);
			foreach ($features AS $feature)
			{
				echo '
				<tr>
					<td class="center"><input type="checkbox" name="feature_value'.$id.'Box[]" value="'.$feature['id_feature_value'].'" class="noborder" /></td>
					<td>'.$feature['value'].'</td>
					<td class="center">
						<a href="'.self::$currentIndex.'&id_feature_value='.$feature['id_feature_value'].'&updatefeature_value&token='.$this->token.'">
						<img src="../img/admin/edit.gif" border="0" alt="'.$this->l('Edit').'" title="'.$this->l('Edit').'" /></a>&nbsp;
						<a href="'.self::$currentIndex.'&id_feature_value='.$feature['id_feature_value'].'&deletefeature_value&token='.$this->token.'"
						onclick="return confirm(\''.$this->l('Delete value', __CLASS__, true, false).' #'.$feature['id_feature_value'].'?\');">
						<img src="../img/admin/delete.gif" border="0" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a>
					</td>
				</tr>';
			}
			if (!sizeof($features))
				echo '
						<tr><td colspan="3" style="text-align:center">'.$this->l('No values defined').'</td></tr>';
			echo '
					</table>
					<p><input type="Submit" class="button" name="submitDelfeature_value" value="'.$this->l('Delete selection').'"
					onclick="changeFormParam(this.form, \'?tab=AdminFeatures\', '.$id.'); return confirm(\''.$this->l('Delete selected items?', __CLASS__, true, false).'\');" /></p>
					</div>
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
				<td style="vertical-align: top; padding: 4px 0 4px 0" class="center">
					<a href="'.self::$currentIndex.'&id_'.$this->table.'='.$id.'&update'.$this->table.'&token='.$this->token.'">
					<img src="../img/admin/edit.gif" border="0" alt="'.$this->l('Edit').'" title="'.$this->l('Edit').'" /></a>&nbsp;
					<a href="'.self::$currentIndex.'&id_'.$this->table.'='.$id.'&delete'.$this->table.'&token='.$this->token.'" onclick="return confirm(\''.$this->l('Delete item', __CLASS__, true, false).' #'.$id.'?\');">
					<img src="../img/admin/delete.gif" border="0" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a>
				</td>
			</tr>';
		}

		$this->displayListFooter();

	}

	public function displayForm($isMainTab = true)
	{
		if (!Feature::isFeatureActive())
		{
			$this->displayWarning($this->l('This feature has been disabled, you can active this feature at this page:').'<a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
			return;
		}

		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;

		echo '
		<h2>'.$this->l('Add a new feature').'</h2>
		<form action="'.self::$currentIndex.'&token='.$this->token.'"" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset class="width2">
				<legend><img src="../img/t/AdminFeatures.gif" />'.$this->l('Add a new feature').'</legend>
				<label>'.$this->l('Name:').'</label>
				<div class="margin-form">';
		foreach ($this->_languages AS $language)
			echo '
					<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="name_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'name', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>
				<script type="text/javascript">
					var flag_fields = \'name\';
				</script>';
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'flag_fields', 'name', false, true);
		echo '
					<div class="clear"></div>
				</div>';
				if (Shop::isFeatureActive())
				{
					echo '<label>'.$this->l('GroupShop association:').'</label><div class="margin-form">';
					$this->displayAssoShop('group_shop');
					echo '</div>';
				}
				echo '
				'.Module::hookExec('featureForm', array('id_feature' => $obj->id)).'
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}

	public function displayErrors()
	{
		$this->adminFeaturesValues->displayErrors();
		parent::displayErrors();
	}

	public function postProcess()
	{
		if (!Feature::isFeatureActive())
			return ;

		$this->adminFeaturesValues->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, $this->id);

		if (Tools::isSubmit('submitAddfeature_value') || Tools::isSubmit('submitDelfeature_value'))
			$this->adminFeaturesValues->postProcess($this->token);

		Module::hookExec('postProcessFeature',
		array('errors' => &$this->_errors)); // send _errors as reference to allow postProcessFeature to stop saving process

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
		}
		else if (Tools::isSubmit('submitAdd'.$this->table))
		{
			if ($this->tabAccess['add'] === '1')
			{
				$id_feature = (int)Tools::getValue('id_feature');
				// Adding last position to the feature if not exist
				if ($id_feature <= 0)
				{
					$sql = 'SELECT `position`+1
							FROM `'._DB_PREFIX_.'feature`
							ORDER BY position DESC';
				// set the position of the new feature in $_POST for postProcess() method
					$_POST['position'] = DB::getInstance()->getValue($sql);
				}
				// clean \n\r characters
				foreach ($_POST as $key => $value)
					if (preg_match('/^name_/Ui', $key))
						$_POST[$key] = str_replace ('\n', '', str_replace('\r', '', $value));
				parent::postProcess();
			}

		}
		else
			parent::postProcess();
	}

	/**
	 * Modifying initial getList method to display position feature (drag and drop)
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		if ($order_by && $this->context->cookie->__get($this->table.'Orderby'))
			$order_by = $this->context->cookie->__get($this->table.'Orderby');
		else
			$order_by = 'position';

		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
	}
}
