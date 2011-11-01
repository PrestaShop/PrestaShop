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

	public 		$checkedTos = false;
	public		$pictures;
	public		$textFields;

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
		$this->validateFields();

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

		$uploadedFiles = Db::getInstance()->executeS('
		SELECT cd.`value`
		FROM `'._DB_PREFIX_.'customized_data` cd
		INNER JOIN `'._DB_PREFIX_.'customization` c ON (cd.`id_customization`= c.`id_customization`)
		WHERE cd.`type`= 0 AND c.`id_cart`='.(int)$this->id);

		foreach ($uploadedFiles as $mustUnlink)
		{
			unlink(_PS_UPLOAD_DIR_.$mustUnlink['value'].'_small');
			unlink(_PS_UPLOAD_DIR_.$mustUnlink['value']);
		}

		Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'customized_data`
		WHERE `id_customization` IN (
			SELECT `id_customization`
			FROM `'._DB_PREFIX_.'customization`
			WHERE `id_cart`='.(int)$this->id.'
		)');

		Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'customization`
		WHERE `id_cart` = '.(int)$this->id);

		if (!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_cart_rule` WHERE `id_cart` = '.(int)($this->id))
		 OR !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)($this->id)))
			return false;

		return parent::delete();
	}

	public static function getTaxesAverageUsed($id_cart)
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
	 * @deprecated 1.5.0.1
	 */
	public function getDiscounts($lite = false, $refresh = false)
	{
		Tools::displayAsDeprecated();
		return $this->getCartRules();
	}
	
	/**
	 * Return cart discounts
	 *
	 * @param bool true will return discounts with basic informations
	 * @param bool true will erase the cache
	 * @result array Discounts
	 */
	public function getCartRules()
	{
		// TODO : add cache

		// If the cart has not been saved, then there can't be any cart rule applied
		if (!CartRule::isFeatureActive() || !$this->id)
			return array();

		$total_products_ti = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS);
		$total_products_te = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS);
		$shipping_ti = $this->getOrderShippingCost();
		$shipping_te = $this->getOrderShippingCost(NULL, false);
		
		$result = Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'cart_cart_rule` cd
		LEFT JOIN `'._DB_PREFIX_.'cart_rule` cr ON cd.`id_cart_rule` = cr.`id_cart_rule`
		LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (cd.`id_cart_rule` = cr.`id_cart_rule` AND crl.id_lang = '.(int)$this->id_lang.')
		WHERE `id_cart` = '.(int)$this->id);

		foreach ($result as &$row)
		{
			$cartRule = new CartRule($row['id_cart_rule'], (int)$this->id_lang);
			$row['value_real'] = $cartRule->getValue(true);
			$row['value_tax_exc'] = $cartRule->getValue(false);
			
			// Retro compatibility < 1.5.0.2
			$row['id_discount'] = $row['id_cart_rule'];
			$row['description'] = $row['name'];
		}

		return $result;
	}

	// Todo: see uses and change name
	public function getDiscountsCustomer($id_discount)
	{
		if (!CartRule::isFeatureActive())
			return 0;

		return Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'cart_cart_rule`
			WHERE `id_discount` = '.(int)($id_discount).' AND `id_cart` = '.(int)($this->id));
	}

	public function getLastProduct()
	{
		$sql = '
			SELECT `id_product`, `id_product_attribute`, id_shop
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
	public function getProducts($refresh = false, $id_product = false, $id_country = null)
	{
		if (!$this->id)
			return array();
		// Product cache must be strictly compared to NULL, or else an empty cart will add dozens of queries
		if ($this->_products !== NULL AND !$refresh)
		{
			// Return product row with specified ID if it exists
			if (is_int($id_product))
			{
				foreach ($this->_products as $product)
					if ($product['id_product'] == $id_product)
						return array($product);
				return array();
			}
			return $this->_products;
		}
		if (!$id_country)
			$id_country = Context::getContext()->country->id;

		// Build query
		$sql = new DbQuery();

		// Build SELECT
		$sql->select('cp.`id_product_attribute`, cp.`id_product`, cp.`quantity` AS cart_quantity, cp.id_shop, pl.`name`, p.`is_virtual`,
						pl.`description_short`, pl.`available_now`, pl.`available_later`, p.`id_product`, p.`id_category_default`, p.`id_supplier`, p.`id_manufacturer`,
						p.`on_sale`, p.`ecotax`, p.`additional_shipping_cost`, p.`available_for_order`, p.`price`, p.`weight`, p.`width`, p.`height`, p.`depth`, sa.`out_of_stock`,
						p.`active`, p.`date_add`, p.`date_upd`, t.`id_tax`, tl.`name` AS tax, t.`rate`, stock.quantity, pl.`link_rewrite`, cl.`link_rewrite` AS category,
						CONCAT(cp.`id_product`, cp.`id_product_attribute`) AS unique_id');

		// Build FROM
		$sql->from('cart_product cp');

		// Build JOIN
		$sql->leftJoin('product p ON p.`id_product` = cp.`id_product`');
		$sql->leftJoin('product_lang pl ON p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$this->id_lang.Context::getContext()->shop->addSqlRestrictionOnLang('pl'));
		$sql->leftJoin('tax_rule tr ON p.`id_tax_rules_group` = tr.`id_tax_rules_group`
										AND tr.`id_country` = '.(int)$id_country.'
										AND tr.`id_state` = 0
										AND tr.`zipcode_from` = 0');
		$sql->leftJoin('tax t ON t.`id_tax` = tr.`id_tax`');
		$sql->leftJoin('stock_available sa ON sa.`id_product` = p.`id_product` AND sa.id_product_attribute = 0');
		$sql->leftJoin('tax_lang tl ON t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)$this->id_lang);
		$sql->leftJoin('category_lang cl ON p.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->id_lang.Context::getContext()->shop->addSqlRestrictionOnLang('cl'));

		// @todo test if everything is ok, then refactorise call of this method
		Product::sqlStock('cp', 'cp', false, null, $sql);

		// Build WHERE clauses
		$sql->where('cp.`id_cart` = '.(int)$this->id);
		if ($id_product)
			$sql->where('cp.`id_product` = '.(int)$id_product);
		$sql->where('p.`id_product` IS NOT NULL');

		// Build GROUP BY
		$sql->groupBy('unique_id');

		// Build ORDER BY
		$sql->orderBy('cp.date_add ASC');

		if (Customization::isFeatureActive())
		{
			$sql->select('cu.`id_customization`, cu.`quantity` AS customization_quantity');
			$sql->leftJoin('customization cu ON p.`id_product` = cu.`id_product`');
		}

		if (Combination::isFeatureActive())
		{
			$sql->select('pa.`price` AS price_attribute, pa.`ecotax` AS ecotax_attr,
							IF (IFNULL(pa.`reference`, \'\') = \'\', p.`reference`, pa.`reference`) AS reference,
							IF (IFNULL(pa.`supplier_reference`, \'\') = \'\', p.`supplier_reference`, pa.`supplier_reference`) AS supplier_reference,
							(p.`weight`+ pa.`weight`) weight_attribute,
							IF (IFNULL(pa.`ean13`, \'\') = \'\', p.`ean13`, pa.`ean13`) AS ean13, IF (IFNULL(pa.`upc`, \'\') = \'\', p.`upc`, pa.`upc`) AS upc,
							pai.`id_image` as pai_id_image, il.`legend` as pai_legend, IFNULL(pa.`minimal_quantity`, p.`minimal_quantity`) as minimal_quantity, pa.`ecotax` AS ecotax_attr');

			$sql->leftJoin('product_attribute pa ON pa.`id_product_attribute` = cp.`id_product_attribute`');
			$sql->leftJoin('product_attribute_image pai ON pai.`id_product_attribute` = pa.`id_product_attribute`');
			$sql->leftJoin('image_lang il ON il.id_image = pai.id_image AND il.id_lang = '.(int)$this->id_lang);
		}
		else
			$sql->select('p.`reference` AS reference, p.`supplier_reference` AS supplier_reference, p.`ean13`, p.`upc` AS upc, p.`minimal_quantity` AS minimal_quantity');


		$result = Db::getInstance()->executeS($sql);

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
		foreach ($result AS $row)
		{
			if (isset($row['ecotax_attr']) && $row['ecotax_attr'] > 0)
				$row['ecotax'] = (float)($row['ecotax_attr']);
			$row['stock_quantity'] = (int)($row['quantity']);
			// for compatibility with 1.2 themes
			$row['quantity'] = (int)($row['cart_quantity']);
			if (isset($row['id_product_attribute']) && (int)$row['id_product_attribute'] && isset($row['weight_attribute']))
				$row['weight'] = $row['weight_attribute'];
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

				// In case when you use QuantityDiscount, getPriceStatic() can be return more of 2 decimals
				$row['price_wt'] = Tools::ps_round($row['price_wt'], 2);
				$row['total_wt'] = $row['price_wt'] * (int)($row['cart_quantity']);
				$row['total'] = Tools::ps_round($row['price'] * (int)($row['cart_quantity']), 2);
			}

			if (!isset($row['pai_id_image']) OR $row['pai_id_image'] == 0)
			{
				$row2 = Db::getInstance()->getRow('
				SELECT i.`id_image`, il.`legend`
				FROM `'._DB_PREFIX_.'image` i
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$this->id_lang.')
					WHERE i.`id_product` = '.(int)$row['id_product'].' AND i.`cover` = 1');
				if (!$row2)
					$row2 = array('id_image' => false, 'legend' => false);
					else
				$row = array_merge($row, $row2);
			}
			else
			{
				$row['id_image'] = $row['pai_id_image'];
				$row['legend'] = $row['pai_legend'];
			}

			$row['reduction_applies'] = ($specificPriceOutput AND (float)$specificPriceOutput['reduction']);
			$row['quantity_discount_applies'] = ($specificPriceOutput AND $row['cart_quantity'] >= (int)$specificPriceOutput['from_quantity']);
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
		if (!Combination::isFeatureActive())
			return;
		$paImplode = array();
		foreach ($ipaList as $id_product_attribute)
			if ((int)$id_product_attribute AND !array_key_exists($id_product_attribute.'-'.$id_lang, self::$_attributesLists))
			{
				$paImplode[] = (int)$id_product_attribute;
				self::$_attributesLists[(int)$id_product_attribute.'-'.$id_lang] = array('attributes' => '', 'attributes_small' => '');
			}
		if (!count($paImplode))
			return;

		$result = Db::getInstance()->executeS('
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
	 * @deprecated 1.5.0.1
	 */
	public function addDiscount($id_discount)
	{
		Tools::displayAsDeprecated();
		return $this->addCartRule($id_discount);
	}

	public function addCartRule($id_cart_rule)
	{
		return Db::getInstance()->AutoExecute(_DB_PREFIX_.'cart_cart_rule', array('id_cart_rule' => (int)$id_cart_rule, 'id_cart' => (int)$this->id), 'INSERT');
	}

	public function containsProduct($id_product, $id_product_attribute = 0, $id_customization = false)
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
	public	function updateQty($quantity, $id_product, $id_product_attribute = null, $id_customization = false, $operator = 'up', Shop $shop = null)
	{
		if (!$shop)
			$shop = Context::getContext()->shop;
		$quantity = (int)$quantity;
		$id_product = (int)$id_product;
		$id_product_attribute = (int)$id_product_attribute;
		$product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'), $shop->getID());

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
					$sql = 'SELECT stock.out_of_stock, stock.quantity
							FROM '._DB_PREFIX_.'product p
							'.Product::sqlStock('p', $id_product_attribute, true, $shop).'
							WHERE p.id_product = '.$id_product;
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
					Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'cart_product`
					SET `quantity` = `quantity` '.$qty.', `date_add` = NOW()
					WHERE `id_product` = '.(int)$id_product.
					(!empty($id_product_attribute) ? ' AND `id_product_attribute` = '.(int)$id_product_attribute : '').'
					AND `id_cart` = '.(int)$this->id.'
					LIMIT 1');
			}

			/* Add product to the cart */
			else
			{
				$sql = 'SELECT stock.out_of_stock, stock.quantity
						FROM '._DB_PREFIX_.'product p
						'.Product::sqlStock('p', $id_product_attribute, true, $shop).'
						WHERE p.id_product = '.$id_product;
				$result2 = Db::getInstance()->getRow($sql);
				if (!Product::isAvailableWhenOutOfStock((int)$result2['out_of_stock']))
					if ((int)$quantity > $result2['quantity'])
						return false;

				if ((int)$quantity < $minimalQuantity)
					return -1;

				$resultAdd = Db::getInstance()->AutoExecute(_DB_PREFIX_.'cart_product', array(
					'id_product' => 			(int)$id_product,
					'id_product_attribute' => 	(int)$id_product_attribute,
					'id_cart' => 				(int)$this->id,
					'id_shop' => 				$shop->getID(true),
					'quantity' => 				(int)$quantity,
					'date_add' => 				date('Y-m-d H:i:s')
				), 'INSERT');
				if (!$resultAdd)
					return false;
			}
		}
		// refresh cache of self::_products
		$this->_products = $this->getProducts(true);
		$this->update(true);
		$context = Context::getContext()->cloneContext();
		$context->cart = $this;
		CartRule::autoAddToCart($context);

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
		// Link customization to product combination when it is first added to cart
		if (empty($id_customization))
		{
			$customization = $this->getProductCustomization($id_product, null, true);
			foreach ($customization as $field)
			{
				if ($field['quantity'] == 0)
				{
					Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'customization`
					SET `quantity` = '.(int)($quantity).',
						`id_product_attribute` = '.(int)$id_product_attribute.',
						`in_cart` = 1
					WHERE `id_customization` = '.(int)$field['id_customization']);
				}
			}
		}

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
					return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization` WHERE `id_customization` = '.(int)$id_customization);
				return Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'customization`
					SET `quantity` = `quantity` '.($operator == 'up' ? '+ ' : '- ').(int)($quantity).'
					WHERE `id_customization` = '.(int)($id_customization));
			}
		}
		// refresh cache of self::_products
		$this->_products = $this->getProducts(true);
		$this->update(true);
		return true;
	}

	/**
	 * Add customization item to database
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $index
	 * @param int $type
	 * @param string $field
	 * @param int $quantity
	 * @return boolean success
	 */
	public function _addCustomization($id_product, $id_product_attribute, $index, $type, $field, $quantity)
	{
		$exising_customization = Db::getInstance()->executeS('
			SELECT cu.`id_customization`, cd.`index`, cd.`value`, cd.`type` FROM `'._DB_PREFIX_.'customization` cu
			LEFT JOIN `'._DB_PREFIX_.'customized_data` cd
			ON cu.`id_customization` = cd.`id_customization`
			WHERE cu.id_cart = '.(int)$this->id.'
			AND cu.id_product = '.(int)$id_product.'
			AND in_cart = 0');

		if ($exising_customization)
		{
			// If the customization field is alreay filled, delete it
			foreach($exising_customization as $customization)
			{
				if ($customization['type'] == $type && $customization['index'] == $index)
				{
					Db::getInstance()->execute('
						DELETE FROM `'._DB_PREFIX_.'customized_data`
						WHERE id_customization = '.(int)$customization['id_customization'].'
						AND type = '.(int)$customization['type'].'
						AND `index` = '.(int)$customization['index']);
					if ($type == Product::CUSTOMIZE_FILE)
					{
						@unlink(_PS_UPLOAD_DIR_.$customization['value']);
						@unlink(_PS_UPLOAD_DIR_.$customization['value'].'_small');
					}
					break;
				}
			}
			$id_customization = $exising_customization[0]['id_customization'];
		}
		else
		{
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'customization` (`id_cart`, `id_product`, `id_product_attribute`, `quantity`) VALUES ('.(int)($this->id).', '.(int)($id_product).', '.(int)($id_product_attribute).', '.(int)($quantity).')');
			$id_customization = Db::getInstance()->Insert_ID();
		}

		$query = 'INSERT INTO `'._DB_PREFIX_.'customized_data` (`id_customization`, `type`, `index`, `value`) VALUES ('.(int)$id_customization.', '.(int)$type.', '.(int)$index.', \''.pSql($field).'\')';

		if (!Db::getInstance()->execute($query))
			return false;
		return true;
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

	/**
	 * @deprecated 1.5.0.1
	 */
	public function deleteDiscount($id_discount)
	{
		Tools::displayAsDeprecated();
		return $this->removeCartRule($id_discount);
	}

	public function removeCartRule($id_cart_rule)
	{
		return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_cart_rule` WHERE `id_cart_rule` = '.(int)$id_cart_rule.' AND `id_cart` = '.(int)$this->id.' LIMIT 1');
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
		if ((int)$id_customization)
		{
			$productTotalQuantity = (int)Db::getInstance()->getValue('SELECT `quantity`
				FROM `'._DB_PREFIX_.'cart_product`
				WHERE `id_product` = '.(int)$id_product.' AND `id_product_attribute` = '.(int)$id_product_attribute);
			$customizationQuantity = (int)Db::getInstance()->getValue('SELECT `quantity`
				FROM `'._DB_PREFIX_.'customization`
				WHERE `id_cart` = '.(int)$this->id.'
					AND `id_product` = '.(int)$id_product.'
					AND `id_product_attribute` = '.(int)$id_product_attribute);
			if (!$this->_deleteCustomization((int)$id_customization, (int)$id_product, (int)$id_product_attribute))
				return false;
			// refresh cache of self::_products
			$this->_products = $this->getProducts(true);
			return ($customizationQuantity == $productTotalQuantity && $this->deleteProduct((int)$id_product, $id_product_attribute, null));
		}

		/* Get customization quantity */
		if (($result = Db::getInstance()->getRow('
			SELECT SUM(`quantity`) AS \'quantity\'
			FROM `'._DB_PREFIX_.'customization`
			WHERE `id_cart` = '.(int)$this->id.'
			AND `id_product` = '.(int)$id_product.'
			AND `id_product_attribute` = '.(int)$id_product_attribute)
		) === false)
			return false;

		/* If the product still possesses customization it does not have to be deleted */
		if (Db::getInstance()->NumRows() AND (int)($result['quantity']))
			return Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'cart_product`
				SET `quantity` = '.(int)($result['quantity']).'
				WHERE `id_cart` = '.(int)($this->id).'
				AND `id_product` = '.(int)($id_product).
				($id_product_attribute != NULL ? ' AND `id_product_attribute` = '.(int)($id_product_attribute) : ''));

		/* Product deletion */
		if (Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_product` = '.(int)($id_product).(!is_null($id_product_attribute) ? ' AND `id_product_attribute` = '.(int)($id_product_attribute) : '').' AND `id_cart` = '.(int)($this->id)))
		{
			// refresh cache of self::_products
			$this->_products = $this->getProducts(true);
			/* Update cart */
			return $this->update(true);
		}
		return false;
	}

	/**
	 * Delete a customization from the cart. If customization is a Picture,
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

		if ($customization)
		{
			$custData = Db::getInstance()->getRow('SELECT *
				FROM `'._DB_PREFIX_.'customized_data`
				WHERE `id_customization` = '.(int)($id_customization));

			// Delete customization picture if necessary
			if (isset($custData['type']) and $custData['type'] == 0)
				$result &= (@unlink(_PS_UPLOAD_DIR_.$custData['value']) && @unlink(_PS_UPLOAD_DIR_.$custData['value'].'_small'));

			$result &= Db::getInstance()->execute('DELETE
				FROM `'._DB_PREFIX_.'customized_data`
				WHERE `id_customization` = '.(int)($id_customization));

			if($result)
				$result &= Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'cart_product`
					SET `quantity` = `quantity` - '.(int)($customization['quantity']).'
					WHERE `id_cart` = '.(int)($this->id).'
					AND `id_product` = '.(int)($id_product).((int)($id_product_attribute) ? '
					AND `id_product_attribute` = '.(int)($id_product_attribute) : ''));

			if (!$result)
				return false;

			return Db::getInstance()->execute('DELETE
				FROM `'._DB_PREFIX_.'customization`
				WHERE `id_customization` = '.(int)($id_customization));
		}

		return true;
	}

	public static function getTotalCart($id_cart, $use_tax_display = false)
	{
		$cart = new Cart($id_cart);
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
		$type = (int)$type;
		if (!in_array($type, array(Cart::ONLY_PRODUCTS, Cart::ONLY_DISCOUNTS, Cart::BOTH, Cart::BOTH_WITHOUT_SHIPPING, Cart::ONLY_SHIPPING, Cart::ONLY_WRAPPING, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING)))
			die(Tools::displayError());

		// if discounts are never used
		// Todo: remove and replace by cart rules
		if ($type == Cart::ONLY_DISCOUNTS && !CartRule::isFeatureActive())
			return 0;
		// no shipping cost if is a cart with only virtuals products
		$virtual = $this->isVirtualCart();
		if ($virtual AND $type ==  Cart::ONLY_SHIPPING)
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
				$price = Product::getPriceStatic((int)($product['id_product']), true, (int)($product['id_product_attribute']), 2, NULL, false, true, $product['cart_quantity'], false, ((int)($this->id_customer) ? (int)($this->id_customer) : NULL), (int)($this->id), ((int)($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) ? (int)($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) : NULL));
				$total_price = Tools::ps_round($price, 2) * (int)($product['cart_quantity']);
				if (!$withTaxes)
					$total_price = Tools::ps_round($total_price / (1 + ((float)(Tax::getProductTaxRate((int)$product['id_product'], (int)$this->{Configuration::get('PS_TAX_ADDRESS_TYPE')})) / 100)), 2);
			}
			$order_total += $total_price;
		}
		$order_total_products = $order_total;
		// Todo: consider optimizations
		if ($type == Cart::ONLY_DISCOUNTS)
			$order_total = 0;
		// Wrapping Fees
		$wrapping_fees = 0;
		if ($this->gift)
		{
			$wrapping_fees = (float)Configuration::get('PS_GIFT_WRAPPING_PRICE');
			if ($withTaxes)
			{
				$wrapping_fees_tax = new Tax(Configuration::get('PS_GIFT_WRAPPING_TAX'));
				$wrapping_fees *= 1 + ((float)$wrapping_fees_tax->rate / 100);
			}
			$wrapping_fees = Tools::convertPrice(Tools::ps_round($wrapping_fees, 2), Currency::getCurrencyInstance((int)($this->id_currency)));
		}
		
		$order_total_discount = 0;
		if ($type != Cart::ONLY_PRODUCTS && CartRule::isFeatureActive())
		{
			$result = $this->getCartRules();
			foreach (ObjectModel::hydrateCollection('CartRule', $result, Configuration::get('PS_LANG_DEFAULT')) AS $cartRule)
				$order_total_discount += Tools::ps_round($cartRule->getValue($withTaxes));
			$order_total_discount = min(Tools::ps_round($order_total_discount), $wrapping_fees + $order_total_products + $shipping_fees);
			$order_total -= $order_total_discount;
		}

		if ($type == Cart::ONLY_SHIPPING)
			return $shipping_fees;
		if ($type == Cart::ONLY_WRAPPING)
			return $wrapping_fees;
		if ($type == Cart::BOTH)
			$order_total += $shipping_fees + $wrapping_fees;
		if ($order_total < 0 AND $type != Cart::ONLY_DISCOUNTS)
			return 0;
		if ($type == Cart::ONLY_DISCOUNTS)
			return $order_total_discount;
		return Tools::ps_round((float)$order_total, 2);
	}

	/**
	* Return shipping total
	*
	* @param integer $id_carrier Carrier ID (default : current carrier)
	* @return float Shipping total
	*/
    function getOrderShippingCost($id_carrier = NULL, $useTax = true, Country $default_country = null)
    {
		if ($this->isVirtualCart())
			return 0;

		if (!$default_country)
			$default_country = Context::getContext()->country;

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
			if (!Validate::isLoadedObject($default_country))
				$default_country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'), Configuration::get('PS_LANG_DEFAULT'));
			$id_zone = (int)$default_country->id_zone;
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
					$shipping = $carrier->getDeliveryPriceByWeight($this->getTotalWeight(), $id_zone);
				else
					$shipping = $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int)($this->id_currency));

				if (!isset($minShippingPrice))
					$minShippingPrice = $shipping;

				if ($shipping <= $minShippingPrice)
					{
						$id_carrier = (int)($row['id_carrier']);
					$minShippingPrice = $shipping;
				}
			}
		}

		if (empty($id_carrier))
			$id_carrier = Configuration::get('PS_CARRIER_DEFAULT');

		if (!isset(self::$_carriers[$id_carrier]))
			self::$_carriers[$id_carrier] = new Carrier($id_carrier, Configuration::get('PS_LANG_DEFAULT'));
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
			 $carrierTax = $carrier->getTaxesRate(new Address((int)$this->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));

		$configuration = Configuration::getMultiple(array('PS_SHIPPING_FREE_PRICE', 'PS_SHIPPING_HANDLING', 'PS_SHIPPING_METHOD', 'PS_SHIPPING_FREE_WEIGHT'));
		// Free fees
		$free_fees_price = 0;
		if (isset($configuration['PS_SHIPPING_FREE_PRICE']))
			$free_fees_price = Tools::convertPrice((float)($configuration['PS_SHIPPING_FREE_PRICE']), Currency::getCurrencyInstance((int)($this->id_currency)));
		// $orderTotalwithDiscounts = $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
		// if ($orderTotalwithDiscounts >= (float)($free_fees_price) AND (float)($free_fees_price) > 0)
			// return $shipping_cost;
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
					$id_zone = (int)$default_country->id_zone;
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

		// TODO : $products does not exists
		// Additional Shipping Cost per product
		// foreach($products AS $product)
			// $shipping_cost += $product['additional_shipping_cost'] * $product['cart_quantity'];

		$shipping_cost = Tools::convertPrice($shipping_cost, Currency::getCurrencyInstance((int)($this->id_currency)));

		//get external shipping cost from module
		if ($carrier->shipping_external)
		{
			$moduleName = $carrier->external_module_name;
			$module = Module::getInstanceByName($moduleName);

			if (Validate::isLoadedObject($module))
			{
				if (array_key_exists('id_carrier', $module))
				$module->id_carrier = $carrier->id;
			if($carrier->need_range)
				$shipping_cost = $module->getOrderShippingCost($this, $shipping_cost);
			else
				$shipping_cost = $module->getOrderShippingCostExternal($this);

			// Check if carrier is available
			if ($shipping_cost === false)
				return false;
		}
			else
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
			if (Combination::isFeatureActive())
				$weight_product_with_attribute = Db::getInstance()->getValue('
				SELECT SUM((p.`weight` + pa.`weight`) * cp.`quantity`) as nb
				FROM `'._DB_PREFIX_.'cart_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p ON (cp.`id_product` = p.`id_product`)
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (cp.`id_product_attribute` = pa.`id_product_attribute`)
				WHERE (cp.`id_product_attribute` IS NOT NULL AND cp.`id_product_attribute` != 0)
				AND cp.`id_cart` = '.(int)($this->id));
			else
				$weight_product_with_attribute = 0;

			$weight_product_without_attribute = Db::getInstance()->getValue('
			SELECT SUM(p.`weight` * cp.`quantity`) as nb
			FROM `'._DB_PREFIX_.'cart_product` cp
			LEFT JOIN `'._DB_PREFIX_.'product` p ON (cp.`id_product` = p.`id_product`)
			WHERE (cp.`id_product_attribute` IS NULL OR cp.`id_product_attribute` = 0)
			AND cp.`id_cart` = '.(int)($this->id));

			self::$_totalWeight[$this->id] = round((float)$weight_product_with_attribute + (float)$weight_product_without_attribute, 3);
		}
		return self::$_totalWeight[$this->id];
	}
	
	/**
	 * @deprecated 1.5.0.1
	 */
	public function checkDiscountValidity($discountObj, $discounts, $order_total, $products, $checkCartDiscount = false)
	{
		Tools::displayAsDeprecated();
		$context = Context::getContext()->cloneContext();
		$context->cart = $this;
		return $discountObj->checkValidity($context);
	}

	/**
	* Return useful informations for cart
	*
	* @return array Cart details
	*/
	public function getSummaryDetails($id_lang = null)
	{
		if (!$id_lang)
			$id_lang = Context::getContext()->language->id;

		$delivery = new Address((int)($this->id_address_delivery));
		$invoice = new Address((int)($this->id_address_invoice));

		// New layout system with personalization fields
		$formattedAddresses['invoice'] = AddressFormat::getFormattedLayoutData($invoice);
		$formattedAddresses['delivery'] = AddressFormat::getFormattedLayoutData($delivery);

		$total_tax = $this->getOrderTotal() - $this->getOrderTotal(false);

		if ($total_tax < 0)
			$total_tax = 0;

		$total_free_ship = 0;
		if ($free_ship = Tools::convertPrice((float)(Configuration::get('PS_SHIPPING_FREE_PRICE')), new Currency((int)($this->id_currency))))
		{
		    $discounts = $this->getCartRules();
		    $total_free_ship =  $free_ship - ($this->getOrderTotal(true, Cart::ONLY_PRODUCTS) + $this->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
		    foreach ($discounts as $discount)
		    	if ($discount['id_discount_type'] == Discount::FREE_SHIPPING)
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
			'formattedAddresses' => $formattedAddresses,
			'carrier' => new Carrier($this->id_carrier, $id_lang),
			'products' => $this->getProducts(false),
			'discounts' => $this->getCartRules(),
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

	public function checkQuantities()
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return false;
		foreach ($this->getProducts() AS $product)
			if (!$product['active'] OR (!$product['allow_oosp'] AND $product['stock_quantity'] < $product['cart_quantity']) OR !$product['available_for_order'])
				return false;
		return true;
	}

	public static function lastNoneOrderedCart($id_customer)
	{
		$sql = 'SELECT c.`id_cart`
				FROM '._DB_PREFIX_.'cart c
				LEFT JOIN '._DB_PREFIX_.'orders o ON (c.`id_cart` = o.`id_cart`)
				WHERE c.`id_customer` = '.(int)($id_customer).'
					AND o.`id_cart` IS NULL
					'.Context::getContext()->shop->addSqlRestriction(Shop::SHARE_ORDER, 'c').'
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
	public function isVirtualCart($strict = false)
	{
		if (!ProductDownload::isFeatureActive())
			return false;

		if (!isset(self::$_isVirtualCart[$this->id]))
		{
			$products = $this->getProducts();
			if (!sizeof($products))
				return false;

			$is_virtual = 1;
			foreach ($products AS $product)
			{
				if (empty($product['is_virtual']))
					$is_virtual = 0;
			}
			self::$_isVirtualCart[$this->id] = (int) $is_virtual;
		}

		return self::$_isVirtualCart[$this->id];
	}

	public static function getCartByOrderId($id_order)
	{
		if ($id_cart = self::getCartIdByOrderId($id_order))
			return new Cart((int)($id_cart));

		return false;
	}

	public static function getCartIdByOrderId($id_order)
	{
		$result = Db::getInstance()->getRow('SELECT `id_cart` FROM '._DB_PREFIX_.'orders WHERE `id_order` = '.(int)$id_order);
		if (!$result OR empty($result) OR !key_exists('id_cart', $result))
			return false;
		return $result['id_cart'];
	}

	/*
	* Add customer's text
	*
	* @return bool Always true
	*/
	public function addTextFieldToProduct($id_product, $index, $type, $textValue)
	{
		$textValue = str_replace(array("\n", "\r"), '', nl2br($textValue));
		$textValue = str_replace('\\', '\\\\', $textValue);
		$textValue = str_replace('\'', '\\\'', $textValue);
		return $this->_addCustomization($id_product, 0, $index, $type, $textValue, 0);
	}

	/*
	* Add customer's pictures
	*
	* @return bool Always true
	*/
	public function addPictureToProduct($id_product, $index, $type, $file)
	{
		return $this->_addCustomization($id_product, 0, $index, $type, $file, 0);
	}

	/*
	* Remove a customer's customization
	*
	* @return bool
	*/
	public function deleteCustomizationToProduct($id_product, $index)
	{
		$result = true;

		$custData = Db::getInstance()->getRow('
		SELECT cu.`id_customization`, cd.`index`, cd.`value`, cd.`type` FROM `'._DB_PREFIX_.'customization` cu
		LEFT JOIN `'._DB_PREFIX_.'customized_data` cd
		ON cu.`id_customization` = cd.`id_customization`
		WHERE cu.`id_cart` = '.(int)$this->id.'
		AND cu.`id_product` = '.(int)$id_product.'
		AND `index` = '.(int)$index.'
		AND `in_cart` = 0'
		);

		// Delete customization picture if necessary
		if ($custData['type'] == 0)
			$result &= (@unlink(_PS_UPLOAD_DIR_.$custData['value']) && @unlink(_PS_UPLOAD_DIR_.$custData['value'].'_small'));

		$result &= Db::getInstance()->execute('DELETE
			FROM `'._DB_PREFIX_.'customized_data`
			WHERE `id_customization` = '.(int)$custData['id_customization'].'
			AND `index` = '.(int)$index
		);
		return $result;
	}

	/**
	 * Return custom pictures in this cart for a specified product
	 *
	 * @param int $id_product
	 * @param int $type only return customization of this type
	 * @param bool $not_in_cart only return customizations that are not in cart already
	 * @return array result rows
	 */
	public function getProductCustomization($id_product, $type = null, $not_in_cart = false)
	{
		if (!Customization::isFeatureActive())
			return array();
		$result = Db::getInstance()->executeS('
			SELECT cu.id_customization, cd.index, cd.value, cd.type, cu.in_cart, cu.quantity
			FROM `'._DB_PREFIX_.'customization` cu
			LEFT JOIN `'._DB_PREFIX_.'customized_data` cd ON (cu.`id_customization` = cd.`id_customization`)
			WHERE cu.id_cart = '.(int)$this->id.'
			AND cu.id_product = '.(int)$id_product.
			($type === Product::CUSTOMIZE_FILE ? ' AND type = '.(int)Product::CUSTOMIZE_FILE : '').
			($type === Product::CUSTOMIZE_TEXTFIELD ? ' AND type = '.(int)Product::CUSTOMIZE_TEXTFIELD : '').
			($not_in_cart ? ' AND in_cart = 0' : '')
		);
		return $result;
	}

	public static function getCustomerCarts($id_customer)
    {
	 	$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		 	SELECT *
			FROM '._DB_PREFIX_.'cart c
			WHERE c.`id_customer` = '.(int)($id_customer).'
			ORDER BY c.`date_add` DESC');
	 	return $result;
    }

	public static function replaceZeroByShopName($echo, $tr)
	{
		return ($echo == '0' ? Configuration::get('PS_SHOP_NAME') : $echo);
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
		$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)$this->id);

		foreach ($products AS $product)
			$success &= $cart->updateQty($product['quantity'], (int)$product['id_product'], (int)$product['id_product_attribute'], NULL, 'up', new Shop($cart->id_shop));

		// Customized products
		$customs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM '._DB_PREFIX_.'customization c
		LEFT JOIN '._DB_PREFIX_.'customized_data cd ON cd.id_customization = c.id_customization
		WHERE c.id_cart = '.(int)$this->id);

		// Get datas from customization table
		$customsById = array();
		foreach ($customs AS $custom)
		{
			if(!isset($customsById[$custom['id_customization']]))
				$customsById[$custom['id_customization']] = array('id_product_attribute' => $custom['id_product_attribute'],
				'id_product' => $custom['id_product'], 'quantity' => $custom['quantity']);
		}

		// Insert new customizations
		$custom_ids = array();
		foreach($customsById as $customizationId => $val)
		{
			Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'customization` (id_cart, id_product_attribute, id_product, quantity)
			VALUES('.(int)$cart->id.', '.(int)$val['id_product_attribute'].', '.(int)$val['id_product'].', '.(int)$val['quantity'].')');
			$custom_ids[$customizationId] = Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
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
			Db::getInstance()->execute($sql_custom_data);
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
				$query .= '('.(int)$this->id.', '.(int)$value['id_product'].', '.(isset($value['id_product_attribute']) ? (int)$value['id_product_attribute'] : 'NULL').', '.(int)$value['quantity'].', NOW()),';
			Db::getInstance()->execute(rtrim($query, ','));
		}
		return true;
	}

	public function deleteAssociations()
	{
		return (Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'cart_product`
				WHERE `id_cart` = '.(int)($this->id)) !== false);
	}

	/**
	 * isGuestCartByCartId
	 *
	 * @param int $id_cart
	 * @return bool true if cart has been made by a guest customer
	 */
	public static function isGuestCartByCartId($id_cart)
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
		if (!$carrier->range_behavior)
			return true;

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

