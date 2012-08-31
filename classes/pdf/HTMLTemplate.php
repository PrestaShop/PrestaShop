<?php

/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 8797 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5
 */
abstract class HTMLTemplateCore
{
	public $title;
	public $date;
	public $available_in_your_account = true;
	public $smarty;
	public $shop;

	/**
	 * Returns the template's HTML header
	 * @return string HTML header
	 */
	public function getHeader()
	{
		$shop_name = Configuration::get('PS_SHOP_NAME');
		$path_logo = $this->getLogo();

		$width = 0;
		$height = 0;
		if (!empty($path_logo))
			list($width, $height) = getimagesize($path_logo);

		$this->smarty->assign(array(
			'logo_path' => $path_logo,
			'img_ps_dir' => 'http://'.Tools::getMediaServer(_PS_IMG_)._PS_IMG_,
			'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
			'title' => $this->title,
			'date' => $this->date,
			'shop_name' => $shop_name,
			'width_logo' => $width,
			'height_logo' => $height
		));

		return $this->smarty->fetch($this->getTemplate('header'));
	}

	/**
	 * Returns the template's HTML footer
	 * @return string HTML footer
	 */
	public function getFooter()
	{
		$shop_address = $this->getShopAddress();
		$this->smarty->assign(array(
			'available_in_your_account' => $this->available_in_your_account,
			'shop_address' => $shop_address,
			'shop_fax' => Configuration::get('PS_SHOP_FAX'),
			'shop_phone' => Configuration::get('PS_SHOP_PHONE'),
			'shop_details' => Configuration::get('PS_SHOP_DETAILS'),
			'free_text' => Configuration::get('PS_INVOICE_FREE_TEXT', (int)Context::getContext()->language->id)
		));

		return $this->smarty->fetch($this->getTemplate('footer'));
	}

	/**
	 * Returns the shop address
	 * @return string
	 */
	protected function getShopAddress()
	{
		$shop_address = '';
		if (Validate::isLoadedObject($this->shop))
		{
			$shop_address_obj = $this->shop->getAddress();
			if (isset($shop_address_obj) && $shop_address_obj instanceof Address)
				$shop_address = AddressFormat::generateAddress($shop_address_obj, array(), ' - ', ' ');
			return $shop_address;
		}

		return $shop_address;
	}

	/**
	 * Returns the invoice logo
	 */
	protected function getLogo()
	{
		$logo = '';

		$physical_uri = Context::getContext()->shop->physical_uri.'img/';

		if (Configuration::get('PS_LOGO_INVOICE') != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE')))
			$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE');
		elseif (Configuration::get('PS_LOGO') != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO')))
			$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO');
		return $logo;
	}

	/**
	* Assign hook data
	*
	* @param $object generally the object used in the constructor
	*/
	public function assignHookData($object)
	{
		$template = ucfirst(str_replace('HTMLTemplate', '', get_class($this)));
		$hook_name = 'displayPDF'.$template;

		$this->smarty->assign(array(
			'HOOK_DISPLAY_PDF' => Hook::exec($hook_name, array('object' => $object))
		));
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	abstract public function getContent();


	/**
	 * Returns the template filename
	 * @return string filename
	 */
	abstract public function getFilename();

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	abstract public function getBulkFilename();

	/**
	 * If the template is not present in the theme directory, it will return the default template
	 * in _PS_PDF_DIR_ directory
	 *
	 * @param $template_name
	 * @return string
	 */
	protected function getTemplate($template_name)
	{
		$template = false;
		$default_template = _PS_PDF_DIR_.'/'.$template_name.'.tpl';
		$overriden_template = _PS_THEME_DIR_.'/pdf/'.$template_name.'.tpl';

		if (file_exists($overriden_template))
			$template = $overriden_template;
		else if (file_exists($default_template))
			$template = $default_template;

		return $template;
	}


	/**
	 * Translatation method
	 * @param string $string
	 * @return string translated text
	 */
	protected static function l($string)
	{
		return Translate::getPdfTranslation($string);
	}
}

