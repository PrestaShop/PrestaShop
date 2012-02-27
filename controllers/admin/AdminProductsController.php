<?php
/*
* 2007-2012 PrestaShop
* NOTICE OF LICENSE
*
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7331 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminProductsControllerCore extends AdminController
{
	/** @var integer Max image size for upload
	 * As of 1.5 it is recommended to not set a limit to max image size
	 **/
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
	protected $available_tabs = array(
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
		'Quantities' => 6,
		'Suppliers' => 13,
		'Warehouses' => 14,
		'Accounting' => 15,
	);

	protected $default_tab = 'Informations';

	protected $available_tabs_lang = array();

	public function __construct()
	{
		$this->table = 'product';
		$this->className = 'Product';
		$this->lang = true;
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->imageType = 'jpg';
		$this->context = Context::getContext();
		$this->_defaultOrderBy = 'position';
		$this->max_file_size = (int)(Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1000000);
		$this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');

		$this->fieldsDisplay = array(
			'id_product' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 20
			),
			'image' => array(
				'title' => $this->l('Photo'),
				'align' => 'center',
				'image' => 'p',
				'width' => 70,
				'orderby' => false,
				'filter' => false,
				'search' => false
			),
			'name' => array(
				'title' => $this->l('Name'),
				'filter_key' => 'b!name'
			),
			'reference' => array(
				'title' => $this->l('Reference'),
				'align' => 'left',
				'width' => 80
			),
			'name_category' => array(
				'title' => $this->l('Category'),
				'width' => 230,
				'filter_key' => 'cl!name',
			),
			'price' => array(
				'title' => $this->l('Base price'),
				'width' => 90,
				'type' => 'price',
				'align' => 'right',
				'filter_key' => 'a!price'
			),
			'price_final' => array(
				'title' => $this->l('Final price'),
				'width' => 90,
				'type' => 'price',
				'align' => 'right',
				'havingFilter' => true,
				'orderby' => false
			),
			'active' => array(
				'title' => $this->l('Displayed'),
				'width' => 70,
				'active' => 'status',
				'filter_key' => 'a!active',
				'align' => 'center',
				'type' => 'bool',
				'orderby' => false
			),
			'position' => array(
				'title' => $this->l('Position'),
				'width' => 70,
				'filter_key' => 'cp!position',
				'align' => 'center',
				'position' => 'position'
			)
		);

		// @since 1.5 : translations for tabs
		$this->available_tabs_lang = array (
			'Informations' => $this->l('Informations'),
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
			'Accounting' => $this->l('Accounting')
		);


		/* Adding tab if modules are hooked */
		$modules_list = Hook::getHookModuleExecList('displayAdminProductsExtra');
		if (is_array($modules_list) && count($modules_list) > 0)
			foreach ($modules_list as $m)
			{
				$this->available_tabs['Module'.ucfirst($m['module'])] = 23;
				$this->available_tabs_lang['Module'.ucfirst($m['module'])] = Module::getModuleName($m['module']);
			}


		/* Join categories table */
		if ($id_category = (int)Tools::getValue('productFilter_cl!name'))
		{
			$this->_category = new Category((int)$id_category);
			$_POST['productFilter_cl!name'] = $this->_category->name[$this->context->language->id];
		}
		elseif ($id_category = Tools::getvalue('id_category'))
			$this->_category = new Category((int)$id_category);
		else
			$this->_category = new Category();


		$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (a.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang`)
			LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = a.`id_product` AND i.`cover` = 1)
			LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = a.`id_product`)
			LEFT JOIN `'._DB_PREFIX_.'product_tax_rules_group_shop` ptrgs ON (a.`id_product` = ptrgs.`id_product` 
				AND ptrgs.id_shop='.(int)$this->context->shop->id.')
			LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ptrgs.`id_tax_rules_group` = tr.`id_tax_rules_group`
				AND tr.`id_country` = '.(int)$this->context->country->id.' AND tr.`id_state` = 0)
			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)';

		// if no category selected, display all products
		if (Validate::isLoadedObject($this->_category) && empty($this->_filter))
			$this->_filter = 'AND cp.`id_category` = '.(int)$this->_category->id;

		$this->_select = 'cl.name `name_category`, cp.`position`, i.`id_image`, (a.`price` * ((100 + (t.`rate`))/100)) AS price_final';

		parent::__construct();
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
		if (Tools::getIsset('unit_price') != null)
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
		parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);

		/* update product quantity with attributes ...*/
		$nb = count($this->_list);
		if ($this->_list)
		{
			/* update product final price */
			for ($i = 0; $i < $nb; $i++)
				$this->_list[$i]['price_tmp'] = Product::getPriceStatic($this->_list[$i]['id_product'], true, null, 6, null, false, true, 1, true);
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

	public function processDeleteVirtualProduct($token)
	{
		if (!($id_product_download = ProductDownload::getIdFromIdAttribute((int)Tools::getValue('id_product'), 0)))
			return false;
		$product_download = new ProductDownload((int)$id_product_download);
		return $product_download->deleteFile((int)$id_product_download);
	}

	public function processDeleteVirtualProductAttribute($token)
	{
		if (!($id_product_download = ProductDownload::getIdFromIdAttribute((int)Tools::getValue('id_product'), (int)Tools::getValue('id_product_attribute'))))
			return false;
		$product_download = new ProductDownload((int)$id_product_download);
		return $product_download->deleteFile((int)$id_product_download);
	}

	/**
	 * Upload new attachment
	 *
	 * @param $token
	 * @return void
	 */
	public function processAddAttachments($token)
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
				$this->errors[] = Tools::displayError('Name is too long.').' '.'(32 '.Tools::displayError('chars max').')';
			if (!Validate::isCleanHtml(Tools::getValue('attachment_description_'.(int)($language['id_lang']))))
				$this->errors[] = Tools::displayError('Invalid description');
		}
		if (!$is_attachment_name_valid)
			$this->errors[] = Tools::displayError('Attachment Name Required');

		if (empty($this->errors))
		{
			if (isset($_FILES['attachment_file']) && is_uploaded_file($_FILES['attachment_file']['tmp_name']))
			{
				if ($_FILES['attachment_file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024))
					$this->errors[] = $this->l('File too large, maximum size allowed:').' '.(Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024).' '.$this->l('kb').'. '.$this->l('File size you\'re trying to upload is:').number_format(($_FILES['attachment_file']['size'] / 1024), 2, '.', '').$this->l('kb');
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
				$this->errors[] = $this->l('the File').' <b>'.$_FILES['attachment_file']['name'].'</b> '.$this->l('exceeds the size allowed by the server, this limit is set to').' <b>'.$upload_mb.$this->l('Mb').'</b>';
			}
			else
				$this->errors[] = Tools::displayError('File is missing');

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
					$this->errors[] = Tools::displayError('File name too long');
				if (empty($this->errors))
				{
					$res = $attachment->add();
					if (!$res)
						$this->errors[] = Tools::displayError('Unable to add this attachment in the database');
					else
					{
						$id_product = (int)Tools::getValue($this->identifier);
						$res = $attachment->attachProduct($id_product);
						if (!$res)
							$this->errors[] = Tools::displayErrors('Unable to associate this attachment to product');
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
	 * @param $token
	 * @return void
	 */
	public function processAttachments($token)
	{
		if ($this->action == 'attachments')
			if ($id = (int)Tools::getValue($this->identifier))
			{
				$attachments = trim(Tools::getValue('arrayAttachments'), ',');
				$attachments = explode(',', $attachments);
				if (Attachment::attachToProduct($id, $attachments))
					$this->redirect_after = self::$currentIndex.'&id_product='.(int)$id.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&conf=4&add'.$this->table.'&action=Attachments&token='.($token ? $token : $this->token);
			}
	}

	public function processDuplicate($token)
	{
		if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
		{
			$id_product_old = $product->id;
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
			&& Product::duplicateDownload($id_product_old, $product->id)
			&& Product::duplicateTaxRulesGroup((int)$id_product_old, (int)$product->id)
			&& $product->duplicateShops($id_product_old))
			{
				if ($product->hasAttributes())
					Product::updateDefaultAttribute($product->id);

				if (!Tools::getValue('noimage') && !Image::duplicateProductImages($id_product_old, $product->id, $combination_images))
					$this->errors[] = Tools::displayError('An error occurred while copying images.');
				else
				{
					Hook::exec('actionProductAdd', array('product' => $product));
					Search::indexation(false, $product->id);
					$this->redirect_after = self::$currentIndex.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&conf=19&token='.($token ? $token : $this->token);
				}
			}
			else
				$this->errors[] = Tools::displayError('An error occurred while creating object.');
		}
	}

	public function processDelete($token)
	{
		if (Validate::isLoadedObject($object = $this->loadObject()) && isset($this->fieldImageSettings))
		{
			// check if request at least one object with noZeroObject
			if (isset($object->noZeroObject) && count($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1)
				$this->errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
			else
			{
				$id_category = (int)Tools::getValue('id_category');
				$category_url = empty($id_category) ? '' : '&id_category='.(int)$id_category;

				if ($this->deleted)
				{
					$object->deleteImages();
					$object->deleted = 1;
					if ($object->update())
						$this->redirect_after = self::$currentIndex.'&conf=1&token='.($token ? $token : $this->token).$category_url;
				}
				elseif ($object->delete())
					$this->redirect_after = self::$currentIndex.'&conf=1&token='.($token ? $token : $this->token).$category_url;
				$this->errors[] = Tools::displayError('An error occurred during deletion.');
			}
		}
		else
			$this->errors[] = Tools::displayError('An error occurred while deleting object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
	}

	public function processImage($token)
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
					$this->errors[] = Tools::displayError('Cannot change the product cover');
				else
				{
					$productId = (int)Tools::getValue('id_product');
					@unlink(_PS_TMP_IMG_DIR_.'product_'.$productId.'.jpg');
					@unlink(_PS_TMP_IMG_DIR_.'product_mini_'.$productId.'.jpg');
					$this->redirect_after = self::$currentIndex.'&id_product='.$image->id_product.'&id_category='.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&action=Images&addproduct'.'&token='.($token ? $token : $this->token);
				}
			}

			/* Choose product image position */
			elseif (Tools::getIsset('imgPosition') && Tools::getIsset('imgDirection'))
			{
				$image->updatePosition(Tools::getValue('imgDirection'), Tools::getValue('imgPosition'));
				$this->redirect_after = self::$currentIndex.'&id_product='.$image->id_product.'&id_category='.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&add'.$this->table.'&action=Images&token='.($token ? $token : $this->token);
			}
		}
		else
			$this->errors[] = Tools::displayError('Could not find image.');
	}

	protected function processBulkDelete($token)
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
				$result = true;
				if ($this->deleted)
				{
					foreach (Tools::getValue($this->table.'Box') as $id)
					{
						$toDelete = new $this->className((int)$id);
						$toDelete->deleted = 1;
						$result = $result && $toDelete->update();
					}
				}
				else
					$result = $object->deleteSelection(Tools::getValue($this->table.'Box'));

				if ($result)
				{
					$id_category = (int)Tools::getValue('id_category');
					$category_url = empty($id_category) ? '' : '&id_category='.(int)$id_category;

					$this->redirect_after = self::$currentIndex.'&conf=2&token='.$token.$category_url;
				}
				$this->errors[] = Tools::displayError('An error occurred while deleting selection.');
			}
		}
		else
			$this->errors[] = Tools::displayError('You must select at least one element to delete.');
	}

	public function processProductAttribute($token)
	{
		// Don't process if the combination fields have not been submitted
		if (!Combination::isFeatureActive() || !Tools::getIsset('attribute'))
			return;

		$is_virtual = (int)Tools::getValue('is_virtual');

		if (Validate::isLoadedObject($product = $this->object))
		{
			if (!Tools::getIsset('attribute_price') || Tools::getIsset('attribute_price') == null)
				$this->errors[] = Tools::displayError('Attribute price required.');
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
				if ($id_product_attribute = (int)Tools::getValue('id_product_attribute'))
				{
					if ($this->tabAccess['edit'] === '1')
					{
						if ($product->productAttributeExists(Tools::getValue('attribute_combination_list'), (int)$id_product_attribute))
							$this->errors[] = Tools::displayError('This attribute already exists.');
						else
						{
							if (Validate::isDateFormat(Tools::getValue('available_date_attribute')))
							{
								$product->updateAttribute((int)$id_product_attribute,
									Tools::getValue('attribute_wholesale_price'),
									Tools::getValue('attribute_price') * Tools::getValue('attribute_price_impact'),
									Tools::getValue('attribute_weight') * Tools::getValue('attribute_weight_impact'),
									Tools::getValue('attribute_unity') * Tools::getValue('attribute_unit_impact'),
									Tools::getValue('attribute_ecotax'),
									Tools::getValue('id_image_attr'),
									Tools::getValue('attribute_reference'),
									Tools::getValue('attribute_ean13'),
									Tools::getValue('attribute_default'),
									Tools::getValue('attribute_location'),
									Tools::getValue('attribute_upc'),
									Tools::getValue('minimal_quantity'),
									Tools::getValue('available_date_attribute'));

								Hook::exec('actionProductAttributeUpdate', array('id_product_attribute' => (int)$id_product_attribute));
								$this->updateDownloadProduct($product, 1, $id_product_attribute);
							}
							else
								$this->errors[] = Tools::displayError('Invalid date format.');
						}
					}
					else
						$this->errors[] = Tools::displayError('You do not have permission to add here.');
				}
				// Add new
				else
				{
					if ($this->tabAccess['add'] === '1')
					{
						if ($product->productAttributeExists(Tools::getValue('attribute_combination_list')))
							$this->errors[] = Tools::displayError('This combination already exists.');
						else
							$id_product_attribute = $product->addCombinationEntity(
								Tools::getValue('attribute_wholesale_price'),
								Tools::getValue('attribute_price') * Tools::getValue('attribute_price_impact'),
								Tools::getValue('attribute_weight') * Tools::getValue('attribute_weight_impact'),
								Tools::getValue('attribute_unity') * Tools::getValue('attribute_unit_impact'),
								Tools::getValue('attribute_ecotax'),
								Tools::getValue('id_image_attr'),
								Tools::getValue('attribute_reference'),
								null,
								Tools::getValue('attribute_ean13'),
								Tools::getValue('attribute_default'),
								Tools::getValue('attribute_location'),
								Tools::getValue('attribute_upc')
							);
						$this->updateDownloadProduct($product, 0, $id_product_attribute);
					}
					else
						$this->errors[] = Tools::displayError('You do not have permission to').'<hr>'.Tools::displayError('Edit here.');
				}
				if (!count($this->errors))
				{
					$combination = new Combination((int)$id_product_attribute);
					$combination->setAttributes(Tools::getValue('attribute_combination_list'));
					$product->checkDefaultAttributes();
				}
				if (!count($this->errors))
				{
					if (!$product->cache_default_attribute)
						Product::updateDefaultAttribute($product->id);

					if (!empty($is_virtual))
						Product::updateIsVirtual($product->id);

					//$this->redirect_after = self::$currentIndex.'&id_product='.$product->id.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&add'.$this->table.'&conf=4&action=Combinations&token='.($token ? $token : $this->token);
				}
			}
		}
	}

	public function processFeatures($token)
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
						else
							$id_value = $product->addFeaturesToDB($match[1], 0, 1);
					}
				}
			}
		}
		else
			$this->errors[] = Tools::displayError('Product must be created before adding features.');
	}

	/**
	 * This function is never called at the moment (specific prices cannot be edited)
	 *
	 * @param $token
	 */
	public function processPricesModification($token)
	{
		$id_specific_prices = Tools::getValue('spm_id_specific_price');
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
			if ($this->_validateSpecificPrice($id_shops[$key], $id_currencies[$key], $id_countries[$key], $id_groups[$key], $id_customers[$key], $prices[$key], $from_quantities[$key], $reductions[$key], $reduction_types[$key], $froms[$key], $tos[$key]))
			{
				$specific_price = new SpecificPrice((int)($id_specific_price));
				$specific_price->id_shop = (int)$id_shops[$key];
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
					$this->errors = Tools::displayError('An error occurred while updating the specific price.');
			}
		if (!count($this->errors))
			$this->redirect_after = self::$currentIndex.'&id_product='.(int)(Tools::getValue('id_product')).(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&update'.$this->table.'&action=Prices&token='.($token ? $token : $this->token);

	}

	public function processPriceAddition($token)
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
		$price = Tools::getValue('sp_price');
		$from_quantity = Tools::getValue('sp_from_quantity');
		$reduction = (float)(Tools::getValue('sp_reduction'));
		$reduction_type = !$reduction ? 'amount' : Tools::getValue('sp_reduction_type');
		$from = Tools::getValue('sp_from');
		$to = Tools::getValue('sp_to');
		if ($this->_validateSpecificPrice($id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to))
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
			$specificPrice->from = !$from ? '0000-00-00 00:00:00' : $from;
			$specificPrice->to = !$to ? '0000-00-00 00:00:00' : $to;
			if (!$specificPrice->add())
				$this->errors = Tools::displayError('An error occurred while updating the specific price.');
		}
	}

	public function ajaxProcessDeleteSpecificPrice()
	{
		if ($this->tabAccess['delete'] === '1')
		{
			$id_specific_price = (int)Tools::getValue('id_specific_price');
			if (!$id_specific_price || !Validate::isUnsignedId($id_specific_price))
				$error = Tools::displayError('Invalid specific price ID');
			else
			{
				$specificPrice = new SpecificPrice((int)$id_specific_price);
				if (!$specificPrice->delete())
					$error = Tools::displayError('An error occurred while deleting the specific price');
			}
		}
		else
			$error = Tools::displayError('You do not have permission to delete here.');

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

	public function processSpecificPricePriorities($token)
	{
		if (!($obj = $this->loadObject()))
			return;
		if (!$priorities = Tools::getValue('specificPricePriority'))
			$this->errors[] = Tools::displayError('Please specify priorities');
		elseif (Tools::isSubmit('specificPricePriorityToAll'))
		{
			if (!SpecificPrice::setPriorities($priorities))
				$this->errors[] = Tools::displayError('An error occurred while updating priorities.');
			else
				$this->confirmations[] = $this->l('Price rule successfully updated');
		}
		elseif (!SpecificPrice::setSpecificPriority((int)$obj->id, $priorities))
			$this->errors[] = Tools::displayError('An error occurred while setting priorities.');
	}

	public function processCustomizationConfiguration($token)
	{
		if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
		{
			if (!$product->createLabels((int)Tools::getValue('uploadable_files') - (int)$product->uploadable_files, (int)Tools::getValue('text_fields') - (int)$product->text_fields))
				$this->errors[] = Tools::displayError('An error occurred while creating customization fields.');
			if (!count($this->errors) && !$product->updateLabels())
				$this->errors[] = Tools::displayError('An error occurred while updating customization.');
			$product->uploadable_files = (int)Tools::getValue('uploadable_files');
			$product->text_fields = (int)Tools::getValue('text_fields');
			$product->customizable = ((int)Tools::getValue('uploadable_files') > 0 || (int)Tools::getValue('text_fields') > 0) ? 1 : 0;
			if (!count($this->errors) && !$product->update())
				$this->errors[] = Tools::displayError('An error occurred while updating customization configuration.');
			if (empty($this->errors))
				$this->confirmations[] = $this->l('Update successfull');
		}
		else
			$this->errors[] = Tools::displayError('Product must be created before adding customization possibilities.');
	}

	public function processProductCustomization($token)
	{
		if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
		{
			foreach ($_POST as $field => $value)
				if (strncmp($field, 'label_', 6) == 0 && !Validate::isLabel($value))
					$this->errors[] = Tools::displayError('Label fields are invalid');
			if (empty($this->errors) && !$product->updateLabels())
				$this->errors[] = Tools::displayError('An error occurred while updating customization.');
			if (empty($this->errors))
				$this->confirmations[] = $this->l('Update successful');
		}
		else
			$this->errors[] = Tools::displayError('Product must be created before adding customization possibilities.');
	}

	/**
	 * Overrides parent for custom redirect link
	 * @param $token
	 */
	public function processPosition($token)
	{
		if (!Validate::isLoadedObject($object = $this->loadObject()))
		{
			$this->errors[] = Tools::displayError('An error occurred while updating status for object.').
				' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
		}
		else if (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position')))
			$this->errors[] = Tools::displayError('Failed to update the position.');
		else
			$this->redirect_after = self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&action=Customization&conf=5'.(($id_category = (Tools::getIsset('id_category') ? (int)Tools::getValue('id_category') : '')) ? ('&id_category='.$id_category) : '').'&token='.Tools::getAdminTokenLite('AdminProducts');
	}

	public function initProcess()
	{
		// Delete a product in the download folder
		if (Tools::getValue('deleteVirtualProduct'))
		{
			if ($this->tabAccess['delete'] === '1')
				$this->action = 'deleteVirtualProduct';
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		elseif (Tools::getValue('deleteVirtualProductAttribute'))/* Delete a product in the download folder */
		{
			if ($this->tabAccess['delete'] === '1')
				$this->action = 'deleteVirtualProductAttribute';
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
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
				$this->errors[] = Tools::displayError('You do not have permission to add here.');
		}
		elseif (Tools::isSubmit('submitAttachments'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$this->action = 'attachments';
				$this->tab_display = 'attachments';
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		// Product duplication
		elseif (Tools::getIsset('duplicate'.$this->table))
		{
			if ($this->tabAccess['add'] === '1')
				$this->action = 'duplicate';
			else
				$this->errors[] = Tools::displayError('You do not have permission to add here.');
		}
		// Product images management
		elseif (Tools::getValue('id_image') && Tools::getValue('ajax'))
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'image';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		// Product attributes management
		elseif (Tools::isSubmit('submitProductAttribute'))
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'productAttribute';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		// Product features management
		elseif (Tools::isSubmit('submitFeatures') || Tools::isSubmit('submitFeaturesAndStay'))
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'features';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		// Product specific prices management NEVER USED
		elseif (Tools::isSubmit('submitPricesModification'))
		{
			if ($this->tabAccess['add'] === '1')
				$this->action = 'pricesModification';
			else
				$this->errors[] = Tools::displayError('You do not have permission to add here.');
		}
		elseif (Tools::isSubmit('deleteSpecificPrice'))
		{
			if ($this->tabAccess['delete'] === '1')
				$this->action = 'deleteSpecificPrice';
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		elseif (Tools::isSubmit('submitSpecificPricePriorities'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$this->action = 'specificPricePriorities';
				$this->tab_display = 'prices';
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
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
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
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
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if (!$this->action)
			parent::initProcess();

		if ($this->action == 'save' || $this->action == 'new' && isset($this->available_tabs[Tools::getValue('key_tab')]))
			$this->tab_display = Tools::getValue('key_tab');

		// Set tab to display if not decided already
		if (!$this->tab_display && $this->action)
			if (in_array($this->action, array_keys($this->available_tabs)))
				$this->tab_display = $this->action;

		// And if still not set, use default
		if (!$this->tab_display)
			$this->tab_display = $this->default_tab;
	}

	/**
	 * Override parent to add stock data to object
	 * We don't want to make a "full" product load because of side effects to prices
	 *
	 * @param boolean $opt Return an empty object if load fail
	 * @return object
	 */
	protected function loadObject($opt = false)
	{
		$result = parent::loadObject($opt);
		if ($result)
			$this->object->loadStockData();
		return $result;
	}

	/**
	 * postProcess handle every checks before saving products information
	 *
	 * @param mixed $token
	 * @return void
	 */
	public function postProcess($token = null)
	{
		if (!$this->redirect_after)
			parent::postProcess();
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
			$json = array(
				'name' => $result['success']['name'],
				'status' => 'ok',
				'id'=>$obj->id,
				'path' => $obj->getExistingImgPath(),
				'position' => $obj->position,
				'cover' => $obj->cover,
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

				$id_product_download = (int)ProductDownload::getIdFromIdAttribute($id_product, $id_product_attribute);
				if ($id_product_download)
				{
					$product_download = new ProductDownload((int)$id_product_download);
					$this->deleteDownloadProduct((int)$id_product_download);
					$product_download->deleteFile();
				}
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
					'message'=> $this->l('Cannot delete attribute')
				);
		}
		else
			$json = array(
				'status' => 'error',
				'message'=> $this->l('You do not have permission to delete here.')
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
					'message'=> $this->l('Cannot make default attribute')
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
				$product_download = ProductDownload::getAttributeFromIdAttribute($id_product, $id_product_attribute);
				foreach ($combinations as $key => $combination)
				{
					$combinations[$key]['attributes'][] = array($combination['group_name'], $combination['attribute_name'], $combination['id_attribute']);

					// Added fields virtual product
					$combinations[$key]['id_product_download'] = count($product_download) ? $product_download[0]['id_product_download'] : '';
					$combinations[$key]['display_filename'] = count($product_download) ? $product_download[0]['display_filename'] : '';
					$combinations[$key]['filename'] = count($product_download) ? $product_download[0]['filename'] : '';
					$combinations[$key]['date_expiration'] = count($product_download) ? $product_download[0]['date_expiration'] : '0000-00-00';
					$combinations[$key]['nb_days_accessible'] = count($product_download) ? $product_download[0]['nb_days_accessible'] : 0;
					$combinations[$key]['nb_downloadable'] = count($product_download) ? $product_download[0]['nb_downloadable'] : 0;
					$combinations[$key]['is_shareable'] = count($product_download) ? $product_download[0]['is_shareable'] : 0;
				}

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
		$this->json = true;
		if (($id_image = Tools::getValue('id_image')) && ($id_shop = (int)Tools::getValue('id_shop')))
			if (Tools::getValue('active') == 'true')
				$res = Db::getInstance()->execute(
					'INSERT INTO '._DB_PREFIX_.'image_shop (`id_image`, `id_shop`)
					VALUES('.(int)$id_image.', '.(int)$id_shop.')
				');
			else
				$res = Db::getInstance()->execute('
					DELETE FROM '._DB_PREFIX_.'image_shop
					WHERE `id_image`='.(int)$id_image.' && `id_shop`='.(int)$id_shop
				);

		if ($res)
			$this->confirmations[] = $this->_conf[27];
		else
			$this->errors[] = Tools::displayError('Error on picture shop association');
		$this->status = 'ok';
	}

	public function ajaxProcessUpdateImagePosition()
	{
		$this->json = true;
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
			$this->confirmations[] = $this->_conf[25];
		else
			$this->errors[] = Tools::displayError('Error on moving picture');
		$this->status = 'ok';
	}

	public function ajaxProcessUpdateCover()
	{
		$this->json = true;
		Image::deleteCover((int)Tools::getValue('id_product'));
		$img = new Image((int)Tools::getValue('id_image'));
		$img->cover = 1;

		@unlink(_PS_TMP_IMG_DIR_.'product_'.(int)$img->id_product.'.jpg');
		@unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$img->id_product.'.jpg');

		if ($img->update())
			$this->confirmations[] = $this->_conf[26];
		else
			$this->errors[] = Tools::displayError('Error on moving picture');
	}

	public function ajaxProcessDeleteProductImage()
	{
		$this->json = true;
		$this->display = 'content';
		$res = true;
		/* Delete product image */
		$image = new Image((int)Tools::getValue('id_image'));
		$this->content['id'] = $image->id;
		$res &= $image->delete();
		// if deleted image was the cover, change it to the first one
		if (!Image::getCover($image->id_product))
		{
			$res &= Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'image`
			SET `cover` = 1
			WHERE `id_product` = '.(int)$image->id_product.' LIMIT 1');
		}

		if (file_exists(_PS_TMP_IMG_DIR_.'product_'.$image->id_product.'.jpg'))
			$res &= @unlink(_PS_TMP_IMG_DIR_.'product_'.$image->id_product.'.jpg');
		if (file_exists(_PS_TMP_IMG_DIR_.'product_mini_'.$image->id_product.'.jpg'))
			$res &= @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.$image->id_product.'.jpg');

		if ($res)
			$this->confirmations[] = $this->_conf[7];
		else
			$this->errors[] = Tools::displayError('Error on deleting product image');

		$this->status = 'ok';
	}

	protected function _validateSpecificPrice($id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to)
	{
		if (!Validate::isUnsignedId($id_shop) || !Validate::isUnsignedId($id_currency) || !Validate::isUnsignedId($id_country) || !Validate::isUnsignedId($id_group) || !Validate::isUnsignedId($id_customer))
			$this->errors[] = Tools::displayError('Wrong ID\'s');
		else if ((empty($price) && empty($reduction)) || (!empty($price) && !Validate::isPrice($price)) || (!empty($reduction) && !Validate::isPrice($reduction)))
			$this->errors[] = Tools::displayError('Invalid price/reduction amount');
		else if (!Validate::isUnsignedInt($from_quantity))
			$this->errors[] = Tools::displayError('Invalid quantity');
		else if ($reduction && !Validate::isReductionType($reduction_type))
			$this->errors[] = Tools::displayError('Please select a reduction type (amount or percentage)');
		else if ($from && $to && (!Validate::isDateFormat($from) || !Validate::isDateFormat($to)))
			$this->errors[] = Tools::displayError('Wrong from/to date');
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
					$this->errors[] = Tools::displayError('name for feature').' <b>'.$feature['name'].'</b> '.Tools::displayError('is too long in').' '.$current_language->name;
				elseif (!call_user_func(array('Validate', $rules['validateLang']['value']), $val))
					$this->errors[] = Tools::displayError('Valid name required for feature.').' <b>'.$feature['name'].'</b> '.Tools::displayError('in').' '.$current_language->name;
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
				$this->errors[] = Tools::displayError('An error occurred while loading object image.');
			else
			{
				if (($cover = Tools::getValue('cover')) == 1)
					Image::deleteCover($product->id);
				$image->cover = $cover;
				$this->validateRules('Image');
				$this->copyFromPost($image, 'image');
				if (count($this->errors) || !$image->update())
					$this->errors[] = Tools::displayError('An error occurred while updating image.');
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
				$this->errors[] = Tools::displayError('An error occurred during new folder creation');
			if (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image_product']['tmp_name'], $tmpName))
				$this->errors[] = Tools::displayError('An error occurred during the image upload');
			else if (!ImageManager::resize($tmpName, $new_path.'.'.$image->image_format))
				$this->errors[] = Tools::displayError('An error occurred while copying image.');
			else if ($method == 'auto')
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

	public function processAdd($token)
	{
		$this->checkProduct();

		if (!empty($this->errors))
			return false;

		$this->object = new $this->className();
		$this->_removeTaxFromEcotax();
		$this->copyFromPost($this->object, $this->table);

		if ($this->object->add())
		{
			$this->addCarriers();
			$this->updateAssoShop((int)$this->object->id);
			$this->updateAccessories($this->object);
			if (!$this->updatePackItems($this->object))
				$this->errors[] = Tools::displayError('An error occurred while adding products to the pack.');
			$this->updateDownloadProduct($this->object);
			$this->object->setTaxRulesGroup((int)Tools::getValue('id_tax_rules_group'), true);

			if (!count($this->errors))
			{
				$languages = Language::getLanguages(false);
				if (!$this->object->updateCategories(Tools::getValue('categoryBox')))
					$this->errors[] = Tools::displayError('An error occurred while linking object.').' <b>'.$this->table.'</b> '.Tools::displayError('To categories');
				else if (!$this->updateTags($languages, $this->object))
					$this->errors[] = Tools::displayError('An error occurred while adding tags.');
				else
				{
					Hook::exec('actionProductAdd', array('product' => $this->object));
					Search::indexation(false, $this->object->id);
				}

				// If the product is virtual, set out_of_stock = 1 (allow sales when out of stock)
				if (Tools::getValue('type_product') == Product::PTYPE_VIRTUAL)
					StockAvailable::setProductOutOfStock($this->object->id, 1);
				else
					StockAvailable::setProductOutOfStock($this->object->id, 2);

				// Save and preview
				if (Tools::isSubmit('submitAddProductAndPreview'))
				{
					$preview_url = ($this->context->link->getProductLink(
										$this->getFieldValue($this->object, 'id'),
										$this->getFieldValue($this->object, 'link_rewrite', $this->context->language->id),
										Category::getLinkRewrite($this->getFieldValue($this->object, 'id_category_default'), $this->context->language->id)));

					if (!$this->object->active)
					{
						$admin_dir = dirname($_SERVER['PHP_SELF']);
						$admin_dir = substr($admin_dir, strrpos($admin_dir, '/') + 1);
						$token = Tools::encrypt('PreviewProduct'.$this->object->id);
						$preview_url .= '&adtoken='.$token.'&ad='.$admin_dir;
					}

					$this->redirect_after = $preview_url;
				}

				if (Tools::getValue('resizer') == 'man' && isset($id_image) && is_int($id_image) && $id_image)
					$this->redirect_after = self::$currentIndex.'&id_product='.$this->object->id
						.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '')
						.'&id_image='.(int)$id_image.'&imageresize&toconf=3&submitAddAndStay='.(Tools::isSubmit('submitAdd'.$this->table.'AndStay') ? 'on' : 'off')
						.'&token='.($token ? $token : $this->token);

				// Save and stay on same form
				if ($this->display == 'edit')
					$this->redirect_after = self::$currentIndex.'&id_product='.(int)$this->object->id
						.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '')
						.'&addproduct&conf=3&key_tab='.Tools::safeOutput(Tools::getValue('key_tab')).'&token='.($token ? $token : $this->token);
				else
					// Default behavior (save and back)
					$this->redirect_after = self::$currentIndex
						.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '')
						.'&conf=3&token='.($token ? $token : $this->token);
			}
			else
				$this->object->delete();
		}
		else
			$this->errors[] = Tools::displayError('An error occurred while creating object.').' <b>'.$this->table.'</b>';
	}

	public function processUpdate($token)
	{
		$this->checkProduct();
		if (!empty($this->errors))
			return false;

		$id = (int)Tools::getValue('id_'.$this->table);
		/* Update an existing product */
		if (isset($id) && !empty($id))
		{
			$object = new $this->className((int)$id);
			$this->object = $object;

			if (Validate::isLoadedObject($object))
			{
				$this->_removeTaxFromEcotax();
				$this->copyFromPost($object, $this->table);

				if ($object->update())
				{
					$this->addCarriers();
					$this->updateAccessories($object);
					$this->updateDownloadProduct($object, 1);
					$this->updateAssoShop((int)$object->id);
					$this->processAccounting($token);
					$this->processSuppliers($token);
					$this->processWarehouses($token);
					$this->processFeatures($token);
					$this->processProductAttribute($token);
					$this->processPriceAddition($token);
					$this->processSpecificPricePriorities($token);
					$this->object->setTaxRulesGroup((int)Tools::getValue('id_tax_rules_group'));
					if (!$this->updatePackItems($object))
						$this->errors[] = Tools::displayError('An error occurred while adding products to the pack.');
					elseif (!$object->updateCategories(Tools::getValue('categoryBox'), true))
						$this->errors[] = Tools::displayError('An error occurred while linking object.').' <b>'.$this->table.'</b> '.Tools::displayError('To categories');
					elseif (!$this->updateTags(Language::getLanguages(false), $object))
						$this->errors[] = Tools::displayError('An error occurred while adding tags.');
					elseif (empty($this->errors))
					{
						Hook::exec('actionProductUpdate', array('product' => $object));
						Search::indexation(false, $object->id);

						// Save and preview
						if (Tools::isSubmit('submitAddProductAndPreview'))
						{
							$preview_url = $this->context->link->getProductLink($this->getFieldValue($object, 'id'), $this->getFieldValue($object, 'link_rewrite', $this->context->language->id), Category::getLinkRewrite($this->getFieldValue($object, 'id_category_default'), $this->context->language->id), null, null, Context::getContext()->shop->id);
							if (!$object->active)
							{
								$admin_dir = dirname($_SERVER['PHP_SELF']);
								$admin_dir = substr($admin_dir, strrpos($admin_dir, '/') + 1);
								$token = Tools::encrypt('PreviewProduct'.$object->id);
								if (strpos($preview_url, '?') === false)
									$preview_url .= '?';
								else
									$preview_url .= '&';
								$preview_url .= 'adtoken='.$token.'&ad='.$admin_dir;
							}
							$this->redirect_after = $preview_url;
						}
						else
						{
							// Save and stay on same form
							if ($this->display == 'edit')
								$this->confirmations[] = $this->l('Update successful');
							else
								// Default behavior (save and back)
								$this->redirect_after = self::$currentIndex.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&conf=4&token='.($token ? $token : $this->token);
						}
					}
				}
				else
					$this->errors[] = Tools::displayError('An error occurred while updating object.').' <b>'.$this->table.'</b> ('.Db::getInstance()->getMsgError().')';
			}
			else
				$this->errors[] = Tools::displayError('An error occurred while updating object.').' <b>'.$this->table.'</b> ('.Tools::displayError('Cannot load object').')';
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

		/* Check required fields */
		foreach ($rules['required'] as $field)
			if (($value = Tools::getValue($field)) == false && $value != '0')
			{
				if (Tools::getValue('id_'.$this->table) && $field == 'passwd')
					continue;
				$this->errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $field, $className).'</b> '.$this->l('is required');
			}

		/* Check multilingual required fields */
		foreach ($rules['requiredLang'] as $fieldLang)
			if (!Tools::getValue($fieldLang.'_'.$default_language->id))
				$this->errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $fieldLang, $className).'</b> '.$this->l('is required at least in').' '.$default_language->name;

		/* Check fields sizes */
		foreach ($rules['size'] as $field => $maxLength)
			if ($value = Tools::getValue($field) && Tools::strlen($value) > $maxLength)
				$this->errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $field, $className).'</b> '.$this->l('is too long').' ('.$maxLength.' '.$this->l('chars max').')';

		if (Tools::getIsset('description_short'))
		{
			$saveShort = Tools::getValue('description_short');
			$_POST['description_short'] = strip_tags(Tools::getValue('description_short'));
		}

		/* Check description short size without html */
		$limit = (int)Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
		if ($limit <= 0) $limit = 400;
		foreach ($languages as $language)
			if ($value = Tools::getValue('description_short_'.$language['id_lang']))
				if (Tools::strlen(strip_tags($value)) > $limit)
					$this->errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), 'description_short').' ('.$language['name'].')</b> '.$this->l('is too long').' : '.$limit.' '.$this->l('chars max').' ('.$this->l('count now').' '.Tools::strlen(strip_tags($value)).')';
		/* Check multilingual fields sizes */
		foreach ($rules['sizeLang'] as $fieldLang => $maxLength)
			foreach ($languages as $language)
				if ($value = Tools::getValue($fieldLang.'_'.$language['id_lang']) && Tools::strlen($value) > $maxLength)
					$this->errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $fieldLang, $className).' ('.$language['name'].')</b> '.$this->l('is too long').' ('.$maxLength.' '.$this->l('chars max').')';
		if (isset($_POST['description_short']))
			$_POST['description_short'] = $saveShort;

		/* Check fields validity */
		foreach ($rules['validate'] as $field => $function)
			if ($value = Tools::getValue($field))
				if (!Validate::$function($value))
					$this->errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $field, $className).'</b> '.$this->l('is invalid');

		/* Check multilingual fields validity */
		foreach ($rules['validateLang'] as $fieldLang => $function)
			foreach ($languages as $language)
				if ($value = Tools::getValue($fieldLang.'_'.$language['id_lang']))
					if (!Validate::$function($value))
						$this->errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $fieldLang, $className).' ('.$language['name'].')</b> '.$this->l('is invalid');

		/* Categories */
		$productCats = '';
		if (!Tools::isSubmit('categoryBox') || !count(Tools::getValue('categoryBox')))
			$this->errors[] = $this->l('product must be in at least one Category');

		if (!is_array(Tools::getValue('categoryBox')) || !in_array(Tools::getValue('id_category_default'), Tools::getValue('categoryBox')))
			$this->errors[] = $this->l('product must be in the default category');

		/* Tags */
		foreach ($languages as $language)
			if ($value = Tools::getValue('tags_'.$language['id_lang']))
				if (!Validate::isTagsList($value))
					$this->errors[] = $this->l('Tags list').' ('.$language['name'].') '.$this->l('is invalid');
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
	public function updateDownloadProduct($product, $edit = 0, $id_product_attribute = null)
	{
		$is_virtual_file = (int)Tools::getValue('is_virtual_file');

		// add or update a virtual product
		if (Tools::getValue('is_virtual_good') == 'true')
		{
			if (!Tools::getValue('virtual_product_name') && !Tools::getValue('virtual_product_name_attribute') && !empty($is_virtual_file))
			{
				if (!Tools::getValue('virtual_product_name'))
				{
					if (!Tools::getValue('virtual_product_name_attribute') && !empty($id_product_attribute))
					{
						$this->errors[] = $this->l('the field').' <b>'.$this->l('display filename attribute').'</b> '.$this->l('is required');
						return false;
					}
					elseif (!empty($id_product_attribute))
					{
						$this->errors[] = $this->l('the field').' <b>'.$this->l('display filename').'</b> '.$this->l('is required');
						return false;
					}
				}
			}

			if (Tools::getValue('virtual_product_nb_days') === false && Tools::getValue('virtual_product_nb_days_attribute') === false && !empty($is_virtual_file))
			{
				if (!Tools::getValue('virtual_product_nb_days'))
				{
					if (!Tools::getValue('virtual_product_nb_days_attribute'))
					{
						if (!empty($edit) && !empty($id_product_attribute))
						{
							$this->errors[] = $this->l('the field').' <b>'.$this->l('number of days attribute').'</b> '.$this->l('is required');
							return false;
						}
					}
					elseif (!empty($id_product_attribute))
					{
						$this->errors[] = $this->l('the field').' <b>'.$this->l('number of days').'</b> '.$this->l('is required');
						return false;
					}
				}
			}

			if (Tools::getValue('virtual_product_expiration_date') && !Validate::isDate(Tools::getValue('virtual_product_expiration_date') && !empty($is_virtual_file))
			&& Tools::getValue('virtual_product_expiration_date_attribute') && !Validate::isDate(Tools::getValue('virtual_product_expiration_date_attribute')))
			{
				if (!Tools::getValue('virtual_product_expiration_date'))
				{
					if (!Tools::getValue('virtual_product_expiration_date_attribute'))
					{
						$this->errors[] = $this->l('the field').' <b>'.$this->l('expiration date attribute').'</b> '.$this->l('is required');
						return false;
					}
					else if (!empty($id_product_attribute))
					{
						$this->errors[] = $this->l('the field').' <b>'.$this->l('expiration date').'</b> '.$this->l('is not valid');
						return false;
					}
				}
			}

			// Trick's
			if ($edit == 1)
			{
				$id_product_download_attribute = ProductDownload::getIdFromIdAttribute((int)$product->id, $id_product_attribute);
				$id_product_download = $id_product_download_attribute ? (int)$id_product_download_attribute : (int)Tools::getValue('virtual_product_id');
			}
			else
				$id_product_download = Tools::getValue('virtual_product_id');

			$is_shareable = Tools::getValue('virtual_product_is_shareable');
			$virtual_product_name = Tools::getValue('virtual_product_name');
			$virtual_product_filename = Tools::getValue('virtual_product_filename');
			$virtual_product_nb_days = Tools::getValue('virtual_product_nb_days');
			$virtual_product_nb_downloable = Tools::getValue('virtual_product_nb_downloable');
			$virtual_product_expiration_date = Tools::getValue('virtual_product_expiration_date');

			$is_shareable_attribute = Tools::getValue('virtual_product_is_shareable_attribute');
			$virtual_product_name_attribute = Tools::getValue('virtual_product_name_attribute');
			$virtual_product_filename_attribute = Tools::getValue('virtual_product_filename_attribute');
			$virtual_product_nb_days_attribute = Tools::getValue('virtual_product_nb_days_attribute');
			$virtual_product_nb_downloable_attribute = Tools::getValue('virtual_product_nb_downloable_attribute');
			$virtual_product_expiration_date_attribute = Tools::getValue('virtual_product_expiration_date_attribute');

			if (!empty($is_shareable_attribute))
				$is_shareable = $is_shareable_attribute;

			if (!empty($virtual_product_name_attribute))
				$virtual_product_name = $virtual_product_name_attribute;

			if (!empty($virtual_product_nb_days_attribute))
				$virtual_product_nb_days = $virtual_product_nb_days_attribute;

			if (!empty($virtual_product_nb_downloable_attribute))
				$virtual_product_nb_downloable = $virtual_product_nb_downloable_attribute;

			if (!empty($virtual_product_expiration_date_attribute))
				$virtual_product_expiration_date = $virtual_product_expiration_date_attribute;

			if (!empty($virtual_product_filename_attribute))
				$filename = $virtual_product_filename_attribute;
			elseif ($virtual_product_filename)
				$filename = $virtual_product_filename;
			else
				$filename = ProductDownload::getNewFilename();

			$download = new ProductDownload((int)$id_product_download);
			$download->id_product = (int)$product->id;
			$download->id_product_attribute = (int)$id_product_attribute;
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
				$id_product_download_attribute = ProductDownload::getIdFromIdAttribute((int)$product->id, (int)$id_product_attribute);
				$id_product_download = ($id_product_download_attribute) ? (int)$id_product_download_attribute : (int)Tools::getValue('virtual_product_id');
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

	public function deleteDownloadProduct($id_product_attribute = null)
	{
		if (!empty($id_product_attribute))
		{
			$product_download = new ProductDownload((int)$id_product_attribute);
			$product_download->date_expiration = date('Y-m-d H:i:s', time() - 1);
			$product_download->active = 0;
			return $product_download->save();
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
		$tagError = true;
		/* Reset all tags for THIS product */
		if (!Tag::deleteTagsForProduct((int)$product->id))
			return false;
		/* Assign tags to this product */
		foreach ($languages as $language)
			if ($value = Tools::getValue('tags_'.$language['id_lang']))
				$tagError &= Tag::addTags($language['id_lang'], (int)$product->id, $value);
		return $tagError;
	}

	public function initContent($token = null)
	{
		if ($this->display == 'edit' || $this->display == 'add')
		{
			$this->addJS(_PS_JS_DIR_.'admin-products.js');
			$this->fields_form = array();

			// Check if Module
			if (substr($this->tab_display, 0, 6) == 'Module')
			{
				$this->tab_display_module = strtolower(substr($this->tab_display, 6, strlen($this->tab_display) - 6));
				$this->tab_display = 'Modules';
			}

			if (method_exists($this, 'initForm'.$this->tab_display))
				$this->tpl_form = strtolower($this->tab_display).'.tpl';

			if ($this->ajax)
				$this->content_only = true;
			else
			{
				$product_tabs = array();

				// Check if Module
				if (substr($this->tab_display, 0, 6) == 'Module')
				{
					$this->tab_display = 'Module';
					$this->tab_display_module = strtolower(substr($this->tab_display, 6, strlen($this->tab_display) - 6));
				}

				// tab_display defines which tab to display first
				if (!method_exists($this, 'initForm'.$this->tab_display))
					$this->tab_display = $this->default_tab;

				$advanced_stock_management_active = Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');
				$stock_management_active = Configuration::get('PS_STOCK_MANAGEMENT');

				foreach ($this->available_tabs as $product_tab => $value)
				{
					// if it's the quantities tab and stock management is disabled, continue
					if ($stock_management_active == 0 && $product_tab == 'Quantities')
						continue;

					// if it's the warehouses tab and advanced stock management is disabled, continue
					if ($advanced_stock_management_active == 0 && $product_tab == 'Warehouses')
						continue;

					$product_tabs[$product_tab] = array(
						'id' => $product_tab,
						'selected' => (strtolower($product_tab) == strtolower($this->tab_display)),
						'name' => $this->available_tabs_lang[$product_tab],
						'href' => $this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.(int)Tools::getValue('id_product').'&amp;action='.$product_tab,
					);
				}
				$this->tpl_form_vars['product_tabs'] = $product_tabs;
			}
		}
		else
		{
			if ($id_category = (int)Tools::getValue('id_category'))
				self::$currentIndex .= '&id_category='.(int)$id_category;
			$this->getList($this->context->language->id, !$this->context->cookie->__get($this->table.'Orderby') ? 'position' : null, !$this->context->cookie->__get($this->table.'Orderway') ? 'ASC' : null, 0, null, $this->context->shop->id);

			$id_category = (int)Tools::getValue('id_category', 1);
			$this->tpl_list_vars['is_category_filter'] = Tools::getValue('id_category') ? true : false;

			// Generate category selection tree
			$helper = new Helper();
			$this->tpl_list_vars['category_tree'] = $helper->renderCategoryTree(null, array((int)$id_category), 'categoryBox', true, false);

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

		if (!Tools::getValue('id_category'))
			unset($this->fieldsDisplay['position']);

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
				'.$this->l('Your product will be saved as draft').'
				</span>
				<span style="float:right"><a href="#" class="button" style="display: block" onclick="submitAddProductAndPreview()" >'.$this->l('Save and preview').'</a></span>
				<input type="hidden" name="fakeSubmitAddProductAndPreview" id="fakeSubmitAddProductAndPreview" />
				<br class="clear" />
				</p>
	 		</div>';
			$this->tpl_form_vars['draft_warning'] = $content;
	}

	public function initToolbar()
	{
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
							'desc' => $this->l('Delete this product'),
							'confirm' => 1);

					// adding button for duplicate this product
					if ($this->tabAccess['add'] && $this->display != 'add')
						$this->toolbar_btn['duplicate'] = array(
							'short' => 'Duplicate',
							//'href' => $this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.$product->id.'&amp;duplicateproduct',
							'desc' => $this->l('Duplicate'),
							'confirm' => 1,
							'js' => 'if (confirm(\''.$this->l('Also copy images').' ?\')) document.location = \''.$this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.(int)$product->id.'&amp;duplicateproduct\'; else document.location = \''.$this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.(int)$product->id.'&amp;duplicateproduct&amp;noimage=1\';'
						);

					// adding button for preview this product
					if ($url_preview = $this->getPreviewUrl($product))
						$this->toolbar_btn['preview'] = array(
							'short' => 'Preview',
							'href' => $url_preview,
							'desc' => $this->l('prevdesc'),
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

		parent::initToolbar();
		$this->context->smarty->assign('toolbar_fix', 1);
		$this->context->smarty->assign('show_toolbar', 1);
		$this->context->smarty->assign('toolbar_btn', $this->toolbar_btn);
	}

	public function initToolbarTitle()
	{
		parent::initToolbarTitle();
			if ($product = $this->loadObject(true))
				if ((bool)$product->id)
					$this->toolbar_title .= ' ('.$product->name[$this->context->language->id].')';
	}

	/**
	 * renderForm contains all necessary initialization needed for all tabs
	 *
	 * @return void
	 */
	public function renderForm()
	{
		if (!method_exists($this, 'initForm'.$this->tab_display))
			return;

		// Sort the tabs that need to be preloaded by their priority number
		asort($this->available_tabs, SORT_NUMERIC);
		$this->tpl_form_vars['tabs_preloaded'] = $this->available_tabs;

		$this->addJqueryUI('ui.datepicker');
        $this->addJqueryUI('ui.slider');
        $this->addJS(_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');

		// getLanguages init this->_languages
		$this->getLanguages();
		$languages = $this->_languages;
		$default_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$this->tpl_form_vars['defaultLanguage'] = Language::getLanguage($default_language);

		$this->tpl_form_vars['currentIndex'] = self::$currentIndex;
		$this->fields_form = array('');
		$this->display = 'edit';
		$this->tpl_form_vars['token'] = $this->token;
		$this->tpl_form_vars['combinationImagesJs'] = $this->getCombinationImagesJs();
		$this->tpl_form_vars['post_data'] = Tools::jsonEncode($_POST);

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

		// let's calculate this once for all
		if (!Validate::isLoadedObject($this->object) && Tools::getValue('id_product'))
			$this->errors[] = 'Unable to load object';
		else
		{
			$this->_displayDraftWarning($this->object->active);

			$this->initPack($this->object);
			$this->{'initForm'.$this->tab_display}($this->object, $languages, $default_language);
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

		$preview_url = $this->context->link->getProductLink(
			$product,
			$this->getFieldValue($product, 'link_rewrite', $this->context->language->id),
			Category::getLinkRewrite($product->id_category_default, $this->context->language->id), null, null, Context::getContext()->shop->id);
		if (!$product->active)
		{
			$preview_url = $this->context->link->getProductLink($product, $this->getFieldValue($product, 'link_rewrite', $this->default_form_language), Category::getLinkRewrite($this->getFieldValue($product, 'id_category_default'), $this->context->language->id));
			if (!$product->active)
			{
				$admin_dir = dirname($_SERVER['PHP_SELF']);
				$admin_dir = substr($admin_dir, strrpos($admin_dir, '/') + 1);
				$token = Tools::encrypt('PreviewProduct'.$product->id);

				$preview_url .= $product->active ? '' : '&adtoken='.$token.'&ad='.$admin_dir;
			}
		}
		return $preview_url;
	}

	/**
	* Post treatment for accounting
	*/
	public function processAccounting($token)
	{
		if (Validate::isLoadedObject(($product = new Product((int)Tools::getValue('id_product')))))
		{
			$id_shop = $this->context->shop->id;

			// If zone still exist, then update the database with the new value
			if (count($zones = Zone::getZones()))
			{
				// Build tab with associated data
				$tab = array();
				foreach ($zones as $zone)
					if (($num = Tools::getValue('zone_'.$zone['id_zone'])) !== null)
						$tab[] = array(
							'id_zone' => $zone['id_zone'],
							'id_product' => $product->id,
							'id_shop' => (int)$id_shop,
							'num' => $num);

				// Save to the database the account
				if (empty($tab) || !Accounting::saveProductAccountingInformations($tab))
					$this->errors[] = $this->l('Account Numbers could not be updated or added in the database');
			}
		}
	}

	/**
	* Post treatment for suppliers
	*/
	public function processSuppliers($token)
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
						$existing_id = (int)ProductSupplier::getIdByProductAndSupplier($product->id, $attribute['id_product_attribute'], $supplier->id_supplier);

						if ($existing_id <= 0)
						{
							$product->addSupplierReference($reference, (int)$attribute['id_product_attribute'], (int)$id_currency);
							if ($product->id_supplier == $supplier->id_supplier)
							{
								if ((int)$attribute['id_product_attribute'] > 0)
								{
									Db::getInstance()->execute('
										UPDATE '._DB_PREFIX_.'product_attribute
										SET supplier_reference = "'.pSQL($reference).'",
										wholesale_price = '.(float)Tools::convertPrice($price, $id_currency).'
										WHERE id_product = '.(int)$product->id.'
										AND id_product_attribute = '.(int)$attribute['id_product_attribute'].'
										LIMIT 1
									');
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
							$product_supplier = new ProductSupplier($existing_id);
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
	public function processWarehouses($token)
	{
		if ((int)Tools::getValue('warehouse_loaded') === 1 && Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
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
		}
	}

	/**
	* Init data for accounting
	*/
	public function initFormAccounting($obj)
	{
		$data = $this->createTemplate($this->tpl_form);

		if ($obj->id)
		{
			$error = '';
			$detail = array();

			if (count(Shop::getContextListShopID()) > 1)
				$error = $this->l('Please select the shop you want to configure');
			else
			{
				$zones = Zone::getZones();
				$id_shop = $this->context->shop->id;

				// Set default zone value to the shop	and sort it
				foreach ($zones as $zone)
				{
					$detail['zones'][$zone['id_zone']]['name'] = $zone['name'];
					$detail['zones'][$zone['id_zone']]['account_number'] = '';
				}
				$zoneAccountNumberList = Accounting::getProductAccountNumberZoneShop($obj->id, $id_shop);

				// Set Account number to the id_zone for an id_shop if exist
				foreach ($zoneAccountNumberList as $zone)
					$detail['zones'][$zone['id_zone']]['account_number'] = $zone['account_number'];
			}

			$data->assign(array(
				'productAccountNumberList' => $detail,
				'shopName' => $this->context->shop->name,
				'error' => $error,
				'product' => $obj
			));
		}
		else
			$this->displayWarning($this->l('You must save this product before manage accounting.'));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormAssociations($obj)
	{
		$product = $obj;
		$data = $this->createTemplate($this->tpl_form);
		// Prepare Categories tree for display in Associations tab
		$root = Category::getRootCategory();
		$default_category = Tools::getValue('id_category', $root->id);

		if (!$product->id)
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

		$data->assign('displayAssoShop', $helper->renderAssoShop());

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

		$helper = new Helper();
		$data->assign(array('default_category' => $default_category,
					'selected_cat_ids' => implode(',', array_keys($selected_cat)),
					'selected_cat' => $selected_cat,
					'category_tree' => $helper->renderCategoryTree(null, $selected_cat, 'categoryBox', false, true),
					'product' => $product,
					'link' => $this->context->link
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

			$data->assign(array(
				'shops' => $shops,
				'currencies' => $currencies,
				'countries' => $countries,
				'groups' => $groups,
				'combinations' => $combinations,
				'product' => $product,
				'multi_shop' => Shop::isFeatureActive(),
				'link' => new Link()
			));
		}
		else
			$this->displayWarning($this->l('You must save this product before adding specific prices'));
		// prices part
		$data->assign('link', $this->context->link);
		$data->assign('currency', $currency = $this->context->currency);
		$data->assign('tax_rules_groups', TaxRulesGroup::getTaxRulesGroups(true));
		$data->assign('taxesRatesByGroup', TaxRulesGroup::getAssociatedTaxRatesByIdCountry($this->context->country->id));
		$data->assign('ecotaxTaxRate', Tax::getProductEcotaxRate());
		$data->assign('tax_exclude_taxe_option', Tax::excludeTaxeOption());
		$data->assign('ps_use_ecotax', Configuration::get('PS_USE_ECOTAX'));
		if ($product->unit_price_ratio != 0)
			$data->assign('unit_price', Tools::ps_round($product->price / $product->unit_price_ratio, 2));
		else
			$data->assign('unit_price', 0);
		$data->assign('ps_tax', Configuration::get('PS_TAX'));

		$data->assign('country_display_tax_label', $this->context->country->display_tax_label);
		$unities = array(
			'PS_WEIGHT_UNIT' => Configuration::get('PS_WEIGHT_UNIT'),
			'PS_DISTANCE_UNIT' => Configuration::get('PS_DISTANCE_UNIT'),
			'PS_VOLUME_UNIT' => Configuration::get('PS_VOLUME_UNIT'),
			'PS_DIMENSION_UNIT' => Configuration::get('PS_DIMENSION_UNIT')
		);
		$data->assign(array(
			'currency', $this->context->currency,
			'product' => $product,
			'unities' => $unities,
			'token' => $this->token
		));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormSeo($product, $languages, $default_language)
	{
		$data = $this->createTemplate($this->tpl_form);

		$data->assign(array(
			'product' => $product,
			'languages' => $languages,
			'default_language' => $default_language,
			'ps_ssl_enabled' => Configuration::get('PS_SSL_ENABLED')
		));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormPack($product, $languages, $default_language)
	{
		$data = $this->createTemplate($this->tpl_form);

		$product->packItems = Pack::getItems($product->id, $this->context->language->id);

		$input_pack_items = '';
		if (Tools::getValue('inputPackItems'))
			$input_pack_items = Tools::getValue('inputPackItems');
		else
			foreach ($product->packItems as $pack_item)
				$input_pack_items .= $pack_item->pack_quantity.'x'.$pack_item->id.'-';

		$input_namepack_items = '';
		if (Tools::getValue('namePackItems'))
			$input_namepack_items = Tools::getValue('namePackItems');
		else
			foreach ($product->packItems as $pack_item)
				$input_namepack_items .= $pack_item->pack_quantity.' x '.$pack_item->name.'¤';

		$data->assign(array(
			'product' => $product,
			'languages' => $languages,
			'default_language' => $default_language,
			'ps_ssl_enabled' => Configuration::get('PS_SSL_ENABLED'),
			'is_pack' => ($product->id && Pack::isPack($product->id)) || Tools::getValue('ppack') || Tools::getValue('type_product') == Product::PTYPE_PACK,
			'input_pack_items' => $input_pack_items,
			'input_namepack_items' => $input_namepack_items
		));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormVirtualProduct($product, $languages, $default_language)
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
			&& !empty($product->cache_default_attribute))
			$msg = sprintf(Tools::displayError('This file "%s" is missing'), $product->productDownload->display_filename);
		else
			$msg = '';

		$data->assign('download_product_file_missing', $msg);
		$data->assign('download_dir_writable', ProductDownload::checkWritableDir());

		if (empty($product->cache_default_attribute))
		{
			$data->assign('show_file_input', !strval(Tools::getValue('virtual_product_filename')) || $product->productDownload->id > 0);
			// found in informations and combination : to merge
			$data->assign('up_filename', strval(Tools::getValue('virtual_product_filename')));
			$display_filename = ($product->productDownload->id > 0) ? $product->productDownload->display_filename : htmlentities(Tools::getValue('virtual_product_name'), ENT_COMPAT, 'UTF-8');

			if (!$product->productDownload->id || !$product->productDownload->active)
				$hidden = 'display:none;';
			else
				$hidden = '';

			$product->productDownload->nb_downloadable = ($product->productDownload->id > 0) ? $product->productDownload->nb_downloadable : htmlentities(Tools::getValue('virtual_product_nb_downloable'), ENT_COMPAT, 'UTF-8');
			$product->productDownload->date_expiration = ($product->productDownload->id > 0) ? ((!empty($product->productDownload->date_expiration) && $product->productDownload->date_expiration != '0000-00-00 00:00:00') ? date('Y-m-d', strtotime($product->productDownload->date_expiration)) : '' ) : htmlentities(Tools::getValue('virtual_product_expiration_date'), ENT_COMPAT, 'UTF-8');
			$product->productDownload->nb_days_accessible = ($product->productDownload->id > 0) ? $product->productDownload->nb_days_accessible : htmlentities(Tools::getValue('virtual_product_nb_days'), ENT_COMPAT, 'UTF-8');
			$product->productDownload->is_shareable = $product->productDownload->id > 0 && $product->productDownload->is_shareable;
		}
		else
		{
			$error = '';
			$product_attribute = ProductDownload::getAttributeFromIdProduct($this->getFieldValue($product, 'id'));
			foreach ($product_attribute as $p)
			{
				$product_download_attribute = new ProductDownload($p['id_product_download']);
				$exists_file2 = realpath(_PS_DOWNLOAD_DIR_).'/'.$product_download_attribute->filename;
				if (!file_exists($exists_file2) && !empty($product_download_attribute->id_product_attribute))
				{
					$msg = sprintf(Tools::displayError('This file "%s" is missing'), $product_download_attribute->display_filename);
					$error .= '<p class="alert" id="file_missing">
						<b>'.$msg.' :<br/>
						'.realpath(_PS_DOWNLOAD_DIR_).'/'.$product_download_attribute->filename.'</b>
					</p>';
				}
			}
			$data->assign('error_product_download', $error);
		}

		$data->assign('ad', dirname($_SERVER['PHP_SELF']));
		$data->assign('product', $product);
		$data->assign('token', $this->token);
		$data->assign('currency', $currency);
		$data->assign($this->tpl_form_vars);
		$data->assign('link', $this->context->link);
		$this->tpl_form_vars['product'] = $product;
		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	protected function _getFinalPrice($specific_price, $productPrice, $taxRate)
	{
		$price = Tools::ps_round((float)($specific_price['price']) ? $specific_price['price'] : $productPrice, 2);
		if (!(float)($specific_price['reduction']))
			return (float)($specific_price['price']);
		return ($specific_price['reduction_type'] == 'amount') ? ($price - $specific_price['reduction'] / (1 + $taxRate / 100)) : ($price - $price * $specific_price['reduction']);
	}

	protected function _displaySpecificPriceModificationForm($defaultCurrency, $shops, $currencies, $countries, $groups)
	{
		$content = '';
		if (!($obj = $this->loadObject()))
			return;
		$specific_prices = SpecificPrice::getByProductId((int)$obj->id);
		$specific_price_priorities = SpecificPrice::getPriority((int)$obj->id);

		$taxRate = $obj->getTaxesRate(Address::initialize());

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
					<td colspan="13">'.$this->l('No specific prices').'</td>
				</tr>';
		else
		{
			$i = 0;
			foreach ($specific_prices as $specific_price)
			{
				$current_specific_currency = $currencies[($specific_price['id_currency'] ? $specific_price['id_currency'] : $defaultCurrency->id)];
				if ($specific_price['reduction_type'] == 'percentage')
					$reduction = ($specific_price['reduction'] * 100).' %';
				else
					$reduction = Tools::displayPrice(Tools::ps_round($specific_price['reduction'], 2), $current_specific_currency);

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

				$content .= '
				<tr '.($i % 2 ? 'class="alt_row"' : '').'>
					<td class="cell border">'.$rule_name.'</td>
					<td class="cell border">'.$attributes_name.'</td>
					'.(Shop::isFeatureActive() ? '<td class="cell border">'.($specific_price['id_shop'] ? $shops[$specific_price['id_shop']]['name'] : $this->l('All shops')).'</td>' : '').'
					<td class="cell border">'.($specific_price['id_currency'] ? $currencies[$specific_price['id_currency']]['name'] : $this->l('All currencies')).'</td>
					<td class="cell border">'.($specific_price['id_country'] ? $countries[$specific_price['id_country']]['name'] : $this->l('All countries')).'</td>
					<td class="cell border">'.($specific_price['id_group'] ? $groups[$specific_price['id_group']]['name'] : $this->l('All groups')).'</td>
					<td class="cell border" title="'.$this->l('ID:').' '.$specific_price['id_customer'].'">'.(isset($customer_full_name) ? $customer_full_name : $this->l('All customers')).'</td>
					<td class="cell border">'.Tools::displayPrice((float)$specific_price['price'], $current_specific_currency).'</td>
					<td class="cell border">'.$reduction.'</td>
					<td class="cell border">'.$period.'</td>
					<td class="cell border">'.$specific_price['from_quantity'].'</th>
					<td class="cell border"><b>'.Tools::displayPrice(Tools::ps_round((float)($this->_getFinalPrice($specific_price, (float)($obj->price), $taxRate)), 2), $current_specific_currency).'</b></td>
					<td class="cell border">'.(!$rule->id ? '<a name="delete_link" href="'.self::$currentIndex.'&id_product='.(int)(Tools::getValue('id_product')).'&action=deleteSpecificPrice&id_specific_price='.(int)($specific_price['id_specific_price']).'&token='.Tools::getValue('token').'"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /></a>': '').'</td>
				</tr>';
				$i++;
				unset($customer_full_name);
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
		<div class="separation"></div>
		<h4>'.$this->l('Priorities management').'</h4>
		<div class="hint" style="display:block;min-height:0;">
				'.$this->l('Sometimes one customer can fit in multiple specific prices rules. Priorities allow you to define which rule applies to the customer.').'
		</div
	<br /><br />
		<label>'.$this->l('Priorities:').'</label>
		<div class="margin-form">
			<select name="specificPricePriority[]">
				<option value="id_shop"'.($specific_price_priorities[0] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
				<option value="id_currency"'.($specific_price_priorities[0] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
				<option value="id_country"'.($specific_price_priorities[0] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
				<option value="id_group"'.($specific_price_priorities[0] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
			</select>
			&gt;
			<select name="specificPricePriority[]">
				<option value="id_shop"'.($specific_price_priorities[1] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
				<option value="id_currency"'.($specific_price_priorities[1] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
				<option value="id_country"'.($specific_price_priorities[1] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
				<option value="id_group"'.($specific_price_priorities[1] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
			</select>
			&gt;
			<select name="specificPricePriority[]">
				<option value="id_shop"'.($specific_price_priorities[2] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
				<option value="id_currency"'.($specific_price_priorities[2] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
				<option value="id_country"'.($specific_price_priorities[2] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
				<option value="id_group"'.($specific_price_priorities[2] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
			</select>
			&gt;
			<select name="specificPricePriority[]">
				<option value="id_shop"'.($specific_price_priorities[3] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
				<option value="id_currency"'.($specific_price_priorities[3] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
				<option value="id_country"'.($specific_price_priorities[3] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
				<option value="id_group"'.($specific_price_priorities[3] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
			</select>
		</div>

		<div class="margin-form">
			<input type="checkbox" name="specificPricePriorityToAll" id="specificPricePriorityToAll" /> <label class="t" for="specificPricePriorityToAll">'.$this->l('Apply to all products').'</label>
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

	public function initFormCustomization($obj, $languages, $default_language)
	{
		$data = $this->createTemplate($this->tpl_form);

		if ((bool)$obj->id)
		{
			$labels = $obj->getCustomizationFields();

			$has_file_labels = (int)$this->getFieldValue($obj, 'uploadable_files');
			$has_text_labels = (int)$this->getFieldValue($obj, 'text_fields');

			$data->assign(array(
				'obj' => $obj,
				'table' => $this->table,
				'languages' => $languages,
				'has_file_labels' => $has_file_labels,
				'display_file_labels' => $this->_displayLabelFields($obj, $labels, $languages, $default_language, Product::CUSTOMIZE_FILE),
				'has_text_labels' => $has_text_labels,
				'display_text_labels' => $this->_displayLabelFields($obj, $labels, $languages, $default_language, Product::CUSTOMIZE_TEXTFIELD),
				'uploadable_files' => (int)($this->getFieldValue($obj, 'uploadable_files') ? (int)$this->getFieldValue($obj, 'uploadable_files') : '0'),
				'text_fields' => (int)($this->getFieldValue($obj, 'text_fields') ? (int)$this->getFieldValue($obj, 'text_fields') : '0'),
			));
		}
		else
			$this->displayWarning($this->l('You must save this product before adding customization.'));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormAttachments($obj, $languages, $default_language)
	{
		$data = $this->createTemplate($this->tpl_form);

		if ((bool)$obj->id)
		{
			$attachment_name = array();
			$attachment_description = array();
			foreach ($languages as $language)
			{
				$attachment_name[$language['id_lang']] = '';
				$attachment_description[$language['id_lang']] = '';
			}

			$data->assign(array(
				'obj' => $obj,
				'table' => $this->table,
				'languages' => $languages,
				'attach1' => Attachment::getAttachments($this->context->language->id, $obj->id, true),
				'attach2' => Attachment::getAttachments($this->context->language->id, $obj->id, false),
				'default_form_language' => $default_language,
				'attachment_name' => $attachment_name,
				'attachment_description' => $attachment_description,
				'PS_ATTACHMENT_MAXIMUM_SIZE' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')
			));
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
		$this->_applyTaxToEcotax($product);

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
			$images[$k]['src'] = $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id], $product->id.'-'.$image['id_image'], 'small');
		$data->assign('images', $images);
		$data->assign('imagesTypes', ImageType::getImagesTypes('products'));

		$product->tags = Tag::getProductTags($product->id);

		// TinyMCE
		$iso_tiny_mce = $this->context->language->iso_code;
		$iso_tiny_mce = (file_exists(_PS_JS_DIR_.'tiny_mce/langs/'.$iso_tiny_mce.'.js') ? $iso_tiny_mce : 'en');
		$data->assign('ad', dirname($_SERVER['PHP_SELF']));
		$data->assign('iso_tiny_mce', $iso_tiny_mce);
		$category_box = Tools::getValue('categoryBox', array());
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
		$carrier_list = Carrier::getCarriers($this->context->language->id);
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
		if (Tools::getValue('carriers'))
		{
			if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
			{
				if (Tools::getValue('carriers'))
					$product->setCarriers(Tools::getValue('carriers'));
			}
		}
	}

	public function initFormImages($obj)
	{
		$data = $this->createTemplate($this->tpl_form);

		if ((bool)$obj->id)
		{
			$data->assign('product', $this->loadObject());

			$shops = false;
			if (Shop::isFeatureActive())
				$shops = Shop::getShops();
			$data->assign('shops', $shops);

			$count_images = Db::getInstance()->getValue('
				SELECT COUNT(id_product)
				FROM '._DB_PREFIX_.'image
				WHERE id_product = '.(int)$obj->id
			);
			$data->assign('countImages', $count_images);

			$images = Image::getImages($this->context->language->id, $obj->id);
			$data->assign('id_product', (int)Tools::getValue('id_product'));
			$data->assign('id_category_default', (int)$this->_category->id);

			foreach ($images as $k => $image)
				$images[$k] = new Image($image['id_image']);

			$data->assign('images', $images);

			$data->assign('token', $this->token);
			$data->assign('table', $this->table);
			$data->assign('max_image_size', $this->max_image_size / 1000);

			$data->assign('up_filename', strval(Tools::getValue('virtual_product_filename_attribute')));
			$data->assign('currency', $this->context->currency);
		}
		else
			$this->displayWarning($this->l('You must save this product before adding images.'));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormCombinations($obj, $languages, $default_language)
	{
		return $this->initFormAttributes($obj, $languages, $default_language);
	}

	public function initFormAttributes($product)
	{
		if (!Combination::isFeatureActive())
		{
			$this->displayWarning($this->l('This feature has been disabled, you can active this feature at this page:').
				' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
			return;
		}

		$data = $this->createTemplate($this->tpl_form);
		if (Validate::isLoadedObject($product))
		{
			$attribute_js = array();
			$attributes = Attribute::getAttributes($this->context->language->id, true);
			foreach ($attributes as $k => $attribute)
				$attribute_js[$attribute['id_attribute_group']][$attribute['id_attribute']] = $attribute['name'];
			$currency = $this->context->currency;
			$data->assign('attributeJs', $attribute_js);
			$data->assign('attributes_groups', AttributeGroup::getAttributesGroups($this->context->language->id));
			$default_country = new Country((int)Configuration::get('PS_COUNTRY_DEFAULT'));

			$product->productDownload = new ProductDownload();
			$id_product_download = (int)$product->productDownload->getIdFromIdProduct($this->getFieldValue($product, 'id'));
			if (!empty($id_product_download))
				$product->productDownload = new ProductDownload($id_product_download);

		//	$data->assign('productDownload', $product_download);
			$data->assign('currency', $currency);

			$images = Image::getImages($this->context->language->id, $product->id);

			$data->assign('tax_exclude_option', Tax::excludeTaxeOption());
			$data->assign('ps_weight_unit', Configuration::get('PS_WEIGHT_UNIT'));

			$data->assign('ps_use_ecotax', Configuration::get('PS_USE_ECOTAX'));
			$data->assign('field_value_unity', $this->getFieldValue($product, 'unity'));

			$data->assign('reasons', $reasons = StockMvtReason::getStockMvtReasons($this->context->language->id));
			$data->assign('ps_stock_mvt_reason_default', $ps_stock_mvt_reason_default = Configuration::get('PS_STOCK_MVT_REASON_DEFAULT'));
			$data->assign('minimal_quantity', $this->getFieldValue($product, 'minimal_quantity') ? $this->getFieldValue($product, 'minimal_quantity') : 1);
			$data->assign('available_date', ($this->getFieldValue($product, 'available_date') != 0) ? stripslashes(htmlentities(Tools::displayDate($this->getFieldValue($product, 'available_date'), $this->context->language->id))) : '0000-00-00');

			$i = 0;
			$data->assign('imageType', ImageType::getByNameNType('small', 'products'));
			$data->assign('imageWidth', (isset($image_type['width']) ? (int)($image_type['width']) : 64) + 25);
			foreach ($images as $k => $image)
			{
				$images[$k]['obj'] = new Image($image['id_image']);
				++$i;
			}
			$data->assign('images', $images);

			// @todo
			$data->assign('up_filename', strval(Tools::getValue('virtual_product_filename_attribute')));
			$data->assign($this->tpl_form_vars);
			$data->assign(array(
				'list' => $this->renderListAttributes($id_product_download, $product, $currency),
				'product' => $product,
				'id_category' => $product->id_category_default,
				'token_generator' => Tools::getAdminTokenLite('AdminAttributeGenerator')
			));
		}
		else
		{
			$data->assign('product', $product);
			$this->displayWarning($this->l('You must save this product before adding combinations.'));
		}

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function renderListAttributes($id_product_download, $product, $currency)
	{
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
		$this->addRowAction('edit');
		$this->addRowAction('default');
		$this->addRowAction('delete');

		$color_by_default = '#BDE5F8';

		$this->fieldsDisplay = array(
			'attributes' => array('title' => $this->l('Attributes'), 'align' => 'left'),
			'price' => array('title' => $this->l('Impact'), 'type' => 'price', 'align' => 'left', 'width' => 70),
			'weight' => array('title' => $this->l('Weight'), 'align' => 'left', 'width' => 70),
			'reference' => array('title' => $this->l('Reference'), 'align' => 'left', 'width' => 70),
			'ean13' => array('title' => $this->l('EAN13'), 'align' => 'left', 'width' => 70),
			'upc' => array('title' => $this->l('UPC'), 'align' => 'left', 'width' => 70)
		);

		$product_download = new ProductDownload($id_product_download);

		if ($id_product_download && !empty($product_download->display_filename))
		{
			$this->fieldsDisplay['Filename'] = array('title' => $this->l('Filename'), 'align' => 'center', 'width' => 70);
			$this->fieldsDisplay['nb_downloadable'] = array('title' => $this->l('Number of downloads'), 'align' => 'center', 'width' => 70);
			$this->fieldsDisplay['date_expiration'] = array('title' => $this->l('Number of days'), 'align' => 'center', 'width' => 70);
			$this->fieldsDisplay['is_shareable'] = array('title' => $this->l('Share'), 'align' => 'center', 'width' => 70);
		}

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
					$price = Tools::displayPrice($combination['price'], $currency);

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
					$js_list = '';

					/* In order to keep the same attributes order */
					asort($product_attribute['attributes']);

					foreach ($product_attribute['attributes'] as $attribute)
					{
						$list .= htmlspecialchars($attribute[0]).' - '.htmlspecialchars($attribute[1]).', ';
						$js_list .= '\''.htmlspecialchars($attribute[0]).' : '.htmlspecialchars($attribute[1]).'\', \''.$attribute[2].'\', ';
					}
					$list = rtrim($list, ', ');
					$js_list = rtrim($js_list, ', ');
					$comb_array[$id_product_attribute]['image'] = $product_attribute['id_image'] ? new Image($product_attribute['id_image']) : false;
					$comb_array[$id_product_attribute]['available_date'] = $product_attribute['available_date'] != 0 ? date('Y-m-d', strtotime($product_attribute['available_date'])) : '0000-00-00';
					$comb_array[$id_product_attribute]['attributes'] = $list;
					if ($product_attribute['default_on'])
					{
						$this->list_skip_actions['default'][] = $product_attribute['id_product_attribute'];
						$comb_array[$id_product_attribute]['color'] = $color_by_default;
					}

					$id_product_download = $product->productDownload->getIdFromIdAttribute((int)$product->id, (int)$id_product_attribute);
					if ($id_product_download)
						$product->productDownload = new ProductDownload($id_product_download);

					$available_date_attribute = substr($product->productDownload->date_expiration, 0, -9);

					if ($available_date_attribute == '0000-00-00')
						$available_date_attribute = '';

					if ($id_product_download && !empty($product->productDownload->display_filename))
					{
						if ($product->productDownload->is_shareable == 1)
							$is_shareable = $this->l('Yes');
						else
							$is_shareable = $this->l('No');

						$comb_array[$id_product_attribute]['link'] = $product->productDownload->getHtmlLink(false, true);
						$comb_array[$id_product_attribute]['nb_downloadable'] = $product->productDownload->nb_downloadable;
						$comb_array[$id_product_attribute]['is_shareable'] = $is_shareable;
					}

					$exists_file = realpath(_PS_DOWNLOAD_DIR_).'/'.$product->productDownload->filename;

					if ($product->productDownload->id && file_exists($exists_file))
						$filename = $product->productDownload->filename;
					else
						$filename = '';

					//$comb_array[$id_product_attribute]['productDownload'] = $product->productDownload;
					$comb_array[$id_product_attribute]['id_product_download'] = $id_product_download;
					$comb_array[$id_product_attribute]['date_expiration'] = $available_date_attribute;
					$comb_array[$id_product_attribute]['filename'] = $filename;
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

		return $helper->generateList($comb_array, $this->fieldsDisplay);
	}

	public function initFormQuantities($obj, $languages)
	{
		$data = $this->createTemplate($this->tpl_form);

		if ($obj->id)
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
			$group_shop = $this->context->shop->getGroup();

			// if we are in all shops context, it's not possible to manage quantities at this level
			if ($shop_context == Shop::CONTEXT_ALL)
				$show_quantities = false;
			// if we are in group shop context
			else if ($shop_context == Shop::CONTEXT_GROUP)
			{
				// if quantities are not shared between shops of the group, it's not possible to manage them at group level
				if (!$group_shop->share_stock)
					$show_quantities = false;
			}
			// if we are in shop context
			else
			{
				// if quantities are shared between shops of the group, it's not possible to manage them for a given shop
				if ($group_shop->share_stock)
					$show_quantities = false;
			}

			$data->assign('ps_stock_management', Configuration::get('PS_STOCK_MANAGEMENT'));
			$data->assign('has_attribute', $obj->hasAttributes());
			// Check if product has combination, to display the available date only for the product or for each combination
			if (Combination::isFeatureActive())
				$data->assign('countAttributes', (int)Db::getInstance()->getValue('SELECT COUNT(id_product) FROM '._DB_PREFIX_.'product_attribute WHERE id_product = '.(int)$obj->id));

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
				$warehouses = array_unique($warehouses);

				if (empty($warehouses))
					$advanced_stock_management_warning = true;
			}
			if ($advanced_stock_management_warning)
			{
				$this->displayWarning($this->l('If you wish to use the advanced stock management, you have to:'));
				$this->displayWarning('- '.$this->l('associate your products with warehouses'));
				$this->displayWarning('- '.$this->l('associate your warehouses with carriers'));
				$this->displayWarning('- '.$this->l('associate your warehouses with the appropriates shops'));
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
						$pack_quantities[] = Product::getQuantity($item->id) / ($item->pack_quantity !== 0 ? $item->pack_quantity : 1);
				}

				// gets the minimum
				$pack_quantity = $pack_quantities[0];
				foreach ($pack_quantities as $value)
				{
					if ($pack_quantity > $value)
						$pack_quantity = $value;
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
				'token_preferences' => Tools::getAdminTokenLite('AdminPPreferences'),
				'token' => $this->token,
				'languages' => $languages,
			));
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
			$this->displayWarning($this->l('You must save this product before managing suppliers'));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormWarehouses($obj)
	{
		$data = $this->createTemplate($this->tpl_form);

		if ($obj->id)
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
			$this->displayWarning($this->l('You must save this product before managing warehouses'));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function initFormFeatures($obj)
	{
		if (!Feature::isFeatureActive())
		{
			$this->displayWarning($this->l('This feature has been disabled, you can active this feature at this page:').' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
			return;
		}

		$data = $this->createTemplate($this->tpl_form);
		if ($obj->id)
		{
			$features = Feature::getFeatures($this->context->language->id);

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
			$this->displayWarning($this->l('You must save this product before adding features.'));

		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}

	public function ajaxProcessProductQuantity()
	{
		if (!Tools::getValue('actionQty'))
			return Tools::jsonEncode(array('error' => $this->l('Undefined action')));

		$product = new Product((int)Tools::getValue('id_product'));
		switch (Tools::getValue('actionQty'))
		{
			case 'depends_on_stock':
				if (Tools::getValue('value') === false)
					die (Tools::jsonEncode(array('error' =>  $this->l('Undefined value'))));
				if ((int)Tools::getValue('value') != 0 && (int)Tools::getValue('value') != 1)
					die (Tools::jsonEncode(array('error' =>  $this->l('Uncorrect value'))));
				if (!$product->advanced_stock_management && (int)Tools::getValue('value') == 1)
					die (Tools::jsonEncode(array('error' =>  $this->l('Not possible if advanced stock management is not enabled'))));
				if ($product->advanced_stock_management && Pack::isPack($product->id))
					die (Tools::jsonEncode(array('error' =>  $this->l('Not possible if the product is a pack'))));

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
					die (Tools::jsonEncode(array('error' =>  $this->l('Not possible if advanced stock management is not enabled'))));
				if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && Pack::isPack($product->id))
					die (Tools::jsonEncode(array('error' =>  $this->l('Not possible if the product is a pack'))));

				$product->advanced_stock_management = (int)Tools::getValue('value');
				$product->save();
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
							if (!Pack::addItem((int)$product->id, (int)$item_id, (int)$qty))
							return false;
						}
					}
		}
		return true;
	}

	public function getL($key)
	{
		$trad = array(
			'Default category:' => $this->l('Default category:'),
			'Catalog:' => $this->l('Catalog:'),
			'Consider changing the default category.' => $this->l('Consider changing the default category.'),
			'ID' => $this->l('ID'),
			'Name' => $this->l('Name'),
			'Mark all checkbox(es) of categories in which product is to appear' => $this->l('Mark all checkbox(es) of categories in which product is to appear')
		);
		return $trad[$key];
	}

	public function setMedia()
	{
		parent::setMedia();
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
                'ui.slider'
			));

			$this->addJS(array(
				_PS_JS_DIR_.'admin-products.js',
				_PS_JS_DIR_.'attributesBack.js',
				_PS_JS_DIR_.'price.js',
				_PS_JS_DIR_.'tiny_mce/tiny_mce.js',
				_PS_JS_DIR_.'tinymce.inc.js',
				_PS_JS_DIR_.'fileuploader.js',
				_PS_JS_DIR_.'admin-dnd.js',
				_PS_JS_DIR_.'jquery/plugins/treeview/jquery.treeview.js',
				_PS_JS_DIR_.'jquery/plugins/treeview/jquery.treeview.async.js',
				_PS_JS_DIR_.'jquery/plugins/treeview/jquery.treeview.edit.js',
				_PS_JS_DIR_.'admin-categories-tree.js',
				_PS_JS_DIR_.'/jquery/ui/jquery.ui.progressbar.min.js'
			));

			$this->addCSS(array(
                _PS_JS_DIR_.'jquery/plugins/treeview/jquery.treeview.css',
                _PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css',
            ));

		}
	}

	protected function _displayUnavailableProductWarning()
	{
		$content = '<div class="warn">
				<p>
				<span style="float: left">
				'.$this->l('Your product will be saved as draft').'
				</span>
				<span style="float:right"><a href="#" class="button" style="display: block" onclick="submitAddProductAndPreview()" >'.$this->l('Save and preview').'</a></span>
				<input type="hidden" name="fakeSubmitAddProductAndPreview" id="fakeSubmitAddProductAndPreview" />
				<br class="clear" />
				</p>
			</div>';
			$this->tpl_form_vars['warning_unavailable_product'] = $content;
	}
}
