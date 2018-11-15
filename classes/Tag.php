<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class TagCore.
 */
class TagCore extends ObjectModel
{
    /** @var int Language id */
    public $id_lang;

    /** @var string Name */
    public $name;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'tag',
        'primary' => 'id_tag',
        'fields' => array(
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
        ),
    );

    protected $webserviceParameters = array(
        'fields' => array(
            'id_lang' => array('xlink_resource' => 'languages'),
        ),
    );

    public function __construct($id = null, $name = null, $idLang = null)
    {
        $this->def = Tag::getDefinition($this);
        $this->setDefinitionRetrocompatibility();

        if ($id) {
            parent::__construct($id);
        } elseif ($name && Validate::isGenericName($name) && $idLang && Validate::isUnsignedId($idLang)) {
            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'tag` t
            WHERE `name` = \'' . pSQL($name) . '\' AND `id_lang` = ' . (int) $idLang);

            if ($row) {
                $this->id = (int) $row['id_tag'];
                $this->id_lang = (int) $row['id_lang'];
                $this->name = $row['name'];
            }
        }
    }

    public function add($autoDate = true, $nullValues = false)
    {
        if (!parent::add($autoDate, $nullValues)) {
            return false;
        } elseif (isset($_POST['products'])) {
            return $this->setProducts(Tools::getValue('products'));
        }

        return true;
    }

    /**
     * Add several tags in database and link it to a product.
     *
     * @param int $idLang Language id
     * @param int $idProduct Product id to link tags with
     * @param string|array $tagList List of tags, as array or as a string with comas
     *
     * @return bool Operation success
     */
    public static function addTags($idLang, $idProduct, $tagList, $separator = ',')
    {
        if (!Validate::isUnsignedId($idLang)) {
            return false;
        }

        if (!is_array($tagList)) {
            $tagList = array_filter(array_unique(array_map('trim', preg_split('#\\' . $separator . '#', $tagList, null, PREG_SPLIT_NO_EMPTY))));
        }

        $list = array();
        if (is_array($tagList)) {
            foreach ($tagList as $tag) {
                if (!Validate::isGenericName($tag)) {
                    return false;
                }
                $tag = trim(Tools::substr($tag, 0, self::$definition['fields']['name']['size']));
                $tagObj = new Tag(null, $tag, (int) $idLang);

                /* Tag does not exist in database */
                if (!Validate::isLoadedObject($tagObj)) {
                    $tagObj->name = $tag;
                    $tagObj->id_lang = (int) $idLang;
                    $tagObj->add();
                }
                if (!in_array($tagObj->id, $list)) {
                    $list[] = $tagObj->id;
                }
            }
        }

        $data = array();
        foreach ($list as $tag) {
            $data[] = array(
                'id_tag' => (int) $tag,
                'id_product' => (int) $idProduct,
                'id_lang' => (int) $idLang,
            );
        }
        $result = Db::getInstance()->insert('product_tag', $data);

        if ($list != array()) {
            self::updateTagCount($list);
        }

        return $result;
    }

    /**
     * Update tag count.
     *
     * @param array|null $tagList
     */
    public static function updateTagCount($tagList = null)
    {
        if (!Module::getBatchMode()) {
            if ($tagList != null) {
                $tagListQuery = ' AND pt.id_tag IN (' . implode(',', array_map('intval', $tagList)) . ')';
                Db::getInstance()->execute('DELETE pt FROM `' . _DB_PREFIX_ . 'tag_count` pt WHERE 1=1 ' . $tagListQuery);
            } else {
                $tagListQuery = '';
            }

            Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'tag_count` (id_group, id_tag, id_lang, id_shop, counter)
            SELECT cg.id_group, pt.id_tag, pt.id_lang, id_shop, COUNT(pt.id_tag) AS times
                FROM `' . _DB_PREFIX_ . 'product_tag` pt
                INNER JOIN `' . _DB_PREFIX_ . 'product_shop` product_shop
                    USING (id_product)
                JOIN (SELECT DISTINCT id_group FROM `' . _DB_PREFIX_ . 'category_group`) cg
                WHERE product_shop.`active` = 1
                AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
                                LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cgo ON (cp.`id_category` = cgo.`id_category`)
                                WHERE cgo.`id_group` = cg.id_group AND product_shop.`id_product` = cp.`id_product`)
                ' . $tagListQuery . '
                GROUP BY pt.id_tag, pt.id_lang, cg.id_group, id_shop ORDER BY NULL');
            Db::getInstance()->execute('REPLACE INTO `' . _DB_PREFIX_ . 'tag_count` (id_group, id_tag, id_lang, id_shop, counter)
            SELECT 0, pt.id_tag, pt.id_lang, id_shop, COUNT(pt.id_tag) AS times
                FROM `' . _DB_PREFIX_ . 'product_tag` pt
                INNER JOIN `' . _DB_PREFIX_ . 'product_shop` product_shop
                    USING (id_product)
                WHERE product_shop.`active` = 1
                ' . $tagListQuery . '
                GROUP BY pt.id_tag, pt.id_lang, id_shop ORDER BY NULL');
        }
    }

    /**
     * Get main tags.
     *
     * @param int $idLang Language ID
     * @param int $nb number
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getMainTags($idLang, $nb = 10)
    {
        $context = Context::getContext();
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();

            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT t.name, counter AS times
            FROM `' . _DB_PREFIX_ . 'tag_count` pt
            LEFT JOIN `' . _DB_PREFIX_ . 'tag` t ON (t.id_tag = pt.id_tag)
            WHERE pt.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= 1') . '
            AND pt.`id_lang` = ' . (int) $idLang . ' AND pt.`id_shop` = ' . (int) $context->shop->id . '
            ORDER BY times DESC
            LIMIT ' . (int) $nb);
        } else {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT t.name, counter AS times
            FROM `' . _DB_PREFIX_ . 'tag_count` pt
            LEFT JOIN `' . _DB_PREFIX_ . 'tag` t ON (t.id_tag = pt.id_tag)
            WHERE pt.id_group = 0 AND pt.`id_lang` = ' . (int) $idLang . ' AND pt.`id_shop` = ' . (int) $context->shop->id . '
            ORDER BY times DESC
            LIMIT ' . (int) $nb);
        }
    }

    /**
     * Get Product Tags.
     *
     * @param int $idProduct Product ID
     *
     * @return array|bool
     */
    public static function getProductTags($idProduct)
    {
        if (!$tmp = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT t.`id_lang`, t.`name`
        FROM ' . _DB_PREFIX_ . 'tag t
        LEFT JOIN ' . _DB_PREFIX_ . 'product_tag pt ON (pt.id_tag = t.id_tag)
        WHERE pt.`id_product`=' . (int) $idProduct)) {
            return false;
        }
        $result = array();
        foreach ($tmp as $tag) {
            $result[$tag['id_lang']][] = $tag['name'];
        }

        return $result;
    }

    /**
     * Get Products.
     *
     * @param bool $associated
     * @param Context|null $context
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getProducts($associated = true, \Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $idLang = $this->id_lang ? $this->id_lang : $context->language->id;

        if (!$this->id && $associated) {
            return array();
        }

        $in = $associated ? 'IN' : 'NOT IN';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT pl.name, pl.id_product
        FROM `' . _DB_PREFIX_ . 'product` p
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON p.id_product = pl.id_product' . Shop::addSqlRestrictionOnLang('pl') . '
        ' . Shop::addSqlAssociation('product', 'p') . '
        WHERE pl.id_lang = ' . (int) $idLang . '
        AND product_shop.active = 1
        ' . ($this->id ? ('AND p.id_product ' . $in . ' (SELECT pt.id_product FROM `' . _DB_PREFIX_ . 'product_tag` pt WHERE pt.id_tag = ' . (int) $this->id . ')') : '') . '
        ORDER BY pl.name');
    }

    /**
     * Set products.
     *
     * @param array $array
     *
     * @return bool
     */
    public function setProducts($array)
    {
        $result = Db::getInstance()->delete('product_tag', 'id_tag = ' . (int) $this->id);
        if (is_array($array)) {
            $array = array_map('intval', $array);
            $result &= ObjectModel::updateMultishopTable('Product', array('indexed' => 0), 'a.id_product IN (' . implode(',', $array) . ')');
            $ids = array();
            foreach ($array as $idProduct) {
                $ids[] = '(' . (int) $idProduct . ',' . (int) $this->id . ',' . (int) $this->id_lang . ')';
            }

            if ($result) {
                $result &= Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'product_tag (id_product, id_tag, id_lang) VALUES ' . implode(',', $ids));
                if (Configuration::get('PS_SEARCH_INDEXATION')) {
                    $result &= Search::indexation(false);
                }
            }
        }
        self::updateTagCount(array((int) $this->id));

        return $result;
    }

    /**
     * Delete tags for product.
     *
     * @param int $idProduct Product ID
     *
     * @return bool
     */
    public static function deleteTagsForProduct($idProduct)
    {
        $tagsRemoved = Db::getInstance()->executeS('SELECT id_tag FROM ' . _DB_PREFIX_ . 'product_tag WHERE id_product=' . (int) $idProduct);
        $result = Db::getInstance()->delete('product_tag', 'id_product = ' . (int) $idProduct);
        Db::getInstance()->delete('tag', 'NOT EXISTS (SELECT 1 FROM ' . _DB_PREFIX_ . 'product_tag
        												WHERE ' . _DB_PREFIX_ . 'product_tag.id_tag = ' . _DB_PREFIX_ . 'tag.id_tag)');
        $tagList = array();
        foreach ($tagsRemoved as $tagRemoved) {
            $tagList[] = $tagRemoved['id_tag'];
        }
        if ($tagList != array()) {
            self::updateTagCount($tagList);
        }

        return $result;
    }
}
