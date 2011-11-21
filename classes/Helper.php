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

/*
 * TODO : move HTML code in template files
 * TODO : phpDoc on two last methods: includeDatepicker() & bindDatepicker()
 */

class HelperCore
{
	public $currentIndex;
	public $table;
	public $identifier;
	public $token;
	public $toolbar_btn;
	public $ps_help_context;
	public $title;
	public $show_toolbar = true;
	public $context;
	public $toolbar_fix = false;

	/** @var string Helper tpl folder */
	public $base_folder;

	/** @var string Controller tpl folder */
	public $override_folder;

	/**
	 * @var smartyTemplate base template object
	 */
	protected $tpl;

	/**
	 * @var string base template name
	 */
	protected $base_tpl = 'content.tpl';

	public $tpl_vars = array();

	public function __construct()
	{
		$this->context = Context::getContext();
	}

	public function setTpl($tpl)
	{
		$this->tpl = $this->createTemplate($tpl);
	}

	/**
	 * Create a template from the override file, else from the base file.
	 *
	 * @param string $tpl_name filename
	 * @return Template
	 */
	public function createTemplate($tpl_name)
	{
		// Overrides exists?
		if ($this->override_folder && file_exists($this->context->smarty->template_dir[0].$this->override_folder.$tpl_name))
			return $this->context->smarty->createTemplate($this->override_folder.$tpl_name);

		return $this->context->smarty->createTemplate($this->base_folder.$tpl_name);
	}

	/**
	 * default behaviour for helper is to return a tpl fetched
	 *
	 * @return void
	 */
	public function generate()
	{
		$this->tpl->assign($this->tpl_vars);
		return $this->tpl->fetch();
	}

	/**
	 *
	 * @param type $trads values of translations keys
	 *					For the moment, translation are not automatic
	 * @param type $selected_cat array of selected categories
	 *					Format
	 *						Array
	 * 					(
	 *							 [0] => 1
	 *						 [1] => 2
	 *					)
	 * 					OR
	 *					Array
	 *					(
	 *						 [1] => Array
	 *							  (
	 *									[id_category] => 1
	 *									[name] => Home page
	 *									[link_rewrite] => home
	 *							  )
	 *					)
	 * @param type $input_name name of input
	 * @return string
	 */
	public static function renderAdminCategorieTree($trads, $selected_cat = array(), $input_name = 'categoryBox', $use_radio = false, $use_search = false, $disabled_categories = array())
	{
		if (!$use_radio)
			$input_name = $input_name.'[]';
		
		$context = Context::getContext();
		
		$context->controller->addCSS(_PS_JS_DIR_.'jquery/plugins/treeview/jquery.treeview.css');
		
		
		$context->controller->addJs(array(
			_PS_JS_DIR_.'jquery/plugins/treeview/jquery.treeview.js',
			_PS_JS_DIR_.'jquery/plugins/treeview/jquery.treeview.async.js',
			_PS_JS_DIR_.'jquery/plugins/treeview/jquery.treeview.edit.js',
			_PS_JS_DIR_.'admin-categories-tree.js'));
		if ($use_search)
			$context->controller->addJs(_PS_JS_DIR_.'jquery/plugins/autocomplete/jquery.autocomplete.js');
			
			
		$html = '
		<script type="text/javascript">
			var inputName = "'.$input_name.'";
			var use_radio = '.($use_radio ? '1' : '0').';
		';
		if (sizeof($selected_cat) > 0)
		{
			if (isset($selected_cat[0]))
				$html .= 'var selectedCat = "'.implode(',', $selected_cat).'";';
			else
				$html .= 'var selectedCat = "'.implode(',', array_keys($selected_cat)).'";';
		}
		else
			$html .= 'var selectedCat = "";';
		$html .= '
			var selectedLabel = \''.$trads['selected'].'\';
			var home = \''.$trads['Home'].'\';
			var use_radio = '.(int)$use_radio.';
		</script>';

		$html .= '
		<div style="background-color:#F4E6C9; width:99%;padding:5px 0 5px 5px;">
			<a href="#" id="collapse_all" >'.$trads['Collapse All'].'</a>
			 - <a href="#" id="expand_all" >'.$trads['Expand All'].'</a>
			'.(!$use_radio ? '
			 - <a href="#" id="check_all" >'.$trads['Check All'].'</a>
			 - <a href="#" id="uncheck_all" >'.$trads['Uncheck All'].'</a>
			 ' : '').($use_search ? '<span style="margin-left:20px">'.$trads['search'].' : <form method="post" id="filternameForm"><input type="text" name="search_cat" id="search_cat"></form></span>' : '').'
		</div>
		';

		$home_is_selected = false;
		foreach($selected_cat AS $cat)
		{
			if (is_array($cat))
			{
				$disabled = in_array($cat['id_category'], $disabled_categories);
				if  ($cat['id_category'] != 1)
					$html .= '<input '.($disabled?'disabled="disabled"':'').' type="hidden" name="'.$input_name.'" value="'.$cat['id_category'].'" >';
				else
					$home_is_selected = true;
			}
			else
			{
				$disabled = in_array($cat, $disabled_categories);
				if  ($cat != 1)
					$html .= '<input '.($disabled?'disabled="disabled"':'').' type="hidden" name="'.$input_name.'" value="'.$cat.'" >';
				else
					$home_is_selected = true;
			}
		}
		$html .= '
			<ul id="categories-treeview" class="filetree">
				<li id="1" class="hasChildren">
					<span class="folder"> <input type="'.(!$use_radio ? 'checkbox' : 'radio').'" name="'.$input_name.'" value="1" '.($home_is_selected ? 'checked' : '').' onclick="clickOnCategoryBox($(this));" /> '.$trads['Home'].'</span>
					<ul>
						<li><span class="placeholder">&nbsp;</span></li>
				  </ul>
				</li>
			</ul>';
		return $html;
	}

	/**
	* Create a select input field
	*
	* @param array $values
	* @param array $html_options any key => value options
	* @param array $select_options
	* - key: the array value that will be used as a key in my select (optional)
	* - value: the array value that will be used as a label in my select (optional)
	* - empty: the label displayed as an empty value (optional)
	* - selected: the key corresponding to the selected value  (optional)
	*
	* @return string html content
	*/
	public static function selectInput(array $values, array $html_options = array(), array $select_options = array())
	{
		// options management
		$options = self::buildHtmlOptions($html_options);
		$select_html = '<select '.$options.'>';

		if (isset($select_options['key']))
			$use_key = $select_options['key'];

		if (isset($select_options['value']))
			$use_value = $select_options['value'];

		if (isset($select_options['empty']))
			$select_html .= '<option value="">'.$select_options['empty'].'</option>';

		if (isset($select_options['selected']) && !is_array($select_options['selected']))
			$select_options['selected'] = array($select_options['selected']);
		// render options fields
		foreach ($values as $key => $value)
		{
			$current_key = isset($use_key) ? $value[$use_key] : $key;
			$current_value = isset($use_value) ? $value[$use_value] : $value;

			if (isset($select_options['selected']) && in_array($current_key, $select_options['selected']))
				$selected = 'selected="selected"';
			else
				$selected = '';

			$select_html .= '<option value="'.Tools::htmlentitiesUTF8($current_key).'" '.$selected.'>'.Tools::htmlentitiesUTF8($current_value).'</option>';
		}

		$select_html .= '</select>';
		return $select_html;
	}

	/**
	* Create html a string containing html options
	* eg: buildHtmlOptions(array('name' => 'myInputName', 'id' => 'myInputId'));
	*     return => 'name="myInputName" id="myInputId"'
	*
	* @param array $html_options
	*
	* @return string
	*/
	protected static function buildHtmlOptions(array $html_options)
	{
		$html = '';

		foreach ($html_options as $html_option => $value)
				$html .= Tools::htmlentitiesUTF8($html_option).'="'.Tools::htmlentitiesUTF8($value).'" ';

		return rtrim($html, ' ');
	}

	/**
	 * use translations files to replace english expression.
	 *
	 * @param mixed $string term or expression in english
	 * @param string $class
	 * @param boolan $addslashes if set to true, the return value will pass through addslashes(). Otherwise, stripslashes().
	 * @param boolean $htmlentities if set to true(default), the return value will pass through htmlentities($string, ENT_QUOTES, 'utf-8')
	 * @return string the translation if available, or the english default text.
	 */
	protected function l($string, $class = 'AdminTab', $addslashes = FALSE, $htmlentities = TRUE)
	{
		// if the class is extended by a module, use modules/[module_name]/xx.php lang file
		$currentClass = get_class($this);
		if(Module::getModuleNameFromClass($currentClass))
		{
			$string = str_replace('\'', '\\\'', $string);
			return Module::findTranslation(Module::$classInModule[$currentClass], $string, $currentClass);
		}
		global $_LANGADM;

        if ($class == __CLASS__)
                $class = 'AdminTab';

		$key = md5(str_replace('\'', '\\\'', $string));
		$str = (key_exists(get_class($this).$key, $_LANGADM)) ? $_LANGADM[get_class($this).$key] : ((key_exists($class.$key, $_LANGADM)) ? $_LANGADM[$class.$key] : $string);
		$str = $htmlentities ? htmlentities($str, ENT_QUOTES, 'utf-8') : $str;
		return str_replace('"', '&quot;', ($addslashes ? addslashes($str) : stripslashes($str)));
	}

	protected function displayAssoShop($type = 'shop')
	{
		if (!Shop::isFeatureActive() || (!$this->id && $this->context->shop->getContextType() != Shop::CONTEXT_ALL))
			return;

		if ($type != 'shop' && $type != 'group_shop')
			$type = 'shop';

		$assos = array();
		$sql = 'SELECT id_'.$type.', `'.pSQL($this->identifier).'`
				FROM `'._DB_PREFIX_.pSQL($this->table).'_'.$type.'`';

		foreach (Db::getInstance()->executeS($sql) as $row)
			$assos[$row['id_'.$type]][] = $row[$this->identifier];

		$html = <<<EOF
			<script type="text/javascript">
			$().ready(function()
			{
				// Click on "all shop"
				$('.input_all_shop').click(function()
				{
					var checked = $(this).attr('checked');
					$('.input_group_shop').attr('checked', checked);
					$('.input_shop').attr('checked', checked);
				});

				// Click on a group shop
				$('.input_group_shop').click(function()
				{
					$('.input_shop[value='+$(this).val()+']').attr('checked', $(this).attr('checked'));
					check_all_shop();
				});

				// Click on a shop
				$('.input_shop').click(function()
				{
					check_group_shop_status($(this).val());
					check_all_shop();
				});

				// Initialize checkbox
				$('.input_shop').each(function(k, v)
				{
					check_group_shop_status($(v).val());
					check_all_shop();
				});
			});

			function check_group_shop_status(id_group)
			{
				var groupChecked = true;
				$('.input_shop[value='+id_group+']').each(function(k, v)
				{
					if (!$(v).attr('checked'))
						groupChecked = false;
				});
				$('.input_group_shop[value='+id_group+']').attr('checked', groupChecked);
			}

			function check_all_shop()
			{
				var allChecked = true;
				$('.input_group_shop').each(function(k, v)
				{
					if (!$(v).attr('checked'))
						allChecked = false;
				});
				$('.input_all_shop').attr('checked', allChecked);
			}
			</script>
EOF;

		$html .= '<div class="assoShop">';
		$html .= '<table class="table" cellpadding="0" cellspacing="0" width="100%">
					<tr><th>'.$this->l('Shop').'</th></tr>';
		$html .= '<tr'.(($type == 'group_shop') ? ' class="alt_row"' : '').'><td><label class="t"><input class="input_all_shop" type="checkbox" /> '.$this->l('All shops').'</label></td></tr>';
		foreach (Shop::getTree() as $groupID => $groupData)
		{
			$groupChecked = ($type == 'group_shop' && ((isset($assos[$groupID]) && in_array($this->id, $assos[$groupID])) || !$this->id));
			$html .= '<tr'.(($type == 'shop') ? ' class="alt_row"' : '').'>';
			$html .= '<td><img style="vertical-align: middle;" alt="" src="../img/admin/lv2_b.gif" /><label class="t"><input class="input_group_shop" type="checkbox" name="checkBoxGroupShopAsso_'.$this->table.'_'.$this->id.'_'.$groupID.'" value="'.$groupID.'" '.($groupChecked ? 'checked="checked"' : '').' /> '.$groupData['name'].'</label></td>';
			$html .= '</tr>';

			if ($type == 'shop')
			{
				$total = count($groupData['shops']);
				$j = 0;
				foreach ($groupData['shops'] as $shopID => $shopData)
				{
					$checked = ((isset($assos[$shopID]) && in_array($this->id, $assos[$shopID])) || !$this->id);
					$html .= '<tr>';
					$html .= '<td><img style="vertical-align: middle;" alt="" src="../img/admin/lv3_'.(($j < $total - 1) ? 'b' : 'f').'.png" /><label class="child">';
					$html .= '<input class="input_shop" type="checkbox" value="'.$groupID.'" name="checkBoxShopAsso_'.$this->table.'_'.$this->id.'_'.$shopID.'" id="checkedBox_'.$shopID.'" '.($checked ? 'checked="checked"' : '').' /> ';
					$html .= $shopData['name'].'</label></td>';
					$html .= '</tr>';
					$j++;
				}
			}
		}
		$html .= '</table></div>';
		return $html;
	}

}

