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

class AttachmentCore extends ObjectModel
{
	public		$file;
	public		$file_name;
	public		$name;
	public		$mime;
	public		$description;

	/** @var integer position */
	public		$position;

	protected	$fieldsRequired = array('file', 'mime');
	protected	$fieldsSize = array('file' => 40, 'mime' => 64, 'file_name' => 128);
	protected	$fieldsValidate = array('file' => 'isGenericName', 'mime' => 'isCleanHtml', 'file_name' => 'isGenericName');

	protected	$fieldsRequiredLang = array('name');
	protected	$fieldsSizeLang = array('name' => 32);
	protected	$fieldsValidateLang = array('name' => 'isGenericName', 'description' => 'isCleanHtml');

	protected 	$table = 'attachment';
	protected 	$identifier = 'id_attachment';

	public function getFields()
	{
		parent::validateFields();
		$fields['file_name'] = pSQL($this->file_name);
		$fields['file'] = pSQL($this->file);
		$fields['mime'] = pSQL($this->mime);
		return $fields;
	}

	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('name', 'description'));
	}
	
	public function delete()
	{
		@unlink(_PS_DOWNLOAD_DIR_.$this->file);
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'product_attachment WHERE id_attachment = '.(int)($this->id));
		return parent::delete();
	}
	
	public function deleteSelection($attachments)
	{
		$return = 1;
		foreach ($attachments AS $id_attachment)
		{
			$attachment = new Attachment((int)($id_attachment));
			$return &= $attachment->delete();
		}
		return $return;
	}
	
	public static function getAttachments($id_lang, $id_product, $include = true)
	{
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'attachment a
		LEFT JOIN '._DB_PREFIX_.'attachment_lang al ON (a.id_attachment = al.id_attachment AND al.id_lang = '.(int)($id_lang).')
		WHERE a.id_attachment '.($include ? 'IN' : 'NOT IN').' (SELECT pa.id_attachment FROM '._DB_PREFIX_.'product_attachment pa WHERE id_product = '.(int)($id_product).')');
	}
	
	public static function attachToProduct($id_product, $array)
	{
		$result1 = Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'product_attachment WHERE id_product = '.(int)($id_product));
		if (is_array($array))
		{
			$ids = array();
			foreach ($array as $id_attachment)
				$ids[] = '('.(int)($id_product).','.(int)($id_attachment).')';
			Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET cache_has_attachments = '.(count($ids) ? '1' : '0').' WHERE id_product = '.(int)($id_product).' LIMIT 1');
			return ($result1 && Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'product_attachment (id_product, id_attachment) VALUES '.implode(',',$ids)));
		}
		return $result1;
	}
}

