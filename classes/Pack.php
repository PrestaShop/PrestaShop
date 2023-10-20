<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;

class PackCore extends Product
{
    /**
     * Only decrement pack quantity.
     *
     * @var int
     */
    public const STOCK_TYPE_PACK_ONLY = PackStockType::STOCK_TYPE_PACK_ONLY;

    /**
     * Only decrement pack products quantities.
     *
     * @var int
     */
    public const STOCK_TYPE_PRODUCTS_ONLY = PackStockType::STOCK_TYPE_PRODUCTS_ONLY;

    /**
     * Decrement pack quantity and pack products quantities.
     *
     * @var int
     */
    public const STOCK_TYPE_PACK_BOTH = PackStockType::STOCK_TYPE_BOTH;

    /**
     * Use pack quantity default setting.
     *
     * @var int
     */
    public const STOCK_TYPE_DEFAULT = PackStockType::STOCK_TYPE_DEFAULT;

    protected static $cachePackItems = [];
    protected static $cacheIsPack = [];
    protected static $cacheIsPacked = [];

    public static function resetStaticCache()
    {
        self::$cachePackItems = [];
        self::$cacheIsPack = [];
        self::$cacheIsPacked = [];
    }

    /**
     * Is product a pack?
     *
     * @param int $id_product
     *
     * @return bool
     */
    public static function isPack($id_product)
    {
        if (!Pack::isFeatureActive()) {
            return false;
        }

        if (!$id_product) {
            return false;
        }

        if (!array_key_exists($id_product, self::$cacheIsPack)) {
            $result = Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'pack` WHERE id_product_pack = ' . (int) $id_product);
            $productType = Db::getInstance()->getValue('SELECT product_type FROM `' . _DB_PREFIX_ . 'product` WHERE id_product = ' . (int) $id_product);
            self::$cacheIsPack[$id_product] = ($result > 0) || $productType === ProductType::TYPE_PACK;
        }

        return self::$cacheIsPack[$id_product];
    }

    /**
     * Is product in a pack?
     * If $id_product_attribute specified, then will restrict search on the given combination,
     * else this method will match a product if at least one of all its combination is in a pack.
     *
     * @param int $id_product
     * @param int|bool $id_product_attribute Optional combination of the product
     *
     * @return bool
     */
    public static function isPacked($id_product, $id_product_attribute = false)
    {
        if (!Pack::isFeatureActive()) {
            return false;
        }
        if ($id_product_attribute === false) {
            $cache_key = $id_product . '-0';
            if (!array_key_exists($cache_key, self::$cacheIsPacked)) {
                $result = Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'pack` WHERE id_product_item = ' . (int) $id_product);
                self::$cacheIsPacked[$cache_key] = ($result > 0);
            }

            return self::$cacheIsPacked[$cache_key];
        } else {
            $cache_key = $id_product . '-' . $id_product_attribute;
            if (!array_key_exists($cache_key, self::$cacheIsPacked)) {
                $result = Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'pack` WHERE id_product_item = ' . ((int) $id_product) . ' AND
					id_product_attribute_item = ' . ((int) $id_product_attribute));
                self::$cacheIsPacked[$cache_key] = ($result > 0);
            }

            return self::$cacheIsPacked[$cache_key];
        }
    }

    public static function noPackPrice($id_product)
    {
        $sum = 0;
        $price_display_method = !self::$_taxCalculationMethod;
        $items = Pack::getItems($id_product, Configuration::get('PS_LANG_DEFAULT'));
        foreach ($items as $item) {
            $pricePerItem = $item->getPrice($price_display_method, ($item->id_pack_product_attribute ? $item->id_pack_product_attribute : null));

            // Different calculation depending on rounding type
            switch (Configuration::get('PS_ROUND_TYPE')) {
                case Order::ROUND_TOTAL:
                    $sum += $pricePerItem * $item->pack_quantity;

                    break;
                case Order::ROUND_LINE:
                    $sum += Tools::ps_round(
                        $pricePerItem * $item->pack_quantity,
                        Context::getContext()->getComputingPrecision()
                    );

                    break;
                case Order::ROUND_ITEM:
                default:
                    $sum += Tools::ps_round(
                        $pricePerItem,
                        Context::getContext()->getComputingPrecision()
                    ) * $item->pack_quantity;

                    break;
            }
        }

        return $sum;
    }

    public static function noPackWholesalePrice($id_product)
    {
        $sum = 0;
        $items = Pack::getItems($id_product, Configuration::get('PS_LANG_DEFAULT'));
        foreach ($items as $item) {
            $sum += $item->wholesale_price * $item->pack_quantity;
        }

        return $sum;
    }

    public static function getItems($id_product, $id_lang)
    {
        if (!Pack::isFeatureActive()) {
            return [];
        }

        if (array_key_exists($id_product, self::$cachePackItems)) {
            return self::$cachePackItems[$id_product];
        }
        $result = Db::getInstance()->executeS('SELECT id_product_item, id_product_attribute_item, quantity FROM `' . _DB_PREFIX_ . 'pack` where id_product_pack = ' . (int) $id_product);
        $array_result = [];
        foreach ($result as $row) {
            $p = new Product($row['id_product_item'], false, $id_lang);
            $p->loadStockData();
            $p->pack_quantity = $row['quantity'];
            $p->id_pack_product_attribute = (isset($row['id_product_attribute_item']) && $row['id_product_attribute_item'] ? $row['id_product_attribute_item'] : 0);
            if (isset($row['id_product_attribute_item']) && $row['id_product_attribute_item']) {
                $sql = 'SELECT agl.`name` AS group_name, al.`name` AS attribute_name, pa.`reference` AS attribute_reference
					FROM `' . _DB_PREFIX_ . 'product_attribute` pa
					' . Shop::addSqlAssociation('product_attribute', 'pa') . '
					LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) Context::getContext()->language->id . ')
					LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) Context::getContext()->language->id . ')
					WHERE pa.`id_product_attribute` = ' . $row['id_product_attribute_item'] . '
					GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
					ORDER BY pa.`id_product_attribute`';

                $combinations = Db::getInstance()->executeS($sql);
                foreach ($combinations as $k => $combination) {
                    $p->name .= ' ' . $combination['group_name'] . '-' . $combination['attribute_name'];
                    $p->reference = $combination['attribute_reference'];
                }
            }
            $array_result[] = $p;
        }
        self::$cachePackItems[$id_product] = $array_result;

        return self::$cachePackItems[$id_product];
    }

    /**
     * Indicates if a pack and its associated products are available for orders in the desired quantity.
     *
     * @todo This method returns true even if the pack feature is not active.
     *       Should throw an exception instead.
     *       Developers should first test if product is a pack
     *       and then if it's in stock.
     *
     * @param int $idProduct
     * @param int $wantedQuantity
     * @param Cart|null $cart
     *
     * @return bool
     *
     * @throws PrestaShopException
     */
    public static function isInStock($idProduct, $wantedQuantity = 1, Cart $cart = null)
    {
        if (!Pack::isFeatureActive()) {
            return true;
        }
        $idProduct = (int) $idProduct;
        $wantedQuantity = (int) $wantedQuantity;
        $product = new Product($idProduct, false);
        $packQuantity = self::getQuantity($idProduct, null, null, $cart);

        if ($product->isAvailableWhenOutOfStock($product->out_of_stock)) {
            return true;
        } elseif ($wantedQuantity > $packQuantity) {
            return false;
        }

        return true;
    }

    /**
     * Returns the available quantity of a given pack (this method already have decreased products in cart).
     *
     * @param int $idProduct Product id
     * @param int|null $idProductAttribute Product attribute id (optional)
     * @param bool|null $cacheIsPack
     * @param CartCore|null $cart
     * @param int|null $idCustomization Product customization id (optional)
     *
     * @return int
     *
     * @throws PrestaShopException
     */
    public static function getQuantity(
        $idProduct,
        $idProductAttribute = null,
        $cacheIsPack = null,
        CartCore $cart = null,
        $idCustomization = null
    ) {
        $idProduct = (int) $idProduct;
        $idProductAttribute = (int) $idProductAttribute;

        if (!self::isPack($idProduct)) {
            throw new PrestaShopException("Product with id $idProduct is not a pack");
        }

        // Initialize
        $product = new Product($idProduct, false);
        $packQuantity = 0;
        $packQuantityInStock = StockAvailable::getQuantityAvailableByProduct(
            $idProduct,
            $idProductAttribute
        );
        $packStockType = $product->pack_stock_type;
        $allPackStockType = [
            self::STOCK_TYPE_PACK_ONLY,
            self::STOCK_TYPE_PRODUCTS_ONLY,
            self::STOCK_TYPE_PACK_BOTH,
            self::STOCK_TYPE_DEFAULT,
        ];

        if (!in_array($packStockType, $allPackStockType)) {
            throw new PrestaShopException('Unknown pack stock type');
        }

        // If no pack stock or shop default, set it
        if (empty($packStockType)
            || $packStockType == self::STOCK_TYPE_DEFAULT
        ) {
            $packStockType = Configuration::get('PS_PACK_STOCK_TYPE');
        }

        // Initialize with pack quantity if not only products
        if (in_array($packStockType, [self::STOCK_TYPE_PACK_ONLY, self::STOCK_TYPE_PACK_BOTH])) {
            $packQuantity = $packQuantityInStock;
        }

        // Set pack quantity to the minimum quantity of pack, or
        // product pack
        if (in_array($packStockType, [self::STOCK_TYPE_PACK_BOTH, self::STOCK_TYPE_PRODUCTS_ONLY])) {
            $items = array_values(Pack::getItems($idProduct, Configuration::get('PS_LANG_DEFAULT')));

            foreach ($items as $index => $item) {
                $itemQuantity = Product::getQuantity($item->id, $item->id_pack_product_attribute ?: null, null, $cart, $idCustomization);
                $nbPackAvailableForItem = (int) floor($itemQuantity / $item->pack_quantity);

                // Initialize packQuantity with the first product quantity
                // if pack decrement stock type is products only
                if ($index === 0
                    && $packStockType == self::STOCK_TYPE_PRODUCTS_ONLY
                ) {
                    $packQuantity = $nbPackAvailableForItem;

                    continue;
                }

                if ($nbPackAvailableForItem < $packQuantity) {
                    $packQuantity = $nbPackAvailableForItem;
                }
            }
        } elseif (!empty($cart)) {
            $cartProduct = $cart->getProductQuantity($idProduct, $idProductAttribute, $idCustomization);

            if (!empty($cartProduct['deep_quantity'])) {
                $packQuantity -= $cartProduct['deep_quantity'];
            }
        }

        return $packQuantity;
    }

    public static function getItemTable($id_product, $id_lang, $full = false)
    {
        if (!Pack::isFeatureActive()) {
            return [];
        }

        $context = Context::getContext();

        $sql = 'SELECT p.*, product_shop.*, pl.*, image_shop.`id_image` id_image, il.`legend`, cl.`name` AS category_default, a.quantity AS pack_quantity, product_shop.`id_category_default`, a.id_product_pack, a.id_product_attribute_item
				FROM `' . _DB_PREFIX_ . 'pack` a
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.id_product = a.id_product_item
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
					ON p.id_product = pl.id_product
					AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
				' . Shop::addSqlAssociation('product', 'p') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
					ON product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('cl') . '
				WHERE product_shop.`id_shop` = ' . (int) $context->shop->id . '
				AND a.`id_product_pack` = ' . (int) $id_product . '
				GROUP BY a.`id_product_item`, a.`id_product_attribute_item`';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        /** @var array{id_product: int, id_product_attribute_item: int|null, name: string} $line */
        foreach ($result as &$line) {
            if (Combination::isFeatureActive() && isset($line['id_product_attribute_item']) && $line['id_product_attribute_item']) {
                $line['cache_default_attribute'] = $line['id_product_attribute'] = $line['id_product_attribute_item'];

                $sql = 'SELECT pa.`reference` AS attribute_reference, agl.`name` AS group_name, al.`name` AS attribute_name,  pai.`id_image` AS id_product_attribute_image
				FROM `' . _DB_PREFIX_ . 'product_attribute` pa
				' . Shop::addSqlAssociation('product_attribute', 'pa') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = ' . $line['id_product_attribute_item'] . '
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) Context::getContext()->language->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) Context::getContext()->language->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON (' . $line['id_product_attribute_item'] . ' = pai.`id_product_attribute`)
				WHERE pa.`id_product` = ' . (int) $line['id_product'] . ' AND pa.`id_product_attribute` = ' . $line['id_product_attribute_item'] . '
				GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
				ORDER BY pa.`id_product_attribute`';

                $attr_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

                if (isset($attr_name[0]['id_product_attribute_image']) && $attr_name[0]['id_product_attribute_image']) {
                    $line['id_image'] = $attr_name[0]['id_product_attribute_image'];
                }

                $line['reference'] = $attr_name[0]['attribute_reference'] ?? '';

                $line['name'] .= "\n";
                foreach ($attr_name as $value) {
                    $line['name'] .= ' ' . $value['group_name'] . '-' . $value['attribute_name'];
                }
            }
            $line = Product::getTaxesInformations($line);
        }

        if (!$full) {
            return $result;
        }

        $array_result = [];
        foreach ($result as $prow) {
            if (!Pack::isPack($prow['id_product'])) {
                $prow['id_product_attribute'] = (int) $prow['id_product_attribute_item'];
                $array_result[] = Product::getProductProperties($id_lang, $prow);
            }
        }

        return $array_result;
    }

    public static function getPacksTable($id_product, $id_lang, $full = false, $limit = null)
    {
        if (!Pack::isFeatureActive()) {
            return [];
        }

        $packs = Db::getInstance()->getValue('
		SELECT GROUP_CONCAT(a.`id_product_pack`)
		FROM `' . _DB_PREFIX_ . 'pack` a
		WHERE a.`id_product_item` = ' . (int) $id_product);

        if (!(int) $packs) {
            return [];
        }

        $context = Context::getContext();

        $sql = '
		SELECT p.*, product_shop.*, pl.*, image_shop.`id_image` id_image, il.`legend`, IFNULL(product_attribute_shop.id_product_attribute, 0) id_product_attribute
		FROM `' . _DB_PREFIX_ . 'product` p
		NATURAL LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
		' . Shop::addSqlAssociation('product', 'p') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
	   		ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
			ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
		WHERE pl.`id_lang` = ' . (int) $id_lang . '
			' . Shop::addSqlRestrictionOnLang('pl') . '
			AND p.`id_product` IN (' . $packs . ')
		GROUP BY p.id_product';
        if ($limit) {
            $sql .= ' LIMIT ' . (int) $limit;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (!$full) {
            return $result;
        }

        $array_result = [];
        foreach ($result as $row) {
            if (!Pack::isPacked($row['id_product'])) {
                $array_result[] = Product::getProductProperties($id_lang, $row);
            }
        }

        return $array_result;
    }

    public static function deleteItems($id_product, $refreshCache = true)
    {
        $result = true;
        if ($refreshCache) {
            $result = Db::getInstance()->update('product', ['cache_is_pack' => 0], 'id_product = ' . (int) $id_product);
        }

        return $result &&
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'pack` WHERE `id_product_pack` = ' . (int) $id_product) &&
            Configuration::updateGlobalValue('PS_PACK_FEATURE_ACTIVE', Pack::isCurrentlyUsed());
    }

    /**
     * Add an item to the pack.
     *
     * @param int $id_product
     * @param int $id_item
     * @param int $qty
     * @param int $id_attribute_item
     *
     * @return bool true if everything was fine
     *
     * @throws PrestaShopDatabaseException
     */
    public static function addItem($id_product, $id_item, $qty, $id_attribute_item = 0)
    {
        $id_attribute_item = (int) $id_attribute_item ? (int) $id_attribute_item : Product::getDefaultAttribute((int) $id_item);

        return Db::getInstance()->update('product', ['cache_is_pack' => 1, 'product_type' => ProductType::TYPE_PACK], 'id_product = ' . (int) $id_product) &&
            Db::getInstance()->insert('pack', [
                'id_product_pack' => (int) $id_product,
                'id_product_item' => (int) $id_item,
                'id_product_attribute_item' => (int) $id_attribute_item,
                'quantity' => (int) $qty,
            ])
            && Configuration::updateGlobalValue('PS_PACK_FEATURE_ACTIVE', '1');
    }

    public static function duplicate($id_product_old, $id_product_new)
    {
        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'pack` (`id_product_pack`, `id_product_item`, `id_product_attribute_item`, `quantity`)
		(SELECT ' . (int) $id_product_new . ', `id_product_item`, `id_product_attribute_item`, `quantity` FROM `' . _DB_PREFIX_ . 'pack` WHERE `id_product_pack` = ' . (int) $id_product_old . ')');

        // If return query result, a non-pack product will return false
        return true;
    }

    /**
     * This method is allow to know if a feature is used or active.
     *
     * @since 1.5.0.1
     *
     * @return bool
     */
    public static function isFeatureActive()
    {
        return Configuration::get('PS_PACK_FEATURE_ACTIVE');
    }

    /**
     * This method is allow to know if a Pack entity is currently used.
     *
     * @since 1.5.0
     *
     * @param string|null $table Name of table linked to entity
     * @param bool $has_active_column True if the table has an active column
     *
     * @return bool
     */
    public static function isCurrentlyUsed($table = null, $has_active_column = false)
    {
        // We dont't use the parent method because the identifier isn't id_pack
        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_product_pack`
			FROM `' . _DB_PREFIX_ . 'pack`
		');
    }

    /**
     * For a given pack, tells if it has at least one product using the advanced stock management.
     *
     * @param int $id_product id_pack
     *
     * @return bool
     */
    public static function usesAdvancedStockManagement($id_product)
    {
        if (!Pack::isPack($id_product)) {
            return false;
        }

        $products = Pack::getItems($id_product, Configuration::get('PS_LANG_DEFAULT'));
        foreach ($products as $product) {
            // if one product uses the advanced stock management
            if ($product->advanced_stock_management == 1) {
                return true;
            }
        }
        // not used
        return false;
    }

    /**
     * For a given pack, tells if all products using the advanced stock management.
     *
     * @param int $id_product id_pack
     *
     * @return bool
     */
    public static function allUsesAdvancedStockManagement($id_product)
    {
        if (!Pack::isPack($id_product)) {
            return false;
        }

        $products = Pack::getItems($id_product, Configuration::get('PS_LANG_DEFAULT'));
        foreach ($products as $product) {
            // if one product uses the advanced stock management
            if ($product->advanced_stock_management == 0) {
                return false;
            }
        }
        // not used
        return true;
    }

    /**
     * Returns Packs that contains the given product in the right declinaison.
     *
     * @param int $id_item Product item id that could be contained in a|many pack(s)
     * @param int $id_attribute_item The declinaison of the product
     * @param int $id_lang
     *
     * @return array[Product] Packs that contains the given product
     */
    public static function getPacksContainingItem($id_item, $id_attribute_item, $id_lang)
    {
        if (!Pack::isFeatureActive() || !$id_item) {
            return [];
        }

        $query = 'SELECT `id_product_pack`, `quantity` FROM `' . _DB_PREFIX_ . 'pack`
			WHERE `id_product_item` = ' . ((int) $id_item);
        if (Combination::isFeatureActive()) {
            $query .= ' AND `id_product_attribute_item` = ' . ((int) $id_attribute_item);
        }
        $result = Db::getInstance()->executeS($query);
        $array_result = [];
        foreach ($result as $row) {
            $p = new Product($row['id_product_pack'], true, $id_lang);
            $p->loadStockData();
            $p->pack_item_quantity = $row['quantity']; // Specific need from StockAvailable::updateQuantity()
            $array_result[] = $p;
        }

        return $array_result;
    }
}
