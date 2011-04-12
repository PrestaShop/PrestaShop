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

class AdminCMSCategories extends AdminTab
{
	protected $maxImageSize = 300000;

	/** @var object CMSCategory() instance for navigation*/
	private $_CMSCategory;

	public function __construct()
	{
		global $cookie;
		
		$this->table = 'cms_category';
	 	$this->className = 'CMSCategory';
	 	$this->lang = true;
	 	$this->edit = true;
	 	$this->view = true;
	 	$this->delete = true;


		$this->fieldsDisplay = array(
		'id_cms_category' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 30),
		'name' => array('title' => $this->l('Name'), 'width' => 100, 'callback' => 'hideCMSCategoryPosition'),
		'description' => array('title' => $this->l('Description'), 'width' => 500, 'maxlength' => 90, 'orderby' => false),
		'position' => array('title' => $this->l('Position'), 'width' => 40,'filter_key' => 'position', 'align' => 'center', 'position' => 'position'),
		'active' => array('title' => $this->l('Displayed'), 'active' => 'status', 'align' => 'center', 'type' => 'bool', 'orderby' => false));
		
		$this->_CMSCategory = AdminCMSContent::getCurrentCMSCategory();
		$this->_filter = 'AND `id_parent` = '.(int)($this->_CMSCategory->id);
		$this->_select = 'position ';

		parent::__construct();
	}

	public function displayList($token = NULL)
	{
		global $currentIndex;
		/* Display list header (filtering, pagination and column names) */
		$this->displayListHeader($token);
		if (!sizeof($this->_list))
			echo '<tr><td class="center" colspan="'.(sizeof($this->fieldsDisplay) + 2).'">'.$this->l('No items found').'</td></tr>';

		/* Show the content of the table */
		$this->displayListContent($token);

		/* Close list table and submit button */
		$this->displayListFooter($token);
	}

	public function display($token = NULL)
	{
		global $currentIndex, $cookie;
		$id_cms_category = (int)(Tools::getValue('id_cms_category', 1));

		$this->getList((int)($cookie->id_lang), !$cookie->__get($this->table.'Orderby') ? 'position' : NULL, !$cookie->__get($this->table.'Orderway') ? 'ASC' : NULL);
		
		echo '<h3>'.(!$this->_listTotal ? ($this->l('There are no subcategories')) : ($this->_listTotal.' '.($this->_listTotal > 1 ? $this->l('subcategories') : $this->l('subCMS Category')))).' '.$this->l('in CMS Category').' "'.stripslashes(CMSCategory::hideCMSCategoryPosition($this->_CMSCategory->getName())).'"</h3>';
		echo '<a href="'.__PS_BASE_URI__.substr($_SERVER['PHP_SELF'], strlen(__PS_BASE_URI__)).'?tab=AdminCMSContent&add'.$this->table.'&id_parent='.Tools::getValue('id_cms_category').'&token='.($token!=NULL ? $token : $this->token).'"><img src="../img/admin/add.gif" border="0" /> '.$this->l('Add a new sub CMS Category').'</a>
		<div style="margin:10px;">';
		$this->displayList($token);
		echo '</div>';
	}

	public function postProcess($token = NULL)
	{
		global $cookie, $currentIndex;

		$this->tabAccess = Profile::getProfileAccess($cookie->profile, $this->id);
		
		
		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			
			if ($id_cms_category = (int)(Tools::getValue('id_cms_category')))
			{
				if (!CMSCategory::checkBeforeMove($id_cms_category, (int)(Tools::getValue('id_parent'))))
				{
					$this->_errors[] = Tools::displayError('CMS Category cannot be moved here');
					return false;
				}
			}
		}
		/* Change object statuts (active, inactive) */
		elseif (isset($_GET['status']) AND Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					if ($object->toggleStatus())
						Tools::redirectAdmin($currentIndex.'&conf=5'.((int)$object->id_parent ? '&id_cms_category='.(int)$object->id_parent : '').'&token='.Tools::getValue('token'));
					else
						$this->_errors[] = Tools::displayError('An error occurred while updating status.');
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		/* Delete object */
		elseif (isset($_GET['delete'.$this->table]))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()) AND isset($this->fieldImageSettings))
				{
					// check if request at least one object with noZeroObject
					if (isset($object->noZeroObject) AND sizeof($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1)
						$this->_errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
					else
					{
						$this->deleteImage($object->id);
						if ($this->deleted)
						{
							$object->deleted = 1;
							if ($object->update())
								Tools::redirectAdmin($currentIndex.'&conf=1&token='.Tools::getValue('token'));
						}
						elseif ($object->delete())
							Tools::redirectAdmin($currentIndex.'&conf=1&token='.Tools::getValue('token'));
						$this->_errors[] = Tools::displayError('An error occurred during deletion.');
					}
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while deleting object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		elseif (isset($_GET['position']))
		{
			if ($this->tabAccess['edit'] !== '1')
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
			elseif (!Validate::isLoadedObject($object = new CMSCategory((int)(Tools::getValue($this->identifier, Tools::getValue('id_cms_category_to_move', 1))))))
				$this->_errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			elseif (!$object->updatePosition((int)(Tools::getValue('way')), (int)(Tools::getValue('position'))))
				$this->_errors[] = Tools::displayError('Failed to update the position.');
			else
				Tools::redirectAdmin($currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.(($id_category = (int)(Tools::getValue($this->identifier, Tools::getValue('id_cms_category_parent', 1)))) ? ('&'.$this->identifier.'='.$id_category) : '').'&token='.Tools::getAdminTokenLite('AdminCMSContent'));
		}
		/* Delete multiple objects */
		elseif (Tools::getValue('submitDel'.$this->table))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (isset($_POST[$this->table.'Box']))
				{
					$cms_category = new CMSCategory();
					$result = true;
					$result = $cms_category->deleteSelection(Tools::getValue($this->table.'Box'));
					if ($result)
					{
						$cms_category->cleanPositions((int)(Tools::getValue('id_cms_category')));
						Tools::redirectAdmin($currentIndex.'&conf=2&token='.Tools::getAdminTokenLite('AdminCMSContent').'&id_category='.(int)(Tools::getValue('id_cms_category')));
					}
					$this->_errors[] = Tools::displayError('An error occurred while deleting selection.');

				}
				else
					$this->_errors[] = Tools::displayError('You must select at least one element to delete.');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		parent::postProcess(true);
	}

	public function displayForm($token=NULL)
	{
		global $currentIndex, $cookie;
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;
		$active = $this->getFieldValue($obj, 'active');

		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.($token!=NULL ? $token : $this->token).'" method="post" enctype="multipart/form-data">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset style="width:520px"><legend><img src="../img/admin/tab-categories.gif" />'.$this->l('CMS Category').'</legend>
				<label>'.$this->l('Name:').' </label>
				<div class="margin-form translatable">';
		foreach ($this->_languages as $language)
			echo '
					<div class="lang_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" style="width: 260px" name="name_'.$language['id_lang'].'" id="name_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'name', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" '.((!$obj->id) ? ' onkeyup="copy2friendlyURL();"' : '').' /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>';
		echo '	<p class="clear"></p>
				</div>
				<label>'.$this->l('Displayed:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.($active ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.(!$active ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<label>'.$this->l('Parent CMS Category:').' </label>
				<div class="margin-form">
					<select name="id_parent">';
		$categories = CMSCategory::getCategories((int)($cookie->id_lang), false);
		CMSCategory::recurseCMSCategory($categories, $categories[0][1], 1, $this->getFieldValue($obj, 'id_parent'));
		echo '
					</select>
				</div>
				<label>'.$this->l('Description:').' </label>
				<div class="margin-form translatable">';
		foreach ($this->_languages as $language)
			echo '
					<div class="lang_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<textarea name="description_'.$language['id_lang'].'" rows="5" cols="40">'.htmlentities($this->getFieldValue($obj, 'description', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'</textarea>
					</div>';
		echo '	<p class="clear"></p>
				</div>
				<div class="clear"><br /></div>	
				<label>'.$this->l('Meta title:').' </label>
				<div class="margin-form translatable">';
		foreach ($this->_languages as $language)
			echo '
					<div class="lang_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="meta_title_'.$language['id_lang'].'" id="meta_title_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'meta_title', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Forbidden characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>';
		echo '	<p class="clear"></p>
				</div>
				<label>'.$this->l('Meta description:').' </label>
				<div class="margin-form translatable">';
		foreach ($this->_languages as $language)
			echo '<div class="lang_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="meta_description_'.$language['id_lang'].'" id="meta_description_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'meta_description', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Forbidden characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
				</div>';
		echo '	<p class="clear"></p>
				</div>
				<label>'.$this->l('Meta keywords:').' </label>
				<div class="margin-form translatable">';
		foreach ($this->_languages as $language)
			echo '
					<div class="lang_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="meta_keywords_'.$language['id_lang'].'" id="meta_keywords_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'meta_keywords', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Forbidden characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>';
		echo '	<p class="clear"></p>
				</div>
				<label>'.$this->l('Friendly URL:').' </label>
				<div class="margin-form translatable">';
		foreach ($this->_languages as $language)
			echo '<div class="lang_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="link_rewrite_'.$language['id_lang'].'" id="link_rewrite_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'link_rewrite', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" onkeyup="this.value = str2url(this.value);" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Only letters and the minus (-) character are allowed').'<span class="hint-pointer">&nbsp;</span></span>
					</div>';
		echo '	<p class="clear"></p>
				</div>
				
				<div class="margin-form">
					<input type="submit" value="'.$this->l('Save and back to parent CMS Category').'" name="submitAdd'.$this->table.'AndBackToParent" class="button" />
					&nbsp;<input type="submit" class="button" name="submitAdd'.$this->table.'" value="'.$this->l('Save').'"/>
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>
		<p class="clear"></p>';
	}
}
