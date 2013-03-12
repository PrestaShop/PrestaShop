<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
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
	public $toolbar_scroll = false;

	/**
	 * @var Module
	 */
	public $module;

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
		if ($this->override_folder)
		{
			if ($this->context->controller instanceof ModuleAdminController)
				$override_tpl_path = $this->context->controller->getTemplatePath().$this->override_folder.$this->base_folder.$tpl_name;
			else if ($this->module)
				$override_tpl_path = _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/_configure/'.$this->override_folder.$this->base_folder.$tpl_name;
			else
			{
				if (file_exists($this->context->smarty->getTemplateDir(1).DIRECTORY_SEPARATOR.$this->override_folder.$this->base_folder.$tpl_name))
					$override_tpl_path = $this->context->smarty->getTemplateDir(1).DIRECTORY_SEPARATOR.$this->override_folder.$this->base_folder.$tpl_name;
				else if (file_exists($this->context->smarty->getTemplateDir(0).DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$this->override_folder.$this->base_folder.$tpl_name))
					$override_tpl_path = $this->context->smarty->getTemplateDir(0).'controllers'.DIRECTORY_SEPARATOR.$this->override_folder.$this->base_folder.$tpl_name;

			}
		}
		else if ($this->module)
			$override_tpl_path = _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/_configure/'.$this->base_folder.$tpl_name;

		if (isset($override_tpl_path) && file_exists($override_tpl_path))
			return $this->context->smarty->createTemplate($override_tpl_path, $this->context->smarty);
		else
			return $this->context->smarty->createTemplate($this->base_folder.$tpl_name, $this->context->smarty);
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
	 * @deprecated 1.5.0
	 */
	public static function renderAdminCategorieTree($translations,
													$selected_cat = array(),
													$input_name = 'categoryBox',
													$use_radio = false,
													$use_search = false,
													$disabled_categories = array(),
													$use_in_popup = false)
	{
		Tools::displayAsDeprecated();

		$helper = new Helper();
		if (isset($translations['Root']))
			$root = $translations['Root'];
		else if (isset($translations['Home']))
			$root = array('name' => $translations['Home'], 'id_category' => 1);
		else
			throw new PrestaShopException('Missing root category parameter.');

		return $helper->renderCategoryTree($root, $selected_cat, $input_name, $use_radio, $use_search, $disabled_categories, $use_in_popup);
	}

	/**
	 *
	 * @param array $root array with the name and ID of the tree root category, if null the Shop's root category will be used
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
	 *							  )
	 *					)
	 * @param string $input_name name of input
	 * @param bool $use_radio use radio tree or checkbox tree
	 * @param bool $use_search display a find category search box
	 * @param array $disabled_categories
	 * @param bool $use_in_popup
	 * @param bool $use_shop_context
	 * @return string
	 */
	public function renderCategoryTree($root = null,
									   $selected_cat = array(),
									   $input_name = 'categoryBox',
									   $use_radio = false,
									   $use_search = false,
									   $disabled_categories = array(),
									   $use_in_popup = false,
									   $use_shop_context = false)
	{
		$translations = array(
			'selected' => $this->l('Selected'),
			'Collapse All' => $this->l('Collapse All'),
			'Expand All' => $this->l('Expand All'),
			'Check All' => $this->l('Check All'),
			'Uncheck All'  => $this->l('Uncheck All'),
			'search' => $this->l('Find a category')
		);

		$top_category = Category::getTopCategory();
		if (Tools::isSubmit('id_shop'))
			$id_shop = Tools::getValue('id_shop');
		else
			if (Context::getContext()->shop->id)
				$id_shop = Context::getContext()->shop->id;
			else
				if (!Shop::isFeatureActive())
					$id_shop = Configuration::get('PS_SHOP_DEFAULT');
				else
					$id_shop = 0;
		$shop = new Shop($id_shop);
		$root_category = Category::getRootCategory(null, $shop);
		$disabled_categories[] = $top_category->id;
		if (!$root)
			$root = array('name' => $root_category->name, 'id_category' => $root_category->id);

		if (!$use_radio)
			$input_name = $input_name.'[]';

		$this->context->controller->addCSS(_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.css');
		$this->context->controller->addJs(array(
			_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.js',
			_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.async.js',
			_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.edit.js',
			_PS_JS_DIR_.'admin-categories-tree.js'));
		if ($use_search)
			$this->context->controller->addJs(_PS_JS_DIR_.'jquery/plugins/autocomplete/jquery.autocomplete.js');

		$html = '
		<script type="text/javascript">
			var inputName = "'.$input_name.'";
		';
		if (count($selected_cat) > 0)
		{
			if (isset($selected_cat[0]))
				$html .= 'var selectedCat = "'.implode(',', $selected_cat).'";';
			else
				$html .= 'var selectedCat = "'.implode(',', array_keys($selected_cat)).'";';
		}
		else
			$html .= 'var selectedCat = "";';
		$html .= '
			var selectedLabel = \''.$translations['selected'].'\';
			var home = \''.$root['name'].'\';
			var use_radio = '.(int)$use_radio.';';
		if (!$use_in_popup)
			$html .= '
			$(document).ready(function(){
				buildTreeView('.$use_shop_context.');
			});';
		else
			$html .= 'buildTreeView('.$use_shop_context.');';
		$html .= '</script>';

		$html .= '
		<div class="category-filter">
			<span><a href="#" id="collapse_all" >'.$translations['Collapse All'].'</a>
			| </span>
			<span><a href="#" id="expand_all" >'.$translations['Expand All'].'</a>
			'.(!$use_radio ? '
			 |</span>
			 <span> <a href="#" id="check_all" >'.$translations['Check All'].'</a>
			 |</span>
			 <span><a href="#" id="uncheck_all" >'.$translations['Uncheck All'].'</a>|</span>
			 ' : '').($use_search ? '<span>'.$translations['search'].' : <input type="text" name="search_cat" id="search_cat"></span>' : '').'
		</div>
		';

		$home_is_selected = false;
		foreach ($selected_cat as $cat)
		{
			if (is_array($cat))
			{
				$disabled = in_array($cat['id_category'], $disabled_categories);
				if ($cat['id_category'] != $root['id_category'])
					$html .= '<input '.($disabled?'disabled="disabled"':'').' type="hidden" name="'.$input_name.'" value="'.$cat['id_category'].'" >';
				else
					$home_is_selected = true;
			}
			else
			{
				$disabled = in_array($cat, $disabled_categories);
				if ($cat != $root['id_category'])
					$html .= '<input '.($disabled?'disabled="disabled"':'').' type="hidden" name="'.$input_name.'" value="'.$cat.'" >';
				else
					$home_is_selected = true;
			}
		}

		$root_input = '&nbsp;';
		if ($root['id_category'] != $top_category->id || (Tools::isSubmit('ajax') && Tools::getValue('action') == 'getCategoriesFromRootCategory'))
			$root_input = '<input type="'.(!$use_radio ? 'checkbox' : 'radio').'" name="'
									.$input_name.'" value="'.$root['id_category'].'" '
									.($home_is_selected ? 'checked' : '').' onclick="clickOnCategoryBox($(this));" />
							<span class="category_label">'
								.$root['name'].
							'</span>';
		$html .= '
			<ul id="categories-treeview" class="filetree">
				<li id="'.$root['id_category'].'" class="hasChildren">
					<span class="folder">'.$root_input.' </span>
					<ul>
						<li><span class="placeholder">&nbsp;</span></li>
				  </ul>
				</li>
			</ul>';

		if ($use_search)
			$html .= '<script type="text/javascript">searchCategory();</script>';
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
	protected function l($string, $class = 'AdminTab', $addslashes = false, $htmlentities = true)
	{
		// if the class is extended by a module, use modules/[module_name]/xx.php lang file
		$currentClass = get_class($this);
		if (Module::getModuleNameFromClass($currentClass))
			return Translate::getModuleTranslation(Module::$classInModule[$currentClass], $string, $currentClass);

		return Translate::getAdminTranslation($string, get_class($this), $addslashes, $htmlentities);
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

		$tpl = $this->createTemplate('helpers/required_fields.tpl');
		$tpl->assign($this->tpl_vars);

		return $tpl->fetch();
	}
	
	public function renderModulesList($modules_list)
	{
		$this->tpl_vars = array('modules_list' => $modules_list);
		
		$tpl = $this->createTemplate('helpers/modules_list/list.tpl');
		$tpl->assign($this->tpl_vars);

		return $tpl->fetch();
	}

	public static function renderShopList()
	{
		if (!Shop::isFeatureActive() || Shop::getTotalShops(false, null) < 2)
			return null;

		$tree = Shop::getTree();
		$context = Context::getContext();

		// Get default value
		$shop_context = Shop::getContext();
		if ($shop_context == Shop::CONTEXT_ALL || ($context->controller->multishop_context_group == false && $shop_context == Shop::CONTEXT_GROUP))
			$value = '';
		else if ($shop_context == Shop::CONTEXT_GROUP)
			$value = 'g-'.Shop::getContextShopGroupID();
		else
			$value = 's-'.Shop::getContextShopID();

		// Generate HTML
		$url = $_SERVER['REQUEST_URI'].(($_SERVER['QUERY_STRING']) ? '&' : '?').'setShopContext=';
		$html = '<select class="shopList chosen" onchange="location.href = \''.$url.'\'+$(this).val();">';

		$html .= '<option value="" class="first">'.Translate::getAdminTranslation('All shops').'</option>';
		foreach ($tree as $gID => $group_data)
		{
			if ((!isset($context->controller->multishop_context) || $context->controller->multishop_context & Shop::CONTEXT_GROUP))
				$html .= '<option class="group" value="g-'.$gID.'" '.(($value == 'g-'.$gID) ? 'selected="selected"' : '').' '.($context->controller->multishop_context_group == false ? 'disabled="disabled"' : '').'>'.Translate::getAdminTranslation('Group:').' '.htmlspecialchars($group_data['name']).'</option>';
			else
				$html .= '<optgroup class="group" label="'.Translate::getAdminTranslation('Group:').' '.htmlspecialchars($group_data['name']).'" '.($context->controller->multishop_context_group == false ? 'disabled="disabled"' : '').'>';

			if (!isset($context->controller->multishop_context) || $context->controller->multishop_context & Shop::CONTEXT_SHOP)
				foreach ($group_data['shops'] as $sID => $shopData)
					if ($shopData['active'])
						$html .= '<option value="s-'.$sID.'" class="shop" '.(($value == 's-'.$sID) ? 'selected="selected"' : '').'>&raquo; '.($context->controller->multishop_context_group == false ? htmlspecialchars($group_data['name']).' - ' : '').$shopData['name'].'</option>';

			if (!(!isset($context->controller->multishop_context) || $context->controller->multishop_context & Shop::CONTEXT_GROUP))
				$html .= '</optgroup>';
		}
		$html .= '</select>';

		return $html;
	}
}

