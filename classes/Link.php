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
*  @version  Release: $Revision: 7465 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class LinkCore
{
	/** @var boolean Rewriting activation */
	protected $allow;
	protected $url;
	public static $cache = array('page' => array());
	
	public $protocol_link;
	public $protocol_content;

	/**
	  * Constructor (initialization only)
	  */
	public function __construct($protocol_link = null, $protocol_content = null)
	{
		$this->allow = (int)Configuration::get('PS_REWRITING_SETTINGS');
		$this->url = $_SERVER['SCRIPT_NAME'];
		$this->protocol_link = $protocol_link;
		$this->protocol_content = $protocol_content;
	}

	/**
	 * Create a link to delete a product
	 *
	 * @param mixed $product ID of the product OR a Product object
	 * @param int $id_picture ID of the picture to delete
	 * @return string
	 */
	public function getProductDeletePictureLink($product, $id_picture)
	{
		$url = $this->getProductLink($product);
		return $url.((strpos($url, '?')) ? '&' : '?').'&deletePicture='.$id_picture;
	}

	/**
	 * Create a link to a product
	 *
	 * @param mixed $id_product ID of the product OR a Product object
	 * @param string $alias If $id_product is not a object, this argument is same as obj->link_rewrite
	 * @param string $category If $id_product is not a object, name of the product category
	 * @param string $ean13
	 * @param int $id_lang
	 * @param int $id_shop (since 1.5.0) ID shop need to be used when we generate a product link for a product in a cart
	 * @return string
	 */
	public function getProductLink($id_product, $alias = null, $category = null, $ean13 = null, $id_lang = null, $id_shop = null)
	{
		$url = _PS_BASE_URL_.__PS_BASE_URI__;
		
		// @todo use specific method ?
		if ($id_shop && ($shop = Shop::getShop($id_shop)))
			$url = 'http://'.$shop['domain'].'/'.$shop['uri'].$this->getLangLink($id_lang);
			
		if (is_object($id_product))
		{
			$product = clone($id_product);
			$id_product = $product->id;
			$category = $product->category;
			$alias = $product->link_rewrite;
			$ean13 = $product->ean13;
		}
		
		if ($category AND $category != 'home')
			return $url.Dispatcher::getInstance()->createUrl('product_rule2', array(
				'id_product' =>	$id_product,
				'text1' =>		$category,
				'text2' =>		$alias.(($ean13) ? '-'.$ean13 : ''),
			), ($alias && $this->allow));
		else
			return $url.Dispatcher::getInstance()->createUrl('product_rule', array(
				'id_product' =>	$id_product,
				'text' =>		(($alias) ? $alias : '').(($ean13) ? '-'.$ean13 : ''),
			), ($alias && $this->allow));
	}

	/**
	 * Create a link to a category
	 *
	 * @param mixed $id_category ID of the category OR a Category object
	 * @param string $alias If $id_category is not a object, this argument is same as obj->link_rewrite
	 * @param int $id_lang
	 * @return string
	 */
	public function getCategoryLink($id_category, $alias = NULL, $id_lang = NULL)
	{
		$url = _PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink($id_lang);
		if (is_object($id_category))
		{
			$category = clone($id_category);
			$id_category = $category->id;
			$alias = $category->link_rewrite;
		}

		return $url.Dispatcher::getInstance()->createUrl('category_rule', array(
			'id_category' =>	$id_category,
			'text' =>			($alias) ? $alias : '',
		), ($alias && $this->allow));
	}

	/**
	 * Create a link to a CMS category
	 *
	 * @param mixed $id_category ID of the category OR a CmsCategory object
	 * @param string $alias If $id_category is not a object, this argument is same as obj->link_rewrite
	 * @param int $id_lang
	 * @return string
	 */
	public function getCMSCategoryLink($id_category, $alias = NULL, $id_lang = NULL)
	{
		$url = _PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink($id_lang);
		if (is_object($id_category))
		{
			$category = clone($id_category);
			$id_category = $category->id;
			$alias = $category->link_rewrite;
		}

		return $url.Dispatcher::getInstance()->createUrl('cms_category_rule', array(
			'id_cms_category' =>	$id_category,
			'text' =>				($alias) ? $alias : '',
		), ($alias && $this->allow));
	}

	/**
	 * Create a link to a CMS page
	 *
	 * @param mixed $cms ID of the CMS page OR a Cms object
	 * @param string $alias If $cms is not a object, this argument is same as obj->link_rewrite
	 * @param bool $ssl
	 * @param int $id_lang
	 * @return string
	 */
	public function getCMSLink($cms, $alias = null, $ssl = false, $id_lang = NULL)
	{
		$base = (($ssl AND Configuration::get('PS_SSL_ENABLED')) ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true));
		$url = $base.__PS_BASE_URI__.$this->getLangLink($id_lang);

		if (is_object($cms))
		{
			$id_cms = $cms->id;
			$alias = $cms->link_rewrite;
		}
		else
			$id_cms = $cms;

		return $url.Dispatcher::getInstance()->createUrl('cms_rule', array(
			'id_cms' =>	$id_cms,
			'text' =>	($alias) ? $alias : '',
		), ($alias && $this->allow));
	}

	/**
	 * Create a link to a supplier
	 *
	 * @param mixed $id_supplier ID of the supplier page OR a Supplier object
	 * @param string $alias If $id_supplier is not a object, this argument is same as obj->link_rewrite
	 * @param int $id_lang
	 * @return string
	 */
	public function getSupplierLink($id_supplier, $alias = NULL, $id_lang = NULL)
	{
		$url = _PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink($id_lang);
		if (is_object($id_supplier))
		{
			$supplier = clone($id_supplier);
			$id_supplier = $supplier->id;
			$alias = $supplier->link_rewrite;
		}

		return $url.Dispatcher::getInstance()->createUrl('supplier_rule', array(
			'id_supplier' =>	$id_supplier,
			'text' =>			($alias) ? $alias : '',
		), ($alias && $this->allow));
	}

	/**
	 * Create a link to a manufacturer
	 *
	 * @param mixed $id_manufacturer ID of the manufacturer page OR a Supplier object
	 * @param string $alias If $id_manufacturer is not a object, this argument is same as obj->link_rewrite
	 * @param int $id_lang
	 * @return string
	 */
	public function getManufacturerLink($id_manufacturer, $alias = NULL, $id_lang = NULL)
	{
		$url = _PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink($id_lang);
		if (is_object($id_manufacturer))
		{
			$manufacturer = clone($id_manufacturer);
			$id_manufacturer = $manufacturer->id;
			$alias = $manufacturer->link_rewrite;
		}

		return $url.Dispatcher::getInstance()->createUrl('manufacturer_rule', array(
			'id_manufacturer' =>	$id_manufacturer,
			'text' =>				($alias) ? $alias : '',
		), ($alias && $this->allow));
	}

	/**
	 * Returns a link to a product image for display
	 * Note: the new image filesystem stores product images in subdirectories of img/p/
	 * 
	 * @param string $name rewrite link of the image
	 * @param string $ids id part of the image filename - can be "id_product-id_image" (legacy support, recommended) or "id_image" (new)
	 * @param string $type
	 */
	public function getImageLink($name, $ids, $type = NULL)
	{
		// legacy mode
		if (Configuration::get('PS_LEGACY_IMAGES') 
			&& (file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').'.jpg')))
		{
		if ($this->allow == 1)
			$uri_path = __PS_BASE_URI__.$ids.($type ? '-'.$type : '').'/'.$name.'.jpg';
		else
			$uri_path = _THEME_PROD_DIR_.$ids.($type ? '-'.$type : '').'.jpg';
		}else
		{
			// if ids if of the form id_product-id_image, we want to extract the id_image part
			$split_ids = explode('-', $ids);
			$id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
			
			if ($this->allow == 1)
				$uri_path = __PS_BASE_URI__.$id_image.($type ? '-'.$type : '').'/'.$name.'.jpg';
			else
				$uri_path = _THEME_PROD_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').'.jpg';
		}
		
		return $this->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
	}

	public function getMediaLink($filepath)
	{
		return Tools::getProtocol().Tools::getMediaServer($filepath).$filepath;
	}

	/**
	 * Create a simple link
	 * 
	 * @param string $controller
	 * @param bool $ssl
	 * @param int $id_lang
	 * @param string $request
	 * @param Context $context
	 */
	public function getPageLink($controller, $ssl = false, $id_lang = null, $request = null)
	{
		$controller = str_replace('.php', '', $controller);

		if (!$id_lang)
			$id_lang = (int)Context::getContext()->language->id;

		$uri_path = Dispatcher::getInstance()->createUrl($controller);
		$url = ($ssl AND Configuration::get('PS_SSL_ENABLED')) ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true);
		$url .= __PS_BASE_URI__.$this->getLangLink($id_lang).ltrim($uri_path, '/');
		$url .= ($request ? (($this->allow ? '?' : '&').trim($request)) : '');

		return $url;
	}

	public function getCatImageLink($name, $id_category, $type = null)
	{
		return ($this->allow == 1) ? (__PS_BASE_URI__.'c/'.$id_category.($type ? '-'.$type : '').'/'.$name.'.jpg') : (_THEME_CAT_DIR_.$id_category.($type ? '-'.$type : '').'.jpg');
	}

	/**
	  * Create link after language change, for the change language block
	  *
	  * @param integer $id_lang Language ID
	  * @return string link
	  */
	public function getLanguageLink($id_lang, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		$matches = array();
		$request = $_SERVER['REQUEST_URI'];
		preg_match('#^/([a-z]{2})/([^\?]*).*$#', $request, $matches);
		if ($matches)
		{
			$current_iso = $matches[1];
			$rewrite = $matches[2];
			$url_rewrite = Meta::getEquivalentUrlRewrite($id_lang, Language::getIdByIso($current_iso), $rewrite);
			$request = str_replace($rewrite, $url_rewrite, $request);
		}

		$queryTab = array();
		parse_str($_SERVER['QUERY_STRING'], $queryTab);
		unset($queryTab['isolang']);
		$query = http_build_query($queryTab);

		if (!empty($query) OR !$this->allow)
			$query = '?'.$query;

		$switchLangLink = $this->getPageLink(substr($_SERVER['PHP_SELF'], strlen(__PS_BASE_URI__)), false, $id_lang).$query;
		if (!$this->allow)
			if ($id_lang != $context->language->id)
			{
				if (strpos($switchLangLink,'id_lang'))
					$switchLangLink = preg_replace('`id_lang=[0-9]*`','id_lang='.$id_lang,$switchLangLink);
				else
					$switchLangLink = $switchLangLink.'&amp;id_lang='.$id_lang;
			}
		return $switchLangLink;
	}

	public function goPage($url, $p)
	{
		return $url.($p == 1 ? '' : (!strstr($url, '?') ? '?' : '&amp;').'p='.(int)($p));
	}

	public function getPaginationLink($type, $id_object, $nb = false, $sort = false, $pagination = false, $array = false)
	{
		if ($type AND $id_object)
			$url = $this->{'get'.$type.'Link'}($id_object, NULL);
		else
		{
			$url = $this->url;
			if (Configuration::get('PS_REWRITING_SETTINGS'))
				$url = $this->getPageLink(basename($url));
		}
		$vars = (!$array ? '' : array());
		$varsNb = array('n', 'search_query');
		$varsSort = array('orderby', 'orderway');
		$varsPagination = array('p');

		$n = 0;
		foreach ($_GET AS $k => $value)
			if ($k != 'id_'.$type)
			{
				if (Configuration::get('PS_REWRITING_SETTINGS') AND ($k == 'isolang' OR $k == 'id_lang'))
					continue;
				$ifNb = (!$nb OR ($nb AND !in_array($k, $varsNb)));
				$ifSort = (!$sort OR ($sort AND !in_array($k, $varsSort)));
				$ifPagination = (!$pagination OR ($pagination AND !in_array($k, $varsPagination)));
				if ($ifNb AND $ifSort AND $ifPagination AND !is_array($value))
					!$array ? ($vars .= ((!$n++ AND ($this->allow == 1 OR $url == $this->url)) ? '?' : '&').urlencode($k).'='.urlencode($value)) : ($vars[urlencode($k)] = urlencode($value));
			}
		if (!$array)
			return $url.$vars;
		$vars['requestUrl'] = $url;
		if ($type AND $id_object)
			$vars['id_'.$type] = (is_object($id_object) ? (int)$id_object->id : (int)$id_object);
		return $vars;
	}

	public function addSortDetails($url, $orderby, $orderway)
	{
		return $url.(!strstr($url, '?') ? '?' : '&').'orderby='.urlencode($orderby).'&orderway='.urlencode($orderway);
	}

	protected function getLangLink($id_lang = NULL, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		if (!$this->allow OR Language::countActiveLanguages() <= 1)
			return '';

		if (!$id_lang)
			$id_lang = $context->language->id;

		return Language::getIsoById($id_lang).'/';
	}
}
