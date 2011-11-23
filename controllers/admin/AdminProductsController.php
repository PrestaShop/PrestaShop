<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 7331 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminProductsControllerCore extends AdminController
{
	protected $max_file_size = 20000000;
	/** @var integer Max image size for upload
	 * As of 1.5 it is recommended to not set a limit to max image size
	 **/
	protected $max_image_size;

	private $_category;

	protected $available_tabs = array(
		'Informations',
		'Images',
		'Prices',
		'Combinations',
		'Features',
		'Customization',
		'Attachments',
		'Quantities',
		'Suppliers',
		'Warehouses',
		'Accounting'
	);

	protected $available_tabs_lang = array ();

	protected $tabs_toolbar_save_buttons = array(
		'Informations' => true,
		'Images' => false,
		'Prices' => false,
		'Combinations' => false,
		'Features' => true,
		'Customization' => false,
		'Attachments' => true,
		'Quantities' => false,
		'Suppliers' => true,
		'Warehouses' => true,
		'Accounting' => true
	);

	public function __construct()
	{
		$this->table = 'product';
		$this->className = 'Product';
		$this->lang = true;
		$this->addRowAction('edit');
		$this->addRowAction('duplicate');
		$this->addRowAction('delete');
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->imageType = 'jpg';
		$this->context = Context::getContext();
		$this->_defaultOrderBy = 'position';

		$this->fieldsDisplay = array(
			'id_product' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 20),
			'image' => array('title' => $this->l('Photo'), 'align' => 'center', 'image' => 'p',
				'width' => 70, 'orderby' => false, 'filter' => false, 'search' => false),
			'name' => array('title' => $this->l('Name'), 'filter_key' => 'b!name'),
			'reference' => array('title' => $this->l('Reference'), 'align' => 'center', 'width' => 80),
			'name_category' => array('title' => $this->l('Category'), 'width' => 100, 'filter_key' => 'cl!name'),
			'price' => array('title' => $this->l('Base price'), 'width' => 70,
				'type' => 'price', 'align' => 'right', 'filter_key' => 'a!price'),
			'price_final' => array('title' => $this->l('Final price'), 'width' => 70,
				'type' => 'price', 'align' => 'right', 'havingFilter' => true, 'orderby' => false),
			'active' => array('title' => $this->l('Displayed'), 'width' => 70, 'active' => 'status',
				'filter_key' => 'a!active', 'align' => 'center', 'type' => 'bool', 'orderby' => false),
			'position' => array('title' => $this->l('Position'), 'width' => 70,'filter_key' => 'cp!position',
				'align' => 'center', 'position' => 'position'),
		);

		// @since 1.5 : translations for tabs
		$this->available_tabs_lang = array (
			'Informations' => $this->l('Informations'),
			'Images' => $this->l('Images'),
			'Prices' => $this->l('Prices'),
			'Combinations' => $this->l('Combinations'),
			'Features' => $this->l('Features'),
			'Customization' => $this->l('Customization'),
			'Attachments' => $this->l('Attachments'),
			'Quantities' => $this->l('Quantities'),
			'Suppliers' => $this->l('Suppliers'),
			'Warehouses' => $this->l('Warehouses'),
			'Accounting' => $this->l('Accounting')
		);

		/* Join categories table */
		if ($id_category = Tools::getvalue('id_category'))
			$this->_category = new Category($id_category);
		else
			$this->_category = new Category();

		$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (a.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang`)
			LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = a.`id_product` AND i.`cover` = 1)
			LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = a.`id_product`)
			LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (a.`id_tax_rules_group` = tr.`id_tax_rules_group`
				AND tr.`id_country` = '.(int)$this->context->country->id.' AND tr.`id_state` = 0)
	   		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)';

		// if no category selected, display all products
		if (Validate::isLoadedObject($this->_category))
			$this->_filter = 'AND cp.`id_category` = '.(int)$this->_category->id;

		$this->_select = 'cl.name `name_category`, cp.`position`, i.`id_image`, (a.`price` * ((100 + (t.`rate`))/100)) AS price_final';

		parent::__construct();
	}
	private function _cleanMetaKeywords($keywords)
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
		if ($_POST['unit_price'] != null)
			$object->unit_price = str_replace(',', '.', $_POST['unit_price']);
		if (array_key_exists('ecotax', $_POST) && $_POST['ecotax'] != null)
			$object->ecotax = str_replace(',', '.', $_POST['ecotax']);
		$object->available_for_order = (int)Tools::isSubmit('available_for_order');
		$object->show_price = $object->available_for_order ? 1 : (int)Tools::isSubmit('show_price');
		$object->on_sale = Tools::isSubmit('on_sale');
		$object->online_only = Tools::isSubmit('online_only');
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

	public function deleteVirtualProduct()
	{
		if (!($id_product_download = ProductDownload::getIdFromIdAttribute((int)Tools::getValue('id_product'), 0)))
			return false;
		$productDownload = new ProductDownload((int)($id_product_download));
		return $productDownload->deleteFile((int)($id_product_download));
	}

	public function deleteVirtualProductAttribute()
	{
		if (!($id_product_download = ProductDownload::getIdFromIdAttribute((int)Tools::getValue('id_product'), (int) Tools::getValue('id_product_attribute'))))
			return false;
		$productDownload = new ProductDownload((int)($id_product_download));
		return $productDownload->deleteFile((int)($id_product_download));
	}

	/**
	 * postProcess handle every checks before saving products information
	 *
	 * @param mixed $token
	 * @return void
	 */
	public function postProcess($token = null)
	{
		/* Add a new product */
		if (Tools::isSubmit('submitAddproduct') || Tools::isSubmit('submitAddproductAndStay') ||  Tools::isSubmit('submitAddProductAndPreview'))
		{
			$id_product = Tools::getValue('id_product');
			if (($id_product && $this->tabAccess['edit'] === '1')
				|| ($this->tabAccess['add'] == 1 && (Tools::isSubmit('submitAddproduct') ||  Tools::isSubmit('submitAddproductAndStay') ) && !$id_product)
			)
				$this->submitAddproduct($token);
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		/* Delete a product in the download folder */
		else if (Tools::getValue('deleteVirtualProduct'))
		{
			if ($this->tabAccess['delete'] === '1')
				$this->deleteVirtualProduct();
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else
		/* Delete a product in the download folder */
		if (Tools::getValue('deleteVirtualProductAttribute'))
		{
			if ($this->tabAccess['delete'] === '1')
				$this->deleteVirtualProductAttribute();
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}

		/* Update attachments */
		else if (Tools::isSubmit('submitAddAttachments'))
		{
			if ($this->tabAccess['add'] === '1')
			{
				$languages = Language::getLanguages(false);
				$is_attachment_name_valid = false;
				foreach ($languages as $language)
				{
					$attachment_name_lang = Tools::getValue('attachment_name_'.(int)($language['id_lang']));
					if (strlen($attachment_name_lang ) > 0)
						$is_attachment_name_valid = true;

					if (!Validate::isGenericName(Tools::getValue('attachment_name_'.(int)($language['id_lang']))))
						$this->_errors[] = Tools::displayError('Invalid Name');
					else if (Tools::strlen(Tools::getValue('attachment_name_'.(int)($language['id_lang']))) > 32)
						$this->_errors[] = Tools::displayError('Name is too long');
					if (!Validate::isCleanHtml(Tools::getValue('attachment_description_'.(int)($language['id_lang']))))
						$this->_errors[] = Tools::displayError('Invalid description');
				}
				if (!$is_attachment_name_valid)
					$this->_errors[] = Tools::displayError('Attachment Name Required');

				if (empty($this->_errors))
				{
					if (isset($_FILES['attachment_file']) && is_uploaded_file($_FILES['attachment_file']['tmp_name']))
					{
						if ($_FILES['attachment_file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024))
							$this->_errors[] = $this->l('File too large, maximum size allowed:').' '.(Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024).' '.$this->l('kb').'. '.$this->l('File size you\'re trying to upload is:').number_format(($_FILES['attachment_file']['size']/1024), 2, '.', '').$this->l('kb');
						else
						{
							do $uniqid = sha1(microtime());	while (file_exists(_PS_DOWNLOAD_DIR_.$uniqid));
							if (!copy($_FILES['attachment_file']['tmp_name'], _PS_DOWNLOAD_DIR_.$uniqid))
								$this->_errors[] = $this->l('File copy failed');
							@unlink($_FILES['attachment_file']['tmp_name']);
						}
					}
					else if ((int)$_FILES['attachment_file']['error'] === 1)
					{
						$max_upload = (int)(ini_get('upload_max_filesize'));
						$max_post = (int)(ini_get('post_max_size'));
						$upload_mb = min($max_upload, $max_post);
						$this->_errors[] = $this->l('the File').' <b>'.$_FILES['attachment_file']['name'].'</b> '.$this->l('exceeds the size allowed by the server, this limit is set to').' <b>'.$upload_mb.$this->l('Mb').'</b>';
					}

					if (empty($this->_errors) && isset($uniqid))
					{
						$attachment = new Attachment();
						foreach ($languages as $language)
						{
							if (isset($_POST['attachment_name_'.(int)($language['id_lang'])]))
								$attachment->name[(int)($language['id_lang'])] = pSQL($_POST['attachment_name_'.(int)($language['id_lang'])]);
							if (isset($_POST['attachment_description_'.(int)($language['id_lang'])]))
								$attachment->description[(int)($language['id_lang'])] = pSQL($_POST['attachment_description_'.(int)($language['id_lang'])]);
						}
						$attachment->file = $uniqid;
						$attachment->mime = $_FILES['attachment_file']['type'];
						$attachment->file_name = pSQL($_FILES['attachment_file']['name']);
						if (empty($attachment->mime) || Tools::strlen($attachment->mime) > 128)
							$this->_errors[] = Tools::displayError('Invalid file extension');
						if (!Validate::isGenericName($attachment->file_name))
							$this->_errors[] = Tools::displayError('Invalid file name');
						if (Tools::strlen($attachment->file_name) > 128)
							$this->_errors[] = Tools::displayError('File name too long');
						if (!sizeof($this->_errors))
						{
							$attachment->add();
							$this->redirect_after = self::$currentIndex.'&id_product='.(int)(Tools::getValue($this->identifier)).'&id_category='.(int)(Tools::getValue('id_category')).'&addproduct&conf=4&tabs=6&token='.($token ? $token : $this->token);
						}
						else
							$this->_errors[] = Tools::displayError('Invalid file');
					}
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		else if (Tools::isSubmit('submitAttachments'))
		{
			if ($this->tabAccess['edit'] === '1')
				if ($id = (int)(Tools::getValue($this->identifier)))
					if (Attachment::attachToProduct($id, Tools::getValue('attachments')))
						$this->redirect_after = self::$currentIndex.'&id_product='.(int)$id.(isset($_POST['id_category']) ? '&id_category='.(int)$_POST['id_category'] : '').'&conf=4&add'.$this->table.'&tabs=6&token='.($token ? $token : $this->token);
		}

		/* Product duplication */
		else if (isset($_GET['duplicate'.$this->table]))
		{
			if ($this->tabAccess['add'] === '1')
			{
				if (Validate::isLoadedObject($product = new Product((int)(Tools::getValue('id_product')))))
				{
					$id_product_old = $product->id;
					unset($product->id);
					unset($product->id_product);
					$product->indexed = 0;
					$product->active = 0;
					if ($product->add()
					&& Category::duplicateProductCategories($id_product_old, $product->id)
					&& ($combinationImages = Product::duplicateAttributes($id_product_old, $product->id)) !== false
					&& GroupReduction::duplicateReduction($id_product_old, $product->id)
					&& Product::duplicateAccessories($id_product_old, $product->id)
					&& Product::duplicateFeatures($id_product_old, $product->id)
					&& Product::duplicateSpecificPrices($id_product_old, $product->id)
					&& Pack::duplicate($id_product_old, $product->id)
					&& Product::duplicateCustomizationFields($id_product_old, $product->id)
					&& Product::duplicateTags($id_product_old, $product->id)
					&& Product::duplicateDownload($id_product_old, $product->id)
					&& $product->duplicateShops($id_product_old))
					{
						if ($product->hasAttributes())
							Product::updateDefaultAttribute($product->id);

						if (!Tools::getValue('noimage') && !Image::duplicateProductImages($id_product_old, $product->id, $combinationImages))
							$this->_errors[] = Tools::displayError('An error occurred while copying images.');
						else
						{
							Hook::exec('addProduct', array('product' => $product));
							Search::indexation(false, $product->id);
							$this->redirect_after = self::$currentIndex.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&conf=19&token='.($token ? $token : $this->token);
						}
					}
					else
						$this->_errors[] = Tools::displayError('An error occurred while creating object.');
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		/* Change object statuts (active, inactive) */
		else if (isset($_GET['status']) && Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					// @todo : redirect to the previous page instead of always product list
					if ($object->toggleStatus())
						$this->redirect_after = self::$currentIndex.'&conf=5'.((($id_category = (!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1')) && Tools::getValue('id_product')) ? '&id_category='.$id_category : '').'&token='.($token ? $token : $this->token);
					else
						$this->_errors[] = Tools::displayError('An error occurred while updating status.');
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		/* Delete object */
		else if (isset($_GET['delete'.$this->table]))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()) && isset($this->fieldImageSettings))
				{
					// check if request at least one object with noZeroObject
					if (isset($object->noZeroObject) && sizeof($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1)
						$this->_errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
					else
					{
						$id_category = Tools::getValue('id_category');
						$category_url = empty($id_category) ? '' : '&id_category='.$id_category;

						if ($this->deleted)
						{
							$object->deleteImages();
							$object->deleted = 1;
							if ($object->update())
								$this->redirect_after = self::$currentIndex.'&conf=1&token='.($token ? $token : $this->token).$category_url;
						}
						else if ($object->delete())
							$this->redirect_after = self::$currentIndex.'&conf=1&token='.($token ? $token : $this->token).$category_url;
						$this->_errors[] = Tools::displayError('An error occurred during deletion.');
					}
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while deleting object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}

		/* Delete multiple objects */
		else if (Tools::getValue('submitDel'.$this->table))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (isset($_POST[$this->table.'Box']))
				{
					$object = new $this->className();

					if (isset($object->noZeroObject) &&
						// Check if all object will be deleted
						(sizeof(call_user_func(array($this->className, $object->noZeroObject))) <= 1 || sizeof($_POST[$this->table.'Box']) == sizeof(call_user_func(array($this->className, $object->noZeroObject)))))
						$this->_errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
					else
					{
						$result = true;
						if ($this->deleted)
						{
							foreach (Tools::getValue($this->table.'Box') as $id)
							{
								$toDelete = new $this->className($id);
								$toDelete->deleted = 1;
								$result = $result && $toDelete->update();
							}
						}
						else
							$result = $object->deleteSelection(Tools::getValue($this->table.'Box'));

						if ($result)
						{
							$id_category = Tools::getValue('id_category');
							$category_url = empty($id_category) ? '' : '&id_category='.$id_category;

							$this->redirect_after = self::$currentIndex.'&conf=2&token='.$token.$category_url;
						}
						$this->_errors[] = Tools::displayError('An error occurred while deleting selection.');
					}
				}
				else
					$this->_errors[] = Tools::displayError('You must select at least one element to delete.');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}

		/* Product images management */
		else if (($id_image = (int)(Tools::getValue('id_image'))) && Validate::isUnsignedId($id_image) && Validate::isLoadedObject($image = new Image($id_image)))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				/* Update product image/legend */
				// @todo : move in processEditProductImage
				if (isset($_GET['editImage']))
				{
					if ($image->cover)
						$_POST['cover'] = 1;
					$languages = Language::getLanguages(false);
					foreach ($languages as $language)
						if (isset($image->legend[$language['id_lang']]))
							$_POST['legend_'.$language['id_lang']] = $image->legend[$language['id_lang']];
					$_POST['id_image'] = $image->id;
				}

				/* Choose product cover image */
				else if (isset($_GET['coverImage']))
				{
					Image::deleteCover($image->id_product);
					$image->cover = 1;
					if (!$image->update())
						$this->_errors[] = Tools::displayError('Cannot change the product cover');
					else
					{
						$productId = (int)(Tools::getValue('id_product'));
						@unlink(_PS_TMP_IMG_DIR_.'/product_'.$productId.'.jpg');
						@unlink(_PS_TMP_IMG_DIR_.'/product_mini_'.$productId.'.jpg');
						$this->redirect_after = self::$currentIndex.'&id_product='.$image->id_product.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&addproduct&tabs=1'.'&token='.($token ? $token : $this->token);
					}
				}

				/* Choose product image position */
				else if (isset($_GET['imgPosition']) && isset($_GET['imgDirection']))
				{
					$image->updatePosition(Tools::getValue('imgDirection'), Tools::getValue('imgPosition'));
					$this->redirect_after = self::$currentIndex.'&id_product='.$image->id_product.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&add'.$this->table.'&tabs=1&token='.($token ? $token : $this->token);
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		/* Product attributes management */
		else if (Tools::isSubmit('submitProductAttribute'))
		{
			if (!Combination::isFeatureActive())
				return;

			$is_virtual = (int)Tools::getValue('is_virtual');

			if (Validate::isLoadedObject($product = new Product((int)(Tools::getValue('id_product')))))
			{
				if (!isset($_POST['attribute_price']) || $_POST['attribute_price'] == null)
					$this->_errors[] = Tools::displayError('Attribute price required.');
				if (!isset($_POST['attribute_combinaison_list']) || !sizeof($_POST['attribute_combinaison_list']))
					$this->_errors[] = Tools::displayError('You must add at least one attribute.');

				if (!sizeof($this->_errors))
				{
					if (!isset($_POST['attribute_wholesale_price'])) $_POST['attribute_wholesale_price'] = 0;
					if (!isset($_POST['attribute_price_impact'])) $_POST['attribute_price_impact'] = 0;
					if (!isset($_POST['attribute_weight_impact'])) $_POST['attribute_weight_impact'] = 0;
					if (!isset($_POST['attribute_ecotax'])) $_POST['attribute_ecotax'] = 0;
					if (Tools::getValue('attribute_default'))
						$product->deleteDefaultAttributes();
					// Change existing one
					if ($id_product_attribute = (int)(Tools::getValue('id_product_attribute')))
					{
						if ($this->tabAccess['edit'] === '1')
						{
							if ($product->productAttributeExists($_POST['attribute_combinaison_list'], $id_product_attribute))
								$this->_errors[] = Tools::displayError('This attribute already exists.');
							else
							{
								if (Validate::isDateFormat(Tools::getValue('available_date')))
								{
									$product->updateAttribute($id_product_attribute,
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
										Tools::getValue('available_date'));

									if ($id_reason = (int)Tools::getValue('id_mvt_reason') && (int)Tools::getValue('attribute_mvt_quantity') > 0 && $id_reason > 0)
									{
										if (!$product->addStockMvt(Tools::getValue('attribute_mvt_quantity'), $id_reason, $id_product_attribute, null, $this->context->employee->id))
											$this->_errors[] = Tools::displayError('An error occurred while updating qty.');
									}
									Hook::exec('updateProductAttribute', array('id_product_attribute' => (int)$id_product_attribute));
									$this->updateDownloadProduct($product, 1, $id_product_attribute);
								}
								else
								{
									$this->_errors[] = Tools::displayError('Invalid date format.');
								}
							}
						}
						else
							$this->_errors[] = Tools::displayError('You do not have permission to add here.');
					}
					// Add new
					else
					{
						if ($this->tabAccess['add'] === '1')
						{
							if ($product->productAttributeExists($_POST['attribute_combinaison_list']))
								$this->_errors[] = Tools::displayError('This combination already exists.');
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
							$this->_errors[] = Tools::displayError('You do not have permission to').'<hr>'.Tools::displayError('Edit here.');
					}
					if (!sizeof($this->_errors))
					{
						$product->addAttributeCombinaison($id_product_attribute, Tools::getValue('attribute_combinaison_list'));
						$product->checkDefaultAttributes();
					}
					if (!sizeof($this->_errors))
					{
						if (!$product->cache_default_attribute)
							Product::updateDefaultAttribute($product->id);

						if (!empty($is_virtual))
							Product::updateIsVirtual($product->id);

						$this->redirect_after = self::$currentIndex.'&id_product='.$product->id.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&add'.$this->table.'&tabs=3&token='.($token ? $token : $this->token);
					}
				}
			}
		}
		else if (Tools::isSubmit('deleteProductAttribute'))
		{
			if (!Combination::isFeatureActive())
				return;
			if ($this->tabAccess['delete'] === '1')
			{
				if (($id_product = (int)(Tools::getValue('id_product'))) && Validate::isUnsignedId($id_product) && Validate::isLoadedObject($product = new Product($id_product)))
				{
					$product->deleteAttributeCombinaison(Tools::getValue('id_product_attribute'));

					$id_product_download = ProductDownload::getIdFromIdAttribute((int) $id_product, (int) Tools::getValue('id_product_attribute'));
					if ($id_product_download)
					{
						$productDownload = new ProductDownload((int) $id_product_download);
						$this->deleteDownloadProduct((int) $id_product_download);
						$productDownload->deleteFile();
					}
					$product->checkDefaultAttributes();
					$product->updateQuantityProductWithAttributeQuantity();
					if (!$product->hasAttributes())
					{
						$product->cache_default_attribute = 0;
						$product->update();
					}
					else
						Product::updateDefaultAttribute($id_product);

					$this->redirect_after = self::$currentIndex.'&add'.$this->table.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&tabs=3&id_product='.$product->id.'&token='.($token ? $token : $this->token);
				}
				else
					$this->_errors[] = Tools::displayError('Cannot delete attribute');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else if (Tools::isSubmit('deleteAllProductAttributes'))
		{
			if (!Combination::isFeatureActive())
				return;
			if ($this->tabAccess['delete'] === '1')
			{
				if (($id_product = (int)(Tools::getValue('id_product'))) && Validate::isUnsignedId($id_product) && Validate::isLoadedObject($product = new Product($id_product)))
				{
					$product->deleteProductAttributes();
					$product->updateQuantityProductWithAttributeQuantity();
					if ($product->cache_default_attribute)
					{
						$product->cache_default_attribute = 0;
						$product->update();
					}
					$this->redirect_after = self::$currentIndex.'&add'.$this->table.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&tabs=3&id_product='.$product->id.'&token='.($token ? $token : $this->token);
				}
				else
					$this->_errors[] = Tools::displayError('Cannot delete attributes');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else if (Tools::isSubmit('defaultProductAttribute'))
		{
			if (!Combination::isFeatureActive())
				return;
			if (Validate::isLoadedObject($product = new Product((int)(Tools::getValue('id_product')))))
			{
				$product->deleteDefaultAttributes();
				$product->setDefaultAttribute((int)(Tools::getValue('id_product_attribute')));
				$this->redirect_after = self::$currentIndex.'&add'.$this->table.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&tabs=3&id_product='.$product->id.'&token='.($token ? $token : $this->token);
			}
			else
				$this->_errors[] = Tools::displayError('Cannot make default attribute');
		}

		/* Product features management */
		else if (Tools::isSubmit('submitFeatures') || Tools::isSubmit('submitFeaturesAndStay'))
		{
			if (!Feature::isFeatureActive())
				return;
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($product = new Product((int)(Tools::getValue('id_product')))))
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
					if (!sizeof($this->_errors))
						$this->redirect_after = self::$currentIndex.'&id_product='.(int)$product->id.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&add'.$this->table.'&action=Features&tabs=4&conf=4&token='.($token ? $token : $this->token);
				}
				else
					$this->_errors[] = Tools::displayError('Product must be created before adding features.');
			}
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		/* Product specific prices management */
		else if (Tools::isSubmit('submitPricesModification'))
		{
			$_POST['tabs'] = 5;
			if ($this->tabAccess['edit'] === '1')
			{
				$id_specific_prices = Tools::getValue('spm_id_specific_price');
				$id_shops = Tools::getValue('spm_id_shop');
				$id_currencies = Tools::getValue('spm_id_currency');
				$id_countries = Tools::getValue('spm_id_country');
				$id_groups = Tools::getValue('spm_id_group');
				$prices = Tools::getValue('spm_price');
				$from_quantities = Tools::getValue('spm_from_quantity');
				$reductions = Tools::getValue('spm_reduction');
				$reduction_types = Tools::getValue('spm_reduction_type');
				$froms = Tools::getValue('spm_from');
				$tos = Tools::getValue('spm_to');

				foreach ($id_specific_prices as $key => $id_specific_price)
					if ($this->_validateSpecificPrice($id_shops[$key], $id_currencies[$key], $id_countries[$key], $id_groups[$key], $prices[$key], $from_quantities[$key], $reductions[$key], $reduction_types[$key], $froms[$key], $tos[$key]))
					{
						$specificPrice = new SpecificPrice((int)($id_specific_price));
						$specificPrice->id_shop = (int)$id_shops[$key];
						$specificPrice->id_currency = (int)($id_currencies[$key]);
						$specificPrice->id_country = (int)($id_countries[$key]);
						$specificPrice->id_group = (int)($id_groups[$key]);
						$specificPrice->price = (float)($prices[$key]);
						$specificPrice->from_quantity = (int)($from_quantities[$key]);
						$specificPrice->reduction = (float)($reduction_types[$key] == 'percentage' ? ($reductions[$key] / 100) : $reductions[$key]);
						$specificPrice->reduction_type = !$reductions[$key] ? 'amount' : $reduction_types[$key];
						$specificPrice->from = !$froms[$key] ? '0000-00-00 00:00:00' : $froms[$key];
						$specificPrice->to = !$tos[$key] ? '0000-00-00 00:00:00' : $tos[$key];
						if (!$specificPrice->update())
							$this->_errors = Tools::displayError('An error occurred while updating the specific price.');
					}
				if (!sizeof($this->_errors))
					$this->redirect_after = self::$currentIndex.'&id_product='.(int)(Tools::getValue('id_product')).'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&update'.$this->table.'&tabs=2&token='.($token ? $token : $this->token);
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		else if (Tools::isSubmit('submitPriceAddition'))
		{
			if ($this->tabAccess['add'] === '1')
			{
				$id_product = (int)(Tools::getValue('id_product'));
				$id_shop = Tools::getValue('sp_id_shop');
				$id_currency = Tools::getValue('sp_id_currency');
				$id_country = Tools::getValue('sp_id_country');
				$id_group = Tools::getValue('sp_id_group');
				$price = Tools::getValue('sp_price');
				$from_quantity = Tools::getValue('sp_from_quantity');
				$reduction = (float)(Tools::getValue('sp_reduction'));
				$reduction_type = !$reduction ? 'amount' : Tools::getValue('sp_reduction_type');
				$from = Tools::getValue('sp_from');
				$to = Tools::getValue('sp_to');
				if ($this->_validateSpecificPrice($id_shop, $id_currency, $id_country, $id_group, $price, $from_quantity, $reduction, $reduction_type, $from, $to))
				{
					$specificPrice = new SpecificPrice();
					$specificPrice->id_product = $id_product;
					$specificPrice->id_product_attribute = (int)Tools::getValue('id_product_attribute');
					$specificPrice->id_shop = (int)$id_shop;
					$specificPrice->id_currency = (int)($id_currency);
					$specificPrice->id_country = (int)($id_country);
					$specificPrice->id_group = (int)($id_group);
					$specificPrice->price = (float)($price);
					$specificPrice->from_quantity = (int)($from_quantity);
					$specificPrice->reduction = (float)($reduction_type == 'percentage' ? $reduction / 100 : $reduction);
					$specificPrice->reduction_type = $reduction_type;
					$specificPrice->from = !$from ? '0000-00-00 00:00:00' : $from;
					$specificPrice->to = !$to ? '0000-00-00 00:00:00' : $to;
					if (!$specificPrice->add())
						$this->_errors = Tools::displayError('An error occurred while updating the specific price.');
					else
						$this->redirect_after = self::$currentIndex.(Tools::getValue('id_category') ? '&id_category='.Tools::getValue('id_category') : '').'&id_product='.$id_product.'&add'.$this->table.'&tabs=2&conf=3&token='.($token ? $token : $this->token);
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		else if (Tools::isSubmit('deleteSpecificPrice'))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (!($obj = $this->loadObject()))
					return;
				if (!$id_specific_price = Tools::getValue('id_specific_price') || !Validate::isUnsignedId($id_specific_price))
					$this->_errors[] = Tools::displayError('Invalid specific price ID');
				else
				{
					$specificPrice = new SpecificPrice((int)($id_specific_price));
					if (!$specificPrice->delete())
						$this->_errors[] = Tools::displayError('An error occurred while deleting the specific price');
					else
						$this->redirect_after = self::$currentIndex.(Tools::getValue('id_category') ? '&id_category='.Tools::getValue('id_category') : '').'&id_product='.$obj->id.'&add'.$this->table.'&tabs=2&conf=1&token='.($token ? $token : $this->token);
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else if (Tools::isSubmit('submitSpecificPricePriorities'))
		{
			if (!($obj = $this->loadObject()))
				return;
			if (!$priorities = Tools::getValue('specificPricePriority'))
				$this->_errors[] = Tools::displayError('Please specify priorities');
			else if (Tools::isSubmit('specificPricePriorityToAll'))
			{
				if (!SpecificPrice::setPriorities($priorities))
					$this->_errors[] = Tools::displayError('An error occurred while updating priorities.');
				else
					$this->redirect_after = self::$currentIndex.'&id_product='.$obj->id.'&add'.$this->table.'&tabs=2&conf=4&token='.($token ? $token : $this->token);
			}
			else if (!SpecificPrice::setSpecificPriority((int)($obj->id), $priorities))
				$this->_errors[] = Tools::displayError('An error occurred while setting priorities.');
			else
				$this->redirect_after = self::$currentIndex.(Tools::getValue('id_category') ? '&id_category='.Tools::getValue('id_category') : '').'&id_product='.$obj->id.'&add'.$this->table.'&tabs=2&conf=4&token='.($token ? $token : $this->token);
		}
		/* Customization management */
		else if (Tools::isSubmit('submitCustomizationConfiguration'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($product = new Product((int)(Tools::getValue('id_product')))))
				{
					if (!$product->createLabels((int)($_POST['uploadable_files']) - (int)($product->uploadable_files), (int)($_POST['text_fields']) - (int)($product->text_fields)))
						$this->_errors[] = Tools::displayError('An error occurred while creating customization fields.');
					if (!sizeof($this->_errors) && !$product->updateLabels())
						$this->_errors[] = Tools::displayError('An error occurred while updating customization.');
					$product->uploadable_files = (int)($_POST['uploadable_files']);
					$product->text_fields = (int)($_POST['text_fields']);
					$product->customizable = ((int)($_POST['uploadable_files']) > 0 || (int)($_POST['text_fields']) > 0) ? 1 : 0;
					if (!sizeof($this->_errors) && !$product->update())
						$this->_errors[] = Tools::displayError('An error occurred while updating customization configuration.');
					if (!sizeof($this->_errors))
						$this->redirect_after = self::$currentIndex.'&id_product='.$product->id.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&add'.$this->table.'&tabs=5&token='.($token ? $token : $this->token);
				}
				else
					$this->_errors[] = Tools::displayError('Product must be created before adding customization possibilities.');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else if (Tools::isSubmit('submitProductCustomization'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($product = new Product((int)(Tools::getValue('id_product')))))
				{
					foreach ($_POST as $field => $value)
						if (strncmp($field, 'label_', 6) == 0 && !Validate::isLabel($value))
							$this->_errors[] = Tools::displayError('Label fields are invalid');
					if (!sizeof($this->_errors) && !$product->updateLabels())
						$this->_errors[] = Tools::displayError('An error occurred while updating customization.');
					if (!sizeof($this->_errors))
						$this->redirect_after = self::$currentIndex.'&id_product='.$product->id.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&add'.$this->table.'&tabs=5&token='.($token ? $token : $this->token);
				}
				else
					$this->_errors[] = Tools::displayError('Product must be created before adding customization possibilities.');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else if (isset($_GET['position']))
		{
			if ($this->tabAccess['edit'] !== '1')
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
			else if (!Validate::isLoadedObject($object = $this->loadObject()))
				$this->_errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			if (!$object->updatePosition((int)(Tools::getValue('way')), (int)(Tools::getValue('position'))))
				$this->_errors[] = Tools::displayError('Failed to update the position.');
			else
				$this->redirect_after = self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.(($id_category = (!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1')) ? ('&id_category='.$id_category) : '').'&token='.Tools::getAdminTokenLite('AdminProducts');
		}
		else if (Tools::isSubmit('submitAccounting') || Tools::isSubmit('submitAccountingAndStay'))
			$this->postProcessFormAccounting();
		//Manage suppliers
		else if (Tools::isSubmit('submitSuppliers') || Tools::isSubmit('submitSuppliersAndStay'))
			$this->postProcessFormSuppliers();
		//Manage warehouses
		else if (Tools::isSubmit('submitWarehouses') || Tools::isSubmit('submitWarehousesAndStay'))
		{
			$this->postProcessFormWarehouses();
		}
		parent::postProcess(true);
	}

	// @todo rename to processaddproductimage
	public function ajaxProcessAddImage()
	{
		self::$currentIndex = 'index.php?tab=AdminProducts';
		$allowedExtensions = array("jpeg", "gif", "png", "jpg");
		// max file size in bytes
		$sizeLimit = $this->max_file_size;
		$uploader = new FileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload();
		if (isset($result['success']))
		{
			$obj = new Image($result['success']['id_image']);
			$json = array(
				'status' => 'ok',
				'id'=>$obj->id,
				'path' => $obj->getExistingImgPath(),
				'position' => $obj->position,
				'cover' => $obj->cover,
			);
			die(Tools::jsonEncode($json));
		}
		else
			die(Tools::jsonEncode($result));
	}

	public function ajaxPreProcess()
	{
		$this->action = Tools::getValue('action');
	}

	public function ajaxProcessUpdateProductImageShopAsso()
	{
		$this->json = true;
		if (($id_image = $_GET['id_image']) && ($id_shop = (int)$_GET['id_shop']))
			if (Tools::getValue('active') == "true")
				$res = Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'image_shop (`id_image`, `id_shop`) VALUES('.(int)$id_image.', '.(int)$id_shop.')');
			else
				$res= Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'image_shop WHERE `id_image`='.(int)$id_image.' && `id_shop`='.(int)$id_shop);

		if ($res)
			$this->confirmations[] = $this->_conf[27];
		else
			$this->_errors[] = Tools::displayError('Error on picture shop association');
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
			$this->_errors[] = Tools::displayError('Error on moving picture');
		$this->status = 'ok';
	}

	public function ajaxProcessUpdateCover()
	{
		$this->json = true;
		Image::deleteCover((int)$_GET['id_product']);
		$img = new Image((int)$_GET['id_image']);
		$img->cover = 1;
		if ($img->update())
			$this->confirmations[] = $this->_conf[26];
		else
			$this->_errors[] = Tools::displayError('Error on moving picture');
	}

	public function ajaxProcessDeleteProductImage()
	{
		/* Delete product image */
		if (isset($_GET['deleteProductImage']) || $this->action == 'deleteProductImage')
		{
			$image = new Image((int)Tools::getValue('id_image'));
			$image->delete();
			if (!Image::getCover($image->id_product))
			{
				$first_img = Db::getInstance()->getRow('
				SELECT `id_image` FROM `'._DB_PREFIX_.'image`
				WHERE `id_product` = '.(int)($image->id_product));
				Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'image`
				SET `cover` = 1
				WHERE `id_image` = '.(int)($first_img['id_image']));
			}
			@unlink(_PS_TMP_IMG_DIR_.'/product_'.$image->id_product.'.jpg');
			@unlink(_PS_TMP_IMG_DIR_.'/product_mini_'.$image->id_product.'.jpg');
			if (!$this->ajax)
				$this->redirect_after = $currentIndex.'&id_product='.$image->id_product.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&add'.$this->table.'&tabs=1'.'&token='.($token ? $token : $this->token);
		else
			$this->content = '{"status":"ok"}';
		}
	}

	protected function _validateSpecificPrice($id_shop, $id_currency, $id_country, $id_group, $price, $from_quantity, $reduction, $reduction_type, $from, $to)
	{
		if (!Validate::isUnsignedId($id_shop) || !Validate::isUnsignedId($id_currency) || !Validate::isUnsignedId($id_country) || !Validate::isUnsignedId($id_group))
			$this->_errors[] = Tools::displayError('Wrong ID\'s');
		else if ((empty($price) && empty($reduction)) || (!empty($price) && !Validate::isPrice($price)) || (!empty($reduction) && !Validate::isPrice($reduction)))
			$this->_errors[] = Tools::displayError('Invalid price/reduction amount');
		else if (!Validate::isUnsignedInt($from_quantity))
			$this->_errors[] = Tools::displayError('Invalid quantity');
		else if ($reduction && !Validate::isReductionType($reduction_type))
			$this->_errors[] = Tools::displayError('Please select a reduction type (amount or percentage)');
		else if ($from && $to && (!Validate::isDateFormat($from) || !Validate::isDateFormat($to)))
			$this->_errors[] = Tools::displayError('Wrong from/to date');
		else
			return true;
		return false;
	}

	// Checking customs feature
	private function checkFeatures($languages, $feature_id)
	{
		$rules = call_user_func(array('FeatureValue', 'getValidationRules'), 'FeatureValue');
		$feature = Feature::getFeature(Configuration::get('PS_LANG_DEFAULT'), $feature_id);
		$val = 0;
		foreach ($languages as $language)
			if ($val = Tools::getValue('custom_'.$feature_id.'_'.$language['id_lang']))
			{
				$currentLanguage = new Language($language['id_lang']);
				if (Tools::strlen($val) > $rules['sizeLang']['value'])
					$this->_errors[] = Tools::displayError('name for feature').' <b>'.$feature['name'].'</b> '.Tools::displayError('is too long in').' '.$currentLanguage->name;
				else if (!call_user_func(array('Validate', $rules['validateLang']['value']), $val))
					$this->_errors[] = Tools::displayError('Valid name required for feature.').' <b>'.$feature['name'].'</b> '.Tools::displayError('in').' '.$currentLanguage->name;
				if (sizeof($this->_errors))
					return (0);
				// Getting default language
				if ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'))
					return ($val);
			}
		return (0);
	}

	/**
	 * Add or update a product image
	 *
	 * @param object $product Product object to add image
	 */
	public function addProductImage($product, $method = 'auto')
	{
		/* Updating an existing product image */
		if ($id_image = ((int)(Tools::getValue('id_image'))))
		{
			$image = new Image($id_image);
			if (!Validate::isLoadedObject($image))
				$this->_errors[] = Tools::displayError('An error occurred while loading object image.');
			else
			{
				if (($cover = Tools::getValue('cover')) == 1)
					Image::deleteCover($product->id);
				$image->cover = $cover;
				$this->validateRules('Image');
				$this->copyFromPost($image, 'image');
				if (sizeof($this->_errors) || !$image->update())
					$this->_errors[] = Tools::displayError('An error occurred while updating image.');
				else if (isset($_FILES['image_product']['tmp_name']) && $_FILES['image_product']['tmp_name'] != null)
					$this->copyImage($product->id, $image->id, $method);
			}
		}
		if (isset($image) && Validate::isLoadedObject($image) && !file_exists(_PS_PROD_IMG_DIR_.$image->getExistingImgPath().'.'.$image->image_format))
			$image->delete();
		if (sizeof($this->_errors))
			return false;
		@unlink(_PS_TMP_IMG_DIR_.'/product_'.$product->id.'.jpg');
		@unlink(_PS_TMP_IMG_DIR_.'/product_mini_'.$product->id.'.jpg');
		return ((isset($id_image) && is_int($id_image) && $id_image) ? $id_image : true);
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
		if ($error = checkImage($_FILES['image_product']))
			$this->_errors[] = $error;
		else
		{
			$image = new Image($id_image);

			if (!$new_path = $image->getPathForCreation())
				$this->_errors[] = Tools::displayError('An error occurred during new folder creation');
			if (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') || !move_uploaded_file($_FILES['image_product']['tmp_name'], $tmpName))
				$this->_errors[] = Tools::displayError('An error occurred during the image upload');
			else if (!imageResize($tmpName, $new_path.'.'.$image->image_format))
				$this->_errors[] = Tools::displayError('An error occurred while copying image.');
			else if ($method == 'auto')
			{
				$imagesTypes = ImageType::getImagesTypes('products');
				foreach ($imagesTypes as $k => $imageType)
					if (!imageResize($tmpName, $new_path.'-'.stripslashes($imageType['name']).'.'.$image->image_format, $imageType['width'], $imageType['height'], $image->image_format))
						$this->_errors[] = Tools::displayError('An error occurred while copying image:').' '.stripslashes($imageType['name']);
			}

			@unlink($tmpName);
			Hook::exec('watermark', array('id_image' => $id_image, 'id_product' => $id_product));
		}
	}

	/**
	 * Add or update a product
	 */
	public function submitAddProduct($token = null)
	{
		$className = 'Product';
		$rules = call_user_func(array($this->className, 'getValidationRules'), $this->className);
		$defaultLanguage = new Language((int)(Configuration::get('PS_LANG_DEFAULT')));
		$languages = Language::getLanguages(false);

		/* Check required fields */
		foreach ($rules['required'] as $field)
			if (($value = Tools::getValue($field)) == false && $value != '0')
			{
				if (Tools::getValue('id_'.$this->table) && $field == 'passwd')
					continue;
				$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $field, $className).'</b> '.$this->l('is required');
			}

		/* Check multilingual required fields */
		foreach ($rules['requiredLang'] as $fieldLang)
			if (!Tools::getValue($fieldLang.'_'.$defaultLanguage->id))
				$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $fieldLang, $className).'</b> '.$this->l('is required at least in').' '.$defaultLanguage->name;

		/* Check fields sizes */
		foreach ($rules['size'] as $field => $maxLength)
			if ($value = Tools::getValue($field) && Tools::strlen($value) > $maxLength)
				$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $field, $className).'</b> '.$this->l('is too long').' ('.$maxLength.' '.$this->l('chars max').')';

		if (isset($_POST['description_short']))
		{
			$saveShort = $_POST['description_short'];
			$_POST['description_short'] = strip_tags($_POST['description_short']);
		}

		/* Check description short size without html */
		$limit = (int)Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
		if ($limit <= 0) $limit = 400;
		foreach ($languages as $language)
			if ($value = Tools::getValue('description_short_'.$language['id_lang']))
				if (Tools::strlen(strip_tags($value)) > $limit)
					$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), 'description_short').' ('.$language['name'].')</b> '.$this->l('is too long').' : '.$limit.' '.$this->l('chars max').' ('.$this->l('count now').' '.Tools::strlen(strip_tags($value)).')';
		/* Check multilingual fields sizes */
		foreach ($rules['sizeLang'] as $fieldLang => $maxLength)
			foreach ($languages as $language)
				if ($value = Tools::getValue($fieldLang.'_'.$language['id_lang']) && Tools::strlen($value) > $maxLength)
					$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $fieldLang, $className).' ('.$language['name'].')</b> '.$this->l('is too long').' ('.$maxLength.' '.$this->l('chars max').')';
		if (isset($_POST['description_short']))
			$_POST['description_short'] = $saveShort;

		/* Check fields validity */
		foreach ($rules['validate'] as $field => $function)
			if ($value = Tools::getValue($field))
				if (!Validate::$function($value))
					$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $field, $className).'</b> '.$this->l('is invalid');

		/* Check multilingual fields validity */
		foreach ($rules['validateLang'] as $fieldLang => $function)
			foreach ($languages as $language)
				if ($value = Tools::getValue($fieldLang.'_'.$language['id_lang']))
					if (!Validate::$function($value))
						$this->_errors[] = $this->l('the field').' <b>'.call_user_func(array($className, 'displayFieldName'), $fieldLang, $className).' ('.$language['name'].')</b> '.$this->l('is invalid');

		/* Categories */
		$productCats = '';
		if (!Tools::isSubmit('categoryBox') || !sizeof(Tools::getValue('categoryBox')))
			$this->_errors[] = $this->l('product must be in at least one Category');

		if (!is_array(Tools::getValue('categoryBox')) || !in_array(Tools::getValue('id_category_default'), Tools::getValue('categoryBox')))
			$this->_errors[] = $this->l('product must be in the default category');

		/* Tags */
		foreach ($languages as $language)
			if ($value = Tools::getValue('tags_'.$language['id_lang']))
				if (!Validate::isTagsList($value))
					$this->_errors[] = $this->l('Tags list').' ('.$language['name'].') '.$this->l('is invalid');

		if (!sizeof($this->_errors))
		{
			$id = (int)Tools::getValue('id_'.$this->table);
			$tagError = true;

			/* Update an existing product */
			if (isset($id) && !empty($id))
			{
				$object = new $this->className($id);
				if (Validate::isLoadedObject($object))
				{
					$this->_removeTaxFromEcotax();
					$this->copyFromPost($object, $this->table);
					if ($object->update())
					{
						$this->addCarriers();
						if ($id_reason = (int)Tools::getValue('id_mvt_reason') && Tools::getValue('mvt_quantity') > 0 && $id_reason > 0)
						{
							if (!$object->addStockMvt(Tools::getValue('mvt_quantity'), $id_reason, null, null, $this->context->employee->id))
								$this->_errors[] = Tools::displayError('An error occurred while updating qty.');
						}
						$this->updateAccessories($object);
						$this->updateDownloadProduct($object, 1);
						$this->updateAssoShop((int)$object->id);

						if (!$this->updatePackItems($object))
							$this->_errors[] = Tools::displayError('An error occurred while adding products to the pack.');
						else if (!$object->updateCategories($_POST['categoryBox'], true))
							$this->_errors[] = Tools::displayError('An error occurred while linking object.').' <b>'.$this->table.'</b> '.Tools::displayError('To categories');
						else if (!$this->updateTags($languages, $object))
							$this->_errors[] = Tools::displayError('An error occurred while adding tags.');
						else if (Tools::getValue('id_image') && $id_image = $this->addProductImage($object, Tools::getValue('resizer')))
						{
							self::$currentIndex .= '&image_updated='.$id_image;
							Hook::exec('updateProduct', array('product' => $object));
							Search::indexation(false, $object->id);
							if (Tools::getValue('resizer') == 'man' && isset($id_image) && is_int($id_image) && $id_image)
								$this->redirect_after = self::$currentIndex.'&id_product='.$object->id.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&edit='.strval(Tools::getValue('productCreated')).'&id_image='.$id_image.'&imageresize&toconf=4&submitAddAndStay='.((Tools::isSubmit('submitAdd'.$this->table.'AndStay') || Tools::getValue('productCreated') == 'on') ? 'on' : 'off').'&token='.(($token ? $token : $this->token));

							// Save and preview
							if (Tools::isSubmit('submitAddProductAndPreview'))
							{
								$preview_url = $this->context->link->getProductLink($this->getFieldValue($object, 'id'), $this->getFieldValue($object, 'link_rewrite', $this->context->language->id), Category::getLinkRewrite($this->getFieldValue($object, 'id_category_default'), $this->context->language->id), null, null, Context::getContext()->shop->getID());
								if (!$object->active)
								{
									$admin_dir = dirname($_SERVER['PHP_SELF']);
									$admin_dir = substr($admin_dir, strrpos($admin_dir,'/') + 1);
									$token = Tools::encrypt('PreviewProduct'.$object->id);
									if (strpos($preview_url, '?') === false)
										$preview_url .= '?';
									else
										$preview_url .= '&';
									$preview_url .= 'adtoken='.$token.'&ad='.$admin_dir;
								}
								$this->redirect_after = $preview_url;
							}
							else if (Tools::isSubmit('submitAdd'.$this->table.'AndStay') || ($id_image && $id_image !== true)) // Save and stay on same form
							{// Save and stay on same form
							if (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
								$this->redirect_after = self::$currentIndex.'&id_product='.$object->id.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&addproduct&conf=4&tabs='.(int)(Tools::getValue('tabs')).'&token='.($token ? $token : $this->token);

							// Default behavior (save and back)
							$this->redirect_after = self::$currentIndex.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&conf=4&token='.($token ? $token : $this->token);
							}
						}
					}
					else
						$this->_errors[] = Tools::displayError('An error occurred while updating object.').' <b>'.$this->table.'</b> ('.Db::getInstance()->getMsgError().')';
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while updating object.').' <b>'.$this->table.'</b> ('.Tools::displayError('Cannot load object').')';
			}

			/* Add a new product */
			else
			{
				$object = new $this->className();
				$this->_removeTaxFromEcotax();
				$this->copyFromPost($object, $this->table);
				if ($object->add())
				{
					$this->addCarriers();
					$this->updateAssoShop((int)$object->id);
					$this->updateAccessories($object);
					if (!$this->updatePackItems($object))
						$this->_errors[] = Tools::displayError('An error occurred while adding products to the pack.');
					$this->updateDownloadProduct($object);
					if (!sizeof($this->_errors))
					{
						if (!$object->updateCategories($_POST['categoryBox']))
							$this->_errors[] = Tools::displayError('An error occurred while linking object.').' <b>'.$this->table.'</b> '.Tools::displayError('To categories');
						else if (!$this->updateTags($languages, $object))
							$this->_errors[] = Tools::displayError('An error occurred while adding tags.');
						else if ($id_image = $this->addProductImage($object))
						{
							Hook::exec('addProduct', array('product' => $object));
							Search::indexation(false, $object->id);

							// Save and preview
							if (Tools::isSubmit('submitAddProductAndPreview'))
							{
								$preview_url = ($this->context->link->getProductLink($this->getFieldValue($object, 'id'), $this->getFieldValue($object, 'link_rewrite', $this->context->language->id), Category::getLinkRewrite($this->getFieldValue($object, 'id_category_default'), $this->context->language->id)));
								if (!$object->active)
								{
									$admin_dir = dirname($_SERVER['PHP_SELF']);
									$admin_dir = substr($admin_dir, strrpos($admin_dir,'/') + 1);
									$token = Tools::encrypt('PreviewProduct'.$object->id);
									$preview_url .= '&adtoken='.$token.'&ad='.$admin_dir;
								}

								$this->redirect_after = $preview_url;
							}

							if (Tools::getValue('resizer') == 'man' && isset($id_image) && is_int($id_image) && $id_image)
								$this->redirect_after = self::$currentIndex.'&id_product='.$object->id.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&id_image='.$id_image.'&imageresize&toconf=3&submitAddAndStay='.(Tools::isSubmit('submitAdd'.$this->table.'AndStay') ? 'on' : 'off').'&token='.($token ? $token : $this->token);
							// Save and stay on same form
							if (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
								$this->redirect_after = self::$currentIndex.'&id_product='.$object->id.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&addproduct&conf=3&tabs='.(int)(Tools::getValue('tabs')).'&token='.($token ? $token : $this->token);
							else
								// Default behavior (save and back)
								$this->redirect_after = self::$currentIndex.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&conf=3&token='.($token ? $token : $this->token);
						}
					}
					else
						$object->delete();
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while creating object.').' <b>'.$this->table.'</b>';
			}
		}

	}

	private function _removeTaxFromEcotax()
	{
	    $ecotaxTaxRate = Tax::getProductEcotaxRate();
		if ($ecotax = Tools::getValue('ecotax'))
			$_POST['ecotax'] = Tools::ps_round(Tools::getValue('ecotax') / (1 + $ecotaxTaxRate / 100), 6);
	}

	private function _applyTaxToEcotax($product)
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
		$is_virtual_file = (int) Tools::getValue('is_virtual_file');

		/* add or update a virtual product */
		if (Tools::getValue('is_virtual_good') == 'true')
		{
			if (!Tools::getValue('virtual_product_name') && !Tools::getValue('virtual_product_name_attribute') && !empty($is_virtual_file))
			{
				if (!Tools::getValue('virtual_product_name'))
				{
					if (!Tools::getValue('virtual_product_name_attribute') && !empty($id_product_attribute))
					{
						$this->_errors[] = $this->l('the field').' <b>'.$this->l('display filename attribute').'</b> '.$this->l('is required');
						return false;
					}
					else if (!empty($id_product_attribute))
					{
						$this->_errors[] = $this->l('the field').' <b>'.$this->l('display filename').'</b> '.$this->l('is required');
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
							$this->_errors[] = $this->l('the field').' <b>'.$this->l('number of days attribute').'</b> '.$this->l('is required');
							return false;
						}
					}
					else if (!empty($id_product_attribute))
					{
						$this->_errors[] = $this->l('the field').' <b>'.$this->l('number of days').'</b> '.$this->l('is required');
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
						$this->_errors[] = $this->l('the field').' <b>'.$this->l('expiration date attribute').'</b> '.$this->l('is required');
						return false;
					}
					else if (!empty($id_product_attribute))
					{
						$this->_errors[] = $this->l('the field').' <b>'.$this->l('expiration date').'</b> '.$this->l('is not valid');
						return false;
					}
				}
			}

			// The oos behavior MUST be "Deny orders" for virtual products
			if (Tools::getValue('out_of_stock') != 0)
			{
				$this->_errors[] = $this->l('The "when out of stock" behavior selection must be "deny order" for virtual products');
				return false;
			}

			// Trick's
			if ($edit == 1)
			{
				$id_product_download_attribute = ProductDownload::getIdFromIdAttribute((int) $product->id, $id_product_attribute);
				$id_product_download = ($id_product_download_attribute) ? (int) $id_product_download_attribute : (int) Tools::getValue('virtual_product_id');
			} else
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
			else if ($virtual_product_filename)
				$filename = $virtual_product_filename;
			else
				$filename = ProductDownload::getNewFilename();

			$download = new ProductDownload($id_product_download);
			$download->id_product = (int) $product->id;
			$download->id_product_attribute = (int) $id_product_attribute;
			$download->display_filename = $virtual_product_name;
			$download->filename = $filename;
			$download->date_add = date('Y-m-d H:i:s');
			$download->date_expiration = $virtual_product_expiration_date ? $virtual_product_expiration_date.' 23:59:59' : '';
			$download->nb_days_accessible = (int) $virtual_product_nb_days;
			$download->nb_downloadable = (int) $virtual_product_nb_downloable;
			$download->active = 1;
			$download->is_shareable = (int) $is_shareable;

			if ($download->save())
				return true;
		}
		else
		{
			/* unactive download product if checkbox not checked */
			if ($edit == 1)
			{
				$id_product_download_attribute = ProductDownload::getIdFromIdAttribute((int) $product->id, $id_product_attribute);
				$id_product_download = ($id_product_download_attribute) ? (int) $id_product_download_attribute : (int) Tools::getValue('virtual_product_id');
			}
			else
				$id_product_download = ProductDownload::getIdFromIdProduct($product->id);

			if (!empty($id_product_download))
			{
				$productDownload = new ProductDownload($id_product_download);
				$productDownload->date_expiration = date('Y-m-d H:i:s', time()-1);
				$productDownload->active = 0;
				return $productDownload->save();
			}
		}
		return false;
	}

	public function deleteDownloadProduct($id_product_attribute = NULL)
	{
		if (!empty($id_product_attribute))
		{
			$productDownload = new ProductDownload($id_product_attribute);
			$productDownload->date_expiration = date('Y-m-d H:i:s', time()-1);
			$productDownload->active = 0;
			return $productDownload->save();
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
			if (sizeof($accessories_id))
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
		if (!Db::getInstance()->Execute('
		DELETE FROM `'._DB_PREFIX_.'product_tag`
		WHERE `id_product` = '.(int)($product->id)))
			return false;
		/* Assign tags to this product */
		foreach ($languages as $language)
			if ($value = Tools::getValue('tags_'.$language['id_lang']))
				$tagError &= Tag::addTags($language['id_lang'], (int)($product->id), $value);
		return $tagError;
	}

	public function initContent($token = null)
	{
		// this is made to "save and stay" feature
		$this->tpl_form_vars['show_product_tab_content'] = Tools::getValue('action');
		if (Tools::getValue('id_product') || ((Tools::isSubmit('submitAddproduct') OR Tools::isSubmit('submitAddproductAndPreview') OR Tools::isSubmit('submitAddproductAndStay') OR Tools::isSubmit('submitSpecificPricePriorities') OR Tools::isSubmit('submitPriceAddition') OR Tools::isSubmit('submitPricesModification')) AND sizeof($this->_errors)) OR Tools::isSubmit('updateproduct') OR Tools::isSubmit('addproduct'))
		{
			$this->fields_form = array();
			if (empty($this->action))
				$this->action = 'Informations';

			if(method_exists($this, 'initForm'.$this->action))
				$this->tpl_form = 'products/'.strtolower($this->action).'.tpl';

			if ($this->ajax)
			{
				$this->display = 'edit';
				$this->content_only = true;
			}
			else
			{
				$product_tabs = array();
				// action defines which tab to display first
				$action = $this->action;
				if (empty($action) || !method_exists($this, 'initForm'.$action))
					$action = 'Informations';
				if (Tools::getValue('id_product'))
				{
					// i is used as product_tab id
					$i = 0;
					$advanced_stock_management_active = Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');

					foreach ($this->available_tabs as $product_tab)
					{
						if ($advanced_stock_management_active == 1 || ($advanced_stock_management_active == 0 && ($product_tab != 'Warehouses')))
						{
							$product_tabs[$product_tab] = array(
								'id' => ++$i.'-'.$product_tab,
								'selected' => (strtolower($product_tab) == strtolower($action)),
								'name' => $this->available_tabs_lang[$product_tab],
								'href' => $this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.Tools::getValue('id_product').'&amp;action='.$product_tab,
							);
						}
					}
					$this->tpl_form_vars['newproduct'] = 0;
				}
				else
					$this->tpl_form_vars['newproduct'] = 1;

				$this->tpl_form_vars['product_tabs'] = $product_tabs;
				$this->tpl_form_vars['tabs_toolbar_save_buttons'] = $this->tabs_toolbar_save_buttons;
			}


			$languages = Language::getLanguages(false);
			$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		}
		else
		{
			$this->display = 'list';
			if ($id_category = (int)Tools::getValue('id_category'))
				self::$currentIndex .= '&id_category='.$id_category;
			$this->getList($this->context->language->id, !$this->context->cookie->__get($this->table.'Orderby') ? 'position' : null, !$this->context->cookie->__get($this->table.'Orderway') ? 'ASC' : null, 0, null, $this->context->shop->getID(true));

			if (!empty($this->_list))
			{
				$id_category = Tools::getValue('id_category', 1);
				if (!$id_category)
					$id_category = 1;
				// @todo lot of ergonomy works around here
				// @todo : move blockcategories select queries in class Category
				$root_categ = Category::getRootCategory();
				$children = $root_categ->getAllChildren();
				$category_tree = array();

				// Add category "all products" to tree
				$all_categ = new Category();
				$all_categ->name = 'All products';
				$all_categ->selected = $this->_category->id_category == $all_categ->id;
				$all_categ->dashes = '';
				$category_tree[] = $all_categ;

				// Add root category to tree
				$root_categ->selected = $this->_category->id_category == $root_categ->id;
				$root_categ->dashes = str_repeat('&nbsp;-&nbsp;',$root_categ->level_depth);
				$category_tree[] = $root_categ;

				foreach ($children as $k => $categ)
				{
					$categ = new Category($categ['id_category'],$this->context->language->id);
					$categ->selected = $this->_category->id_category == $categ->id;
					$categ->dashes = str_repeat('&nbsp;-&nbsp;',$categ->level_depth);
					$category_tree[] = $categ;
				}
				$this->tpl_list_vars['category_tree'] = $category_tree;

				// used to build the new url when changing category
				$this->tpl_list_vars['base_url'] = preg_replace('#&id_category=[0-9]*#', '', self::$currentIndex).'&token='.$this->token;
			}
		}
		// @todo module free
		$this->tpl_form_vars['vat_number'] = file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php');
		parent::initContent();
	}

	public function initList()
	{
		if (!Tools::getValue('id_category'))
			unset($this->fieldsDisplay['position']);
		return parent::initList();
	}

	public function ajaxProcessProductManufacturers()
	{
		$manufacturers = Manufacturer::getManufacturers();
		if ($manufacturers)
		{
		$jsonArray = array();
			foreach ($manufacturers AS $manufacturer)
				$jsonArray[] = '{"optionValue": "'.$manufacturer['id_manufacturer'].'", "optionDisplay": "'.htmlspecialchars(trim($manufacturer['name'])).'"}';
			die('['.implode(',', $jsonArray).']');
		}
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

		$todo = sizeof($categories[$current['infos']['id_parent']]);
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
					$content .= self::recurseCategoryForInclude($id_obj, $indexedCategories, $categories, $categories[$id_category][$key], $key, $id_category_default, $has_suite);
		return $content;
	}

	public function displayErrors()
	{
		if ($this->includeSubTab('displayErrors'))
			;
		else if ($nbErrors = sizeof($this->_errors))
		{
			$this->content .= '<div class="error">
				<img src="../img/admin/error2.png" />
				'.$nbErrors.' '.($nbErrors > 1 ? $this->l('errors') : $this->l('error')).'
				<ol>';
			foreach ($this->_errors as $error)
				$this->content .= '<li>'.$error.'</li>';
			$this->content .= '
				</ol>
			</div>';
		}
	}

	private function _displayDraftWarning($active)
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
			if ($product = $this->loadObject(true))
			{
				if ($this->tabAccess['delete'])
					$this->toolbar_btn['delete'] = array(
						'short' => 'Delete',
						'href' => $this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.$product->id.'&amp;deleteproduct',
						'desc' => $this->l('Delete this product'),
						'confirm' => 1);

				if ($this->tabAccess['add'])
					$this->toolbar_btn['duplicate'] = array(
						'short' => 'Duplicate',
						'href' => '#todo'.$this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.$product->id,
						'desc' => $this->l('Duplicate'),
						'confirm' => 1
					);

				// @TODO navigation
				if ($url_preview = $this->getPreviewUrl($product))
					$this->toolbar_btn['preview'] = array(
						'short' => 'Preview',
						'href' => $url_preview,
						'desc' => $this->l('prevdesc'),
						'target' => true,
						'class' => 'previewUrl'
					);

				if (file_exists(_PS_MODULE_DIR_.'statsproduct/statsproduct.php'))
					$this->toolbar_btn['stats'] = array(
					'short' => 'Statistics',
					'href' => $this->context->link->getAdminLink('AdminStats').'&amp;module=statsproduct&amp;id_product='.$product->id,
					'desc' => $this->l('View product sales'),
				);

				$this->toolbar_btn['cancel'] = array(
					'short' => 'Close',
					'href' => '#todo'.$this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.$product->id,
					'desc' => $this->l('Cancel'),
					'confirm' => 1
				);

				if ($this->tabAccess['add'])
					$this->toolbar_btn['new'] = array(
					'short' => 'Create',
					'href' => '#todo'.$this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.$product->id,
					'desc' => $this->l('Create'),
				);

				if ($this->tabAccess['edit'])
				{
					$this->toolbar_btn['save'] = array(
						'short' => 'Save',
						'href' => '#todo'.$this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.$product->id,
						'desc' => $this->l('Save'),
					);

					$this->toolbar_btn['save-and-stay'] = array(
						'short' => 'SaveAndStay',
						'href' => '#todo'.$this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.$product->id,
						'desc' => $this->l('Save and stay'),
					);
				}
			}
		parent::initToolbar();
		$this->context->smarty->assign('toolbar_fix', 1);
		$this->context->smarty->assign('show_toolbar', 1);
		$this->context->smarty->assign('toolbar_btn', $this->toolbar_btn);
	}

	/**
	 * initForm contains all necessary initialization needed for all tabs
	 *
	 * @return void
	 */
	public function initForm()
	{
		if(!method_exists($this, 'initForm'.$this->action))
			return "";
		// getLanguages init this->_languages
		$this->getLanguages();
		$languages = $this->_languages;
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));

		$this->tpl_form_vars['currentIndex'] = self::$currentIndex;
		$this->fields_form = array("pouet"=>"eh oui");
		$this->addJs(_PS_JS_DIR_.'attributesBack.js');
		$this->display = 'edit';
		$this->addJqueryUI('ui.datepicker');
		$this->addJqueryUI('ui.accordion');
		$this->tpl_form_vars['pos_select'] = ($tab = Tools::getValue('tabs')) ? $tab : '0';
		$this->tpl_form_vars['token'] = $this->token;
		$this->tpl_form_vars['combinationImagesJs'] = $this->getCombinationImagesJs();
		$id_product = Tools::getvalue('id_product');
		$this->tpl_form_vars['form_action'] = $this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.$id_product;
		$this->tpl_form_vars['id_product'] = $id_product;

		$this->tpl_form_vars['upload_max_filesize'] = ini_get('upload_max_filesize');
		$this->tpl_form_vars['country_display_tax_label'] = $this->context->country->display_tax_label;

		// let's calculate this once for all
		if (!Validate::isLoadedObject($this->object) && Tools::getValue('id_product'))
			$this->_errors[] = 'Unable to load object';
	//		throw new PrestashopException('object not loaded');
		else
		{
			$this->_displayDraftWarning($this->object->active);
			$this->{'initForm'.$this->action}($this->object, $languages, $defaultLanguage);
			$this->tpl_form_vars['product'] = $this->object;
			if ($this->ajax)
				if (!isset($this->tpl_form_vars['custom_form']))
					throw new PrestashopException('custom_form empty for action '.$this->action);
				else
					return $this->tpl_form_vars['custom_form'];
		}
		return parent::initForm();
	}

	public function getPreviewUrl(Product $product)
	{
		if (!(bool)$this->context->shop->virtual_uri)
			return false;

		$preview_url = $this->context->link->getProductLink(
		$this->getFieldValue($product, 'id'),
			$this->getFieldValue($product, 'link_rewrite', $this->context->language->id),
			Category::getLinkRewrite($product->id_category_default, $this->context->language->id), null, null, Context::getContext()->shop->getID());
		if (!$product->active)
		{
		$admin_dir = dirname($_SERVER['PHP_SELF']);
		$admin_dir = substr($admin_dir, strrpos($admin_dir,'/') + 1);
		$token = Tools::encrypt('PreviewProduct'.$product->id);
		if (strpos($preview_url, '?') === false)
			$preview_url .= '?';
			$preview_url = ($this->context->link->getProductLink($this->getFieldValue($product, 'id'), $this->getFieldValue($product, 'link_rewrite', $this->default_form_language), Category::getLinkRewrite($this->getFieldValue($product, 'id_category_default'), $this->context->language->id)));
			if (!$product->active)
			{
				$admin_dir = dirname($_SERVER['PHP_SELF']);
				$admin_dir = substr($admin_dir, strrpos($admin_dir,'/') + 1);
				$token = Tools::encrypt('PreviewProduct'.$product->id);

				$preview_url .= $product->active ? '' : '&adtoken='.$token.'&ad='.$admin_dir;
			}
		}
		return $preview_url;
	}

	/**
	* Post traitment for accounting
	*/
	public function postProcessFormAccounting()
	{

		if (Validate::isLoadedObject($product = new Product((int)(Tools::getValue('id_product')))))
		{
			$id_shop = $this->context->shop->getID();

			// If zone still exist, then update the database with the new value
			if (count($zones = Zone::getZones()))
			{
				// Build tab with associated data
				$tab = array();
				foreach($zones as $zone)
					if (($num = Tools::getValue('zone_'.$zone['id_zone'])) !== NULL)
						$tab[] = array(
							'id_zone' => $zone['id_zone'],
							'id_product' => $product->id,
							'id_shop' => $id_shop,
							'num' => $num);

			// Save to the database the account
			if (count($tab) && Accounting::saveProductAccountingInformations($tab))
				$this->confirmations[] = $this->l('Account numbers have been updated');
			else
				$this->_errors[] = $this->l('Account Numbers could not be updated or added in the database');
			}
		}
	}

	/**
	* Post traitment for suppliers
	*/
	public function postProcessFormSuppliers()
	{
		if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
		{
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
			foreach ($associated_suppliers as $key => &$associated_supplier)
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

			// Manage defaut supplier for product
			if ($new_default_supplier != 0 && $new_default_supplier != $product->id_supplier && Supplier::supplierExists($new_default_supplier))
			{
				$product->id_supplier = $new_default_supplier;
				$product->update();
			}

			$this->confirmations[] = $this->l('Suppliers of the product have been updated');

			// Get all id_product_attribute
			$attributes = $product->getAttributesResume($this->context->language->id);
			if (empty($attributes))
				$attributes[] = array(
					'id_product_attribute' => 0,
					'attribute_designation' => ''
				);

			// Manage references
			foreach ($attributes as $attribute)
				foreach ($associated_suppliers as $supplier)
					if (Tools::isSubmit('supplier_reference_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier))
					{
						$reference = Tools::getValue('supplier_reference_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier, '');

						if (!empty($reference))
						{
							$existing_id = (int)ProductSupplier::getIdByProductAndSupplier($product->id, $attribute['id_product_attribute'], $supplier->id_supplier);

							if ($existing_id <= 0)
							{
								//create new record
								$product_supplier_entity = new ProductSupplier();
								$product_supplier_entity->id_product = $product->id;
								$product_supplier_entity->id_product_attribute = $attribute['id_product_attribute'];
								$product_supplier_entity->id_supplier = $supplier->id_supplier;
								$product_supplier_entity->product_supplier_reference = pSQL($reference);
								$product_supplier_entity->save();
							}
							else
							{
								//update existing record
								$product_supplier_entity = new ProductSupplier($existing_id);
								$reference = pSQL($reference);

								if ($product_supplier_entity->product_supplier_reference != $reference)
								{
									$product_supplier_entity->product_supplier_reference = pSQL($reference);
									$product_supplier_entity->update();
								}
							}
						}
					}

			$this->confirmations[] = $this->l('Supplier Reference(s) of the product have been updated');
		}
	}

	/**
	* Post traitment for warehouses
	*/
	public function postProcessFormWarehouses()
	{
		if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
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
					$key = $warehouse['id_warehouse'].'_'.$attribute['id_product'].'_'.$attribute['id_product_attribute'];

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

				$wpl_id = WarehouseProductLocation::getIdByProductAndWarehouse($params[1], $params[2], $params[0]);

				if (empty($wpl_id))
				{
					//create new record
					$warehouse_location_entity = new WarehouseProductLocation();
					$warehouse_location_entity->id_product = $params[1];
					$warehouse_location_entity->id_product_attribute = $params[2];
					$warehouse_location_entity->id_warehouse = $params[0];
					$warehouse_location_entity->location = pSQL($location);
					$warehouse_location_entity->save();
				}
				else
				{
					$warehouse_location_entity = new WarehouseProductLocation($wpl_id);

					$location = pSQL($location);

					if ($location != $warehouse_location_entity->location)
					{
						$warehouse_location_entity->location = pSQL($location);
						$warehouse_location_entity->update();
					}
					break;
				}
			}

			$this->confirmations[] = $this->l('Warehouses and location(s) of the product have been updated');
		}
	}

	/**
	* Init data for accounting
	*/
	public function initFormAccounting($product, $t)
	{
		$error = '';
		$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;
		$detail = array();

		if (count($this->context->shop->getListOfID()) > 1)
			$error = $this->l('Please select the shop you want to configure');
		else
		{
			$zones = Zone::getZones();
			$id_shop = $this->context->shop->getID();

			// Set default zone value to the shop	and sort it
			foreach($zones as $zone)
			{
				$detail['zones'][$zone['id_zone']]['name'] = $zone['name'];
				$detail['zones'][$zone['id_zone']]['account_number'] = '';
			}
			$zoneAccountNumberList = Accounting::getProductAccountNumberZoneShop($product->id, $id_shop);

			// Set Account number to the id_zone for an id_shop if exist
			foreach($zoneAccountNumberList as $zone)
				$detail['zones'][$zone['id_zone']]['account_number'] = $zone['account_number'];
		}

		$this->context->smarty->assign(array(
			'productAccountNumberList' => $detail,
			'shopName' => $this->context->shop->name,
			'error' => $error,
		));
		$this->tpl_form_vars['custom_form'] = $this->context->smarty->fetch('products/accounting.tpl');
	}

	public function initFormPrices($obj, $languages, $defaultLanguage)
	{
		$data = $this->context->smarty->createData();

		if ($this->object->id)
		{
			$shops = Shop::getShops();
			$countries = Country::getCountries($this->context->language->id);
			$groups = Group::getGroups($this->context->language->id);
			$currencies = Currency::getCurrencies();
			$currency =
			$attributes = $this->object->getAttributesGroups((int)$this->context->language->id);
			$combinations = array();
			foreach($attributes as $attribute)
			{
				$combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
				if (!isset($combinations[$attribute['id_product_attribute']]['attributes']))
					$combinations[$attribute['id_product_attribute']]['attributes'] = '';
				$combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'].' - ';

				$combinations[$attribute['id_product_attribute']]['price'] = Tools::displayPrice(Tools::convertPrice(Product::getPriceStatic((int)$this->object->id, false, $attribute['id_product_attribute']), $this->context->currency), $this->context->currency);
			}
			foreach ($combinations as &$combination)
				$combination['attributes'] = rtrim($combination['attributes'], ' - ');

			$data->assign(array(
				'shops' => $shops,
				'currencies' => $currencies,
				'countries' => $countries,
				'groups' => $groups,
				'combinations' => $combinations,
				'product' => $this->object
			));

			$data->assign('country_display_tax_label', $this->context->country->display_tax_label);
			$data->assign('specificPriceModificationForm', $this->_displaySpecificPriceModificationForm($this->context->currency, $shops, $currencies, $countries, $groups));
		}
		else
			$data->assign('content', '<b>'.$this->l('You must save this product before adding specific prices').'.</b>');

		$data->assign('currency', $this->context->currency);

		$this->tpl_form_vars['custom_form'] = $this->context->smarty->createTemplate($this->tpl_form, $data)->fetch();
	}

	private function _getFinalPrice($specificPrice, $productPrice, $taxRate)
	{
		$price = Tools::ps_round((float)($specificPrice['price']) ? $specificPrice['price'] : $productPrice, 2);
		if (!(float)($specificPrice['reduction']))
			return (float)($specificPrice['price']);
		return ($specificPrice['reduction_type'] == 'amount') ? ($price - $specificPrice['reduction'] / (1 + $taxRate / 100)) : ($price - $price * $specificPrice['reduction']);
	}

	protected function _displaySpecificPriceModificationForm($defaultCurrency, $shops, $currencies, $countries, $groups)
	{
		$content = '';
		if (!($obj = $this->loadObject()))
			return;
		$specificPrices = SpecificPrice::getByProductId((int)($obj->id));
		$specificPricePriorities = SpecificPrice::getPriority((int)($obj->id));

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

		if (!is_array($specificPrices) || !sizeof($specificPrices))
			$content .= '
				<tr>
					<td colspan="9">'.$this->l('No specific prices').'</td>
				</tr>';
		else
		{
			$i = 0;
			foreach ($specificPrices as $specificPrice)
			{
				$current_specific_currency = $currencies[($specificPrice['id_currency'] ? $specificPrice['id_currency'] : $defaultCurrency->id)];
				if ($specificPrice['reduction_type'] == 'percentage')
					$reduction = ($specificPrice['reduction'] * 100).' %';
				else
					$reduction = Tools::displayPrice(Tools::ps_round($specificPrice['reduction'], 2), $current_specific_currency);

				if ($specificPrice['from'] == '0000-00-00 00:00:00' && $specificPrice['to'] == '0000-00-00 00:00:00')
					$period = $this->l('Unlimited');
				else
					$period = $this->l('From').' '.($specificPrice['from'] != '0000-00-00 00:00:00' ? $specificPrice['from'] : '0000-00-00 00:00:00').'<br />'.$this->l('To').' '.($specificPrice['to'] != '0000-00-00 00:00:00' ? $specificPrice['to'] : '0000-00-00 00:00:00');
				if ($specificPrice['id_product_attribute'])
				{
					$combination = new Combination((int)$specificPrice['id_product_attribute']);
					$attributes = $combination->getAttributesName((int)$this->context->language->id);
					$attributes_name = '';
					foreach ($attributes as $attribute)
						$attributes_name .= $attribute['name'].' - ';
					$attributes_name = rtrim($attributes_name, ' - ');
				}
				else
					$attributes_name = $this->l('All combinations');
				$content .= '
				<tr '.($i%2 ? 'class="alt_row"' : '').'>
					<td class="cell border">'.$attributes_name.'</td>
					<td class="cell border">'.($specificPrice['id_shop'] ? $shops[$specificPrice['id_shop']]['name'] : $this->l('All shops')).'</td>
					<td class="cell border">'.($specificPrice['id_currency'] ? $currencies[$specificPrice['id_currency']]['name'] : $this->l('All currencies')).'</td>
					<td class="cell border">'.($specificPrice['id_country'] ? $countries[$specificPrice['id_country']]['name'] : $this->l('All countries')).'</td>
					<td class="cell border">'.($specificPrice['id_group'] ? $groups[$specificPrice['id_group']]['name'] : $this->l('All groups')).'</td>
					<td class="cell border">'.Tools::displayPrice((float)$specificPrice['price'], $current_specific_currency).'</td>
					<td class="cell border">'.$reduction.'</td>
					<td class="cell border">'.$period.'</td>
					<td class="cell border">'.$specificPrice['from_quantity'].'</th>
					<td class="cell border"><b>'.Tools::displayPrice(Tools::ps_round((float)($this->_getFinalPrice($specificPrice, (float)($obj->price), $taxRate)), 2), $current_specific_currency).'</b></td>
					<td class="cell border"><a href="'.self::$currentIndex.(Tools::getValue('id_category') ? '&id_category='.Tools::getValue('id_category') : '').'&id_product='.(int)(Tools::getValue('id_product')).'&updateproduct&deleteSpecificPrice&id_specific_price='.(int)($specificPrice['id_specific_price']).'&token='.Tools::getValue('token').'"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /></a></td>
				</tr>';
				$i++;
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

		$content .= '
		<div class="separation"></div>
		<h4>'.$this->l('Priorities management').'</h4>
		<div class="hint clear" style="display:block;">
				'.$this->l('Sometimes one customer could fit in multiple rules, priorities allows you to define which rule to apply.').'
		</div>
	<br />
		<label>'.$this->l('Priorities:').'</label>
		<div class="margin-form">
			<select name="specificPricePriority[]">
				<option value="id_shop"'.($specificPricePriorities[0] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
				<option value="id_currency"'.($specificPricePriorities[0] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
				<option value="id_country"'.($specificPricePriorities[0] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
				<option value="id_group"'.($specificPricePriorities[0] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
			</select>
			<select name="specificPricePriority[]">
				<option value="id_shop"'.($specificPricePriorities[1] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
				<option value="id_currency"'.($specificPricePriorities[1] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
				<option value="id_country"'.($specificPricePriorities[1] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
				<option value="id_group"'.($specificPricePriorities[1] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
			</select>
			&gt;
			<select name="specificPricePriority[]">
				<option value="id_shop"'.($specificPricePriorities[2] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
				<option value="id_currency"'.($specificPricePriorities[2] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
				<option value="id_country"'.($specificPricePriorities[2] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
				<option value="id_group"'.($specificPricePriorities[2] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
			</select>
			&gt;
			<select name="specificPricePriority[]">
				<option value="id_shop"'.($specificPricePriorities[3] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
				<option value="id_currency"'.($specificPricePriorities[3] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
				<option value="id_country"'.($specificPricePriorities[3] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
				<option value="id_group"'.($specificPricePriorities[3] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
			</select>
		</div>

		<div class="margin-form">
			<input type="checkbox" name="specificPricePriorityToAll" id="specificPricePriorityToAll" /> <label class="t" for="specificPricePriorityToAll">'.$this->l('Apply to all products').'</label>
		</div>

		<div class="margin-form">
			<input class="button" type="submit" name="submitSpecificPricePriorities" value="'.$this->l('Apply').'" />
		</div>
		';
		return $content;
	}

	private function _getCustomizationFieldIds($labels, $alreadyGenerated, $obj)
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

	private function _displayLabelField(&$label, $languages, $defaultLanguage, $type, $fieldIds, $id_customization_field)
	{
		$content = '';
		$fieldsName = 'label_'.$type.'_'.(int)($id_customization_field);
		$fieldsContainerName = 'labelContainer_'.$type.'_'.(int)($id_customization_field);
		$content .= '<div id="'.$fieldsContainerName.'" class="translatable clear" style="line-height: 18px">';
		foreach ($languages as $language)
		{
			$fieldName = 'label_'.$type.'_'.(int)($id_customization_field).'_'.(int)($language['id_lang']);
			$text = (isset($label[(int)($language['id_lang'])])) ? $label[(int)($language['id_lang'])]['name'] : '';
			$content .= '<div class="lang_'.$language['id_lang'].'" id="'.$fieldName.'" style="display: '.((int)($language['id_lang']) == (int)($defaultLanguage) ? 'block' : 'none').'; clear: left; float: left; padding-bottom: 4px;">
						<div style="margin-right: 6px; float:left; text-align:right;">#'.(int)($id_customization_field).'</div><input type="text" name="'.$fieldName.'" value="'.htmlentities($text, ENT_COMPAT, 'UTF-8').'" style="float: left" />
					</div>';
		}

		$required = (isset($label[(int)($language['id_lang'])])) ? $label[(int)($language['id_lang'])]['required'] : false;
		$content .= '</div>
				<div style="margin: 3px 0 0 3px; font-size: 11px">
					<input type="checkbox" name="require_'.$type.'_'.(int)($id_customization_field).'" id="require_'.$type.'_'.(int)($id_customization_field).'" value="1" '.($required ? 'checked="checked"' : '').' style="float: left; margin: 0 4px"/><label for="require_'.$type.'_'.(int)($id_customization_field).'" style="float: none; font-weight: normal;"> '.$this->l('required').'</label>
				</div>';
		return $content;
	}

	private function _displayLabelFields(&$obj, &$labels, $languages, $defaultLanguage, $type)
	{
		$content = '';
		$type = (int)($type);
		$labelGenerated = array(Product::CUSTOMIZE_FILE => (isset($labels[Product::CUSTOMIZE_FILE]) ? count($labels[Product::CUSTOMIZE_FILE]) : 0), Product::CUSTOMIZE_TEXTFIELD => (isset($labels[Product::CUSTOMIZE_TEXTFIELD]) ? count($labels[Product::CUSTOMIZE_TEXTFIELD]) : 0));

		$fieldIds = $this->_getCustomizationFieldIds($labels, $labelGenerated, $obj);
		if (isset($labels[$type]))
			foreach ($labels[$type] as $id_customization_field => $label)
				$content .= $this->_displayLabelField($label, $languages, $defaultLanguage, $type, $fieldIds, (int)($id_customization_field));
		return $content;
	}

	public function initFormCustomization($obj, $languages, $defaultLanguage)
	{
		$content = '';
		$labels = $obj->getCustomizationFields();
		$defaultIso = Language::getIsoById($defaultLanguage);

		$hasFileLabels = (int)($this->getFieldValue($obj, 'uploadable_files'));
		$hasTextLabels = (int)($this->getFieldValue($obj, 'text_fields'));

		$content .= '
			<table cellpadding="5">
				<tr>
					<td colspan="2"><b>'.$this->l('Add or modify customizable properties').'</b></td>
				</tr>
			</table>
			<div class="separation"></div><br />
			<table cellpadding="5" style="width:100%">
				<tr>
					<td style="width:150px;text-align:right;padding-right:10px;font-weight:bold;vertical-align:top;" valign="top">'.$this->l('File fields:').'</td>
					<td style="padding-bottom:5px;">
						<input type="text" name="uploadable_files" id="uploadable_files" size="4" value="'.((int)($this->getFieldValue($obj, 'uploadable_files')) ? (int)($this->getFieldValue($obj, 'uploadable_files')) : '0').'" />
						<p>'.$this->l('Number of upload file fields displayed').'</p>
					</td>
				</tr>
				<tr>
					<td style="width:150px;text-align:right;padding-right:10px;font-weight:bold;vertical-align:top;" valign="top">'.$this->l('Text fields:').'</td>
					<td style="padding-bottom:5px;">
						<input type="text" name="text_fields" id="text_fields" size="4" value="'.((int)($this->getFieldValue($obj, 'text_fields')) ? (int)($this->getFieldValue($obj, 'text_fields')) : '0').'" />
						<p>'.$this->l('Number of text fields displayed').'</p>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center;">
						<input type="submit" name="submitCustomizationConfiguration" value="'.$this->l('Update settings').'" class="button" onclick="this.form.action += \'&addproduct&tabs=5\';" />
					</td>
				</tr>';

				if ($hasFileLabels)
				{
					$content .= '
				<tr><td colspan="2"><div class="separation"></div></td></tr>
				<tr>
					<td style="width:150px" valign="top">'.$this->l('Files fields:').'</td>
					<td>';
					$content .= $this->_displayLabelFields($obj, $labels, $languages, $defaultLanguage, Product::CUSTOMIZE_FILE);
					$content .= '
					</td>
				</tr>';
				}

				if ($hasTextLabels)
				{
					$content .= '
				<tr><td colspan="2"><div class="separation"></div></td></tr>
				<tr>
					<td style="width:150px" valign="top">'.$this->l('Text fields:').'</td>
					<td>';
					$content .= $this->_displayLabelFields($obj, $labels, $languages, $defaultLanguage, Product::CUSTOMIZE_TEXTFIELD);
					$content .= '
					</td>
				</tr>';
				}

				$content .= '
				<tr>
					<td colspan="2" style="text-align:center;">';
				if ($hasFileLabels || $hasTextLabels)
					$content .= '<input type="submit" name="submitProductCustomization" id="submitProductCustomization" value="'.$this->l('Save labels').'" class="button" onclick="this.form.action += \'&addproduct&tabs=5\';" style="margin-top: 9px" />';
				$content .= '
					</td>
				</tr>
			</table>';
		$this->tpl_form_vars['custom_form'] = $content;
	}

	public function initFormAttachments($obj, $languages, $defaultLanguage)
	{
		$content = '';
		if (!($obj = $this->loadObject(true)))
			return;
		$languages = Language::getLanguages(false);
		$attach1 = Attachment::getAttachments($this->context->language->id, $obj->id, true);
		$attach2 = Attachment::getAttachments($this->context->language->id, $obj->id, false);

				$content .= '
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/t/AdminAttachments.gif" />'.$this->l('Attachment').'</legend>
				<label>'.$this->l('Filename:').' </label>
				<div class="margin-form">';
		foreach ($languages as $language)
			$content .= '	<div id="attachment_name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="attachment_name_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'attachment_name', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
					</div>';
		$content .= $this->getTranslationsFlags($languages, $defaultLanguage, 'attachment_name¤attachment_description', 'attachment_name');
		$content .= '	</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Description:').' </label>
				<div class="margin-form">';
		foreach ($languages as $language)
			$content .= '	<div id="attachment_description_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<textarea name="attachment_description_'.$language['id_lang'].'">'.htmlentities($this->getFieldValue($obj, 'attachment_description', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'</textarea>
					</div>';
		$content .= $this->getTranslationsFlags($languages, $defaultLanguage, 'attachment_name¤attachment_description', 'attachment_description');
		$content .= '	</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('File').'</label>
				<div class="margin-form">
					<p><input type="file" name="attachment_file" /></p>
					<p>'.$this->l('Upload file from your computer').'</p>
				</div>
				<div class="clear">&nbsp;</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('Add a new attachment file').'" name="submitAddAttachments" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		<div class="clear">&nbsp;</div>
		<table>
			<tr>
				<td>
					<p>'.$this->l('Attachments for this product:').'</p>
					<select multiple id="selectAttachment1" name="attachments[]" style="width:300px;height:160px;">';
			foreach ($attach1 as $attach)
				$content .= '<option value="'.$attach['id_attachment'].'">'.$attach['name'].'</option>';
			$content .= '	</select><br /><br />
					<a href="#" id="removeAttachment" style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px">
						'.$this->l('Remove').' &gt;&gt;
					</a>
				</td>
				<td style="padding-left:20px;">
					<p>'.$this->l('Available attachments:').'</p>
					<select multiple id="selectAttachment2" style="width:300px;height:160px;">';
			foreach ($attach2 as $attach)
				$content .= '<option value="'.$attach['id_attachment'].'">'.$attach['name'].'</option>';
			$content .= '	</select><br /><br />
					<a href="#" id="addAttachment" style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px">
						&lt;&lt; '.$this->l('Add').'
					</a>
				</div>
				</td>
			</tr>
		</table>
		<div class="clear">&nbsp;</div>
		<input type="submit" name="submitAttachments" id="submitAttachments" value="'.$this->l('Update attachments').'" class="button" />';
		$this->tpl_form_vars['custom_form'] = $content;
	}

	public function initFormInformations($product, $languages, $defaultLanguage)
	{
		$data = $this->context->smarty->createData();

		// autoload rich text editor (tiny mce)
		$iso = $this->context->language->iso_code;
		$this->tpl_form_vars['iso'] = file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en';
		$this->tpl_form_vars['ad'] = dirname($_SERVER['PHP_SELF']);
		$this->tpl_form_vars['tinymce'] = true;
		$this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
		$this->addJS(_PS_JS_DIR_.'tinymce.inc.js');


		$currency = $this->context->currency;

		$data->assign('languages',$languages);
		$this->object = $product;
		$this->display = 'edit';
		$content = '';

		$has_attribute = $product->hasAttributes();
		// @FIXME Stock, need to use StockManagerFactory
		$qty = 0;
		$cover = Product::getCover($product->id);
		$this->_applyTaxToEcotax($product);

		/*
		* Form for add a virtual product like software, mp3, etc...
		*/
		$productDownload = new ProductDownload();
		if ($id_product_download = $productDownload->getIdFromIdProduct($this->getFieldValue($product, 'id')))
			$productDownload = new ProductDownload($id_product_download);
		$product->{'productDownload'} = $productDownload;

		$this->displayInitInformationAndAttachment();


		$cache_default_attribute = (int) $this->getFieldValue($product, 'cache_default_attribute');
		$data->assign('feature_shop_active', Shop::isFeatureActive());
		// @todo : uses the helperform
		$data->assign('displayAssoShop', $this->displayAssoShop());
		$data->assign('carrier_list', $this->getCarrierList());


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
			'available_now', 'available_later', 'available_date', 'out_of_stock'
		);

		if(Configuration::get('PS_USE_ECOTAX'))
			array_push($product_props, 'ecotax');

		foreach($product_props as $prop)
			$product->$prop = $this->getFieldValue($product, $prop);

		$product->name['class'] = 'updateCurrentText';
		if (!$product->id)
			$product->name['class'] .= ' copy2friendlyUrl';

		$product->manufacturer_name = Manufacturer::getNameById($product->id_manufacturer);

		$data->assign('ps_dimension_unit', Configuration::get('PS_DIMENSION_UNIT'));
		$data->assign('ps_weight_unit', Configuration::get('PS_WEIGHT_UNIT'));
		// @todo : initPack should not be called like this
		$this->initPack($product);

		/*
		 * Form for add a virtual product like software, mp3, etc...
		 */
		$productDownload = new ProductDownload();
		if ($id_product_download = $productDownload->getIdFromIdProduct($this->getFieldValue($product, 'id')))
			$product->{'product_download'} = new ProductDownload($id_product_download);

		$this->displayInitInformationAndAttachment();
		// @todo price.js is used in information .. for now
		$this->addJs(_PS_JS_DIR_.'price.js');

		// @todo handle is_virtual with the value of the product
		$exists_file = realpath(_PS_DOWNLOAD_DIR_).'/'.$product->productDownload->filename;
		$data->assign('product_downloaded', $product->productDownload->id && !empty($product->productDownload->display_filename));

		if (!file_exists($exists_file)
			&& !empty($product->productDownload->display_filename)
			&& !empty($product->cache_default_attribute))
		{
			$msg = sprintf(Tools::displayError('This file "%s" is missing'), $product->productDownload->display_filename);
		}
		else
			$msg = '';

		$data->assign('download_product_file_missing', $msg);
		$data->assign('download_dir_writable', ProductDownload::checkWritableDir());

		if (empty($product->cache_default_attribute))
		{
			$data->assign('show_file_input', !strval(Tools::getValue('virtual_product_filename')) OR $product->productDownload->id > 0);
			// found in informations and combination : to merge
			$data->assign('up_filename', strval(Tools::getValue('virtual_product_filename')));
			$display_filename = ($product->productDownload->id > 0) ? $product->productDownload->display_filename : htmlentities(Tools::getValue('virtual_product_name'), ENT_COMPAT, 'UTF-8');

			if (!$product->productDownload->id || !$product->productDownload->active)
				$hidden = 'display:none;';
			else $hidden ='';

			$product->productDownload->nb_downloadable = ($product->productDownload->id > 0) ? $product->productDownload->nb_downloadable : htmlentities(Tools::getValue('virtual_product_nb_downloable'), ENT_COMPAT, 'UTF-8');
			$product->productDownload->date_expiration = ($product->productDownload->id > 0) ? ((!empty($product->productDownload->date_expiration) && $product->productDownload->date_expiration != '0000-00-00 00:00:00') ? date('Y-m-d', strtotime($product->productDownload->date_expiration)) : '' ) : htmlentities(Tools::getValue('virtual_product_expiration_date'), ENT_COMPAT, 'UTF-8');
			$product->productDownload->nb_days_accessible = ($product->productDownload->id > 0) ? $product->productDownload->nb_days_accessible : htmlentities(Tools::getValue('virtual_product_nb_days'), ENT_COMPAT, 'UTF-8');
			$product->productDownload->is_shareable = $product->productDownload->id > 0 && $product->productDownload->is_shareable;
		}
		else
		{
			$error ='';
			$product_attribute = ProductDownload::getAttributeFromIdProduct($this->getFieldValue($product, 'id'));
			foreach ($product_attribute as $p)
			{
				$productDownloadAttribute = new ProductDownload($p['id_product_download']);
				$exists_file2 = realpath(_PS_DOWNLOAD_DIR_).'/'.$productDownloadAttribute->filename;
				if (!file_exists($exists_file2) && !empty($productDownloadAttribute->id_product_attribute))
				{
					$msg = sprintf(Tools::displayError('This file "%s" is missing'), $productDownloadAttribute->display_filename);
					$error .= '<p class="alert" id="file_missing">
						<b>'.$msg.' :<br/>
						'.realpath(_PS_DOWNLOAD_DIR_) .'/'. $productDownloadAttribute->filename.'</b>
					</p>';
				}
			}
			$data->assign('error_product_download', $error);
		}

		// prices part
		$data->assign('currency', $currency = $this->context->currency);
		$data->assign('tax_rules_groups', TaxRulesGroup::getTaxRulesGroups(true));
		$data->assign('taxesRatesByGroup', TaxRulesGroup::getAssociatedTaxRatesByIdCountry($this->context->country->id));
		$data->assign('ecotaxTaxRate', Tax::getProductEcotaxRate());
		$data->assign('tax_exclude_taxe_option', Tax::excludeTaxeOption());

		$data->assign('ps_use_ecotax', Configuration::get('PS_USE_ECOTAX'));
		if ($product->unit_price_ratio != 0)
			$data->assign('unit_price', Tools::ps_round($product->price)/$product->unit_price_ratio);
		else
			$data->assign('unit_price', 0);

		$data->assign('ps_tax', Configuration::get('PS_TAX'));
		$data->assign('ps_stock_management', Configuration::get('PS_STOCK_MANAGEMENT'));
		$data->assign('has_attribute', $has_attribute);
		// Check if product has combination, to display the available date only for the product or for each combination
		if (Combination::isFeatureActive())
			$data->assign('countAttributes', (int)Db::getInstance()->getValue('SELECT COUNT(id_product) FROM '._DB_PREFIX_.'product_attribute WHERE id_product = '.(int)$product->id));

		$data->assign('ps_order_out_of_stock', Configuration::get('PS_ORDER_OUT_OF_STOCK'));

		$default_category = Tools::getValue('id_category', 1);
		$data->assign('default_category', $default_category);
		if (!$product->id)
		{
			$selectedCat = Category::getCategoryInformations(Tools::getValue('categoryBox', array($default_category)), $this->default_form_language);
		}
		else
		{
			if (Tools::isSubmit('categoryBox'))
				$selectedCat = Category::getCategoryInformations(Tools::getValue('categoryBox', array($default_category)), $this->default_form_language);
			else
				$selectedCat = Product::getProductCategoriesFull($product->id, $this->default_form_language);
		}
		$data->assign('selected_cat_ids', implode(',', array_keys($selectedCat)));
		$data->assign('selected_cat', $selectedCat);

		$trads = array(
			'Home' => $this->l('Home'),
			'selected' => $this->l('selected'),
			'Collapse All' => $this->l('Collapse All'),
			'Expand All' => $this->l('Expand All'),
			'Check All' => $this->l('Check All'),
			'Uncheck All'  => $this->l('Uncheck All'),
			'search' => $this->l('Search a category')
		);
		$data->assign('category_tree', Helper::renderAdminCategorieTree($trads, $selectedCat, 'categoryBox', false, true));

		$images = Image::getImages($this->context->language->id, $product->id);

		foreach($images as $k => $image)
			$images[$k]['src'] = $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id], $product->id.'-'.$image['id_image'], 'small');
		$data->assign('ps_ssl_enabled', Configuration::get('PS_SSL_ENABLED'));
		$data->assign('images', $images);
		$data->assign('imagesTypes', ImageType::getImagesTypes('products'));

			$accessories = Product::getAccessoriesLight($this->context->language->id, $product->id);

			if ($postAccessories = Tools::getValue('inputAccessories'))
			{
				$postAccessoriesTab = explode('-', Tools::getValue('inputAccessories'));
				foreach ($postAccessoriesTab as $accessoryId)
					if (!$this->haveThisAccessory($accessoryId, $accessories) && $accessory = Product::getAccessoryById($accessoryId))
						$accessories[] = $accessory;
			}
			$data->assign('accessories', $accessories);
			$product->tags = Tag::getProductTags($product->id);


						$this->addJqueryPlugin('autocomplete');

		// TinyMCE
		$iso_tiny_mce = $this->context->language->iso_code;
		$iso_tiny_mce = (file_exists(_PS_JS_DIR_.'tiny_mce/langs/'.$iso_tiny_mce.'.js') ? $iso_tiny_mce : 'en');
		$data->assign('ad', dirname($_SERVER["PHP_SELF"]));
		$data->assign('iso_tiny_mce', $iso_tiny_mce);
		$categoryBox = Tools::getValue('categoryBox', array());
		$data->assign('product', $product);
		$data->assign('last_content', $content);
		$data->assign('token', $this->token);
		$data->assign($this->tpl_form_vars);
		$data->assign('link', $this->context->link);
		$this->tpl_form_vars['product'] = $product;
		$this->tpl_form_vars['custom_form'] = $this->context->smarty->createTemplate($this->tpl_form, $data)->fetch();
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
			if (Validate::isLoadedObject($product = new Product((int)(Tools::getValue('id_product')))))
			{
				if (Tools::getValue('carriers'))
					$product->setCarriers(Tools::getValue('carriers'));
			}
		}
	}

	public function initFormImages($obj, $token = null)
	{
		$this->addJs('admin-dnd');
		$this->addJqueryPlugin('tablednd');

		$data = $this->context->smarty->createData();
		$data->assign('product', $this->loadObject());
		$content = '';
		if (!Tools::getValue('id_product'))
			return ''; // TEMPO
		global $attributeJs, $images;
		$shops = false;
		if (Shop::isFeatureActive())
			$shops = Shop::getShops();

		$data->assign('shops', $shops);
		$countImages = Db::getInstance()->getValue('SELECT COUNT(id_product) FROM '._DB_PREFIX_.'image WHERE id_product = '.(int)$obj->id);
		$data->assign('countImages', $countImages);

		$images = Image::getImages($this->context->language->id, $obj->id);
		$imagesTotal = Image::getImagesTotal($obj->id);
		$data->assign('id_product', (int)Tools::getValue('id_product'));
		$data->assign('id_category_default', (int)$this->_category->id);



		foreach ($images as $k => $image)
			$images[$k] = new Image($image['id_image']);

		$data->assign('images', $images);
		$data->assign('token', $token);
		$data->assign($this->tpl_form_vars);
		$data->assign('table', $this->table);
		$data->assign('max_image_size', $this->max_image_size);

		$data->assign('up_filename', strval(Tools::getValue('virtual_product_filename_attribute')));
		$data->assign('currency', $this->context->currency);

		$this->tpl_form_vars['custom_form'] = $this->context->smarty->createTemplate($this->tpl_form, $data)->fetch();
	}

	public function initFormCombinations($obj, $languages, $defaultLanguage)
	{
		return $this->initFormAttributes($obj, $languages, $defaultLanguage);
	}

	public function initFormAttributes($product, $languages, $defaultLanguage)
	{
		if (!Combination::isFeatureActive())
		{
			$this->displayWarning($this->l('This feature has been disabled, you can active this feature at this page:').' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
			return;
		}

		$smarty = $this->context->smarty;
		$content = '';

		$attributeJs = array();
		$attributes = Attribute::getAttributes($this->context->language->id, true);
		foreach ($attributes as $k => $attribute)
			$attributeJs[$attribute['id_attribute_group']][$attribute['id_attribute']] = $attribute['name'];
		$currency = $this->context->currency;
		$smarty->assign('attributeJs', $attributeJs);
		$smarty->assign('attributes_groups', AttributeGroup::getAttributesGroups($this->context->language->id));
		$default_country = new Country((int)Configuration::get('PS_COUNTRY_DEFAULT'));

		$product->productDownload = new ProductDownload();
		$id_product_download = (int) $product->productDownload->getIdFromIdProduct($this->getFieldValue($product, 'id'));
		if (!empty($id_product_download))
			$product->productDownload = new ProductDownload($id_product_download);

	//	$smarty->assign('productDownload', $productDownload);
		$smarty->assign('currency', $currency);

		$images = Image::getImages($this->context->language->id, $product->id);
		if ($product->id)
		{
			$smarty->assign('tax_exclude_option', Tax::excludeTaxeOption());
			$smarty->assign('ps_weight_unit', Configuration::get('PS_WEIGHT_UNIT'));

			$smarty->assign('ps_use_ecotax', Configuration::get('PS_USE_ECOTAX'));
			$smarty->assign('field_value_unity', $this->getFieldValue($product, 'unity'));

			$smarty->assign('reasons', $reasons = StockMvtReason::getStockMvtReasons($this->context->language->id));
			$smarty->assign('ps_stock_mvt_reason_default', $ps_stock_mvt_reason_default = Configuration::get('PS_STOCK_MVT_REASON_DEFAULT'));
			$smarty->assign('minimal_quantity', $this->getFieldValue($product, 'minimal_quantity') ? $this->getFieldValue($product, 'minimal_quantity') : 1);
			$smarty->assign('available_date', ($this->getFieldValue($product, 'available_date') != 0) ? stripslashes(htmlentities(Tools::displayDate($this->getFieldValue($product, 'available_date'), $language['id_lang']))) : '0000-00-00');
		  // date picker include


			$i = 0;
			$smarty->assign('imageType', ImageType::getByNameNType('small', 'products'));
			$smarty->assign('imageWidth', (isset($imageType['width']) ? (int)($imageType['width']) : 64) + 25);
			foreach ($images as $k => $image)
			{
				$images[$k]['obj'] = new Image($image['id_image']);
				++$i;
			}
			$smarty->assign('images', $images);
			$content .= '
		<div>
		<table>
		  <tr><td colspan="2"><div class="separation"></div></td></tr>
		  <tr>
			  <td colspan="2">
					<br />
					<table border="0" cellpadding="0" cellspacing="0" class="table">
						<tr>
							<th>'.$this->l('Attributes').'</th>
							<th>'.$this->l('Impact').'</th>
							<th>'.$this->l('Weight').'</th>
							<th>'.$this->l('Reference').'</th>
							<th>'.$this->l('EAN13').'</th>
							<th>'.$this->l('UPC').'</th>';

							if ($id_product_download && !empty($productDownload->display_filename))
							{
								$content .= '
								<th class="center virtual_header">'.$this->l('Filename').'</th>
								<th class="center virtual_header">'.$this->l('Number of downloads').'</th>
								<th class="center virtual_header">'.$this->l('Number of days').'</th>
								<th class="center virtual_header">'.$this->l('Share').'</th>';
							}

							$content .= '<th class="center">'.$this->l('Actions').'</th>
						</tr>';
			if ($product->id)
			{
				/* Build attributes combinaisons */
				$combinaisons = $product->getAttributeCombinaisons($this->context->language->id);
				$groups = array();
				if (is_array($combinaisons))
				{
					$combinationImages = $product->getCombinationImages($this->context->language->id);
					foreach ($combinaisons as $k => $combinaison)
					{
						$combArray[$combinaison['id_product_attribute']]['wholesale_price'] = $combinaison['wholesale_price'];
						$combArray[$combinaison['id_product_attribute']]['price'] = $combinaison['price'];
						$combArray[$combinaison['id_product_attribute']]['weight'] = $combinaison['weight'];
						$combArray[$combinaison['id_product_attribute']]['unit_impact'] = $combinaison['unit_price_impact'];
						$combArray[$combinaison['id_product_attribute']]['reference'] = $combinaison['reference'];
						$combArray[$combinaison['id_product_attribute']]['ean13'] = $combinaison['ean13'];
						$combArray[$combinaison['id_product_attribute']]['upc'] = $combinaison['upc'];
						$combArray[$combinaison['id_product_attribute']]['minimal_quantity'] = $combinaison['minimal_quantity'];
						$combArray[$combinaison['id_product_attribute']]['available_date'] = strftime($combinaison['available_date']);
						$combArray[$combinaison['id_product_attribute']]['id_image'] = isset($combinationImages[$combinaison['id_product_attribute']][0]['id_image']) ? $combinationImages[$combinaison['id_product_attribute']][0]['id_image'] : 0;
						$combArray[$combinaison['id_product_attribute']]['default_on'] = $combinaison['default_on'];
						$combArray[$combinaison['id_product_attribute']]['ecotax'] = $combinaison['ecotax'];
						$combArray[$combinaison['id_product_attribute']]['attributes'][] = array($combinaison['group_name'], $combinaison['attribute_name'], $combinaison['id_attribute']);
						if ($combinaison['is_color_group'])
							$groups[$combinaison['id_attribute_group']] = $combinaison['group_name'];
					}
				}
				$irow = 0;
				if (isset($combArray))
				{
					foreach ($combArray as $id_product_attribute => $product_attribute)
					{
						$list = '';
						$jsList = '';

						/* In order to keep the same attributes order */
						asort($product_attribute['attributes']);

						foreach ($product_attribute['attributes'] as $attribute)
						{
							$list .= addslashes(htmlspecialchars($attribute[0])).' - '.addslashes(htmlspecialchars($attribute[1])).', ';
							$jsList .= '\''.addslashes(htmlspecialchars($attribute[0])).' : '.addslashes(htmlspecialchars($attribute[1])).'\', \''.$attribute[2].'\', ';
						}
						$list = rtrim($list, ', ');
						$jsList = rtrim($jsList, ', ');
						$attrImage = $product_attribute['id_image'] ? new Image($product_attribute['id_image']) : false;
						$available_date = ($product_attribute['available_date'] != 0) ? date('Y-m-d', strtotime($product_attribute['available_date'])) : '0000-00-00';

						$id_product_download = $product->productDownload->getIdFromIdAttribute((int) $product->id, (int) $id_product_attribute);
						if ($id_product_download)
							$product->productDownload = new ProductDownload($id_product_download);

						$available_date_attribute = substr($product->productDownload->date_expiration, 0, -9);

						if ($available_date_attribute == '0000-00-00')
							$available_date_attribute = '';

						if ($product->productDownload->is_shareable == 1)
							$is_shareable = $this->l('Yes');
						else
							$is_shareable = $this->l('No');

						$content .= '
						<tr'.($irow++ % 2 ? ' class="alt_row"' : '').($product_attribute['default_on'] ? ' style="background-color:#D1EAEF"' : '').'>
							<td>'.stripslashes($list).'</td>
							<td class="right">'.($currency->format % 2 != 0 ? $currency->sign.' ' : '').$product_attribute['price'].($currency->format % 2 == 0 ? ' '.$currency->sign : '').'</td>
							<td class="right">'.$product_attribute['weight'].Configuration::get('PS_WEIGHT_UNIT').'</td>
							<td class="right">'.$product_attribute['reference'].'</td>
							<td class="right">'.$product_attribute['ean13'].'</td>
							<td class="right">'.$product_attribute['upc'].'</td>';

							if ($id_product_download && !empty($product->productDownload->display_filename))
							{
								$content .= '<td class="right">'.$product->productDownload->getHtmlLink(false, true).'</td>
								<td class="center">'.$product->productDownload->nb_downloadable.'</td>
								<td class="center">'.$product->productDownload->nb_downloadable.'</td>
								<td class="right">'.$is_shareable.'</td>';
							}

							$exists_file = realpath(_PS_DOWNLOAD_DIR_).'/'.$product->productDownload->filename;

							if ($product->productDownload->id && file_exists($exists_file))
								$filename = $product->productDownload->filename;
							else
								$filename = '';
							// @todo : a better way to "fillCombinaison" maybe ?
							$content .= '<td class="center">
							<a style="cursor: pointer;">
							<img src="../img/admin/edit.gif" alt="'.$this->l('Modify this combination').'"
							onclick="javascript:fillCombinaison(\''.$product_attribute['wholesale_price'].'\', \''.$product_attribute['price'].'\', \''.$product_attribute['weight'].'\', \''.$product_attribute['unit_impact'].'\', \''.$product_attribute['reference'].'\', \''.$product_attribute['ean13'].'\',
							\'0\', \''.($attrImage ? $attrImage->id : 0).'\', Array('.$jsList.'), \''.$id_product_attribute.'\', \''.$product_attribute['default_on'].'\', \''.$product_attribute['ecotax'].'\', \''.$product_attribute['upc'].'\', \''.$product_attribute['minimal_quantity'].'\', \''.$available_date.'\',
							\''.$product->productDownload->display_filename.'\', \''.$filename.'\', \''.$product->productDownload->nb_downloadable.'\', \''.$available_date_attribute.'\',  \''.$product->productDownload->nb_days_accessible.'\',  \''.$product->productDownload->is_shareable.'\'); calcImpactPriceTI();" /></a>&nbsp;
							'.(!$product_attribute['default_on'] ? '<a href="'.self::$currentIndex.'&defaultProductAttribute&id_product_attribute='.$id_product_attribute.'&id_product='.$product->id.'&'.(Tools::isSubmit('id_category') ? 'id_category='.(int)(Tools::getValue('id_category')).'&' : '&').'token='.Tools::getAdminToken('AdminProducts'.(int)(Tab::getIdFromClassName('AdminProducts')).$this->context->employee->id).'">
							<img src="../img/admin/asterisk.gif" alt="'.$this->l('Make this the default combination').'" title="'.$this->l('Make this combination the default one').'"></a>' : '').'
							<a href="'.self::$currentIndex.'&deleteProductAttribute&id_product_attribute='.$id_product_attribute.'&id_product='.$product->id.'&'.(Tools::isSubmit('id_category') ? 'id_category='.(int)(Tools::getValue('id_category')).'&' : '&').'token='.Tools::getAdminToken('AdminProducts'.(int)(Tab::getIdFromClassName('AdminProducts')).(int)$this->context->employee->id).'" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');">
							<img src="../img/admin/delete.gif" alt="'.$this->l('Delete this combination').'" /></a></td>
						</tr>';
					}
					$content .= '<tr><td colspan="7" align="center"><a href="'.self::$currentIndex.'&deleteAllProductAttributes&id_product='.$product->id.'&token='.Tools::getAdminToken('AdminProducts'.(int)(Tab::getIdFromClassName('AdminProducts')).(int)$this->context->employee->id).'" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete this combination').'" /> '.$this->l('Delete all combinations').'</a></td></tr>';
				}
				else
					$content .= '<tr><td colspan="7" align="center"><i>'.$this->l('No combination yet').'.</i></td></tr>';
			}
			$content .= '
						</table>
						<br />'.$this->l('The row in blue is the default combination.').'
						<br />
						'.$this->l('A default combination must be designated for each product.').'
						</td>
						</tr>
					</table>
					<script type="text/javascript">
						var impact = getE(\'attribute_price_impact\');
						var impact2 = getE(\'attribute_weight_impact\');

						var s_attr_group = $(\'#span_new_group\');
						var s_attr_name = $(\'#span_new_attr\');
						var s_impact = $(\'#span_impact\');
						var s_impact2 = $(\'#span_weight_impact\');

						init_elems();
					</script>';
				}
				else
					$content .= '<b>'.$this->l('You must save this product before adding combinations').'.</b>';
		// @todo
		$smarty->assign('up_filename', strval(Tools::getValue('virtual_product_filename_attribute')));
		$this->context->smarty->assign($this->tpl_form_vars);
		$this->context->smarty->assign(array(
			'content' => $content,
			'product' => $product,
			'id_category' => $product->id_category_default,
			'token_generator' => Tools::getAdminTokenLite('AdminAttributeGenerator')
		));
		$this->tpl_form_vars['custom_form'] = $this->context->smarty->fetch('products/combinations.tpl');
	}

	public function initFormQuantities($obj)
	{
		$data = $this->context->smarty->createData();

		if ($this->object->id)
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
				$product_designation[$attribute['id_product_attribute']] = rtrim($obj->name[$this->context->language->id].' - '.$attribute['attribute_designation'], ' - ');
			}

			$data->assign(array(
				'attributes' => $attributes,
				'available_quantity' => $available_quantity,
				'stock_management_active' => Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
				'product_designation' => $product_designation,
				'product' => $this->object,
				'token_preferences' => Tools::getAdminTokenLite('AdminPPreferences'),
				'token' => $this->token
			));
		}
		else
			$data->assign('content', '<b>'.$this->l('You must save this product before manage quantities').'.</b>');

		$this->tpl_form_vars['custom_form'] = $this->context->smarty->createTemplate($this->tpl_form, $data)->fetch();
	}

	public function initFormSuppliers($obj)
	{
		$data = $this->context->smarty->createData();

		if ($this->object->id)
		{
			// Get all id_product_attribute
			$attributes = $obj->getAttributesResume($this->context->language->id);
			if (empty($attributes))
				$attributes[] = array(
					'id_product' => $this->object->id,
					'id_product_attribute' => 0,
					'attribute_designation' => ''
				);

			$product_designation = array();

			foreach ($attributes as $attribute)
				$product_designation[$attribute['id_product_attribute']] = rtrim($obj->name[$this->context->language->id].' - '.$attribute['attribute_designation'], ' - ');

			// Get all available suppliers
			$suppliers = Supplier::getSuppliers();

			// Get already associated suppliers
			$associated_suppliers = ProductSupplier::getSupplierCollection($this->object->id);

			// Get already associated suppliers and force to retreive product declinaisons
			$product_supplier_collection = ProductSupplier::getSupplierCollection($this->object->id, false);

			$default_supplier = 0;

			foreach ($suppliers as &$supplier)
			{
				$supplier['is_selected'] = false;
				$supplier['is_default'] = false;

				foreach ($associated_suppliers as &$associated_supplier)
					if ($associated_supplier->id_supplier == $supplier['id_supplier'])
					{
						$associated_supplier->name = $supplier['name'];
						$supplier['is_selected'] = true;

						if ($this->object->id_supplier == $supplier['id_supplier'])
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
				'product' => $this->object,
				'link' => $this->context->link,
				'token' => $this->token
			));
		}
		else
			$data->assign('content', '<b>'.$this->l('You must save this product before manage suppliers').'.</b>');

		$this->tpl_form_vars['custom_form'] = $this->context->smarty->createTemplate($this->tpl_form, $data)->fetch();
	}

	public function initFormWarehouses($obj)
	{
		$data = $this->context->smarty->createData();

		if ($this->object->id)
		{
			// Get all id_product_attribute
			$attributes = $obj->getAttributesResume($this->context->language->id);
			if (empty($attributes))
				$attributes[] = array(
					'id_product_attribute' => 0,
					'attribute_designation' => ''
				);

			$product_designation = array();

			foreach ($attributes as $attribute)
				$product_designation[$attribute['id_product_attribute']] = rtrim($obj->name[$this->context->language->id].' - '.$attribute['attribute_designation'], ' - ');

			// Get all available warehouses
			$warehouses = Warehouse::getWarehouses(true);

			// Get already associated warehouses
			$associated_warehouses_collection = WarehouseProductLocation::getCollection($this->object->id);

			$data->assign(array(
				'attributes' => $attributes,
				'warehouses' => $warehouses,
				'associated_warehouses' => $associated_warehouses_collection,
				'product_designation' => $product_designation,
				'product' => $this->object,
				'link' => $this->context->link,
				'token' => $this->token
			));
		}
		else
			$data->assign('content', '<b>'.$this->l('You must save this product before manage warehouses').'.</b>');

		$this->tpl_form_vars['custom_form'] = $this->context->smarty->createTemplate($this->tpl_form, $data)->fetch();
	}

	public function initFormFeatures($obj)
	{
		if (!Feature::isFeatureActive())
		{
			$this->displayWarning($this->l('This feature has been disabled, you can active this feature at this page:').' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
			return;
		}

		$data = $this->context->smarty->createData();
		if ($obj->id)
		{
			$features = Feature::getFeatures($this->context->language->id);

			foreach ($features as $k => $tab_features)
			{
				$features[$k]['current_item'] = false;
				$features[$k]['val'] = array();

				$custom = true;
				foreach ($this->object->getFeatures() as $tab_products)
					if ($tab_products['id_feature'] == $tab_features['id_feature'])
						$features[$k]['current_item'] = $tab_products['id_feature_value'];

				$features[$k]['featureValues'] = FeatureValue::getFeatureValuesWithLang($this->context->language->id, (int)$tab_features['id_feature']);
				if (sizeof($features[$k]['featureValues']))
					foreach ($features[$k]['featureValues'] as $value)
						if ($features[$k]['current_item'] == $value['id_feature_value'])
							$custom = false;

				if($custom)
					$features[$k]['val'] = FeatureValue::getFeatureValueLang($features[$k]['current_item']);
			}
		}

		$data->assign('available_features', $features);

		$data->assign('product', $obj);
		$data->assign('link', $this->context->link);
		$data->assign('languages', $this->_languages);
		$data->assign('default_form_language', $this->default_form_language);
		$this->tpl_form_vars['custom_form'] = $this->context->smarty->createTemplate($this->tpl_form, $data)->fetch();
	}

	public function ajaxProcessProductQuantity()
	{
		if(!Tools::getValue('actionQty'))
			return Tools::jsonEncode(array('error' => 'Undefined action'));

		$product = new Product((int)(Tools::getValue('id_product')));
		switch(Tools::getValue('actionQty'))
		{
			case 'depends_on_stock':
				if (Tools::getValue('value') === false)
					return Tools::jsonEncode(array('error' => 'Undefined value'));
				if ((int)Tools::getValue('value') != 0 && (int)Tools::getValue('value') != 1)
					return Tools::jsonEncode(array('error' => 'Uncorrect value'));

				StockAvailable::setProductDependsOnStock($product->id, (int)Tools::getValue('value'));
				break;

			case 'out_of_stock':
				if (Tools::getValue('value') === false)
					return Tools::jsonEncode(array('error' => 'Undefined value'));
				if (!in_array((int)Tools::getValue('value'), array(0, 1, 2)))
					return Tools::jsonEncode(array('error' => 'Uncorrect value'));

				StockAvailable::setProductOutOfStock($product->id, (int)Tools::getValue('value'));
				break;

			case 'set_qty':
				if (Tools::getValue('value') === false)
					return Tools::jsonEncode(array('error' => 'Undefined value'));
				if (Tools::getValue('id_product_attribute') === false)
					return Tools::jsonEncode(array('error' => 'Undefined id product attribute'));
				// @todo : Product class should handle that
				$stock_available = new StockAvailable(StockAvailable::getStockAvailableIdByProductId($product->id, (int)Tools::getValue('id_product_attribute')));
				if (!$stock_available->id)
				{
					$stock_available->id_product = $product->id;
					$stock_available->id_shop = Context::getContext()->shop->getID(true);
					$stock_available->id_product_attribute = Tools::getValue('id_product_attribute');
				}
				$stock_available->quantity = (int)Tools::getValue('value');
				$stock_available->save();
				break;
		}
		die(Tools::jsonEncode(array('error' => false)));
	}

	public function getLineTableImage($image, $imagesTotal, $token, $shops)
	{
		if (Shop::isFeatureActive())
			$imgObj = new Image((int)$image['id_image']);
		$image_obj = new Image($image['id_image']);
		$img_path = $image_obj->getExistingImgPath();
		$html =  '
			<tr id="tr_'.$image_obj->id.'">
				<td style="padding: 4px;"><a href="'._THEME_PROD_DIR_.$img_path.'.jpg" target="_blank">
					<img src="'._THEME_PROD_DIR_.$img_path.'-small.jpg'.((int)(Tools::getValue('image_updated')) === (int)($image['id_image']) ? '?date='.time() : '').'"
					alt="'.htmlentities(stripslashes($image['legend']), ENT_COMPAT, 'UTF-8').'" title="'.htmlentities(stripslashes($image['legend']), ENT_COMPAT, 'UTF-8').'" /></a>
				</td>
				<td class="center positionImage">'.(int)($image['position']).'</td>
				<td id="td_'.$image_obj->id.'" class="pointer dragHandle center">';

		$html .= '
				<a '.($image['position'] == 1 ? ' style="display: none;"' : '').' href="'.self::$currentIndex.'&id_image='.$image['id_image'].'&imgPosition='.($image['position'] - 1).'&imgDirection=0&token='.($token ? $token : $this->token).'"><img src="../img/admin/up.gif" alt="" border="0"></a>
				<a '.($image['position'] == $imagesTotal ? ' style="display: none;"' : '').' href="'.self::$currentIndex.'&id_image='.$image['id_image'].'&imgPosition='.($image['position'] + 1).'&imgDirection=1&token='.($token ? $token : $this->token).'"><img src="../img/admin/down.gif" alt="" border="0"></a>';
		$html .= '
				</td>';
		if (Shop::isFeatureActive())
			foreach ($shops as $shop)
				$html .= '
				<td class="center"><input type="checkbox" class="image_shop" name="'.(int)$image['id_image'].'" value="'.(int)$shop['id_shop'].'" '.($imgObj->isAssociatedToShop($shop['id_shop']) ? 'checked="1"' : '').' /></td>';
		$html .= '
				<td class="center"><a href="'.self::$currentIndex.'&id_image='.$image['id_image'].'&coverImage&token='.($token ? $token : $this->token).'"><img class="covered" src="../img/admin/'.($image['cover'] ? 'enabled.gif' : 'forbbiden.gif').'" alt="" /></a></td>
				<td class="center">
				<a href="#" class="delete_product_image"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete this image').'" title="'.$this->l('Delete this image').'" /></a>
				</td>
			</tr>';
		$json = array(
			'status' => 'ok',
			'id'=>$image_obj->id,
			'path' => _THEME_PROD_DIR_.$img_path.'.jpg',
			'path_small' => _THEME_PROD_DIR_.$img_path.'-small.jpg',
			'position' => $image['position'],
			'cover' => $image['cover'],
			'html' => $html
			);

		return $this->content = Tools::jsonEncode($json);
	//	return $html;
	}

	public function getCombinationImagesJS()
	{
		if (!($obj = $this->loadObject(true)))
			return;

		$content = 'var combination_images = new Array();';
		if (!$allCombinationImages = $obj->getCombinationImages($this->context->language->id))
			return $content;
		foreach ($allCombinationImages as $id_product_attribute => $combinationImages)
		{
			$i = 0;
			$content .= 'combination_images['.(int)($id_product_attribute).'] = new Array();';
			foreach ($combinationImages as $combinationImage)
				$content .= 'combination_images['.(int)($id_product_attribute).']['.$i++.'] = '.(int)($combinationImage['id_image']).';';
		}
		return $content;
	}

	public function haveThisAccessory($accessoryId, $accessories)
	{
		foreach ($accessories as $accessory)
			if ((int)($accessory['id_product']) == (int)($accessoryId))
				return true;
		return false;
	}

	private function initPack(Product $product)
	{
		$this->tpl_form_vars['is_pack'] = ($product->id && Pack::isPack($product->id)) || Tools::getValue('ppack');
		$product->packItems = Pack::getItems($product->id, $this->context->language->id);

		$input_pack_items = '';
		if(Tools::getValue('inputPackItems'))
			$input_pack_items = Tools::getValue('inputPackItems');
		else
			foreach ($product->packItems as $packItem)
				$input_pack_items .= $packItem->pack_quantity.'x'.$packItem->id.'-';
		$this->tpl_form_vars['input_pack_items'] = $input_pack_items;

		$input_namepack_items = '';
		if (Tools::getValue('namePackItems'))
			$input_namepack_items = Tools::getValue('namePackItems');
		else
			foreach ($product->packItems as $packItem)
				$input_namepack_items.= $packItem->pack_quantity.' x '.$packItem->name.'¤';
		$this->tpl_form_vars['input_namepack_items'] = $input_namepack_items;
	}

	public function updatePackItems($product)
	{
		Pack::deleteItems($product->id);

		// lines format: QTY x ID-QTY x ID
		if (Tools::getValue('ppack'))
		{
			$items = Tools::getValue('inputPackItems');
			$lines = array_unique(explode('-', $items));
			// lines is an array of string with format : QTYxID
			if(count($lines))
				foreach ($lines as $line)
					if (!empty($line))
					{
						list($qty, $item_id) = explode('x', $line);
						if ($qty > 0 && isset($item_id))
						{
							if (!Pack::addItem((int)($product->id), (int)($item_id), (int)($qty)))
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

	/**
	 * Load class object using identifier in $_GET (if possible)
	 * otherwise return an empty object, or die
	 *
	 * @param boolean $opt Return an empty object if load fail
	 * @return object
	 */
	protected function loadObject($opt = false)
	{
		if ($id = (int)(Tools::getValue($this->identifier)) AND Validate::isUnsignedId($id))
		{
			if (!$this->object)
				$this->object = new $this->className($id);
			if (Validate::isLoadedObject($this->object))
				return $this->object;
			$this->_errors[] = Tools::displayError('Object cannot be loaded (not found)');
		}
		else if ($opt)
		{
			$this->object = new $this->className();
			return $this->object;
		}
		else
			$this->_errors[] = Tools::displayError('Object cannot be loaded (identifier missing or invalid)');

		$this->displayErrors();
		return $this->object;
	}

	public function displayInitInformationAndAttachment()
	{
		$this->addJqueryPlugin('thickbox');
		$this->addJqueryPlugin('ajaxfileupload');
		$this->addJqueryPlugin('date');
	}
}
