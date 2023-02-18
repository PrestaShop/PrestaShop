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

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;

/**
 * Class ImageCore.
 */
class ImageCore extends ObjectModel
{
    public $id;

    /** @var int Image ID */
    public $id_image;

    /** @var int Product ID */
    public $id_product;

    /** @var int Position used to order images of the same product */
    public $position;

    /** @var bool|null Image is cover */
    public $cover;

    /** @var array<int,string> Legend */
    public $legend;

    /** @var string image extension */
    public $image_format = 'jpg';

    /** @var string path to index.php file to be copied to new image folders */
    public $source_index;

    /** @var string image folder */
    protected $folder;

    /** @var string image path without extension */
    protected $existing_path;

    /** @var int access rights of created folders (octal) */
    protected static $access_rights = 0775;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'image',
        'primary' => 'id_image',
        'multilang' => true,
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'shop' => 'both', 'validate' => 'isUnsignedId', 'required' => true],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'cover' => ['type' => self::TYPE_BOOL, 'allow_null' => true, 'validate' => 'isBool', 'shop' => true],
            'legend' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128],
        ],
    ];

    /**
     * @var array
     */
    protected static $_cacheGetSize = [];

    /**
     * ImageCore constructor.
     *
     * @param int|null $id
     * @param int|null $idLang
     * @param null $id_shop
     * @param null $translator
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function __construct($id = null, $idLang = null, $id_shop = null, $translator = null)
    {
        parent::__construct($id, $idLang, $id_shop, $translator);
        $this->image_dir = _PS_PRODUCT_IMG_DIR_;
        $this->source_index = _PS_PRODUCT_IMG_DIR_ . 'index.php';
    }

    /**
     * Adds current Image as a new Object to the database.
     *
     * @param bool $autoDate Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Image has been successfully added
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        if ($this->position <= 0) {
            $this->position = Image::getHighestPosition($this->id_product) + 1;
        }

        if ($this->cover) {
            $this->cover = true;
        } else {
            $this->cover = null;
        }

        return parent::add($autoDate, $nullValues);
    }

    /**
     * This override is needed because we need to set 'id_product' => (int) $this->id_product, in $data array which is
     * a specific case for association between shop and image
     *
     * {@inheritDoc}
     */
    public function associateTo($id_shops, int $productId = null)
    {
        if (!$this->id) {
            return;
        }

        $productId = $productId ?? $this->id_product;
        if (empty($productId)) {
            throw new InvalidArgumentException('You cannot associate an image to shop without specifying product ID');
        }

        if (!is_array($id_shops)) {
            $id_shops = [$id_shops];
        }

        $data = [];
        foreach ($id_shops as $id_shop) {
            if (!$this->isAssociatedToShop($id_shop)) {
                $data[] = [
                    $this->def['primary'] => (int) $this->id,
                    'id_shop' => (int) $id_shop,
                    'id_product' => $productId,
                ];
            }
        }

        if ($data) {
            return Db::getInstance()->insert($this->def['table'] . '_shop', $data);
        }

        return true;
    }

    /**
     * Updates the current Image in the database.
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Image has been successfully updated
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        if ($this->cover) {
            $this->cover = true;
        } else {
            $this->cover = null;
        }

        return parent::update($nullValues);
    }

    /**
     * Deletes current Image from the database.
     *
     * @return bool `true` if delete was successful
     *
     * @throws PrestaShopException
     */
    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }

        if ($this->hasMultishopEntries()) {
            return true;
        }

        if (!$this->deleteProductAttributeImage() || !$this->deleteImage()) {
            return false;
        }

        // update positions
        Db::getInstance()->execute('SET @position:=0', false);
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'image` SET position=(@position:=@position+1)
									WHERE `id_product` = ' . (int) $this->id_product . ' ORDER BY position ASC');

        return true;
    }

    /**
     * Return first image (by position) associated with a product attribute.
     *
     * @param int $idShop Shop ID
     * @param int $idLang Language ID
     * @param int $idProduct Product ID
     * @param int $idProductAttribute Product Attribute ID
     *
     * @return array
     */
    public static function getBestImageAttribute($idShop, $idLang, $idProduct, $idProductAttribute)
    {
        $cacheId = 'Image::getBestImageAttribute' . '-' . (int) $idProduct . '-' . (int) $idProductAttribute . '-' . (int) $idLang . '-' . (int) $idShop;

        if (!Cache::isStored($cacheId)) {
            $row = Db::getInstance()->getRow('
					SELECT image_shop.`id_image` id_image, il.`legend`
					FROM `' . _DB_PREFIX_ . 'image` i
					INNER JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
						ON (i.id_image = image_shop.id_image AND image_shop.id_shop = ' . (int) $idShop . ')
						INNER JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai
						ON (pai.`id_image` = i.`id_image` AND pai.`id_product_attribute` = ' . (int) $idProductAttribute . ')
					LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
						ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $idLang . ')
					WHERE i.`id_product` = ' . (int) $idProduct . ' ORDER BY i.`position` ASC');

            Cache::store($cacheId, $row);
        } else {
            $row = Cache::retrieve($cacheId);
        }

        return $row;
    }

    /**
     * Return available images for a product.
     *
     * @param int $idLang Language ID
     * @param int $idProduct Product ID
     * @param int $idProductAttribute Product Attribute ID
     * @param int $idShop Shop ID
     *
     * @return array Images
     */
    public static function getImages($idLang, $idProduct, $idProductAttribute = null, $idShop = null)
    {
        $attributeFilter = ($idProductAttribute ? ' AND ai.`id_product_attribute` = ' . (int) $idProductAttribute : '');
        $shopFilter = ($idShop ? ' AND ims.`id_shop` = ' . (int) $idShop : '');
        $sql = 'SELECT *
			FROM `' . _DB_PREFIX_ . 'image` i
			LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image`)';

        if ($idProductAttribute) {
            $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` ai ON (i.`id_image` = ai.`id_image`)';
        }

        if ($idShop) {
            $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` ims ON (i.`id_image` = ims.`id_image`)';
        }

        $sql .= ' WHERE i.`id_product` = ' . (int) $idProduct . ' AND il.`id_lang` = ' . (int) $idLang . $attributeFilter . $shopFilter . '
			ORDER BY i.`position` ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Check if a product has an image available.
     *
     * @param int $idLang Language ID
     * @param int $idProduct Product ID
     * @param int $idProductAttribute Product Attribute ID
     *
     * @return bool
     */
    public static function hasImages($idLang, $idProduct, $idProductAttribute = null)
    {
        $attribute_filter = ($idProductAttribute ? ' AND ai.`id_product_attribute` = ' . (int) $idProductAttribute : '');
        $sql = 'SELECT 1
			FROM `' . _DB_PREFIX_ . 'image` i
			LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image`)';

        if ($idProductAttribute) {
            $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` ai ON (i.`id_image` = ai.`id_image`)';
        }

        $sql .= ' WHERE i.`id_product` = ' . (int) $idProduct . ' AND il.`id_lang` = ' . (int) $idLang . $attribute_filter;

        return (bool) Db::getInstance()->getValue($sql);
    }

    /**
     * Return Images.
     *
     * @return array Images
     */
    public static function getAllImages()
    {
        return Db::getInstance()->executeS('
		SELECT `id_image`, `id_product`
		FROM `' . _DB_PREFIX_ . 'image`
		ORDER BY `id_image` ASC');
    }

    /**
     * Return number of images for a product.
     *
     * @param int $idProduct Product ID
     *
     * @return int number of images
     */
    public static function getImagesTotal($idProduct)
    {
        $result = Db::getInstance()->getRow('
		SELECT COUNT(`id_image`) AS total
		FROM `' . _DB_PREFIX_ . 'image`
		WHERE `id_product` = ' . (int) $idProduct);

        return $result['total'];
    }

    /**
     * Return highest position of images for a product.
     *
     * @param int $idProduct Product ID
     *
     * @return int highest position of images
     */
    public static function getHighestPosition($idProduct)
    {
        $result = Db::getInstance()->getRow('
		SELECT MAX(`position`) AS max
		FROM `' . _DB_PREFIX_ . 'image`
		WHERE `id_product` = ' . (int) $idProduct);

        return $result['max'];
    }

    /**
     * Delete product cover.
     *
     * @param int $idProduct Product ID
     *
     * @return bool result
     */
    public static function deleteCover($idProduct)
    {
        if (!Validate::isUnsignedId($idProduct)) {
            die(Tools::displayError());
        }

        if (file_exists(_PS_TMP_IMG_DIR_ . 'product_' . $idProduct . '.jpg')) {
            unlink(_PS_TMP_IMG_DIR_ . 'product_' . $idProduct . '.jpg');
        }

        return Db::getInstance()->execute(
            '
			UPDATE `' . _DB_PREFIX_ . 'image`
			SET `cover` = NULL
			WHERE `id_product` = ' . (int) $idProduct
        ) &&
        Db::getInstance()->execute(
            '
			UPDATE `' . _DB_PREFIX_ . 'image_shop` image_shop
			SET image_shop.`cover` = NULL
			WHERE image_shop.id_shop IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ') AND image_shop.`id_product` = ' . (int) $idProduct
        );
    }

    /**
     *Get product cover.
     *
     * @param int $idProduct Product ID
     *
     * @return bool result
     */
    public static function getCover($idProduct)
    {
        return Db::getInstance()->getRow('
			SELECT * FROM `' . _DB_PREFIX_ . 'image_shop` image_shop
			WHERE image_shop.`id_product` = ' . (int) $idProduct . '
			AND image_shop.`cover`= 1');
    }

    /**
     *Get global product cover.
     *
     * @param int $idProduct Product ID
     *
     * @return bool result
     */
    public static function getGlobalCover($idProduct)
    {
        return Db::getInstance()->getRow('
			SELECT * FROM `' . _DB_PREFIX_ . 'image` i
			WHERE i.`id_product` = ' . (int) $idProduct . '
			AND i.`cover`= 1');
    }

    /**
     * Copy images from a product to another.
     *
     * @param int $idProductOld Source product ID
     * @param int $idProductNew Destination product ID
     */
    public static function duplicateProductImages($idProductOld, $idProductNew, $combinationImages)
    {
        $imagesTypes = ImageType::getImagesTypes('products');
        $result = Db::getInstance()->executeS('
		SELECT `id_image`
		FROM `' . _DB_PREFIX_ . 'image`
		WHERE `id_product` = ' . (int) $idProductOld);
        foreach ($result as $row) {
            $imageOld = new Image($row['id_image']);
            $imageNew = clone $imageOld;
            unset($imageNew->id);
            $imageNew->id_product = (int) $idProductNew;

            // A new id is generated for the cloned image when calling add()
            if ($imageNew->add()) {
                $newPath = $imageNew->getPathForCreation();
                foreach ($imagesTypes as $imageType) {
                    if (file_exists(_PS_PRODUCT_IMG_DIR_ . $imageOld->getExistingImgPath() . '-' . $imageType['name'] . '.jpg')) {
                        if (!Configuration::get('PS_LEGACY_IMAGES')) {
                            $imageNew->createImgFolder();
                        }
                        copy(
                            _PS_PRODUCT_IMG_DIR_ . $imageOld->getExistingImgPath() . '-' . $imageType['name'] . '.jpg',
                        $newPath . '-' . $imageType['name'] . '.jpg'
                        );
                        if (Configuration::get('WATERMARK_HASH')) {
                            $oldImagePath = _PS_PRODUCT_IMG_DIR_ . $imageOld->getExistingImgPath() . '-' . $imageType['name'] . '-' . Configuration::get('WATERMARK_HASH') . '.jpg';
                            if (file_exists($oldImagePath)) {
                                copy($oldImagePath, $newPath . '-' . $imageType['name'] . '-' . Configuration::get('WATERMARK_HASH') . '.jpg');
                            }
                        }
                    }
                }

                if (file_exists(_PS_PRODUCT_IMG_DIR_ . $imageOld->getExistingImgPath() . '.jpg')) {
                    copy(_PS_PRODUCT_IMG_DIR_ . $imageOld->getExistingImgPath() . '.jpg', $newPath . '.jpg');
                }

                Image::replaceAttributeImageAssociationId($combinationImages, (int) $imageOld->id, (int) $imageNew->id);

                // Duplicate shop associations for images
                $imageNew->duplicateShops($idProductOld);
            } else {
                return false;
            }
        }

        return Image::duplicateAttributeImageAssociations($combinationImages);
    }

    /**
     * @param array $combinationImages
     * @param int $savedId
     * @param int $idImage
     */
    protected static function replaceAttributeImageAssociationId(&$combinationImages, $savedId, $idImage)
    {
        if (!isset($combinationImages['new']) || !is_array($combinationImages['new'])) {
            return;
        }
        foreach ($combinationImages['new'] as $id_product_attribute => $image_ids) {
            foreach ($image_ids as $key => $imageId) {
                if ((int) $imageId == (int) $savedId) {
                    $combinationImages['new'][$id_product_attribute][$key] = (int) $idImage;
                }
            }
        }
    }

    /**
     * Duplicate product attribute image associations.
     *
     * @param array $combinationImages
     *
     * @return bool
     */
    public static function duplicateAttributeImageAssociations($combinationImages)
    {
        if (!isset($combinationImages['new']) || !is_array($combinationImages['new'])) {
            return true;
        }
        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'product_attribute_image` (`id_product_attribute`, `id_image`) VALUES ';
        foreach ($combinationImages['new'] as $idProductAttribute => $imageIds) {
            foreach ($imageIds as $imageId) {
                $query .= '(' . (int) $idProductAttribute . ', ' . (int) $imageId . '), ';
            }
        }
        $query = rtrim($query, ', ');

        return Db::getInstance()->execute($query);
    }

    /**
     * Change an image position and update relative positions.
     *
     * @param int $way position is moved up if 0, moved down if 1
     * @param int $position new position of the moved image
     *
     * @return bool success
     */
    public function updatePosition($way, $position)
    {
        if (!isset($this->id) || !$position) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return
            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'image`
                SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
                WHERE `position`
                ' . ($way
                    ? '> ' . (int) $this->position . ' AND `position` <= ' . (int) $position
                    : '< ' . (int) $this->position . ' AND `position` >= ' . (int) $position) . '
                AND `id_product`=' . (int) $this->id_product
            )
            && Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'image`
                SET `position` = ' . (int) $position . '
                WHERE `id_image` = ' . (int) $this->id_image
            )
        ;
    }

    /**
     * @param string $type
     *
     * @return mixed
     */
    public static function getSize($type)
    {
        if (!isset(self::$_cacheGetSize[$type])) {
            self::$_cacheGetSize[$type] = Db::getInstance()->getRow('
				SELECT `width`, `height`
				FROM ' . _DB_PREFIX_ . 'image_type
				WHERE `name` = \'' . pSQL($type) . '\'
			');
        }

        return self::$_cacheGetSize[$type];
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public static function getWidth($params)
    {
        $result = self::getSize($params['type']);

        return $result['width'];
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public static function getHeight($params)
    {
        $result = self::getSize($params['type']);

        return $result['height'];
    }

    /**
     * Clear all images in tmp dir.
     */
    public static function clearTmpDir()
    {
        foreach (scandir(_PS_TMP_IMG_DIR_, SCANDIR_SORT_NONE) as $d) {
            if (preg_match('/(.*)\.jpg$/', $d)) {
                unlink(_PS_TMP_IMG_DIR_ . $d);
            }
        }
    }

    /**
     * Delete Image - Product attribute associations for this image.
     */
    public function deleteProductAttributeImage()
    {
        return Db::getInstance()->execute(
            '
			DELETE
			FROM `' . _DB_PREFIX_ . 'product_attribute_image`
			WHERE `id_image` = ' . (int) $this->id
        );
    }

    /**
     * Delete the product image from disk and remove the containing folder if empty
     * Handles both legacy and new image filesystems.
     */
    public function deleteImage($forceDelete = false)
    {
        if (!$this->id) {
            return false;
        }

        // Delete base image
        if (file_exists($this->image_dir . $this->getExistingImgPath() . '.' . $this->image_format)) {
            unlink($this->image_dir . $this->getExistingImgPath() . '.' . $this->image_format);
        } else {
            return false;
        }

        $filesToDelete = [];

        // Delete auto-generated images
        $image_types = ImageType::getImagesTypes();
        $sfContainer = SymfonyContainer::getInstance();
        $isMultipleImageFormatFeatureEnabled = $sfContainer->get('prestashop.core.admin.feature_flag.repository')->isEnabled(FeatureFlagSettings::FEATURE_FLAG_MULTIPLE_IMAGE_FORMAT);

        foreach ($image_types as $imageType) {
            $filesToDelete = $this->deleteAutoGeneratedImage($imageType, $this->image_format, $filesToDelete);

            if ($isMultipleImageFormatFeatureEnabled) {
                foreach (ImageFormatConfiguration::SUPPORTED_FORMATS as $imageFormat) {
                    $filesToDelete = $this->deleteAutoGeneratedImage($imageType, $imageFormat, $filesToDelete);
                }
            }
        }

        // Delete watermark image
        $filesToDelete[] = $this->image_dir . $this->getExistingImgPath() . '-watermark.' . $this->image_format;
        $filesToDelete[] = $this->image_dir . $this->getExistingImgPath() . '-watermark2x.' . $this->image_format;
        // delete index.php
        $filesToDelete[] = $this->image_dir . $this->getImgFolder() . 'index.php';
        // delete fileType
        $filesToDelete[] = $this->image_dir . $this->getImgFolder() . 'fileType';
        // Delete tmp images
        $filesToDelete[] = _PS_TMP_IMG_DIR_ . 'product_' . $this->id_product . '.' . $this->image_format;
        $filesToDelete[] = _PS_TMP_IMG_DIR_ . 'product_mini_' . $this->id_product . '.' . $this->image_format;

        foreach ($filesToDelete as $file) {
            if (file_exists($file) && !@unlink($file)) {
                return false;
            }
        }

        // Can we delete the image folder?
        if (is_dir($this->image_dir . $this->getImgFolder())) {
            $deleteFolder = true;
            foreach (scandir($this->image_dir . $this->getImgFolder(), SCANDIR_SORT_NONE) as $file) {
                if (($file != '.' && $file != '..')) {
                    $deleteFolder = false;

                    break;
                }
            }
        }
        if (isset($deleteFolder) && $deleteFolder) {
            @rmdir($this->image_dir . $this->getImgFolder());
        }

        return true;
    }

    /**
     * Recursively deletes all product images in the given folder tree and removes empty folders.
     *
     * @param string $path folder containing the product images to delete
     * @param string $format image format
     *
     * @return bool success
     */
    public static function deleteAllImages($path, $format = 'jpg')
    {
        if (!$path || !$format || !is_dir($path)) {
            return false;
        }
        foreach (scandir($path, SCANDIR_SORT_NONE) as $file) {
            if (preg_match('/^[0-9]+(\-(.*))?\.' . $format . '$/', $file)) {
                unlink($path . $file);
            } elseif (is_dir($path . $file) && (preg_match('/^[0-9]$/', $file))) {
                Image::deleteAllImages($path . $file . '/', $format);
            }
        }

        // Can we remove the image folder?
        if (is_numeric(basename($path))) {
            $removeFolder = true;
            foreach (scandir($path, SCANDIR_SORT_NONE) as $file) {
                if (($file != '.' && $file != '..' && $file != 'index.php')) {
                    $removeFolder = false;

                    break;
                }
            }

            if ($removeFolder) {
                // we're only removing index.php if it's a folder we want to delete
                if (file_exists($path . 'index.php')) {
                    @unlink($path . 'index.php');
                }
                @rmdir($path);
            }
        }

        return true;
    }

    /**
     * Returns image path in the old or in the new filesystem.
     *
     * @ returns string image path
     */
    public function getExistingImgPath()
    {
        if (!$this->id) {
            return false;
        }

        if (!$this->existing_path) {
            if (Configuration::get('PS_LEGACY_IMAGES') && file_exists(_PS_PRODUCT_IMG_DIR_ . $this->id_product . '-' . $this->id . '.' . $this->image_format)) {
                $this->existing_path = $this->id_product . '-' . $this->id;
            } else {
                $this->existing_path = $this->getImgPath();
            }
        }

        return $this->existing_path;
    }

    /**
     * Returns the path to the folder containing the image in the new filesystem.
     *
     * @return string|bool path to folder
     */
    public function getImgFolder()
    {
        if (!$this->id) {
            return false;
        }

        if (!$this->folder) {
            $this->folder = Image::getImgFolderStatic($this->id);
        }

        return $this->folder;
    }

    /**
     * Create parent folders for the image in the new filesystem.
     *
     * @return bool success
     */
    public function createImgFolder()
    {
        if (!$this->id) {
            return false;
        }

        if (!file_exists(_PS_PRODUCT_IMG_DIR_ . $this->getImgFolder())) {
            // Apparently sometimes mkdir cannot set the rights, and sometimes chmod can't. Trying both.
            $success = @mkdir(_PS_PRODUCT_IMG_DIR_ . $this->getImgFolder(), self::$access_rights, true);
            $chmod = @chmod(_PS_PRODUCT_IMG_DIR_ . $this->getImgFolder(), self::$access_rights);

            // Create an index.php file in the new folder
            if (($success || $chmod)
                && !file_exists(_PS_PRODUCT_IMG_DIR_ . $this->getImgFolder() . 'index.php')
                && file_exists($this->source_index)) {
                return @copy($this->source_index, _PS_PRODUCT_IMG_DIR_ . $this->getImgFolder() . 'index.php');
            }
        }

        return true;
    }

    /**
     * Returns the path to the image without file extension.
     *
     * @return string|bool path
     */
    public function getImgPath()
    {
        if (!$this->id) {
            return false;
        }

        return $this->getImgFolder() . $this->id;
    }

    /**
     * Returns the path to the folder containing the image in the new filesystem.
     *
     * @param mixed $idImage
     *
     * @return string|bool path to folder
     */
    public static function getImgFolderStatic($idImage)
    {
        if (!is_numeric($idImage)) {
            return false;
        }
        $folders = str_split((string) $idImage);

        return implode('/', $folders) . '/';
    }

    /**
     * Move all legacy product image files from the image folder root to their subfolder in the new filesystem.
     * If max_execution_time is provided, stops before timeout and returns string "timeout".
     * If any image cannot be moved, stops and returns "false".
     *
     * @param int $maxExecutionTime
     *
     * @return mixed success or timeout
     */
    public static function moveToNewFileSystem($maxExecutionTime = 0)
    {
        $startTime = time();
        $image = null;
        $tmpFolder = 'duplicates/';
        foreach (scandir(_PS_PRODUCT_IMG_DIR_, SCANDIR_SORT_NONE) as $file) {
            // matches the base product image or the thumbnails
            if (preg_match('/^([0-9]+\-)([0-9]+)(\-(.*))?\.jpg$/', $file, $matches)) {
                // don't recreate an image object for each image type
                if (!$image || $image->id !== (int) $matches[2]) {
                    $image = new Image((int) $matches[2]);
                }
                // image exists in DB and with the correct product?
                if (Validate::isLoadedObject($image) && $image->id_product == (int) rtrim($matches[1], '-')) {
                    // create the new folder if it does not exist
                    if (!$image->createImgFolder()) {
                        return false;
                    }

                    // if there's already a file at the new image path, move it to a dump folder
                    // most likely the preexisting image is a demo image not linked to a product and it's ok to replace it
                    $newPath = _PS_PRODUCT_IMG_DIR_ . $image->getImgPath() . (isset($matches[3]) ? $matches[3] : '') . '.jpg';
                    if (file_exists($newPath)) {
                        if (!file_exists(_PS_PRODUCT_IMG_DIR_ . $tmpFolder)) {
                            @mkdir(_PS_PRODUCT_IMG_DIR_ . $tmpFolder, self::$access_rights);
                            @chmod(_PS_PRODUCT_IMG_DIR_ . $tmpFolder, self::$access_rights);
                        }
                        $tmpPath = _PS_PRODUCT_IMG_DIR_ . $tmpFolder . basename($file);
                        if (!@rename($newPath, $tmpPath) || !file_exists($tmpPath)) {
                            return false;
                        }
                    }
                    // move the image
                    if (!@rename(_PS_PRODUCT_IMG_DIR_ . $file, $newPath) || !file_exists($newPath)) {
                        return false;
                    }
                }
            }
            if ((int) $maxExecutionTime != 0 && (time() - $startTime > (int) $maxExecutionTime - 4)) {
                return 'timeout';
            }
        }

        return true;
    }

    /**
     * Try to create and delete some folders to check if moving images to new file system will be possible.
     *
     * @return bool success
     */
    public static function testFileSystem()
    {
        $folder1 = _PS_PRODUCT_IMG_DIR_ . 'testfilesystem/';
        $testFolder = $folder1 . 'testsubfolder/';
        // check if folders are already existing from previous failed test
        if (file_exists($testFolder)) {
            @rmdir($testFolder);
            @rmdir($folder1);
        }
        if (file_exists($testFolder)) {
            return false;
        }

        @mkdir($testFolder, self::$access_rights, true);
        @chmod($testFolder, self::$access_rights);
        if (!is_writable($testFolder)) {
            return false;
        }
        @rmdir($testFolder);
        @rmdir($folder1);

        if (file_exists($folder1)) {
            return false;
        }

        return true;
    }

    /**
     * Returns the path where a product image should be created (without file format).
     *
     * @return string|bool path
     */
    public function getPathForCreation()
    {
        if (!$this->id) {
            return false;
        }
        if (Configuration::get('PS_LEGACY_IMAGES')) {
            if (!$this->id_product) {
                return false;
            }
            $path = $this->id_product . '-' . $this->id;
        } else {
            $path = $this->getImgPath();
            $this->createImgFolder();
        }

        return _PS_PRODUCT_IMG_DIR_ . $path;
    }

    /**
     * @param array $imageType
     * @param string $imageFormat
     * @param array $filesToDelete
     *
     * @return array
     */
    private function deleteAutoGeneratedImage(array $imageType, string $imageFormat, array $filesToDelete): array
    {
        $configuration = SymfonyContainer::getInstance()->get('prestashop.adapter.legacy.configuration');
        $filesToDelete[] = $this->image_dir . $this->getExistingImgPath() . '-' . $imageType['name'] . '.' . $imageFormat;
        $filesToDelete[] = $this->image_dir . $this->getExistingImgPath() . '-' . $imageType['name'] . '2x.' . $imageFormat;
        if ($configuration->get('WATERMARK_HASH')) {
            $filesToDelete[] = $this->image_dir . $this->getExistingImgPath() . '-' . $imageType['name'] . '-' . $configuration->get('WATERMARK_HASH') . '.' . $imageFormat;
        }

        return $filesToDelete;
    }
}
