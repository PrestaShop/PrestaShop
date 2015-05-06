<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CMSRepository extends RepositoryManager
{
	protected $entity = null;

	public function __construct($component_name)
	{
		$this->component = $component_name;
		$this->entity = 'CMSEntity';
	}

	/**
	 * Return the complete list of CMS pages
	 * @return array|false
	 */
	public function getCMSPagesList()
	{
		return CMS::listCms();
	}

	/**
	 * Return a CMS Page from given ID
	 * @param $cms_id
	 * @param $id_language
	 * @return CMS
	 */
	public function getCMSById($cms_id, $id_language)
	{
		return new CMS($cms_id, $id_language);
	}

	/**
	 * Get CMS Page content for a given id_cms / id_lang (optionnal) / id_shop (optionnal)
	 * @param $id_cms
	 * @param null $id_lang
	 * @param null $id_shop
	 * @return array|bool|null|object
	 */
	public function getCMSContent($id_cms, $id_lang = null, $id_shop = null)
	{
		return CMS::getCMSContent($id_cms, $id_lang, $id_shop);
	}
}