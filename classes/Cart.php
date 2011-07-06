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
*  @version  Release: $Revision: 7506 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CartCore extends ObjectModel
{
	public		$id;
	
	public		$id_group_shop;
	
	public 		$id_shop;
	
	/** @var integer Customer delivery address ID */
	public 		$id_address_delivery;

	/** @var integer Customer invoicing address ID */
	public 		$id_address_invoice;

	/** @var integer Customer currency ID */
	public 		$id_currency;

	/** @var integer Customer ID */
	public 		$id_customer;

	/** @var integer Guest ID */
	public 		$id_guest;

	/** @var integer Language ID */
	public 		$id_lang;

	/** @var integer Carrier ID */
	public 		$id_carrier;

	/** @var boolean True if the customer wants a recycled package */
	public		$recyclable = 1;

	/** @var boolean True if the customer wants a gift wrapping */
	public		$gift = 0;

	/** @var string Gift message if specified */
	public 		$gift_message;

	/** @var string Object creation date */
	public 		$date_add;

	/** @var string secure_key */
	public		$secure_key;

	/** @var string Object last modification date */
	public 		$date_upd;

	protected static $_nbProducts = array();
	protected static $_isVirtualCart = array();

	protected	$fieldsRequired = array('id_currency', 'id_lang');
	protected	$fieldsValidate = array('id_address_delivery' => 'isUnsignedId', 'id_address_invoice' => 'isUnsignedId',
		'id_currency' => 'isUnsignedId', 'id_customer' => 'isUnsignedId', 'id_guest' => 'isUnsignedId', 'id_lang' => 'isUnsignedId',
		'id_carrier' => 'isUnsignedId', 'recyclable' => 'isBool', 'gift' => 'isBool', 'gift_message' => 'isMessage');

	protected	$_products = NULL;
	protected 	static $_totalWeight = array();
	protected	$_taxCalculationMethod = PS_TAX_EXC;
	protected	static $_discounts = NULL;
	protected	static $_discountsLite = NULL;
	protected	static $_carriers = NULL;
	protected	static $_taxes_rate = NULL;
	protected 	static $_attributesLists = array();
	protected 	$table = 'cart';
	protected 	$identifier = 'id_cart';

	protected	$webserviceParameters = array(
		'fields' => array(
		'id_address_delivery' => array('xlink_resource' => 'addresses'),
		'id_address_invoice' => array('xlink_resource' => 'addresses'),
		'id_currency' => array('xlink_resource' => 'currencies'),
		'id_customer' => array('xlink_resource' => 'customers'),
		'id_guest' => array('xlink_resource' => 'guests'),
		'id_lang' => array('xlink_resource' => 'languages'),
		'id_carrier' => array('xlink_resource' => 'carriers'),
		),
		'associations' => array(
			'cart_rows' => array('resource' => 'cart_row', 'virtual_entity' => true, 'fields' => array(
				'id_product' => array('required' => true, 'xlink_resource' => 'products'),
				'id_product_attribute' => array('required' => true, 'xlink_resource' => 'combinations'),
				'quantity' => array('required' => true),
				)
			),
		),
	);

	const ONLY_PRODUCTS = 1;
	const ONLY_DISCOUNTS = 2;
	const BOTH = 3;
	const BOTH_WITHOUT_SHIPPING = 4;
	const ONLY_SHIPPING = 5;
	const ONLY_WRAPPING = 6;
	const ONLY_PRODUCTS_WITHOUT_SHIPPING = 7;

	public function getFields()
	{
		parent::validateFields();

		$fields['id_group_shop'] = (int)$this->id_group_shop;
		$fields['id_shop'] = (int)$this->id_shop;
		
		$fields['id_address_delivery'] = (int)($this->id_address_delivery);
		$fields['id_address_invoice'] = (int)($this->id_address_invoice);
		$fields['id_currency'] = (int)($this->id_currency);
		$fields['id_customer'] = (int)($this->id_customer);
		$fields['id_guest'] = (int)($this->id_guest);
		$fields['id_lang'] = (int)($this->id_lang);
		$fields['id_carrier'] = (int)($this->id_carrier);
		$fields['recyclable'] = (int)($this->recyclable);
		$fields['gift'] = (int)($this->gift);
		$fields['secure_key'] = pSQL($this->secure_key);
		$fields['gift_message'] = pSQL($this->gift_message);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);

		return $fields;
	}

	public function __construct($id = NULL, $id_lang = NULL)
	{
		parent::__construct($id, $id_lang);
		if ($this->id_customer)
		{
			$customer = new Customer((int)($this->id_customer));
			$this->_taxCalculationMethod = Group::getPriceDisplayMethod((int)($customer->id_default_group));
			if ((!$this->secure_key OR $this->secure_key == '-1') AND $customer->secure_key)
			{
				$this->secure_key = $customer->secure_key;
				$this->save();
			}
		}
		else
			$this->_taxCalculationMethod = Group::getDefaultPriceDisplayMethod();
	}

	public function add($autodate = true, $nullValues = false)
	{
		$return = parent::add($autodate);
		Module::hookExec('cart');
		return $return;
	}

	public function update($nullValues = false)
	{
		if (isset(self::$_nbProducts[$this->id]))
			unset(self::$_nbProducts[$this->id]);
		if (isset(self::$_totalWeight[$this->id]))
			unset(self::$_totalWeight[$this->id]);
		$this->_products = NULL;
		$return = parent::update();
		Module::hookExec('cart');
		return $return;
	}

	public function delete()
	{
		if ($this->OrderExists()) //NOT delete a cart which is associated with an order
			return false;
		
		$uploadedFiles = Db::getInstance()->ExecuteS('
		SELECT cd.`value` 
		FROM `'._DB_PREFIX_.'customized_data` cd
		INNER JOIN `'._DB_PREFIX_.'customization` c ON (cd.`id_customization`= c.`id_customization`)
		WHERE cd.`type`= 0 AND c.`id_cart`='.(int)$this->id);
        
		foreach ($uploadedFiles as $mustUnlink)
		{
			unlink(_PS_UPLOAD_DIR_.$mustUnlink['value'].'_small');
			unlink(_PS_UPLOAD_DIR_.$mustUnlink['value']);
		}
		
		Db::getInstance()->Execute('
		DELETE FROM `'._DB_PREFIX_.'customized_data` 
		WHERE `id_customization` IN (
			SELECT `id_customization` 
			FROM `'._DB_PREFIX_.'customization` 
			WHERE `id_cart`='.(int)$this->id.'
		)');
		
		Db::getInstance()->Execute('
		DELETE FROM `'._DB_PREFIX_.'customization` 
		WHERE `id_cart` = '.(int)$this->id);

		if (!Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_discount` WHERE `id_cart` = '.(int)($this->id)) 
		 OR !Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)($this->id)))
			return false;
			
		return parent::delete();
	}

	static public function getTaxesAverageUsed($id_cart)
	{
		$cart = new Cart((int)($id_cart));
		if (!Validate::isLoadedObject($cart))
			die(Tools::displayError());

		if (!Configuration::get('PS_TAX'))
			return 0;

		$products = $cart->getProducts();
		$totalProducts_moy = 0;
		$ratioTax = 0;

		if (!sizeof($products))
			return 0;

		foreach ($products AS $product)
		{
			$totalProducts_moy += $product['total_wt'];
			$ratioTax += $product['total_wt'] * Tax::getProductTaxRate((int)$product['id_product'], (int)$cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
		}

		if ($totalProducts_moy > 0)
			return $ratioTax / $totalProducts_moy;

		return 0;
	}

	/**
	 * Return cart discounts
	 *
	 * @result array Discounts
	 */
	public function getDiscounts($lite = false, $refresh = false)
	{
		if (!$this->id)
			return array();
		if (!$lite AND !$refresh AND isset(self::$_discounts[$this->id]))
			return self::$_discounts[$this->id];
		if ($lite AND isset(self::$_discountsLite[$this->id]))
			return self::$_discountsLite[$this->id];

		$result = Db::getInstance()->ExecuteS('
		SELECT d.*, `id_cart`
		FROM `'._DB_PREFIX_.'cart_discount` c
		LEFT JOIN `'._DB_PREFIX_.'discount` d ON c.`id_discount` = d.`id_discount`
		WHERE `id_cart` = '.(int)($this->id));

		$products = $this->getProducts();
		foreach ($result AS $k => $discount)
		{
			$categories = Discount::getCategories((int)($discount['id_discount']));
			$in_category = false;
			foreach ($products AS $product)
				if (Product::idIsOnCategoryId((int)($product['id_product']), $categories))
				{
					$in_category = true;
					break;
				}
			if (!$in_category)
				unset($result[$k]);
		}

		if ($lite)
		{
			self::$_discountsLite[$this->id] = $result;
			return $result;
		}

		$total_products_wt = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS);
		$total_products = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS);
		$shipping_wt = $this->getOrderShippingCost();
		$shipping = $this->getOrderShippingCost(NULL, false);
		self::$_discounts[$this->id] = array();
		foreach ($result as $row)
		{
			$discount = new Discount($row['id_discount'], (int)($this->id_lang));
			$row['description'] = $discount->description ? $discount->description : $discount->name;
			$row['value_real'] = $discount->getValue(sizeof($result), $total_products_wt, $shipping_wt, $this->id);
			$row['value_tax_exc'] = $discount->getValue(sizeof($result), $total_products, $shipping, $this->id, false);
			self::$_discounts[$this->id][] = $row;
		}

		return isset(self::$_discounts[$this->id]) ? self::$_discounts[$this->id] : NULL;
	}

	public function getDiscountsCustomer($id_discount)
	{
		$result = Db::getInstance()->ExecuteS('
		SELECT `id_discount`
		FROM `'._DB_PREFIX_.'cart_discount`
		WHERE `id_discount` = '.(int)($id_discount).' AND `id_cart` = '.(int)($this->id));

		return Db::getInstance()->NumRows();
	}

	public function getLastProduct()
	{
		$sql = '
			SELECT `id_product`, `id_product_attribute`
			FROM `'._DB_PREFIX_.'cart_product`
			WHERE `id_cart` = '.(int)($this->id).'
			ORDER BY `date_add` DESC';
		$result = Db::getInstance()->getRow($sql);
		if ($result AND isset($result['id_product']) AND $result['id_product'])
			return $result;
		return false;
	}

	/**
	 * Return cart products
	 *
	 * @result array Products
	 */
	public function getProducts($refresh = false, $id_product = false)
	{
		if (!$this->id)
			return array();
		// Product cache must be strictly compared to NULL, or else an empty cart will add dozens of queries
		if ($this->_products !== NULL AND !$refresh)
			return $this->_products;
		$sql = '
		SELECT cp.`id_product_attribute`, cp.`id_product`, cu.`id_customization`, cp.`quantity` AS cart_quantity, cu.`quantity` AS customization_quantity, pl.`name`,
		pl.`description_short`, pl.`available_now`, pl.`available_later`, p.`id_product`, p.`id_category_default`, p.`id_supplier`, p.`id_manufacturer`, p.`on_sale`, p.`ecotax`, p.`additional_shipping_cost`, p.`available_for_order`,
		p.`price`, p.`weight`, p.`width`, p.`height`, p.`depth`, p.`out_of_stock`, p.`active`, p.`date_add`, p.`date_upd`, IFNULL(pa.`minimal_quantity`, p.`minimal_quantity`) as minimal_quantity,
		t.`id_tax`, tl.`name` AS tax, t.`rate`, pa.`price` AS price_attribute, s.quantity,
        pa.`ecotax` AS ecotax_attr, i.`id_image`, il.`legend`, pl.`link_rewrite`, cl.`link_rewrite` AS category, CONCAT(cp.`id_product`, cp.`id_product_attribute`) AS unique_id,
        IF (IFNULL(pa.`reference`, \'\') = \'\', p.`reference`, pa.`reference`) AS reference,
        IF (IFNULL(pa.`supplier_reference`, \'\') = \'\', p.`supplier_reference`, pa.`supplier_reference`) AS supplier_reference,
        (p.`weight`+ pa.`weight`) weight_attribute,
        IF (IFNULL(pa.`ean13`, \'\') = \'\', p.`ean13`, pa.`ean13`) AS ean13, IF (IFNULL(pa.`upc`, \'\') = \'\', p.`upc`, pa.`upc`) AS upc,
		pai.`id_image` as pai_id_image
		FROM `'._DB_PREFIX_.'cart_product` cp
		LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$this->id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.`id_product_attribute` = cp.`id_product_attribute`)
		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
			AND tr.`id_country` = '.(int)Country::getDefaultCountryId().'
			AND tr.`id_state` = 0)
	    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
		LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)$this->id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'customization` cu ON (p.`id_product` = cu.`id_product`)
		LEFT JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON (pai.`id_product_attribute` = pa.`id_product_attribute`)
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (IF(pai.`id_image`,
			i.`id_image` =
			(SELECT i2.`id_image`
			FROM `'._DB_PREFIX_.'image` i2
			INNER JOIN `'._DB_PREFIX_.'product_attribute_image` pai2 ON (pai2.`id_image` = i2.`id_image`)
			WHERE i2.`id_product` = p.`id_product` AND pai2.`id_product_attribute` = pa.`id_product_attribute`
			ORDER BY i2.`position`
			LIMIT 1),
			i.`id_product` = p.`id_product` AND i.`cover` = 1)
		)
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$this->id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (p.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->id_lang.')
		LEFT JOIN '._DB_PREFIX_.'stock s ON cp.id_product = s.id_product AND cp.id_product_attribute = s.id_product_attribute '.Shop::sqlSharedStock('s').'
		WHERE cp.`id_cart` = '.(int)$this->id.'
		'.($id_product ? ' AND cp.`id_product` = '.(int)$id_product : '').'
		AND p.`id_product` IS NOT NULL
		GROUP BY unique_id
		ORDER BY cp.date_add ASC';
		$result = Db::getInstance()->ExecuteS($sql);
		// Reset the cache before the following return, or else an empty cart will add dozens of queries

		$productsIds = array();
		$paIds = array();
		foreach ($result as $row)
		{
			$productsIds[] = $row['id_product'];
			$paIds[] = $row['id_product_attribute'];
		}
		// Thus you can avoid one query per product, because there will be only one query for all the products of the cart
		Product::cacheProductsFeatures($productsIds);
		self::cacheSomeAttributesLists($paIds, $this->id_lang);

		$this->_products = array();
		if (empty($result))
			return array();
		foreach ($result AS $k => $row)
		{
			if (isset($row['ecotax_attr']) AND $row['ecotax_attr'] > 0)
				$row['ecotax'] = (float)($row['ecotax_attr']);
			$row['stock_quantity'] = (int)($row['quantity']);
			// for compatibility with 1.2 themes
			$row['quantity'] = (int)($row['cart_quantity']);
			if (isset($row['id_product_attribute']) AND (int)$row['id_product_attribute'])
			{
				$row['weight'] = $row['weight_attribute'];
			}
			if ($this->_taxCalculationMethod == PS_TAX_EXC)
			{
				$row['price'] = Product::getPriceStatic((int)$row['id_product'], false, isset($row['id_product_attribute']) ? (int)($row['id_product_attribute']) : NULL, 2, NULL, false, true, (int)($row['cart_quantity']), false, ((int)($this->id_customer) ? (int)($this->id_customer) : NULL), (int)($this->id), ((int)($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) ? (int)($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) : NULL), $specificPriceOutput); // Here taxes are computed only once the quantity has been applied to the product price
				$row['price_wt'] = Product::getPriceStatic((int)$row['id_product'], true, isset($row['id_product_attribute']) ? (int)($row['id_product_attribute']) : NULL, 2, NULL, false, true, (int)($row['cart_quantity']), false, ((int)($this->id_customer) ? (int)($this->id_customer) : NULL), (int)($this->id), ((int)($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) ? (int)($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) : NULL));
                $tax_rate = Tax::getProductTaxRate((int)$row['id_product'], (int)($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
                
				$row['total_wt'] = Tools::ps_round($row['price'] * (float)$row['cart_quantity'] * (1 + (float)($tax_rate) / 100), 2);
				$row['total'] = $row['price'] * (int)($row['cart_quantity']);
			}
			else
			{
				$row['price'] = Product::getPriceStatic((int)$row['id_product'], false, (int)$row['id_product_attribute'], 6, NULL, false, true, $row['cart_quantity'], false, ((int)($this->id_customer) ? (int)($this->id_customer) : NULL), (int)($this->id), ((int)($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) ? (int)($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) : NULL), $specificPriceOutput);
				$row['price_wt'] = Product::getPriceStatic((int)$row['id_product'], true, (int)$row['id_product_attribute'], 2, NULL, false, true, $row['cart_quantity'], false, ((int)($this->id_customer) ? (int)($this->id_customer) : NULL), (int)($this->id), ((int)($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) ? (int)($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) : NULL));

				/* In case when you use QuantityDiscount, getPriceStatic() can be return more of 2 decimals */
				$row['price_wt'] = Tools::ps_round($row['price_wt'], 2);
				$row['total_wt'] = $row['price_wt'] * (int)($row['cart_quantity']);
				$row['total'] = Tools::ps_round($row['price'] * (int)($row['cart_quantity']), 2);
			}
			$row['reduction_applies'] = $specificPriceOutput AND (float)($specificPriceOutput['reduction']);
			$row['id_image'] = Product::defineProductImage($row,$this->id_lang);
			$row['allow_oosp'] = Product::isAvailableWhenOutOfStock($row['out_of_stock']);
			$row['features'] = Product::getFeaturesStatic((int)$row['id_product']);
			if (array_key_exists($row['id_product_attribute'].'-'.$this->id_lang, self::$_attributesLists))
				$row = array_merge($row, self::$_attributesLists[$row['id_product_attribute'].'-'.$this->id_lang]);

			$this->_products[] = $row;
		}
		return $this->_products;
	}

	public static function cacheSomeAttributesLists($ipaList, $id_lang)
	{
		$paImplode = array();
		$attributesList = array();
		$attributesListSmall = array();
		foreach ($ipaList as $id_product_attribute)
			if ((int)$id_product_attribute AND !array_key_exists($id_product_attribute.'-'.$id_lang, self::$_attributesLists))
			{
				$paImplode[] = (int)$id_product_attribute;
				self::$_attributesLists[(int)$id_product_attribute.'-'.$id_lang] = array('attributes' => '', 'attributes_small' => '');
			}
		if (!count($paImplode))
			return;

		$result = Db::getInstance()->ExecuteS('
		SELECT pac.`id_product_attribute`, agl.`public_name` AS public_group_name, al.`name` AS attribute_name
		FROM `'._DB_PREFIX_.'product_attribute_combination` pac
		LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
		LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
		LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
		WHERE pac.`id_product_attribute` IN ('.implode($paImplode, ',').')
		ORDER BY agl.`public_name` ASC');

		foreach ($result as $row)
		{
			self::$_attributesLists[$row['id_product_attribute'].'-'.$id_lang]['attributes'] .= $row['public_group_name'].' : '.$row['attribute_name'].', ';
			self::$_attributesLists[$row['id_product_attribute'].'-'.$id_lang]['attributes_small'] .= $row['attribute_name'].', ';
		}

		foreach ($paImplode as $id_product_attribute)
		{
			self::$_attributesLists[$id_product_attribute.'-'.$id_lang]['attributes'] = rtrim(self::$_attributesLists[$id_product_attribute.'-'.$id_lang]['attributes'], ', ');
			self::$_attributesLists[$id_product_attribute.'-'.$id_lang]['attributes_small'] = rtrim(self::$_attributesLists[$id_product_attribute.'-'.$id_lang]['attributes_small'], ', ');
		}
	}

	/**
	 * Return cart products quantity
	 *
	 * @result integer Products quantity
	 */
	public	function nbProducts()
	{
		if (!$this->id)
			return 0;
		return self::getNbProducts($this->id);
	}

	public static function getNbProducts($id)
	{
		// Must be strictly compared to NULL, or else an empty cart will bypass the cache and add dozens of queries
		if (isset(self::$_nbProducts[$id]) && self::$_nbProducts[$id] !== NULL)
			return self::$_nbProducts[$id];
		self::$_nbProducts[$id] = (int)(Db::getInstance()->getValue('
			SELECT SUM(`quantity`) 
			FROM `'._DB_PREFIX_.'cart_product` 
			WHERE `id_cart` = '.(int)($id)));
		return self::$_nbProducts[$id];
	}

	/**
	 * Add a discount to the cart (NO controls except doubles)
	 *
	 * @param integer $id_discount The discount to add to the cart
	 * @result boolean Update result
	 */
	public	function addDiscount($id_discount)
	{
		return Db::getInstance()->AutoExecute(_DB_PREFIX_.'cart_discount', array('id_discount' => (int)($id_discount), 'id_cart' => (int)($this->id)), 'INSERT');
	}

	public function containsProduct($id_product, $id_product_attribute, $id_customization)
	{
		return Db::getInstance()->getRow('
		SELECT cp.`quantity`
		FROM `'._DB_PREFIX_.'cart_product` cp
		'.($id_customization ? 'LEFT JOIN `'._DB_PREFIX_.'customization` c ON (c.`id_product` = cp.`id_product` AND c.`id_product_attribute` = cp.`id_product_attribute`)' : '').'
		WHERE cp.`id_product` = '.(int)$id_product.' AND cp.`id_product_attribute` = '.(int)$id_product_attribute.' AND cp.`id_cart` = '.(int)$this->id.
		($id_customization ? ' AND c.`id_customization` = '.(int)$id_customization : ''));
	}

	/**
	 * Update product quantity
	 *
	 * @param integer $quantity Quantity to add (or substract)
	 * @param integer $id_product Product ID
	 * @param integer $id_product_attribute Attribute ID if needed
	 * @param string $operator Indicate if quantity must be increased or decreased
	 */
	public	function updateQty($quantity, $id_product, $id_product_attribute = NULL, $id_customization = false, $operator = 'up', $id_shop = NULL, $id_group_shop = NULL)
	{
		if (is_null($id_shop)) $id_shop = Shop::getCurrentShop(true);
		$quantity = (int)$quantity;
		$id_product = (int)$id_product;
		$id_product_attribute = (int)$id_product_attribute;
		$product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'), $id_shop);

		/* If we have a product combination, the minimal quantity is set with the one of this combination */
		if (!empty($id_product_attribute))
			$minimalQuantity = (int)Attribute::getAttributeMinimalQty($id_product_attribute);
		else
			$minimalQuantity = (int)$product->minimal_quantity;

		if (!Validate::isLoadedObject($product))
			die(Tools::displayError());
		if (isset(self::$_nbProducts[$this->id]))
			unset(self::$_nbProducts[$this->id]);
		if (isset(self::$_totalWeight[$this->id]))
			unset(self::$_totalWeight[$this->id]);
		if ((int)$quantity <= 0)
			return $this->deleteProduct($id_product, $id_product_attribute, (int)$id_customization);
		elseif (!$product->available_for_order OR Configuration::get('PS_CATALOG_MODE'))
			return false;
		else
		{
			/* Check if the product is already in the cart */
			$result = $this->containsProduct($id_product, $id_product_attribute, (int)$id_customization);

			/* Update quantity if product already exist */
			
			if ($result)
			{
				if ($operator == 'up')
				{
					$sql = 'SELECT p.out_of_stock, s.quantity
							FROM '._DB_PREFIX_.'product p
							LEFT JOIN '._DB_PREFIX_.'stock s ON s.id_product = p.id_product
							WHERE s.id_product = '.$id_product.'
								AND s.id_product_attribute = '.$id_product_attribute
								.Shop::sqlSharedStock('s', $id_shop, $id_group_shop);
					$result2 = Db::getInstance()->getRow($sql);
					$productQty = (int)$result2['quantity'];
					$newQty = (int)$result['quantity'] + (int)$quantity;
					$qty = '+ '.(int)$quantity;

					if (!Product::isAvailableWhenOutOfStock((int)$result2['out_of_stock']))
						if ($newQty > $productQty)
							return false;
				}
				elseif ($operator == 'down')
				{
					$qty = '- '.(int)$quantity;
					$newQty = (int)$result['quantity'] - (int)$quantity;
					if ($newQty < $minimalQuantity AND $minimalQuantity > 1)
						return -1;
				}
				else
					return false;

				/* Delete product from cart */
				if ($newQty <= 0)
					return $this->deleteProduct((int)$id_product, (int)$id_product_attribute, (int)$id_customization);
				elseif ($newQty < $minimalQuantity)
					return -1;
				else
					Db::getInstance()->Execute('
					UPDATE `'._DB_PREFIX_.'cart_product`
					SET `quantity` = `quantity` '.$qty.'
					WHERE `id_product` = '.(int)$id_product.
					(!empty($id_product_attribute) ? ' AND `id_product_attribute` = '.(int)$id_product_attribute : '').'
					AND `id_cart` = '.(int)$this->id.'
					LIMIT 1');
			}

			/* Add product to the cart */
			else
			{
				$sql = 'SELECT p.out_of_stock, s.quantity
						FROM '._DB_PREFIX_.'product p
						LEFT JOIN '._DB_PREFIX_.'stock s ON s.id_product = p.id_product
						WHERE s.id_product = '.$id_product.'
							AND s.id_product_attribute = '.$id_product_attribute
							.Shop::sqlSharedStock('s', $id_shop, $id_group_shop);
				$result2 = Db::getInstance()->getRow($sql);
				if (!Product::isAvailableWhenOutOfStock((int)$result2['out_of_stock']))
					if ((int)$quantity > $result2['quantity'])
						return false;

				if ((int)$quantity < $minimalQuantity)
					return -1;

				if (!Db::getInstance()->AutoExecute(_DB_PREFIX_.'cart_product', array('id_product' => (int)$id_product,
				'id_product_attribute' => (int)$id_product_attribute, 'id_cart' => (int)$this->id,
				'quantity' => (int)$quantity, 'date_add' => date('Y-m-d H:i:s')), 'INSERT'))
					return false;
			}
		}
		// refresh cache of self::_products
		$this->_products = $this->getProducts(true);
		$this->update(true);

		if ($product->customizable)
			return $this->_updateCustomizationQuantity((int)$quantity, (int)$id_customization, (int)$id_product, (int)$id_product_attribute, $operator);
		else
			return true;
	}

	/*
	** Customization management
	*/
	protected function _updateCustomizationQuantity($quantity, $id_customization, $id_product, $id_product_attribute, $operator = 'up')
	{
		global $cookie;

		/* Getting datas */
		$files = $cookie->getFamily('pictures_'.(int)($id_product).'_');
		$textFields = $cookie->getFamily('textFields_'.(int)($id_product).'_');
		/* Customization addition */
		if (count($files) > 0 OR count($textFields) > 0)
			return $this->_addCustomization((int)$id_product, (int)$id_product_attribute, $files, $textFields, (int)$quantity);
		/* Deletion */
		if (!empty($id_customization) AND (int)($quantity) < 1)
			return $this->_deleteCustomization((int)$id_customization, (int)$id_product, (int)$id_product_attribute);
		/* Quantity update */
		if (!empty($id_customization))
		{
			$result = Db::getInstance()->getRow('SELECT `quantity` FROM `'._DB_PREFIX_.'customization` WHERE `id_customization` = '.(int)$id_customization);
			if ($result AND Db::getInstance()->NumRows())
			{
				if ($operator == 'down' AND (int)($result['quantity']) - (int)($quantity) < 1)
					return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'customization` WHERE `id_customization` = '.(int)$id_customization);
				return Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'customization` SET `quantity` = `quantity` '.($operator == 'up' ? '+ ' : '- ').(int)($quantity).' WHERE `id_customization` = '.(int)($id_customization));
			}
		}
		// refresh cache of self::_products
		$this->_products = $this->getProducts(true);
		$this->update(true);
		return true;
	}

	public function _addCustomization($id_product, $id_product_attribute, $files, $textFields, $quantity)
	{
		if (!is_array($files) OR !is_array($textFields))
			die(Tools::displayError());
		/* Copying them inside the db */
		if (!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'customization` (`id_cart`, `id_product`, `id_product_attribute`, `quantity`) VALUES ('.(int)($this->id).', '.(int)($id_product).', '.(int)($id_product_attribute).', '.(int)($quantity).')'))
			return false;
		if (!$id_customization = Db::getInstance()->Insert_ID())
			return false;
		$query = 'INSERT INTO `'._DB_PREFIX_.'customized_data` (`id_customization`, `type`, `index`, `value`) VALUES ';
		if (count($files))
			foreach ($files AS $key => $filename)
			{
				$tmp = explode('_', $key);
				$query .= '('.(int)($id_customization).', '._CUSTOMIZE_FILE_.', '.$tmp[2].', \''.$filename.'\'), ';
			}
		if (count($textFields))
			foreach ($textFields AS $key => $textFieldValue)
			{
				$tmp = explode('_', $key);
				$query .= '('.(int)($id_customization).', '._CUSTOMIZE_TEXTFIELD_.', '.$tmp[2].', \''.$textFieldValue.'\'), ';
			}
		$query = rtrim($query, ', ');
		if (!$result = Db::getInstance()->Execute($query))
			return false;
		/* Deleting customized informations from the cart (we just copied them inside the db) */
		return Cart::deleteCustomizationInformations((int)($id_product));
	}

	/**
	 * Check if order has already been placed
	 *
	 * @return boolean result
	 */
	public function OrderExists()
	{
		return (bool)Db::getInstance()->getValue('SELECT `id_cart` FROM `'._DB_PREFIX_.'orders` WHERE `id_cart` = '.(int)$this->id);
	}

	/*
	** Deletion
	*/

	/**
	 * Delete a discount from the cart
	 *
	 * @param integer $id_discount Discount ID
	 * @return boolean result
	 */
	public	function deleteDiscount($id_discount)
	{
		return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_discount` WHERE `id_discount` = '.(int)($id_discount).' AND `id_cart` = '.(int)($this->id).' LIMIT 1');
	}

	/**
	 * Delete a product from the cart
	 *
	 * @param integer $id_product Product ID
	 * @param integer $id_product_attribute Attribute ID if needed
	 * @param integer $id_customization Customization id
	 * @return boolean result
	 */
	public	function deleteProduct($id_product, $id_product_attribute = NULL, $id_customization = NULL)
	{
		if (isset(self::$_nbProducts[$this->id]))
			unset(self::$_nbProducts[$this->id]);
		if (isset(self::$_totalWeight[$this->id]))
			unset(self::$_totalWeight[$this->id]);
		if ((int)($id_customization))
		{
			$productTotalQuantity = (int)(Db::getInstance()->getValue('SELECT `quantity`
				FROM `'._DB_PREFIX_.'cart_product`
				WHERE `id_product` = '.(int)($id_product).' AND `id_product_attribute` = '.(int)($id_product_attribute)));
			$customizationQuantity = (int)(Db::getInstance()->getValue('SELECT `quantity`
				FROM `'._DB_PREFIX_.'customization`
				WHERE `id_cart` = '.(int)($this->id).'
					AND `id_product` = '.(int)($id_product).'
					AND `id_product_attribute` = '.(int)($id_product_attribute)));
			if (!$this->_deleteCustomization((int)($id_customization), (int)($id_product), (int)($id_product_attribute)))
				return false;
			// refresh cache of self::_products
			$this->_products = $this->getProducts(true);
			return ($customizationQuantity == $productTotalQuantity AND $this->deleteProduct((int)($id_product), $id_product_attribute, NULL));
		}

		/* Get customization quantity */
		if (($result = Db::getInstance()->getRow('SELECT SUM(`quantity`) AS \'quantity\' FROM `'._DB_PREFIX_.'customization` WHERE `id_cart` = '.(int)($this->id).' AND `id_product` = '.(int)($id_product).' AND `id_product_attribute` = '.(int)($id_product_attribute))) === false)
			return false;

		/* If the product still possesses customization it does not have to be deleted */
		if (Db::getInstance()->NumRows() AND (int)($result['quantity']))
			return Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'cart_product` SET `quantity` = '.(int)($result['quantity']).' WHERE `id_cart` = '.(int)($this->id).' AND `id_product` = '.(int)($id_product).($id_product_attribute != NULL ? ' AND `id_product_attribute` = '.(int)($id_product_attribute) : ''));

		/* Product deletion */
		if (Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_product` = '.(int)($id_product).($id_product_attribute != NULL ? ' AND `id_product_attribute` = '.(int)($id_product_attribute) : '').' AND `id_cart` = '.(int)($this->id)))
		{
			// refresh cache of self::_products
			$this->_products = $this->getProducts(true);
			/* Update cart */
			return $this->update(true);
		}
		return false;
	}

	/**
	 * Delete a customization from the cart. If customization is a Picture (type=2),
	 * then the image is also deleted
	 *
	 * @param integer $id_customization
	 * @return boolean result
	 */
	protected	function _deleteCustomization($id_customization, $id_product, $id_product_attribute)
	{
		$result = true;
		$customization = Db::getInstance()->getRow('SELECT *
			FROM `'._DB_PREFIX_.'customization`
			WHERE `id_customization` = '.(int)($id_customization));

		if ($customization and sizeof($customization))
		{
			$custData = Db::getInstance()->getRow('SELECT *
				FROM `'._DB_PREFIX_.'customized_data`
				WHERE `id_customization` = '.(int)($id_customization));

			if (isset($custData['type']) and $custData['type'] == 0)
				$result &= $this->deletePictureToProduct($id_product,$custData['value']);

			$result &= Db::getInstance()->execute('DELETE
				FROM `'._DB_PREFIX_.'customized_data`
				WHERE `id_customization` = '.(int)($id_customization));

			if($result)
				$result &= Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'cart_product`
					SET `quantity` = `quantity` - '.(int)($customization['quantity']).'
					WHERE `id_cart` = '.(int)($this->id).'
					AND `id_product` = '.(int)($id_product).((int)($id_product_attribute) ? '
					AND `id_product_attribute` = '.(int)($id_product_attribute) : ''));

			if (!$result)
				return false;

			return Db::getInstance()->Execute('DELETE
				FROM `'._DB_PREFIX_.'customization`
				WHERE `id_customization` = '.(int)($id_customization));
		}

		return true;
	}

	static public function getTotalCart($id_cart, $use_tax_display = false)
	{
		$cart = new Cart((int)($id_cart));
		if (!Validate::isLoadedObject($cart))
			die(Tools::displayError());
	    $with_taxes = $use_tax_display ? $cart->_taxCalculationMethod != PS_TAX_EXC : true;
		return Tools::displayPrice($cart->getOrderTotal($with_taxes), Currency::getCurrencyInstance((int)($cart->id_currency)), false);
	}


    public static function getOrderTotalUsingTaxCalculationMethod($id_cart)
    {
        return Cart::getTotalCart($id_cart, true);
    }

	/**
	* This function returns the total cart amount
	*
	* Possible values for $type:
	* Cart::ONLY_PRODUCTS
	* Cart::ONLY_DISCOUNTS
	* Cart::BOTH
	* Cart::BOTH_WITHOUT_SHIPPING
	* Cart::ONLY_SHIPPING
	* Cart::ONLY_WRAPPING
	* Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING
	*
	* @param boolean $withTaxes With or without taxes
	* @param integer $type Total type
	* @return float Order total
	*/
	public function getOrderTotal($withTaxes = true, $type = Cart::BOTH)
	{
		if (!$this->id)
			return 0;
		$type = (int)($type);
		if (!in_array($type, array(Cart::ONLY_PRODUCTS, Cart::ONLY_DISCOUNTS, Cart::BOTH, Cart::BOTH_WITHOUT_SHIPPING, Cart::ONLY_SHIPPING, Cart::ONLY_WRAPPING, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING)))
			die(Tools::displayError());

		// no shipping cost if is a cart with only virtuals products
		$virtual = $this->isVirtualCart();
		if ($virtual AND $type == Cart::ONLY_SHIPPING)
			return 0;
		if ($virtual AND $type == Cart::BOTH)
			$type = Cart::BOTH_WITHOUT_SHIPPING;
		$shipping_fees = ($type != Cart::BOTH_WITHOUT_SHIPPING AND $type != Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING) ? $this->getOrderShippingCost(NULL, (int)($withTaxes)) : 0;
		if ($type == Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING)
			$type = Cart::ONLY_PRODUCTS;

		$products = $this->getProducts();
		$order_total = 0;
		if (Tax::excludeTaxeOption())
			$withTaxes = false;
		foreach ($products AS $product)
		{
			if ($this->_taxCalculationMethod == PS_TAX_EXC)
			{

				// Here taxes are computed only once the quantity has been applied to the product price
				$price = Product::getPriceStatic((int)$product['id_product'], false, (int)$product['id_product_attribute'], 2, NULL, false, true, $product['cart_quantity'], false, (int)$this->id_customer ? (int)$this->id_customer : NULL, (int)$this->id, ($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));

                $total_ecotax = $product['ecotax'] * (int)$product['cart_quantity'];
				$total_price = $price * (int)$product['cart_quantity'];

				if ($withTaxes)
				{
				   $total_price = ($total_price - $total_ecotax) * (1 + (float)(Tax::getProductTaxRate((int)$product['id_product'], (int)$this->{Configuration::get('PS_TAX_ADDRESS_TYPE')})) / 100);
				   $total_ecotax = $total_ecotax * (1 + Tax::getProductEcotaxRate((int)$this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) / 100);
					$total_price = Tools::ps_round($total_price + $total_ecotax, 2);
				}
			}
			else
			{
				$price = Product::getPriceStatic((int)($product['id_product']), $withTaxes, (int)($product['id_product_attribute']), 2, NULL, false, true, $product['cart_quantity'], false, ((int)($this->id_customer) ? (int)($this->id_customer) : NULL), (int)($this->id), ((int)($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) ? (int)($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) : NULL));
				$total_price = Tools::ps_round($price, 2) * (int)($product['cart_quantity']);
			}
			$order_total += $total_price;
		}
		$order_total_products = $order_total;
		if ($type == Cart::ONLY_DISCOUNTS)
			$order_total = 0;
		// Wrapping Fees
		$wrapping_fees = 0;
		if ($this->gift)
		{
			$wrapping_fees = (float)(Configuration::get('PS_GIFT_WRAPPING_PRICE'));
			if ($withTaxes)
			{
				$wrapping_fees_tax = new Tax((int)(Configuration::get('PS_GIFT_WRAPPING_TAX')));
				$wrapping_fees *= 1 + (((float)($wrapping_fees_tax->rate) / 100));
			}
			$wrapping_fees = Tools::convertPrice(Tools::ps_round($wrapping_fees, 2), Currency::getCurrencyInstance((int)($this->id_currency)));
		}

		if ($type != Cart::ONLY_PRODUCTS)
		{
			$discounts = array();
			/* Firstly get all discounts, looking for a free shipping one (in order to substract shipping fees to the total amount) */
			if ($discountIds = $this->getDiscounts(true))
			{
				foreach ($discountIds AS $id_discount)
				{
					$discount = new Discount((int)($id_discount['id_discount']));
					if (Validate::isLoadedObject($discount))
					{
						$discounts[] = $discount;
						if ($discount->id_discount_type == 3)
							foreach($products AS $product)
							{
								$categories = Discount::getCategories($discount->id);
								if (count($categories) AND Product::idIsOnCategoryId($product['id_product'], $categories))
								{
									if($type == Cart::ONLY_DISCOUNTS)
										$order_total -= $shipping_fees;
									$shipping_fees = 0;
									break;
								}
							}
					}
				}
				/* Secondly applying all vouchers to the correct amount */
				$shrunk = false;
				foreach ($discounts AS $discount)
					if ($discount->id_discount_type != 3)
					{
						$order_total -= Tools::ps_round((float)($discount->getValue(sizeof($discounts), $order_total_products, $shipping_fees, $this->id, (int)($withTaxes))), 2);
						if ($discount->id_discount_type == 2)
							if (in_array($discount->behavior_not_exhausted, array(1,2)))
								$shrunk = true;
					}

					$order_total_discount = 0;
					if ($shrunk AND $order_total < (-$wrapping_fees - $order_total_products - $shipping_fees))
						$order_total_discount = -$wrapping_fees - $order_total_products - $shipping_fees;
					else
						$order_total_discount = $order_total;
			}
		}

		if ($type == Cart::ONLY_SHIPPING) return $shipping_fees;
		if ($type == Cart::ONLY_WRAPPING) return $wrapping_fees;
		if ($type == Cart::BOTH) $order_total += $shipping_fees + $wrapping_fees;
		if ($order_total < 0 AND $type != Cart::ONLY_DISCOUNTS) return 0;
		if ($type == Cart::ONLY_DISCOUNTS AND isset($order_total_discount))
			return Tools::ps_round((float)($order_total_discount), 2);
		return Tools::ps_round((float)($order_total), 2);
	}

	/**
	* Return shipping total
	*
	* @param integer $id_carrier Carrier ID (default : current carrier)
	* @return float Shipping total
	*/
    function getOrderShippingCost($id_carrier = NULL, $useTax = true)
    {
		global $defaultCountry;

		if ($this->isVirtualCart())
			return 0;

		// Checking discounts in cart
		$products = $this->getProducts();
		$discounts = $this->getDiscounts(true);
		if ($discounts)
			foreach ($discounts AS $id_discount)
				if ($id_discount['id_discount_type'] == 3)
				{
					if ($id_discount['minimal'] > 0)
					{
						$total_cart = 0;

						$categories = Discount::getCategories((int)($id_discount['id_discount']));
						if (sizeof($categories))
							foreach($products AS $product)
								if (Product::idIsOnCategoryId((int)($product['id_product']), $categories))
									$total_cart += $product['total_wt'];

						if ($total_cart >= $id_discount['minimal'])
							return 0;
					}
					else
						return 0;
				}

		// Order total in default currency without fees
		$order_total = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);

		// Start with shipping cost at 0
        $shipping_cost = 0;

		// If no product added, return 0
		if ($order_total <= 0 AND !(int)(self::getNbProducts($this->id)))
			return $shipping_cost;

		// Get id zone
		if (isset($this->id_address_delivery)
			AND $this->id_address_delivery
			AND Customer::customerHasAddress($this->id_customer, $this->id_address_delivery))
			$id_zone = Address::getZoneById((int)($this->id_address_delivery));
		else
		{
			// This method can be called from the backend, and $defaultCountry won't be defined
			if (!Validate::isLoadedObject($defaultCountry))
				$defaultCountry = new Country(Configuration::get('PS_COUNTRY_DEFAULT'), Configuration::get('PS_LANG_DEFAULT'));
			$id_zone = (int)$defaultCountry->id_zone;
		}		

		// If no carrier, select default one
		if (!$id_carrier)
			$id_carrier = $this->id_carrier;

		if ($id_carrier && !$this->isCarrierInRange($id_carrier, $id_zone))
			$id_carrier = '';

		if (empty($id_carrier) && $this->isCarrierInRange(Configuration::get('PS_CARRIER_DEFAULT'), $id_zone))
				$id_carrier = (int)(Configuration::get('PS_CARRIER_DEFAULT'));

		if (empty($id_carrier))
		{
			if ((int)($this->id_customer))
			{
				$customer = new Customer((int)($this->id_customer));
				$result = Carrier::getCarriers((int)(Configuration::get('PS_LANG_DEFAULT')), true, false, (int)($id_zone), $customer->getGroups());
				unset($customer);
			}
			else
				$result = Carrier::getCarriers((int)(Configuration::get('PS_LANG_DEFAULT')), true, false, (int)($id_zone));

			$resultsArray = array();
			foreach ($result AS $k => $row)
			{
				if ($row['id_carrier'] == Configuration::get('PS_CARRIER_DEFAULT'))
					continue;

				if (!isset(self::$_carriers[$row['id_carrier']]))
					self::$_carriers[$row['id_carrier']] = new Carrier((int)($row['id_carrier']));

				$carrier = self::$_carriers[$row['id_carrier']];

				// Get only carriers that are compliant with shipping method
				if (($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT AND $carrier->getMaxDeliveryPriceByWeight($id_zone) === false)
				OR ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE AND $carrier->getMaxDeliveryPriceByPrice($id_zone) === false))
				{
					unset($result[$k]);
					continue ;
				}

				// If out-of-range behavior carrier is set on "Desactivate carrier"
				if ($row['range_behavior'])
				{
					// Get only carriers that have a range compatible with cart
					if (($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT AND (!Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $this->getTotalWeight(), $id_zone)))
					OR ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE AND (!Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING), $id_zone, (int)($this->id_currency)))))
					{
						unset($result[$k]);
						continue ;
					}
				}

				if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT)
				{
					$shipping = $carrier->getDeliveryPriceByWeight($this->getTotalWeight(), $id_zone);

					if (!isset($tmp))
						$tmp = $shipping;

					if ($shipping <= $tmp)
						$id_carrier = (int)($row['id_carrier']);
				}
				else // by price
				{
					$shipping = $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int)($this->id_currency));

					if (!isset($tmp))
						$tmp = $shipping;

					if ($shipping <= $tmp)
						$id_carrier = (int)($row['id_carrier']);
				}
			}
		}

		if (empty($id_carrier))
			$id_carrier = Configuration::get('PS_CARRIER_DEFAULT');

		if (!isset(self::$_carriers[$id_carrier]))
			self::$_carriers[$id_carrier] = new Carrier((int)($id_carrier), Configuration::get('PS_LANG_DEFAULT'));
		$carrier = self::$_carriers[$id_carrier];
		if (!Validate::isLoadedObject($carrier))
			die(Tools::displayError('Fatal error: "no default carrier"'));
        if (!$carrier->active)
			return $shipping_cost;
			
		// Free fees if free carrier
		if ($carrier->is_free == 1)
			return 0;	
			
		// Select carrier tax
		if ($useTax AND !Tax::excludeTaxeOption())
			 $carrierTax = Tax::getCarrierTaxRate((int)$carrier->id, (int)$this->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

		$configuration = Configuration::getMultiple(array('PS_SHIPPING_FREE_PRICE', 'PS_SHIPPING_HANDLING', 'PS_SHIPPING_METHOD', 'PS_SHIPPING_FREE_WEIGHT'));
		// Free fees
		$free_fees_price = 0;
		if (isset($configuration['PS_SHIPPING_FREE_PRICE']))
			$free_fees_price = Tools::convertPrice((float)($configuration['PS_SHIPPING_FREE_PRICE']), Currency::getCurrencyInstance((int)($this->id_currency)));
		$orderTotalwithDiscounts = $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
		if ($orderTotalwithDiscounts >= (float)($free_fees_price) AND (float)($free_fees_price) > 0)
			return $shipping_cost;
		if (isset($configuration['PS_SHIPPING_FREE_WEIGHT']) AND $this->getTotalWeight() >= (float)($configuration['PS_SHIPPING_FREE_WEIGHT']) AND (float)($configuration['PS_SHIPPING_FREE_WEIGHT']) > 0)
			return $shipping_cost;

			// Get shipping cost using correct method
			if ($carrier->range_behavior)
			{
				// Get id zone
		        if (
              isset($this->id_address_delivery)
              AND $this->id_address_delivery
              AND Customer::customerHasAddress($this->id_customer, $this->id_address_delivery)
            )
					$id_zone = Address::getZoneById((int)($this->id_address_delivery));
				else
					$id_zone = (int)$defaultCountry->id_zone;
				if (($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT AND (!Carrier::checkDeliveryPriceByWeight($carrier->id, $this->getTotalWeight(), $id_zone)))
						OR ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE AND (!Carrier::checkDeliveryPriceByPrice($carrier->id, $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING), $id_zone, (int)($this->id_currency)))))
						$shipping_cost += 0;
					else {
							if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT)
								$shipping_cost += $carrier->getDeliveryPriceByWeight($this->getTotalWeight(), $id_zone);
							else // by price
								$shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int)($this->id_currency));
						 }
			}
			else
			{
				if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT)
					$shipping_cost += $carrier->getDeliveryPriceByWeight($this->getTotalWeight(), $id_zone);
				else
					$shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int)($this->id_currency));

			}
		// Adding handling charges
		if (isset($configuration['PS_SHIPPING_HANDLING']) AND $carrier->shipping_handling)
			$shipping_cost += (float)($configuration['PS_SHIPPING_HANDLING']);

		$shipping_cost = Tools::convertPrice($shipping_cost, Currency::getCurrencyInstance((int)($this->id_currency)));

		// Additional Shipping Cost per product
		foreach($products AS $product)
			$shipping_cost += $product['additional_shipping_cost'] * $product['cart_quantity'];

		//get external shipping cost from module
		if ($carrier->shipping_external)
		{
			$moduleName = $carrier->external_module_name;
			$module = Module::getInstanceByName($moduleName);
			if (key_exists('id_carrier', $module))
				$module->id_carrier = $carrier->id;
			if($carrier->need_range)
				$shipping_cost = $module->getOrderShippingCost($this, $shipping_cost);
			else
				$shipping_cost = $module->getOrderShippingCostExternal($this);

			// Check if carrier is available
			if ($shipping_cost === false)
				return false;
		}

		// Apply tax
		if (isset($carrierTax))
			$shipping_cost *= 1 + ($carrierTax / 100);

		return (float)(Tools::ps_round((float)($shipping_cost), 2));
    }

	/**
	* Return cart weight
	*
	* @return float Cart weight
	*/
	public function getTotalWeight()
	{
		if (!isset(self::$_totalWeight[$this->id]))
		{
			$result = Db::getInstance()->getRow('
			SELECT SUM((p.`weight` + pa.`weight`) * cp.`quantity`) as nb
			FROM `'._DB_PREFIX_.'cart_product` cp
			LEFT JOIN `'._DB_PREFIX_.'product` p ON cp.`id_product` = p.`id_product`
			LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON cp.`id_product_attribute` = pa.`id_product_attribute`
			WHERE (cp.`id_product_attribute` IS NOT NULL AND cp.`id_product_attribute` != 0)
			AND cp.`id_cart` = '.(int)($this->id));
			$result2 = Db::getInstance()->getRow('
			SELECT SUM(p.`weight` * cp.`quantity`) as nb
			FROM `'._DB_PREFIX_.'cart_product` cp
			LEFT JOIN `'._DB_PREFIX_.'product` p ON cp.`id_product` = p.`id_product`
			WHERE (cp.`id_product_attribute` IS NULL OR cp.`id_product_attribute` = 0)
			AND cp.`id_cart` = '.(int)($this->id));
			self::$_totalWeight[$this->id] = round((float)($result['nb']) + (float)($result2['nb']), 3);
		}
		return self::$_totalWeight[$this->id];
	}

	/**
	* Check discount validity
	*
	* @return mixed Return a string if an error occurred and false otherwise
	*/
	function checkDiscountValidity($discountObj, $discounts, $order_total, $products, $checkCartDiscount = false, $id_group_shop = false, $id_shop = false)
	{
		global $cookie;

		if (!$order_total)
			 return Tools::displayError('Cannot add voucher if order is free.');
		if (!$discountObj->active)
			return Tools::displayError('This voucher has already been used or is disabled.');
		if (!$discountObj->quantity)
			return Tools::displayError('This voucher has expired (usage limit attained).');
		if ($discountObj->id_discount_type == 2 AND $this->id_currency != $discountObj->id_currency)
			return Tools::displayError('This voucher can only be used in the following currency:').'
				'.Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `name` FROM `'._DB_PREFIX_.'currency` WHERE id_currency = '.(int)$discountObj->id_currency);
		if ($checkCartDiscount
			AND (
				$this->getDiscountsCustomer($discountObj->id) >= $discountObj->quantity_per_user
				OR (Order::getDiscountsCustomer((int)($cookie->id_customer), $discountObj->id) + $this->getDiscountsCustomer($discountObj->id) >= $discountObj->quantity_per_user) >= $discountObj->quantity_per_user
				)
			)
			return Tools::displayError('You cannot use this voucher anymore (usage limit attained).');
		if (strtotime($discountObj->date_from) > time())
			return Tools::displayError('This voucher is not yet valid');
		if (strtotime($discountObj->date_to) < time())
			return Tools::displayError('This voucher has expired.');
		if ($id_group_shop AND $id_shop)
			if (!$discountObj->availableWithShop((int)$id_group_shop, (int)$id_shop))
				return Tools::displayError('This voucher is not available with this shop.');
		if (sizeof($discounts) >= 1 AND $checkCartDiscount)
		{
			if (!$discountObj->cumulable)
				return Tools::displayError('This voucher is not valid with other current discounts.');
			foreach ($discounts as $discount)
				if (!$discount['cumulable'])
					return Tools::displayError('Voucher is not valid with other discounts.');
			
			foreach($discounts as $discount)
				if($discount['id_discount'] == $discountObj->id)
					return Tools::displayError('This voucher is already in your cart');
		}
		
		$groups = Customer::getGroupsStatic($this->id_customer);

		if (($discountObj->id_customer OR $discountObj->id_group) AND ($this->id_customer != $discountObj->id_customer AND !in_array($discountObj->id_group, $groups)))
		{
			if (!$cookie->isLogged())
				return Tools::displayError('You cannot use this voucher.').' - '.Tools::displayError('Please log in.');
			return Tools::displayError('You cannot use this voucher.');
		}
		$currentDate = date('Y-m-d');
		$onlyProductWithDiscount = true;
		if (!$discountObj->cumulable_reduction)
		{
			foreach ($products as $product)
				if (!$product['reduction_applies'] AND !$product['on_sale'])
					$onlyProductWithDiscount = false;
		}
		if (!$discountObj->cumulable_reduction AND $onlyProductWithDiscount)
			return Tools::displayError('This voucher is not valid for marked or reduced products.');
		$total_cart = 0;
		$categories = Discount::getCategories($discountObj->id);
		$returnErrorNoProductCategory = true;
		foreach($products AS $product)
		{
			if(count($categories))
				if (Product::idIsOnCategoryId($product['id_product'], $categories))
				{
					if ((!$discountObj->cumulable_reduction AND !$product['reduction_applies'] AND !$product['on_sale']) OR $discountObj->cumulable_reduction)
						$total_cart += $product['total_wt'];
					$returnErrorNoProductCategory = false;
				}
		}
		if ($returnErrorNoProductCategory)
			return Tools::displayError('This discount does not apply to that product category.');
		if ($total_cart < $discountObj->minimal)
			return Tools::displayError('The order total is not high enough or this voucher cannot be used with those products.');
		return false;
	}

	/**
	 * @param Discount $discountObj
	 * @return bool
	 * @deprecated
	 */
	public function hasProductInCategory($discountObj)
	{
		Tools::displayAsDeprecated();
		$products = $this->getProducts();
		$categories = Discount::getCategories($discountObj->id);
		foreach ($products AS $product)
		{
			if (Product::idIsOnCategoryId($product['id_product'], $categories))
				return true;
		}
		return false;
	}

	/**
	* Return useful informations for cart
	*
	* @return array Cart details
	*/
	function getSummaryDetails()
	{
		global $cookie;

		$delivery = new Address((int)($this->id_address_delivery));
		$invoice = new Address((int)($this->id_address_invoice));

		$total_tax = $this->getOrderTotal() - $this->getOrderTotal(false);

		if ($total_tax < 0)
			$total_tax = 0;

		$total_free_ship = 0;
		if ($free_ship = Tools::convertPrice((float)(Configuration::get('PS_SHIPPING_FREE_PRICE')), new Currency((int)($this->id_currency))))
		{
		    $discounts = $this->getDiscounts();
		    $total_free_ship =  $free_ship - ($this->getOrderTotal(true, Cart::ONLY_PRODUCTS) + $this->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
		    foreach ($discounts as $discount)
		    	if ($discount['id_discount_type'] == 3)
		    	{
		    		$total_free_ship = 0;
		    		break;
		    	}
		}

		return array(
			'delivery' => $delivery,
			'delivery_state' => State::getNameById($delivery->id_state),
			'invoice' => $invoice,
			'invoice_state' => State::getNameById($invoice->id_state),
			'carrier' => new Carrier((int)($this->id_carrier), $cookie->id_lang),
			'products' => $this->getProducts(false),
			'discounts' => $this->getDiscounts(false, true),
			'is_virtual_cart' => (int)$this->isVirtualCart(),
			'total_discounts' => $this->getOrderTotal(true, Cart::ONLY_DISCOUNTS),
			'total_discounts_tax_exc' => $this->getOrderTotal(false, Cart::ONLY_DISCOUNTS),
			'total_wrapping' => $this->getOrderTotal(true, Cart::ONLY_WRAPPING),
			'total_wrapping_tax_exc' => $this->getOrderTotal(false, Cart::ONLY_WRAPPING),
			'total_shipping' => $this->getOrderShippingCost(),
			'total_shipping_tax_exc' => $this->getOrderShippingCost(NULL, false),
			'total_products_wt' => $this->getOrderTotal(true, Cart::ONLY_PRODUCTS),
			'total_products' => $this->getOrderTotal(false, Cart::ONLY_PRODUCTS),
			'total_price' => $this->getOrderTotal(),
			'total_tax' => $total_tax,
			'total_price_without_tax' => $this->getOrderTotal(false),
			'free_ship' => $total_free_ship);
	}

	/**
	* Return carts thats have not been converted in orders
	*
	* @param string $dateFrom Select only cart updated after this date
	* @param string $dateTo Select only cart updated before this date
	* @return array Carts
	* @deprecated
	*/
	static function getNonOrderedCarts($dateFrom, $dateTo)
	{
		Tools::displayAsDeprecated();
		if (!Validate::isDate($dateFrom) OR !Validate::isDate($dateTo))
			die (Tools::displayError());

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT cart.`id_cart`, cart.`date_upd`, c.`id_customer` AS id_customer, c.`lastname` AS customer_lastname, c.`firstname` AS customer_firstname,
		SUM(cp.`quantity`) AS nb_products,
		COUNT(cd.`id_cart`) AS nb_discounts
		FROM `'._DB_PREFIX_.'cart` cart
		LEFT JOIN `'._DB_PREFIX_.'cart_product` cp ON cart.`id_cart` = cp.`id_cart`
		LEFT JOIN `'._DB_PREFIX_.'cart_discount` cd ON cart.`id_cart` = cd.`id_cart`
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON cart.`id_customer` = c.`id_customer`
		WHERE cart.`id_cart` NOT IN (SELECT `id_cart` FROM `'._DB_PREFIX_.'orders`)
		AND TO_DAYS(cart.`date_upd`) >= TO_DAYS(\''.pSQL(strftime('%Y-%m-%d %H:%M:%S', strtotime($dateFrom))).'\')
		AND TO_DAYS(cart.`date_upd`) <= TO_DAYS(\''.pSQL(strftime('%Y-%m-%d %H:%M:%S', strtotime($dateTo))).'\')
		GROUP BY cart.`id_cart`, cp.`id_cart`, cd.`id_cart`
		ORDER BY cart.`date_upd` DESC');
	}

	public function checkQuantities()
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return false;
		foreach ($this->getProducts() AS $product)
			if (!$product['active'] OR (!$product['allow_oosp'] AND $product['stock_quantity'] < $product['cart_quantity']) OR !$product['available_for_order'])
				return false;
		return true;
	}

	static public function lastNoneOrderedCart($id_customer)
	{
		$sql = 'SELECT c.`id_cart`
				FROM '._DB_PREFIX_.'cart c
				LEFT JOIN '._DB_PREFIX_.'orders o ON (c.`id_cart` = o.`id_cart`)
				WHERE c.`id_customer` = '.(int)($id_customer).'
					AND o.`id_cart` IS NULL
					'.Shop::sqlRestriction(true, 'c').'
				ORDER BY c.`date_upd` DESC';
	 	if (!$id_cart = Db::getInstance()->getValue($sql))
	 		return false;
	 	return $id_cart;
	}

	/**
	* Check if cart contains only virtual products
	* @return boolean true if is a virtual cart or false
	*
	*/
	public function isVirtualCart()
	{
		if (!isset(self::$_isVirtualCart[$this->id]))
		{
			$products = $this->getProducts();

			if (!sizeof($products))
				return false;

			$list = '';
			foreach ($products AS $product)
				$list .= (int)($product['id_product']).',';
			$list = rtrim($list, ',');

			$n = (int)Db::getInstance()->getValue('
			SELECT COUNT(`id_product_download`) n
			FROM `'._DB_PREFIX_.'product_download`
			WHERE `id_product` IN ('.pSQL($list).') AND `active` = 1');

			self::$_isVirtualCart[$this->id] = ($n == sizeof($products));
		}
		return self::$_isVirtualCart[$this->id];
	}

	static public function getCartByOrderId($id_order)
	{
		if ($id_cart = self::getCartIdByOrderId($id_order))
			return new Cart((int)($id_cart));

		return false;
	}

	static public function getCartIdByOrderId($id_order)
	{
		$result = Db::getInstance()->getRow('SELECT `id_cart` FROM '._DB_PREFIX_.'orders WHERE `id_order` = '.(int)$id_order);
		if (!$result OR empty($result) OR !key_exists('id_cart', $result))
			return false;
		return $result['id_cart'];
	}

	/*
	* Add customer's pictures
	*
	* @return bool Always true
	*/
	public function addPictureToProduct($id_product, $index, $identifier)
	{
		global $cookie;

		$varName = 'pictures_'.(int)($id_product).'_'.(int)($index);
		if ($cookie->$varName)
		{
			@unlink(_PS_UPLOAD_DIR_.$cookie->$varName);
			@unlink(_PS_UPLOAD_DIR_.$cookie->$varName.'_small');
		}
		$cookie->$varName = $identifier;
		return true;
	}

	/*
	* Add customer's text
	*
	* @return bool Always true
	*/
	public function addTextFieldToProduct($id_product, $index, $textValue)
	{
		global $cookie;
		$textValue = str_replace(array("\n", "\r"), '', nl2br($textValue));
		$textValue = str_replace('\\', '\\\\', $textValue);
		$textValue = str_replace('\'', '\\\'', $textValue);
		$cookie->{'textFields_'.(int)($id_product).'_'.(int)($index)} = $textValue;
		return true;
	}

	/*
	* Delete customer's text
	*
	* @return bool Always true
	*/
	public function deleteTextFieldFromProduct($id_product, $index)
	{
		global $cookie;

		unset($cookie->{'textFields_'.(int)($id_product).'_'.(int)($index)});
		return true;
	}

	/*
	* Remove a customer's picture
	*
	* @return bool
	*/
	public function deletePictureToProduct($id_product, $index)
	{
		global $cookie;

		$varName = 'pictures_'.(int)($id_product).'_'.(int)($index);
		// if cookie->varName is empty, use index which is the name of the picture
		$picture = !empty($cookie->$varName)?$cookie->$varName:$index;
		if ($picture)
		{
			if (!@unlink(_PS_UPLOAD_DIR_.$picture) OR !@unlink(_PS_UPLOAD_DIR_.$picture.'_small'))
				return false;
			unset($cookie->$varName);
			return true;
		}
		return false;
	}

	static public function deleteCustomizationInformations($id_product)
	{
		global $cookie;

		$cookie->unsetFamily('pictures_'.(int)($id_product).'_');
		$cookie->unsetFamily('textFields_'.(int)($id_product).'_');
		return true;
	}

	static public function getCustomerCarts($id_customer)
    {
	 	$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		 	SELECT *
			FROM '._DB_PREFIX_.'cart c
			WHERE c.`id_customer` = '.(int)($id_customer).'
			ORDER BY c.`date_add` DESC');
	 	return $result;
    }

	static public function replaceZeroByShopName($echo, $tr)
	{
		return ($echo == '0' ? Configuration::get('PS_SHOP_NAME') : $echo);
	}

	/* DEPRECATED */
	public function getCustomeremail()
	{
		Tools::displayAsDeprecated();
		$customer = new Customer((int)($this->id_customer));
		return $customer->email;
	}

	public function duplicate()
	{
		if (!Validate::isLoadedObject($this))
			return false;
		$cart = new Cart($this->id);
		$cart->id = NULL;
		$cart->id_shop = $this->id_shop;
		$cart->id_group_shop = $this->id_group_shop;
		
		$cart->add();

		if (!Validate::isLoadedObject($cart))
			return false;
		$success = true;
		$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)$this->id);
		foreach ($products AS $product)
			$success &= $cart->updateQty($product['quantity'], (int)$product['id_product'], (int)$product['id_product_attribute'], NULL, 'up', (int)$cart->id_shop, (int)$cart->id_group_shop);
		
		// Customized products
		$customs = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'customization c
		LEFT JOIN '._DB_PREFIX_.'customized_data cd ON cd.id_customization = c.id_customization
		WHERE c.id_cart = '.(int)$this->id);
		
		// Group line by id_customization
		$customsById = array();
		foreach ($customs AS $custom)
		{
			if(!isset($customsById[$custom['id_customization']]))
				$customsById[$custom['id_customization']] = array();
			$customsById[$custom['id_customization']][] = $custom;
		}
		
		// Insert new customizations
		$custom_ids = array();
		foreach($customsById as $customizationId => $val)
		{
			Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
				INSERT INTO '._DB_PREFIX_.'customization (id_customization, id_cart, id_product_attribute, id_product, quantity)
				VALUES(\'\', '.(int)$cart->id.', '.(int)$custom['id_product_attribute'].', '.(int)$custom['id_product'].', '.(int)$custom['quantity'].')');
			$custom_ids[$custom['id_customization']] = Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
		}
		
		// Insert customized_data
		if (sizeof($customs))
		{
			$first = true;
			$sql_custom_data = 'INSERT INTO '._DB_PREFIX_.'customized_data (`id_customization`, `type`, `index`, `value`) VALUES ';
			foreach ($customs AS $custom)
			{
				if(!$first)
					$sql_custom_data .= ',';
				else
					$first = false;
				$sql_custom_data .= '('.(int)$custom_ids[$custom['id_customization']].', '.(int)$custom['type'].', '.(int)$custom['index'].', \''.pSQL($custom['value']).'\')';
			}
			Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_custom_data);
		}
		
		return array('cart' => $cart, 'success' => $success);
	}

	public function getWsCartRows()
	{
		$query = 'SELECT id_product, id_product_attribute, quantity
		FROM `'._DB_PREFIX_.'cart_product`
		WHERE id_cart = '.(int)$this->id;
		$result = Db::getInstance()->executeS($query);
		return $result;
	}

	public function setWsCartRows($values)
	{
		if ($this->deleteAssociations())
		{
			$query = 'INSERT INTO `'._DB_PREFIX_.'cart_product`(`id_cart`, `id_product`, `id_product_attribute`, `quantity`, `date_add`) VALUES ';
			foreach ($values as $value)
				$query .= '('.(int)$this->id.', '.(int)$value['id_product'].', '.(int)$value['id_product_attribute'].', '.(int)$value['quantity'].', NOW()),';
			$result = Db::getInstance()->Execute(rtrim($query, ','));
		}
		return true;
	}

	public function deleteAssociations()
	{
		return (Db::getInstance()->Execute('
				DELETE FROM `'._DB_PREFIX_.'cart_product`
				WHERE `id_cart` = '.(int)($this->id)) !== false);
	}

	/**
	 * isGuestCartByCartId
	 *
	 * @param int $id_cart
	 * @return bool true if cart has been made by a guest customer
	 */
	static public function isGuestCartByCartId($id_cart)
	{
		if (!(int)$id_cart)
			return false;
		return (bool)Db::getInstance()->getValue('
			SELECT `is_guest`
			FROM `'._DB_PREFIX_.'customer` cu
			LEFT JOIN `'._DB_PREFIX_.'cart` ca ON (ca.`id_customer` = cu.`id_customer`)
			WHERE ca.`id_cart` = '.(int)$id_cart);
	}

	/**
	 * isCarrierInRange
	 *
	 * Check if the specified carrier is in range
	 *
	 * @id_carrier int
	 * @id_zone int
	 */
	public function isCarrierInRange($id_carrier, $id_zone)
	{
		$carrier = new Carrier((int)$id_carrier, Configuration::get('PS_LANG_DEFAULT'));
		$shippingMethod = $carrier->getShippingMethod();

		if ($shippingMethod == Carrier::SHIPPING_METHOD_FREE)
			return true;
		if ($shippingMethod == Carrier::SHIPPING_METHOD_WEIGHT
		AND (Carrier::checkDeliveryPriceByWeight((int)$id_carrier, $this->getTotalWeight(), $id_zone)))
			return true;
		if ($shippingMethod == Carrier::SHIPPING_METHOD_PRICE
			AND (Carrier::checkDeliveryPriceByPrice((int)$id_carrier, $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING), $id_zone, (int)$this->id_currency)))
			return true;

		return false;
	}
}

