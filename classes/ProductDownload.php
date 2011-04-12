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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ProductDownloadCore extends ObjectModel
{
	/** @var integer Product id which download belongs */
	public $id_product;
	
	/** @var string DisplayFilename the name which appear */
	public $display_filename;

	/** @var string PhysicallyFilename the name of the file on hard disk */
	public $physically_filename;
	
	/** @var string DateDeposit when the file is upload */
	public $date_deposit;

	/** @var string DateExpiration deadline of the file */
	public $date_expiration;
	
	/** @var string NbDaysAccessible how many days the customer can access to file */
	public $nb_days_accessible;

	/** @var string NbDownloadable how many time the customer can download the file */
	public $nb_downloadable;

	/** @var boolean Active if file is accessible or not */
	public $active = 1;

	protected static $_productIds = array();

	protected	$fieldsRequired = array(
		'id_product',
		'display_filename'
	);
	protected	$fieldsSize = array(
		'display_filename' => 255,
		'physically_filename' => 255,
		'date_deposit' => 20,
		'date_expiration' => 20,
		'nb_days_accessible' => 10,
		'nb_downloadable' => 10,
		'active' => 1
	);
	protected	$fieldsValidate = array(
		'id_product' => 'isUnsignedId',
		'display_filename' => 'isGenericName',
		'physically_filename' => 'isSha1',
		'date_deposit' => 'isDate',
		'date_expiration' => 'isDate',
		'nb_days_accessible' => 'isUnsignedInt',
		'nb_downloadable' => 'isUnsignedInt',
		'active' => 'isUnsignedInt'
	);

	protected $table = 'product_download';
	protected $identifier = 'id_product_download';
	
	/**
	 * Build a virtual product
	 *
	 * @param integer $id_product_download Existing productDownload id in order to load object (optional)
	 */
	public function __construct($id_product_download = NULL)
	{
		parent::__construct($id_product_download);
		// TODO check if the file is present on hard drive
	}
	
	public function delete($deleteFile=false)
	{
		if ($deleteFile)
			$this->deleteFile();
	}

	public function getFields()
	{
		parent::validateFields();

		$fields['id_product'] = (int)($this->id_product);
		$fields['display_filename'] = pSQL($this->display_filename);
		$fields['physically_filename'] = pSQL($this->physically_filename);
		$fields['date_deposit'] = pSQL($this->date_deposit);
		$fields['date_expiration'] = pSQL($this->date_expiration);
		$fields['nb_days_accessible'] = (int)($this->nb_days_accessible);
		$fields['nb_downloadable'] = (int)($this->nb_downloadable);
		$fields['active'] = (int)($this->active);
		return $fields;
	}


	/**
	 * Delete the file
	 *
	 * @return boolean
	 */
	public function deleteFile()
	{
		if (!$this->checkFile())
			return false;
		return unlink(_PS_DOWNLOAD_DIR_.$this->physically_filename);
	}

	/**
	 * Check if file exists
	 *
	 * @return boolean
	 */
	public function checkFile()
	{
		if (!$this->physically_filename) return false;
		return file_exists(_PS_DOWNLOAD_DIR_.$this->physically_filename);
	}

	/**
	 * Check if download repository is writable
	 *
	 * @return boolean
	 */
	static public function checkWritableDir()
	{
		return is_writable(_PS_DOWNLOAD_DIR_);
	}

	/**
	 * Return the id_product_download from an id_product
	 *
	 * @param int $id_product Product the id
	 * @return integer Product the id for this virtual product
	 */
	public static function getIdFromIdProduct($id_product)
	{
		if (array_key_exists($id_product, self::$_productIds))
			return self::$_productIds[$id_product];
			
		$data = Db::getInstance()->getRow('
		SELECT `id_product_download`
		FROM `'._DB_PREFIX_.'product_download`
		WHERE `id_product` = '.(int)($id_product).' AND `active` = 1');
		
		self::$_productIds[$id_product] = isset($data['id_product_download']) ? (int)($data['id_product_download']) : false;
		
		return self::$_productIds[$id_product];
	}

	/**
	 * Return the filename from an id_product
	 *
	 * @param int $id_product Product the id
	 * @return string Filename the filename for this virtual product
	 */
	public static function getFilenameFromIdProduct($id_product)
	{
		$data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `physically_filename`
		FROM `'._DB_PREFIX_.'product_download`
		WHERE `id_product` = '.(int)($id_product).'
		AND `active` = 1');
		return $data['physically_filename'];
	}

	/**
	 * Return the display filename from a physical filename
	 *
	 * @param string $physically_filename Filename physically
	 * @return string Filename the display filename for this virtual product
	 */
	public static function getFilenameFromFilename($physically_filename)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `display_filename`
		FROM `'._DB_PREFIX_.'product_download`
		WHERE `physically_filename` = \''.pSQL($physically_filename).'\'');
	}

	/**
	 * Return html link
	 *
	 * @param string $class CSS selector (optionnal)
	 * @param bool $admin specific to backend (optionnal)
	 * @param string $hash hash code in table order detail (optionnal)
	 * @return string Html all the code for print a link to the file
	 */
	public function getTextLink($admin=true, $hash=false)
	{
		$key = $this->physically_filename . '-' . ($hash ? $hash : 'orderdetail');
		$link = ($admin) ? './get-file-admin.php?' : Tools::getHttpHost(true, true).__PS_BASE_URI__.'get-file.php?';
		$link .= ($admin) ? 'file='.$this->physically_filename : 'key='.$key;
		return $link;
	}

	/**
	 * Return html link
	 *
	 * @param string $class CSS selector (optionnal)
	 * @param bool $admin specific to backend (optionnal)
	 * @param string $hash hash code in table order detail (optionnal)
	 * @return string Html all the code for print a link to the file
	 */
	public function getHtmlLink($class=false, $admin=true, $hash=false)
	{
		$link = $this->getTextLink($admin, $hash);
		$html = '<a href="'.$link.'" title=""';
		if ($class) $html.= ' class="'.$class.'"';
		$html.= '>'.$this->display_filename.'</a>';
		return $html;
	}

	/**
	 * Return a deadline
	 *
	 * @return string Datetime in SQL format
	 */
	public function getDeadline()
	{
		if (!(int)($this->nb_days_accessible))
			return '0000-00-00 00:00:00';
		$timestamp = strtotime('+'.(int)($this->nb_days_accessible).' day');
		return date('Y-m-d H:i:s', $timestamp);
	}

	/**
	 * Return a hash for control download access
	 *
	 * @return string Hash ready to insert in database
	 */
	public function getHash()
	{
		// TODO check if this hash not already in database
		return sha1(microtime().$this->id);
	}

	/**
	 * Return a sha1 filename
	 *
	 * @return string Sha1 unique filename
	 */
	static public function getNewFilename()
	{
		$ret = sha1(microtime());
		if (file_exists(_PS_DOWNLOAD_DIR_.$ret))
			$ret = ProductDownload::getNewFilename();
		return $ret;
	}

}


