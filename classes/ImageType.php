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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Class ImageTypeCore.
 */
class ImageTypeCore extends ObjectModel
{
    public $id;

    /** @var string Name */
    public $name;

    /** @var int Width */
    public $width;

    /** @var int Height */
    public $height;

    /** @var bool Apply to products */
    public $products;

    /** @var bool Apply to categories */
    public $categories;

    /** @var bool Apply to manufacturers */
    public $manufacturers;

    /** @var bool Apply to suppliers */
    public $suppliers;

    /** @var bool Apply to store */
    public $stores;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'image_type',
        'primary' => 'id_image_type',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isImageTypeName', 'required' => true, 'size' => 64],
            'width' => ['type' => self::TYPE_INT, 'validate' => 'isImageSize', 'required' => true],
            'height' => ['type' => self::TYPE_INT, 'validate' => 'isImageSize', 'required' => true],
            'categories' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'products' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'manufacturers' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'suppliers' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'stores' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    /**
     * @var array Image types cache
     */
    protected static $images_types_cache = [];

    protected static $images_types_name_cache = [];

    protected $webserviceParameters = [];

    /**
     * Returns image type definitions.
     *
     * @param string|null $type Image type
     * @param bool $orderBySize
     *
     * @return array Image type definitions
     *
     * @throws PrestaShopDatabaseException
     */
    public static function getImagesTypes($type = null, $orderBySize = false)
    {
        if (!isset(self::$images_types_cache[$type])) {
            $where = 'WHERE 1';
            if (!empty($type)) {
                $where .= ' AND `' . bqSQL($type) . '` = 1 ';
            }

            if ($orderBySize) {
                $query = 'SELECT * FROM `' . _DB_PREFIX_ . 'image_type` ' . $where . ' ORDER BY `width` DESC, `height` DESC, `name`ASC';
            } else {
                $query = 'SELECT * FROM `' . _DB_PREFIX_ . 'image_type` ' . $where . ' ORDER BY `name` ASC';
            }

            self::$images_types_cache[$type] = Db::getInstance()->executeS($query);
        }

        return self::$images_types_cache[$type];
    }

    /**
     * Check if type already is already registered in database.
     *
     * @param string $typeName Name
     *
     * @return int Number of results found
     */
    public static function typeAlreadyExists($typeName)
    {
        if (!Validate::isImageTypeName($typeName)) {
            die(Tools::displayError());
        }

        Db::getInstance()->executeS('
			SELECT `id_image_type`
			FROM `' . _DB_PREFIX_ . 'image_type`
			WHERE `name` = \'' . pSQL($typeName) . '\'', false);

        return Db::getInstance()->numRows();
    }

    /**
     * Finds image type definition by name and type.
     *
     * @param string $name
     * @param string $type
     */
    public static function getByNameNType($name, $type = null, $order = 0)
    {
        static $is_passed = false;

        if (!isset(self::$images_types_name_cache[$name . '_' . $type . '_' . $order]) && !$is_passed) {
            $results = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'image_type`');

            $types = ['products', 'categories', 'manufacturers', 'suppliers', 'stores'];
            $total = count($types);

            foreach ($results as $result) {
                foreach ($result as $value) {
                    for ($i = 0; $i < $total; ++$i) {
                        self::$images_types_name_cache[$result['name'] . '_' . $types[$i] . '_' . $value] = $result;
                    }
                }
            }

            $is_passed = true;
        }

        $return = false;
        if (isset(self::$images_types_name_cache[$name . '_' . $type . '_' . $order])) {
            $return = self::$images_types_name_cache[$name . '_' . $type . '_' . $order];
        }

        return $return;
    }

    /**
     * Get formatted name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function getFormattedName($name)
    {
        $themeName = Context::getContext()->shop->theme_name;
        $nameWithoutThemeName = str_replace(['_' . $themeName, $themeName . '_'], '', $name);

        //check if the theme name is already in $name if yes only return $name
        if ($themeName !== null && strstr($name, $themeName) && self::getByNameNType($name)) {
            return $name;
        }

        if (self::getByNameNType($nameWithoutThemeName . '_' . $themeName)) {
            return $nameWithoutThemeName . '_' . $themeName;
        }

        if (self::getByNameNType($themeName . '_' . $nameWithoutThemeName)) {
            return $themeName . '_' . $nameWithoutThemeName;
        }

        return $nameWithoutThemeName . '_default';
    }

    /**
     * Get all image types.
     *
     * @return array
     */
    public static function getAll()
    {
        $context = Context::getContext();
        if (isset($context->shop->theme)) {
            $imagesTypes = $context->shop->theme->get('image_types');

            return is_array($imagesTypes) ? $imagesTypes : [];
        }

        return [];
    }
}
