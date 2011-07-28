<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class BlockLayered extends Module
{
	private $products;
	private $nbr_products;

	public function __construct()
	{
		$this->name = 'blocklayered';
		$this->tab = 'front_office_features';
		$this->version = 1.4;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Layered navigation block');
		$this->description = $this->l('Displays a block with layered navigation filters.');
	}

	public function install()
	{		
		if ($result = parent::install() AND $this->registerHook('leftColumn') AND $this->registerHook('header') AND $this->registerHook('footer')
		AND $this->registerHook('categoryAddition') AND $this->registerHook('categoryUpdate') AND $this->registerHook('categoryDeletion'))
		{
			Configuration::updateValue('PS_LAYERED_SHARE', 0);
			Configuration::updateValue('PS_LAYERED_HIDE_0_VALUES', 0);
			Configuration::updateValue('PS_LAYERED_SHOW_QTIES', 1);
			
			$this->rebuildLayeredStructure();
			$this->rebuildLayeredCache();
		}

		return $result;
	}
	
	public function uninstall()
	{
		/* Delete all configurations */
		Configuration::deleteByName('PS_LAYERED_SHARE');
		Configuration::deleteByName('PS_LAYERED_HIDE_0_VALUES');
		Configuration::deleteByName('PS_LAYERED_SHOW_QTIES');
		Configuration::deleteByName('PS_LAYERED_BITLY_USERNAME');
		Configuration::deleteByName('PS_LAYERED_BITLY_API_KEY');
		
		return parent::uninstall();
	}
	
	public function hookLeftColumn($params)
	{
		return $this->generateFiltersBlock($this->getSelectedFilters());
	}
	
	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}
	
	public function hookHeader($params)
	{
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return;

		$context = Context::getContext();
		$context->controller->addJS($this->_path.'blocklayered.js');
		$context->controller->addJS(_PS_JS_DIR_.'jquery/jquery-ui-1.8.10.custom.min.js');
		$context->controller->addCSS(_PS_CSS_DIR_.'jquery-ui-1.8.10.custom.css', 'all');
		$context->controller->addCSS(($this->_path).'blocklayered.css', 'all');
	}
		
	public function hookFooter($params)
	{
		if (basename($_SERVER['PHP_SELF']) == 'category.php')
			return '
			<script type="text/javascript">
				//<![CDATA[
				$(document).ready(function()
				{
					$(\'#selectPrductSort\').unbind(\'change\').bind(\'change\', function()
					{
						reloadContent();
					})
				});
				//]]>
				</script>';
	}
		
	public function hookCategoryAddition($params)
	{
		$this->rebuildLayeredCache(array(), array((int)$params['category']->id));
	}
	
	public function hookCategoryUpdate($params)
	{
		/* The category status might (active, inactive) have changed, we have to update the layered cache table structure */
		if (!$params['category']->active)
			$this->hookCategoryDeletion($params);
			}
	
	public function hookCategoryDeletion($params)
	{
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_category WHERE id_category = '.(int)$params['category']->id);
	}
	
	public function getContent()
	{
		$context = Context::getContext();
		
		$errors = array();
		$html = '';

		if (!extension_loaded('curl'))
			$html .= '<div class="warn"><p>'.$this->l('You must enable cURL extension on your server if you want to use short link.').'</p></div>';

		if (Tools::isSubmit('submitLayeredCache'))
		{
			$this->rebuildLayeredStructure();
			$this->rebuildLayeredCache();
			
			$html .= '
			<div class="conf">
				<img src="../img/admin/ok2.png" alt="" /> '.$this->l('Layered navigation database was initialized successfully').'
			</div>';
		}
		elseif (Tools::isSubmit('SubmitFilter'))
		{
			if (isset($_POST['id_layered_filter']) AND $_POST['id_layered_filter'])
				Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_filter WHERE id_layered_filter = '.(int)Tools::getValue('id_layered_filter'));
			
			if (Tools::getValue('scope') == 1)
			{
				Db::getInstance()->Execute('TRUNCATE TABLE '._DB_PREFIX_.'layered_filter');
				$categories = Db::getInstance()->ExecuteS('SELECT id_category FROM '._DB_PREFIX_.'category');
				foreach ($categories AS $category)
					$_POST['categoryBox'][] = (int)$category['id_category'];
			}
			
			if (sizeof($_POST['categoryBox']))
			{
				Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_category WHERE id_category IN ('.implode(',', $_POST['categoryBox']).')');

				$filterValues = array();

				$sqlToInsert = 'INSERT INTO '._DB_PREFIX_.'layered_category (id_category, id_value, type, position) VALUES ';
				foreach ($_POST['categoryBox'] AS $id_category_layered)
				{
					$n = 0;
					foreach ($_POST AS $key => $value)
						if (substr($key, 0, 17) == 'layered_selection' AND $value == 'on')
						{							
							$filterValues[$key] = $value;
							
							$n++;
							if ($key == 'layered_selection_stock')
								$sqlToInsert .= '('.(int)$id_category_layered.',NULL,\'quantity\','.(int)$n.'),';
							elseif ($key == 'layered_selection_subcategories')
								$sqlToInsert .= '('.(int)$id_category_layered.',NULL,\'category\','.(int)$n.'),';
							elseif ($key == 'layered_selection_condition')
								$sqlToInsert .= '('.(int)$id_category_layered.',NULL,\'condition\','.(int)$n.'),';
							elseif ($key == 'layered_selection_weight_slider')
								$sqlToInsert .= '('.(int)$id_category_layered.',NULL,\'weight\','.(int)$n.'),';
							elseif ($key == 'layered_selection_manufacturer')
								$sqlToInsert .= '('.(int)$id_category_layered.',NULL,\'manufacturer\','.(int)$n.'),';
							elseif (substr($key, 0, 21) == 'layered_selection_ag_')
								$sqlToInsert .= '('.(int)$id_category_layered.','.(int)str_replace('layered_selection_ag_', '', $key).',\'id_attribute_group\','.(int)$n.'),';
							elseif (substr($key, 0, 23) == 'layered_selection_feat_')
								$sqlToInsert .= '('.(int)$id_category_layered.','.(int)str_replace('layered_selection_feat_', '', $key).',\'id_feature\','.(int)$n.'),';
						}
					$filterValues['categories'] = Tools::getValue('categoryBox');
				}
				Db::getInstance()->Execute(rtrim($sqlToInsert, ','));
				
				$valuesToInsert = array('name' => pSQL(Tools::getValue('layered_tpl_name')), 'filters' => pSQL(serialize($filterValues)), 'n_categories' => (int)sizeof($_POST['categoryBox']), 'date_add' => date('Y-m-d H:i:s'));
				if (isset($_POST['id_layered_filter']) AND $_POST['id_layered_filter'])
					$valuesToInsert['id_layered_filter'] = (int)Tools::getValue('id_layered_filter');
				
				Db::getInstance()->AutoExecute(_DB_PREFIX_.'layered_filter', $valuesToInsert, 'INSERT');

				echo '<div class="conf"><img src="../img/admin/ok2.png" alt="" /> '.$this->l('Your filter').' "'.Tools::getValue('layered_tpl_name').'" '.((isset($_POST['id_layered_filter']) AND $_POST['id_layered_filter']) ? $this->l('was updated successfully.') : $this->l('was added successfully.')).'</div>';
			}
		}
		elseif (Tools::isSubmit('submitLayeredSettings'))
		{
			if (Tools::getValue('share_url'))
			{
				if (Tools::getValue('bitly_username') == '')
					$errors[] = $this->l('Bit.ly username is empty');
				if (Tools::getValue('bitly_api_key') == '')
					$errors[] = $this->l('Bit.ly api_key is empty');
			}
			
			if (!sizeof($errors))
			{
				Configuration::updateValue('PS_LAYERED_BITLY_USERNAME', Tools::getValue('bitly_username'));
				Configuration::updateValue('PS_LAYERED_BITLY_API_KEY', Tools::getValue('bitly_api_key'));
				Configuration::updateValue('PS_LAYERED_SHARE', Tools::getValue('share_url'));
				Configuration::updateValue('PS_LAYERED_HIDE_0_VALUES', Tools::getValue('ps_layered_hide_0_values'));
				Configuration::updateValue('PS_LAYERED_SHOW_QTIES', Tools::getValue('ps_layered_show_qties'));
				
				$html .= '
				<div class="conf">
					<img src="../img/admin/ok2.png" alt="" /> '.$this->l('Settings saved successfully').'
				</div>';
			}
			else
			{
				$html .= '
				<div class="error">
					<img src="../img/admin/error.png" alt="" title="" />'.$this->l('Settings not saved :').'<ul>';
						foreach($errors AS $error)
							$html .= '<li>'.$error.'</li>';
				$html .= '</ul>
				</div>';
			}
		
		}
		elseif (isset($_GET['deleteFilterTemplate']))
		{
			$layeredValues = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT filters 
			FROM '._DB_PREFIX_.'layered_filter 
			WHERE id_layered_filter = '.(int)$_GET['id_layered_filter']);
		
			if ($layeredValues)
			{
				Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_filter WHERE id_layered_filter = '.(int)$_GET['id_layered_filter'].' LIMIT 1');
				
				$html .= '
				<div class="conf">
					<img src="../img/admin/ok2.png" alt="" /> '.$this->l('Filters template deleted, categories updated (reverted to default Filters template).').'
				</div>';
			}
			else
			{
				$html .= '
				<div class="error">
					<img src="../img/admin/error.png" alt="" title="" /> '.$this->l('Filters template not found').'
				</div>';
			}
		}
		
		$html .= '
		<script type="text/javascript">
					$(document).ready(function()
					{
						$(\'.share_url\').change(function(){
							toggleBitly();
						});
						toggleBitly();
						
						function toggleBitly(){
							if ($(\'#share_url_on\').attr(\'checked\'))
								$(\'#bitly\').slideDown();
							else
								$(\'#bitly\').slideUp();
						}
					});
				</script>
		<h2>'.$this->l('Layered navigation').'</h2>
		<fieldset class="width4">
			<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Existing filters templates').'</legend>';
	
		$filtersTemplates = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM '._DB_PREFIX_.'layered_filter ORDER BY date_add DESC');
		if (sizeof($filtersTemplates))
		{		
			$html .= '<p>'.sizeof($filtersTemplates).' '.$this->l('filters templates are configured:').'</p>
			<table id="table-filter-templates" class="table" style="width: 700px;">
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Categories</th>
					<th>Created on</th>
					<th>Actions</th>
				</tr>';
				
			foreach ($filtersTemplates AS $filtersTemplate)
			{
				/* Clean request URI first */
				$_SERVER['REQUEST_URI'] = preg_replace('/&deleteFilterTemplate=[0-9]*&id_layered_filter=[0-9]*/', '', $_SERVER['REQUEST_URI']);
				
				$html .= '
				<tr>
					<td>'.(int)$filtersTemplate['id_layered_filter'].'</td>
					<td style="text-align: left; padding-left: 10px; width: 270px;">'.$filtersTemplate['name'].'</td>
					<td style="text-align: center;">'.(int)$filtersTemplate['n_categories'].'</td>
					<td>'.Tools::displayDate($filtersTemplate['date_add'], (int)$context->language->id, true).'</td>
					<td>
						<a href="#" onclick="updElements('.($filtersTemplate['n_categories'] ? 0 : 1).', '.(int)$filtersTemplate['id_layered_filter'].');"><img src="../img/admin/edit.gif" alt="" title="'.$this->l('Edit').'" /></a> 
						<a href="'.$_SERVER['REQUEST_URI'].'&deleteFilterTemplate=1&id_layered_filter='.(int)$filtersTemplate['id_layered_filter'].'" onclick="return confirm(\''.addslashes($this->l('Delete filter template #').(int)$filtersTemplate['id_layered_filter'].$this->l('?')).'\');"><img src="../img/admin/delete.gif" alt="" title="'.$this->l('Delete').'" /></a>
					</td>
				</tr>';
			}
				
			$html .= '
			</table>';
		}
		else
			$html .= $this->l('No filter template found.');
			
		$html .= '
		</fieldset><br />
		<fieldset class="width4">
			<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Build your own filters template').'</legend>
			<link rel="stylesheet" href="'._PS_CSS_DIR_.'jquery-ui-1.8.10.custom.css" />
			<style type="text/css">
				#layered_container_left ul, #layered_container_right ul { list-style-type: none; padding-left: 0px; }
				.ui-effects-transfer { border: 1px solid #CCC; }
				.ui-state-highlight { height: 1.5em; line-height: 1.2em; }
				ul#selected_filters, #layered_container_right ul { list-style-type: none; margin: 0; padding: 0; }
				ul#selected_filters li, #layered_container_right ul li { width: 326px; font-size: 11px; padding: 8px 9px 7px 20px; height: 14px; margin-bottom: 5px; }
				ul#selected_filters li span.ui-icon { position: absolute; margin-top: -2px; margin-left: -18px; }
				#layered_container_right ul li span { display: none; }
				#layered_container_right ul li { padding-left: 8px; }
				#layered_container_left ul li { cursor: move; }
				#layered-cat-counter { display: none; }
				#layered-step-2, #layered-step-3 { display: none; }
				#table-filter-templates tr th, #table-filter-templates tr td { text-align: center; }
			</style>
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post" onsubmit="return checkForm();">';
			
		$html.= '
			<h2>'.$this->l('Step 1/3 - Select categories').'</h2>
			<p style="margin-top: 20px;">'.$this->l('Use this template for:').' 
				<input type="radio" id="scope_1" name="scope" value="1" style="margin-left: 15px;" onclick="$(\'#error-treeview\').hide(); $(\'#layered-step-2\').show(); updElements(1, 0);" /> 
				<label for="scope_1" style="float: none;">'.$this->l('All categories').'</label>
				<input type="radio" id="scope_2" name="scope" value="2" style="margin-left: 15px;" onclick="$(\'label a#inline\').click(); $(\'#layered-step-2\').show();" /> 
				<label for="scope_2" style="float: none;"><a id="inline" href="#layered-categories-selection" style="text-decoration: underline;">'.$this->l('Specific').'</a> '.$this->l('categories').' (<span id="layered-cat-counter"></span> '.$this->l('selected').')</label>
			</p>
			<div id="error-treeview" class="error" style="display: none;">
				<img src="../img/admin/error2.png" alt="" /> '.$this->l('Please select at least one specific category or select "All categories".').'
			</div>
			<div style="display: none;">
				<div id="layered-categories-selection" style="padding: 10px; text-align: left;">
					<h2>'.$this->l('Categories using this template').'</h2>
					<ol style="padding-left: 20px;">
						<li>'.$this->l('Select one ore more category using this filter template').'</li>
						<li>'.$this->l('Press "Save this selection" or close the window to save').'</li>
					</ol>';

			$trads = array();
			$selectedCat = array();
			foreach(Helper::$translationsKeysForAdminCategorieTree AS $key)
				$trads[$key] = $this->l($key);
			$html .= Helper::renderAdminCategorieTree($trads, $selectedCat, 'categoryBox');
			
			$html .= '
					<br />
					<center><input type="button" class="button" value="'.$this->l('Save this selection').'" onclick="$.fancybox.close();" /></center>
				</div>
			</div>
			<div id="layered-step-2">
				<hr size="1" noshade />
				<h2>'.$this->l('Step 2/3 - Select filters').'</h2>
				<div id="layered_container">
					<div id="layered_container_left" style="width: 360px; float: left; height: 200px; overflow-y: auto;">
						<h3>'.$this->l('Selected filters').' <span id="num_sel_filters">(0)</span></h3>
						<p id="no-filters">'.$this->l('No filters selected yet.').'</p>
						<ul id="selected_filters"></ul>
					</div>
					<div id="layered-ajax-refresh">					
					'.$this->ajaxCallBackOffice().'
					</div>
				</div>
				<div class="clear"></div>
				<hr size="1" noshade />
				<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/jquery-ui-1.8.10.custom.min.js"></script>
				<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/jquery.fancybox-1.3.4.js"></script>
				<link type="text/css" rel="stylesheet" href="'.__PS_BASE_URI__.'css/jquery.fancybox-1.3.4.css" />
				<script type="text/javascript">					
					
					function updLayCounters()
					{
						$(\'#num_sel_filters\').html(\'(\'+$(\'ul#selected_filters\').find(\'li\').length+\')\');
						$(\'#num_avail_filters\').html(\'(\'+$(\'#layered_container_right ul\').find(\'li\').length+\')\');
						
						if ($(\'ul#selected_filters\').find(\'li\').length >= 1)
							$(\'#layered-step-3\').show();
						else
							$(\'#layered-step-3\').hide();
					}

					function updPositions()
					{
						$(\'#layered_container_left li\').each(function(idx) {
							$(this).find(\'span.position\').html(parseInt(1+idx)+\'. \');
						});
					}

					function updCatCounter()
					{
						$(\'#layered-cat-counter\').html($(\'#categories-treeview\').find(\'input:checked\').length);
						$(\'#layered-cat-counter\').show();
					}

					function updHeight()
					{
						$(\'#layered_container_left\').css(\'height\', 30+(1+$(\'#layered_container_left\').find(\'li\').length)*34);
						$(\'#layered_container_right\').css(\'height\', 30+(1+$(\'#layered_container_right\').find(\'li\').length)*34);
					}

					function updElements(all, id_layered_filter)
					{
						if ($(\'#error-treeview\').is(\':hidden\'))
							$(\'#layered-step-2\').show();
						else
							$(\'#layered-step-2\').hide();
						$(\'#layered-ajax-refresh\').css(\'background-color\', \'black\');
						$(\'#layered-ajax-refresh\').css(\'opacity\', \'0.2\');
						$(\'#layered-ajax-refresh\').html(\'<div style="margin: 0 auto; padding: 10px; text-align: center;"><img src="../img/admin/ajax-loader-big.gif" alt="" /><br /><p style="color: white;">'.$this->l('Loading...').'</p></div>\');
						
						$.ajax(
						{
							type: \'GET\',
							url: '.__PS_BASE_URI__.' + \'modules/blocklayered/blocklayered-ajax-back.php\',
							data: (all ? \'\' : $(\'input[name="categoryBox[]"]\').serialize()+\'&\')+(id_layered_filter ? \'id_layered_filter=\'+parseInt(id_layered_filter)+\'\' : \'\'),
							success: function(result)
							{
								$(\'#layered-ajax-refresh\').css(\'background-color\', \'transparent\');
								$(\'#layered-ajax-refresh\').css(\'opacity\', \'1\');
								$(\'#layered-ajax-refresh\').html(result);
								
								$(\'#layered_container_right li input\').each(function() {
									if ($(\'#layered_container_left\').find(\'input[id="\'+$(this).attr(\'id\')+\'"]\').length > 0)
										$(this).parent().remove();
								});								
								
								updHeight();
								updLayCounters();
							}
						});
					}
					
					function checkForm()
					{
						if ($(\'#scope_1\').attr(\'checked\') && $(\'#n_existing\').val() > 0)
							if (!confirm(\''.addslashes($this->l('You selected -All categories-, all existing filter templates will be deleted, OK?')).'\'))
								return false;
						return true;
					}

					function launch()
					{
						$(\'#layered_container input\').live(\'click\', function ()
						{
							if ($(this).parent().hasClass(\'layered_right\'))
							{
								$(\'p#no-filters\').hide();
								$(this).parent().css(\'background\', \'url("../img/jquery-ui/ui-bg_glass_100_fdf5ce_1x400.png") repeat-x scroll 50% 50% #FDF5CE\');
								$(this).parent().removeClass(\'layered_right\');
								$(this).parent().addClass(\'layered_left\');
								$(this).effect(\'transfer\', { to: $(\'#layered_container_left ul#selected_filters\') }, 300, function() {
									$(this).parent().appendTo(\'ul#selected_filters\');
									updLayCounters();
									updHeight();
									updPositions();
								});
							}
							else
							{
								$(this).parent().css(\'background\', \'url("../img/jquery-ui/ui-bg_glass_100_f6f6f6_1x400.png") repeat-x scroll 50% 50% #F6F6F6\');
								$(this).effect(\'transfer\', { to: $(\'#layered_container_right ul#all_filters\') }, 300, function() {									
									$(this).parent().removeClass(\'layered_left\');
									$(this).parent().addClass(\'layered_right\');
									$(this).parent().appendTo(\'ul#all_filters\');
									updLayCounters();
									updHeight();
									updPositions();
									if ($(\'#layered_container_left ul\').length == 0)
										$(\'p#no-filters\').show();
								});
							}
							enableSortable();
						});
						
						$(\'label a#inline\').fancybox({ 
							\'hideOnContentClick\': false,
							\'onClosed\': function() {
								updCatCounter();
								if ($(\'#categories-treeview\').find(\'input:checked\').length == 0)
									$(\'#error-treeview\').show(500);
								else
									$(\'#error-treeview\').hide(500);
								updElements(0, 0);
							}
						});

						updHeight();
						updLayCounters();
						updPositions();
						updCatCounter();
						enableSortable();
					}
					
					function enableSortable()
					{
						$(function() {
							$(\'ul#selected_filters\').sortable({
								axis: \'y\',
								update: function() { updPositions(); },
								placeholder: \'ui-state-highlight\'

							});
							$(\'ul#selected_filters\').disableSelection();
						});
					}

					$(document).ready(function() {
						launch();					
					});
				</script>
			</div>
			<div id="layered-step-3">
				<h2>'.$this->l('Step 3/3 - Name your template').'</h2>
				<p>'.$this->l('Template name:').' <input type="text" id="layered_tpl_name" name="layered_tpl_name" maxlength="64" value="'.$this->l('My template').' '.date('Y-m-d').'" style="width: 200px; font-size: 11px;" /> <span style="font-size: 10px; font-style: italic;">('.$this->l('only as a reminder').'</span>)</p>
				<hr size="1" noshade />
				<br />
				<center><input type="submit" class="button" name="SubmitFilter" value="'.$this->l('Save this filter template').'" /></center>
			</div>
				<input type="hidden" name="id_layered_filter" id="id_layered_filter" value="0" />
				<input type="hidden" name="n_existing" id="n_existing" value="'.(int)sizeof($filtersTemplates).'" />
			</form>
		</fieldset><br />
		<fieldset class="width2">
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Configuration').'</legend>
				<table border="0" style="font-size: 11px; width: 100%; margin: 0 auto;" class="table">
					<tr>
						<th style="text-align: center;">'.$this->l('Option').'</th>
						<th style="text-align: center;">'.$this->l('Value').'</th>
					</tr>
					<tr>
						<td style="text-align: right;">'.$this->l('Hide filter values with no product is matching').'</td>
						<td>
							<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />
							'.$this->l('Yes').' <input type="radio" name="ps_layered_hide_0_values" value="1" '.(Configuration::get('PS_LAYERED_HIDE_0_VALUES') ? 'checked="checked"' : '').' />
							<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" />
							'.$this->l('No').' <input type="radio" name="ps_layered_hide_0_values" value="0" '.(!Configuration::get('PS_LAYERED_HIDE_0_VALUES') ? 'checked="checked"' : '').' />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">'.$this->l('Show the number of matching products').'</td>
						<td>
							<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />
							'.$this->l('Yes').' <input type="radio" name="ps_layered_show_qties" value="1" '.(Configuration::get('PS_LAYERED_SHOW_QTIES') ? 'checked="checked"' : '').' />
							<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" />
							'.$this->l('No').' <input type="radio" name="ps_layered_show_qties" value="0" '.(!Configuration::get('PS_LAYERED_SHOW_QTIES') ? 'checked="checked"' : '').' />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">'.$this->l('Allow customers to share URLs').'</td>
						<td>
							<label class="t" for="share_url_on"><img src="../img/admin/enabled.gif" alt="Yes" title="Yes"></label>
							'.$this->l('Yes').' <input type="radio" id="share_url_on" name="share_url" class="share_url" value="1" '.(Configuration::get('PS_LAYERED_SHARE') ? 'checked="checked"' : '').'>
							<label class="t" for="share_url_off"><img src="../img/admin/disabled.gif" alt="No" title="No" style="margin-left: 10px;"></label>
							'.$this->l('No').' <input type="radio" id="share_url_off" name="share_url" class="share_url" value="0" '.(!Configuration::get('PS_LAYERED_SHARE') ? 'checked="checked"' : '').'>
						</td>
					</tr>				
				</table>
						<div id="bitly">
							<p>'.$this->l('To offer your customers short links, create an account on bit.ly, then copy and paste login and API key.').'
							<a style="text-decoration:underline" href="http://bit.ly/a/sign_up">'.$this->l('Sign Up').'</a></p>
							<label>'.$this->l('Login bit.ly').'</label>
							<div class="margin-form">
									<input type="text" name="bitly_username" value="'. Tools::getValue('bitly_username', Configuration::get('PS_LAYERED_BITLY_USERNAME')).'">
							</div>
							<label>'.$this->l('API Key bit.ly').'</label>
							<div class="margin-form">
								<input type="text" name="bitly_api_key" value="'.Tools::getValue('bitly_api_key', Configuration::get('PS_LAYERED_BITLY_API_KEY')).'">
							</div>
						</div>
				<p style="text-align: center;"><input type="submit" class="button" name="submitLayeredSettings" value="'.$this->l('Save configuration').'" /></p>
			</form>
		</fieldset>';

		return $html;
	}
	
	private function getSelectedFilters()
	{
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return;
		
		/* Analyze all the filters selected by the user and store them into a tab */
		$selectedFilters = array('category' => array(), 'manufacturer' => array(), 'quantity' => array(), 'condition' => array());
		foreach ($_GET AS $key => $value)
		{
			if (substr($key, 0, 8) == 'layered_')
			{
				preg_match('/^(.*)_[0-9|new|used|refurbished|slider]+$/', substr($key, 8, strlen($key) - 8), $res);
				if (isset($res[1]))
				{
					$tmpTab = explode('_', $value);
					$value = $tmpTab[0];
					$id_key = false;
					if (isset($tmpTab[1]))
						$id_key = $tmpTab[1];
					if ($res[1] == 'condition' AND in_array($value, array('new', 'used', 'refurbished')))
						$selectedFilters['condition'][] = $value;
					elseif ($res[1] == 'quantity' AND (!$value OR $value == 1))
						$selectedFilters['quantity'][] = $value;
					elseif (in_array($res[1], array('id_attribute_group', 'category', 'id_feature', 'manufacturer')))
					{
						if (!isset($selectedFilters[$res[1].($id_key ? '_'.$id_key : '')]))
							$selectedFilters[$res[1].($id_key ? '_'.$id_key : '')] = array();
						$selectedFilters[$res[1].($id_key ? '_'.$id_key : '')][] = (int)$value;
					}
					elseif (in_array($res[1], array('weight')))
						$selectedFilters[$res[1]] = $tmpTab;
				}
			}
		}
		return $selectedFilters;
	}
	
	public function getProductByFilters($selectedFilters = array())
	{
		if (!empty($this->products))
			return $this->products;
			
		$context = Context::getContext();

		/* If the current category isn't defined of if it's homepage, we have nothing to display */
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return;

		$queryFilters = '';
		
		$parent = new Category((int)$id_parent);
		if (!sizeof($selectedFilters['category']))
			 $queryFilters .= ' AND p.id_product IN (SELECT id_product FROM '._DB_PREFIX_.'category_product cp 
			 LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category) 
			 WHERE 1 AND c.nleft >= '.(int)$parent->nleft.' AND c.nright <= '.(int)$parent->nright.')';

		foreach ($selectedFilters AS $key => $filterValues)
		{
			if (!sizeof($filterValues))
				continue;

			preg_match('/^(.*[^_0-9])/', $key, $res);
			$key = $res[1];

			switch ($key)
			{
				case 'id_feature':
					$queryFilters .= ' AND p.id_product IN ( SELECT id_product FROM '._DB_PREFIX_.'feature_product fp WHERE ';
					foreach ($filterValues AS $filterValue)
						$queryFilters .= 'fp.`id_feature_value` = '.(int)$filterValue.' OR ';
					$queryFilters = rtrim($queryFilters, 'OR ').')';
				break;

				case 'id_attribute_group':
					$queryFilters .= ' AND p.id_product IN ( SELECT pa.`id_product`
										FROM `'._DB_PREFIX_.'product_attribute_combination` pac
										LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
										ON (pa.`id_product_attribute` = pac.`id_product_attribute`) WHERE ';
										
					foreach ($filterValues AS $filterValue)
						$queryFilters .= 'pac.`id_attribute` = '.(int)$filterValue.' OR ';
					$queryFilters = rtrim($queryFilters, 'OR ').')';
				break;

				case 'category':
					$queryFilters .= ' AND p.id_product IN (SELECT id_product FROM '._DB_PREFIX_.'category_product cp WHERE ';				
						foreach ($selectedFilters['category'] AS $id_category)
							$queryFilters .= 'cp.`id_category` = '.(int)$id_category.' OR ';
						$queryFilters = rtrim($queryFilters, 'OR ').')';
				break;

				case 'quantity':
					if (sizeof($selectedFilters['quantity']) == 2)
						break;
					$queryFilters .= ' AND p.quantity '.(!$selectedFilters['quantity'][0] ? '=' : '>').' 0';
				break;

				case 'manufacturer':
					$queryFilters .= ' AND p.id_manufacturer IN ('.implode($selectedFilters['manufacturer'], ',').')';
				break;

				case 'condition':
					if (sizeof($selectedFilters['condition']) == 3)
						break;
					$queryFilters .= ' AND p.condition IN (';
					foreach ($selectedFilters['condition'] AS $cond)
						$queryFilters .= '\''.$cond.'\',';
					$queryFilters = rtrim($queryFilters, ',').')';
				break;

				case 'weight':
					$queryFilters .= ' AND p.`weight` BETWEEN '.(float)($selectedFilters['weight'][0] - 0.001).' AND '.(float)($selectedFilters['weight'][1] + 0.001);
				break;
			}
		}

		/* Return only the number of products */
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT COUNT(p.`id_product`) AS total
		FROM `'._DB_PREFIX_.'product` p
		WHERE 1 '.$queryFilters);

		$this->nbr_products = isset($result) ? (int)$result['total'] : 0;

		$n = (int)Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'));
		$sql = 'SELECT p.id_product, p.on_sale, p.out_of_stock, p.available_for_order, p.quantity, p.minimal_quantity, p.id_category_default, p.customizable, p.show_price, p.`weight`,
					p.ean13, pl.available_later, pl.description_short, pl.link_rewrite, pl.name, i.id_image, il.legend,  m.name manufacturer_name, p.condition, p.id_manufacturer, stock.quantity,
					DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new
				FROM '._DB_PREFIX_.'product p
				'.$context->shop->sqlAsso('product', 'p').'
				LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product'.$context->shop->sqlLang('pl').')
				'.Product::sqlStock('p', 0).'
				LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = p.id_product AND i.cover = 1)
				LEFT JOIN '._DB_PREFIX_.'image_lang il ON (i.id_image = il.id_image AND il.id_lang = '.(int)$context->language->id.')
				LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
				WHERE p.`active` = 1
					AND pl.id_lang = '.(int)$context->language->id
					.$queryFilters.
				' ORDER BY '.Tools::getProductsOrder('by', Tools::getValue('orderby')).' '.Tools::getProductsOrder('way', Tools::getValue('orderway')).
				' LIMIT '.(((int)(Tools::getValue('p', 1)) - 1) * $n.','.$n);
		$this->products = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);

		return $this->products;
	}
	
	public function generateFiltersBlock($selectedFilters = array())
	{
		$context = Context::getContext();

		/* If the current category isn't defined of if it's homepage, we have nothing to display */
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return;

		/* First we need to get all subcategories of current category */
		$category = new Category($id_parent);

		$groups = FrontController::getCurrentCustomerGroups();

		$subCategories = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.id_category, c.id_parent, cl.name
		FROM '._DB_PREFIX_.'category c
		LEFT JOIN '._DB_PREFIX_.'category_group cg ON (cg.id_category = c.id_category)
		LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category)
		WHERE c.nleft > '.(int)$category->nleft.' and c.nright <= '.(int)$category->nright.' AND c.active = 1 AND c.id_parent = '.(int)$category->id.' AND cl.id_lang = '.(int)$context->language->id.'
		AND cg.id_group '.pSQL(sizeof($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').'
		GROUP BY c.id_category
		ORDER BY c.position ASC');
		
		$whereC = ' cp.`id_category` = '.(int)$id_parent.' OR ';
		foreach ($subCategories AS $subcategory)
				$whereC .= ' cp.`id_category` = '.(int)$subcategory['id_category'].' OR ';

		$whereC = rtrim($whereC, 'OR ');
		$productsSQL = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT p.`id_product`, p.`condition`, p.`id_manufacturer`, p.`weight`, stock.quantity,
		(SELECT GROUP_CONCAT(`id_category`) FROM `'._DB_PREFIX_.'category_product` cp WHERE cp.`id_product` = p.`id_product`) as ids_cat,
			(SELECT GROUP_CONCAT(`id_feature_value`) FROM `'._DB_PREFIX_.'feature_product` fp WHERE fp.`id_product` = p.`id_product`) as ids_feat,
			(SELECT GROUP_CONCAT(DISTINCT(pac.`id_attribute`)) 
				FROM `'._DB_PREFIX_.'product_attribute_combination` pac 
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.`id_product_attribute` = pac.`id_product_attribute`) 
				WHERE pa.`id_product` = p.`id_product` ) as ids_attr
		FROM '._DB_PREFIX_.'product p 
		'.Product::sqlStock('p', 0).'
		WHERE p.`active` = 1 AND p.`id_product` IN ( SELECT id_product FROM `'._DB_PREFIX_.'category_product` cp WHERE'.$whereC.')', false);

		$products = array();
		$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
		$weight = array();
		while ($product = $db->nextRow($productsSQL))
		{
			$row = array();
			foreach ($product AS $key => $value)
			{
				if($key == 'ids_feat')
					$row['f'] = explode(',', $value);
				if($key == 'ids_attr')
					$row['a'] = explode(',', $value);
				if($key == 'ids_cat')
					$row['c'] = explode(',', $value);
				if($key == 'weight')
					$weight[] = $value;
			}
			
			$row['id_manufacturer'] = (int)$product['id_manufacturer'];
			$row['quantity'] = (bool)$product['quantity'];
			$row['condition'] = $product['condition'];
			$row['weight'] = $product['weight'];
			$products[(int)$product['id_product']] = $row;
		}

		/* Get the filters for the current category */
		$filters = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM '._DB_PREFIX_.'layered_category WHERE id_category = '.(int)$id_parent.' ORDER BY position ASC');
		$filterBlocks = $f = $a = array();
	
		foreach ($filters AS $filter)
		{
			$filterBlocks[(int)$filter['position']]['type_lite'] = $filter['type'];
			$filterBlocks[(int)$filter['position']]['type'] = $filter['type'].($filter['id_value'] ? '_'.(int)$filter['id_value'] : '');
			$filterBlocks[(int)$filter['position']]['id_key'] = (int)$filter['id_value'];
			switch ($filter['type'])
			{					
				case 'id_feature':
					$f[] = (int)$filter['id_value'];
					
					$filterBlocks[(int)$filter['position']]['SQLvalues'] = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT fvl.id_feature_value, fvl.value
					FROM '._DB_PREFIX_.'feature_value fv
					LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = fv.id_feature_value)
					WHERE (fv.custom IS NULL OR fv.custom = 0) AND fv.id_feature = '.(int)$filterBlocks[(int)$filter['position']]['id_key'].' AND fvl.id_lang = '.(int)$context->language->id);
					break;

				case 'id_attribute_group':
					$a[] = (int)$filter['id_value'];
					
					$filterBlocks[(int)$filter['position']]['SQLvalues'] = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT al.id_attribute, al.name, a.color
					FROM '._DB_PREFIX_.'attribute a
					LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = a.id_attribute)
					WHERE a.id_attribute_group = '.(int)$filterBlocks[(int)$filter['position']]['id_key'].' AND al.id_lang = '.(int)$context->language->id);					
					break;
			}
		}

		/* Get the feature block names & values */
		if (sizeof($f))
		{
			$fNames = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT id_feature, name
			FROM '._DB_PREFIX_.'feature_lang
			WHERE id_lang = '.(int)$context->language->id.' AND id_feature IN ('.implode(',', $f).')');
			$fNameByID = array();
			foreach ($fNames AS $fName)
				$fNameByID[(int)$fName['id_feature']] = $fName['name'];
		}
		
		/* Get the attribute block names & values */
		if (sizeof($a))
		{
			$aNames = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT ag.id_attribute_group, agl.public_name, ag.is_color_group
			FROM '._DB_PREFIX_.'attribute_group ag
			LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group = ag.id_attribute_group)
			WHERE agl.id_lang = '.(int)$context->language->id.' AND ag.id_attribute_group IN ('.implode(',', $a).')');

			$aNameByID = $colorGroups = array();
			foreach ($aNames AS $aName)
			{
				$aNameByID[(int)$aName['id_attribute_group']] = $aName['public_name'];
				if ($aName['is_color_group'])
					$colorGroups[(int)$aName['id_attribute_group']] = true;
			}
		}

		$weight_unit = Configuration::get('PS_WEIGHT_UNIT');
		foreach ($filterBlocks AS &$filterBlock)
		{
			if ($filterBlock['type_lite'] == 'category')
			{
				$filterBlock['name'] = $this->l('Categories');
				
				$c = array();
				foreach ($subCategories AS $subCat)
				{
					$c[] = (int)$subCat['id_category'];
					$filterBlock['values'][(int)$subCat['id_category']]['name'] = $subCat['name'];
					
					//init the number of product in this category
					if (!isset($filterBlock['values'][(int)$subCat['id_category']]['nbr']))
						$filterBlock['values'][(int)$subCat['id_category']]['nbr'] = 0;

					//check if the category is selected and set to true
					if (isset($selectedFilters['category']) AND in_array($subCat['id_category'], $selectedFilters['category']))
						$filterBlock['values'][(int)$subCat['id_category']]['checked'] = true;
				}
				
				$productCat = $this->filterProducts($products, $selectedFilters, 'category');
				
				// Count number of products in each category
				foreach ($c AS $idSubCategory)
					foreach ($productCat AS $product)
						if (in_array($idSubCategory, $product['c']))
							$filterBlock['values'][(int)$idSubCategory]['nbr']++;
							
				if (Configuration::get('PS_LAYERED_HIDE_0_VALUES') AND !$filterBlock['values'][(int)$idSubCategory]['nbr'])
					unset($filterBlock['values'][(int)$subCat['id_category']]);
			}
			elseif ($filterBlock['type_lite'] == 'id_feature')
			{
				$filterBlock['name'] = $fNameByID[(int)$filterBlock['id_key']];
				$filterBlock['values'] = array();
				
				$productFeat = $this->filterProducts($products, $selectedFilters, 'id_feature_'.(int)$filterBlock['id_key']);
					
				foreach ($filterBlock['SQLvalues'] AS $value)
				{	
					foreach ($productFeat AS $product)
					{
						if (Configuration::get('PS_LAYERED_HIDE_0_VALUES') AND !in_array($value['id_feature_value'], $product['f']))
							continue;
						else
						{
							$filterBlock['values'][(int)$value['id_feature_value']]['name'] = $value['value'];
							if (!isset($filterBlock['values'][(int)$value['id_feature_value']]['nbr']))
								$filterBlock['values'][(int)$value['id_feature_value']]['nbr'] = 0;
							if (in_array($value['id_feature_value'], $product['f']))
							$filterBlock['values'][(int)$value['id_feature_value']]['nbr']++;							
						}
						if (in_array($value['id_feature_value'], $product['f']) AND !isset($filterBlock['values'][(int)$value['id_feature_value']]))
						{
							$filterBlock['values'][(int)$value['id_feature_value']]['name'] = $value['value'];
							$filterBlock['values'][(int)$value['id_feature_value']]['nbr'] = 0;
						}
					}
					if (isset($selectedFilters['id_feature_'.(int)$filterBlock['id_key']]) AND in_array((int)$value['id_feature_value'].'_'.(int)$filterBlock['id_key'], $selectedFilters['id_feature_'.(int)$filterBlock['id_key']]))
						$filterBlock['values'][(int)$value['id_feature_value']]['checked'] = true;
				}
				
				unset($filterBlock['SQLvalues']);
			}
			elseif ($filterBlock['type_lite'] == 'id_attribute_group')
			{
				$filterBlock['name'] = $aNameByID[(int)$filterBlock['id_key']];
				$filterBlock['is_color_group'] = isset($colorGroups[(int)$filterBlock['id_key']]);
				$filterBlock['values'] = array();
				
				$productsAttr = $this->filterProducts($products, $selectedFilters, 'id_attribute_group_'.(int)$filterBlock['id_key']);
				
				foreach ($filterBlock['SQLvalues'] AS $value)
				{
					foreach ($productsAttr AS $product)
					{
						if(in_array($value['id_attribute'], $product['a']))
						{
							$filterBlock['values'][(int)$value['id_attribute']]['name'] = $value['name'];
							$filterBlock['values'][(int)$value['id_attribute']]['color'] = $value['color'];
							if (!isset($filterBlock['values'][(int)$value['id_attribute']]['nbr']))
								$filterBlock['values'][(int)$value['id_attribute']]['nbr'] = 0;
								$filterBlock['values'][(int)$value['id_attribute']]['nbr']++;							
						}
						if (isset($product['a'.$value['id_attribute']]) AND !isset($filterBlock['values'][(int)$value['id_attribute']]))
						{
							$filterBlock['values'][(int)$value['id_attribute']]['name'] = $value['name'];
							$filterBlock['values'][(int)$value['id_attribute']]['nbr'] = 0;
						}
					}
					if (isset($selectedFilters['id_attribute_group_'.(int)$filterBlock['id_key']]) AND in_array((int)$value['id_attribute'].'_'.(int)$filterBlock['id_key'], $selectedFilters['id_attribute_group_'.(int)$filterBlock['id_key']]))
						$filterBlock['values'][(int)$value['id_attribute']]['checked'] = true;
				}
				unset($filterBlock['SQLvalues']);
			}
			elseif ($filterBlock['type_lite'] == 'condition')
			{
				$filterBlock['name'] = $this->l('Condition');
				$filterBlock['values'] = array(
				'new' => array('name' => $this->l('New'), 'nbr' => 0), 
				'used' => array('name' => $this->l('Used'), 'nbr' => 0), 
				'refurbished' => array('name' => $this->l('Refurbished'), 'nbr' => 0));
				
				$productCond = $this->filterProducts($products, $selectedFilters, 'condition');
				
				foreach ($filterBlock['values'] AS $conditionKey => &$condition)
				{
					foreach ($productCond AS $product)
						if ($product['condition'] == $conditionKey)
							$condition['nbr']++;
					if (isset($selectedFilters['condition']) AND in_array($conditionKey, $selectedFilters['condition']))
						$condition['checked'] = true;
				}
				
				if (Configuration::get('PS_LAYERED_HIDE_0_VALUES'))
				{
					foreach ($filterBlock['values'] AS $conditionKey2 => $condition2)
						if (!$condition2['nbr'])
							unset($filterBlock['values'][$conditionKey2]);
			}
			}
			elseif ($filterBlock['type_lite'] == 'quantity')
			{
				$filterBlock['name'] = $this->l('Availability');
				$filterBlock['values'] = array(
				'1' => array('name' => $this->l('In stock'), 'nbr' => 0),
				'0' => array('name' => $this->l('Not available'), 'nbr' => 0));				
				
				$productQuant = $this->filterProducts($products, $selectedFilters, 'quantity');
				
				foreach ($filterBlock['values'] AS $quantKey => &$quantity)
				{
					foreach ($productQuant AS $product)
						if ($product['quantity'] == $quantKey)
							$quantity['nbr']++;
					if (isset($selectedFilters['quantity']) AND in_array($quantKey, $selectedFilters['quantity']))
						$quantity['checked'] = true;
				}
				
				if (Configuration::get('PS_LAYERED_HIDE_0_VALUES'))
				{
					foreach ($filterBlock['values'] AS $quantKey2 => $quantity2)
						if (!$quantity2['nbr'])
							unset($filterBlock['values'][$quantKey2]);
			}
			}
			elseif ($filterBlock['type_lite'] == 'manufacturer')
			{
				$filterBlock['name'] = $this->l('Manufacturer');
				
				$man = array();
				$productKeys = array_keys($products);
				if(!empty($productKeys))
					$man = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT DISTINCT(p.id_manufacturer), m.name
					FROM '._DB_PREFIX_.'product p			
					LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
					WHERE p.id_product IN ('.implode(',', $productKeys).') AND p.id_manufacturer != 0');
				
				$productsManuf = $this->filterProducts($products, $selectedFilters, 'manufacturer');
				
				$productsManuf = $this->filterProducts($products, $selectedFilters, 'manufacturer');

				foreach ($man AS $manufacturer)
				{
					$filterBlock['values'][(int)$manufacturer['id_manufacturer']]['name'] = $manufacturer['name'];
					if (!isset($filterBlock['values'][(int)$manufacturer['id_manufacturer']]['nbr']))
						$filterBlock['values'][(int)$manufacturer['id_manufacturer']]['nbr'] = 0;					
					foreach ($productsManuf AS $product)
						if ($product['id_manufacturer'] == $manufacturer['id_manufacturer'])
							$filterBlock['values'][(int)$manufacturer['id_manufacturer']]['nbr']++;
					if (isset($selectedFilters['manufacturer']) AND in_array($manufacturer['id_manufacturer'], $selectedFilters['manufacturer']))
						$filterBlock['values'][(int)$manufacturer['id_manufacturer']]['checked'] = true;
				}
			}
			elseif ($filterBlock['type_lite'] == 'weight')
			{
				if (!empty($weight) && max($weight) != min($weight))
				{
					$filterBlock['name'] = $this->l('Weight');
					$filterBlock['slider'] = true;
					$filterBlock['max'] = max($weight);
					$filterBlock['min'] = min($weight);
					if (isset($selectedFilters['weight']))
						$filterBlock['values'] = array($selectedFilters['weight'][0], $selectedFilters['weight'][1]);
					else
						$filterBlock['values'] = array(min($weight), max($weight));
					$filterBlock['unit'] = $weight_unit;
				}
				else
					unset($selectedFilters['weight']);
			}
		}
		
		$nFilters = 0;
		foreach ($selectedFilters AS $filters)
			$nFilters += sizeof($filters);

		$params = '?';
		foreach($_GET AS $key => $val)
			$params .= $key.'='.$val.'&';
		
		$share_url = $context->link->getCategoryLink((int)$category->id, $category->link_rewrite[(int)$context->language->id], $context->language->id).rtrim($params, '&');
				
		$context->smarty->assign(array(
		'display_share' => (int)Configuration::get('PS_LAYERED_SHARE'),
		'share_url' => $this->getShortLink($share_url),
		'layered_show_qties' => (int)Configuration::get('PS_LAYERED_SHOW_QTIES'),
		'id_category_layered' => (int)$id_parent,
		'selected_filters' => $selectedFilters,
		'n_filters' => (int)$nFilters,
		'nbr_filterBlocks' => sizeof($filterBlocks),
		'filters' => $filterBlocks));
				
		return $this->display(__FILE__, 'blocklayered.tpl');
	}
	
	public function ajaxCallBackOffice($categoryBox = array(), $id_layered_filter = NULL)
	{
		$context = Context::getContext();
				
		if (!empty($id_layered_filter))
		{
			$layeredFilter = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'layered_filter WHERE id_layered_filter = '.(int)$id_layered_filter);
			if ($layeredFilter AND isset($layeredFilter['filters']) AND !empty($layeredFilter['filters']))
				$layeredValues = unserialize($layeredFilter['filters']);
			if (isset($layeredValues['categories']) AND sizeof($layeredValues['categories']))
				foreach ($layeredValues['categories'] AS $id_category)
					$categoryBox[] = (int)$id_category;
		}
		
		$attributeGroups = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT ag.id_attribute_group, ag.is_color_group, agl.name, COUNT(DISTINCT(a.id_attribute)) n
		FROM '._DB_PREFIX_.'attribute_group ag
		LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group = ag.id_attribute_group)
		LEFT JOIN '._DB_PREFIX_.'attribute a ON (a.id_attribute_group = ag.id_attribute_group)
		'.(sizeof($categoryBox) ? '
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_attribute = a.id_attribute)
		LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = pac.id_product_attribute)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = pa.id_product)' : '').'
		WHERE agl.id_lang = '.(int)$context->language->id.
		(sizeof($categoryBox) ? ' AND cp.id_category IN ('.implode(',', $categoryBox).')' : '').'
		GROUP BY ag.id_attribute_group');
		
		$features = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT fl.id_feature, fl.name, COUNT(DISTINCT(fv.id_feature_value)) n
		FROM '._DB_PREFIX_.'feature_lang fl
		LEFT JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature = fl.id_feature)
		'.(sizeof($categoryBox) ? '
		LEFT JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_feature = fv.id_feature)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = fp.id_product)' : '').'		
		WHERE (fv.custom IS NULL OR fv.custom = 0) AND fl.id_lang = '.(int)$context->language->id.
		(sizeof($categoryBox) ? ' AND cp.id_category IN ('.implode(',', $categoryBox).')' : '').'
		GROUP BY fl.id_feature');
		
		$nElements = sizeof($attributeGroups) + sizeof($features) + 4;
		if ($nElements > 20)
			$nElements = 20;
		
		$html = '					
		<div id="layered_container_right" style="width: 360px; float: left; margin-left: 20px; height: '.(int)(30 + $nElements * 38).'px; overflow-y: auto;">
			<h3>'.$this->l('Available filters').' <span id="num_avail_filters">(0)</span></h3>
			<ul id="all_filters"></ul>
			<ul><li class="ui-state-default layered_right"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" id="layered_selection_subcategories" name="layered_selection_subcategories" /> <span class="position"></span>'.$this->l('Sub-categories filter').'</li></ul>
			<ul><li class="ui-state-default layered_right"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" id="layered_selection_stock" name="layered_selection_stock" /> <span class="position"></span>'.$this->l('Product stock filter').'</li></ul>
			<ul><li class="ui-state-default layered_right"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" id="layered_selection_condition" name="layered_selection_condition" /> <span class="position"></span>'.$this->l('Product condition filter').'</li></ul>
			<ul><li class="ui-state-default layered_right"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" id="layered_selection_manufacturer" name="layered_selection_manufacturer" /> <span class="position"></span>'.$this->l('Product manufacturer filter').'</li></ul>
			<ul><li class="ui-state-default layered_right"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" id="layered_selection_weight_slider" name="layered_selection_weight_slider" /> <span class="position"></span>'.$this->l('Product weight filter (slider)').'</li></ul>';
			
			if (sizeof($attributeGroups))
			{
				$html .= '<ul>';
				foreach ($attributeGroups AS $attributeGroup)
					$html .= '<li class="ui-state-default layered_right"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" id="layered_selection_ag_'.(int)$attributeGroup['id_attribute_group'].'" name="layered_selection_ag_'.(int)$attributeGroup['id_attribute_group'].'" /> <span class="position"></span>'.$this->l('Attribute group:').' '.$attributeGroup['name'].' ('.(int)$attributeGroup['n'].' '.($attributeGroup['n'] > 1 ? $this->l('attributes') : $this->l('attribute')).')'.($attributeGroup['is_color_group'] ? ' <img src="../img/admin/color_swatch.png" alt="" title="'.$this->l('This group will allow user to select a color').'" />' : '').'</li>';
				$html .= '</ul>';
			}

			if (sizeof($features))
			{
				$html .= '<ul>';
				foreach ($features AS $feature)
					$html .= '<li class="ui-state-default layered_right"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" id="layered_selection_feat_'.(int)$feature['id_feature'].'" name="layered_selection_feat_'.(int)$feature['id_feature'].'" /> <span class="position"></span>'.$this->l('Feature:').' '.$feature['name'].' ('.(int)$feature['n'].' '.($feature['n'] > 1 ? $this->l('values') : $this->l('value')).')</li>';
				$html .= '</ul>';
			}

		$html .= '
		</div>';
		
		if (isset($layeredValues))
		{
			$html .= '
			<script type="text/javascript">
				//<![CDATA[
				$(document).ready(function()
				{
					$(\'#selected_filters li\').remove();
			';
				
			foreach ($layeredValues AS $key => $layeredValue)
				if ($key != 'categories')
					$html .= '$(\'#'.$key.'\').click();'."\n";
			
			if (isset($layeredValues['categories']) AND sizeof($layeredValues['categories']))
			{
				foreach ($layeredValues['categories'] AS $id_category)
					$html .= '$(\'#categories-treeview\').find(\'input[name="categoryBox[]"][value='.(int)$id_category.']\').attr(\'checked\', \'checked\');'."\n";
				$html .= '
				updCatCounter();
				$(\'#scope_1\').attr(\'checked\', \'\');
				$(\'#scope_2\').attr(\'checked\', \'checked\');
				';
			}
			else
				$html .= '
				$(\'#scope_2\').attr(\'checked\', \'\');
				$(\'#scope_1\').attr(\'checked\', \'checked\');
				';
				
			$html .= '
			$(\'#layered_tpl_name\').val(\''.addslashes($layeredFilter['name']).'\');
			$(\'#id_layered_filter\').val(\''.(int)$layeredFilter['id_layered_filter'].'\')';
				
			$html .= '
				});
			</script>';
		}

		return $html;
	}
	
	public function ajaxCall()
	{
		$context = Context::getContext();

		$selectedFilters = $this->getSelectedFilters();
		$products = $this->getProductByFilters($selectedFilters);
		$products = Product::getProductsProperties($context->language->id, $products);
		$nbProducts = $this->nbr_products;
		$range = 2; /* how many pages around page selected */
		
		$n = (int)Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'));
		$p = Tools::getValue('p', 1);
		
		if ($p < 0)
			$p = 0;
		
		if ($p > ($nbProducts / $n))
			$p = ceil($nbProducts / $n);
		$pages_nb = ceil($nbProducts / (int)($n));

		$start = (int)($p - $range);
		if ($start < 1)
			$start = 1;
			
		$stop = (int)($p + $range);
		if ($stop > $pages_nb)
			$stop = (int)($pages_nb);
			
		$context->smarty->assign('nb_products', $nbProducts);
		$pagination_infos = array(
			'pages_nb' => (int)($pages_nb),
			'p' => (int)$p,
			'n' => (int)$n,
			'range' => (int)$range,
			'start' => (int)$start,
			'stop' => (int)$stop,
			'nArray' => $nArray = (int)Configuration::get('PS_PRODUCTS_PER_PAGE') != 10 ? array((int)Configuration::get('PS_PRODUCTS_PER_PAGE'), 10, 20, 50) : array(10, 20, 50)
		);
		$context->smarty->assign($pagination_infos);
		
		$context->smarty->assign('products', $products);
		
		/* We are sending an array in jSon to the .js controller, it will update both the filters and the products zones */
		return Tools::jsonEncode(array(
			'filtersBlock' => $this->generateFiltersBlock($selectedFilters),
			'productList' => $context->smarty->fetch(_PS_THEME_DIR_.'product-list.tpl'),
			'pagination' => $context->smarty->fetch(_PS_THEME_DIR_.'pagination.tpl')
		));
	}
	
	public function rebuildLayeredStructure()
	{
		@set_time_limit(0);
		
		/* Set memory limit to 128M only if current is lower */
		$memory_limit = ini_get('memory_limit');
		if (substr($memory_limit,-1) != 'G' AND ((substr($memory_limit,-1) == 'M' AND substr($memory_limit,0,-1) < 128) OR is_numeric($memory_limit) AND (intval($memory_limit) < 131072)))
			@ini_set('memory_limit','128M');

		/* Delete and re-create the layered categories table */
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_category');
		Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'layered_category` (
		`id_layered_category` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_category` INT(10) UNSIGNED NOT NULL,
		`id_value` INT(10) UNSIGNED NULL DEFAULT \'0\',
		`type` ENUM(\'category\',\'id_feature\',\'id_attribute_group\',\'quantity\',\'condition\',\'manufacturer\',\'weight\') NOT NULL,
		`position` INT(10) UNSIGNED NOT NULL,
		PRIMARY KEY (`id_layered_category`),
		KEY `id_category` (`id_category`,`type`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;'); /* MyISAM + latin1 = Smaller/faster */
		
		Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'layered_filter` (
		`id_layered_filter` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`name` VARCHAR(64) NOT NULL,
		`filters` TEXT NULL,
		`n_categories` INT(10) UNSIGNED NOT NULL,
		`date_add` DATETIME NOT NULL)');
	}
	
	public function rebuildLayeredCache($productsIds = array(), $categoriesIds = array())
	{
		@set_time_limit(0);
		
		/* Set memory limit to 128M only if current is lower */
		$memory_limit = ini_get('memory_limit');
		if (substr($memory_limit,-1) != 'G' AND ((substr($memory_limit,-1) == 'M' AND substr($memory_limit,0,-1) < 128) OR is_numeric($memory_limit) AND (intval($memory_limit) < 131072)))
			@ini_set('memory_limit','128M');

		$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
		$nCategories = array();
		$doneCategories = array();
		
		$attributeGroups = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT a.id_attribute, a.id_attribute_group
		FROM '._DB_PREFIX_.'attribute a
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_attribute = a.id_attribute)
		LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = pac.id_product_attribute)
		LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = pa.id_product)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
		WHERE c.active = 1'.(sizeof($categoriesIds) ? ' AND cp.id_category IN ('.implode(',', $categoriesIds).')' : '').' AND p.active = 1'.(sizeof($productsIds) ? ' AND p.id_product IN ('.implode(',', $productsIds).')' : ''), false);
		
		$attributeGroupsById = array();
		while ($row = $db->nextRow($attributeGroups))
			$attributeGroupsById[(int)$row['id_attribute']] = (int)$row['id_attribute_group'];
			
		$features = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT fv.id_feature_value, fv.id_feature
		FROM '._DB_PREFIX_.'feature_value fv
		LEFT JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_feature_value = fv.id_feature_value)
		LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = fp.id_product)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
		WHERE (fv.custom IS NULL OR fv.custom = 0) AND c.active = 1'.(sizeof($categoriesIds) ? ' AND cp.id_category IN ('.implode(',', $categoriesIds).')' : '').' AND p.active = 1'.(sizeof($productsIds) ? ' AND p.id_product IN ('.implode(',', $productsIds).')' : ''), false);
		
		$featuresById = array();
		while ($row = $db->nextRow($features))
			$featuresById[(int)$row['id_feature_value']] = (int)$row['id_feature'];
		
		$result = $db->ExecuteS('
		SELECT p.id_product, GROUP_CONCAT(DISTINCT fv.id_feature_value) features, GROUP_CONCAT(DISTINCT cp.id_category) categories, GROUP_CONCAT(DISTINCT pac.id_attribute) attributes
		FROM '._DB_PREFIX_.'product p
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
		LEFT JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature_value = fp.id_feature_value)
		LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_product_attribute = pa.id_product_attribute)
		WHERE c.active = 1'.(sizeof($categoriesIds) ? ' AND cp.id_category IN ('.implode(',', $categoriesIds).')' : '').' AND p.active = 1'.(sizeof($productsIds) ? ' AND p.id_product IN ('.implode(',', $productsIds).')' : '').' AND (fv.custom IS NULL OR fv.custom = 0)
		GROUP BY p.id_product', false);
		
		while ($product = $db->nextRow($result))
		{
			$a = $c = $f = array();
			if (!empty($product['attributes']))
				$a = array_flip(explode(',', $product['attributes']));
			if (!empty($product['categories']))
				$c = array_flip(explode(',', $product['categories']));
			if (!empty($product['features']))
				$f = array_flip(explode(',', $product['features']));

			$queryCategory = 'INSERT INTO '._DB_PREFIX_.'layered_category (id_category, id_value, type, position) VALUES ';
			$toInsert = false;
			foreach ($c AS $id_category => $category)
			{
				if (!isset($nCategories[(int)$id_category]))
					$nCategories[(int)$id_category] = 1;
				if (!isset($doneCategories[(int)$id_category]['cat']))
				{
					$doneCategories[(int)$id_category]['cat'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'category\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
				foreach ($a AS $kAttribute => $attribute)
					if (!isset($doneCategories[(int)$id_category]['a'.(int)$attributeGroupsById[(int)$kAttribute]]))
					{
						$doneCategories[(int)$id_category]['a'.(int)$attributeGroupsById[(int)$kAttribute]] = true;
						$queryCategory .= '('.(int)$id_category.','.(int)$attributeGroupsById[(int)$kAttribute].',\'id_attribute_group\','.(int)$nCategories[(int)$id_category]++.'),';
						$toInsert = true;
					}
				foreach ($f AS $kFeature => $feature)
					if (!isset($doneCategories[(int)$id_category]['f'.(int)$featuresById[(int)$kFeature]]))
					{
						$doneCategories[(int)$id_category]['f'.(int)$featuresById[(int)$kFeature]] = true;
						$queryCategory .= '('.(int)$id_category.','.(int)$featuresById[(int)$kFeature].',\'id_feature\','.(int)$nCategories[(int)$id_category]++.'),';
						$toInsert = true;
					}
				if (!isset($doneCategories[(int)$id_category]['q']))
				{
					$doneCategories[(int)$id_category]['q'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'quantity\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
				if (!isset($doneCategories[(int)$id_category]['m']))
				{
					$doneCategories[(int)$id_category]['m'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'manufacturer\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
				if (!isset($doneCategories[(int)$id_category]['c']))
				{
					$doneCategories[(int)$id_category]['c'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'condition\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
				if (!isset($doneCategories[(int)$id_category]['w']))
				{
					$doneCategories[(int)$id_category]['w'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'weight\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
				/* Filter by price (dev in progress)
if (!isset($doneCategories[(int)$id_category]['p']))
				{
					$doneCategories[(int)$id_category]['p'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'price\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
*/
			}
			if ($toInsert)
				Db::getInstance()->Execute(rtrim($queryCategory, ','));
		}
	}
	
	function filterProducts($products, $selectedFilters, $excludeType = false)
	{
		$productsToKeep = array();
		$filterByLetter = array('id_attribute_group' => 'a', 'id_feature' => 'f', 'category' => 'c', 'manufacturer' => 'id_manufacturer',
								'quantity' => 'quantity', 'condition' => 'condition', 'weight' => 'weight', 'price' => 'price');
		
		foreach ($selectedFilters AS $type => $filters)
		{		
			if ($type == $excludeType OR !sizeof($filters))
				continue;
			else
			{			
				$type = preg_match('/^(.*[^_0-9])/', $type, $res);
				$type = $res[1];
				
				switch ($type)
				{
					case 'category':
						foreach ($products AS $k => $product)
							if ($filter = Tools::getValue('id_category_layered'))
								$productsToKeep[] = (int)$k;
						//don't break me
					case 'id_attribute_group':
					case 'id_feature':
						foreach ($products AS $k => $product)
							foreach ($filters AS $filter)
								if (in_array($filter, $product[$filterByLetter[$type]]))
									$productsToKeep[] = (int)$k;
						break;

					case 'manufacturer':
					case 'condition':
					case 'quantity':
						foreach ($products AS $k => $product)
							foreach ($filters AS $filter)
								if ($product[$filterByLetter[$type]] == $filter)
									$productsToKeep[] = (int)$k;
						break;

					case 'weight':
					case 'price':
						$min = $filters[0];
						$max = $filters[1]; 
						foreach ($products AS $k => $product)
							if((float)$min <= (float)$product[$filterByLetter[$type]] AND (float)$product[$filterByLetter[$type]] <= (float)$max)
								$productsToKeep[] = (int)$k;
						break;
				}
				
				foreach ($products AS $k => $product)
					if (!in_array($k, $productsToKeep))
						unset($products[(int)$k]);
				$productsToKeep = array();
			}
		}
		return $products;
	}

	private function getShortLink($share_url) {
	
		$return = '';
		if (extension_loaded('curl'))
		{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://api.bitly.com/v3/shorten");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPGET, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'login='.Configuration::get('PS_LAYERED_BITLY_USERNAME').'&apiKey='.Configuration::get('PS_LAYERED_BITLY_API_KEY').'&longUrl='.urlencode($share_url).'&format=txt');
		$return = curl_exec($ch);
		}
		if ($return != 'INVALID_LOGIN' AND $return != 'INVALID_APIKEY' AND extension_loaded('curl'))
			return $return;
		else
			return $share_url;
	}
}