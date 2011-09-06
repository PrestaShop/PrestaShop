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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
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
			Configuration::updateValue('PS_LAYERED_HIDE_0_VALUES', 0);
			Configuration::updateValue('PS_LAYERED_SHOW_QTIES', 1);
			
			$this->rebuildLayeredStructure();
			$this->rebuildLayeredCache();
		}
		
		self::_installPriceIndexTable();

		return $result;
	}

	public function uninstall()
	{
		/* Delete all configurations */
		Configuration::deleteByName('PS_LAYERED_HIDE_0_VALUES');
		Configuration::deleteByName('PS_LAYERED_SHOW_QTIES');
		
		return parent::uninstall();
	}
	
	private function _installPriceIndexTable()
	{
		Db::getInstance()->execute('
		DROP TABLE IF EXISTS  `'._DB_PREFIX_.'price_static_index`;
		');
		Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'price_static_index` (
			`id_product` INT  NOT NULL,
			`id_currency` INT NOT NULL,
			`price_min` INT NOT NULL,
			`price_max` INT NOT NULL,
			PRIMARY KEY (`id_product`, `id_currency`),
			INDEX `id_currency` (`id_currency`),
			INDEX `price_min` (`price_min`),
			INDEX `price_max` (`price_max`)
		)
		ENGINE = '._MYSQL_ENGINE_.';
		');
	}
	
	/*
	 * $cursor $cursor in order to restart indexing from the last state
	 */
	public static function fullIndexProcess($cursor = 0, $ajax = false, $smart = false)
	{
		if ($cursor == 0 && !$smart)
			self::_installPriceIndexTable();
		
		return self::_indexer($cursor, true, $ajax, $smart);
	}
	
	/*
	 * $cursor $cursor in order to restart indexing from the last state
	 */
	public static function indexProcess($cursor = 0, $ajax = false)
	{
		return self::_indexer($cursor, false, $ajax);
	}
	
	private static function _indexer($cursor = null, $full = false, $ajax = false, $smart = false)
	{
		if ($full)
			$nbProducts = (int)Db::getInstance()->getValue('SELECT count(*) FROM '._DB_PREFIX_.'product WHERE `active` = 1');
		else
			$nbProducts = (int)Db::getInstance()->getValue(
			'SELECT COUNT(*) FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN  `'._DB_PREFIX_.'price_static_index` psi ON (psi.id_product = p .id_product)
			WHERE `active` = 1 AND psi.id_product IS NULL');
		
		$maxExecutionTime = ini_get('max_execution_time') * 0.9; // 90% of safety margin
		if ($maxExecutionTime > 5)
			$maxExecutionTime = 5;
		
		$startTime = microtime(true);
		
		do
		{
			$cursor = (int)self::_index((int)$cursor, $full, $smart);
			$timeElapsed = microtime(true) - $startTime;
		}
		while($cursor < $nbProducts AND (Tools::getMemoryLimit() * 0.9) > memory_get_peak_usage() AND $timeElapsed < $maxExecutionTime);
		
		if (($nbProducts > 0 AND !$full OR $cursor < $nbProducts AND $full) AND !$ajax)
		{
			if (!Tools::file_get_contents(Tools::getProtocol().Tools::getHttpHost().'/modules/blocklayered/blocklayered-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'&cursor='.(int)$cursor.'&full='.(int)$full))
				self::_indexer((int)$cursor, (int)$full);
			return $cursor;
		}
		if ($ajax AND $nbProducts > 0 AND $cursor < $nbProducts AND $full)
			return '{"cursor": '.$cursor.', "count": '.($nbProducts - $cursor).'}';
		elseif ($ajax AND $nbProducts > 0 AND !$full)
			return '{"cursor": '.$cursor.', "count": '.($nbProducts).'}';
		else
		{
			Configuration::updateValue('PS_LAYERED_INDEXED', 1);
			if ($ajax)
				return '{"result": "ok"}';
			else
				return -1;
		}
	}
	
	/*
	 * $cursor $cursor in order to restart indexing from the last state
	 */
	private static function _index($cursor, $full = false, $smart = false)
	{
		static $length = 100; // Nb of products to index
		
		if (is_null($cursor))
			$cursor = 0;
		
		if ($full)
			$query = '
			SELECT id_product
			FROM `'._DB_PREFIX_.'product`
			WHERE `active` = 1
			ORDER by id_product LIMIT '.(int)$cursor.','.(int)$length;
		else
			$query = '
			SELECT p.id_product
			FROM `'._DB_PREFIX_.'product` as p
			LEFT JOIN  `'._DB_PREFIX_.'price_static_index` psi ON (psi.id_product = p.id_product)
			WHERE `active` = 1 AND psi.id_product is null
			ORDER by id_product LIMIT 0,'.(int)$length;
		
		foreach (Db::getInstance()->executeS($query) as $product)
			self::indexProduct((int)$product['id_product'], ($smart AND $full));

		return (int)($cursor + $length);
	}
	
	public static function indexProduct($idProduct, $smart = true)
	{
		static $groups = null;

		if (is_null($groups))
			$groups = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_group FROM `'._DB_PREFIX_.'group_reduction`');
		
		static $currencyList = null;
		if (is_null($currencyList))
			$currencyList = Currency::getCurrencies();
		
		$minPrice = array();
		$maxPrice = array();
		
		
		if ($smart)
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'price_static_index` WHERE `id_product` = '.(int)$idProduct);
		
		$maxTaxRate = Db::getInstance()->getValue('
		SELECT max(t.rate) as max_rate
		FROM `'._DB_PREFIX_.'product` as p
		LEFT JOIN `'._DB_PREFIX_.'tax_rules_group` trg ON (trg.id_tax_rules_group = p.id_tax_rules_group)
		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (tr.id_tax_rules_group = trg.id_tax_rules_group)
		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.id_tax = tr.id_tax AND t.active = 1)
		WHERE id_product = '.(int)$idProduct.'
		GROUP BY id_product;');
		
		$productMinPrices = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT id_shop, id_currency, id_country, id_group, from_quantity
		FROM `'._DB_PREFIX_.'specific_price`
		WHERE id_product = '.(int)$idProduct);
		
		// Get min price
		foreach ($productMinPrices as $specificPrice)
			foreach ($currencyList as $currency)
			{
				if ($specificPrice['id_currency'] AND $specificPrice['id_currency'] != $currency['id_currency'])
					continue;
				$price = Product::priceCalculation((($specificPrice['id_shop'] == 0) ? null : (int)$specificPrice['id_shop']), (int)$idProduct,
					null, (($specificPrice['id_country'] == 0) ? null : $specificPrice['id_country']), null, null,
					$currency['id_currency'], (($specificPrice['id_group'] == 0) ? null : $specificPrice['id_group']),
					$specificPrice['from_quantity'], false, true, false, true, true, $specificPriceOutput, true);
				
				if (!isset($maxPrice[$currency['id_currency']]))
					$maxPrice[$currency['id_currency']] = 0;
				if (!isset($minPrice[$currency['id_currency']]))
					$minPrice[$currency['id_currency']] = null;
				if ($price > $maxPrice[$currency['id_currency']])
					$maxPrice[$currency['id_currency']] = $price;
				if ($price == 0)
					continue;
				if (is_null($minPrice[$currency['id_currency']]) || $price < $minPrice[$currency['id_currency']])
					$minPrice[$currency['id_currency']] = $price;
			}
		
		foreach ($groups as $group)
			foreach ($currencyList as $currency)
			{
				$price = Product::priceCalculation(null, (int)$idProduct, null, null, null, null, (int)$currency['id_currency'], (int)$group['id_group'],
					null, false, true, false, true, true, $specificPriceOutput, true);
					
				if (!isset($maxPrice[$currency['id_currency']]))
					$maxPrice[$currency['id_currency']] = 0;
				if (!isset($minPrice[$currency['id_currency']]))
					$minPrice[$currency['id_currency']] = null;
				if ($price > $maxPrice[$currency['id_currency']])
					$maxPrice[$currency['id_currency']] = $price;
				if ($price == 0)
					continue;
				if (is_null($minPrice[$currency['id_currency']]) || $price < $minPrice[$currency['id_currency']])
					$minPrice[$currency['id_currency']] = $price;
			}
		
		foreach ($currencyList as $currency)
			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'price_static_index` (id_product, id_currency, price_min, price_max)
			VALUES ('.(int)$idProduct.', '.(int)$currency['id_currency'].', '.(int)$minPrice[$currency['id_currency']].', '.(int)$maxPrice[$currency['id_currency']].')');
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
		Tools::addJS(($this->_path).'blocklayered.js');
		Tools::addJS(_PS_JS_DIR_.'jquery/jquery-ui-1.8.10.custom.min.js');
		Tools::addCSS(_PS_CSS_DIR_.'jquery-ui-1.8.10.custom.css', 'all');
		Tools::addCSS(($this->_path).'blocklayered.css', 'all');		
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
		global $cookie;

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
				$categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT id_category FROM '._DB_PREFIX_.'category');
				foreach ($categories AS $category)
					$_POST['categoryBox'][] = (int)$category['id_category'];
			}
			
			if (sizeof($_POST['categoryBox']))
			{
				/* Clean categoryBox before use */
				if (isset($_POST['categoryBox']) AND is_array($_POST['categoryBox']))
					foreach ($_POST['categoryBox'] AS &$value)
						$value = (int)$value;
				
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
							elseif ($key == 'layered_selection_price_slider')
								$sqlToInsert .= '('.(int)$id_category_layered.',NULL,\'price\','.(int)$n.'),';
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
			if (!sizeof($errors))
			{
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
						foreach ($errors AS $error)
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
		<h2>'.$this->l('Layered navigation').'</h2>';
		$html .= '
		<fieldset class="width4">
			<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Indexes and caches').'</legend>
			<span id="indexing-warning" style="display: none; color:red; font-weight: bold">'.$this->l('Indexing are in progress. Please don\'t leave this page').'<br/><br/></span>
		';
		if(!Configuration::get('PS_LAYERED_INDEXED'))
			$html .= '
			<script type="text/javascript">
			$(document).ready(function() {
				$(\'#full-index\').click();
			});
			</script>';
		$html .= '
			- <a class="bold ajaxcall" href="'.Tools::getProtocol().Tools::getHttpHost().__PS_BASE_URI__.'modules/blocklayered/blocklayered-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'),0,10).'">'.$this->l('Index all missing products.').'</a>
			<br />
			- <a class="bold ajaxcall" id="full-index" href="'.Tools::getProtocol().Tools::getHttpHost().__PS_BASE_URI__.'modules/blocklayered/blocklayered-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'),0,10).'&full=1">'.$this->l('Re-build entire index.').'</a>
			<br />
			<br />
			'.$this->l('You can set a cron job that will re-build your index using the following URL: ').Tools::getProtocol().Tools::getHttpHost().__PS_BASE_URI__.'modules/blocklayered/blocklayered-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'),0,10).'&full=1
			<br />
			'.$this->l('A full re-index process must be done each time products are modified. A nightly rebuild is recomanded.').'
			<script type="text/javascript">
				$(\'.ajaxcall\').each(function(it, elm) {
					$(elm).click(function() {
						if (this.cursor == undefined)
							this.cursor = 0;
						
						if (this.legend == undefined)
							this.legend = $(this).html();
							
						if (this.running == undefined)
							this.running = false;
						
						if (this.running == true)
							return false;
						
						this.running = true;
						
						if ($(this).html() == this.legend)
						{
							$(this).html(this.legend+\' (in progress)\');
							$(\'#indexing-warning\').show();
						}
							
						
						$.ajax({
							url: this.href+\'&ajax=1&cursor=\'+this.cursor,
							context: this,
							dataType: \'json\',
							success: function(res)
							{
								this.running = false;
								if (res.result)
								{
									this.cursor = 0;
									$(\'#indexing-warning\').hide();
									$(this).html(this.legend+\' (finished)\');
									return;
								}
								this.cursor = parseInt(res.cursor);
								$(this).html(this.legend+\' (in progress, \'+res.count+\' products to index)\');
								this.click();
							}
						});
						return false;
					});
				});
			</script>
		</fieldset>
		<br />';
		
		$html .= '
		<fieldset class="width4">
			<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Existing filters templates').'</legend>';
	
		$filtersTemplates = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM '._DB_PREFIX_.'layered_filter ORDER BY date_add DESC');
		if (sizeof($filtersTemplates))
		{		
			$html .= '<p>'.sizeof($filtersTemplates).' '.$this->l('filters templates are configured:').'</p>
			<table id="table-filter-templates" class="table" style="width: 700px;">
				<tr>
					<th>'.$this->l('ID').'</th>
					<th>'.$this->l('Name').'</th>
					<th>'.$this->l('Categories').'</th>
					<th>'.$this->l('Created on').'</th>
					<th>'.$this->l('Actions').'</th>
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
					<td>'.Tools::displayDate($filtersTemplate['date_add'], (int)$cookie->id_lang, true).'</td>
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
			foreach (Helper::$translationsKeysForAdminCategorieTree AS $key)
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
							url: \''.__PS_BASE_URI__.'\' + \'modules/blocklayered/blocklayered-ajax-back.php\',
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
							},
							\'onComplete\': function() {
								$(\'#categories-treeview li#1\').removeClass(\'static\');
								$(\'#categories-treeview li#1 span\').trigger(\'click\');
								$(\'#categories-treeview li#1\').children(\'div\').remove();
								$(\'#categories-treeview li#1\').
									removeClass(\'collapsable lastCollapsable\').
									addClass(\'last static\');
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
				</table>
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
					elseif (in_array($res[1], array('category', 'manufacturer')))
					{
						if (!isset($selectedFilters[$res[1].($id_key ? '_'.$id_key : '')]))
							$selectedFilters[$res[1].($id_key ? '_'.$id_key : '')] = array();
						$selectedFilters[$res[1].($id_key ? '_'.$id_key : '')][] = (int)$value;
					}
					elseif (in_array($res[1], array('id_attribute_group', 'id_feature')))
					{
						if (!isset($selectedFilters[$res[1]]))
							$selectedFilters[$res[1]] = array();
						$selectedFilters[$res[1]][$value] = (int)$value;
					}
					elseif ($res[1] == 'weight')
						$selectedFilters[$res[1]] = $tmpTab;
					elseif ($res[1] == 'price')
						$selectedFilters[$res[1]] = $tmpTab;
				}
			}

		return $selectedFilters;
	}

	public function getProductByFilters($selectedFilters = array())
	{
		global $cookie;

		if (!empty($this->products))
			return $this->products;

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
					$queryFilters .= ' AND p.id_product IN (SELECT id_product FROM '._DB_PREFIX_.'feature_product fp WHERE ';
					foreach ($filterValues AS $filterValue)
						$queryFilters .= 'fp.`id_feature_value` = '.(int)$filterValue.' OR ';
					$queryFilters = rtrim($queryFilters, 'OR ').')';
				break;

				case 'id_attribute_group':
					$queryFilters .= ' AND p.id_product IN (SELECT pa.`id_product`
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
					if ($selectedFilters['weight'][0] != 0 || $selectedFilters['weight'][1] != 0)
						$queryFilters .= ' AND p.`weight` BETWEEN '.(float)($selectedFilters['weight'][0] - 0.001).' AND '.(float)($selectedFilters['weight'][1] + 0.001);

				case 'price':
					if (isset($selectedFilters['price']))
					{
						if ($selectedFilters['price'][0] != 0 || $selectedFilters['price'][1] != 0)
						{
							$priceFilter = array();
							$priceFilter['min'] = (float)($selectedFilters['price'][0]);
							$priceFilter['max'] = (float)($selectedFilters['price'][1]);
						}
					}
					else
						$priceFilter = false;
				break;
			}
		}
		
		$idCurrency = Currency::getCurrent()->id;
		$priceFilterQueryIn = ''; // All products with price range between price filters limits
		$priceFilterQueryOut = ''; // All products with a price filters limit on it price range
		if (isset($priceFilter) && $priceFilter)
		{
			$priceFilterQueryIn = 'INNER JOIN `'._DB_PREFIX_.'price_static_index` as psi
			ON psi.price_min >= '.(int)$priceFilter['min'].'
				AND psi.price_max <= '.(int)$priceFilter['max'].'
				AND psi.`id_product` = p.`id_product`
				AND psi.`id_currency` = '.(int)$idCurrency;
			
			$priceFilterQueryOut = 'INNER JOIN `'._DB_PREFIX_.'price_static_index` as psi
			ON 
				((psi.price_min < '.(int)$priceFilter['min'].' AND psi.price_max > '.(int)$priceFilter['min'].')
				OR
				(psi.price_max > '.(int)$priceFilter['max'].' AND psi.price_min < '.(int)$priceFilter['max'].'))
				AND psi.`id_product` = p.`id_product`
				AND psi.`id_currency` = '.(int)$idCurrency;
		}
		
		$allProductsOut = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT p.`id_product` id_product
		FROM `'._DB_PREFIX_.'product` p
		'.$priceFilterQueryOut.'
		WHERE 1 '.$queryFilters);
		
		$allProductsIn = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT p.`id_product` id_product
		FROM `'._DB_PREFIX_.'product` p
		'.$priceFilterQueryIn.'
		WHERE 1 '.$queryFilters);

		$productIdList = array();
		
		foreach ($allProductsIn as $product)
			$productIdList[] = (int)$product['id_product'];

		foreach ($allProductsOut as $product)
			if (isset($priceFilter) AND $priceFilter)
			{
				$price = Product::getPriceStatic($product['id_product']);
				if ($price < $priceFilter['min'] OR $price > $priceFilter['max'])
					continue;
				$productIdList[] = (int)$product['id_product'];
			}
		$this->nbr_products = count($productIdList);
		
		if ($this->nbr_products == 0)
			$this->products = array();
		else {
			$n = (int)Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'));
			$this->products = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT p.id_product, p.on_sale, p.out_of_stock, p.available_for_order, p.quantity, p.minimal_quantity, p.id_category_default, p.customizable, p.show_price, p.`weight`,
			p.ean13, pl.available_later, pl.description_short, pl.link_rewrite, pl.name, i.id_image, il.legend,  m.name manufacturer_name, p.condition, p.id_manufacturer,
			DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new
			FROM `'._DB_PREFIX_.'category_product` cp
			LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
			LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
			LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product)
			LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = p.id_product AND i.cover = 1)
			LEFT JOIN '._DB_PREFIX_.'image_lang il ON (i.id_image = il.id_image AND il.id_lang = '.(int)($cookie->id_lang).')
			LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
			WHERE p.`active` = 1 AND c.nleft >= '.(int)$parent->nleft.' AND c.nright <= '.(int)$parent->nright.' AND pl.id_lang = '.(int)$cookie->id_lang.' AND p.id_product IN ('.implode(',', $productIdList).')'
			.' ORDER BY '.Tools::getProductsOrder('by', Tools::getValue('orderby'), true).' '.Tools::getProductsOrder('way', Tools::getValue('orderway')).
			' LIMIT '.(((int)Tools::getValue('p', 1) - 1) * $n.','.$n));
		}
		
		return $this->products;
	}
	public function generateFiltersBlockOld($selectedFilters = array())
	{
		global $smarty, $link, $cookie;

		/* If the current category isn't defined of if it's homepage, we have nothing to display */
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return;

		/* First we need to get all subcategories of current category */
		$category = new Category((int)$id_parent);

		$groups = FrontController::getCurrentCustomerGroups();

		$subCategories = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.id_category, c.id_parent, cl.name
		FROM '._DB_PREFIX_.'category c
		LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category)
		LEFT JOIN '._DB_PREFIX_.'category_group cg ON (cg.id_category = c.id_category)
		WHERE c.nleft > '.(int)$category->nleft.' and c.nright <= '.(int)$category->nright.' AND c.active = 1 AND c.id_parent = '.(int)$category->id.' AND cl.id_lang = '.(int)$cookie->id_lang.'
		AND cg.id_group '.pSQL(sizeof($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').'
		GROUP BY c.id_category
		ORDER BY c.position ASC');

		$whereC = ' cp.`id_category` = '.(int)$id_parent.' OR ';
		foreach ($subCategories AS $subcategory)
				$whereC .= ' cp.`id_category` = '.(int)$subcategory['id_category'].' OR ';

		$whereC = rtrim($whereC, 'OR ').')';
		$productsSQL = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT p.`id_product`, p.`condition`, p.`id_manufacturer`, p.`quantity`, p.`weight`, psi.price_max, psi.price_min,
		(SELECT GROUP_CONCAT(`id_category`) FROM `'._DB_PREFIX_.'category_product` cp WHERE cp.`id_product` = p.`id_product`) ids_cat,
			(SELECT GROUP_CONCAT(`id_feature_value`) FROM `'._DB_PREFIX_.'feature_product` fp WHERE fp.`id_product` = p.`id_product`) ids_feat,
			(SELECT GROUP_CONCAT(DISTINCT(pac.`id_attribute`)) 
				FROM `'._DB_PREFIX_.'product_attribute_combination` pac
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.`id_product_attribute` = pac.`id_product_attribute`) 
				WHERE pa.`id_product` = p.`id_product`) ids_attr
		FROM '._DB_PREFIX_.'product p 
		LEFT JOIN  `'._DB_PREFIX_.'price_static_index` as psi
			ON psi.`id_product` = p.`id_product` AND psi.`id_currency` = '.(int)Currency::getCurrent()->id.'
		WHERE p.`active` = 1 AND p.`id_product` IN (SELECT id_product FROM `'._DB_PREFIX_.'category_product` cp WHERE'.$whereC, false);
		
		$products = array();
		$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
		$weight = array();
		$price = array();
		$productIdList = array();
		$productList = array();
		while ($product = $db->nextRow($productsSQL))
		{
			$row = array();
			foreach ($product AS $key => $value)
			{
				if ($key == 'ids_feat')
					$row['f'] = explode(',', $value);
				if ($key == 'ids_attr')
					$row['a'] = explode(',', $value);
				if ($key == 'ids_cat')
					$row['c'] = explode(',', $value);
				if ($key == 'weight')
					$weight[] = $value;
			}

			$row['id_manufacturer'] = (int)$product['id_manufacturer'];
			$row['quantity'] = (bool)$product['quantity'];
			$row['condition'] = $product['condition'];
			$row['weight'] = $product['weight'];
			$row['price_min'] = $product['price_min'];
			$row['price_max'] = $product['price_max'];
			$price[] = $product['price_min'];
			$price[] = $product['price_max'];
			
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
					WHERE (fv.custom IS NULL OR fv.custom = 0) AND fv.id_feature = '.(int)$filterBlocks[(int)$filter['position']]['id_key'].' AND fvl.id_lang = '.(int)$cookie->id_lang);
					break;

				case 'id_attribute_group':
					$a[] = (int)$filter['id_value'];
					
					$filterBlocks[(int)$filter['position']]['SQLvalues'] = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT al.id_attribute, al.name, a.color
					FROM '._DB_PREFIX_.'attribute a
					LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = a.id_attribute)
					WHERE a.id_attribute_group = '.(int)$filterBlocks[(int)$filter['position']]['id_key'].' AND al.id_lang = '.(int)$cookie->id_lang);
					break;
			}
		}

		/* Get the feature block names & values */
		if (sizeof($f))
		{
			$fNames = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT id_feature, name
			FROM '._DB_PREFIX_.'feature_lang
			WHERE id_lang = '.(int)$cookie->id_lang.' AND id_feature IN ('.implode(',', $f).')');
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
			WHERE agl.id_lang = '.(int)$cookie->id_lang.' AND ag.id_attribute_group IN ('.implode(',', $a).')');

			$aNameByID = $colorGroups = array();
			foreach ($aNames AS $aName)
			{
				$aNameByID[(int)$aName['id_attribute_group']] = $aName['public_name'];
				if ($aName['is_color_group'])
					$colorGroups[(int)$aName['id_attribute_group']] = true;
			}
		}

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
						if (in_array($value['id_attribute'], $product['a']))
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
				if (!empty($productKeys))
					$man = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT DISTINCT(p.id_manufacturer), m.name
					FROM '._DB_PREFIX_.'product p			
					LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
					WHERE p.id_product IN ('.implode(',', $productKeys).') AND p.id_manufacturer != 0');

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
					$filterBlock['unit'] = Configuration::get('PS_WEIGHT_UNIT');
				}
				else
					unset($selectedFilters['weight']);
			}
			elseif ($filterBlock['type_lite'] == 'price')
			{
				if (!empty($price) && max($price) != min($price))
				{
					$filterBlock['name'] = $this->l('Price');
					$filterBlock['slider'] = true;
					$filterBlock['max'] = round(max($price));
					$filterBlock['min'] = round(min($price));
					if (isset($selectedFilters['price']))
						$filterBlock['values'] = array($selectedFilters['price'][0], $selectedFilters['price'][1]);
					else
						$filterBlock['values'] = array(round(min($price)), ceil(max($price)));
					$filterBlock['unit'] = Currency::getCurrent()->sign;
				}
				else
					unset($selectedFilters['price']);
			}
		}

		$nFilters = 0;
		foreach ($selectedFilters AS $filters)
			$nFilters += sizeof($filters);
		
		$params = '?';
		foreach ($_GET AS $key => $val)
			$params .= $key.'='.$val.'&';
		
		$smarty->assign(array(
		'layered_show_qties' => (int)Configuration::get('PS_LAYERED_SHOW_QTIES'),
		'id_category_layered' => (int)$id_parent,
		'selected_filters' => $selectedFilters,
		'n_filters' => (int)$nFilters,
		'nbr_filterBlocks' => sizeof($filterBlocks),
		'filters' => $filterBlocks));
		return $this->display(__FILE__, 'blocklayered.tpl');
	}
	
	
	public function generateFiltersBlockNew($selectedFilters = array())
	{
		global $cookie, $smarty;
		
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return;
			
		/* Get the filters for the current category */
		$filters = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM '._DB_PREFIX_.'layered_category WHERE id_category = '.(int)$id_parent.' ORDER BY position ASC');
		// Remove all empty selected filters
		foreach ($selectedFilters as $key => $value)
			switch($key)
			{
				case 'price':
				case 'weight':
					if ($value[0] == '' && $value[1] == '' || $value[0] == 0 && $value[1] == 0)
						unset($selectedFilters[$key]);
					break;
				default:
					if ($value == '')
						unset($selectedFilters[$key]);
					break;
			}
		
		$filterBlocks = array();
		foreach ($filters as $filter)
		{
			$sqlQuery = array('select' => '', 'from' => '', 'join' => '', 'where' => '', 'group' => '');
			switch($filter['type'])
			{
				// conditions + quantities + weight + price
				case 'price': case 'weight': case 'condition': case 'quantity':
					$sqlQuery['select'] = '
					SELECT p.`id_product`, p.`condition`, p.`id_manufacturer`, p.`quantity`, p.`weight`,
					(SELECT GROUP_CONCAT(`id_category`) FROM `'._DB_PREFIX_.'category_product` cp WHERE cp.`id_product` = p.`id_product`) ids_cat,
					(SELECT GROUP_CONCAT(`id_feature_value`) FROM `'._DB_PREFIX_.'feature_product` fp WHERE fp.`id_product` = p.`id_product`) ids_feat,
					(SELECT GROUP_CONCAT(DISTINCT(pac.`id_attribute`)) 
					FROM `'._DB_PREFIX_.'product_attribute_combination` pac
					LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.`id_product_attribute` = pac.`id_product_attribute`) 
					WHERE pa.`id_product` = p.`id_product`) ids_attr';
					$sqlQuery['from'] = '
					FROM '._DB_PREFIX_.'product p ';
					$sqlQuery['join'] = '
					INNER JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
					INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category AND c.id_category = '.(int)$id_parent.') ';
					$sqlQuery['where'] = 'WHERE p.`active` = 1 ';
					break;
				case 'manufacturer':
					$sqlQuery['select'] = 'SELECT m.name, count(p.id_product) AS nbr, m.id_manufacturer ';
					$sqlQuery['from'] = '
					FROM `'._DB_PREFIX_.'category_product` AS cp
					INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = cp.id_product AND p.active = 1)
					INNER JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer) ';
					$sqlQuery['where'] = '
					WHERE cp.`id_category` = '.(int)$id_parent.' ';
					$sqlQuery['group'] = ' GROUP BY p.id_manufacturer ';
					break;
				case 'id_attribute_group':// attribute group
					$sqlQuery['select'] = '
					SELECT COUNT(tmp.id_attribute) nbr, tmp.id_attribute_group, tmp.color, tmp.name AS name, agl.public_name AS attributeName,
					tmp.id_attribute AS id_attribute, a.is_color_group FROM (SELECT p.id_product, pac.id_attribute, a.color, al.name, a.id_attribute_group';
					$sqlQuery['from'] = '
					FROM '._DB_PREFIX_.'product_attribute_combination pac
					LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = pac.id_product_attribute)
					LEFT JOIN '._DB_PREFIX_.'product p ON (pa.id_product = p.id_product)
					LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = pa.id_product)
					INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category AND c.id_category = '.(int)$id_parent.')
					LEFT JOIN '._DB_PREFIX_.'attribute a ON (a.id_attribute = pac.id_attribute)
					LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = pac.id_attribute AND al.id_lang = '.(int)$cookie->id_lang.') ';
					$sqlQuery['group'] = '
					WHERE p.`active` = 1 AND a.id_attribute_group = '.(int)$filter['id_value'].'
					GROUP BY pac.id_attribute, p.id_product) tmp
					LEFT JOIN '._DB_PREFIX_.'attribute_group_lang al ON (al.id_attribute_group = tmp.id_attribute_group AND al.id_lang = '.(int)$cookie->id_lang.')
					LEFT JOIN '._DB_PREFIX_.'attribute_group a ON (a.id_attribute_group = al.id_attribute_group)
					LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (a.id_attribute_group = agl.id_attribute_group AND al.id_lang = '.(int)$cookie->id_lang.')
					GROUP BY tmp.id_attribute
					ORDER BY id_attribute_group';
					break;
				case 'id_feature':
					$sqlQuery['select'] = 'SELECT fl.name, fp.id_feature, fv.id_feature_value, fvl.value, count(fv.id_feature_value) nbr ';
					$sqlQuery['from'] = '
					FROM '._DB_PREFIX_.'feature_product fp
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = '.(int)$id_parent.' AND cp.`id_product` = fp.`id_product`)
					INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = cp.id_product AND p.active = 1)
					LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON (fl.id_feature = fp.id_feature AND fl.id_lang = '.(int)$cookie->id_lang.')
					INNER JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature_value = fp.id_feature_value AND (fv.custom IS NULL OR fv.custom = 0))
					LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = fp.id_feature_value AND fvl.id_lang = '.(int)$cookie->id_lang.') ';
					$sqlQuery['where'] = 'WHERE p.`active` = 1 ';
					$sqlQuery['group'] = 'GROUP BY fv.id_feature_value ';
					break;
				case 'category':
					$sqlQuery['select'] = '
					SELECT c.id_category, c.id_parent, cl.name, (SELECT count(*) # ';
					$sqlQuery['from'] = '
					FROM '._DB_PREFIX_.'category_product cp
					LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = cp.id_product) ';
					$sqlQuery['where'] = '
					WHERE cp.id_category = c.id_category ';
					$sqlQuery['group'] = ') count_products
					FROM '._DB_PREFIX_.'category c
					LEFT JOIN '._DB_PREFIX_.'category_lang as cl ON (cl.id_category = c.id_category AND cl.id_lang = '.(int)$cookie->id_lang.')
					WHERE c.id_parent = '.(int)$id_parent.'
					GROUP BY c.id_category ORDER BY level_depth';
			}
			foreach ($filters as $filterTmp)
			{
				$methodName = 'get'.ucfirst($filterTmp['type']).'FilterSubQuery';
				if (method_exists('BlockLayered', $methodName))
				{
					if ($filter['type'] == $filterTmp['type'])
						$subQueryFilter = self::$methodName(array());
					else
						$subQueryFilter = self::$methodName(@$selectedFilters[$filterTmp['type']]);
					foreach ($subQueryFilter as $key => $value)
					{
						$sqlQuery[$key] .= $value;
					}
				}
			}
			
			$products = false;
			if (!empty($sqlQuery['from']))
				$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sqlQuery['select']."\n".$sqlQuery['from']."\n".$sqlQuery['join']."\n".$sqlQuery['where']."\n".$sqlQuery['group']);
			
			if (isset($products) AND $products)
			{
				foreach ($filters as $filterTmp)
				{
					$methodName = 'filterProductsBy'.ucfirst($filterTmp['type']);
					if (method_exists('BlockLayered', $methodName))
						if ($filter['type'] == $filterTmp['type'])
							$products = self::$methodName(array(), $products);
						else
							$products = self::$methodName(@$selectedFilters[$filterTmp['type']], $products);
				}

				switch($filter['type'])
				{
					case 'price':
						$priceArray = array('type_lite' => 'price', 'type' => 'price', 'id_key' => 0, 'name' => $this->l('Price'),
						'slider' => true, 'max' => '0', 'min' => null, 'values' => array ('1' => 0), 'unit' => Currency::getCurrent()->sign);
						foreach ($products as $product)
						{
							if (is_null($priceArray['min']))
							{
								$priceArray['min'] = $product['price_min'];
								$priceArray['values'][0] = $product['price_min'];
							}
							elseif ($priceArray['min'] > $product['price_min'])
							{
								$priceArray['min'] = $product['price_min'];
								$priceArray['values'][0] = $product['price_min'];
							}

							if ($priceArray['max'] < $product['price_max'])
							{
								$priceArray['max'] = $product['price_max'];
								$priceArray['values'][1] = $product['price_max'];
							}
							
							if (isset($selectedFilters['price']) AND isset($selectedFilters['price'][0])
							AND isset($selectedFilters['price'][1]) AND !empty($selectedFilters['price'][0])
							AND !empty($selectedFilters['price'][1]))
							{
								$priceArray['values'][0] = $selectedFilters['price'][0];
								$priceArray['values'][1] = $selectedFilters['price'][1];
							}
						}
						$filterBlocks[] = $priceArray;
						break;

					case 'weight':
						$weightArray = array('type_lite' => 'weight', 'type' => 'weight', 'id_key' => 0, 'name' => $this->l('Weight'), 'slider' => true,
						'max' => '0', 'min' => null, 'values' => array ('1' => 0), 'unit' => Configuration::get('PS_WEIGHT_UNIT'));
						foreach ($products as $product)
						{
							if (is_null($weightArray['min']))
							{
								$weightArray['min'] = $product['weight'];
								$weightArray['values'][0] = $product['weight'];
							}
							elseif ($weightArray['min'] > $product['weight'])
							{
								$weightArray['min'] = $product['weight'];
								$weightArray['values'][0] = $product['weight'];
							}
							
							if ($weightArray['max'] < $product['weight'])
							{
								$weightArray['max'] = $product['weight'];
								$weightArray['values'][1] = $product['weight'];
							}
							
							if (isset($selectedFilters['weight']) AND isset($selectedFilters['weight'][0])
							AND isset($selectedFilters['weight'][1]) AND !empty($selectedFilters['weight'][0])
							AND !empty($selectedFilters['weight'][1]))
							{
								$weightArray['values'][0] = $selectedFilters['weight'][0];
								$weightArray['values'][1] = $selectedFilters['weight'][1];
							}
						}
						$filterBlocks[] = $weightArray;
						break;

					case 'condition':
						$conditionArray =  array('new' => array('name' => $this->l('New'), 'nbr' => 0), 
						'used' => array('name' => $this->l('Used'), 'nbr' => 0), 'refurbished' => array('name' => $this->l('Refurbished'), 'nbr' => 0));
						if (isset($selectedFilters['condition']) AND in_array($product['condition'], $selectedFilters['condition']))
							$conditionArray[$product['condition']]['checked'] = true;
						foreach ($conditionArray as $key => $condition)
							if (isset($selectedFilters['condition']) AND in_array($key, $selectedFilters['condition']))
								$conditionArray[$key]['checked'] = true;
						foreach ($products as $product)
							$conditionArray[$product['condition']]['nbr']++;
						$filterBlocks[] = array('type_lite' => 'condition', 'type' => 'condition', 'id_key' => 0, 'name' => $this->l('Condition'), 'values' => $conditionArray);
						break;

					case 'quantity':
						$quantityArray = array (0 => array('name' => $this->l('Not available'), 'nbr' => 0), 1 => array('name' => $this->l('In stock'), 'nbr' => 0));
						foreach ($quantityArray as $key => $quantity)
							if (isset($selectedFilters['quantity']) AND in_array($key, $selectedFilters['quantity']))
								$quantityArray[$key]['checked'] = true;
						foreach ($products as $product)
							$quantityArray[(int)($product['quantity'] > 0)]['nbr']++;
						$filterBlocks[] = array('type_lite' => 'quantity', 'type' => 'quantity', 'id_key' => 0, 'name' => $this->l('Availability'), 'values' => $quantityArray);
						break;

					case 'manufacturer':
						$manufaturersArray = array();
						foreach ($products as $manufacturer)
						{
							$manufaturersArray[$manufacturer['id_manufacturer']] = array('name' => $manufacturer['name'], 'nbr' => $manufacturer['nbr']);
							if (isset($selectedFilters['manufacturer']) AND in_array((int)$manufacturer['id_manufacturer'], $selectedFilters['manufacturer']))
								$manufaturersArray[$manufacturer['id_manufacturer']]['checked'] = true;
						}
						$filterBlocks[] = array('type_lite' => 'manufacturer', 'type' => 'manufacturer', 'id_key' => 0, 'name' => $this->l('Manufacturer'), 'values' => $manufaturersArray);
						break;

					case 'id_attribute_group':
						$attributesArray = array();
						foreach ($products as $attributes)
						{
							if (!isset($attributesArray[$attributes['id_attribute_group']]))
							{
								$attributesArray[$attributes['id_attribute_group']] = array ('type_lite' => 'id_attribute_group',
								'type' => 'id_attribute_group_'.(int)$attributes['id_attribute_group'], 'id_key' => (int)$attributes['id_attribute_group'],
								'name' =>  $attributes['attributeName'], 'is_color_group' => (bool)$attributes['is_color_group'], 'values' => array());
							}
							$attributesArray[$attributes['id_attribute_group']]['values'][$attributes['id_attribute']] = array(
							'color' => $attributes['color'], 'name' => $attributes['name'], 'nbr' => (int)$attributes['nbr']);
							if (isset($selectedFilters['id_attribute_group'][$attributes['id_attribute']]))
								$attributesArray[$attributes['id_attribute_group']]['values'][$attributes['id_attribute']]['checked'] = true;
						}
						$filterBlocks = array_merge($filterBlocks, $attributesArray);
						break;
					case 'id_feature':
						$featureArray = array ();
						foreach ($products as $feature)
						{
							if (!isset($featureArray[$feature['id_feature']]))
								$featureArray[$feature['id_feature']] = array('type_lite' => 'id_feature', 'type' => 'id_feature_'.(int)$feature['id_feature'],
								'id_key' => (int)$feature['id_feature'], 'values' => array(), 'name' => $feature['name']);
								
							$featureArray[$feature['id_feature']]['values'][$feature['id_feature_value']] = array('nbr' => (int)$feature['nbr'], 'name' => $feature['value']);
							if (isset($selectedFilters['id_feature']) AND in_array($feature['id_feature_value'], $selectedFilters['id_feature']))
								$featureArray[$feature['id_feature']]['values'][$feature['id_feature_value']]['checked'] = true;
						}
						$filterBlocks = array_merge($filterBlocks, $featureArray);
						break;

					case 'category':
						$tmpArray = array();
						foreach ($products as $category)
							$tmpArray[] = array('name' => $category['name'], 'nbr' => (int)$category['count_products']);
						$filterBlocks[] = array ('type_lite' => 'category', 'type' => 'category', 'id_key' => 0, 'name' => $this->l('Categories'), 'values' => $tmpArray);
						break;
				}
				
			}
			else {
				// Debug
				// We must never enter here
				// The next line must be remove before the release
				//var_export($sqlQuery['select']."\n".$sqlQuery['from']."\n".$sqlQuery['join']."\n".$sqlQuery['where']."\n".$sqlQuery['group']);die();
			}
		}
		
		$nFilters = 0;
		foreach ($selectedFilters AS $filters)
			$nFilters += sizeof($filters);
		
		$smarty->assign(array('layered_show_qties' => (int)Configuration::get('PS_LAYERED_SHOW_QTIES'), 'id_category_layered' => (int)$id_parent,
		'selected_filters' => $selectedFilters, 'n_filters' => (int)$nFilters, 'nbr_filterBlocks' => sizeof($filterBlocks), 'filters' => $filterBlocks));

		return $this->display(__FILE__, 'blocklayered.tpl');
	}
	
	/*
	 * This function must be improved
	 * For the moment, we don't "filter filters"
	 */
	private static function getPriceFilterSubQuery($filterValue)
	{
		$idCurrency = Currency::getCurrent()->id;
		$priceFilterQuery = '';
		if (isset($filterValue) && $filterValue)
		{
			$idCurrency = Currency::getCurrent()->id;
			$priceFilterQuery = '
			INNER JOIN `'._DB_PREFIX_.'price_static_index` as psi ON (psi.id_product = p.id_product AND psi.id_currency = '.(int)$idCurrency.'
			AND psi.price_min <= '.(int)$filterValue[1].' AND psi.price_max >= '.(int)$filterValue[0].' AND psi.`id_product` = p.`id_product` AND psi.`id_currency` = '.(int)$idCurrency.') ';
		}
		else{
			$idCurrency = Currency::getCurrent()->id;
			$priceFilterQuery = '
			INNER JOIN `'._DB_PREFIX_.'price_static_index` as psi ON (psi.id_product = p.id_product AND psi.id_currency = '.(int)$idCurrency.'
			AND psi.`id_product` = p.`id_product` AND psi.`id_currency` = '.(int)$idCurrency.') ';
		}
		
		return array('join' => $priceFilterQuery, 'select' => ', psi.price_min, psi.price_max');
	}
	
	private static function filterProductsByPrice($filterValue, $productCollection)
	{
		if (empty($filterValue))
			return $productCollection;
		foreach ($productCollection as $key => $product)
		{
			if (isset($filterValue) AND $filterValue AND isset($product['price_min']) AND isset($product['id_product'])
			AND ((int)$filterValue[0] > $product['price_min'] OR (int)$filterValue[1] < $product['price_max']))
			{
				$price = Product::getPriceStatic($product['id_product']);
				if ($price < $filterValue[0] || $price > $filterValue[1])
					continue;
				unset($productCollection[$key]);
			}
		}
		return $productCollection;
	}
	
	private static function getWeightFilterSubQuery($filterValue)
	{
		if (isset($filterValue) && $filterValue)
			if ($filterValue[0] != 0 || $filterValue[1] != 0)
				return array('where' => ' AND p.`weight` BETWEEN '.(float)($filterValue[0] - 0.001).' AND '.(float)($filterValue[1] + 0.001).' ');
		
		return array();
	}
	
	private static function getFeatureFilterSubQuery($filterValue)
	{
		if (empty($filterValue))
			return array();
		$queryFilters = ' AND p.id_product IN (SELECT id_product FROM '._DB_PREFIX_.'feature_product fp WHERE ';
		foreach ($filterValue AS $filterVal)
			$queryFilters .= 'fp.`id_feature_value` = '.(int)$filterVal.' OR ';
		$queryFilters = rtrim($queryFilters, 'OR ').') ';
		
		return array('where' => $queryFilters);
	}
	private static function getId_attribute_groupFilterSubQuery($filterValue)
	{
		if (empty($filterValue))
			return array();
		$queryFilters = '
		AND p.id_product IN (SELECT pa.`id_product`
		FROM `'._DB_PREFIX_.'product_attribute_combination` pac
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
		WHERE ';
						
		foreach ($filterValue AS $filterVal)
			$queryFilters .= 'pac.`id_attribute` = '.(int)$filterVal.' OR ';
		$queryFilters = rtrim($queryFilters, 'OR ').') ';
		
		return array('where' => $queryFilters);
	}
	
	private static function getCategoryFilterSubQuery($filterValue)
	{
		if (empty($filterValue))
			return array();
		$queryFiltersJoin = '';
		$queryFiltersWhere = ' AND p.id_product IN (SELECT id_product FROM '._DB_PREFIX_.'category_product cp WHERE ';
		foreach ($filterValue AS $id_category)
			$queryFiltersWhere .= 'cp.`id_category` = '.(int)$id_category.' OR ';
		$queryFiltersWhere = rtrim($queryFilters, 'OR ').') ';
		
		return array('where' => $queryFiltersWhere, 'join' => $queryFiltersJoin);
	}
	
	private static function getQuantityFilterSubQuery($filterValue)
	{
		if (sizeof($filterValue) == 2 OR empty($filterValue))
			return array();
		$queryFilters = ' AND p.quantity '.(!$filterValue[0] ? '=' : '>').' 0 ';
		
		return array('where' => $queryFilters);
	}
	
	private static function getManufacturerFilterSubQuery($filterValue)
	{
		if (empty($filterValue))
			return array();
		
		$queryFilters = ' AND p.id_manufacturer IN ('.implode($filterValue, ',').')';
		
		return array('where' => $queryFilters, 'select' => ', m.name');
	}
	
	private static function getConditionFilterSubQuery($filterValue)
	{
		if (sizeof($filterValue) == 3 OR empty($filterValue))
			return array();
		$queryFilters = ' AND p.condition IN (';
		foreach ($filterValue AS $cond)
			$queryFilters .= '\''.$cond.'\',';
		$queryFilters = rtrim($queryFilters, ',').') ';
		
		return array('where' => $queryFilters);
	}
	
	/*
	 * This function must be improved
	 * For the moment, we don't "filter filters"
	 */
	public function generateFiltersBlock($selectedFilters = array())
	{
		return self::generateFiltersBlockNew($selectedFilters);
		// return self::generateFiltersBlockOld($selectedFilters);
	}
	
	public function ajaxCallBackOffice($categoryBox = array(), $id_layered_filter = NULL)
	{
		global $cookie;
		
		if (!empty($id_layered_filter))
		{
			$layeredFilter = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM '._DB_PREFIX_.'layered_filter WHERE id_layered_filter = '.(int)$id_layered_filter);
			if ($layeredFilter AND isset($layeredFilter['filters']) AND !empty($layeredFilter['filters']))
				$layeredValues = unserialize($layeredFilter['filters']);
			if (isset($layeredValues['categories']) AND sizeof($layeredValues['categories']))
				foreach ($layeredValues['categories'] AS $id_category)
					$categoryBox[] = (int)$id_category;
		}
		
		/* Clean categoryBox before use */
		if (isset($categoryBox) AND is_array($categoryBox))
			foreach ($categoryBox AS &$value)
				$value = (int)$value;
		
		$attributeGroups = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT ag.id_attribute_group, ag.is_color_group, agl.name, COUNT(DISTINCT(a.id_attribute)) n
		FROM '._DB_PREFIX_.'attribute_group ag
		LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group = ag.id_attribute_group)
		LEFT JOIN '._DB_PREFIX_.'attribute a ON (a.id_attribute_group = ag.id_attribute_group)
		'.(sizeof($categoryBox) ? '
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_attribute = a.id_attribute)
		LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = pac.id_product_attribute)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = pa.id_product)' : '').'
		WHERE agl.id_lang = '.(int)$cookie->id_lang.
		(sizeof($categoryBox) ? ' AND cp.id_category IN ('.implode(',', $categoryBox).')' : '').'
		GROUP BY ag.id_attribute_group');
		
		$features = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT fl.id_feature, fl.name, COUNT(DISTINCT(fv.id_feature_value)) n
		FROM '._DB_PREFIX_.'feature_lang fl
		LEFT JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature = fl.id_feature)
		'.(sizeof($categoryBox) ? '
		LEFT JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_feature = fv.id_feature)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = fp.id_product)' : '').'		
		WHERE (fv.custom IS NULL OR fv.custom = 0) AND fl.id_lang = '.(int)$cookie->id_lang.
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
			<ul><li class="ui-state-default layered_right"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" id="layered_selection_weight_slider" name="layered_selection_weight_slider" /> <span class="position"></span>'.$this->l('Product weight filter (slider)').'</li></ul>
			<ul><li class="ui-state-default layered_right"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" id="layered_selection_price_slider" name="layered_selection_price_slider" /> <span class="position"></span>'.$this->l('Product price filter (slider)').'</li></ul>';
			
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
		global $smarty, $cookie;

		$selectedFilters = $this->getSelectedFilters();
		$products = $this->getProductByFilters($selectedFilters);
		$products = Product::getProductsProperties((int)$cookie->id_lang, $products);
		
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
			
		$smarty->assign('nb_products', $nbProducts);
		$pagination_infos = array('pages_nb' => (int)($pages_nb), 'p' => (int)$p, 'n' => (int)$n, 'range' => (int)$range, 'start' => (int)$start, 'stop' => (int)$stop,
		'nArray' => $nArray = (int)Configuration::get('PS_PRODUCTS_PER_PAGE') != 10 ? array((int)Configuration::get('PS_PRODUCTS_PER_PAGE'), 10, 20, 50) : array(10, 20, 50));
		$smarty->assign($pagination_infos);
		$smarty->assign('comparator_max_item', (int)(Configuration::get('PS_COMPARATOR_MAX_ITEM')));
		$smarty->assign('products', $products);
		
		/* We are sending an array in jSon to the .js controller, it will update both the filters and the products zones */
		return Tools::jsonEncode(array(
		'filtersBlock' => $this->generateFiltersBlock($selectedFilters),
		'productList' => $smarty->fetch(_PS_THEME_DIR_.'product-list.tpl'),
		'pagination' => $smarty->fetch(_PS_THEME_DIR_.'pagination.tpl')));
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
		`type` ENUM(\'category\',\'id_feature\',\'id_attribute_group\',\'quantity\',\'condition\',\'manufacturer\',\'weight\',\'price\') NOT NULL,
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
				if (!isset($doneCategories[(int)$id_category]['p']))
				{
					$doneCategories[(int)$id_category]['p'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'price\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
			}
			if ($toInsert)
				Db::getInstance()->Execute(rtrim($queryCategory, ','));
		}
	}
	
	function filterProducts($products, $selectedFilters, $excludeType = false)
	{
		static $priceStatics = array();
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
						$min = $filters[0];
						$max = $filters[1]; 
						foreach ($products AS $k => $product)
							if ((float)$min <= (float)$product[$filterByLetter[$type]] AND (float)$product[$filterByLetter[$type]] <= (float)$max)
								$productsToKeep[] = (int)$k;
					break;
					case 'price':
						$min = $filters[0];
						$max = $filters[1]; 
						foreach ($products AS $k => $product)
							if ((float)$min <= (float)$product['price_min'] AND (float)$product['price_max'] <= (float)$max)
								$productsToKeep[] = (int)$k;
							elseif ((float)$product['price_min'] < (float)$max AND (float)$product['price_max'] > (float)$max
							OR (float)$product['price_min'] < (float)$min AND (float)$product['price_max'] > (float)$min)
							{
								if (!isset($priceStatics[(int)$k]))
									$priceStatics[(int)$k] = Product::getPriceStatic((int)$k);
								$price = $priceStatics[(int)$k];
								if ((float)$min <= $price AND $price <= (float)$max)
									$productsToKeep[] = (int)$k;
							}
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
}
