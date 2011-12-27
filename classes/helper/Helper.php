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
	public $base_tpl = 'content.tpl';

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
	public static function renderAdminCategorieTree($trads, $selected_cat = array(), $input_name = 'categoryBox', $use_radio = false, $use_search = false, $disabled_categories = array(), $use_in_popup = false)
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
			var use_radio = '.(int)$use_radio.';';
		if (!$use_in_popup)
			$html .= '
			$(document).ready(function(){
				buildTreeView();
			});';
		else
			$html .= 'buildTreeView();';
		$html .= '</script>';

		$html .= '
		<div class="category-filter">
			<span><a href="#" id="collapse_all" >'.$trads['Collapse All'].'</a>
			| </span>
			<span><a href="#" id="expand_all" >'.$trads['Expand All'].'</a>
			'.(!$use_radio ? '
			 |</span>
			 <span> <a href="#" id="check_all" >'.$trads['Check All'].'</a>
			 |</span>
			 <span><a href="#" id="uncheck_all" >'.$trads['Uncheck All'].'</a>|</span>
			 ' : '').($use_search ? '<span>'.$trads['search'].' : <input type="text" name="search_cat" id="search_cat"></span>' : '').'
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

	/**
	 * Render an area to determinate shop association
	 * 
	 * @param string $type 'shop' or 'group_shop'
	 * 
	 * @return string
	 */
	public function renderAssoShop($type = 'shop')
	{
		if (!Shop::isFeatureActive())
			return;

		if ($type != 'shop' && $type != 'group_shop')
			$type = 'shop';

		$assos = array();

		if ((int)$this->id)
		{
			$sql = 'SELECT `id_'.bqSQL($type).'`, `'.bqSQL($this->identifier).'`
					FROM `'._DB_PREFIX_.bqSQL($this->table).'_'.bqSQL($type).'`
					WHERE `'.bqSQL($this->identifier).'` = '.(int)$this->id;

			foreach (Db::getInstance()->executeS($sql) as $row)
				$assos[$row['id_'.$type]] = $row['id_'.$type];
		}
		$tpl = $this->createTemplate('helper/assoshop.tpl');
		$tpl->assign(array(
			'input' => array(
				'type' => $type,
				'values' => Shop::getTree(),
			),
			'fields_value' => array(
				'shop' => $assos
			),
			'form_id' => $this->id,
			'table' => $this->table
		));
		return $tpl->fetch();
	}

	/**
	 * Render a form with potentials required fields
	 *
	 * @param string $class_name
	 * @param string $identifier
	 * @param array $table_fields
	 * @return string
	 */
	public function renderRequiredFields($class_name, $identifier, $table_fields)
	{
		$rules = call_user_func_array(array($class_name, 'getValidationRules'), array($class_name));
		$required_class_fields = array($identifier);
		foreach ($rules['required'] as $required)
			$required_class_fields[] = $required;

		$object = new $class_name();
		$res = $object->getFieldsRequiredDatabase();

		$required_fields = array();
		foreach ($res as $row)
			$required_fields[(int)$row['id_required_field']] = $row['field_name'];

		$this->tpl_vars = array(
			'table_fields' => $table_fields,
			'irow' => 0,
			'required_class_fields' => $required_class_fields,
			'required_fields' => $required_fields,
			'current' => $this->currentIndex,
			'token' => $this->token
		);

		$tpl = $this->createTemplate('helper/required_fields.tpl');
		$tpl->assign($this->tpl_vars);

		return $tpl->fetch();
	}
}

