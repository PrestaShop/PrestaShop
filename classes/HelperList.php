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

class HelperListCore extends Helper
{
	/** @var array Cache for query results */
	protected $_list = array();

	/** @var integer Number of results in list */
	public $listTotal = 0;

	/** @var array WHERE clause determined by filter fields */
	protected $_filter;

	/** @var array Temporary SQL table WHERE clause determinated by filter fields */
	protected $_tmpTableFilter = '';

	/** @var array Number of results in list per page (used in select field) */
	protected $_pagination = array(20, 50, 100, 300);

	/** @var string ORDER BY clause determined by field/arrows in list header */
	public $orderBy;

	/** @var string Default ORDER BY clause when $orderBy is not defined */
	public $_defaultOrderBy = false;

	/** @var string Order way (ASC, DESC) determined by arrows in list header */
	public $orderWay;

	public $identifier;

	protected $deleted = 0;

	/** @var string Folder of controller */
	public $tpl_folder;

	/** @var array $cache_lang use to cache texts in current language */
	public static $cache_lang = array();

	protected $is_cms = false;

	protected $is_dnd_identifier = false;

	public $toolbar = true;

	/* Customize list display
	 *
	 * align  : determine value alignment
	 * prefix : displayed before value
	 * suffix : displayed after value
	 * image  : object image
	 * icon   : icon determined by values
	 * active : allow to toggle status
	 */
	protected $fieldsDisplay;

	/** @var boolean Content line is clickable if true */
	public $no_link = false;

	protected $tpl = 'helper/list/list.tpl';
	protected $header_tpl = 'helper/list/list_header.tpl';
	protected $content_tpl = 'helper/list/list_content.tpl';
	protected $footer_tpl = 'helper/list/list_footer.tpl';

	/** @var array list of required actions for each list row */
	public $actions = array();

	/** @var array list of row ids associated with a given action for witch this action have to not be available */
	public $list_skip_actions = array();

	public $bulk_actions = false;
	public $specificConfirmDelete;
	public $colorOnBackground;

	protected $identifiersDnd = array(
		'id_product' => 'id_product',
		'id_category' => 'id_category_to_move',
		'id_cms_category' => 'id_cms_category_to_move',
		'id_cms' => 'id_cms',
		'id_attribute' => 'id_attribute',
		'id_attribute_group' => 'id_attribute_group',
		'id_carrier' => 'id_carrier',
		'id_tab' => 'id_tab'
	);

	/** @var if not null, a title will be added on that list */
	public $title = null;

	/** @var boolean ask for simple header : no filters, no paginations and no sorting */
	public $simple_header = false;

	public function __construct()
	{
		parent::__construct();
		$smarty = $this->context->smarty;
		$controller = $this->context->controller;

		// handle template overriding (smarty 3 template inheritance)
		if (file_exists($smarty->template_dir[0].'/'.$controller->tpl_folder.'list_header.tpl'))
			$this->header_tpl = $controller->tpl_folder.'list_header.tpl';

		if (file_exists($smarty->template_dir[0].'/'.$controller->tpl_folder.'list_content.tpl'))
			$this->content_tpl = $controller->tpl_folder.'list_content.tpl';

		if (file_exists($smarty->template_dir[0].'/'.$controller->tpl_folder.'list_footer.tpl'))
			$this->footer_tpl = $controller->tpl_folder.'list_footer.tpl';

		$this->header_tpl = $this->context->smarty->createTemplate($this->header_tpl);
		$this->content_tpl = $this->context->smarty->createTemplate($this->content_tpl);
		$this->footer_tpl = $this->context->smarty->createTemplate($this->footer_tpl);
	}

	/**
	 * Return an html list given the data to fill it up
	 *
	 * @param array $list entries to display (rows)
	 * @param array $fieldsDisplay fields (cols)
	 * @return string html
	 */
	public function generateList($list, $fields_display)
	{
		/*if ($this->edit AND (!isset($this->noAdd) OR !$this->noAdd))
			$this->displayAddButton();*/

		/* Append when we get a syntax error in SQL query */
		if ($list === false)
		{
			$this->displayWarning($this->l('Bad SQL query'));
			return false;
		}
		$this->_list = $list;
		$this->fieldsDisplay = $fields_display;

		/* Display list header (filtering, pagination and column names) */
		$tpl_vars['header'] = $this->displayListHeader();
		/* Show the content of the table */
		$tpl_vars['content'] = $this->displayListContent();
		/* Close list table and submit button */
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
	 */
	protected function displayEnableLink($token, $id, $value, $active, $id_category = null, $id_product = null)
	{
		$tpl_enable = $this->context->smarty->createTemplate('helper/list/list_action_enable.tpl');
		$tpl_enable->assign(array(
			'enabled' => (bool)$value,
			'url_enable' => $this->currentIndex.'&'.$this->identifier.'='.$id.'&'.$active.$this->table.
				((int)$id_category && (int)$id_product ? '&id_category='.$id_category : '').'&token='.($token != null ? $token : $this->token)
		));
		return $tpl_enable->fetch();
	}

	public function displayListContent($token = null)
	{

		if ($this->is_dnd_identifier)
			$id_category = (int)Tools::getValue('id_'.($this->is_cms ? 'cms_' : '').'category', '1');
		else
			$id_category = 1; // default categ

		if (isset($this->fieldsDisplay['position']))
		{
			$positions = array_map(create_function('$elem', 'return (int)($elem[\'position\']);'), $this->_list);
			sort($positions);
		}

		$identifier = in_array($this->identifier, array('id_category', 'id_cms_category')) ? '_parent' : '';
		$key_to_get = 'id_'.($this->is_cms ? 'cms_' : '').'category'.$identifier;

		$fields = array();

		foreach ($this->_list as $index => $tr)
		{
			$id = $tr[$this->identifier];

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
						$this->_list[$index][$action] = $this->context->controller->$method_name($token, $id);
					elseif (method_exists($this, $method_name))
						$this->_list[$index][$action] = $this->$method_name($token, $id);

				}
			}

			// @todo skip action for bulk actions
			// $this->_list[$index]['has_bulk_actions'] = true;

			foreach ($this->fieldsDisplay as $key => $params)
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
							'&'.$key_to_get.'='.(int)$id_category.'&'.$this->identifiersDnd[$this->identifier].'='.$id.
							'&way=1&position='.((int)$tr['position'] + 1).'&token='.$this->token,
						'position_url_up' => $this->currentIndex.
							'&'.$key_to_get.'='.(int)$id_category.'&'.$this->identifiersDnd[$this->identifier].'='.$id.
							'&way=0&position='.((int)$tr['position'] - 1).'&token='.$this->token
					);
				}
				elseif (isset($params['image']))
				{
					// item_id is the product id in a product image context, else it is the image id.
					$item_id = isset($params['image_id']) ? $tr[$params['image_id']] : $id;
					// If it's a product image
					if (isset($tr['id_image']))
					{
						$image = new Image((int)$tr['id_image']);
						$path_to_image = _PS_IMG_DIR_.$params['image'].'/'.$image->getExistingImgPath().'.'.$this->imageType;
					}else
						$path_to_image = _PS_IMG_DIR_.$params['image'].'/'.$item_id.(isset($tr['id_image']) ? '-'.(int)$tr['id_image'] : '').'.'.$this->imageType;

					$this->_list[$index][$key] = cacheImage($path_to_image, $this->table.'_mini_'.$item_id.'.'.$this->imageType, 45, $this->imageType);
				}
				elseif (isset($params['icon']) && (isset($params['icon'][$tr[$key]]) || isset($params['icon']['default'])))
					$this->_list[$index][$key] = isset($params['icon'][$tr[$key]]) ? $params['icon'][$tr[$key]] : $params['icon']['default'];
				elseif (isset($params['float']))
					$this->_list[$index][$key] = rtrim(rtrim($tr[$key], '0'), '.');
				elseif (isset($params['type']))
				{
					if ($params['type'] == 'price')
					{
						$currency = isset($params['currency']) ? Currency::getCurrencyInstance($tr['id_currency']) : $this->context->currency;
						$this->_list[$index][$key] = Tools::displayPrice($tr[$key], $currency, false);
					}
					elseif ($params['type'] == 'date')
						$this->_list[$index][$key] = Tools::displayDate($tr[$key], $this->context->language->id);
					elseif ($params['type'] == 'datetime')
						$this->_list[$index][$key] = Tools::displayDate($tr[$key], $this->context->language->id, true);
				}
				elseif (isset($tr[$key]))
				{
					if ($key == 'price')
						$echo = round($tr[$key], 2);
					elseif (isset($params['maxlength']) && Tools::strlen($tr[$key]) > $params['maxlength'])
						$echo = '<span title="'.$tr[$key].'">'.Tools::substr($tr[$key], 0, $params['maxlength']).'...</span>';
					else
						$echo = $tr[$key];

					$this->_list[$index][$key] = isset($params['callback'])
						?
						call_user_func_array(array((isset($params['callback_object']))
							?
							$params['callback_object']
							:
							$this->context->controller, $params['callback']), array($echo, $tr))
						:
						$echo;
				}
			}
		}

		$this->content_tpl->assign(array(
			'shop_link_type' => $this->shopLinkType,
			'name' => isset($name) ? $name : null,
			'is_dnd_identifier' => $this->is_dnd_identifier,
			'identifier' => $this->identifier,
			'table' => $this->table,
			'token' => $this->token,
			'color_on_bg' => $this->colorOnBackground,
			'id_category' => $id_category,
			'bulk_actions' => $this->bulk_actions,
			'key_to_get' => $key_to_get,
			'positions' => isset($positions) ? $positions : null,
			'order_by' => $this->orderBy,
			'order_way' => $this->orderWay,
			'is_cms' => $this->is_cms,
			'fields_display' => $this->fieldsDisplay,
			'list' => $this->_list,
			'actions' => $this->actions,
			'no_link' => $this->no_link,
			'current_index' => $this->currentIndex,
			'view' => in_array('view', $this->actions),
			'edit' => in_array('edit', $this->actions),
			'has_actions' => !empty($this->actions),
			'has_bulk_actions' => !empty($this->bulk_actions),
			'list_skip_actions' => $this->list_skip_actions,
		));
		return $this->content_tpl->fetch();
	}

	/**
	 * Display duplicate action link
	 */
	protected function displayDuplicateLink($token = null, $id)
	{
		$tpl = $this->context->smarty->createTemplate('helper/list/list_action_duplicate.tpl');
		if (!array_key_exists('Duplicate', self::$cache_lang))
			self::$cache_lang['Duplicate'] = $this->l('Duplicate');

		if (!array_key_exists('Copy images too?', self::$cache_lang))
			self::$cache_lang['Copy images too?'] = $this->l('Copy images too?', __CLASS__, true, false);

		$duplicate = $this->currentIndex.'&'.$this->identifier.'='.$id.'&duplicate'.$this->table;

		$tpl->assign(array(
			'href' => $this->currentIndex.'&'.$this->identifier.'='.$id.'&view'.$this->table.'&token='.($token != null ? $token : $this->token),
			'action' => self::$cache_lang['Duplicate'],
			'confirm' => self::$cache_lang['Copy images too?'],
			'location_ok' => $duplicate.'&token='.($token != null ? $token : $this->token),
			'location_ko' => $duplicate.'&noimage=1&token='.($token ? $token : $this->token).'\\',
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
	 *     fields_display: // attribute $fieldsDisplay of the admin controller
	 *   }
	 * or somethins like this:
	 *   {
	 *     use_parent_structure: false // If false, data need to be an html
	 *     data:
	 *       '<p>My html content</p>',
	 *     fields_display: // attribute $fieldsDisplay of the admin controller
	 *   }
	 */
	protected function displayDetailsLink($token = null, $id)
	{
		$tpl = $this->context->smarty->createTemplate('helper/list/list_action_details.tpl');
		if (!array_key_exists('Details', self::$cache_lang))
			self::$cache_lang['Details'] = $this->l('Details');
		$tpl->assign(array(
			'id' => $id,
			'controller' => str_replace('Controller', '', get_class($this->context->controller)),
			'token' => $this->token,
			'action' => self::$cache_lang['Details'],
		));
		return $tpl->fetch();
	}

	/**
	 * Display view action link
	 */
	protected function displayViewLink($token = null, $id)
	{
		$tpl = $this->context->smarty->createTemplate('helper/list/list_action_view.tpl');
		if (!array_key_exists('View', self::$cache_lang))
			self::$cache_lang['View'] = $this->l('View');

		$tpl->assign(array(
			'href' => $this->currentIndex.'&'.$this->identifier.'='.$id.'&view'.$this->table.'&token='.($token != null ? $token : $this->token),
			'action' => self::$cache_lang['View'],
		));

		return $tpl->fetch();

	}

	/**
	 * Display edit action link
	 */
	protected function displayEditLink($token = null, $id)
	{
		$tpl = $this->context->smarty->createTemplate('helper/list/list_action_edit.tpl');
		if (!array_key_exists('Edit', self::$cache_lang))
			self::$cache_lang['Edit'] = $this->l('Edit');

		$tpl->assign(array(
			'href' => $this->currentIndex.'&'.$this->identifier.'='.$id.'&update'.$this->table.'&token='.($token != null ? $token : $this->token),
			'action' => self::$cache_lang['Edit'],
			'id' => (int)$id
		));

		return $tpl->fetch();

	}

	/**
	 * Display delete action link
	 */
	protected function displayDeleteLink($token = null, $id)
	{
		$tpl = $this->context->smarty->createTemplate('helper/list/list_action_delete.tpl');
		if (!array_key_exists('Delete', self::$cache_lang))
			self::$cache_lang['Delete'] = $this->l('Delete');

		if (!array_key_exists('DeleteItem', self::$cache_lang))
			self::$cache_lang['DeleteItem'] = $this->l('Delete item #', __CLASS__, true, false);

		$tpl->assign(array(
			'href' => $this->currentIndex.'&'.$this->identifier.'='.$id.'&delete'.$this->table.'&token='.($token != null ? $token : $this->token),
			'confirm' => (!is_null($this->specificConfirmDelete) ? '\r'.$this->specificConfirmDelete : self::$cache_lang['DeleteItem'].$id.' ? '),
			'action' => self::$cache_lang['Delete'],
		));

		return $tpl->fetch();

	}

	/**
	 * Display list header (filtering, pagination and column names)
	 */
	public function displayListHeader($token = null)
	{
		$id_cat = Tools::getValue('id_'.($this->is_cms ? 'cms_' : '').'category');

		if (!isset($token) || empty($token))
			$token = $this->token;

		/* Determine total page number */
		if (isset($this->context->cookie->{$this->table.'_pagination'}) && $this->context->cookie->{$this->table.'_pagination'})
			$default_pagination = $this->context->cookie->{$this->table.'_pagination'};
		else
			$default_pagination = $this->_pagination[0];

		$total_pages = ceil($this->listTotal / Tools::getValue('pagination', ($default_pagination)));

		if (!$total_pages) $total_pages = 1;

		$identifier = Tools::getIsset($this->identifier) ? '&'.$this->identifier.'='.(int)Tools::getValue($this->identifier) : '';
		$order = '';
		if (Tools::getIsset($this->table.'Orderby'))
			$order = '&'.$this->table.'Orderby='.urlencode($this->orderBy).'&'.$this->table.'Orderway='.urlencode(strtolower($this->orderWay));

		$action = $this->currentIndex.$identifier.'&token='.$token.$order.'#'.$this->table;

		/* Determine current page number */
		$page = (int)Tools::getValue('submitFilter'.$this->table);
		if (!$page) $page = 1;

		/* Choose number of results per page */
		$selected_pagination = Tools::getValue(
			'pagination',
			isset($this->context->cookie->{$this->table.'_pagination'}) ? $this->context->cookie->{$this->table.'_pagination'} : null
		);

		$this->is_dnd_identifier = array_key_exists($this->identifier,$this->identifiersDnd);
/*
		if ($is_dnd_identifier)
		{
			' id="'.
			if(((int)(Tools::getValue($this->identifiersDnd[$this->identifier], 1)))
				substr($this->identifier,3,strlen($this->identifier)))
		}
		.' class="table'.(
		if ($is_dnd_identifier AND ($this->orderBy != 'position 'AND $this->orderWay != 'DESC'))
			' tableDnD'
		.'" cellpadding="0" cellspacing="0">*/

		// Cleaning links
		if (Tools::getValue($this->table.'Orderby') && Tools::getValue($this->table.'Orderway'))
			$this->currentIndex = preg_replace('/&'.$this->table.'Orderby=([a-z _]*)&'.$this->table.'Orderway=([a-z]*)/i', '', $this->currentIndex);

		if (array_key_exists($this->identifier, $this->identifiersDnd) && (int)Tools::getValue($this->identifiersDnd[$this->identifier], 1))
			$table_id = substr($this->identifier, 3, strlen($this->identifier));

		if (array_key_exists($this->identifier, $this->identifiersDnd) && ($this->orderBy == 'position' && $this->orderWay != 'DESC'))
			$table_dnd = true;

		foreach ($this->fieldsDisplay as $key => $params)
		{
			if (!isset($params['type']))
				$params['type'] = 'text';

			$value = Tools::getValue($this->table.'Filter_'.(array_key_exists('filter_key', $params) ? $params['filter_key'] : $key));

			switch ($params['type'])
			{
				case 'bool':
					break;
				case 'date':
				case 'datetime':
					if (is_string($value))
						$value = unserialize($value);
					if (!Validate::isCleanHtml($value[0]) || !Validate::isCleanHtml($value[1]))
						$value = '';
					$name = $this->table.'Filter_'.(isset($params['filter_key']) ? $params['filter_key'] : $key);
					$name_id = str_replace('!', '__', $name);
					$this->context->controller->addJqueryUI('ui.datepicker');
					break;
				case 'select':
					foreach ($params['list'] as $option_value => $option_display)
					{
						if (isset($_POST[$this->table.'Filter_'.$params['filter_key']])
							&& Tools::getValue($this->table.'Filter_'.$params['filter_key']) == $option_value
							&& Tools::getValue($this->table.'Filter_'.$params['filter_key']) != '')
							$this->fieldsDisplay[$key]['select'][$option_value]['selected'] = 'selected';
					}
					break;
				case 'text':
					if (!Validate::isCleanHtml($value))
						$value = '';

			}
			$params['value'] = $value;
			$this->fieldsDisplay[$key] = $params;
		}

		$this->header_tpl->assign(array(
			'table' => $this->table,
			'currentIndex' => $this->currentIndex,
			'action' => $action,
			'page' => $page,
			'simple_header' => $this->simple_header,
			'title' => $this->title,
			'total_pages' => $total_pages,
			'selected_pagination' => $selected_pagination,
			'pagination' => $this->_pagination,
			'list_total' => $this->listTotal,
			'is_order_position' => array_key_exists($this->identifier, $this->identifiersDnd) && $this->orderBy == 'position',
			'order_way' => $this->orderWay,
			'order_by' => $this->orderBy,
			'token' => $this->token,
			'fields_display' => $this->fieldsDisplay,
			'delete' => in_array('delete', $this->actions),
			'identifier' => $this->identifier,
			'id_cat' => $id_cat,
			'shop_link_type' => $this->shopLinkType,
			'has_actions' => !empty($this->actions),
			'has_bulk_actions' => !empty($this->bulk_actions),
			'toolbar_btn' => $this->toolbar_btn,
			'table_id' => isset($table_id) ? $table_id : null,
			'table_dnd' => isset($table_dnd) ? $table_dnd : null,
			'name' => isset($name) ? $name : null,
			'name_id' => isset($name_id) ? $name_id : null,
			'show_toolbar' => $this->show_toolbar,
		));

		return $this->header_tpl->fetch();
	}

	/**
	 * Close list table and submit button
	 */
	public function displayListFooter($token = null)
	{
		$this->footer_tpl->assign(array(
			'token' => $this->token,
			'table' => $this->table,
			'current' => $this->currentIndex,
			'simple_header' => $this->simple_header,
			'bulk_actions' => $this->bulk_actions,
		));
		return $this->footer_tpl->fetch();
	}

}
