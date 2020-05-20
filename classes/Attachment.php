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
 * Class AttachmentCore.
 */
class AttachmentCore extends ObjectModel
{
    public $file;
    public $file_name;
    public $file_size;
    public $name;
    public $mime;
    public $description;

    /** @var int position Position */
    public $position;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'attachment',
        'primary' => 'id_attachment',
        'multilang' => true,
        'fields' => [
            'file' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 40],
            'mime' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 128],
            'file_name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 128],
            'file_size' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],

            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
            'description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
        'associations' => [
            'products' => ['type' => self::HAS_MANY, 'field' => 'id_product', 'object' => 'Product', 'association' => 'product_attachment'],
        ],
    ];

    protected $webserviceParameters = [
        'objectNodeNames' => 'attachments',
        'hidden_fields' => [],
        'fields' => [
            'file' => [],
            'file_name' => [],
            'file_size' => [],
            'mime' => [],
        ],
        'associations' => [
            'products' => [
                'resource' => 'product',
                'api' => 'products',
                'fields' => [
                    'id' => ['required' => true],
                ],
            ],
        ],
    ];

    /**
     * @see ObjectModel::add()
     */
    public function add($autoDate = true, $nullValues = false)
    {
        $this->file_size = filesize(_PS_DOWNLOAD_DIR_ . $this->file);

        return parent::add($autoDate, $nullValues);
    }

    /**
     * @see ObjectModel::update()
     */
    public function update($nullValues = false)
    {
        $this->file_size = filesize(_PS_DOWNLOAD_DIR_ . $this->file);

        return parent::update($nullValues);
    }

    /**
     * @see ObjectModel::delete()
     */
    public function delete()
    {
        @unlink(_PS_DOWNLOAD_DIR_ . $this->file);

        $sql = new DbQuery();
        $sql->select('pa.`id_product`');
        $sql->from('product_attachment', 'pa');
        $sql->where('pa.`id_attachment` = ' . (int) $this->id);
        $products = Db::getInstance()->executeS($sql);

        Db::getInstance()->delete(
            'product_attachment',
            '`id_attachment` = ' . (int) $this->id
        );

        foreach ($products as $product) {
            Product::updateCacheAttachment((int) $product['id_product']);
        }

        return parent::delete();
    }

    /**
     * Delete selection of attachments.
     *
     * @param array $attachments Attachments
     *
     * @return bool|int Whether the selection has been successfully deleted
     * @todo: Find out if $return can be initialized with true. (breaking change)
     */
    public function deleteSelection($attachments)
    {
        $return = 1;
        foreach ($attachments as $idAttachment) {
            $attachment = new Attachment((int) $idAttachment);
            $return &= $attachment->delete();
        }

        return $return;
    }

    /**
     * Get attachments.
     *
     * @param int $idLang Language ID
     * @param int $idProduct Product ID
     * @param bool $include Whether the attachments are included or excluded from the Product ID
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null Database query result
     */
    public static function getAttachments($idLang, $idProduct, $include = true)
    {
        return Db::getInstance()->executeS(
            '
			SELECT *
			FROM ' . _DB_PREFIX_ . 'attachment a
			LEFT JOIN ' . _DB_PREFIX_ . 'attachment_lang al
				ON (a.id_attachment = al.id_attachment AND al.id_lang = ' . (int) $idLang . ')
			WHERE a.id_attachment ' . ($include ? 'IN' : 'NOT IN') . ' (
				SELECT pa.id_attachment
				FROM ' . _DB_PREFIX_ . 'product_attachment pa
				WHERE id_product = ' . (int) $idProduct . '
			)'
        );
    }

    /**
     * Unassociate all products from the current object
     *
     * @param bool $update_cache If set to true attachment cache will be updated
     *
     * @return bool Deletion result
     */
    public function deleteAttachments($updateAttachmentCache = true)
    {
        $productIds = Db::getInstance()->executeS(
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_attachment`
			WHERE `id_attachment` = ' . (int) $this->id
        );
        $res = Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'product_attachment`
			WHERE `id_attachment` = ' . (int) $this->id
        );

        if ($updateAttachmentCache === true) {
            foreach ($productIds as $productId) {
                Product::updateCacheAttachment((int) $productId);
            }
        }

        return $res;
    }

    /**
     * Delete Product attachments for the given Product ID.
     *
     * @param int $idProduct Product ID
     *
     * @return bool
     */
    public static function deleteProductAttachments($idProduct)
    {
        $res = Db::getInstance()->execute('
		DELETE FROM ' . _DB_PREFIX_ . 'product_attachment
		WHERE id_product = ' . (int) $idProduct);

        Product::updateCacheAttachment((int) $idProduct);

        return $res;
    }

    /**
     * Associate $id_product to the current object.
     *
     * @param int $idProduct id of the product to associate
     *
     * @return bool true if success
     */
    public function attachProduct($idProduct)
    {
        return static::associateProductAttachment((int) $idProduct, (int) $this->id);
    }

    /**
     * @param int $productId
     * @param int $attachmentId
     *
     * @return bool true if success
     */
    public static function associateProductAttachment(int $productId, int $attachmentId): bool
    {
        $res = Db::getInstance()->execute('
			INSERT INTO ' . _DB_PREFIX_ . 'product_attachment
				(id_attachment, id_product) VALUES
				(' . $attachmentId . ', ' . $productId . ')');

        Product::updateCacheAttachment($productId);

        return $res;
    }

    /**
     * Associate an array of id_attachment $array to the product $id_product
     * and remove eventual previous association.
     *
     * @param int $idProduct Product ID
     * @param array $array Attachment IDs
     *
     * @return bool Whether the attachments have been successfully associated with the Product
     */
    public static function attachToProduct($idProduct, $array)
    {
        $result1 = Attachment::deleteProductAttachments($idProduct);

        if (is_array($array)) {
            $ids = [];
            foreach ($array as $idAttachment) {
                if ((int) $idAttachment > 0) {
                    $ids[] = ['id_product' => (int) $idProduct, 'id_attachment' => (int) $idAttachment];
                }
            }

            if (!empty($ids)) {
                $result2 = Db::getInstance()->insert('product_attachment', $ids);
            }
        }

        Product::updateCacheAttachment((int) $idProduct);
        if (is_array($array)) {
            return $result1 && (!isset($result2) || $result2);
        }

        return $result1;
    }

    /**
     * Get Attachment IDs for the given Product within the given range of attachment IDs.
     *
     * @param int $idLang Language ID
     * @param array $list List of attachment IDs in which to search
     *
     * @return array|bool List of attachment IDs found. False if nothing found.
     */
    public static function getProductAttached($idLang, $list)
    {
        if (!is_array($list)) {
            return false;
        }

        $idsAttachments = [];
        foreach ($list as $attachment) {
            $idsAttachments[] = $attachment['id_attachment'];
        }

        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'product_attachment` pa
					LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pa.`id_product` = pl.`id_product`' . Shop::addSqlRestrictionOnLang('pl') . ')
					WHERE `id_attachment` IN (' . implode(',', array_map('intval', $idsAttachments)) . ')
					AND pl.`id_lang` = ' . (int) $idLang;
        $tmp = Db::getInstance()->executeS($sql);
        $productAttachments = [];
        foreach ($tmp as $t) {
            $productAttachments[$t['id_attachment']][] = $t['name'];
        }

        return $productAttachments;
    }

    /**
     * Get attachment products ids of current attachment for association.
     *
     * @return array
     */
    public function getWsProducts()
    {
        $result = Db::getInstance()->executeS('SELECT p.`id_product` AS id
			FROM `' . _DB_PREFIX_ . 'product_attachment` pa
			LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product = pa.id_product)
			' . Shop::addSqlAssociation('product', 'p') . '
			WHERE pa.`id_attachment` = ' . (int) $this->id);

        return $result;
    }

    /**
     * Set products ids of current attachment for association.
     *
     * @param $products ids
     *
     * @return bool
     */
    public function setWsProducts($products)
    {
        try {
            $this->deleteAttachments(true);
            foreach ($products as $product) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'product_attachment` (`id_product`, `id_attachment`) VALUES (' . (int) $product['id'] . ', ' . (int) $this->id . ')');
                Product::updateCacheAttachment((int) $product['id']);
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
