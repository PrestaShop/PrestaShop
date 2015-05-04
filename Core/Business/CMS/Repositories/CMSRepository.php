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

class CMSRepository extends EntityRepository
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
		// @TODO: Works as a "proxy" ATM but should be integrated directly here in the future
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
		// @TODO: To be refactored
		return new CMS($cms_id, $id_language);
	}



}
