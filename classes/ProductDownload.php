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

use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\VirtualProductFileSettings;

/**
 * Class ProductDownloadCore.
 */
class ProductDownloadCore extends ObjectModel
{
    /** @var int Product id which download belongs */
    public $id_product;

    /** @var string DisplayFilename the name which appear */
    public $display_filename;

    /** @var string PhysicallyFilename the name of the file on hard disk */
    public $filename;

    /** @var string DateDeposit when the file is upload */
    public $date_add;

    /** @var string DateExpiration deadline of the file */
    public $date_expiration;

    /** @var int NbDaysAccessible how many days the customer can access to file */
    public $nb_days_accessible;

    /** @var int NbDownloadable how many time the customer can download the file */
    public $nb_downloadable;

    /** @var bool Active if file is accessible or not */
    public $active = true;

    /** @var bool is_shareable indicates whether the product can be shared */
    public $is_shareable = false;

    protected static $_productIds = [];

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'product_download',
        'primary' => 'id_product_download',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'display_filename' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => VirtualProductFileSettings::MAX_DISPLAY_FILENAME_LENGTH],
            'filename' => ['type' => self::TYPE_STRING, 'validate' => 'isSha1', 'size' => VirtualProductFileSettings::MAX_FILENAME_LENGTH],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_expiration' => ['type' => self::TYPE_DATE, 'validate' => 'isDateOrNull'],
            'nb_days_accessible' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'size' => 10],
            'nb_downloadable' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'size' => 10],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'is_shareable' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    /**
     * Build a virtual product.
     *
     * @param int $idProductDownload Existing productDownload id in order to load object (optional)
     */
    public function __construct($idProductDownload = null)
    {
        parent::__construct($idProductDownload);
        // @TODO check if the file is present on hard drive
    }

    /**
     * @see ObjectModel::getFields()
     *
     * @return array
     */
    public function getFields()
    {
        $fields = parent::getFields();
        if (!$fields['date_expiration']) {
            $fields['date_expiration'] = '0000-00-00 00:00:00';
        }

        return $fields;
    }

    public function add($autoDate = true, $nullValues = false)
    {
        return (bool) parent::add($autoDate, $nullValues);
    }

    public function update($nullValues = false)
    {
        if (parent::update($nullValues)) {
            // Refresh cache of feature detachable because the row can be deactive
            //Configuration::updateGlobalValue('PS_VIRTUAL_PROD_FEATURE_ACTIVE', ProductDownload::isCurrentlyUsed($this->def['table'], true));
            return true;
        }

        return false;
    }

    public function delete($deleteFile = false)
    {
        $result = parent::delete();
        if ($result && $deleteFile) {
            return $this->deleteFile();
        }

        return $result;
    }

    /**
     * Delete the file.
     *
     * @param int $idProductDownload : if we need to delete a specific product attribute file
     *
     * @return bool
     */
    public function deleteFile($idProductDownload = null)
    {
        if (!$this->checkFile()) {
            return false;
        }

        return unlink(_PS_DOWNLOAD_DIR_ . basename($this->filename))
            && Db::getInstance()->delete('product_download', 'id_product_download = ' . (int) $idProductDownload);
    }

    /**
     * Check if file exists.
     *
     * @return bool
     */
    public function checkFile()
    {
        if (!$this->filename) {
            return false;
        }

        return file_exists(_PS_DOWNLOAD_DIR_ . $this->filename);
    }

    /**
     * Check if download repository is writable.
     *
     * @return bool
     */
    public static function checkWritableDir()
    {
        return is_writable(_PS_DOWNLOAD_DIR_);
    }

    /**
     * Return the id_product_download from an id_product.
     *
     * @param int $idProduct Product the id
     * @param bool $active
     *
     * @return bool|int Product the id for this virtual product
     */
    public static function getIdFromIdProduct($idProduct, $active = true)
    {
        if (!ProductDownload::isFeatureActive()) {
            return false;
        }

        self::$_productIds[$idProduct] = (int) Db::getInstance()->getValue('
		SELECT `id_product_download`
		FROM `' . _DB_PREFIX_ . 'product_download`
		WHERE `id_product` = ' . (int) $idProduct . '
		' . ($active ? ' AND `active` = 1' : '') . '
		ORDER BY `id_product_download` DESC');

        return self::$_productIds[$idProduct];
    }

    /**
     * Return the display filename from a physical filename.
     *
     * @param string $filename Filename physically
     *
     * @return int Product the id for this virtual product
     *
     * @since 1.5.0.1
     */
    public static function getIdFromFilename($filename)
    {
        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `id_product_download`
            FROM `' . _DB_PREFIX_ . 'product_download`
            WHERE `filename` = \'' . pSQL($filename) . '\''
        );
    }

    /**
     * Return the filename from a Product ID.
     *
     * @param int $idProduct Product ID
     *
     * @return string Filename the filename for this virtual product
     */
    public static function getFilenameFromIdProduct($idProduct)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `filename`
			FROM `' . _DB_PREFIX_ . 'product_download`
			WHERE `id_product` = ' . (int) $idProduct . '
				AND `active` = 1
		');
    }

    /**
     * Return the display filename from a physical filename.
     *
     * @param string $filename Filename physically
     *
     * @return string Filename the display filename for this virtual product
     */
    public static function getFilenameFromFilename($filename)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `display_filename`
		FROM `' . _DB_PREFIX_ . 'product_download`
		WHERE `filename` = \'' . pSQL($filename) . '\'');
    }

    /**
     * Return text link.
     *
     * @param string|false $hash hash code in table order detail (optional)
     *
     * @return string Html all the code for print a link to the file
     */
    public function getTextLink($hash = false)
    {
        $key = $this->filename . '-' . ($hash ? $hash : 'orderdetail');

        return Context::getContext()->link->getPageLink('get-file&key=' . $key);
    }

    /**
     * Return html link.
     *
     * @param string|bool $class CSS selector
     * @param string|bool $hash hash code in table order detail
     *
     * @return string Html all the code for print a link to the file
     */
    public function getHtmlLink($class = false, $hash = false)
    {
        $link = $this->getTextLink($hash);
        $html = '<a href="' . $link . '" title=""';
        if ($class) {
            $html .= ' class="' . $class . '"';
        }
        $html .= '>' . $this->display_filename . '</a>';

        return $html;
    }

    /**
     * Return a deadline.
     *
     * @return string Datetime in SQL format
     */
    public function getDeadline()
    {
        if (!(int) $this->nb_days_accessible) {
            return '0000-00-00 00:00:00';
        }
        $timestamp = strtotime('+' . (int) $this->nb_days_accessible . ' day');

        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * Return a hash for control download access.
     *
     * @return string Hash ready to insert in database
     */
    public function getHash()
    {
        // TODO check if this hash not already in database
        return sha1(microtime() . $this->id);
    }

    /**
     * Return a sha1 filename.
     *
     * @return string Sha1 unique filename
     */
    public static function getNewFilename()
    {
        do {
            $filename = sha1(microtime());
        } while (file_exists(_PS_DOWNLOAD_DIR_ . $filename));

        return $filename;
    }

    /**
     * This method is allow to know if a feature is used or active.
     *
     * @return bool
     *
     * @since 1.5.0.1
     */
    public static function isFeatureActive()
    {
        return Configuration::get('PS_VIRTUAL_PROD_FEATURE_ACTIVE');
    }
}
