<?php
class HelperListCore extends Helper
{
	/** @var array Cache for query results */
	protected $_list = array();

	/** @var integer Number of results in list */
	protected $_listTotal = 0;

	/** @var array WHERE clause determined by filter fields */
	protected $_filter;

	/** @var array Temporary SQL table WHERE clause determinated by filter fields */
	protected $_tmpTableFilter = '';

	/** @var array Number of results in list per page (used in select field) */
	protected $_pagination = array(20, 50, 100, 300);

	/** @var string ORDER BY clause determined by field/arrows in list header */
	protected $_orderBy;

	/** @var string Default ORDER BY clause when $_orderBy is not defined */
	protected $_defaultOrderBy = false;

	/** @var string Order way (ASC, DESC) determined by arrows in list header */
	protected $_orderWay;

	protected $context;

	public $identifier;

	protected $deleted = 0;

	public static $currentIndex;

	public $token;

	protected $bulk_action;

	protected $is_cms = false;

	protected $is_dnd_identifier = false;

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
	public $noLink = false;

	public $header_tpl = 'list_header.tpl';
	public $content_tpl = 'list_content.tpl';
	public $footer_tpl = 'list_footer.tpl';
	public $view = false;
	public $edit = false;
	public $delete = false;
	public $duplicate = false;
	public $specificConfirmDelete;
	public $colorOnBackground;

	protected $identifiersDnd = array('id_product' => 'id_product', 'id_category' => 'id_category_to_move','id_cms_category' => 'id_cms_category_to_move', 'id_cms' => 'id_cms', 'id_attribute' => 'id_attribute');

	/**
	 * Return an html list given the data to fill it up
	 *
	 * @param array $list entries to display (rows)
	 * @param array $fieldsDisplay fields (cols)
	 * @return string html
	 */
	public function generateList($list, $fieldsDisplay)
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
		$this->fieldsDisplay = $fieldsDisplay;

		/* Display list header (filtering, pagination and column names) */
		$list_display = $this->displayListHeader();
		if (!sizeof($this->_list))
			$list_display .= '<tr><td class="center" colspan="'.(sizeof($this->fieldsDisplay) + 2).'">'.$this->l('No items found').'</td></tr>';

		/* Show the content of the table */
		$list_display .= $this->displayListContent();

		/* Close list table and submit button */
		$list_display .= $this->displayListFooter();

		return $list_display;
	}

	/**
	 * @TODO refactor
	 *
	 * @param unknown_type $token
	 * @param unknown_type $id
	 * @param unknown_type $value
	 * @param unknown_type $active
	 * @param unknown_type $id_category
	 * @param unknown_type $id_product
	 */
	protected function _displayEnableLink($token, $id, $value, $active,  $id_category = NULL, $id_product = NULL)
	{
	    return '<a href="'.self::$currentIndex.'&'.$this->identifier.'='.$id.'&'.$active.$this->table.
	        ((int)$id_category AND (int)$id_product ? '&id_category='.$id_category : '').'&token='.($token!=NULL ? $token : $this->token).'">
	        <img src="../img/admin/'.($value ? 'enabled.gif' : 'disabled.gif').'"
	        alt="'.($value ? $this->l('Enabled') : $this->l('Disabled')).'" title="'.($value ? $this->l('Enabled') : $this->l('Disabled')).'" /></a>';
	}

	public function displayListContent($token = NULL)
	{
		if (!$this->_list)
			return;

		if ($this->is_dnd_identifier)
			$id_category = (int)(Tools::getValue('id_'.($this->is_cms ? 'cms_' : '').'category', '1'));
		else
			$id_category = 1; // default categ

		if (isset($this->fieldsDisplay['position']))
		{
			$positions = array_map(create_function('$elem', 'return (int)($elem[\'position\']);'), $this->_list);
			sort($positions);
		}

		$key_to_get = 'id_'.($this->is_cms ? 'cms_' : '').'category'.(in_array($this->identifier, array('id_category', 'id_cms_category')) ? '_parent' : '');

		$fields = array();

		foreach ($this->_list AS $index => $tr)
		{
			$id = $tr[$this->identifier];

			if ($this->shopLinkType)
				$this->_list[$index]['short_shop_name'] = (Tools::strlen($tr['shop_name']) > 15) ? Tools::substr($tr['shop_name'], 0, 15).'...' : $tr['shop_name'];

			$has_actions = ($this->edit OR $this->delete OR ($this->view AND $this->view !== 'noActionColumn')) ? true : false;

			if ($has_actions)
			{
				if ($this->view)
					$this->_list[$index]['view'] = $this->_displayViewLink($token, $id);
				if ($this->edit)
					$this->_list[$index]['edit'] = $this->_displayEditLink($token, $id);
				if ($this->delete AND (!isset($this->_listSkipDelete) OR !in_array($id, $this->_listSkipDelete)))
					$this->_list[$index]['delete'] = $this->_displayDeleteLink($token, $id);
				if ($this->duplicate)
					$this->_list[$index]['duplicate'] = $this->_displayDuplicate($token, $id);
			}

			foreach ($this->fieldsDisplay AS $key => $params)
			{
				$tmp = explode('!', $key);
				$key = isset($tmp[1]) ? $tmp[1] : $tmp[0];

				if (isset($params['active']))
					$this->_list[$index][$key] = $this->_displayEnableLink($this->token, $tr['identifier'], $tr[$key], $params['active'], Tools::getValue('id_category'), Tools::getValue('id_product'));
				elseif (isset($params['activeVisu']))
					$this->_list[$index][$key] = (bool)$tr[$key];
				elseif (isset($params['position']))
				{
					$this->_list[$index][$key] = array(
						'position' => $tr[$key],
						'position_url_down' => self::$currentIndex.
							'&'.$key_to_get.'='.(int)($id_category).'&'.$this->identifiersDnd[$this->identifier].'='.$id.
							'&way=1&position='.(int)($tr['position'] + 1).'&token='.$this->token,
						'position_url_up' => self::$currentIndex.
							'&'.$key_to_get.'='.(int)($id_category).'&'.$this->identifiersDnd[$this->identifier].'='.$id.
							'&way=0&position='.(int)($tr['position'] - 1).'&token='.$this->token
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
						$path_to_image = _PS_IMG_DIR_.$params['image'].'/'.$item_id.(isset($tr['id_image']) ? '-'.(int)($tr['id_image']) : '').'.'.$this->imageType;

					$this->_list[$index][$key] = cacheImage($path_to_image, $this->table.'_mini_'.$item_id.'.'.$this->imageType, 45, $this->imageType);
				}
				elseif (isset($params['icon']) AND (isset($params['icon'][$tr[$key]]) OR isset($params['icon']['default'])))
					$this->_list[$index][$key] = isset($params['icon'][$tr[$key]]) ? $params['icon'][$tr[$key]] : $params['icon']['default'];
	            elseif (isset($params['price']))
					$this->_list[$index][$key] =  Tools::displayPrice($tr[$key], (isset($params['currency']) ? Currency::getCurrencyInstance($tr['id_currency']) : $this->context->currency), false);
				elseif (isset($params['float']))
					$this->_list[$index][$key] =  rtrim(rtrim($tr[$key], '0'), '.');
				elseif (isset($params['type']) AND $params['type'] == 'date')
					$this->_list[$index][$key] = Tools::displayDate($tr[$key], $this->context->language->id);
				elseif (isset($params['type']) AND $params['type'] == 'datetime')
					$this->_list[$index][$key] = Tools::displayDate($tr[$key], $this->context->language->id, true);
				elseif (isset($tr[$key]))
				{
					if ($key == 'price')
						$echo = round($tr[$key], 2);
					else if (isset($params['maxlength']) && Tools::strlen($tr[$key]) > $params['maxlength'])
						$echo = '<span title="'.$tr[$key].'">'.Tools::substr($tr[$key], 0, $params['maxlength']).'...</span>';
					else
						$echo = $tr[$key];

					$this->_list[$index][$key] = isset($params['callback']) ? call_user_func_array(array((isset($params['callback_object'])) ? $params['callback_object'] : $this->className, $params['callback']), array($echo, $tr)) : $echo;
				}
			}
		}

		$this->context->smarty->assign(array(
			'is_dnd_identifier' => $this->is_dnd_identifier,
			'color_on_bg' => $this->colorOnBackground,
			'id_category' => $id_category,
			'bulk_action' => $this->bulk_action,
			'key_to_get' => $key_to_get,
			'positions' => isset($positions) ? $positions : NULL,
			'is_cms' => $this->is_cms,
			'fields_display' => $this->fieldsDisplay,
			'list' => $this->_list,
			'no_link' => $this->noLink,
			'bulk_action' => $this->bulk_action,
			'current_index' => self::$currentIndex,
			'view' => $this->view,
			'edit' => $this->edit,
			'has_actions' => $has_actions,

		));
		return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/list_content.tpl');
	}

	protected function displayAddButton()
	{
		echo '<br /><a href="'.self::$currentIndex.'&add'.$this->table.'&token='.$this->token.'"><img src="../img/admin/add.gif" border="0" /> '.$this->l('Add new').'</a><br /><br />';
	}

    protected function _displayDuplicate($token = NULL, $id)
    {
        $_cacheLang['Duplicate'] = $this->l('Duplicate');
		$_cacheLang['Copy images too?'] = $this->l('Copy images too?', __CLASS__, TRUE, FALSE);

    	$duplicate = self::$currentIndex.'&'.$this->identifier.'='.$id.'&duplicate'.$this->table;

		return '
			<a class="pointer" onclick="if (confirm(\''.$_cacheLang['Copy images too?'].'\')) document.location = \''.$duplicate.'&token='.($token!=NULL ? $token : $this->token).'\'; else document.location = \''.$duplicate.'&noimage=1&token='.($token ? $token : $this->token).'\';">
    		<img src="../img/admin/duplicate.png" alt="'.$_cacheLang['Duplicate'].'" title="'.$_cacheLang['Duplicate'].'" /></a>';
    }

	protected function _displayViewLink($token = NULL, $id)
	{
		$_cacheLang['View'] = $this->l('View');

    	return '
			<a href="'.self::$currentIndex.'&'.$this->identifier.'='.$id.'&view'.$this->table.'&token='.($token!=NULL ? $token : $this->token).'">
			<img src="../img/admin/details.gif" alt="'.$_cacheLang['View'].'" title="'.$_cacheLang['View'].'" /></a>';
	}

	protected function _displayEditLink($token = NULL, $id)
	{
		$_cacheLang['Edit'] = $this->l('Edit');

		return '
    		<a href="'.self::$currentIndex.'&'.$this->identifier.'='.$id.'&update'.$this->table.'&token='.($token!=NULL ? $token : $this->token).'">
    		<img src="../img/admin/edit.gif" alt="" title="'.$_cacheLang['Edit'].'" /></a>';
	}

	protected function _displayDeleteLink($token = NULL, $id)
	{
		$_cacheLang['Delete'] = $this->l('Delete');
		$_cacheLang['DeleteItem'] = $this->l('Delete item #', __CLASS__, TRUE, FALSE);

		return '
			<a href="'.self::$currentIndex.'&'.$this->identifier.'='.$id.'&delete'.$this->table.'&token='.($token!=NULL ? $token : $this->token).'" onclick="return confirm(\''.$_cacheLang['DeleteItem'].$id.' ?'.
    				(!is_null($this->specificConfirmDelete) ? '\r'.$this->specificConfirmDelete : '').'\');">
			<img src="../img/admin/delete.gif" alt="'.$_cacheLang['Delete'].'" title="'.$_cacheLang['Delete'].'" /></a>';
	}

	/**
	 * Display list header (filtering, pagination and column names)
	 */
	public function displayListHeader($token = NULL)
	{
		$id_cat = Tools::getValue('id_'.($this->is_cms ? 'cms_' : '').'category');

		if (!isset($token) OR empty($token))
			$token = $this->token;

		/* Determine total page number */
		$total_pages = ceil($this->_listTotal / Tools::getValue('pagination', (isset($this->context->cookie->{$this->table.'_pagination'}) ? $this->context->cookie->{$this->table.'_pagination'} : $this->_pagination[0])));
		if (!$total_pages) $total_pages = 1;

		$action = self::$currentIndex
				  .(Tools::getIsset($this->identifier) ? '&'.$this->identifier.'='.(int)(Tools::getValue($this->identifier)) : '')
				  .'&token='.$token
				  .(Tools::getIsset($this->table.'Orderby') ? '&'.$this->table.'Orderby='.urlencode($this->_orderBy).'&'.$this->table.'Orderway='.urlencode(strtolower($this->_orderWay)) : '')
				  .'#'.$this->table;

		/* Determine current page number */
		$page = (int)(Tools::getValue('submitFilter'.$this->table));
		if (!$page) $page = 1;

		/* Choose number of results per page */
		$selected_pagination = Tools::getValue('pagination', (isset($this->context->cookie->{$this->table.'_pagination'}) ? $this->context->cookie->{$this->table.'_pagination'} : NULL));

		/*$is_dnd_identifier = array_key_exists($this->identifier,$this->identifiersDnd);

		if ($is_dnd_identifier)
		{
			' id="'.
			if(((int)(Tools::getValue($this->identifiersDnd[$this->identifier], 1)))
				substr($this->identifier,3,strlen($this->identifier)))
		}
		.' class="table'.(
		if ($is_dnd_identifier AND ($this->_orderBy != 'position 'AND $this->_orderWay != 'DESC'))
			' tableDnD'
		.'" cellpadding="0" cellspacing="0">*/

		// Cleaning links
		if (Tools::getValue($this->table.'Orderby') && Tools::getValue($this->table.'Orderway'))
			self::$currentIndex = preg_replace('/&'.$this->table.'Orderby=([a-z _]*)&'.$this->table.'Orderway=([a-z]*)/i', '', self::$currentIndex);

		// Check if object can be modified, deleted or detailed
		$has_actions = ($this->edit OR $this->delete OR ($this->view AND $this->view !== 'noActionColumn')) ? true : false;

		if (array_key_exists($this->identifier,$this->identifiersDnd) && (int)(Tools::getValue($this->identifiersDnd[$this->identifier], 1)))
			$table_id = substr($this->identifier,3,strlen($this->identifier));

		if (array_key_exists($this->identifier,$this->identifiersDnd) && ($this->_orderBy != 'position' && $this->_orderWay != 'DESC'))
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
					if (!Validate::isCleanHtml($value[0]) OR !Validate::isCleanHtml($value[1]))
						$value = '';
					$name = $this->table.'Filter_'.(isset($params['filter_key']) ? $params['filter_key'] : $key);
					$name_id = str_replace('!', '__', $name);
					$this->includeDatepicker(array($name_id.'_0', $name_id.'_1'));
					break;
				case 'select':
					foreach ($params['select'] AS $option_value => $option_display)
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

		$this->context->smarty->assign(array(
			'table' => $this->table,
			'currentIndex' => self::$currentIndex,
			'action' => $action,
			'page' => $page,
			'total_pages' => $total_pages,
			'selected_pagination' => $selected_pagination,
			'pagination' => $this->_pagination,
			'list_total' => $this->_listTotal,
			'is_order_position' => array_key_exists($this->identifier, $this->identifiersDnd) && $this->_orderBy == 'position',
			'order_way' => $this->_orderWay,
			'token' => $this->token,
			'fields_display' => $this->fieldsDisplay,
			'delete' => $this->delete,
			'identifier' => $this->identifier,
			'id_cat' => $id_cat,
			'shop_link_type' => $this->shopLinkType,
			'has_actions' => $has_actions,
			'add_button' => $this->edit AND (!isset($this->noAdd) OR !$this->noAdd),
			'table_id' => isset($table_id) ? $table_id : null,
			'table_dnd' => isset($table_dnd) ? $table_dnd : null,
			'name' => isset($name) ? $name : null,
			'name_id' => isset($name_id) ? $name_id : null,
		));

		return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/list_header.tpl');
	}

	/**
	 * Close list table and submit button
	 */
	public function displayListFooter($token = NULL)
	{
		$this->context->smarty->assign(array(
			'token' => $this->token,
		));
		return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/list_footer.tpl');
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
}