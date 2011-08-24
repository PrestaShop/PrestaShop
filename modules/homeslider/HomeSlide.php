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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class HomeSlide extends ObjectModel
{
	public $title;
	public $description;
	public $url;
	public $legend;
	public $image;
	public $active;
	public $position;
	public $maxImageSize = 307200;

	protected $fieldsValidate = array(
		 'active' => 'isunsignedInt',
		 'position' => 'isunsignedInt'
	);
	protected $fieldsRequired = array(
		 'active',
		 'position'
	);
	protected $fieldsRequiredLang = array('title', 'description', 'url', 'legend');
	protected $fieldsSizeLang = array(
		 'description' => 4000,
		 'title' => 255,
		 'legend' => 255,
		 'url' => 255,
		 'image' => 255
	);
	protected $fieldsValidateLang = array(
		 'title' => 'isName',
		 'description' => 'isCleanHtml',
		 'url' => 'isUrl',
		 'legend' => 'isCleanHtml',
		 'image' => 'isCleanHtml'
	);

	protected $tables = array('homeslider_slides, homeslider_slides_lang');
	protected $table = 'homeslider_slides';
	protected $identifier = 'id_slide';

	public function getFields()
	{
		$this->validateFields();
		$fields['id_slide'] = (int)$this->id;
		$fields['active'] = (int)$this->active;
		$fields['position'] = (int)$this->position;
		return $fields;
	}

	public function getTranslationsFieldsChild()
	{
		$this->validateFieldsLang();
		return $this->getTranslationsFields(array(
			'title',
			'description',
			'url',
			'legend',
			'image'
		));
	}

	public	function __construct($id_slide = NULL, $id_lang = NULL, $id_shop = NULL, Context $context = NULL)
	{
		parent::__construct($id_slide, $id_lang, $id_shop);
	}

	public function add()
	{
		$context = Context::getContext();
		$id_shop = $context->shop->getID();

		$res = parent::add();
		$res &= Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'homeslider` (`id_shop`, `id_slide`)
			VALUES('.(int)$id_shop.', '.(int)$this->id.')'
		);

		return $res;
	}

	public function delete()
	{
		$res = null;
		$images = $this->image;
		foreach ($images as $image)
		{
			if (file_exists(dirname(__FILE__).'/images/'.$image))
				$res &= @unlink(dirname(__FILE__).'/images/'.$image);
		}

		$res &= $this->reOrderPositions();
		$res &= Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'homeslider`
			WHERE `id_slide` = '.(int)$this->id
		);

		$res &= parent::delete();
		return $res;
	}

	public function reOrderPositions()
	{
		$id_slide = $this->id;
		$context = Context::getContext();
		$id_shop = $context->shop->getID();

		$max = Db::getInstance()->ExecuteS('
			SELECT MAX(hss.`position`) as position
			FROM `'._DB_PREFIX_.'homeslider_slides` hss, `'._DB_PREFIX_.'homeslider` hs
			WHERE hss.`id_slide` = hs.`id_slide` AND hs.`id_shop` = '.(int)$id_shop
		);

		if ((int)$max == (int)$id_slide)
			return true;

		$rows = Db::getInstance()->ExecuteS('
			SELECT hss.`position` as position, hss.`id_slide` as id_slide
			FROM `'._DB_PREFIX_.'homeslider_slides` hss, `'._DB_PREFIX_.'homeslider` hs
			WHERE hss.`id_slide` = hs.`id_slide` AND hs.`id_shop` = '.(int)$id_shop.' AND hss.`position` > '.(int)$this->position
		);

		if (!$rows)
			return false;

		foreach ($rows as $row)
		{
			$current_slide = new HomeSlide($row['id_slide']);
			--$current_slide->position;
			$current_slide->update();
			unset($current_slide);
		}
		return true;
	}
}