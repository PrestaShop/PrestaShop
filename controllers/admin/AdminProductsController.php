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

class AdminProductsControllerCore extends AdminController
{
	/** @var integer Max image size for upload
	 * As of 1.5 it is recommended to not set a limit to max image size
	 */
	protected $max_file_size = null;
	protected $max_image_size = null;

	protected $_category;
	/**
	 * @var string name of the tab to display
	 */
	protected $tab_display;
	protected $tab_display_module;

	/**
	 * The order in the array decides the order in the list of tab. If an element's value is a number, it will be preloaded.
	 * The tabs are preloaded from the smallest to the highest number.
	 * @var array Product tabs.
	 */
	protected $available_tabs = array();

	protected $default_tab = 'Informations';

	protected $available_tabs_lang = array();

	protected $position_identifier = 'id_product';

	protected $submitted_tabs;
	
	protected $id_current_category;

	public function __construct()
	{
		$this->bootstrap = true;
		$this->table = 'product';
		$this->className = 'Product';
		$this->lang = true;
		$this->explicitSelect = true;
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		if (!Tools::getValue('id_product'))
			$this->multishop_context_group = false;

		parent::__construct();

		$this->imageType = 'jpg';
		$this->_defaultOrderBy = 'position';
		$this->max_file_size = (int)(Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1000000);
		$this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
		$this->allow_export = true;

		// @since 1.5 : translations for tabs
		$this->available_tabs_lang = array (
			'Informations' => $this->l('Information'),
			'Pack' => $this->l('Pack'),
			'VirtualProduct' => $this->l('Virtual Product'),
			'Prices' => $this->l('Prices'),
			'Seo' => $this->l('SEO'),
			'Images' => $this->l('Images'),
			'Associations' => $this->l('Associations'),
			'Shipping' => $this->l('Shipping'),
			'Combinations' => $this->l('Combinations'),
			'Features' => $this->l('Features'),
			'Customization' => $this->l('Customization'),
			'Attachments' => $this->l('Attachments'),
			'Quantities' => $this->l('Quantities'),
			'Suppliers' => $this->l('Suppliers'),
			'Warehouses' => $this->l('Warehouses'),
		);

		$this->available_tabs = array('Quantities' => 6, 'Warehouses' => 14);
		if ($this->context->shop->getContext() != Shop::CONTEXT_GROUP)
			$this->available_tabs = array_merge($this->available_tabs, array(
				'Informations' => 0,
				'Pack' => 7,
				'VirtualProduct' => 8,
				'Prices' => 1,
				'Seo' => 2,
				'Associations' => 3,
				'Images' => 9,
				'Shipping' => 4,
				'Combinations' => 5,
				'Features' => 10,
				'Customization' => 11,
				'Attachments' => 12,
				'Suppliers' => 13,
			));

		// Sort the tabs that need to be preloaded by their priority number
		asort($this->available_tabs, SORT_NUMERIC);

		/* Adding tab if modules are hooked */
		$modules_list = Hook::getHookModuleExecList('displayAdminProductsExtra');
		if (is_array($modules_list) && count($modules_list) > 0)
			foreach ($modules_list as $m)
			{
				$this->available_tabs['Module'.ucfirst($m['module'])] = 23;
				$this->available_tabs_lang['Module'.ucfirst($m['module'])] = Module::getModuleName($m['module']);
			}

		if (Tools::getValue('reset_filter_category'))
			$this->context->cookie->id_category_products_filter = false;
		if (Shop::isFeatureActive() && $this->context->cookie->id_category_products_filter)
		{
			$category = new Category((int)$this->context->cookie->id_category_products_filter);
			if (!$category->inShop())
			{
				$this->context->cookie->id_category_products_filter = false;
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts'));
			}
		}
		/* Join categories table */
		if ($id_category = (int)Tools::getValue('productFilter_cl!name'))
		{
			$this->_category = new Category((int)$id_category);
			$_POST['productFilter_cl!name'] = $this->_category->name[$this->context->language->id];
		}
		else
		{
			if ($id_category = (int)Tools::getValue('id_category'))
			{
				$this->id_current_category = $id_category;
				$this->context->cookie->id_category_products_filter = $id_category;	
			}
			elseif ($id_category = $this->context->cookie->id_category_products_filter)
				$this->id_current_category = $id_category;
			if ($this->id_current_category)
				$this->_category = new Category((int)$this->id_current_category);
			else
				$this->_category = new Category();
		}
			
		$join_category = false;
		if (Validate::isLoadedObject($this->_category) && empty($this->_filter))
			$join_category = true;

		$this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = a.`id_product` '.(!Shop::isFeatureActive() ? ' AND i.cover=1' : '').')';
		if (Shop::isFeatureActive())
		{
			$alias = 'sa';
			$alias_image = 'image_shop';
			if (Shop::getContext() == Shop::CONTEXT_SHOP)
			{
				$this->_join .= ' JOIN `'._DB_PREFIX_.'product_shop` sa ON (a.`id_product` = sa.`id_product` AND sa.id_shop = '.(int)$this->context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON ('.$alias.'.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = '.(int)$this->context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'shop` shop ON (shop.id_shop = '.(int)$this->context->shop->id.') 
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_image` = i.`id_image` AND image_shop.`cover` = 1 AND image_shop.id_shop='.(int)$this->context->shop->id.')';
			}
			else
			{
				$this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_shop` sa ON (a.`id_product` = sa.`id_product` AND sa.id_shop = a.id_shop_default)
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON ('.$alias.'.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = a.id_shop_default)
				LEFT JOIN `'._DB_PREFIX_.'shop` shop ON (shop.id_shop = a.id_shop_default) 
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_image` = i.`id_image` AND image_shop.`cover` = 1 AND image_shop.id_shop=a.id_shop_default)';
			}
			$this->_select .= 'shop.name as shopname, ';
		}
		else
		{
			$alias = 'a';
			$alias_image = 'i';
			$this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON ('.$alias.'.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = 1)';
		}

		$this->_select .= 'MAX('.$alias_image.'.id_image) id_image,';
		
		$this->_join .= ($join_category ? 'INNER JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = a.`id_product` AND cp.`id_category` = '.(int)$this->_category->id.')' : '').'
		LEFT JOIN `'._DB_PREFIX_.'stock_available` sav ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0
		'.StockAvailable::addSqlShopRestriction(null, null, 'sav').') ';
		$this->_select .= 'cl.name `name_category` '.($join_category ? ', cp.`position`' : '').', '.$alias.'.`price`, 0 AS price_final, sav.`quantity` as sav_quantity, '.$alias.'.`active`';

		$this->_group = 'GROUP BY '.$alias.'.id_product';

		$this->fields_list = array();
		$this->fields_list['id_product'] = array(
			'title' => $this->l('ID'),
			'align' => 'center',
		);
		$this->fields_list['image'] = array(
			'title' => $this->l('Photo'),
			'align' => 'center',
			'image' => 'p',
			'orderby' => false,
			'filter' => false,
			'search' => false
		);
		$this->fields_list['name'] = array(
			'title' => $this->l('Name'),
			'filter_key' => 'b!name'
		);
		$this->fields_list['reference'] = array(
			'title' => $this->l('Reference'),
			'align' => 'left',
		);

		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP)
			$this->fields_list['shopname'] = array(
				'title' => $this->l('Default shop:'),
				'filter_key' => 'shop!name',
			);
		else
			$this->fields_list['name_category'] = array(
				'title' => $this->l('Category'),
				'width' => 'auto',
				'filter_key' => 'cl!name',
			);
		$this->fields_list['price'] = array(
			'title' => $this->l('Base price'),
			'type' => 'price',
			'align' => 'right',
			'filter_key' => 'a!price'
		);
		$this->fields_list['price_final'] = array(
			'title' => $this->l('Final price'),
			'type' => 'price',
			'align' => 'right',
			'havingFilter' => true,
			'orderby' => false
		);
		$this->fields_list['sav_quantity'] = array(
			'title' => $this->l('Quantity'),
			'align' => 'right',
			'filter_key' => 'sav!quantity',
			'orderby' => true,
			//'hint' => $this->l('This is the quantity available in the current shop/group.'),
		);
		$this->fields_list['active'] = array(
			'title' => $this->l('Status'),
			'active' => 'status',
			'filter_key' => $alias.'!active',
			'align' => 'center',
			'type' => 'bool',
			'orderby' => false
		);

		if ((int)$this->id_current_category)
			$this->fields_list['position'] = array(
				'title' => $this->l('Position'),
				'filter_key' => 'cp!position',
				'align' => 'center',
				'position' => 'position'
			);
	}
	
	protected function _cleanMetaKeywords($keywords)
	{
		if (!empty($keywords) && $keywords != '')
		{
			$out = array();
			$words = explode(',', $keywords);
			foreach ($words as $word_item)
			{
				$word_item = trim($word_item);
				if (!empty($word_item) && $word_item != '')
					$out[] = $word_item;
			}
			return ((count($out) > 0) ? implode(',', $out) : '');
		}
		else
			return '';
	}

	protected function copyFromPost(&$object, $table)
	{
		parent::copyFromPost($object, $table);
		if (get_class($object) != 'Product')
			return;

		/* Additional fields */
		$languages = Language::getLanguages(false);
		foreach ($languages as $language)
			if (isset($_POST['meta_keywords_'.$language['id_lang']]))
			{
				$_POST['meta_keywords_'.$language['id_lang']] = $this->_cleanMetaKeywords(Tools::strtolower($_POST['meta_keywords_'.$language['id_lang']]));
				// preg_replace('/ *,? +,* /', ',', strtolower($_POST['meta_keywords_'.$language['id_lang']]));
				$object->meta_keywords[$language['id_lang']] = $_POST['meta_keywords_'.$language['id_lang']];
			}
		$_POST['width'] = empty($_POST['width']) ? '0' : str_replace(',', '.', $_POST['width']);
		$_POST['height'] = empty($_POST['height']) ? '0' : str_replace(',', '.', $_POST['height']);
		$_POST['depth'] = empty($_POST['depth']) ? '0' : str_replace(',', '.', $_POST['depth']);
		$_POST['weight'] = empty($_POST['weight']) ? '0' : str_replace(',', '.', $_POST['weight']);

		if (Tools::getIsset('unit_price') != null)
			$object->unit_price = str_replace(',', '.', Tools::getValue('unit_price'));
		if (Tools::getIsset('ecotax') != null)
			$object->ecotax = str_replace(',', '.', Tools::getValue('ecotax'));
		$object->available_for_order = (int)Tools::getValue('available_for_order');
		$object->show_price = $object->available_for_order ? 1 : (int)Tools::getValue('show_price');
		$object->on_sale = (int)Tools::getValue('on_sale');
		$object->online_only = (int)Tools::getValue('online_only');
	}

	public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = null)
	{
		$orderByPriceFinal = (empty($orderBy) ? ($this->context->cookie->__get($this->table.'Orderby') ? $this->context->cookie->__get($this->table.'Orderby') : 'id_'.$this->table) : $orderBy);
		$orderWayPriceFinal = (empty($orderWay) ? ($this->context->cookie->__get($this->table.'Orderway') ? $this->context->cookie->__get($this->table.'Orderby') : 'ASC') : $orderWay);
		if ($orderByPriceFinal == 'price_final')
		{
			$orderBy = 'id_'.$this->table;
			$orderWay = 'ASC';
		}
		parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $this->context->shop->id);

		/* update product quantity with attributes ...*/
		$nb = count($this->_list);
		if ($this->_list)
		{
			/* update product final price */
			for ($i = 0; $i < $nb; $i++)
			{
				// convert price with the currency from context
				$this->_list[$i]['price'] = Tools::convertPrice($this->_list[$i]['price'], $this->context->currency, true, $this->context);
				$this->_list[$i]['price_tmp'] = Product::getPriceStatic($this->_list[$i]['id_product'], true, null, 2, null, false, true, 1, true);
			}
		}

		if ($orderByPriceFinal == 'price_final')
		{
			if (strtolower($orderWayPriceFinal) == 'desc')
				uasort($this->_list, 'cmpPriceDesc');
			else
				uasort($this->_list, 'cmpPriceAsc');
		}
		for ($i = 0; $this->_list && $i < $nb; $i++)
		{
			$this->_list[$i]['price_final'] = $this->_list[$i]['price_tmp'];
			unset($this->_list[$i]['price_tmp']);
		}
	}

	protected function loadObject($opt = false)
	{
		$result = parent::loadObject($opt);
		if ($result && Validate::isLoadedObject($this->object))
		{
			if (Shop::getContext() == Shop::CONTEXT_SHOP && !$this->object->isAssociatedToShop())
			{
				$default_product = new Product((int)$this->object->id, false, null, (int)$this->object->id_shop_default);
				$def = ObjectModel::getDefinition($this->object);
				foreach ($def['fields'] as $field_name => $row)
					$this->object->$field_name = ObjectModel::formatValue($default_product->$field_name, $def['fields'][$field_name]['type']);
			}
			$this->object->loadStockData();
		}
		return $result;
	}

	public function ajaxProcessGetCountriesOptions()
	{
		if (!$res = Country::getCountriesByIdShop((int)Tools::getValue('id_shop'), (int)$this->context->language->id))
			return ;

		$tpl = $this->createTemplate('specific_prices_shop_update.tpl');
		$tpl->assign(array(
			'option_list' => $res,
			'key_id' => 'id_country',
			'key_value' => 'name'
			)
		);

		$this->content = $tpl->fetch();
	}
	
	public function ajaxProcessGetCurrenciesOptions()
	{
		if (!$res = Currency::getCurrenciesByIdShop((int)Tools::getValue('id_shop')))
			return ;

		$tpl = $this->createTemplate('specific_prices_shop_update.tpl');
		$tpl->assign(array(
			'option_list' => $res,
			'key_id' => 'id_currency',
			'key_value' => 'name'
			)
		);

		$this->content = $tpl->fetch();
	}
	
	public function ajaxProcessGetGroupsOptions()
	{
		if (!$res = Group::getGroups((int)$this->context->language->id, (int)Tools::getValue('id_shop')))
			return ;

		$tpl = $this->createTemplate('specific_prices_shop_update.tpl');
		$tpl->assign(array(
			'option_list' => $res,
			'key_id' => 'id_group',
			'key_value' => 'name'
			)
		);

		$this->content = $tpl->fetch();
	}

	public function ajaxProcessDeleteVirtualProduct()
	{
		if (!($id_product_download = ProductDownload::getIdFromIdProduct((int)Tools::getValue('id_product'))))
			$this->jsonError($this->l('Cannot retrieve file'));
		else
		{
			$product_download = new ProductDownload((int)$id_product_download);
			if (!$product_download->deleteFile((int)$id_product_download))
				$this->jsonError($this->l('Cannot delete file'));
			else
				$this->jsonConfirmation($this->_conf[1]);
		}
	}

	/**
	 * Upload new attachment
	 *
	 * @return void
	 */
	public function processAddAttachments()
	{
		$languages = Language::getLanguages(false);
		$is_attachment_name_valid = false;
		foreach ($languages as $language)
		{
			$attachment_name_lang = Tools::getValue('attachment_name_'.(int)($language['id_lang']));
			if (Tools::strlen($attachment_name_lang ) > 0)
				$is_attachment_name_valid = true;

			if (!Validate::isGenericName(Tools::getValue('attachment_name_'.(int)($language['id_lang']))))
				$this->errors[] = Tools::displayError('Invalid Name');
			elseif (Tools::strlen(Tools::getValue('attachment_name_'.(int)($language['id_lang']))) > 32)
				$this->errors[] = sprintf(Tools::displayError('The name is too long (%d chars max).'), 32);
			if (!Validate::isCleanHtml(Tools::getValue('attachment_description_'.(int)($language['id_lang']))))
				$this->errors[] = Tools::displayError('Invalid description');
		}
		if (!$is_attachment_name_valid)
			$this->errors[] = Tools::displayError('An attachment name is required.');

		if (empty($this->errors))
		{
			if (isset($_FILES['attachment_file']) && is_uploaded_file($_FILES['attachment_file']['tmp_name']))
			{
				if ($_FILES['attachment_file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024))
					$this->errors[] = sprintf(
						$this->l('The file is too large. Maximum size allowed is: %1$d kB. The file you\'re trying to upload is: %2$d kB.'),
						(Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024),
						number_format(($_FILES['attachment_file']['size'] / 1024), 2, '.', '')
					);
				else
				{
					do $uniqid = sha1(microtime());
					while (file_exists(_PS_DOWNLOAD_DIR_.$uniqid));
					if (!copy($_FILES['attachment_file']['tmp_name'], _PS_DOWNLOAD_DIR_.$uniqid))
						$this->errors[] = $this->l('File copy failed');
					@unlink($_FILES['attachment_file']['tmp_name']);
				}
			}
			elseif ((int)$_FILES['attachment_file']['error'] === 1)
			{
				$max_upload = (int)ini_get('upload_max_filesize');
				$max_post = (int)ini_get('post_max_size');
				$upload_mb = min($max_upload, $max_post);
				$this->errors[] = sprintf(
					$this->l('The file %1$s exceeds the size allowed by the server. The limit is set to %2$d MB.'),
					'<b>'.$_FILES['attachment_file']['name'].'</b> ',
					'<b>'.$upload_mb.'</b>'
				);
			}
			else
				$this->errors[] = Tools::displayError('The file is missing.');

			if (empty($this->errors) && isset($uniqid))
			{
				$attachment = new Attachment();
				foreach ($languages as $language)
				{
					if (Tools::getIsset('attachment_name_'.(int)$language['id_lang']))
						$attachment->name[(int)$language['id_lang']] = Tools::getValue('attachment_name_'.(int)$language['id_lang']);
					if (Tools::getIsset('attachment_description_'.(int)$language['id_lang']))
						$attachment->description[(int)$language['id_lang']] = Tools::getValue('attachment_description_'.(int)$language['id_lang']);
				}
				$attachment->file = $uniqid;
				$attachment->mime = $_FILES['attachment_file']['type'];
				$attachment->file_name = $_FILES['attachment_file']['name'];
				if (empty($attachment->mime) || Tools::strlen($attachment->mime) > 128)
					$this->errors[] = Tools::displayError('Invalid file extension');
				if (!Validate::isGenericName($attachment->file_name))
					$this->errors[] = Tools::displayError('Invalid file name');
				if (Tools::strlen($attachment->file_name) > 128)
					$this->errors[] = Tools::displayError('The file name is too long.');
				if (empty($this->errors))
				{
					$res = $attachment->add();
					if (!$res)
						$this->errors[] = Tools::displayError('This attachment was unable to be loaded into the database.');
					else
					{
						$id_product = (int)Tools::getValue($this->identifier);
						$res = $attachment->attachProduct($id_product);
						if (!$res)
							$this->errors[] = Tools::displayError('We were unable to associate this attachment to a product.');
					}
				}
				else
					$this->errors[] = Tools::displayError('Invalid file');
			}
		}
	}

	/**
	 * Attach an existing attachment to the product
	 *
	 * @return void
	 */
	public function processAttachments()
	{
		if ($id = (int)Tools::getValue($this->identifier))
		{
			$attachments = trim(Tools::getValue('arrayAttachments'), ',');
			$attachments = explode(',', $attachments);
			if (!Attachment::attachToProduct($id, $attachments))
				$this->errors[] = Tools::displayError('An error occurred while saving product attachments.');
		}
	}

	public function processDuplicate()
	{
		if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
		{
			$id_product_old = $product->id;
			if (empty($product->price) && Shop::getContext() == Shop::CONTEXT_GROUP)
			{
				$shops = ShopGroup::getShopsFromGroup(Shop::getContextShopGroupID());
				foreach ($shops as $shop)
					if ($product->isAssociatedToShop($shop['id_shop']))
					{
						$product_price = new Product($id_product_old, false, null, $shop['id_shop']);
						$product->price = $product_price->price;
					}
			}
			unset($product->id);
			unset($product->id_product);
			$product->indexed = 0;
			$product->active = 0;
			if ($product->add()
			&& Category::duplicateProductCategories($id_product_old, $product->id)
			&& ($combination_images = Product::duplicateAttributes($id_product_old, $product->id)) !== false
			&& GroupReduction::duplicateReduction($id_product_old, $product->id)
			&& Product::duplicateAccessories($id_product_old, $product->id)
			&& Product::duplicateFeatures($id_product_old, $product->id)
			&& Product::duplicateSpecificPrices($id_product_old, $product->id)
			&& Pack::duplicate($id_product_old, $product->id)
			&& Product::duplicateCustomizationFields($id_product_old, $product->id)
			&& Product::duplicateTags($id_product_old, $product->id)
			&& Product::duplicateDownload($id_product_old, $product->id))
			{
				if ($product->hasAttributes())
					Product::updateDefaultAttribute($product->id);

				if (!Tools::getValue('noimage') && !Image::duplicateProductImages($id_product_old, $product->id, $combination_images))
					$this->errors[] = Tools::displayError('An error occurred while copying images.');
				else
				{
					Hook::exec('actionProductAdd', array('product' => $product));
					if (in_array($product->visibility, array('both', 'search')) && Configuration::get('PS_SEARCH_INDEXATION'))
						Search::indexation(false, $product->id);
					$this->redirect_after = self::$currentIndex.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&conf=19&token='.$this->token;
				}
			}
			else
				$this->errors[] = Tools::displayError('An error occurred while creating an object.');
		}
	}

	public function processDelete()
	{
		if (Validate::isLoadedObject($object = $this->loadObject()) && isset($this->fieldImageSettings))
		{
			// check if request at least one object with noZeroObject
			if (isset($object->noZeroObject) && count($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1)
				$this->errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
			else
			{
				/*
				 * @since 1.5.0
				 * It is NOT possible to delete a product if there are currently:
				 * - physical stock for this product
				 * - supply order(s) for this product
				 */
				if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $object->advanced_stock_management)
				{
					$stock_manager = StockManagerFactory::getManager();
					$physical_quantity = $stock_manager->getProductPhysicalQuantities($object->id, 0);
					$real_quantity = $stock_manager->getProductRealQuantities($object->id, 0);
					if ($physical_quantity > 0 || $real_quantity > $physical_quantity)
						$this->errors[] = Tools::displayError('You cannot delete this product because there\'s physical stock left.');
				}

				if (!count($this->errors))
				{
					if ($object->delete())
					{
						$id_category = (int)Tools::getValue('id_category');
						$category_url = empty($id_category) ? '' : '&id_category='.(int)$id_category;
						$this->redirect_after = self::$currentIndex.'&conf=1&token='.$this->token.$category_url;
					}
					else
						$this->errors[] = Tools::displayError('An error occurred during deletion.');
				}
			}
		}
		else
			$this->errors[] = Tools::displayError('An error occurred while deleting the object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
	}

	public function processImage()
	{
		$id_image = (int)Tools::getValue('id_image');
		$image = new Image((int)$id_image);
		if (Validate::isLoadedObject($image))
		{
			/* Update product image/legend */
			// @todo : move in processEditProductImage
			if (Tools::getIsset('editImage'))
			{
				if ($image->cover)
					$_POST['cover'] = 1;

				$_POST['id_image'] = $image->id;
			}

			/* Choose product cover image */
			elseif (Tools::getIsset('coverImage'))
			{
				Image::deleteCover($image->id_product);
				$image->cover = 1;
				if (!$image->update())
					$this->errors[] = Tools::displayError('You cannot change the product\'s cover image.');
				else
				{
					$productId = (int)Tools::getValue('id_product');
					@unlink(_PS_TMP_IMG_DIR_.'product_'.$productId.'.jpg');
					@unlink(_PS_TMP_IMG_DIR_.'product_mini_'.$productId.'.jpg');
					$this->redirect_after = self::$currentIndex.'&id_product='.$image->id_product.'&id_category='.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&action=Images&addproduct'.'&token='.$this->token;
				}
			}

			/* Choose product image position */
			elseif (Tools::getIsset('imgPosition') && Tools::getIsset('imgDirection'))
			{
				$image->updatePosition(Tools::getValue('imgDirection'), Tools::getValue('imgPosition'));
				$this->redirect_after = self::$currentIndex.'&id_product='.$image->id_product.'&id_category='.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&add'.$this->table.'&action=Images&token='.$this->token;
			}
		}
		else
			$this->errors[] = Tools::displayError('The image could not be found. ');
	}

	protected function processBulkDelete()
	{
		if ($this->tabAccess['delete'] === '1')
		{
			if (is_array($this->boxes) && !empty($this->boxes))
			{
				$object = new $this->className();
	
				if (isset($object->noZeroObject) &&
					// Check if all object will be deleted
					(count(call_user_func(array($this->className, $object->noZeroObject))) <= 1 || count($_POST[$this->table.'Box']) == count(call_user_func(array($this->className, $object->noZeroObject)))))
					$this->errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
				else
				{
					$success = 1;
					$products = Tools::getValue($this->table.'Box');
					if (is_array($products) && ($count = count($products)))
					{
						// Deleting products can be quite long on a cheap server. Let's say 1.5 seconds by product (I've seen it!).
						if (intval(ini_get('max_execution_time')) < round($count * 1.5))
							ini_set('max_execution_time', round($count * 1.5));

						if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
							$stock_manager = StockManagerFactory::getManager();
	
						foreach ($products as $id_product)
						{
							$product = new Product((int)$id_product);
							/*
							 * @since 1.5.0
							 * It is NOT possible to delete a product if there are currently:
							 * - physical stock for this product
							 * - supply order(s) for this product
							 */
							if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management)
							{
								$physical_quantity = $stock_manager->getProductPhysicalQuantities($product->id, 0);
								$real_quantity = $stock_manager->getProductRealQuantities($product->id, 0);
								if ($physical_quantity > 0 || $real_quantity > $physical_quantity)
									$this->errors[] = sprintf(Tools::displayError('You cannot delete the product #%d because there is physical stock left.'), $product->id);
							}
							if (!count($this->errors))
								$success &= $product->delete();
							else
								$success = 0;
						}
					}
					
					if ($success)
					{
						$id_category = (int)Tools::getValue('id_category');
						$category_url = empty($id_category) ? '' : '&id_category='.(int)$id_category;
						$this->redirect_after = self::$currentIndex.'&conf=2&token='.$this->token.$category_url;
					}
					else
						$this->errors[] = Tools::displayError('An error occurred while deleting this selection.');
				}
			}
			else
				$this->errors[] = Tools::displayError('You must select at least one element to delete.');
		}
		else
			$this->errors[] = Tools::displayError('You do not have permission to delete this.');
	}

	public function processProductAttribute()
	{
		// Don't process if the combination fields have not been submitted
		if (!Combination::isFeatureActive() || !Tools::getValue('attribute_combination_list'))
			return;

		if (Validate::isLoadedObject($product = $this->object))
		{
			if ($this->isProductFieldUpdated('attribute_price') && (!Tools::getIsset('attribute_price') || Tools::getIsset('attribute_price') == null))
				$this->errors[] = Tools::displayError('The price attribute is required.');
			if (!Tools::getIsset('attribute_combination_list') || Tools::isEmpty(Tools::getValue('attribute_combination_list')))
				$this->errors[] = Tools::displayError('You must add at least one attribute.');

			if (!count($this->errors))
			{
				if (!isset($_POST['attribute_wholesale_price'])) $_POST['attribute_wholesale_price'] = 0;
				if (!isset($_POST['attribute_price_impact'])) $_POST['attribute_price_impact'] = 0;
				if (!isset($_POST['attribute_weight_impact'])) $_POST['attribute_weight_impact'] = 0;
				if (!isset($_POST['attribute_ecotax'])) $_POST['attribute_ecotax'] = 0;
				if (Tools::getValue('attribute_default'))
					$product->deleteDefaultAttributes();

				// Change existing one
				if (($id_product_attribute = (int)Tools::getValue('id_product_attribute')) || ($id_product_attribute = $product->productAttributeExists(Tools::getValue('attribute_combination_list'), false, null, true, true)))
				{
					if ($this->tabAccess['edit'] === '1')
					{

						if ($this->isProductFieldUpdated('available_date_attribute') && !Validate::isDateFormat(Tools::getValue('available_date_attribute')))
							$this->errors[] = Tools::displayError('Invalid date format.');
						else
						{
							$product->updateAttribute((int)$id_product_attribute,
								$this->isProductFieldUpdated('attribute_wholesale_price') ? Tools::getValue('attribute_wholesale_price') : null,
								$this->isProductFieldUpdated('attribute_price_impact') ? Tools::getValue('attribute_price') * Tools::getValue('attribute_price_impact') : null,
								$this->isProductFieldUpdated('attribute_weight_impact') ? Tools::getValue('attribute_weight') * Tools::getValue('attribute_weight_impact') : null,
								$this->isProductFieldUpdated('attribute_unit_impact') ? Tools::getValue('attribute_unity') * Tools::getValue('attribute_unit_impact') : null,
								$this->isProductFieldUpdated('attribute_ecotax') ? Tools::getValue('attribute_ecotax') : null,
								Tools::getValue('id_image_attr'),
								Tools::getValue('attribute_reference'),
								Tools::getValue('attribute_ean13'),
								$this->isProductFieldUpdated('attribute_default') ? Tools::getValue('attribute_default') : null,
								Tools::getValue('attribute_location'),
								Tools::getValue('attribute_upc'),
								$this->isProductFieldUpdated('attribute_minimal_quantity') ? Tools::getValue('attribute_minimal_quantity') : null,
								$this->isProductFieldUpdated('available_date_attribute') ? Tools::getValue('available_date_attribute') : null, false);
								StockAvailable::setProductDependsOnStock((int)$product->id, $product->depends_on_stock, null, (int)$id_product_attribute);
								StockAvailable::setProductOutOfStock((int)$product->id, $product->out_of_stock, null, (int)$id_product_attribute);
						}
					}
					else
						$this->errors[] = Tools::displayError('You do not have permission to add this.');
				}
				// Add new
				else
				{
					if ($this->tabAccess['add'] === '1')
					{
						if ($product->productAttributeExists(Tools::getValue('attribute_combination_list')))
							$this->errors[] = Tools::displayError('This combination already exists.');
						else
						{
							$id_product_attribute = $product->addCombinationEntity(
								Tools::getValue('attribute_wholesale_price'),
								Tools::getValue('attribute_price') * Tools::getValue('attribute_price_impact'),
								Tools::getValue('attribute_weight') * Tools::getValue('attribute_weight_impact'),
								Tools::getValue('attribute_unity') * Tools::getValue('attribute_unit_impact'),
								Tools::getValue('attribute_ecotax'),
								0,
								Tools::getValue('id_image_attr'),
								Tools::getValue('attribute_reference'),
								null,
								Tools::getValue('attribute_ean13'),
								Tools::getValue('attribute_default'),
								Tools::getValue('attribute_location'),
								Tools::getValue('attribute_upc'),
								Tools::getValue('attribute_minimal_quantity')
							);
							StockAvailable::setProductDependsOnStock((int)$product->id, $product->depends_on_stock, null, (int)$id_product_attribute);
							StockAvailable::setProductOutOfStock((int)$product->id, $product->out_of_stock, null, (int)$id_product_attribute);
						}
					}
					else
						$this->errors[] = Tools::displayError('You do not have permission to').'<hr>'.Tools::displayError('edit here.');
				}
				if (!count($this->errors))
				{
					$combination = new Combination((int)$id_product_attribute);
					$combination->setAttributes(Tools::getValue('attribute_combination_list'));
					$product->checkDefaultAttributes();
					if (Tools::getValue('attribute_default'))
					{
						Product::updateDefaultAttribute((int)$product->id);
						if(isset($id_product_attribute))
							$product->cache_default_attribute = (int)$id_product_attribute;
						if ($available_date = Tools::getValue('available_date_attribute'))
							$product->setAvailableDate($available_date);
					}
				}
			}
		}
	}

	public function processFeatures()
	{
		if (!Feature::isFeatureActive())
			return;

		if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
		{
			// delete all objects
			$product->deleteFeatures();

			// add new objects
			$languages = Language::getLanguages(false);
			foreach ($_POST as $key => $val)
			{
				if (preg_match('/^feature_([0-9]+)_value/i', $key, $match))
				{
					if ($val)
						$product->addFeaturesToDB($match[1], $val);
					else
					{
						if ($default_value = $this->checkFeatures($languages, $match[1]))
						{
							$id_value = $product->addFeaturesToDB($match[1], 0, 1);
							foreach ($languages as $language)
							{
								if ($cust = Tools::getValue('custom_'.$match[1].'_'.(int)$language['id_lang']))
									$product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $cust);
								else
									$product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $default_value);
							}
						}
					}
				}
			}
		}
		else
			$this->errors[] = Tools::displayError('A product must be created before adding features.');
	}

	/**
	 * This function is never called at the moment (specific prices cannot be edited)
	 */
	public function processPricesModification()
	{
		$id_specific_prices = Tools::getValue('spm_id_specific_price');
		$id_combinations = Tools::getValue('spm_id_product_attribute');
		$id_shops = Tools::getValue('spm_id_shop');
		$id_currencies = Tools::getValue('spm_id_currency');
		$id_countries = Tools::getValue('spm_id_country');
		$id_groups = Tools::getValue('spm_id_group');
		$id_customers = Tools::getValue('spm_id_customer');
		$prices = Tools::getValue('spm_price');
		$from_quantities = Tools::getValue('spm_from_quantity');
		$reductions = Tools::getValue('spm_reduction');
		$reduction_types = Tools::getValue('spm_reduction_type');
		$froms = Tools::getValue('spm_from');
		$tos = Tools::getValue('spm_to');

		foreach ($id_specific_prices as $key => $id_specific_price)
			if ($reduction_types[$key] == 'percentage' && ((float)$reductions[$key] <= 0 || (float)$reductions[$key] > 100))
				$this->errors[] = Tools::displayError('Submitted reduction value (0-100) is out-of-range');
			elseif ($this->_validateSpecificPrice($id_shops[$key], $id_currencies[$key], $id_countries[$key], $id_groups[$key], $id_customers[$key], $prices[$key], $from_quantities[$key], $reductions[$key], $reduction_types[$key], $froms[$key], $tos[$key], $id_combinations[$key]))
			{
				$specific_price = new SpecificPrice((int)($id_specific_price));
				$specific_price->id_shop = (int)$id_shops[$key];
				$specific_price->id_product_attribute = (int)$id_combinations[$key];
				$specific_price->id_currency = (int)($id_currencies[$key]);
				$specific_price->id_country = (int)($id_countries[$key]);
				$specific_price->id_group = (int)($id_groups[$key]);
				$specific_price->id_customer = (int)$id_customers[$key];
				$specific_price->price = (float)($prices[$key]);
				$specific_price->from_quantity = (int)($from_quantities[$key]);
				$specific_price->reduction = (float)($reduction_types[$key] == 'percentage' ? ($reductions[$key] / 100) : $reductions[$key]);
				$specific_price->reduction_type = !$reductions[$key] ? 'amount' : $reduction_types[$key];
				$specific_price->from = !$froms[$key] ? '0000-00-00 00:00:00' : $froms[$key];
				$specific_price->to = !$tos[$key] ? '0000-00-00 00:00:00' : $tos[$key];
				if (!$specific_price->update())
					$this->errors[] = Tools::displayError('An error occurred while updating the specific price.');
			}
		if (!count($this->errors))
			$this->redirect_after = self::$currentIndex.'&id_product='.(int)(Tools::getValue('id_product')).(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&update'.$this->table.'&action=Prices&token='.$this->token;

	}

	public function processPriceAddition()
	{
		// Check if a specific price has been submitted
		if (!Tools::getIsset('submitPriceAddition'))
			return;

		$id_product = Tools::getValue('id_product');
		$id_product_attribute = Tools::getValue('sp_id_product_attribute');
		$id_shop = Tools::getValue('sp_id_shop');
		$id_currency = Tools::getValue('sp_id_currency');
		$id_country = Tools::getValue('sp_id_country');
		$id_group = Tools::getValue('sp_id_group');
		$id_customer = Tools::getValue('sp_id_customer');
		$price = Tools::getValue('leave_bprice') ? '-1' : Tools::getValue('sp_price');
		$from_quantity = Tools::getValue('sp_from_quantity');
		$reduction = (float)(Tools::getValue('sp_reduction'));
		$reduction_type = !$reduction ? 'amount' : Tools::getValue('sp_reduction_type');
		$from = Tools::getValue('sp_from');
		if (!$from)
			$from = '0000-00-00 00:00:00';
		$to = Tools::getValue('sp_to');
		if (!$to)
			$to = '0000-00-00 00:00:00';
									
		if ($reduction_type == 'percentage' && ((float)$reduction <= 0 || (float)$reduction > 100))
			$this->errors[] = Tools::displayError('Submitted reduction value (0-100) is out-of-range');
		elseif ($this->_validateSpecificPrice($id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_product_attribute))
		{
			$specificPrice = new SpecificPrice();
			$specificPrice->id_product = (int)$id_product;
			$specificPrice->id_product_attribute = (int)$id_product_attribute;
			$specificPrice->id_shop = (int)$id_shop;
			$specificPrice->id_currency = (int)($id_currency);
			$specificPrice->id_country = (int)($id_country);
			$specificPrice->id_group = (int)($id_group);
			$specificPrice->id_customer = (int)$id_customer;
			$specificPrice->price = (float)($price);
			$specificPrice->from_quantity = (int)($from_quantity);
			$specificPrice->reduction = (float)($reduction_type == 'percentage' ? $reduction / 100 : $reduction);
			$specificPrice->reduction_type = $reduction_type;
			$specificPrice->from = $from;
			$specificPrice->to = $to;
			if (!$specificPrice->add())
				$this->errors[] = Tools::displayError('An error occurred while updating the specific price.');
		}
	}

	public function ajaxProcessDeleteSpecificPrice()
	{
		if ($this->tabAccess['delete'] === '1')
		{
			$id_specific_price = (int)Tools::getValue('id_specific_price');
			if (!$id_specific_price || !Validate::isUnsignedId($id_specific_price))
				$error = Tools::displayError('The specific price ID is invalid.');
			else
			{
				$specificPrice = new SpecificPrice((int)$id_specific_price);
				if (!$specificPrice->delete())
					$error = Tools::displayError('An error occurred while attempting to delete the specific price.');
			}
		}
		else
			$error = Tools::displayError('You do not have permission to delete this.');

		if (isset($error))
			$json = array(
				'status' => 'error',
				'message'=> $error
			);
		else
			$json = array(
				'status' => 'ok',
				'message'=> $this->_conf[1]
			);

		die(Tools::jsonEncode($json));
	}

	public function processSpecificPricePriorities()
	{
		if (!($obj = $this->loadObject()))
			return;
		if (!$priorities = Tools::getValue('specificPricePriority'))
			$this->errors[] = Tools::displayError('Please specify priorities.');
		elseif (Tools::isSubmit('specificPricePriorityToAll'))
		{
			if (!SpecificPrice::setPriorities($priorities))
				$this->errors[] = Tools::displayError('An error occurred while updating priorities.');
			else
				$this->confirmations[] = $this->l('The price rule has successfully updated');
		}
		elseif (!SpecificPrice::setSpecificPriority((int)$obj->id, $priorities))
			$this->errors[] = Tools::displayError('An error occurred while setting priorities.');
	}

	public function processCustomizationConfiguration()
	{
		$product = $this->object;
		// Get the number of existing customization fields ($product->text_fields is the updated value, not the existing value)
		$current_customization = $product->getCustomizationFieldIds();
		$files_count = 0;
		$text_count = 0;
		if (is_array($current_customization))
		{
			foreach ($current_customization as $field)
			{
				if ($field['type'] == 1)
					$text_count++;
				else
					$files_count++;
			}
		}

		if (!$product->createLabels((int)$product->uploadable_files - $files_count, (int)$product->text_fields - $text_count))
			$this->errors[] = Tools::displayError('An error occurred while creating customization fields.');
		if (!count($this->errors) && !$product->updateLabels())
			$this->errors[] = Tools::displayError('An error occurred while updating customization fields.');
		$product->customizable = ($product->uploadable_files > 0 || $product->text_fields > 0) ? 1 : 0;
		if (!count($this->errors) && !$product->update())
			$this->errors[] = Tools::displayError('An error occurred while updating the custom configuration.');
	}

	public function processProductCustomization()
	{
		if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
		{
			foreach ($_POST as $field => $value)
				if (strncmp($field, 'label_', 6) == 0 && !Validate::isLabel($value))
					$this->errors[] = Tools::displayError('The label fields defined are invalid.');
			if (empty($this->errors) && !$product->updateLabels())
				$this->errors[] = Tools::displayError('An error occurred while updating customization fields.');
			if (empty($this->errors))
				$this->confirmations[] = $this->l('Update successful');
		}
		else
			$this->errors[] = Tools::displayError('A product must be created before adding customization.');
	}

	/**
	 * Overrides parent for custom redirect link
	 */
	public function processPosition()
	{
		if (!Validate::isLoadedObject($object = $this->loadObject()))
		{
			$this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').
				' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
		}
		elseif (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position')))
			$this->errors[] = Tools::displayError('Failed to update the position.');
		else
		{
			$category = new Category((int)tools::getValue('id_category'));							
			if (Validate::isLoadedObject($category))
				Hook::exec('actionCategoryUpdate', array('category' => $category));
			$this->redirect_after = self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&action=Customization&conf=5'.(($id_category = (Tools::getIsset('id_category') ? (int)Tools::getValue('id_category') : '')) ? ('&id_category='.$id_category) : '').'&token='.Tools::getAdminTokenLite('AdminProducts');				
		}
	}

	public function initProcess()
	{
		// Delete a product in the download folder
		if (Tools::getValue('deleteVirtualProduct'))
		{
			if ($this->tabAccess['delete'] === '1')
				$this->action = 'deleteVirtualProduct';
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete this.');
		}
		// Product preview
		elseif (Tools::isSubmit('submitAddProductAndPreview'))
		{
			$this->display = 'edit';
			$this->action = 'save';
			if (Tools::getValue('id_product'))
			{
				$this->id_object = Tools::getValue('id_product');
				$this->object = new Product((int)Tools::getValue('id_product'));
			}
		}
		// Update attachments
		elseif (Tools::isSubmit('submitAddAttachments'))
		{
			if ($this->tabAccess['add'] === '1')
			{
				$this->action = 'addAttachments';
				$this->tab_display = 'attachments';
				$this->display = 'edit';
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to add this.');
		}
		elseif (Tools::isSubmit('submitAttachments'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$this->action = 'attachments';
				$this->tab_display = 'attachments';
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}
		// Product duplication
		elseif (Tools::getIsset('duplicate'.$this->table))
		{
			if ($this->tabAccess['add'] === '1')
				$this->action = 'duplicate';
			else
				$this->errors[] = Tools::displayError('You do not have permission to add this.');
		}
		// Product images management
		elseif (Tools::getValue('id_image') && Tools::getValue('ajax'))
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'image';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}
		// Product attributes management
		elseif (Tools::isSubmit('submitProductAttribute'))
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'productAttribute';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}
		// Product features management
		elseif (Tools::isSubmit('submitFeatures') || Tools::isSubmit('submitFeaturesAndStay'))
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'features';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}
		// Product specific prices management NEVER USED
		elseif (Tools::isSubmit('submitPricesModification'))
		{
			if ($this->tabAccess['add'] === '1')
				$this->action = 'pricesModification';
			else
				$this->errors[] = Tools::displayError('You do not have permission to add this.');
		}
		elseif (Tools::isSubmit('deleteSpecificPrice'))
		{
			if ($this->tabAccess['delete'] === '1')
				$this->action = 'deleteSpecificPrice';
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete this.');
		}
		elseif (Tools::isSubmit('submitSpecificPricePriorities'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$this->action = 'specificPricePriorities';
				$this->tab_display = 'prices';
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}
		// Customization management
		elseif (Tools::isSubmit('submitCustomizationConfiguration'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$this->action = 'customizationConfiguration';
				$this->tab_display = 'customization';
				$this->display = 'edit';
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}
		elseif (Tools::isSubmit('submitProductCustomization'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$this->action = 'productCustomization';
				$this->tab_display = 'customization';
				$this->display = 'edit';
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}

		if (!$this->action)
			parent::initProcess();
		else
			$this->id_object = (int)Tools::getValue($this->identifier);

		if (isset($this->available_tabs[Tools::getValue('key_tab')]))
			$this->tab_display = Tools::getValue('key_tab');

		// Set tab to display if not decided already
		if (!$this->tab_display && $this->action)
			if (in_array($this->action, array_keys($this->available_tabs)))
				$this->tab_display = $this->action;

		// And if still not set, use default
		if (!$this->tab_display)
		{
			if (in_array($this->default_tab, $this->available_tabs))
				$this->tab_display = $this->default_tab;
			else
				$this->tab_display = key($this->available_tabs);
		}
	}

	/**
	 * postProcess handle every checks before saving products information
	 *
	 * @return void
	 */
	public function postProcess()
	{
		if (!$this->redirect_after)
			parent::postProcess();

		if ($this->display == 'edit' || $this->display == 'add')
		{
			$this->addjQueryPlugin(array(
				'autocomplete',
				'tablednd',
				'thickbox',
				'ajaxfileupload',
				'date'
			));

			$this->addJqueryUI(array(
				'ui.core',
				'ui.widget',
				'ui.accordion',
				'ui.slider',
				'ui.datepicker'
			));

			$this->addJS(array(
				_PS_JS_DIR_.'productTabsManager.js',
				_PS_JS_DIR_.'admin-products.js',
				_PS_JS_DIR_.'attributesBack.js',
				_PS_JS_DIR_.'price.js',
				_PS_JS_DIR_.'tiny_mce/tiny_mce.js',
				_PS_JS_DIR_.'tinymce.inc.js',
				_PS_JS_DIR_.'fileuploader.js',
				_PS_JS_DIR_.'admin-dnd.js',
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.js',
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.async.js',
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.edit.js',
				_PS_JS_DIR_.'admin-categories-tree.js',
				_PS_JS_DIR_.'jquery/ui/jquery.ui.progressbar.min.js',
				_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js'
			));

			$this->addCSS(array(
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.css',
				_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css',
			));
		}
	}

	/* @todo rename to processaddproductimage */
	public function ajaxProcessAddImage()
	{
		self::$currentIndex = 'index.php?tab=AdminProducts';
		$allowedExtensions = array('jpeg', 'gif', 'png', 'jpg');
		// max file size in bytes
		$uploader = new FileUploader($allowedExtensions, $this->max_image_size);
		$result = $uploader->handleUpload();
		if (isset($result['success']))
		{
			$obj = new Image((int)$result['success']['id_image']);

			// Associate image to shop from context
			$shops = Shop::getContextListShopID();
			$obj->associateTo($shops);
			$json_shops = array();
			foreach ($shops as $id_shop)
				$json_shops[$id_shop] = true;

			$json = array(
				'name' => $result['success']['name'],
				'status' => 'ok',
				'id'=>$obj->id,
				'path' => $obj->getExistingImgPath(),
				'position' => $obj->position,
				'cover' => $obj->cover,
				'shops' => $json_shops,
			);
			@unlink(_PS_TMP_IMG_DIR_.'product_'.(int)$obj->id_product.'.jpg');
			@unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$obj->id_product.'.jpg');
			die(Tools::jsonEncode($json));
		}
		else
			die(Tools::jsonEncode($result));
	}

	public function ajaxProcessDeleteProductAttribute()
	{
		if (!Combination::isFeatureActive())
			return;

		if ($this->tabAccess['delete'] === '1')
		{
			$id_product = (int)Tools::getValue('id_product');
			$id_product_attribute = (int)Tools::getValue('id_product_attribute');
			if ($id_product && Validate::isUnsignedId($id_product) && Validate::isLoadedObject($product = new Product($id_product)))
			{
				$product->deleteAttributeCombination((int)$id_product_attribute);
				$product->checkDefaultAttributes();
				if (!$product->hasAttributes())
				{
					$product->cache_default_attribute = 0;
					$product->update();
				}
				else
					Product::updateDefaultAttribute($id_product);

				$json = array(
					'status' => 'ok',
					'message'=> $this->_conf[1]
				);
			}
			else
				$json = array(
					'status' => 'error',
					'message'=> $this->l('You cannot delete this attribute.')
				);
		}
		else
			$json = array(
				'status' => 'error',
				'message'=> $this->l('You do not have permission to delete this.')
			);

		die(Tools::jsonEncode($json));
	}

	public function ajaxProcessDefaultProductAttribute()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			if (!Combination::isFeatureActive())
				return;

			if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
			{
				$product->deleteDefaultAttributes();
				$product->setDefaultAttribute((int)Tools::getValue('id_product_attribute'));
				$json = array(
					'status' => 'ok',
					'message'=> $this->_conf[4]
				);
			}
			else
				$json = array(
					'status' => 'error',
					'message'=> $this->l('You cannot make this the default attribute.')
				);

			die(Tools::jsonEncode($json));
		}
	}

	public function ajaxProcessEditProductAttribute()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			$id_product = (int)Tools::getValue('id_product');
			$id_product_attribute = (int)Tools::getValue('id_product_attribute');
			if ($id_product && Validate::isUnsignedId($id_product) && Validate::isLoadedObject($product = new Product((int)$id_product)))
			{
				$combinations = $product->getAttributeCombinationsById($id_product_attribute, $this->context->language->id);
				foreach ($combinations as $key => $combination)
					$combinations[$key]['attributes'][] = array($combination['group_name'], $combination['attribute_name'], $combination['id_attribute']);

				die(Tools::jsonEncode($combinations));
			}
		}
	}

	public function ajaxPreProcess()
	{
		if (Tools::getIsset('update'.$this->table) && Tools::getIsset('id_'.$this->table))
		{
			$this->display = 'edit';
			$this->action = Tools::getValue('action');
		}
	}

	public function ajaxProcessUpdateProductImageShopAsso()
	{
		$id_product = Tools::getValue('id_product');
		if (($id_image = Tools::getValue('id_image')) && ($id_shop = (int)Tools::getValue('id_shop')))
			if (Tools::getValue('active') == 'true')
				$res = Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'image_shop (`id_image`, `id_shop`) VALUES('.(int)$id_image.', '.(int)$id_shop.')');
			else
				$res = Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'image_shop WHERE `id_image` = '.(int)$id_image.' AND `id_shop` = '.(int)$id_shop);
		
		// Clean covers in image table
		$count_cover_image = Db::getInstance()->getValue('
			SELECT COUNT(*) FROM '._DB_PREFIX_.'image i 
			INNER JOIN '._DB_PREFIX_.'image_shop ish ON (i.id_image = ish.id_image AND ish.id_shop = '.(int)$id_shop.') 
			WHERE i.cover = 1 AND `id_product` = '.(int)$id_product);
		
		$id_image = Db::getInstance()->getValue('
			SELECT i.`id_image` FROM '._DB_PREFIX_.'image i 
			INNER JOIN '._DB_PREFIX_.'image_shop ish ON (i.id_image = ish.id_image AND ish.id_shop = '.(int)$id_shop.') 
			WHERE `id_product` = '.(int)$id_product);
		
		if ($count_cover_image < 1)
			Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image i SET i.cover = 1 WHERE i.id_image = '.(int)$id_image.' AND i.`id_product` = '.(int)$id_product.' LIMIT 1');
		
		if ($count_cover_image > 1)
			Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image i SET i.cover = 0 WHERE i.id_image <> '.(int)$id_image.' AND i.`id_product` = '.(int)$id_product);
			
		// Clean covers in image_shop table
		$count_cover_image_shop = Db::getInstance()->getValue('
			SELECT COUNT(*) 
			FROM '._DB_PREFIX_.'image_shop ish 
			INNER JOIN '._DB_PREFIX_.'image i ON (i.id_image = ish.id_image AND i.`id_product` = '.(int)$id_product.')
			WHERE ish.id_shop = '.(int)$id_shop.' AND ish.cover = 1');
		
		if ($count_cover_image_shop < 1)
			Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image_shop ish SET ish.cover = 1 WHERE ish.id_image = '.(int)$id_image.' AND ish.id_shop =  '.(int)$id_shop.' LIMIT 1');
		if ($count_cover_image_shop > 1)
			Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image_shop ish SET ish.cover = 0 WHERE ish.id_image <> '.(int)$id_image.' AND ish.cover = 1 AND ish.id_shop = '.(int)$id_shop.' LIMIT '.intval($count_cover_image_shop - 1));

		if ($res)
			$this->jsonConfirmation($this->_conf[27]);
		else
			$this->jsonError(Tools::displayError('An error occurred while attempting to associate this image with your shop. '));
	}

	public function ajaxProcessUpdateImagePosition()
	{
		$res = false;
		if ($json = Tools::getValue('json'))
		{
			$res = true;
			$json = stripslashes($json);
			$images = Tools::jsonDecode($json, true);
			foreach ($images as $id => $position)
			{
				$img = new Image((int)$id);
				$img->position = (int)$position;
				$res &= $img->update();
			}
		}
		if ($res)
			$this->jsonConfirmation($this->_conf[25]);
		else
			$this->jsonError(Tools::displayError('An error occurred while attempting to move this picture.'));
	}

	public function ajaxProcessUpdateCover()
	{
		Image::deleteCover((int)Tools::getValue('id_product'));
		$img = new Image((int)Tools::getValue('id_image'));
		$img->cover = 1;

		@unlink(_PS_TMP_IMG_DIR_.'product_'.(int)$img->id_product.'.jpg');
		@unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$img->id_product.'.jpg');

		if ($img->update())
			$this->jsonConfirmation($this->_conf[26]);
		else
			$this->jsonError(Tools::displayError('An error occurred while attempting to move this picture.'));
	}

	public function ajaxProcessDeleteProductImage()
	{
		$this->display = 'content';
		$res = true;
		/* Delete product image */
		$image = new Image((int)Tools::getValue('id_image'));
		$this->content['id'] = $image->id;
		$res &= $image->delete();
		// if deleted image was the cover, change it to the first one
		if (!Image::getCover($image->id_product))
		{
			$res &= Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'image_shop` image_shop, '._DB_PREFIX_.'image i
			SET image_shop.`cover` = 1,
			i.cover = 1
			WHERE image_shop.`id_image` = (SELECT id_image FROM
														(SELECT image_shop.id_image
															FROM '._DB_PREFIX_.'image i'.
															Shop::addSqlAssociation('image', 'i').'
															WHERE i.id_product ='.(int)$image->id_product.' LIMIT 1
														) tmpImage)
			AND id_shop='.(int)$this->context->shop->id.'
			AND i.id_image = image_shop.id_image
			');
		}

		if (file_exists(_PS_TMP_IMG_DIR_.'product_'.$image->id_product.'.jpg'))
			$res &= @unlink(_PS_TMP_IMG_DIR_.'product_'.$image->id_product.'.jpg');
		if (file_exists(_PS_TMP_IMG_DIR_.'product_mini_'.$image->id_product.'.jpg'))
			$res &= @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.$image->id_product.'.jpg');

		if ($res)
			$this->jsonConfirmation($this->_conf[7]);
		else
			$this->jsonError(Tools::displayError('An error occurred while attempting to delete the product image.'));
	}

	protected function _validateSpecificPrice($id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_combination = 0)
	{
		if (!Validate::isUnsignedId($id_shop) || !Validate::isUnsignedId($id_currency) || !Validate::isUnsignedId($id_country) || !Validate::isUnsignedId($id_group) || !Validate::isUnsignedId($id_customer))
			$this->errors[] = Tools::displayError('Wrong IDs');
		elseif ((!isset($price) && !isset($reduction)) || (isset($price) && !Validate::isNegativePrice($price)) || (isset($reduction) && !Validate::isPrice($reduction)))
			$this->errors[] = Tools::displayError('Invalid price/discount amount');
		elseif (!Validate::isUnsignedInt($from_quantity))
			$this->errors[] = Tools::displayError('Invalid quantity');
		elseif ($reduction && !Validate::isReductionType($reduction_type))
			$this->errors[] = Tools::displayError('Please select a discount type (amount or percentage).');
		elseif ($from && $to && (!Validate::isDateFormat($from) || !Validate::isDateFormat($to)))
			$this->errors[] = Tools::displayError('The from/to date is invalid.');
		elseif (SpecificPrice::exists((int)$this->object->id, $id_combination, $id_shop, $id_group, $id_country, $id_currency, $id_customer, $from_quantity, $from, $to, false))
			$this->errors[] = Tools::displayError('A specific price already exists for these parameters.');
		else
			return true;
		return false;
	}

	/* Checking customs feature */
	protected function checkFeatures($languages, $feature_id)
	{
		$rules = call_user_func(array('FeatureValue', 'getValidationRules'), 'FeatureValue');
		$feature = Feature::getFeature((int)Configuration::get('PS_LANG_DEFAULT'), $feature_id);
		$val = 0;
		foreach ($languages as $language)
			if ($val = Tools::getValue('custom_'.$feature_id.'_'.$language['id_lang']))
			{
				$current_language = new Language($language['id_lang']);
				if (Tools::strlen($val) > $rules['sizeLang']['value'])
					$this->errors[] = sprintf(
						Tools::displayError('The name for feature %1$s is too long in %2$s.'),
						' <b>'.$feature['name'].'</b>',
						$current_language->name
					);
				elseif (!call_user_func(array('Validate', $rules['validateLang']['value']), $val))
					$this->errors[] = sprintf(
						Tools::displayError('A valid name required for feature. %1$s in %2$s.'),
						' <b>'.$feature['name'].'</b>',
						$current_language->name
					);
				if (count($this->errors))
					return 0;
				// Getting default language
				if ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'))
					return $val;
			}
		return 0;
	}

	/**
	 * Add or update a product image
	 *
	 * @param object $product Product object to add image
	 */
	public function addProductImage($product, $method = 'auto')
	{
		/* Updating an existing product image */
		if ($id_image = (int)Tools::getValue('id_image'))
		{
			$image = new Image((int)$id_image);
			if (!Validate::isLoadedObject($image))
				$this->errors[] = Tools::displayError('An error occurred while loading the object image.');
			else
			{
				if (($cover = Tools::getValue('cover')) == 1)
					Image::deleteCover($product->id);
				$image->cover = $cover;
				$this->validateRules('Image');
				$this->copyFromPost($image, 'image');
				if (count($this->errors) || !$image->update())
					$this->errors[] = Tools::displayError('An error occurred while updating the image.');
				elseif (isset($_FILES['image_product']['tmp_name']) && $_FILES['image_product']['tmp_name'] != null)
					$this->copyImage($product->id, $image->id, $method);
			}
		}
		if (isset($image) && Validate::isLoadedObject($image) && !file_exists(_PS_PROD_IMG_DIR_.$image->getExistingImgPath().'.'.$image->image_format))
			$image->delete();
		if (count($this->errors))
			return false;
		@unlink(_PS_TMP_IMG_DIR_.'product_'.$product->id.'.jpg');
		@unlink(_PS_TMP_IMG_DIR_.'product_mini_'.$product->id.'.jpg');
		return ((isset($id_image) && is_int($id_image) && $id_image) ? $id_image : false);
	}
	/**
	 * Copy a product image
	 *
	 * @param integer $id_product Product Id for product image filename
	 * @param integer $id_image Image Id for product image filename
	 */
	public function copyImage($id_product, $id_image, $method = 'auto')
	{
		if (!isset($_FILES['image_product']['tmp_name']))
			return false;
		if ($error = ImageManager::validateUpload($_FILES['image_product']))
			$this->errors[] = $error;
		else
		{
			$image = new Image($id_image);

			if (!$new_path = $image->getPathForCreation())
				$this->errors[] = Tools::displayError('An error occurred while attempting to create a new folder.');
			if (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image_product']['tmp_name'], $tmpName))
				$this->errors[] = Tools::displayError('An error occurred during the image upload process.');
			elseif (!ImageManager::resize($tmpName, $new_path.'.'.$image->image_format))
				$this->errors[] = Tools::displayError('An error occurred while copying the image.');
			elseif ($method == 'auto')
			{
				$imagesTypes = ImageType::getImagesTypes('products');
				foreach ($imagesTypes as $k => $image_type)
				{
					if (!ImageManager::resize($tmpName, $new_path.'-'.stripslashes($image_type['name']).'.'.$image->image_format, $image_type['width'], $image_type['height'], $image->image_format))
						$this->errors[] = Tools::displayError('An error occurred while copying image:').' '.stripslashes($image_type['name']);
				}
			}

			@unlink($tmpName);
			Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_product));
		}
	}
	
	protected function updateAssoShop($id_object)
	{
		//override AdminController::updateAssoShop() specifically for products because shop association is set with the context in ObjectModel
		return;
	}
	
	public function processAdd()
	{
		$this->checkProduct();

		if (!empty($this->errors))
		{
			$this->display = 'add';
			return false;
		}

		$this->object = new $this->className();
		$this->_removeTaxFromEcotax();
		$this->copyFromPost($this->object, $this->table);

		if ($this->object->add())
		{
			$this->addCarriers();
			$this->updateAccessories($this->object);
			$this->updatePackItems($this->object);
			$this->updateDownloadProduct($this->object);

			if (empty($this->errors))
			{
				$languages = Language::getLanguages(false);
				if ($this->isProductFieldUpdated('category_box') && !$this->object->updateCategories(Tools::getValue('categoryBox')))
					$this->errors[] = Tools::displayError('An error occurred while linking the object.').' <b>'.$this->table.'</b> '.Tools::displayError('To categories');
				elseif (!$this->updateTags($languages, $this->object))
					$this->errors[] = Tools::displayError('An error occurred while adding tags.');
				else
				{
					Hook::exec('actionProductAdd', array('product' => $this->object));
					if (in_array($this->object->visibility, array('both', 'search')) && Configuration::get('PS_SEARCH_INDEXATION'))
						Search::indexation(false, $this->object->id);
				}

				// Save and preview
				if (Tools::isSubmit('submitAddProductAndPreview'))
				{
					$preview_url = $this->context->link->getProductLink(
						$this->getFieldValue($this->object, 'id'),
						$this->getFieldValue($this->object, 'link_rewrite', $this->context->language->id),
						Category::getLinkRewrite($this->getFieldValue($this->object, 'id_category_default'), $this->context->language->id),
						null,
						null,
						Context::getContext()->shop->id,
						0,
						(bool)Configuration::get('PS_REWRITING_SETTINGS')
					);

					if (!$this->object->active)
					{
						$admin_dir = dirname($_SERVER['PHP_SELF']);
						$admin_dir = substr($admin_dir, strrpos($admin_dir, '/') + 1);
						$preview_url .= '&adtoken='.$this->token.'&ad='.$admin_dir.'&id_employee='.(int)$this->context->employee->id;
					}

					$this->redirect_after = $preview_url;
				}

				// Save and stay on same form
				if ($this->display == 'edit')
					$this->redirect_after = self::$currentIndex.'&id_product='.(int)$this->object->id
						.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '')
						.'&updateproduct&conf=3&key_tab='.Tools::safeOutput(Tools::getValue('key_tab')).'&token='.$this->token;
				else
					// Default behavior (save and back)
					$this->redirect_after = self::$currentIndex
						.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '')
						.'&conf=3&token='.$this->token;
			}
			else
				$this->object->delete();
				// if errors : stay on edit page
				$this->display = 'edit';
		}
		else
			$this->errors[] = Tools::displayError('An error occurred while creating an object.').' <b>'.$this->table.'</b>';
			
		return $this->object;
	}

	protected function isTabSubmitted($tab_name)
	{
		if (!is_array($this->submitted_tabs))
			$this->submitted_tabs = Tools::getValue('submitted_tabs');

		if (is_array($this->submitted_tabs) && in_array($tab_name, $this->submitted_tabs))
			return true;

		return false;
	}

	public function processStatus()
	{
		$this->loadObject(true);
		if (!Validate::isLoadedObject($this->object))
			return false;
		if (($error = $this->object->validateFields(false, true)) !== true)
			$this->errors[] = $error;
		if (($error = $this->object->validateFieldsLang(false, true)) !== true)
			$this->errors[] = $error;

		return !count($this->errors) ? parent::processStatus() : false;
	}
	
	public function processUpdate()
	{
		$this->checkProduct();

		if (!empty($this->errors))
		{
			$this->display = 'edit';
			return false;
		}

		$id = (int)Tools::getValue('id_'.$this->table);
		/* Update an existing product */
		if (isset($id) && !empty($id))
		{
			$object = new $this->className((int)$id);
			$this->object = $object;

			if (Validate::isLoadedObject($object))
			{
				$this->_removeTaxFromEcotax();
				$product_type_before = $object->getType();
				$this->copyFromPost($object, $this->table);
				$object->indexed = 0;

				if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP)
					$object->setFieldsToUpdate((array)Tools::getValue('multishop_check'));

				// Duplicate combinations if not associated to shop
				if ($this->context->shop->getContext() == Shop::CONTEXT_SHOP && !$object->isAssociatedToShop())
				{
					$is_associated_to_shop = false;
					$combinations = Product::getProductAttributesIds($object->id);
					if ($combinations)
					{
						foreach ($combinations as $id_combination)
						{
							$combination = new Combination((int)$id_combination['id_product_attribute']);
							$default_combination = new Combination((int)$id_combination['id_product_attribute'], null, (int)$this->object->id_shop_default);

							$def = ObjectModel::getDefinition($default_combination);
							foreach ($def['fields'] as $field_name => $row)
								$combination->$field_name = ObjectModel::formatValue($default_combination->$field_name, $def['fields'][$field_name]['type']);

							$combination->save();
						}
					}
				}
				else
					$is_associated_to_shop = true;

				if ($object->update())
				{
					if (in_array($this->context->shop->getContext(), array(Shop::CONTEXT_SHOP, Shop::CONTEXT_ALL)))
					{
						if ($this->isTabSubmitted('Shipping'))
							$this->addCarriers();
						if ($this->isTabSubmitted('Associations'))
							$this->updateAccessories($object);
						if ($this->isTabSubmitted('Suppliers'))
							$this->processSuppliers();
						if ($this->isTabSubmitted('Features'))
							$this->processFeatures();
						if ($this->isTabSubmitted('Combinations'))
							$this->processProductAttribute();
						if ($this->isTabSubmitted('Prices'))
						{
							$this->processPriceAddition();
							$this->processSpecificPricePriorities();
						}
						if ($this->isTabSubmitted('Customization'))
							$this->processCustomizationConfiguration();
						if ($this->isTabSubmitted('Attachments'))
							$this->processAttachments();

						$this->updatePackItems($object);
						// Disallow avanced stock management if the product become a pack
						if ($product_type_before == Product::PTYPE_SIMPLE && $object->getType() == Product::PTYPE_PACK)
							StockAvailable::setProductDependsOnStock((int)$object->id, false);
						$this->updateDownloadProduct($object, 1);
						$this->updateTags(Language::getLanguages(false), $object);
						
						if ($this->isProductFieldUpdated('category_box') && !$object->updateCategories(Tools::getValue('categoryBox')))
							$this->errors[] = Tools::displayError('An error occurred while linking the object.').' <b>'.$this->table.'</b> '.Tools::displayError('To categories');
					}
					
					if ($this->isTabSubmitted('Warehouses'))
						$this->processWarehouses();
					if (empty($this->errors))
					{
						Hook::exec('actionProductUpdate', array('product' => $object));

						if (in_array($object->visibility, array('both', 'search')) && Configuration::get('PS_SEARCH_INDEXATION'))
							Search::indexation(false, $object->id);

						// Save and preview
						if (Tools::isSubmit('submitAddProductAndPreview'))
						{
							$preview_url = $this->context->link->getProductLink(
								$this->getFieldValue($object, 'id'),
								$this->getFieldValue($object, 'link_rewrite', $this->context->language->id),
								Category::getLinkRewrite($this->getFieldValue($object, 'id_category_default'), $this->context->language->id),
								null,
								null,
								Context::getContext()->shop->id,
								0,
								(bool)Configuration::get('PS_REWRITING_SETTINGS')
							);

							if (!$object->active)
							{
								$admin_dir = dirname($_SERVER['PHP_SELF']);
								$admin_dir = substr($admin_dir, strrpos($admin_dir, '/') + 1);
								if (strpos($preview_url, '?') === false)
									$preview_url .= '?';
								else
									$preview_url .= '&';
								$preview_url .= 'adtoken='.$this->token.'&ad='.$admin_dir.'&id_employee='.(int)$this->context->employee->id;
							}
							$this->redirect_after = $preview_url;
						}
						else
						{
							// Save and stay on same form
							if ($this->display == 'edit')
							{
								$this->confirmations[] = $this->l('Update successful');
								$this->redirect_after = self::$currentIndex.'&id_product='.(int)$this->object->id
									.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '')
									.'&updateproduct&conf=4&key_tab='.Tools::safeOutput(Tools::getValue('key_tab')).'&token='.$this->token;
							}
							else
								// Default behavior (save and back)
								$this->redirect_after = self::$currentIndex.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&conf=4&token='.$this->token;
						}
					}
					// if errors : stay on edit page
					else
						$this->display = 'edit';
				}
				else
				{
					if (!$is_associated_to_shop && $combinations)
						foreach ($combinations as $id_combination)
						{
							$combination = new Combination((int)$id_combination['id_product_attribute']);
							$combination->delete();
						}
					$this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.'</b> ('.Db::getInstance()->getMsgError().')';
				}
			}
			else
				$this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.'</b> ('.Tools::displayError('The object cannot be loaded. ').')';
			return $object;
		}
	}

	/**
	 * Check that a saved product is valid
	 */
	public function checkProduct()
	{
		$className = 'Product';
		// @todo : the call_user_func seems to contains only statics values (className = 'Product')
		$rules = call_user_func(array($this->className, 'getValidationRules'), $this->className);
		$default_language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);

		// Check required fields
		foreach ($rules['required'] as $field)
		{
			if (!$this->isProductFieldUpdated($field))
				continue;

			if (($value = Tools::getValue($field)) == false && $value != '0')
			{
				if (Tools::getValue('id_'.$this->table) && $field == 'passwd')
					continue;
				$this->errors[] = sprintf(
					Tools::displayError('The %s field is required.'),
					call_user_func(array($className, 'displayFieldName'), $field, $className)
				);
			}
		}

		// Check multilingual required fields
		foreach ($rules['requiredLang'] as $fieldLang)
			if ($this->isProductFieldUpdated($fieldLang, $default_language->id) && !Tools::getValue($fieldLang.'_'.$default_language->id))
				$this->errors[] = sprintf(
					Tools::displayError('This %1$s field is required at least in %2$s'),
					call_user_func(array($className, 'displayFieldName'), $fieldLang, $className),
					$default_language->name
				);

		// Check fields sizes
		foreach ($rules['size'] as $field => $maxLength)
			if ($this->isProductFieldUpdated($field) && ($value = Tools::getValue($field)) && Tools::strlen($value) > $maxLength)
				$this->errors[] = sprintf(
					Tools::displayError('The %1$s field is too long (%2$d chars max).'),
					call_user_func(array($className, 'displayFieldName'), $field, $className),
					$maxLength
				);

		if (Tools::getIsset('description_short') && $this->isProductFieldUpdated('description_short'))
		{
			$saveShort = Tools::getValue('description_short');
			$_POST['description_short'] = strip_tags(Tools::getValue('description_short'));
		}

		// Check description short size without html
		$limit = (int)Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
		if ($limit <= 0) $limit = 400;
		foreach ($languages as $language)
			if ($this->isProductFieldUpdated('description_short', $language['id_lang']) && ($value = Tools::getValue('description_short_'.$language['id_lang'])))
				if (Tools::strlen(strip_tags($value)) > $limit)
					$this->errors[] = sprintf(
						Tools::displayError('This %1$s field (%2$s) is too long: %3$d chars max (current count %4$d).'),
						call_user_func(array($className, 'displayFieldName'), 'description_short'),
						$language['name'],
						$limit,
						Tools::strlen(strip_tags($value))
					);

		// Check multilingual fields sizes
		foreach ($rules['sizeLang'] as $fieldLang => $maxLength)
			foreach ($languages as $language)
		  {
				$value = Tools::getValue($fieldLang.'_'.$language['id_lang']);
				if ($value && Tools::strlen($value) > $maxLength)
					$this->errors[] = sprintf(
						Tools::displayError('The %1$s field is too long (%2$d chars max).'),
						call_user_func(array($className, 'displayFieldName'), $fieldLang, $className),
						$maxLength
					);
			}

		if ($this->isProductFieldUpdated('description_short') && isset($_POST['description_short']))
			$_POST['description_short'] = $saveShort;

		// Check fields validity
		foreach ($rules['validate'] as $field => $function)
			if ($this->isProductFieldUpdated($field) && ($value = Tools::getValue($field)))
				if (!Validate::$function($value))
					$this->errors[] = sprintf(
						Tools::displayError('The %s field is invalid.'),
						call_user_func(array($className, 'displayFieldName'), $field, $className)
					);

		// Check multilingual fields validity
		foreach ($rules['validateLang'] as $fieldLang => $function)
			foreach ($languages as $language)
				if ($this->isProductFieldUpdated('description_short', $language['id_lang']) && ($value = Tools::getValue($fieldLang.'_'.$language['id_lang'])))
					if (!Validate::$function($value))
						$this->errors[] = sprintf(
							Tools::displayError('The %1$s field (%2$s) is invalid.'),
							call_user_func(array($className, 'displayFieldName'), $fieldLang, $className),
							$language['name']
						);

		// Categories
		if ($this->isProductFieldUpdated('id_category_default') && (!Tools::isSubmit('categoryBox') || !count(Tools::getValue('categoryBox'))))
			$this->errors[] = $this->l('Products must be in at least one category.');

		if ($this->isProductFieldUpdated('id_category_default') && (!is_array(Tools::getValue('categoryBox')) || !in_array(Tools::getValue('id_category_default'), Tools::getValue('categoryBox'))))
			$this->errors[] = $this->l('This product must be in the default category.');

		// Tags
		foreach ($languages as $language)
			if ($value = Tools::getValue('tags_'.$language['id_lang']))
				if (!Validate::isTagsList($value))
					$this->errors[] = sprintf(
						Tools::displayError('The tags list (%s) is invalid.'),
						$language['name']
					);
	}

	/**
	 * Check if a field is edited (if the checkbox is checked)
	 * This method will do something only for multishop with a context all / group
	 *
	 * @param string $field Name of field
	 * @param int $id_lang
	 * @return bool
	 */
	protected function isProductFieldUpdated($field, $id_lang = null)
	{
		// Cache this condition to improve performances
		static $is_activated = null;
		if (is_null($is_activated))
			$is_activated = Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP && $this->id_object;

		if (!$is_activated)
			return true;

		if (is_null($id_lang))
			return !empty($_POST['multishop_check'][$field]);
		else
			return !empty($_POST['multishop_check'][$field][$id_lang]);
	}

	protected function _removeTaxFromEcotax()
	{
		$ecotaxTaxRate = Tax::getProductEcotaxRate();
		if ($ecotax = Tools::getValue('ecotax'))
			$_POST['ecotax'] = Tools::ps_round(Tools::getValue('ecotax') / (1 + $ecotaxTaxRate / 100), 6);
	}

	protected function _applyTaxToEcotax($product)
	{
		$ecotaxTaxRate = Tax::getProductEcotaxRate();
		if ($product->ecotax)
			$product->ecotax = Tools::ps_round($product->ecotax * (1 + $ecotaxTaxRate / 100), 2);
	}

	/**
	 * Update product download
	 *
	 * @param object $product Product
	 * @return bool
	 */
	public function updateDownloadProduct($product, $edit = 0)
	{
		$is_virtual_file = (int)Tools::getValue('is_virtual_file');
		// add or update a virtual product
		if (Tools::getValue('is_virtual_good') == 'true')
		{
			$product->setDefaultAttribute(0);//reset cache_default_attribute			
			if (Tools::getValue('virtual_product_expiration_date') && !Validate::isDate(Tools::getValue('virtual_product_expiration_date') && !empty($is_virtual_file)))
			{
				if (!Tools::getValue('virtual_product_expiration_date'))
				{
					$this->errors[] = Tools::displayError('The expiration-date attribute is required.');
					return false;
				}
			}

			// Trick's
			if ($edit == 1)
			{
				$id_product_download = (int)ProductDownload::getIdFromIdProduct((int)$product->id);
				if (!$id_product_download)
					$id_product_download = (int)Tools::getValue('virtual_product_id');
			}
			else
				$id_product_download = Tools::getValue('virtual_product_id');

			$is_shareable = Tools::getValue('virtual_product_is_shareable');
			$virtual_product_name = Tools::getValue('virtual_product_name');
			$virtual_product_filename = Tools::getValue('virtual_product_filename');
			$virtual_product_nb_days = Tools::getValue('virtual_product_nb_days');
			$virtual_product_nb_downloable = Tools::getValue('virtual_product_nb_downloable');
			$virtual_product_expiration_date = Tools::getValue('virtual_product_expiration_date');

			if ($virtual_product_filename)
				$filename = $virtual_product_filename;
			else
				$filename = ProductDownload::getNewFilename();

			$download = new ProductDownload((int)$id_product_download);
			$download->id_product = (int)$product->id;
			$download->display_filename = $virtual_product_name;
			$download->filename = $filename;
			$download->date_add = date('Y-m-d H:i:s');
			$download->date_expiration = $virtual_product_expiration_date ? $virtual_product_expiration_date.' 23:59:59' : '';
			$download->nb_days_accessible = (int)$virtual_product_nb_days;
			$download->nb_downloadable = (int)$virtual_product_nb_downloable;
			$download->active = 1;
			$download->is_shareable = (int)$is_shareable;

			if ($download->save())
				return true;
		}
		else
		{
			/* unactive download product if checkbox not checked */
			if ($edit == 1)
			{
				$id_product_download = (int)ProductDownload::getIdFromIdProduct((int)$product->id);
				if (!$id_product_download)
					$id_product_download = (int)Tools::getValue('virtual_product_id');
			}
			else
				$id_product_download = ProductDownload::getIdFromIdProduct($product->id);

			if (!empty($id_product_download))
			{
				$product_download = new ProductDownload((int)$id_product_download);
				$product_download->date_expiration = date('Y-m-d H:i:s', time() - 1);
				$product_download->active = 0;
				return $product_download->save();
			}
		}
		return false;
	}

	/**
	 * Update product accessories
	 *
	 * @param object $product Product
	 */
	public function updateAccessories($product)
	{
		$product->deleteAccessories();
		if ($accessories = Tools::getValue('inputAccessories'))
		{
			$accessories_id = array_unique(explode('-', $accessories));
			if (count($accessories_id))
			{
				array_pop($accessories_id);
				$product->changeAccessories($accessories_id);
			}
		}
	}

	/**
	 * Update product tags
	 *
	 * @param array Languages
	 * @param object $product Product
	 * @return boolean Update result
	 */
	public function updateTags($languages, $product)
	{
		$tag_success = true;
		/* Reset all tags for THIS product */
		if (!Tag::deleteTagsForProduct((int)$product->id))
			$this->errors[] = Tools::displayError('An error occurred while attempting to delete previous tags.');
		/* Assign tags to this product */
		foreach ($languages as $language)
			if ($value = Tools::getValue('tags_'.$language['id_lang']))
				$tag_success &= Tag::addTags($language['id_lang'], (int)$product->id, $value);

		if (!$tag_success)
			$this->errors[] = Tools::displayError('An error occurred while adding tags.');

		return $tag_success;
	}

	public function initContent($token = null)
	{
		if ($this->display == 'edit' || $this->display == 'add')
		{
			$this->fields_form = array();

			// Check if Module
			if (substr($this->tab_display, 0, 6) == 'Module')
			{
				$this->tab_display_module = strtolower(substr($this->tab_display, 6, Tools::strlen($this->tab_display) - 6));
				$this->tab_display = 'Modules';
			}
			if (method_exists($this, 'initForm'.$this->tab_display))
				$this->tpl_form = strtolower($this->tab_display).'.tpl';

			if ($this->ajax)
				$this->content_only = true;
			else
			{
				$product_tabs = array();

				// tab_display defines which tab to display first
				if (!method_exists($this, 'initForm'.$this->tab_display))
					$this->tab_display = $this->default_tab;

				$advanced_stock_management_active = Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');
				$stock_management_active = Configuration::get('PS_STOCK_MANAGEMENT');

				foreach ($this->available_tabs as $product_tab => $value)
				{
					// if it's the warehouses tab and advanced stock management is disabled, continue
					if ($advanced_stock_management_active == 0 && $product_tab == 'Warehouses')
						continue;

					$product_tabs[$product_tab] = array(
						'id' => $product_tab,
						'selected' => (strtolower($product_tab) == strtolower($this->tab_display) || (isset($this->tab_display_module) && 'module'.$this->tab_display_module == Tools::strtolower($product_tab))),
						'name' => $this->available_tabs_lang[$product_tab],
						'href' => $this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.(int)Tools::getValue('id_product').'&amp;action='.$product_tab,
					);
				}
				$this->tpl_form_vars['product_tabs'] = $product_tabs;
			}
		}
		else
		{
			if ($id_category = (int)$this->id_current_category)
				self::$currentIndex .= '&id_category='.(int)$this->id_current_category;

			// If products from all categories are displayed, we don't want to use sorting by position
			if (!$id_category)
			{
				$this->_defaultOrderBy = $this->identifier;
				if ($this->context->cookie->{$this->table.'Orderby'} == 'position')
				{
					unset($this->context->cookie->{$this->table.'Orderby'});
					unset($this->context->cookie->{$this->table.'Orderway'});
				}
			}
			if (!$id_category)
				$id_category = 1;
			$this->tpl_list_vars['is_category_filter'] = (bool)$this->id_current_category;

			// Generate category selection tree
			$helper = new Helper();
			$this->tpl_list_vars['category_tree'] = $helper->renderCategoryTree(null, array((int)$id_category), 'categoryBox', true, false, array(), false, true);

			// used to build the new url when changing category
			$this->tpl_list_vars['base_url'] = preg_replace('#&id_category=[0-9]*#', '', self::$currentIndex).'&token='.$this->token;
		}
		// @todo module free
		$this->tpl_form_vars['vat_number'] = file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php');

		parent::initContent();
	}

	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('duplicate');
		$this->addRowAction('delete');
		return parent::renderList();
	}

	public function ajaxProcessProductManufacturers()
	{
		$manufacturers = Manufacturer::getManufacturers();
		$jsonArray = array();
			if ($manufacturers)
				foreach ($manufacturers as $manufacturer)
					$jsonArray[] = '{"optionValue": "'.(int)$manufacturer['id_manufacturer'].'", "optionDisplay": "'.htmlspecialchars(trim($manufacturer['name'])).'"}';
			die('['.implode(',', $jsonArray).']');
	}

	/**
	 * Build a categories tree
	 *
	 * @param array $indexedCategories Array with categories where product is indexed (in order to check checkbox)
	 * @param array $categories Categories to list
	 * @param array $current Current category
	 * @param integer $id_category Current category id
	 */
	public static function recurseCategoryForInclude($id_obj, $indexedCategories, $categories, $current, $id_category = 1, $id_category_default = null, $has_suite = array())
	{
		global $done;
		static $irow;
		$content = '';
		if (!isset($done[$current['infos']['id_parent']]))
			$done[$current['infos']['id_parent']] = 0;
		$done[$current['infos']['id_parent']] += 1;

		$todo = count($categories[$current['infos']['id_parent']]);
		$doneC = $done[$current['infos']['id_parent']];

		$level = $current['infos']['level_depth'] + 1;

		$content .= '
		<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
			<td>
				<input type="checkbox" name="categoryBox[]" class="categoryBox'.($id_category_default == $id_category ? ' id_category_default' : '').'" id="categoryBox_'.$id_category.'" value="'.$id_category.'"'.((in_array($id_category, $indexedCategories) || ((int)(Tools::getValue('id_category')) == $id_category && !(int)($id_obj))) ? ' checked="checked"' : '').' />
			</td>
			<td>
				'.$id_category.'
			</td>
			<td>';
			for ($i = 2; $i < $level; $i++)
				$content .= '<img src="../img/admin/lvl_'.$has_suite[$i - 2].'.gif" alt="" />';
			$content .= '<img src="../img/admin/'.($level == 1 ? 'lv1.gif' : 'lv2_'.($todo == $doneC ? 'f' : 'b').'.gif').'" alt="" /> &nbsp;
			<label for="categoryBox_'.$id_category.'" class="t">'.stripslashes($current['infos']['name']).'</label></td>
		</tr>';

		if ($level > 1)
			$has_suite[] = ($todo == $doneC ? 0 : 1);
		if (isset($categories[$id_category]))
			foreach ($categories[$id_category] as $key => $row)
				if ($key != 'infos')
					$content .= AdminProductsController::recurseCategoryForInclude($id_obj, $indexedCategories, $categories, $categories[$id_category][$key], $key, $id_category_default, $has_suite);
		return $content;
	}

	protected function _displayDraftWarning($active)
	{
		$content = '<div class="warn draft" style="'.($active ? 'display:none' : '').'">
				<p>
				<span style="float: left">
				'.$this->l('Your product will be saved as a draft.').'
				</span>
				<span style="float:right"><a href="#" class="button" style="display: block" onclick="submitAddProductAndPreview()" >'.$this->l('Save and preview.').'</a></span>
				<input type="hidden" name="fakeSubmitAddProductAndPreview" id="fakeSubmitAddProductAndPreview" />
				<br class="clear" />
				</p>
	 		</div>';
			$this->tpl_form_vars['draft_warning'] = $content;
	}

	public function initToolbar()
	{
		parent::initToolbar();
		if ($this->display == 'edit' || $this->display == 'add')
		{
			if ($product = $this->loadObject(true))
			{
				if ((bool)$product->id)
				{
					// adding button for delete this product
					if ($this->tabAccess['delete'] && $this->display != 'add')
						$this->toolbar_btn['delete'] = array(
							'short' => 'Delete',
							'href' => $this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.(int)$product->id.'&amp;deleteproduct',
							'desc' => $this->l('Delete this product.'),
							'confirm' => 1,
							'js' => 'if (confirm(\''.$this->l('Delete product?').'\')){return true;}else{event.preventDefault();}'
						);

					// adding button for duplicate this product
					if ($this->tabAccess['add'] && $this->display != 'add')
						$this->toolbar_btn['duplicate'] = array(
							'short' => 'Duplicate',
							'desc' => $this->l('Duplicate'),
							'confirm' => 1,
							'js' => 'if (confirm(\''.$this->l('Also copy images').' ?\')) document.location = \''.$this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.(int)$product->id.'&amp;duplicateproduct\'; else document.location = \''.$this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.(int)$product->id.'&amp;duplicateproduct&amp;noimage=1\';'
						);

					// adding button for preview this product
					if ($url_preview = $this->getPreviewUrl($product))
						$this->toolbar_btn['preview'] = array(
							'short' => 'Preview',
							'href' => $url_preview,
							'desc' => $this->l('Preview'),
							'target' => true,
							'class' => 'previewUrl'
						);

					// adding button for preview this product statistics
					if (file_exists(_PS_MODULE_DIR_.'statsproduct/statsproduct.php') && $this->display != 'add')
						$this->toolbar_btn['stats'] = array(
						'short' => 'Statistics',
						'href' => $this->context->link->getAdminLink('AdminStats').'&amp;module=statsproduct&amp;id_product='.(int)$product->id,
						'desc' => $this->l('Product sales'),
					);

					// adding button for adding a new combination in Combination tab
					$this->toolbar_btn['newCombination'] = array(
						'short' => 'New combination',
						'desc' => $this->l('New combination'),
						'class' => 'toolbar-new'
					);
				}

				if ($this->tabAccess['edit'])
				{
					$this->toolbar_btn['save'] = array(
						'short' => 'Save',
						'href' => '#',
						'desc' => $this->l('Save'),
					);

					$this->toolbar_btn['save-and-stay'] = array(
						'short' => 'SaveAndStay',
						'href' => '#',
						'desc' => $this->l('Save and stay'),
					);
				}
			}
		}
		else
			$this->toolbar_btn['import'] = array(
					'href' => $this->context->link->getAdminLink('AdminImport', true).'&import_type='.$this->table,
					'desc' => $this->l('Import')
				);
		
		$this->context->smarty->assign('toolbar_scroll', 1);
		$this->context->smarty->assign('show_toolbar', 1);
		$this->context->smarty->assign('toolbar_btn', $this->toolbar_btn);
	}

	public function initToolbarTitle()
	{
		parent::initToolbarTitle();
		if ($product = $this->loadObject(true))
			if ((bool)$product->id && $this->display != 'list' && isset($this->toolbar_title[2]))
				$this->toolbar_title[2] = $this->toolbar_title[2].' ('.$this->product_name.')';
	}

	/**
	 * renderForm contains all necessary initialization needed for all tabs
	 *
	 * @return void
	 */
	public function renderForm()
	{
		// This nice code (irony) is here to store the product name, because the row after will erase product name in multishop context
		$this->product_name = $this->object->name[$this->context->language->id];

		if (!method_exists($this, 'initForm'.$this->tab_display))
			return;

		$product = $this->object;

		// Product for multishop
		$this->context->smarty->assign('bullet_common_field', '');
		if (Shop::isFeatureActive() && $this->display == 'edit')
		{
			if (Shop::getContext() != Shop::CONTEXT_SHOP)
			{
				$this->context->smarty->assign(array(
					'display_multishop_checkboxes' => true,
					'multishop_check' => Tools::getValue('multishop_check'),
				));
			}

			if (Shop::getContext() != Shop::CONTEXT_ALL)
			{
				$this->context->smarty->assign('bullet_common_field', '<img src="themes/'.$this->context->employee->bo_theme.'/img/bullet_orange.png" style="vertical-align: bottom" />');
				$this->context->smarty->assign('display_common_field', true);
			}
		}

		$this->tpl_form_vars['tabs_preloaded'] = $this->available_tabs;

		$this->tpl_form_vars['product_type'] = (int)Tools::getValue('type_product', $product->getType());

		$this->getLanguages();

		$this->tpl_form_vars['id_lang_default'] = Configuration::get('PS_LANG_DEFAULT');

		$this->tpl_form_vars['currentIndex'] = self::$currentIndex;
		$this->tpl_form_vars['display_multishop_checkboxes'] = (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP && $this->display == 'edit');
		$this->fields_form = array('');
		$this->display = 'edit';
		$this->tpl_form_vars['token'] = $this->token;
		$this->tpl_form_vars['combinationImagesJs'] = $this->getCombinationImagesJs();
		$this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
		$this->tpl_form_vars['post_data'] = Tools::jsonEncode($_POST);
		$this->tpl_form_vars['save_error'] = !empty($this->errors);

		// autoload rich text editor (tiny mce)
		$this->tpl_form_vars['tinymce'] = true;
		$iso = $this->context->language->iso_code;
		$this->tpl_form_vars['iso'] = file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en';
		$this->tpl_form_vars['ad'] = dirname($_SERVER['PHP_SELF']);

		if (Validate::isLoadedObject(($this->object)))
			$id_product = (int)$this->object->id;
		else
			$id_product = (int)Tools::getvalue('id_product');

		$this->tpl_form_vars['form_action'] = $this->context->link->getAdminLink('AdminProducts').'&amp;'.($id_product ? 'id_product='.(int)$id_product : 'addproduct');
		$this->tpl_form_vars['id_product'] = $id_product;

		// Transform configuration option 'upload_max_filesize' in octets
		$upload_max_filesize = Tools::getOctets(ini_get('upload_max_filesize'));

		// Transform configuration option 'upload_max_filesize' in MegaOctets
		$upload_max_filesize = ($upload_max_filesize / 1024) / 1024;

		$this->tpl_form_vars['upload_max_filesize'] = $upload_max_filesize;
		$this->tpl_form_vars['country_display_tax_label'] = $this->context->country->display_tax_label;
		$this->tpl_form_vars['has_combinations'] = $this->object->hasAttributes();

		$this->product_exists_in_shop = true;
		if ($this->display == 'edit' && Validate::isLoadedObject($product) && Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP && !$product->isAssociatedToShop($this->context->shop->id))
		{
			$this->product_exists_in_shop = false;
			if ($this->tab_display == 'Informations')
				$this->displayWarning($this->l('Warning: The product does not exist in this shop.'));
			
			$default_product = new Product();
			$definition = ObjectModel::getDefinition($product);
			foreach ($definition['fields'] as $field_name => $field)
				if (isset($field['shop']) && $field['shop'])
					$product->$field_name = ObjectModel::formatValue($default_product->$field_name, $field['type']);
		}

		// let's calculate this once for all
		if (!Validate::isLoadedObject($this->object) && Tools::getValue('id_product'))
			$this->errors[] = 'Unable to load object';
		else
		{
			$this->_displayDraftWarning($this->object->active);

			// if there was an error while saving, we don't want to lose posted data
			if (!empty($this->errors))
				$this->copyFromPost($this->object, $this->table);

			$this->initPack($this->object);
			$this->{'initForm'.$this->tab_display}($this->object);
			$this->tpl_form_vars['product'] = $this->object;
			if ($this->ajax)
				if (!isset($this->tpl_form_vars['custom_form']))
					throw new PrestaShopException('custom_form empty for action '.$this->tab_display);
				else
					return $this->tpl_form_vars['custom_form'];
		}
		$parent = parent::renderForm();
		$this->addJqueryPlugin(array('autocomplete', 'fancybox', 'typewatch'));
		return $parent;
	}

	public function getPreviewUrl(Product $product)
	{
		if (!ShopUrl::getMainShopDomain())
			return false;
		$is_rewrite_active = (bool)Configuration::get('PS_REWRITING_SETTINGS');
		$preview_url = $this->context->link->getProductLink(
			$product,
			$this->getFieldValue($product, 'link_rewrite', $this->context->language->id),
			Category::getLinkRewrite($product->id_category_default, $this->context->language->id),
			null,
			null,
			Context::getContext()->shop->id,
			0,
			$is_rewrite_active
		);
		if (!$product->active)
		{
			$preview_url = $this->context->link->getProductLink(
				$product,
				$this->getFieldValue($product, 'link_rewrite', $this->default_form_language),
				Category::getLinkRewrite($this->getFieldValue($product, 'id_category_default'), $this->context->language->id),
				null,
				null,
				Context::getContext()->shop->id,
				0,
				$is_rewrite_active
			);

			if (!$product->active)
			{
				$admin_dir = dirname($_SERVER['PHP_SELF']);
				$admin_dir = substr($admin_dir, strrpos($admin_dir, '/') + 1);

				$preview_url .= $product->active ? '' : '&adtoken='.$this->token.'&ad='.$admin_dir.'&id_employee='.(int)$this->context->employee->id;
			}
		}
		return $preview_url;
	}

	/**
	* Post treatment for suppliers
	*/
	public function processSuppliers()
	{
		if ((int)Tools::getValue('supplier_loaded') === 1 && Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
		{
			// Get all id_product_attribute
			$attributes = $product->getAttributesResume($this->context->language->id);
			if (empty($attributes))
				$attributes[] = array(
					'id_product_attribute' => 0,
					'attribute_designation' => ''
				);

			// Get all available suppliers
			$suppliers = Supplier::getSuppliers();

			// Get already associated suppliers
			$associated_suppliers = ProductSupplier::getSupplierCollection($product->id);

			$suppliers_to_associate = array();
			$new_default_supplier = 0;

			if (Tools::isSubmit('default_supplier'))
				$new_default_supplier = (int)Tools::getValue('default_supplier');

			// Get new associations
			foreach ($suppliers as $supplier)
				if (Tools::isSubmit('check_supplier_'.$supplier['id_supplier']))
					$suppliers_to_associate[] = $supplier['id_supplier'];

			// Delete already associated suppliers if needed
			foreach ($associated_suppliers as $key => $associated_supplier)
				if (!in_array($associated_supplier->id_supplier, $suppliers_to_associate))
				{
					$associated_supplier->delete();
					unset($associated_suppliers[$key]);
				}

			// Associate suppliers
			foreach ($suppliers_to_associate as $id)
			{
				$to_add = true;
				foreach ($associated_suppliers as $as)
					if ($id == $as->id_supplier)
						$to_add = false;

				if ($to_add)
				{
					$product_supplier = new ProductSupplier();
					$product_supplier->id_product = $product->id;
					$product_supplier->id_product_attribute = 0;
					$product_supplier->id_supplier = $id;
					$product_supplier->save();

					$associated_suppliers[] = $product_supplier;
				}
			}

			// Manage references and prices
			foreach ($attributes as $attribute)
				foreach ($associated_suppliers as $supplier)
				{
					if (Tools::isSubmit('supplier_reference_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier) ||
						(Tools::isSubmit('product_price_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier) &&
						 Tools::isSubmit('product_price_currency_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier)))
					{
						$reference = pSQL(
							Tools::getValue(
								'supplier_reference_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier,
								''
							)
						);

						$price = (float)str_replace(
							array(' ', ','),
							array('', '.'),
							Tools::getValue(
								'product_price_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier,
								0
							)
						);

						$price = Tools::ps_round($price, 6);

						$id_currency = (int)Tools::getValue(
							'product_price_currency_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier,
							0
						);

						if ($id_currency <= 0 || ( !($result = Currency::getCurrency($id_currency)) || empty($result) ))
							$this->errors[] = Tools::displayError($this->l('The selected currency is not valid.'));

						// Save product-supplier data
						$product_supplier_id = (int)ProductSupplier::getIdByProductAndSupplier($product->id, $attribute['id_product_attribute'], $supplier->id_supplier);
						
						if (!$product_supplier_id)
						{
							$product->addSupplierReference($supplier->id_supplier, (int)$attribute['id_product_attribute'], $reference, (float)Tools::convertPrice($price, $id_currency), (int)$id_currency);
							if ($product->id_supplier == $supplier->id_supplier)
							{
								if ((int)$attribute['id_product_attribute'] > 0)
								{
									$data = array(
										'supplier_reference' => pSQL($reference),
										'wholesale_price' => (float)Tools::convertPrice($price, $id_currency)
									);
									$where = '
										a.id_product = '.(int)$product->id.'
										AND a.id_product_attribute = '.(int)$attribute['id_product_attribute'];
									ObjectModel::updateMultishopTable('Combination', $data, $where);
								}
								else
								{
									$product->wholesale_price = (float)Tools::convertPrice($price, $id_currency); //converted in the default currency
									$product->supplier_reference = pSQL($reference);
									$product->update();
								}
							}
						}
						else
						{
							$product_supplier = new ProductSupplier($product_supplier_id);
							$product_supplier->id_currency = (int)$id_currency;
							$product_supplier->product_supplier_price_te = (float)Tools::convertPrice($price, $id_currency); //converted in the default currency
							$product_supplier->product_supplier_reference = pSQL($reference);
							$product_supplier->update();

						}
					}
					elseif (Tools::isSubmit('supplier_reference_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier))
					{
						//int attribute with default values if possible
						if ((int)$attribute['id_product_attribute'] > 0)
						{
							$product_supplier = new ProductSupplier();
							$product_supplier->id_product = $product->id;
							$product_supplier->id_product_attribute = (int)$attribute['id_product_attribute'];
							$product_supplier->id_supplier = $supplier->id_supplier;
							$product_supplier->save();
						}
					}
				}
			// Manage defaut supplier for product
			if ($new_default_supplier != $product->id_supplier)
			{
				$this->object->id_supplier = $new_default_supplier;
				$this->object->update();
			}
		}
	}

	/**
	* Post treatment for warehouses
	*/
	public function processWarehouses()
	{
		if ((int)Tools::getValue('warehouse_loaded') === 1 && Validate::isLoadedObject($product = new Product((int)$id_product = Tools::getValue('id_product'))))
		{
			// Get all id_product_attribute
			$attributes = $product->getAttributesResume($this->context->language->id);
			if (empty($attributes))
				$attributes[] = array(
					'id_product_attribute' => 0,
					'attribute_designation' => ''
				);

			// Get all available warehouses
			$warehouses = Warehouse::getWarehouses(true);

			// Get already associated warehouses
			$associated_warehouses_collection = WarehouseProductLocation::getCollection($product->id);

			$elements_to_manage = array();

			// get form inforamtion
			foreach ($attributes as $attribute)
			{
				foreach ($warehouses as $warehouse)
				{
					$key = $warehouse['id_warehouse'].'_'.$product->id.'_'.$attribute['id_product_attribute'];

					// get elements to manage
					if (Tools::isSubmit('check_warehouse_'.$key))
					{
						$location = Tools::getValue('location_warehouse_'.$key, '');
						$elements_to_manage[$key] = $location;
					}
				}
			}

			// Delete entry if necessary
			foreach ($associated_warehouses_collection as $awc)
			{
				if (!array_key_exists($awc->id_warehouse.'_'.$awc->id_product.'_'.$awc->id_product_attribute, $elements_to_manage))
					$awc->delete();
			}

			// Manage locations
			foreach ($elements_to_manage as $key => $location)
			{
				$params = explode('_', $key);

				$wpl_id = (int)WarehouseProductLocation::getIdByProductAndWarehouse((int)$params[1], (int)$params[2], (int)$params[0]);

				if (empty($wpl_id))
				{
					//create new record
					$warehouse_location_entity = new WarehouseProductLocation();
					$warehouse_location_entity->id_product = (int)$params[1];
					$warehouse_location_entity->id_product_attribute = (int)$params[2];
					$warehouse_location_entity->id_warehouse = (int)$params[0];
					$warehouse_location_entity->location = pSQL($location);
					$warehouse_location_entity->save();
				}
				else
				{
					$warehouse_location_entity = new WarehouseProductLocation((int)$wpl_id);

					$location = pSQL($location);

					if ($location != $warehouse_location_entity->location)
					{
						$warehouse_location_entity->location = pSQL($location);
						$warehouse_location_entity->update();
					}
				}
			}
			StockAvailable::synchronize((int)$id_product);
		}
	}

	public function initFormAssociations($obj)
	{
		$product = $obj;
		$data = $this->createTemplate($this->tpl_form);
		// Prepare Categories tree for display in Associations tab
		$root = Category::getRootCategory();
		$default_category = $this->context->cookie->id_category_products_filter ? $this->context->cookie->id_category_products_filter : Context::getContext()->shop->id_category;
		if (!$product->id || !$product->isAssociatedToShop())
			$selected_cat = Category::getCategoryInformations(Tools::getValue('categoryBox', array($default_category)), $this->default_form_language);
		else
		{
			if (Tools::isSubmit('categoryBox'))
				$selected_cat = Category::getCategoryInformations(Tools::getValue('categoryBox', array($default_category)), $this->default_form_language);
			else
				$selected_cat = Product::getProductCategoriesFull($product->id, $this->default_form_language);
		}

		// Multishop block
		$data->assign('feature_shop_active', Shop::isFeatureActive());
		$helper = new HelperForm();
		if ($this->object && $this->object->id)
			$helper->id = $this->object->id;
		else
			$helper->id = null;
		$helper->table = $this->table;
		$helper->identifier = $this->identifier;

		// Accessories block
		$accessories = Product::getAccessoriesLight($this->context->language->id, $product->id);

		if ($post_accessories = Tools::getValue('inputAccessories'))
		{
			$post_accessories_tab = explode('-', Tools::getValue('inputAccessories'));
			foreach ($post_accessories_tab as $accessory_id)
				if (!$this->haveThisAccessory($accessory_id, $accessories) && $accessory = Product::getAccessoryById($accessory_id))
					$accessories[] = $accessory;
		}
		$data->assign('accessories', $accessories);

		$product->manufacturer_name = Manufacturer::getNameById($product->id_manufacturer);

		$tab_root = array('id_category' => $root->id, 'name' => $root->name);
		$helper = new Helper();
		$category_tree = $helper->renderCategoryTree($tab_root, $selected_cat, 'categoryBox', false, true, array(), false, true);
		$data->assign(array('default_category' => $default_category,
					'selected_cat_ids' => implode(',', array_keys($selected_cat)),
					'selected_cat' => $selected_cat,
					'id_category_default' => $product->getDefaultCategory(),
					'category_tree' => $category_tree,
					'product' => $product,
					'link' => $this->context->link,
					'is_shop_context' => Shop::getContext() == Shop::CONTEXT_SHOP
		));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormPrices($obj)
	{
		$data = $this->createTemplate($this->tpl_form);
		$product = $obj;
		if ($obj->id)
		{
			$shops = Shop::getShops();
			$countries = Country::getCountries($this->context->language->id);
			$groups = Group::getGroups($this->context->language->id);
			$currencies = Currency::getCurrencies();
			$attributes = $obj->getAttributesGroups((int)$this->context->language->id);
			$combinations = array();
			foreach ($attributes as $attribute)
			{
				$combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
				if (!isset($combinations[$attribute['id_product_attribute']]['attributes']))
					$combinations[$attribute['id_product_attribute']]['attributes'] = '';
				$combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'].' - ';

				$combinations[$attribute['id_product_attribute']]['price'] = Tools::displayPrice(
					Tools::convertPrice(
						Product::getPriceStatic((int)$obj->id, false, $attribute['id_product_attribute']),
						$this->context->currency
					), $this->context->currency
				);
			}
			foreach ($combinations as &$combination)
				$combination['attributes'] = rtrim($combination['attributes'], ' - ');
			$data->assign('specificPriceModificationForm', $this->_displaySpecificPriceModificationForm(
				$this->context->currency, $shops, $currencies, $countries, $groups)
			);

			$data->assign('ecotax_tax_excl', $obj->ecotax);
			$this->_applyTaxToEcotax($obj);

			$data->assign(array(
				'shops' => $shops,
				'admin_one_shop' => count($this->context->employee->getAssociatedShops()) == 1,
				'currencies' => $currencies,
				'countries' => $countries,
				'groups' => $groups,
				'combinations' => $combinations,
				'multi_shop' => Shop::isFeatureActive(),
				'link' => new Link()
			));
		}
		else
		{
			$this->displayWarning($this->l('You must save this product before adding specific pricing'));
			$product->id_tax_rules_group = (int)Product::getIdTaxRulesGroupMostUsed();
			$data->assign('ecotax_tax_excl', 0);
		}

		// prices part
		$data->assign(array(
			'link' => $this->context->link,
			'currency' => $currency = $this->context->currency,
			'tax_rules_groups' => TaxRulesGroup::getTaxRulesGroups(true),
			'taxesRatesByGroup' => TaxRulesGroup::getAssociatedTaxRatesByIdCountry($this->context->country->id),
			'ecotaxTaxRate' => Tax::getProductEcotaxRate(),
			'tax_exclude_taxe_option' => Tax::excludeTaxeOption(),
			'ps_use_ecotax' => Configuration::get('PS_USE_ECOTAX'),
		));

		$product->price = Tools::convertPrice($product->price, $this->context->currency, true, $this->context);
		if ($product->unit_price_ratio != 0)
			$data->assign('unit_price', Tools::ps_round($product->price / $product->unit_price_ratio, 2));
		else
			$data->assign('unit_price', 0);
		$data->assign('ps_tax', Configuration::get('PS_TAX'));

		$data->assign('country_display_tax_label', $this->context->country->display_tax_label);
		$data->assign(array(
			'currency', $this->context->currency,
			'product' => $product,
			'token' => $this->token
		));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormSeo($product)
	{
		$data = $this->createTemplate($this->tpl_form);

		$data->assign(array(
			'product' => $product,
			'languages' => $this->_languages,
			'ps_ssl_enabled' => Configuration::get('PS_SSL_ENABLED'),
			'curent_shop_url' => $this->context->shop->getBaseURL()
		));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	/**
	 * Get an array of pack items for display from the product object if specified, else from POST/GET values
	 *
	 * @param Product $product
	 * @return array of pack items
	 */
	public function getPackItems($product = null)
	{
		$pack_items = array();

		if (!$product)
		{
			$names_input = Tools::getValue('namePackItems');
			$ids_input = Tools::getValue('inputPackItems');
			if (!$names_input || !$ids_input)
				return array();
			// ids is an array of string with format : QTYxID
			$ids = array_unique(explode('-', $ids_input));
			$names = array_unique(explode('¤', $names_input));

			if (!empty($ids))
			{
				$length = count($ids);
				for ($i = 0; $i < $length; $i++)
					if (!empty($ids[$i]) && !empty($names[$i]))
					{
						list($pack_items[$i]['pack_quantity'], $pack_items[$i]['id']) = explode('x', $ids[$i]);
						$exploded_name = explode('x', $names[$i]);
						$pack_items[$i]['name'] = $exploded_name[1];
					}
			}
		}
		else
		{
			$i = 0;
			foreach ($product->packItems as $pack_item)
			{
				$pack_items[$i]['id'] = $pack_item->id;
				$pack_items[$i]['pack_quantity'] = $pack_item->pack_quantity;
				$pack_items[$i]['name']	= $pack_item->name;
				$i++;
			}
		}

		return $pack_items;
	}

	public function initFormPack($product)
	{
		$data = $this->createTemplate($this->tpl_form);

		// If pack items have been submitted, we want to display them instead of the actuel content of the pack
		// in database. In case of a submit error, the posted data is not lost and can be sent again.
		if (Tools::getValue('namePackItems'))
		{
			$input_pack_items = Tools::getValue('inputPackItems');
			$input_namepack_items = Tools::getValue('namePackItems');
			$pack_items = $this->getPackItems();
		}
		else
		{
			$product->packItems = Pack::getItems($product->id, $this->context->language->id);
			$pack_items = $this->getPackItems($product);
			$input_namepack_items = '';
			$input_pack_items = '';
			foreach ($pack_items as $pack_item)
			{
				$input_pack_items .= $pack_item['pack_quantity'].'x'.$pack_item['id'].'-';
				$input_namepack_items .= $pack_item['pack_quantity'].' x '.$pack_item['name'].'¤';
			}
		}

		$data->assign(array(
			'input_pack_items' => $input_pack_items,
			'input_namepack_items' => $input_namepack_items,
			'pack_items' => $pack_items,
			'product_type' => (int)Tools::getValue('type_product', $product->getType())
		));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormVirtualProduct($product)
	{
		$data = $this->createTemplate($this->tpl_form);

		$currency = $this->context->currency;

		/*
		* Form for adding a virtual product like software, mp3, etc...
		*/
		$product_download = new ProductDownload();
		if ($id_product_download = $product_download->getIdFromIdProduct($this->getFieldValue($product, 'id')))
			$product_download = new ProductDownload($id_product_download);
		$product->{'productDownload'} = $product_download;

		// @todo handle is_virtual with the value of the product
		$exists_file = realpath(_PS_DOWNLOAD_DIR_).'/'.$product->productDownload->filename;
		$data->assign('product_downloaded', $product->productDownload->id && !empty($product->productDownload->display_filename));

		if (!file_exists($exists_file)
			&& !empty($product->productDownload->display_filename)
			&& empty($product->cache_default_attribute))
			$msg = sprintf(Tools::displayError('This file "%s" is missing.'), $product->productDownload->display_filename);
		else
			$msg = '';

		$data->assign('download_product_file_missing', $msg);
		$data->assign('download_dir_writable', ProductDownload::checkWritableDir());

		$data->assign('up_filename', strval(Tools::getValue('virtual_product_filename')));

		$product->productDownload->nb_downloadable = ($product->productDownload->id > 0) ? $product->productDownload->nb_downloadable : htmlentities(Tools::getValue('virtual_product_nb_downloable'), ENT_COMPAT, 'UTF-8');
		$product->productDownload->date_expiration = ($product->productDownload->id > 0) ? ((!empty($product->productDownload->date_expiration) && $product->productDownload->date_expiration != '0000-00-00 00:00:00') ? date('Y-m-d', strtotime($product->productDownload->date_expiration)) : '' ) : htmlentities(Tools::getValue('virtual_product_expiration_date'), ENT_COMPAT, 'UTF-8');
		$product->productDownload->nb_days_accessible = ($product->productDownload->id > 0) ? $product->productDownload->nb_days_accessible : htmlentities(Tools::getValue('virtual_product_nb_days'), ENT_COMPAT, 'UTF-8');
		$product->productDownload->is_shareable = $product->productDownload->id > 0 && $product->productDownload->is_shareable;

		$data->assign('ad', dirname($_SERVER['PHP_SELF']));
		$data->assign('product', $product);
		$data->assign('token', $this->token);
		$data->assign('currency', $currency);
		$data->assign($this->tpl_form_vars);
		$data->assign('link', $this->context->link);
		$data->assign('is_file', $product->productDownload->checkFile());
		$this->tpl_form_vars['product'] = $product;
		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	protected function _getFinalPrice($specific_price, $product_price, $tax_rate)
	{
		return $this->object->getPrice(false, $specific_price['id_product_attribute'], 2);
	}

	protected function _displaySpecificPriceModificationForm($defaultCurrency, $shops, $currencies, $countries, $groups)
	{
		$content = '';
		if (!($obj = $this->loadObject()))
			return;
		$specific_prices = SpecificPrice::getByProductId((int)$obj->id);
		$specific_price_priorities = SpecificPrice::getPriority((int)$obj->id);

		$tax_rate = $obj->getTaxesRate(Address::initialize());

		$tmp = array();
		foreach ($shops as $shop)
			$tmp[$shop['id_shop']] = $shop;
		$shops = $tmp;
		$tmp = array();
		foreach ($currencies as $currency)
			$tmp[$currency['id_currency']] = $currency;
		$currencies = $tmp;

		$tmp = array();
		foreach ($countries as $country)
			$tmp[$country['id_country']] = $country;
		$countries = $tmp;

		$tmp = array();
		foreach ($groups as $group)
			$tmp[$group['id_group']] = $group;
		$groups = $tmp;

		if (!is_array($specific_prices) || !count($specific_prices))
			$content .= '
				<tr>
					<td colspan="13">'.$this->l('No specific prices.').'</td>
				</tr>';
		else
		{
			$i = 0;
			foreach ($specific_prices as $specific_price)
			{
				$current_specific_currency = $currencies[($specific_price['id_currency'] ? $specific_price['id_currency'] : $defaultCurrency->id)];
				if ($specific_price['reduction_type'] == 'percentage')
					$impact = '- '.($specific_price['reduction'] * 100).' %';
				elseif ($specific_price['reduction'] > 0)
					$impact = '- '.Tools::displayPrice(Tools::ps_round($specific_price['reduction'], 2), $current_specific_currency);
				else
					$impact = '--';

				if ($specific_price['from'] == '0000-00-00 00:00:00' && $specific_price['to'] == '0000-00-00 00:00:00')
					$period = $this->l('Unlimited');
				else
					$period = $this->l('From').' '.($specific_price['from'] != '0000-00-00 00:00:00' ? $specific_price['from'] : '0000-00-00 00:00:00').'<br />'.$this->l('To').' '.($specific_price['to'] != '0000-00-00 00:00:00' ? $specific_price['to'] : '0000-00-00 00:00:00');
				if ($specific_price['id_product_attribute'])
				{
					$combination = new Combination((int)$specific_price['id_product_attribute']);
					$attributes = $combination->getAttributesName((int)$this->context->language->id);
					$attributes_name = '';
					foreach ($attributes as $attribute)
						$attributes_name .= $attribute['name'].' - ';
					$attributes_name = rtrim($attributes_name, ' - ');
				}
				else
					$attributes_name = $this->l('All combinations');

				$rule = new SpecificPriceRule((int)$specific_price['id_specific_price_rule']);
				$rule_name = ($rule->id ? $rule->name : '--');

				if ($specific_price['id_customer'])
				{
					$customer = new Customer((int)$specific_price['id_customer']);
					if (Validate::isLoadedObject($customer))
						$customer_full_name = $customer->firstname.' '.$customer->lastname;
					unset($customer);
				}

				if (!$specific_price['id_shop'] || in_array($specific_price['id_shop'], Shop::getContextListShopID()))
				{
					$content .= '
					<tr '.($i % 2 ? 'class="alt_row"' : '').'>
						<td class="cell border">'.$rule_name.'</td>
						<td class="cell border">'.$attributes_name.'</td>';

					$can_delete_specific_prices = true;
					if (Shop::isFeatureActive())
					{
						$id_shop_sp = $specific_price['id_shop'];
						$can_delete_specific_prices = (count($this->context->employee->getAssociatedShops()) > 1 && !$id_shop_sp) || $id_shop_sp;
						$content .= '
						<td class="cell border">'.($id_shop_sp ? $shops[$id_shop_sp]['name'] : $this->l('All shops')).'</td>';
					}
					$price = Tools::ps_round($specific_price['price'], 2);
					$fixed_price = ($price == Tools::ps_round($obj->price, 2) || $specific_price['price'] == -1) ? '--' : Tools::displayPrice($price, $current_specific_currency);
					$content .= '
						<td class="cell border">'.($specific_price['id_currency'] ? $currencies[$specific_price['id_currency']]['name'] : $this->l('All currencies')).'</td>
						<td class="cell border">'.($specific_price['id_country'] ? $countries[$specific_price['id_country']]['name'] : $this->l('All countries')).'</td>
						<td class="cell border">'.($specific_price['id_group'] ? $groups[$specific_price['id_group']]['name'] : $this->l('All groups')).'</td>
						<td class="cell border" title="'.$this->l('ID:').' '.$specific_price['id_customer'].'">'.(isset($customer_full_name) ? $customer_full_name : $this->l('All customers')).'</td>
						<td class="cell border">'.$fixed_price.'</td>
						<td class="cell border">'.$impact.'</td>
						<td class="cell border">'.$period.'</td>
						<td class="cell border">'.$specific_price['from_quantity'].'</th>
						<td class="cell border">'.((!$rule->id && $can_delete_specific_prices) ? '<a class="btn btn-default" name="delete_link" href="'.self::$currentIndex.'&id_product='.(int)Tools::getValue('id_product').'&action=deleteSpecificPrice&id_specific_price='.(int)($specific_price['id_specific_price']).'&token='.Tools::getValue('token').'"><i class="icon-trash"></i></a>': '').'</td>
					</tr>';
					$i++;
					unset($customer_full_name);
				}
			}
		}
		$content .= '
			</tbody>
		</table>';

		$content .= '
		<script type="text/javascript">
			var currencies = new Array();
			currencies[0] = new Array();
			currencies[0]["sign"] = "'.$defaultCurrency->sign.'";
			currencies[0]["format"] = '.$defaultCurrency->format.';
			';
			foreach ($currencies as $currency)
			{
				$content .= '
				currencies['.$currency['id_currency'].'] = new Array();
				currencies['.$currency['id_currency'].']["sign"] = "'.$currency['sign'].'";
				currencies['.$currency['id_currency'].']["format"] = '.$currency['format'].';
				';
			}
			$content .= '
		</script>
		';

		// Not use id_customer
		if ($specific_price_priorities[0] == 'id_customer')
			unset($specific_price_priorities[0]);
		// Reindex array starting from 0
		$specific_price_priorities = array_values($specific_price_priorities);

		$content .= '
		<hr/>
		<legend>'.$this->l('Priority management').'</legend>
		<div class="alert alert-info">
				'.$this->l('Sometimes one customer can fit into multiple price rules. Priorities allow you to define which rule applies to the customer.').'
		</div>';

		$content .= '
		<div class="row">
			<label class="control-label col-lg-3">'.$this->l('Priorities:').'</label>
			<div class="input-group col-lg-9">
				<select name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[0] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[0] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[0] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[0] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[1] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[1] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[1] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[1] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[2] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[2] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[2] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[2] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[3] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[3] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[3] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[3] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-9 col-offset-3">
				<p class="checkbox">
					<label for="specificPricePriorityToAll"><input type="checkbox" name="specificPricePriorityToAll" id="specificPricePriorityToAll" />'.$this->l('Apply to all products').'</label>
				</p>
			</div>
		</div>
		';
		return $content;
	}

	protected function _getCustomizationFieldIds($labels, $alreadyGenerated, $obj)
	{
		$customizableFieldIds = array();
		if (isset($labels[Product::CUSTOMIZE_FILE]))
			foreach ($labels[Product::CUSTOMIZE_FILE] as $id_customization_field => $label)
				$customizableFieldIds[] = 'label_'.Product::CUSTOMIZE_FILE.'_'.(int)($id_customization_field);
		if (isset($labels[Product::CUSTOMIZE_TEXTFIELD]))
			foreach ($labels[Product::CUSTOMIZE_TEXTFIELD] as $id_customization_field => $label)
				$customizableFieldIds[] = 'label_'.Product::CUSTOMIZE_TEXTFIELD.'_'.(int)($id_customization_field);
		$j = 0;
		for ($i = $alreadyGenerated[Product::CUSTOMIZE_FILE]; $i < (int)($this->getFieldValue($obj, 'uploadable_files')); $i++)
			$customizableFieldIds[] = 'newLabel_'.Product::CUSTOMIZE_FILE.'_'.$j++;
		$j = 0;
		for ($i = $alreadyGenerated[Product::CUSTOMIZE_TEXTFIELD]; $i < (int)($this->getFieldValue($obj, 'text_fields')); $i++)
			$customizableFieldIds[] = 'newLabel_'.Product::CUSTOMIZE_TEXTFIELD.'_'.$j++;
		return implode('¤', $customizableFieldIds);
	}

	protected function _displayLabelField(&$label, $languages, $default_language, $type, $fieldIds, $id_customization_field)
	{
		$content = '';
		$fieldsName = 'label_'.$type.'_'.(int)($id_customization_field);
		$fieldsContainerName = 'labelContainer_'.$type.'_'.(int)($id_customization_field);
		$content .= '<div id="'.$fieldsContainerName.'" class="translatable clear" style="line-height: 18px">';
		foreach ($languages as $language)
		{
			$fieldName = 'label_'.$type.'_'.(int)($id_customization_field).'_'.(int)($language['id_lang']);
			$text = (isset($label[(int)($language['id_lang'])])) ? $label[(int)($language['id_lang'])]['name'] : '';
			$content .= '<div class="lang_'.$language['id_lang'].'" id="'.$fieldName.'" style="display: '.((int)($language['id_lang']) == (int)($default_language) ? 'block' : 'none').'; clear: left; float: left; padding-bottom: 4px;">
						<input type="text" name="'.$fieldName.'" value="'.htmlentities($text, ENT_COMPAT, 'UTF-8').'" style="float: left" />
					</div>';
		}

		$required = (isset($label[(int)($language['id_lang'])])) ? $label[(int)($language['id_lang'])]['required'] : false;
		$content .= '</div>
				<div style="margin: 3px 0 0 3px; font-size: 11px">
					<input type="checkbox" name="require_'.$type.'_'.(int)($id_customization_field).'" id="require_'.$type.'_'.(int)($id_customization_field).'" value="1" '.($required ? 'checked="checked"' : '').' style="float: left; margin: 0 4px"/><label for="require_'.$type.'_'.(int)($id_customization_field).'" style="float: none; font-weight: normal;"> '.$this->l('required').'</label>
				</div>';
		return $content;
	}

	protected function _displayLabelFields(&$obj, &$labels, $languages, $default_language, $type)
	{
		$content = '';
		$type = (int)($type);
		$labelGenerated = array(Product::CUSTOMIZE_FILE => (isset($labels[Product::CUSTOMIZE_FILE]) ? count($labels[Product::CUSTOMIZE_FILE]) : 0), Product::CUSTOMIZE_TEXTFIELD => (isset($labels[Product::CUSTOMIZE_TEXTFIELD]) ? count($labels[Product::CUSTOMIZE_TEXTFIELD]) : 0));

		$fieldIds = $this->_getCustomizationFieldIds($labels, $labelGenerated, $obj);
		if (isset($labels[$type]))
			foreach ($labels[$type] as $id_customization_field => $label)
				$content .= $this->_displayLabelField($label, $languages, $default_language, $type, $fieldIds, (int)($id_customization_field));
		return $content;
	}

	public function initFormCustomization($obj)
	{
		$data = $this->createTemplate($this->tpl_form);

		if ((bool)$obj->id)
		{
			if ($this->product_exists_in_shop)
			{		
				$labels = $obj->getCustomizationFields();

				$has_file_labels = (int)$this->getFieldValue($obj, 'uploadable_files');
				$has_text_labels = (int)$this->getFieldValue($obj, 'text_fields');

				$data->assign(array(
					'obj' => $obj,
					'table' => $this->table,
					'languages' => $this->_languages,
					'has_file_labels' => $has_file_labels,
					'display_file_labels' => $this->_displayLabelFields($obj, $labels, $this->_languages, Configuration::get('PS_LANG_DEFAULT'), Product::CUSTOMIZE_FILE),
					'has_text_labels' => $has_text_labels,
					'display_text_labels' => $this->_displayLabelFields($obj, $labels, $this->_languages, Configuration::get('PS_LANG_DEFAULT'), Product::CUSTOMIZE_TEXTFIELD),
					'uploadable_files' => (int)($this->getFieldValue($obj, 'uploadable_files') ? (int)$this->getFieldValue($obj, 'uploadable_files') : '0'),
					'text_fields' => (int)($this->getFieldValue($obj, 'text_fields') ? (int)$this->getFieldValue($obj, 'text_fields') : '0'),
				));
			}
			else
				$this->displayWarning($this->l('You must save the product in this shop before adding customization.'));
		}
		else
			$this->displayWarning($this->l('You must save this product before adding customization.'));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormAttachments($obj)
	{
		$data = $this->createTemplate($this->tpl_form);

		if ((bool)$obj->id)
		{
			if ($this->product_exists_in_shop)
			{
				$attachment_name = array();
				$attachment_description = array();
				foreach ($this->_languages as $language)
				{
					$attachment_name[$language['id_lang']] = '';
					$attachment_description[$language['id_lang']] = '';
				}

				$iso_tiny_mce = $this->context->language->iso_code;
				$iso_tiny_mce = (file_exists(_PS_JS_DIR_.'tiny_mce/langs/'.$iso_tiny_mce.'.js') ? $iso_tiny_mce : 'en');

				$data->assign(array(
					'obj' => $obj,
					'table' => $this->table,
					'ad' => dirname($_SERVER['PHP_SELF']),
					'iso_tiny_mce' => $iso_tiny_mce,
					'languages' => $this->_languages,
					'attach1' => Attachment::getAttachments($this->context->language->id, $obj->id, true),
					'attach2' => Attachment::getAttachments($this->context->language->id, $obj->id, false),
					'default_form_language' => (int)Configuration::get('PS_LANG_DEFAULT'),
					'attachment_name' => $attachment_name,
					'attachment_description' => $attachment_description,
					'PS_ATTACHMENT_MAXIMUM_SIZE' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')
				));
			}
			else
				$this->displayWarning($this->l('You must save the product in this shop before adding attachements.'));
		}
		else
			$this->displayWarning($this->l('You must save this product before adding attachements.'));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormInformations($product)
	{
		$data = $this->createTemplate($this->tpl_form);

		$currency = $this->context->currency;
		$data->assign('languages', $this->_languages);
		$data->assign('currency', $currency);
		$this->object = $product;
		$this->display = 'edit';
		$data->assign('product_name_redirected', Product::getProductName((int)$product->id_product_redirected, null, (int)$this->context->language->id));
		/*
		* Form for adding a virtual product like software, mp3, etc...
		*/
		$product_download = new ProductDownload();
		if ($id_product_download = $product_download->getIdFromIdProduct($this->getFieldValue($product, 'id')))
			$product_download = new ProductDownload($id_product_download);
		$product->{'productDownload'} = $product_download;

		$cache_default_attribute = (int)$this->getFieldValue($product, 'cache_default_attribute');

		$product_props = array();
		// global informations
		array_push($product_props, 'reference', 'ean13', 'upc',
		'available_for_order', 'show_price', 'online_only',
		'id_manufacturer'
		);

		// specific / detailled information
		array_push($product_props,
		// physical product
		'width', 'height', 'weight', 'active',
		// virtual product
		'is_virtual', 'cache_default_attribute',
		// customization
		'uploadable_files', 'text_fields'
		);
		// prices
		array_push($product_props,
			'price', 'wholesale_price', 'id_tax_rules_group', 'unit_price_ratio', 'on_sale',
			'unity', 'minimum_quantity', 'additional_shipping_cost',
			'available_now', 'available_later', 'available_date'
		);

		if (Configuration::get('PS_USE_ECOTAX'))
			array_push($product_props, 'ecotax');

		foreach ($product_props as $prop)
			$product->$prop = $this->getFieldValue($product, $prop);

		$product->name['class'] = 'updateCurrentText';
		if (!$product->id)
			$product->name['class'] .= ' copy2friendlyUrl';

		$images = Image::getImages($this->context->language->id, $product->id);

		foreach ($images as $k => $image)
			$images[$k]['src'] = $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id], $product->id.'-'.$image['id_image'], 'small_default');
		$data->assign('images', $images);
		$data->assign('imagesTypes', ImageType::getImagesTypes('products'));

		$product->tags = Tag::getProductTags($product->id);

		$data->assign('product_type', (int)Tools::getValue('type_product', $product->getType()));
		$data->assign('is_in_pack', (int)Pack::isPacked($product->id));

		$check_product_association_ajax = false;
		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
			$check_product_association_ajax = true;

		// TinyMCE
		$iso_tiny_mce = $this->context->language->iso_code;
		$iso_tiny_mce = (file_exists(_PS_JS_DIR_.'tiny_mce/langs/'.$iso_tiny_mce.'.js') ? $iso_tiny_mce : 'en');
		$data->assign('ad', dirname($_SERVER['PHP_SELF']));
		$data->assign('iso_tiny_mce', $iso_tiny_mce);
		$data->assign('check_product_association_ajax', $check_product_association_ajax);
		$data->assign('id_lang', $this->context->language->id);
		$data->assign('product', $product);
		$data->assign('token', $this->token);
		$data->assign('currency', $currency);
		$data->assign($this->tpl_form_vars);
		$data->assign('link', $this->context->link);
		$data->assign('PS_PRODUCT_SHORT_DESC_LIMIT', Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') ? Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') : 400);
		$this->tpl_form_vars['product'] = $product;
		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormShipping($obj)
	{
		$data = $this->createTemplate($this->tpl_form);
		$data->assign(array(
						  'product' => $obj,
						  'ps_dimension_unit' => Configuration::get('PS_DIMENSION_UNIT'),
						  'ps_weight_unit' => Configuration::get('PS_WEIGHT_UNIT'),
						  'carrier_list' => $this->getCarrierList(),
						  'currency' => $this->context->currency,
						  'country_display_tax_label' =>  $this->context->country->display_tax_label
					  ));
		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	protected function getCarrierList()
	{
		$carrier_list = Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS);
		if ($product = $this->loadObject(true))
		{
			$carrier_selected_list = $product->getCarriers();
			foreach ($carrier_list as &$carrier)
				foreach ($carrier_selected_list as $carrier_selected)
					if ($carrier_selected['id_reference'] == $carrier['id_reference'])
					{
						$carrier['selected'] = true;
						continue;
					}
		}
		return $carrier_list;
	}

	protected function addCarriers()
	{
		if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
		{
			$carriers = array();
			
			if (Tools::getValue('carriers'))
				$carriers = Tools::getValue('carriers');
			$product->setCarriers($carriers);
		}
	}

	public function initFormImages($obj)
	{
		$data = $this->createTemplate($this->tpl_form);

		if ((bool)$obj->id)
		{
			if ($this->product_exists_in_shop)
			{
				$data->assign('product', $this->loadObject());

				$shops = false;
				if (Shop::isFeatureActive())
					$shops = Shop::getShops();

				if ($shops)
					foreach ($shops as $key => $shop)
						if (!$obj->isAssociatedToShop($shop['id_shop']))
							unset($shops[$key]);

				$data->assign('shops', $shops);

				$count_images = Db::getInstance()->getValue('
					SELECT COUNT(id_product)
					FROM '._DB_PREFIX_.'image
					WHERE id_product = '.(int)$obj->id
				);
				
				$images = Image::getImages($this->context->language->id, $obj->id);
				foreach ($images as $k => $image)
					$images[$k] = new Image($image['id_image']);

				if ($this->context->shop->getContext() == Shop::CONTEXT_SHOP)
					$current_shop_id = (int)$this->context->shop->id;
				else
					$current_shop_id = 0;
					
				$data->assign(array(
						'countImages' => $count_images,
						'id_product' => (int)Tools::getValue('id_product'),
						'id_category_default' => (int)$this->_category->id,
						'images' => $images,
						'token' =>  $this->token,
						'table' => $this->table,
						'max_image_size' => $this->max_image_size / 1024 / 1024,
						'up_filename' => (string)Tools::getValue('virtual_product_filename_attribute'),
						'currency' => $this->context->currency,
						'current_shop_id' => $current_shop_id
				));
			}
			else
				$this->displayWarning($this->l('You must save the product in this shop before adding images.'));	
		}
		else
			$this->displayWarning($this->l('You must save this product before adding images.'));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormCombinations($obj)
	{
		return $this->initFormAttributes($obj);
	}

	public function initFormAttributes($product)
	{
		$data = $this->createTemplate($this->tpl_form);
		if (!Combination::isFeatureActive())
			$this->displayWarning($this->l('This feature has been disabled. ').
				' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
		else if (Validate::isLoadedObject($product))
		{
			if ($this->product_exists_in_shop)
			{
				if ($product->is_virtual)
				{
					$data->assign('product', $product);
					$this->displayWarning($this->l('A virtual product cannot have combinations.'));
				}
				else
				{
					$attribute_js = array();
					$attributes = Attribute::getAttributes($this->context->language->id, true);
					foreach ($attributes as $k => $attribute)
						$attribute_js[$attribute['id_attribute_group']][$attribute['id_attribute']] = $attribute['name'];
					$currency = $this->context->currency;
					$data->assign('attributeJs', $attribute_js);
					$data->assign('attributes_groups', AttributeGroup::getAttributesGroups($this->context->language->id));

					$data->assign('currency', $currency);

					$images = Image::getImages($this->context->language->id, $product->id);

					$data->assign('tax_exclude_option', Tax::excludeTaxeOption());
					$data->assign('ps_weight_unit', Configuration::get('PS_WEIGHT_UNIT'));

					$data->assign('ps_use_ecotax', Configuration::get('PS_USE_ECOTAX'));
					$data->assign('field_value_unity', $this->getFieldValue($product, 'unity'));

					$data->assign('reasons', $reasons = StockMvtReason::getStockMvtReasons($this->context->language->id));
					$data->assign('ps_stock_mvt_reason_default', $ps_stock_mvt_reason_default = Configuration::get('PS_STOCK_MVT_REASON_DEFAULT'));
					$data->assign('minimal_quantity', $this->getFieldValue($product, 'minimal_quantity') ? $this->getFieldValue($product, 'minimal_quantity') : 1);
					$data->assign('available_date', ($this->getFieldValue($product, 'available_date') != 0) ? stripslashes(htmlentities($this->getFieldValue($product, 'available_date'), $this->context->language->id)) : '0000-00-00');

					$i = 0;
					$data->assign('imageType', ImageType::getByNameNType('small_default', 'products'));
					$data->assign('imageWidth', (isset($image_type['width']) ? (int)($image_type['width']) : 64) + 25);
					foreach ($images as $k => $image)
					{
						$images[$k]['obj'] = new Image($image['id_image']);
						++$i;
					}
					$data->assign('images', $images);

					$data->assign($this->tpl_form_vars);
					$data->assign(array(
						'list' => $this->renderListAttributes($product, $currency),
						'product' => $product,
						'id_category' => $product->getDefaultCategory(),
						'token_generator' => Tools::getAdminTokenLite('AdminAttributeGenerator'),
						'combination_exists' => (Shop::isFeatureActive() && (Shop::getContextShopGroup()->share_stock) && count(AttributeGroup::getAttributesGroups($this->context->language->id)) > 0 && $product->hasAttributes())
					));
				}
			}
			else
				$this->displayWarning($this->l('You must save the product in this shop before adding combinations.'));	
		}
		else
		{
			$data->assign('product', $product);
			$this->displayWarning($this->l('You must save this product before adding combinations.'));
		}

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function renderListAttributes($product, $currency)
	{
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
		$this->addRowAction('edit');
		$this->addRowAction('default');
		$this->addRowAction('delete');

		$color_by_default = '#BDE5F8';

		$this->fields_list = array(
			'attributes' => array('title' => $this->l('Attributes'), 'align' => 'left'),
			'price' => array('title' => $this->l('Impact'), 'type' => 'price', 'align' => 'left', 'width' => 70),
			'weight' => array('title' => $this->l('Weight'), 'align' => 'left', 'width' => 70),
			'reference' => array('title' => $this->l('Reference'), 'align' => 'left', 'width' => 70),
			'ean13' => array('title' => $this->l('EAN13'), 'align' => 'left', 'width' => 70),
			'upc' => array('title' => $this->l('UPC'), 'align' => 'left', 'width' => 70)
		);

		if ($product->id)
		{
			/* Build attributes combinations */
			$combinations = $product->getAttributeCombinations($this->context->language->id);
			$groups = array();
			$comb_array = array();
			if (is_array($combinations))
			{
				$combination_images = $product->getCombinationImages($this->context->language->id);
				foreach ($combinations as $k => $combination)
				{
					$price_to_convert = Tools::convertPrice($combination['price'], $currency);
					$price = Tools::displayPrice($price_to_convert, $currency);

					$comb_array[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
					$comb_array[$combination['id_product_attribute']]['attributes'][] = array($combination['group_name'], $combination['attribute_name'], $combination['id_attribute']);
					$comb_array[$combination['id_product_attribute']]['wholesale_price'] = $combination['wholesale_price'];
					$comb_array[$combination['id_product_attribute']]['price'] = $price;
					$comb_array[$combination['id_product_attribute']]['weight'] = $combination['weight'].Configuration::get('PS_WEIGHT_UNIT');
					$comb_array[$combination['id_product_attribute']]['unit_impact'] = $combination['unit_price_impact'];
					$comb_array[$combination['id_product_attribute']]['reference'] = $combination['reference'];
					$comb_array[$combination['id_product_attribute']]['ean13'] = $combination['ean13'];
					$comb_array[$combination['id_product_attribute']]['upc'] = $combination['upc'];
					$comb_array[$combination['id_product_attribute']]['id_image'] = isset($combination_images[$combination['id_product_attribute']][0]['id_image']) ? $combination_images[$combination['id_product_attribute']][0]['id_image'] : 0;
					$comb_array[$combination['id_product_attribute']]['available_date'] = strftime($combination['available_date']);
					$comb_array[$combination['id_product_attribute']]['default_on'] = $combination['default_on'];
					if ($combination['is_color_group'])
						$groups[$combination['id_attribute_group']] = $combination['group_name'];
				}
			}

			$irow = 0;
			if (isset($comb_array))
			{
				foreach ($comb_array as $id_product_attribute => $product_attribute)
				{
					$list = '';

					/* In order to keep the same attributes order */
					asort($product_attribute['attributes']);

					foreach ($product_attribute['attributes'] as $attribute)
						$list .= $attribute[0].' - '.$attribute[1].', ';

					$list = rtrim($list, ', ');
					$comb_array[$id_product_attribute]['image'] = $product_attribute['id_image'] ? new Image($product_attribute['id_image']) : false;
					$comb_array[$id_product_attribute]['available_date'] = $product_attribute['available_date'] != 0 ? date('Y-m-d', strtotime($product_attribute['available_date'])) : '0000-00-00';
					$comb_array[$id_product_attribute]['attributes'] = $list;

					if ($product_attribute['default_on'])
					{
						$comb_array[$id_product_attribute]['name'] = 'is_default';
						$comb_array[$id_product_attribute]['color'] = $color_by_default;
					}
				}
			}
		}

		foreach ($this->actions_available as $action)
		{
			if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action)
				$this->actions[] = $action;
		}

		$helper = new HelperList();
		$helper->identifier = 'id_product_attribute';
		$helper->token = $this->token;
		$helper->currentIndex = self::$currentIndex;
		$helper->no_link = true;
		$helper->simple_header = true;
		$helper->show_toolbar = false;
		$helper->shopLinkType = $this->shopLinkType;
		$helper->actions = $this->actions;
		$helper->list_skip_actions = $this->list_skip_actions;
		$helper->colorOnBackground = true;
		$helper->override_folder = $this->tpl_folder.'combination/';

		return $helper->generateList($comb_array, $this->fields_list);
	}

	public function initFormQuantities($obj)
	{
		$data = $this->createTemplate($this->tpl_form);

		if ($obj->id)
		{
			if ($this->product_exists_in_shop)
			{
				// Get all id_product_attribute
				$attributes = $obj->getAttributesResume($this->context->language->id);
				if (empty($attributes))
					$attributes[] = array(
						'id_product_attribute' => 0,
						'attribute_designation' => ''
					);

				// Get available quantities
				$available_quantity = array();
				$product_designation = array();

				foreach ($attributes as $attribute)
				{
					// Get available quantity for the current product attribute in the current shop
					$available_quantity[$attribute['id_product_attribute']] = StockAvailable::getQuantityAvailableByProduct((int)$obj->id,
																															$attribute['id_product_attribute']);
					// Get all product designation
					$product_designation[$attribute['id_product_attribute']] = rtrim(
						$obj->name[$this->context->language->id].' - '.$attribute['attribute_designation'],
						' - '
					);
				}

				$show_quantities = true;
				$shop_context = Shop::getContext();
				$shop_group = new ShopGroup((int)Shop::getContextShopGroupID());

				// if we are in all shops context, it's not possible to manage quantities at this level
				if (Shop::isFeatureActive() && $shop_context == Shop::CONTEXT_ALL)
					$show_quantities = false;
				// if we are in group shop context
				elseif (Shop::isFeatureActive() && $shop_context == Shop::CONTEXT_GROUP)
				{
					// if quantities are not shared between shops of the group, it's not possible to manage them at group level
					if (!$shop_group->share_stock)
						$show_quantities = false;
				}
				// if we are in shop context
				else
				{
					// if quantities are shared between shops of the group, it's not possible to manage them for a given shop
					if ($shop_group->share_stock)
						$show_quantities = false;
				}

				$data->assign('ps_stock_management', Configuration::get('PS_STOCK_MANAGEMENT'));
				$data->assign('has_attribute', $obj->hasAttributes());
				// Check if product has combination, to display the available date only for the product or for each combination
				if (Combination::isFeatureActive())
					$data->assign('countAttributes', (int)Db::getInstance()->getValue('SELECT COUNT(id_product) FROM '._DB_PREFIX_.'product_attribute WHERE id_product = '.(int)$obj->id));
				else
					$data->assign('countAttributes', false);
				// if advanced stock management is active, checks associations
				$advanced_stock_management_warning = false;
				if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $obj->advanced_stock_management)
				{
					$p_attributes = Product::getProductAttributesIds($obj->id);
					$warehouses = array();

					if (!$p_attributes)
						$warehouses[] = Warehouse::getProductWarehouseList($obj->id, 0);

					foreach ($p_attributes as $p_attribute)
					{
						$ws = Warehouse::getProductWarehouseList($obj->id, $p_attribute['id_product_attribute']);
						if ($ws)
							$warehouses[] = $ws;
					}
					$warehouses = Tools::arrayUnique($warehouses);

					if (empty($warehouses))
						$advanced_stock_management_warning = true;
				}
				if ($advanced_stock_management_warning)
				{
					$this->displayWarning($this->l('If you wish to use the advanced stock management, you must:'));
					$this->displayWarning('- '.$this->l('associate your products with warehouses.'));
					$this->displayWarning('- '.$this->l('associate your warehouses with carriers.'));
					$this->displayWarning('- '.$this->l('associate your warehouses with the appropriate shops.'));
				}

				$pack_quantity = null;
				// if product is a pack
				if (Pack::isPack($obj->id))
				{
					$items = Pack::getItems((int)$obj->id, Configuration::get('PS_LANG_DEFAULT'));

					// gets an array of quantities (quantity for the product / quantity in pack)
					$pack_quantities = array();
					foreach ($items as $item)
					{
						if (!$item->isAvailableWhenOutOfStock((int)$item->out_of_stock))
						{
							$pack_id_product_attribute = Product::getDefaultAttribute($item->id, 1);
							$pack_quantities[] = Product::getQuantity($item->id, $pack_id_product_attribute) / ($item->pack_quantity !== 0 ? $item->pack_quantity : 1);
						}
					}

					// gets the minimum
					if (count($pack_quantities))
					{	
						$pack_quantity = $pack_quantities[0];
						foreach ($pack_quantities as $value)
						{
							if ($pack_quantity > $value)
								$pack_quantity = $value;
						}
					}

					if (!Warehouse::getPackWarehouses((int)$obj->id))
						$this->displayWarning($this->l('You must have a common warehouse between this pack and its product.'));
				}

				$data->assign(array(
					'attributes' => $attributes,
					'available_quantity' => $available_quantity,
					'pack_quantity' => $pack_quantity,
					'stock_management_active' => Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
					'product_designation' => $product_designation,
					'product' => $obj,
					'show_quantities' => $show_quantities,
					'order_out_of_stock' => Configuration::get('PS_ORDER_OUT_OF_STOCK'),
					'token_preferences' => Tools::getAdminTokenLite('AdminPPreferences'),
					'token' => $this->token,
					'languages' => $this->_languages,
				));
			}
			else
				$this->displayWarning($this->l('You must save the product in this shop before managing quantities.'));
		}
		else
			$this->displayWarning($this->l('You must save this product before managing quantities.'));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormSuppliers($obj)
	{
		$data = $this->createTemplate($this->tpl_form);

		if ($obj->id)
		{
			if ($this->product_exists_in_shop)
			{
				// Get all id_product_attribute
				$attributes = $obj->getAttributesResume($this->context->language->id);
				if (empty($attributes))
					$attributes[] = array(
						'id_product' => $obj->id,
						'id_product_attribute' => 0,
						'attribute_designation' => ''
					);

				$product_designation = array();

				foreach ($attributes as $attribute)
					$product_designation[$attribute['id_product_attribute']] = rtrim(
						$obj->name[$this->context->language->id].' - '.$attribute['attribute_designation'],
						' - '
					);

				// Get all available suppliers
				$suppliers = Supplier::getSuppliers();

				// Get already associated suppliers
				$associated_suppliers = ProductSupplier::getSupplierCollection($obj->id);

				// Get already associated suppliers and force to retreive product declinaisons
				$product_supplier_collection = ProductSupplier::getSupplierCollection($obj->id, false);

				$default_supplier = 0;

				foreach ($suppliers as &$supplier)
				{
					$supplier['is_selected'] = false;
					$supplier['is_default'] = false;

					foreach ($associated_suppliers as $associated_supplier)
						if ($associated_supplier->id_supplier == $supplier['id_supplier'])
						{
							$associated_supplier->name = $supplier['name'];
							$supplier['is_selected'] = true;

							if ($obj->id_supplier == $supplier['id_supplier'])
							{
								$supplier['is_default'] = true;
								$default_supplier = $supplier['id_supplier'];
							}
						}
				}

				$data->assign(array(
					'attributes' => $attributes,
					'suppliers' => $suppliers,
					'default_supplier' => $default_supplier,
					'associated_suppliers' => $associated_suppliers,
					'associated_suppliers_collection' => $product_supplier_collection,
					'product_designation' => $product_designation,
					'currencies' => Currency::getCurrencies(),
					'product' => $obj,
					'link' => $this->context->link,
					'token' => $this->token,
					'id_default_currency' => Configuration::get('PS_CURRENCY_DEFAULT'),
				));
			}
			else
				$this->displayWarning($this->l('You must save the product in this shop before managing suppliers.'));
		}
		else
			$this->displayWarning($this->l('You must save this product before managing suppliers.'));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormWarehouses($obj)
	{
		$data = $this->createTemplate($this->tpl_form);

		if ($obj->id)
		{
			if ($this->product_exists_in_shop)
			{
				// Get all id_product_attribute
				$attributes = $obj->getAttributesResume($this->context->language->id);
				if (empty($attributes))
					$attributes[] = array(
						'id_product' => $obj->id,
						'id_product_attribute' => 0,
						'attribute_designation' => ''
					);

				$product_designation = array();

				foreach ($attributes as $attribute)
					$product_designation[$attribute['id_product_attribute']] = rtrim(
						$obj->name[$this->context->language->id].' - '.$attribute['attribute_designation'],
						' - '
					);

				// Get all available warehouses
				$warehouses = Warehouse::getWarehouses(true);

				// Get already associated warehouses
				$associated_warehouses_collection = WarehouseProductLocation::getCollection($obj->id);

				$data->assign(array(
					'attributes' => $attributes,
					'warehouses' => $warehouses,
					'associated_warehouses' => $associated_warehouses_collection,
					'product_designation' => $product_designation,
					'product' => $obj,
					'link' => $this->context->link,
					'token' => $this->token
				));
			}
			else
				$this->displayWarning($this->l('You must save the product in this shop before managing warehouses.'));
		}
		else
			$this->displayWarning($this->l('You must save this product before managing warehouses.'));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormFeatures($obj)
	{
		$data = $this->createTemplate($this->tpl_form);
		if (!Feature::isFeatureActive())
			$this->displayWarning($this->l('This feature has been disabled. ').' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
		else
		{
			if ($obj->id)
			{
				if ($this->product_exists_in_shop)
				{
					$features = Feature::getFeatures($this->context->language->id, (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP));

					foreach ($features as $k => $tab_features)
					{
						$features[$k]['current_item'] = false;
						$features[$k]['val'] = array();

						$custom = true;
						foreach ($obj->getFeatures() as $tab_products)
							if ($tab_products['id_feature'] == $tab_features['id_feature'])
								$features[$k]['current_item'] = $tab_products['id_feature_value'];

						$features[$k]['featureValues'] = FeatureValue::getFeatureValuesWithLang($this->context->language->id, (int)$tab_features['id_feature']);
						if (count($features[$k]['featureValues']))
							foreach ($features[$k]['featureValues'] as $value)
								if ($features[$k]['current_item'] == $value['id_feature_value'])
									$custom = false;

						if ($custom)
							$features[$k]['val'] = FeatureValue::getFeatureValueLang($features[$k]['current_item']);
					}

					$data->assign('available_features', $features);

					$data->assign('product', $obj);
					$data->assign('link', $this->context->link);
					$data->assign('languages', $this->_languages);
					$data->assign('default_form_language', $this->default_form_language);
				}
				else
					$this->displayWarning($this->l('You must save the product in this shop before adding features.'));
			}
			else
				$this->displayWarning($this->l('You must save this product before adding features.'));
		}
		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function ajaxProcessProductQuantity()
	{
		if (!Tools::getValue('actionQty'))
			return Tools::jsonEncode(array('error' => $this->l('Undefined action')));

		$product = new Product((int)Tools::getValue('id_product'), true);
		switch (Tools::getValue('actionQty'))
		{
			case 'depends_on_stock':
				if (Tools::getValue('value') === false)
					die (Tools::jsonEncode(array('error' =>  $this->l('Undefined value'))));
				if ((int)Tools::getValue('value') != 0 && (int)Tools::getValue('value') != 1)
					die (Tools::jsonEncode(array('error' =>  $this->l('Uncorrect value'))));
				if (!$product->advanced_stock_management && (int)Tools::getValue('value') == 1)
					die (Tools::jsonEncode(array('error' =>  $this->l('Not possible if advanced stock management is disabled. '))));
				if ($product->advanced_stock_management && Pack::isPack($product->id))
					die (Tools::jsonEncode(array('error' =>  $this->l('Not possible if the product is a pack.'))));

				StockAvailable::setProductDependsOnStock($product->id, (int)Tools::getValue('value'));
				break;

			case 'out_of_stock':
				if (Tools::getValue('value') === false)
					die (Tools::jsonEncode(array('error' =>  $this->l('Undefined value'))));
				if (!in_array((int)Tools::getValue('value'), array(0, 1, 2)))
					die (Tools::jsonEncode(array('error' =>  $this->l('Uncorrect value'))));

				StockAvailable::setProductOutOfStock($product->id, (int)Tools::getValue('value'));
				break;

			case 'set_qty':
				if (Tools::getValue('value') === false)
					die (Tools::jsonEncode(array('error' =>  $this->l('Undefined value'))));
				if (Tools::getValue('id_product_attribute') === false)
					die (Tools::jsonEncode(array('error' =>  $this->l('Undefined id product attribute'))));

				StockAvailable::setQuantity($product->id, (int)Tools::getValue('id_product_attribute'), (int)Tools::getValue('value'));
				break;
			case 'advanced_stock_management' :
				if (Tools::getValue('value') === false)
					die (Tools::jsonEncode(array('error' =>  $this->l('Undefined value'))));
				if ((int)Tools::getValue('value') != 1 && (int)Tools::getValue('value') != 0)
					die (Tools::jsonEncode(array('error' =>  $this->l('Uncorrect value'))));
				if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)Tools::getValue('value') == 1)
					die (Tools::jsonEncode(array('error' =>  $this->l('Not possible if advanced stock management is disabled. '))));
				if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && Pack::isPack($product->id))
					die (Tools::jsonEncode(array('error' =>  $this->l('Not possible if the product is a pack.'))));

				$product->setAdvancedStockManagement((int)Tools::getValue('value'));
				if (StockAvailable::dependsOnStock($product->id) == 1 && (int)Tools::getValue('value') == 0)
					StockAvailable::setProductDependsOnStock($product->id, 0);
				break;

		}
		die(Tools::jsonEncode(array('error' => false)));
	}

	public function getCombinationImagesJS()
	{
		if (!($obj = $this->loadObject(true)))
			return;

		$content = 'var combination_images = new Array();';
		if (!$allCombinationImages = $obj->getCombinationImages($this->context->language->id))
			return $content;
		foreach ($allCombinationImages as $id_product_attribute => $combination_images)
		{
			$i = 0;
			$content .= 'combination_images['.(int)$id_product_attribute.'] = new Array();';
			foreach ($combination_images as $combination_image)
				$content .= 'combination_images['.(int)$id_product_attribute.']['.$i++.'] = '.(int)$combination_image['id_image'].';';
		}
		return $content;
	}

	public function haveThisAccessory($accessory_id, $accessories)
	{
		foreach ($accessories as $accessory)
			if ((int)$accessory['id_product'] == (int)$accessory_id)
				return true;
		return false;
	}

	protected function initPack(Product $product)
	{
		$this->tpl_form_vars['is_pack'] = ($product->id && Pack::isPack($product->id)) || Tools::getValue('ppack') || Tools::getValue('type_product') == Product::PTYPE_PACK;
		$product->packItems = Pack::getItems($product->id, $this->context->language->id);

		$input_pack_items = '';
		if (Tools::getValue('inputPackItems'))
			$input_pack_items = Tools::getValue('inputPackItems');
		else
			foreach ($product->packItems as $pack_item)
				$input_pack_items .= $pack_item->pack_quantity.'x'.$pack_item->id.'-';
		$this->tpl_form_vars['input_pack_items'] = $input_pack_items;

		$input_namepack_items = '';
		if (Tools::getValue('namePackItems'))
			$input_namepack_items = Tools::getValue('namePackItems');
		else
			foreach ($product->packItems as $pack_item)
				$input_namepack_items .= $pack_item->pack_quantity.' x '.$pack_item->name.'¤';
		$this->tpl_form_vars['input_namepack_items'] = $input_namepack_items;
	}


	/**
	 *  AdminProducts display hook
	 */
	public function initFormModules($obj)
	{
 		$id_module = Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name` = \''.pSQL($this->tab_display_module).'\'');
		$this->tpl_form_vars['custom_form'] = Hook::exec('displayAdminProductsExtra', array(), (int)$id_module);
	}

	/**
	 * delete all items in pack, then check if type_product value is 2.
	 * if yes, add the pack items from input "inputPackItems"
	 *
	 * @param Product $product
	 * @return boolean
	 */
	public function updatePackItems($product)
	{
		Pack::deleteItems($product->id);
		// lines format: QTY x ID-QTY x ID
		if (Tools::getValue('type_product') == Product::PTYPE_PACK)
		{
			$product->setDefaultAttribute(0);//reset cache_default_attribute		
			$items = Tools::getValue('inputPackItems');
			$lines = array_unique(explode('-', $items));
			// lines is an array of string with format : QTYxID
			if (count($lines))
				foreach ($lines as $line)
					if (!empty($line))
					{
						list($qty, $item_id) = explode('x', $line);
						if ($qty > 0 && isset($item_id))
						{
							if (Pack::isPack((int)$item_id))
								$this->errors[] = Tools::displayError('You can\'t add product packs into a pack');
							elseif (!Pack::addItem((int)$product->id, (int)$item_id, (int)$qty))
								$this->errors[] = Tools::displayError('An error occurred while attempting to add products to the pack.');
						}
					}
		}
	}

	public function getL($key)
	{
		$trad = array(
			'Default category:' => $this->l('Default category:'),
			'Catalog:' => $this->l('Catalog:'),
			'Consider changing the default category.' => $this->l('Consider changing the default category.'),
			'ID' => $this->l('ID'),
			'Name' => $this->l('Name'),
			'Mark all checkbox(es) of categories in which product is to appear' => $this->l('Mark the checkbox of each categories in which this product will appear.')
		);
		return $trad[$key];
	}

	protected function _displayUnavailableProductWarning()
	{
		$content = '<div class="alert">
				<p>
				<span>
				'.$this->l('Your product will be saved as a draft.').'
				</span>
				<span style="float:right"><a href="#" class="button" style="display: block" onclick="submitAddProductAndPreview()" >'.$this->l('Save and preview.').'</a></span>
				<input type="hidden" name="fakeSubmitAddProductAndPreview" id="fakeSubmitAddProductAndPreview" />
				</p>
			</div>';
			$this->tpl_form_vars['warning_unavailable_product'] = $content;
	}

	public function ajaxProcessCheckProductName()
	{
		if ($this->tabAccess['view'] === '1')
		{
			$search = Tools::getValue('q');
			$id_lang = Tools::getValue('id_lang');
			$limit = Tools::getValue('limit');
			if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP)
				$result = false;
			else
				$result = Db::getInstance()->executeS('
					SELECT DISTINCT pl.`name`, p.`id_product`, pl.`id_shop`
					FROM `'._DB_PREFIX_.'product` p
					LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop ='.(int)Context::getContext()->shop->id.')
					LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
						ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int)$id_lang.')
					WHERE pl.`name` LIKE "%'.pSQL($search).'%" AND ps.id_product IS NULL
					GROUP BY pl.`id_product`
					LIMIT '.(int)$limit);
			die(Tools::jsonEncode($result));
		}
	}

	public function ajaxProcessUpdatePositions()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			$way = (int)(Tools::getValue('way'));
			$id_product = (int)(Tools::getValue('id_product'));
			$id_category = (int)(Tools::getValue('id_category'));
			$positions = Tools::getValue('product');

			if (is_array($positions))
				foreach ($positions as $position => $value)
				{
					$pos = explode('_', $value);

					if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_category && (int)$pos[2] === $id_product))
					{
						if ($product = new Product((int)$pos[2]))
							if (isset($position) && $product->updatePosition($way, $position))
							{
								$category = new Category((int)$id_category);
								if (Validate::isLoadedObject($category))
									hook::Exec('categoryUpdate', array('category' => $category));
								echo 'ok position '.(int)$position.' for product '.(int)$pos[2]."\r\n";							
							}
							else
								echo '{"hasError" : true, "errors" : "Can not update product '.(int)$id_product.' to position '.(int)$position.' "}';
						else
							echo '{"hasError" : true, "errors" : "This product ('.(int)$id_product.') can t be loaded"}';

						break;
					}
				}
		}
	}

	public function ajaxProcessPublishProduct()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			if ($id_product = (int)Tools::getValue('id_product'))
			{
				$id_tab_catalog = (int)(Tab::getIdFromClassName('AdminProducts'));
				$bo_product_url = dirname($_SERVER['PHP_SELF']).'/index.php?tab=AdminProducts&id_product='.$id_product.'&updateproduct&token='.$this->token;

				if (Tools::getValue('redirect'))
					die($bo_product_url);

					$product = new Product((int)$id_product);
					if (!Validate::isLoadedObject($product))
						die('error: invalid id');

					$product->active = 1;

					if ($product->save())
						die($bo_product_url);
					else
						die('error: saving');
			}
		}
	}
}
