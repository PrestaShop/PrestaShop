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

/**
 * @since 1.5.0
 */
class HelperListCore extends Helper
{
	/** @var array Cache for query results */
	protected $_list = array();

	/** @var integer Number of results in list */
	public $listTotal = 0;

	/** @var array WHERE clause determined by filter fields */
	protected $_filter;

	/** @var array Number of results in list per page (used in select field) */
	protected $_pagination = array(20, 50, 100, 300);

	/** @var string ORDER BY clause determined by field/arrows in list header */
	public $orderBy;

	/** @var string Default ORDER BY clause when $orderBy is not defined */
	public $_defaultOrderBy = false;

	/** @var array : list of vars for button delete*/
	public $tpl_delete_link_vars = array();

	/** @var string Order way (ASC, DESC) determined by arrows in list header */
	public $orderWay;

	public $identifier;

	protected $deleted = 0;

	/** @var array $cache_lang use to cache texts in current language */
	public static $cache_lang = array();

	public $is_cms = false;

	public $position_identifier;

	/**
	 * @var string Customize list display
	 *
	 * align  : determine value alignment
	 * prefix : displayed before value
	 * suffix : displayed after value
	 * image  : object image
	 * icon   : icon determined by values
	 * active : allow to toggle status
	 */
	protected $fields_list;

	/** @var boolean Content line is clickable if true */
	public $no_link = false;

	protected $header_tpl = 'list_header.tpl';
	protected $content_tpl = 'list_content.tpl';
	protected $footer_tpl = 'list_footer.tpl';

	/** @var array list of required actions for each list row */
	public $actions = array();

	/** @var array list of row ids associated with a given action for witch this action have to not be available */
	public $list_skip_actions = array();

	public $bulk_actions = false;
	public $specificConfirmDelete = null;
	public $colorOnBackground;

	/** @var bool If true, activates color on hover */
	public $row_hover = true;

	/** @var if not null, a title will be added on that list */
	public $title = null;

	/** @var boolean ask for simple header : no filters, no paginations and no sorting */
	public $simple_header = false;

	public $ajax_params = array();

	public function __construct()
	{
		$this->base_folder = 'helpers/list/';
		$this->base_tpl = 'list.tpl';

		parent::__construct();
	}

	/**
	 * Return an html list given the data to fill it up
	 *
	 * @param array $list entries to display (rows)
	 * @param array $fields_display fields (cols)
	 * @return string html
	 */
	public function generateList($list, $fields_display)
	{
		// Append when we get a syntax error in SQL query
		if ($list === false)
		{
			$this->displayWarning($this->l('Bad SQL query', 'Helper'));
			return false;
		}

		$this->tpl = $this->createTemplate($this->base_tpl);
		$this->header_tpl = $this->createTemplate($this->header_tpl);
		$this->content_tpl = $this->createTemplate($this->content_tpl);
		$this->footer_tpl = $this->createTemplate($this->footer_tpl);

		$this->_list = $list;
		$this->fields_list = $fields_display;

		// Display list header (filtering, pagination and column names)
		$tpl_vars['header'] = $this->displayListHeader();

		// Show the content of the table
		$tpl_vars['content'] = $this->displayListContent();

		// Close list table and submit button
		$tpl_vars['footer'] = $this->displayListFooter();

		$this->tpl->assign($tpl_vars);
		return parent::generate();
	}

	/**
	 * Fetch the template for action enable
	 *
	 * @param string $token
	 * @param int $id
	 * @param int $value state enabled or not
	 * @param string $active status
	 * @param int $id_category
	 * @param int $id_product
	 * @return string
	 */
	public function displayEnableLink($token, $id, $value, $active, $id_category = null, $id_product = null)
	{
		$tpl_enable = $this->createTemplate('list_action_enable.tpl');
		$tpl_enable->assign(array(
			'enabled' => (bool)$value,
			'url_enable' => Tools::safeOutput($this->currentIndex.'&'.$this->identifier.'='.(int)$id.'&'.$active.$this->table.
				((int)$id_category && (int)$id_product ? '&id_category='.(int)$id_category : '').'&token='.($token != null ? $token : $this->token))
		));
		return $tpl_enable->fetch();
	}

	public function displayListContent()
	{
		if ($this->position_identifier)
			$id_category = (int)Tools::getValue('id_'.($this->is_cms ? 'cms_' : '').'category', ($this->is_cms ? '1' : Category::getRootCategory()->id ));
		else
			$id_category = Category::getRootCategory()->id;

		if (isset($this->fields_list['position']))
		{
			$positions = array_map(create_function('$elem', 'return (int)($elem[\'position\']);'), $this->_list);
			sort($positions);
		}

		// key_to_get is used to display the correct product category or cms category after a position change
		$identifier = in_array($this->identifier, array('id_category', 'id_cms_category')) ? '_parent' : '';
		if ($identifier)
			$key_to_get = 'id_'.($this->is_cms ? 'cms_' : '').'category'.$identifier;

		foreach ($this->_list as $index => $tr)
		{
			$id = $tr[$this->identifier];
			$name = isset($tr['name']) ? $tr['name'] : null;

			if ($this->shopLinkType)
				$this->_list[$index]['short_shop_name'] = Tools::strlen($tr['shop_name']) > 15 ? Tools::substr($tr['shop_name'], 0, 15).'...' : $tr['shop_name'];

			// Check all available actions to add to the current list row
			foreach ($this->actions as $action)
			{
				//Check if the action is available for the current row
				if (!array_key_exists($action, $this->list_skip_actions) || !in_array($id, $this->list_skip_actions[$action]))
				{
					$method_name = 'display'.ucfirst($action).'Link';

					if (method_exists($this->context->controller, $method_name))
						$this->_list[$index][$action] = $this->context->controller->$method_name($this->token, $id, $name);
					elseif (method_exists($this, $method_name))
						$this->_list[$index][$action] = $this->$method_name($this->token, $id, $name);
				}
			}

			// @todo skip action for bulk actions
			// $this->_list[$index]['has_bulk_actions'] = true;
			foreach ($this->fields_list as $key => $params)
			{
				$tmp = explode('!', $key);
				$key = isset($tmp[1]) ? $tmp[1] : $tmp[0];

				if (isset($params['active']))
				{
					// If method is defined in calling controller, use it instead of the Helper method
					if (method_exists($this->context->controller, 'displayEnableLink'))
						$calling_obj = $this->context->controller;
					else
						$calling_obj = $this;
					$this->_list[$index][$key] = $calling_obj->displayEnableLink(
						$this->token,
						$id,
						$tr[$key],
						$params['active'],
						Tools::getValue('id_category'),
						Tools::getValue('id_product')
					);
				}
				elseif (isset($params['activeVisu']))
					$this->_list[$index][$key] = (bool)$tr[$key];
				elseif (isset($params['position']))
				{
					$this->_list[$index][$key] = array(
						'position' => $tr[$key],
						'position_url_down' => $this->currentIndex.
							(isset($key_to_get) ? '&'.$key_to_get.'='.(int)$id_category : '').
							'&'.$this->position_identifier.'='.$id.
							'&way=1&position='.((int)$tr['position'] + 1).'&token='.$this->token,
						'position_url_up' => $this->currentIndex.
							(isset($key_to_get) ? '&'.$key_to_get.'='.(int)$id_category : '').
							'&'.$this->position_identifier.'='.$id.
							'&way=0&position='.((int)$tr['position'] - 1).'&token='.$this->token
					);
				}
				elseif (isset($params['image']))
				{
					// item_id is the product id in a product image context, else it is the image id.
					$item_id = isset($params['image_id']) ? $tr[$params['image_id']] : $id;
					if ($params['image'] != 'p' || Configuration::get('PS_LEGACY_IMAGES'))
						$path_to_image = _PS_IMG_DIR_.$params['image'].'/'.$item_id.(isset($tr['id_image']) ? '-'.(int)$tr['id_image'] : '').'.'.$this->imageType;
					else
						$path_to_image = _PS_IMG_DIR_.$params['image'].'/'.Image::getImgFolderStatic($tr['id_image']).(int)$tr['id_image'].'.'.$this->imageType;
					$this->_list[$index][$key] = ImageManager::thumbnail($path_to_image, $this->table.'_mini_'.$item_id.'.'.$this->imageType, 45, $this->imageType);
				}
				elseif (isset($params['icon']) && isset($tr[$key]) && (isset($params['icon'][$tr[$key]]) || isset($params['icon']['default'])))
				{
					if (isset($params['icon'][$tr[$key]]) && is_array($params['icon'][$tr[$key]]))
						$this->_list[$index][$key] = array(
							'src' => $params['icon'][$tr[$key]]['src'],
							'alt' => $params['icon'][$tr[$key]]['alt'],
						);
					else
						$this->_list[$index][$key] = array(
							'src' =>  isset($params['icon'][$tr[$key]]) ? $params['icon'][$tr[$key]] : $params['icon']['default'],
							'alt' =>  isset($params['icon'][$tr[$key]]) ? $params['icon'][$tr[$key]] : $params['icon']['default'],
						);
				}
				elseif (isset($params['type']) && $params['type'] == 'float')
					$this->_list[$index][$key] = rtrim(rtrim($tr[$key], '0'), '.');
				elseif (isset($params['type']) && $params['type'] == 'price')
				{
					$currency = (isset($params['currency']) && $params['currency']) ? Currency::getCurrencyInstance($tr['id_currency']) : $this->context->currency;
					$this->_list[$index][$key] = Tools::displayPrice($tr[$key], $currency, false);
				}
				elseif (isset($params['type']) && $params['type'] == 'date')
					$this->_list[$index][$key] = Tools::displayDate($tr[$key], $this->context->language->id);
				elseif (isset($params['type']) && $params['type'] == 'datetime')
					$this->_list[$index][$key] = Tools::displayDate($tr[$key], $this->context->language->id, true);
				elseif (isset($tr[$key]))
				{
					$echo = $tr[$key];
					if (isset($params['callback']))
					{
						$callback_obj = (isset($params['callback_object'])) ? $params['callback_object'] : $this->context->controller;
						$this->_list[$index][$key] = call_user_func_array(array($callback_obj, $params['callback']), array($echo, $tr));
					}
					else
						$this->_list[$index][$key] = $echo;
				}
			}
		}

		$this->content_tpl->assign(array_merge($this->tpl_vars, array(
			'shop_link_type' => $this->shopLinkType,
			'name' => isset($name) ? $name : null,
			'position_identifier' => $this->position_identifier,
			'identifier' => $this->identifier,
			'table' => $this->table,
			'token' => $this->token,
			'color_on_bg' => $this->colorOnBackground,
			'id_category' => $id_category,
			'bulk_actions' => $this->bulk_actions,
			'positions' => isset($positions) ? $positions : null,
			'order_by' => $this->orderBy,
			'order_way' => $this->orderWay,
			'is_cms' => $this->is_cms,
			'fields_display' => $this->fields_list,
			'list' => $this->_list,
			'actions' => $this->actions,
			'no_link' => $this->no_link,
			'current_index' => $this->currentIndex,
			'view' => in_array('view', $this->actions),
			'edit' => in_array('edit', $this->actions),
			'has_actions' => !empty($this->actions),
			'has_bulk_actions' => !empty($this->bulk_actions),
			'list_skip_actions' => $this->list_skip_actions,
			'row_hover' => $this->row_hover,
		)));
		return $this->content_tpl->fetch();
	}

	/**
	 * Display duplicate action link
	 */
	public function displayDuplicateLink($token = null, $id, $name = null)
	{
		$tpl = $this->createTemplate('list_action_duplicate.tpl');
		if (!array_key_exists('Bad SQL query', self::$cache_lang))
			self::$cache_lang['Duplicate'] = $this->l('Duplicate', 'Helper');

		if (!array_key_exists('Copy images too?', self::$cache_lang))
			self::$cache_lang['Copy images too?'] = $this->l('Copy images too?', 'Helper');

		$duplicate = $this->currentIndex.'&'.$this->identifier.'='.$id.'&duplicate'.$this->table;

		$tpl->assign(array(
			'href' => Tools::safeOutput($this->currentIndex.'&'.$this->identifier.'='.$id.'&view'.$this->table.'&token='.($token != null ? $token : $this->token)),
			'action' => self::$cache_lang['Duplicate'],
			'confirm' => self::$cache_lang['Copy images too?'],
			'location_ok' => Tools::safeOutput($duplicate.'&token='.($token != null ? $token : $this->token)),
			'location_ko' => Tools::safeOutput($duplicate.'&noimage=1&token='.($token ? $token : $this->token)),
		));

		return $tpl->fetch();
	}


	/**
	 * Display action show details of a table row
	 * This action need an ajax request with a return like this:
	 *   {
	 *     use_parent_structure: true // If false, data need to be an html
	 *     data:
	 *       [
	 *         {field_name: 'value'}
	 *       ],
	 *     fields_display: // attribute $fields_list of the admin controller
	 *   }
	 * or somethins like this:
	 *   {
	 *     use_parent_structure: false // If false, data need to be an html
	 *     data:
	 *       '<p>My html content</p>',
	 *     fields_display: // attribute $fields_list of the admin controller
	 *   }
	 */
	public function displayDetailsLink($token = null, $id, $name = null)
	{
		$tpl = $this->createTemplate('list_action_details.tpl');
		if (!array_key_exists('Details', self::$cache_lang))
			self::$cache_lang['Details'] = $this->l('Details', 'Helper');

		$ajax_params = $this->ajax_params;
		if (!is_array($ajax_params) || !isset($ajax_params['action']))
			$ajax_params['action'] = 'details';

		$tpl->assign(array(
			'id' => Tools::safeOutput($id),
			'controller' => str_replace('Controller', '', get_class($this->context->controller)),
			'token' => Tools::safeOutput($token != null ? $token : $this->token),
			'action' => self::$cache_lang['Details'],
			'params' => $ajax_params,
			'json_params' => Tools::jsonEncode($ajax_params)
		));
		return $tpl->fetch();
	}

	/**
	 * Display view action link
	 */
	public function displayViewLink($token = null, $id, $name = null)
	{
		$tpl = $this->createTemplate('list_action_view.tpl');
		if (!array_key_exists('View', self::$cache_lang))
			self::$cache_lang['View'] = $this->l('View', 'Helper');

		$tpl->assign(array(
			'href' => Tools::safeOutput($this->currentIndex.'&'.$this->identifier.'='.$id.'&view'.$this->table.'&token='.($token != null ? $token : $this->token)),
			'action' => self::$cache_lang['View'],
		));

		return $tpl->fetch();

	}

	/**
	 * Display edit action link
	 */
	public function displayEditLink($token = null, $id, $name = null)
	{
		$tpl = $this->createTemplate('list_action_edit.tpl');
		if (!array_key_exists('Edit', self::$cache_lang))
			self::$cache_lang['Edit'] = $this->l('Edit', 'Helper');

		$tpl->assign(array(
			'href' => $this->currentIndex.'&'.$this->identifier.'='.$id.'&update'.$this->table.'&token='.($token != null ? $token : $this->token),
			'action' => self::$cache_lang['Edit'],
			'id' => $id
		));

		return $tpl->fetch();
	}

	/**
	 * Display delete action link
	 */
	public function displayDeleteLink($token = null, $id, $name = null)
	{
		$tpl = $this->createTemplate('list_action_delete.tpl');

		if (!array_key_exists('Delete', self::$cache_lang))
			self::$cache_lang['Delete'] = $this->l('Delete', 'Helper');

		if (!array_key_exists('DeleteItem', self::$cache_lang))
			self::$cache_lang['DeleteItem'] = $this->l('Delete selected item?', 'Helper');

		if (!array_key_exists('Name', self::$cache_lang))
			self::$cache_lang['Name'] = $this->l('Name:', 'Helper');

		if (!is_null($name))
			$name = '\n\n'.self::$cache_lang['Name'].' '.$name;

		$data = array(
			$this->identifier => $id,
			'href' => Tools::safeOutput($this->currentIndex.'&'.$this->identifier.'='.$id.'&delete'.$this->table.'&token='.($token != null ? $token : $this->token)),
			'action' => self::$cache_lang['Delete'],
		);
		
		if ($this->specificConfirmDelete !== false)
			$data['confirm'] = !is_null($this->specificConfirmDelete) ? '\r'.$this->specificConfirmDelete : self::$cache_lang['DeleteItem'].$name;
		
		$tpl->assign(array_merge($this->tpl_delete_link_vars, $data));

		return $tpl->fetch();
	}

	/**
	 * Display delete action link
	 */
	public function displayDefaultLink($token = null, $id, $name = null)
	{
		$tpl = $this->createTemplate('list_action_default.tpl');
		if (!array_key_exists('Default', self::$cache_lang))
			self::$cache_lang['Default'] = $this->l('Default', 'Helper');

		$tpl->assign(array_merge($this->tpl_delete_link_vars, array(
			'href' => Tools::safeOutput($this->currentIndex).'&'.Tools::safeOutput($this->identifier).'='.(int)$id.'&delete'.Tools::safeOutput($this->table).'&token='.Tools::safeOutput(($token != null ? $token : $this->token)),
			'action' => self::$cache_lang['Default'],
			'name' => Tools::safeOutput($name),
		)));

		return $tpl->fetch();
	}

	/**
	 * Display list header (filtering, pagination and column names)
	 */
	public function displayListHeader()
	{
		$id_cat = (int)Tools::getValue('id_'.($this->is_cms ? 'cms_' : '').'category');

		if (!isset($token) || empty($token))
			$token = $this->token;

		/* Determine total page number */
		if (isset($this->context->cookie->{$this->table.'_pagination'}) && $this->context->cookie->{$this->table.'_pagination'})
			$default_pagination = $this->context->cookie->{$this->table.'_pagination'};
		else
			$default_pagination = $this->_pagination[0];

		$total_pages = ceil($this->listTotal / Tools::getValue('pagination', ($default_pagination)));

		if (!$total_pages) 
			$total_pages = 1;

		$identifier = Tools::getIsset($this->identifier) ? '&'.$this->identifier.'='.(int)Tools::getValue($this->identifier) : '';
		$order = '';
		if (Tools::getIsset($this->table.'Orderby'))
			$order = '&'.$this->table.'Orderby='.urlencode($this->orderBy).'&'.$this->table.'Orderway='.urlencode(strtolower($this->orderWay));

		$action = $this->currentIndex.$identifier.'&token='.$token.$order.'#'.$this->table;

		/* Determine current page number */
		$page = (int)Tools::getValue('submitFilter'.$this->table);
		if (!$page)
			$page = 1;

		/* Choose number of results per page */
		$selected_pagination = Tools::getValue(
			'pagination',
			isset($this->context->cookie->{$this->table.'_pagination'}) ? $this->context->cookie->{$this->table.'_pagination'} : null
		);

		// Cleaning links
		if (Tools::getValue($this->table.'Orderby') && Tools::getValue($this->table.'Orderway'))
			$this->currentIndex = preg_replace('/&'.$this->table.'Orderby=([a-z _]*)&'.$this->table.'Orderway=([a-z]*)/i', '', $this->currentIndex);

		if ($this->position_identifier && (int)Tools::getValue($this->position_identifier, 1))
			$table_id = substr($this->identifier, 3, strlen($this->identifier));

		if ($this->position_identifier && ($this->orderBy == 'position' && $this->orderWay != 'DESC'))
			$table_dnd = true;

		$prefix = isset($this->controller_name) ? str_replace(array('admin', 'controller'), '', Tools::strtolower($this->controller_name)) : '';
		foreach ($this->fields_list as $key => $params)
		{
			if (!isset($params['type']))
				$params['type'] = 'text';
			$value = Context::getContext()->cookie->{$prefix.$this->table.'Filter_'.(array_key_exists('filter_key', $params) ? $params['filter_key'] : $key)};
			switch ($params['type'])
			{
				case 'bool':
					break;

				case 'date':
				case 'datetime':
					if (is_string($value))
						$value = Tools::unSerialize($value);
					if (!Validate::isCleanHtml($value[0]) || !Validate::isCleanHtml($value[1]))
						$value = '';
					$name = $this->table.'Filter_'.(isset($params['filter_key']) ? $params['filter_key'] : $key);
					$name_id = str_replace('!', '__', $name);

					$params['id_date'] = $name_id;
					$params['name_date'] = $name;

					$this->context->controller->addJqueryUI('ui.datepicker');
					break;

				case 'select':
					foreach ($params['list'] as $option_value => $option_display)
					{
						if (isset(Context::getContext()->cookie->{$prefix.$this->table.'Filter_'.$params['filter_key']})
							&& Context::getContext()->cookie->{$prefix.$this->table.'Filter_'.$params['filter_key']} == $option_value
							&& Context::getContext()->cookie->{$prefix.$this->table.'Filter_'.$params['filter_key']} != '')
							$this->fields_list[$key]['select'][$option_value]['selected'] = 'selected';
					}
					break;

				case 'text':
					if (!Validate::isCleanHtml($value))
						$value = '';
			}
			$params['value'] = $value;
			$this->fields_list[$key] = $params;
		}

		$this->header_tpl->assign(array_merge($this->tpl_vars, array(
			'title' => $this->title,
			'show_toolbar' => $this->show_toolbar,
			'toolbar_scroll' => $this->toolbar_scroll,
			'toolbar_btn' => $this->toolbar_btn,
			'table' => $this->table,
			'currentIndex' => $this->currentIndex,
			'action' => $action,
			'page' => $page,
			'simple_header' => $this->simple_header,
			'total_pages' => $total_pages,
			'selected_pagination' => $selected_pagination,
			'pagination' => $this->_pagination,
			'list_total' => $this->listTotal,
			'is_order_position' => $this->position_identifier && $this->orderBy == 'position',
			'order_way' => $this->orderWay,
			'order_by' => $this->orderBy,
			'token' => $this->token,
			'fields_display' => $this->fields_list,
			'delete' => in_array('delete', $this->actions),
			'identifier' => $this->identifier,
			'id_cat' => $id_cat,
			'shop_link_type' => $this->shopLinkType,
			'has_actions' => !empty($this->actions),
			'has_bulk_actions' => !empty($this->bulk_actions),
			'bulk_actions' => $this->bulk_actions,
			'table_id' => isset($table_id) ? $table_id : null,
			'table_dnd' => isset($table_dnd) ? $table_dnd : null,
			'name' => isset($name) ? $name : null,
			'name_id' => isset($name_id) ? $name_id : null,
			'row_hover' => $this->row_hover,
		)));

		return $this->header_tpl->fetch();
	}

	/**
	 * Close list table and submit button
	 */
	public function displayListFooter()
	{
		$this->footer_tpl->assign(array_merge($this->tpl_vars, array(
			'token' => $this->token,
			'table' => $this->table,
			'current' => $this->currentIndex,
			'bulk_actions' => $this->bulk_actions,
			'simple_header' => $this->simple_header
		)));
		return $this->footer_tpl->fetch();
	}
}
