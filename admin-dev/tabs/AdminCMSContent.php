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

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');
include(PS_ADMIN_DIR.'/tabs/AdminCMSCategories.php');
include(PS_ADMIN_DIR.'/tabs/AdminCMS.php');

class AdminCMSContent extends AdminTab
{
	/** @var object adminCMSCategories() instance */
	private $adminCMSCategories;

	/** @var object adminCMS() instance */
	private $adminCMS;

	/** @var object Category() instance for navigation*/
	private static $_category = NULL;

	public function __construct()
	{
		/* Get current category */
		$id_cms_category = (int)(Tools::getValue('id_cms_category', Tools::getValue('id_cms_category_parent', 1)));
		self::$_category = new CMSCategory($id_cms_category);
		if (!Validate::isLoadedObject(self::$_category))
			die('Category cannot be loaded');

		$this->table = array('cms_category', 'cms');
		$this->adminCMSCategories = new adminCMSCategories();
		$this->adminCMS = new adminCMS();

		parent::__construct();
	}

	/**
	 * Return current category
	 *
	 * @return object
	 */
	public static function getCurrentCMSCategory()
	{
		return self::$_category;
	}

	public function viewAccess($disable = false)
	{
		$result = parent::viewAccess($disable);
		$this->adminCMSCategories->tabAccess = $this->tabAccess;
		$this->adminCMS->tabAccess = $this->tabAccess;
		return $result;
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitDelcms') OR Tools::isSubmit('previewSubmitAddcmsAndPreview') OR Tools::isSubmit('submitAddcms') OR isset($_GET['deletecms']) OR Tools::isSubmit('viewcms') OR (Tools::isSubmit('status') AND Tools::isSubmit('id_cms')) OR (Tools::isSubmit('position') AND !Tools::isSubmit('id_cms_category_to_move')))
			$this->adminCMS->postProcess();
		if(Tools::isSubmit('submitDelcms_category') OR Tools::isSubmit('submitAddcms_categoryAndBackToParent') OR Tools::isSubmit('submitAddcms_category') OR isset($_GET['deletecms_category']) OR (Tools::isSubmit('status') AND Tools::isSubmit('id_cms_category')) OR (Tools::isSubmit('position') AND Tools::isSubmit('id_cms_category_to_move')))
			$this->adminCMSCategories->postProcess();

	}

	public function displayErrors()
	{
		parent::displayErrors();
		$this->adminCMS->displayErrors();
		$this->adminCMSCategories->displayErrors();
	}

	public function display()
	{
		global $currentIndex;

		if (((Tools::isSubmit('submitAddcms_category') OR Tools::isSubmit('submitAddcms_categoryAndStay')) AND sizeof($this->adminCMSCategories->_errors)) OR isset($_GET['updatecms_category']) OR isset($_GET['addcms_category']))
		{
			$this->adminCMSCategories->displayForm($this->token);
			echo '<br /><br /><a href="'.$currentIndex.'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" /> '.$this->l('Back to list').'</a><br />';
			
		}
		elseif (((Tools::isSubmit('submitAddcms') OR Tools::isSubmit('submitAddcmsAndStay')) AND sizeof($this->adminCMS->_errors)) OR isset($_GET['updatecms']) OR isset($_GET['addcms']))
		{
			$this->adminCMS->displayForm($this->token);
			echo '<br /><br /><a href="'.$currentIndex.'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" /> '.$this->l('Back to list').'</a><br />';
		
		}
		else
		{
		$id_cms_category = (int)(Tools::getValue('id_cms_category'));
		if (!$id_cms_category)
			$id_cms_category = 1;
		$cms_tabs = array('cms_category', 'cms');
		// Cleaning links
		$catBarIndex = $currentIndex;
		foreach ($cms_tabs AS $tab)
			if (Tools::getValue($tab.'Orderby') && Tools::getValue($tab.'Orderway')) 
				$catBarIndex = preg_replace('/&'.$tab.'Orderby=([a-z _]*)&'.$tab.'Orderway=([a-z]*)/i', '', $currentIndex);
		echo '<div class="cat_bar"><span style="color: #3C8534;">'.$this->l('Current category').' :</span>&nbsp;&nbsp;&nbsp;'.getPath($catBarIndex, $id_cms_category,'','','cms').'</div>';
		echo '<h2>'.$this->l('Categories').'</h2>';
		$this->adminCMSCategories->display($this->token);
		echo '<div style="margin:10px">&nbsp;</div>';
		echo '<h2>'.$this->l('Pages in this category').'</h2>';
		$this->adminCMS->display($this->token);
		}
		
	}
}

