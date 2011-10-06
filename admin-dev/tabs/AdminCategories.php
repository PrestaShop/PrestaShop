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
*  @version  Release: $Revision: 7499 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminCategories extends AdminTab
{
	/** @var object Category() instance for navigation*/
	private $_category = null;

	public function __construct()
	{
		$this->table = 'category';
	 	$this->className = 'Category';
	 	$this->lang = true;
	 	$this->add = true;
	 	$this->edit = true;
	 	$this->view = true;
	 	$this->delete = true;

 		$this->fieldImageSettings = array('name' => 'image', 'dir' => 'c');

		$this->fieldsDisplay = array(
		'id_category' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 30),
		'name' => array('title' => $this->l('Name'), 'width' => 100),
		'description' => array('title' => $this->l('Description'), 'width' => 500, 'maxlength' => 90, 'callback' => 'getDescriptionClean', 'orderby' => false),
		'position' => array('title' => $this->l('Position'), 'width' => 40,'filter_key' => 'position', 'align' => 'center', 'position' => 'position'),
		'active' => array('title' => $this->l('Displayed'), 'active' => 'status', 'align' => 'center', 'type' => 'bool', 'orderby' => false));
		
		if ($id_category = Tools::getvalue('id_category'))
			$this->_category = new Category($id_category);
		else
			$this->_category = new Category(1);


		$this->_filter = 'AND `id_parent` = '.(int)($this->_category->id);
		$this->_select = 'position ';

		parent::__construct();
	}

	public function displayList($token = NULL)
	{
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
		if (((Tools::isSubmit('submitAddcategory') OR Tools::isSubmit('submitAddcategoryAndStay')) AND sizeof($this->_errors)) OR isset($_GET['updatecategory']) OR isset($_GET['addcategory']))
		{
			$this->displayForm($this->token);
			echo '<br /><br /><a href="'.self::$currentIndex.'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" /> '.$this->l('Back to list').'</a><br />';
		}
		else
		{
			$this->getList((int)($this->context->language->id), !$this->context->cookie->__get($this->table.'Orderby') ? 'position' : NULL, !$this->context->cookie->__get($this->table.'Orderway') ? 'ASC' : NULL, 0, NULL, $this->context->shop->getID(true));
			echo '<h3>'.(!$this->_listTotal ? ($this->l('There are no subcategories')) : ($this->_listTotal.' '.($this->_listTotal > 1 ? $this->l('subcategories') : $this->l('subcategory')))).' '.$this->l('in category').' "'.stripslashes($this->_category->getName()).'"</h3>';
			if ($this->tabAccess['add'] === '1')
				echo '<a href="'.__PS_BASE_URI__.substr($_SERVER['PHP_SELF'], strlen(__PS_BASE_URI__)).'?controller=AdminCategories&add'.$this->table.'&id_parent='.$this->_category->id.'&token='.($token!=NULL ? $token : $this->token).'"><img src="../img/admin/add.gif" border="0" /> '.$this->l('Add a new subcategory').'</a>';
				echo '<a href="'.$this->context->link->getAdminLink('AdminProducts').'&amp;addproduct&amp;id_parent='.$this->_category->id.'">
					<img src="../img/admin/add.gif" border="0" /> '.$this->l('Add a new product').'</a>';
	
			echo '<div style="margin:10px;">';
			$this->displayList($token);
			echo '</div>';
		}
	}

	public function postProcess($token = NULL)
	{
		$this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, $this->id);

		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			if ($id_category = (int)(Tools::getValue('id_category')))
			{
				if (!Category::checkBeforeMove($id_category, $this->_category->id_parent))
				{
					$this->_errors[] = Tools::displayError('Category cannot be moved here');
					return false;
				}
			}
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
						if ($this->deleted)
						{
							$object->deleteImage();
							$object->deleted = 1;
							if ($object->update())
								Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.Tools::getValue('token').'&id_category='.(int)($object->id_parent));
						}
						elseif ($object->delete())
							Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.Tools::getValue('token').'&id_category='.(int)($object->id_parent));
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
			elseif (!Validate::isLoadedObject($object = new Category((int)(Tools::getValue($this->identifier, Tools::getValue('id_category_to_move', 1))))))
				$this->_errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			if (!$object->updatePosition((int)(Tools::getValue('way')), (int)(Tools::getValue('position'))))
				$this->_errors[] = Tools::displayError('Failed to update the position.');
			else
				Tools::redirectAdmin(self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.(($id_category = (int)(Tools::getValue($this->identifier, Tools::getValue('id_category_parent', 1)))) ? ('&'.$this->identifier.'='.$id_category) : '').'&token='.Tools::getAdminTokenLite('AdminCategories'));
		}
		/* Delete multiple objects */
		elseif (Tools::getValue('submitDel'.$this->table))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (isset($_POST[$this->table.'Box']))
				{
					$category = new Category();
					$result = true;
					$result = $category->deleteSelection(Tools::getValue($this->table.'Box'));
					if ($result)
					{
						$category->cleanPositions((int)(Tools::getValue('id_category')));
						Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.Tools::getAdminTokenLite('AdminCategories').'&id_category='.(int)(Tools::getValue('id_category')));
					}
					$this->_errors[] = Tools::displayError('An error occurred while deleting selection.');

				}
				else
					$this->_errors[] = Tools::displayError('You must select at least one element to delete.');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
			return;
		}
		parent::postProcess();
	}

	protected function postImage($id)
	{
		$ret = parent::postImage($id);
		if (($id_category = (int)(Tools::getValue('id_category'))) AND isset($_FILES) AND sizeof($_FILES) AND $_FILES['image']['name'] != NULL AND file_exists(_PS_CAT_IMG_DIR_.$id_category.'.jpg'))
		{
			$imagesTypes = ImageType::getImagesTypes('categories');
			foreach ($imagesTypes AS $k => $imageType)
				imageResize(_PS_CAT_IMG_DIR_.$id_category.'.jpg', _PS_CAT_IMG_DIR_.$id_category.'-'.stripslashes($imageType['name']).'.jpg', (int)($imageType['width']), (int)($imageType['height']));
		}
		return $ret;
	}

	public function displayForm($token = NULL)
	{
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;
		$active = $this->getFieldValue($obj, 'active');
		$customer_groups = $obj->getGroups();
		if ($this->context->shop->getContextType() == Shop::CONTEXT_SHOP)
			$id_category = $this->context->shop->getCategory();
		else
			$id_category = (int)Tools::getValue('id_parent');

		echo '
		<form action="'.self::$currentIndex.'&submitAdd'.$this->table.'=1&token='.($token!=NULL ? $token : $this->token).'" method="post" enctype="multipart/form-data">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/tab-categories.gif" />'.$this->l('Category').'</legend>
				<label>'.$this->l('Name:').' </label>
				<div class="margin-form translatable">';
		foreach ($this->_languages AS $language)
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
				<label>'.$this->l('Parent category:').' </label>
				<div class="margin-form">';
				// Translations are not automatic for the moment ;)
				$trads = array(
					 'Home' => $this->l('Home'),
					 'selected' => $this->l('selected'),
					 'Collapse All' => $this->l('Collapse All'),
					 'Expand All' => $this->l('Expand All')
				);
				echo Helper::renderAdminCategorieTree($trads, array(isset($obj->id_parent) ? $obj->id_parent : Tools::getValue('id_parent', 1)), 'id_parent', true);
				echo '</div>
				<label>'.$this->l('Description:').' </label>
				<div class="margin-form translatable">';
		foreach ($this->_languages AS $language)
			echo '
					<div class="lang_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<textarea name="description_'.$language['id_lang'].'" rows="10" cols="100">'.htmlentities($this->getFieldValue($obj, 'description', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'</textarea>
					</div>';
		echo '	<p class="clear"></p>
				</div>
				<label>'.$this->l('Image:').' </label>
				<div class="margin-form">';
		echo 		$this->displayImage($obj->id, _PS_IMG_DIR_.'c/'.$obj->id.'.jpg', 350, NULL, Tools::getAdminToken('AdminCategories'.(int)(Tab::getIdFromClassName('AdminCategories')).$this->context->employee->id), true);
		echo '	<br /><input type="file" name="image" />
					<p>'.$this->l('Upload category logo from your computer').'</p>
				</div>
				<div class="clear"><br /></div>
				<label>'.$this->l('Meta title:').' </label>
				<div class="margin-form translatable">';
		foreach ($this->_languages AS $language)
			echo '
					<div class="lang_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="meta_title_'.$language['id_lang'].'" id="meta_title_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'meta_title', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Forbidden characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>';
		echo '	<p class="clear"></p>
				</div>
				<label>'.$this->l('Meta description:').' </label>
				<div class="margin-form translatable">';
		foreach ($this->_languages AS $language)
			echo '<div class="lang_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="meta_description_'.$language['id_lang'].'" id="meta_description_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'meta_description', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Forbidden characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
				</div>';
		echo '	<p class="clear"></p>
				</div>
				<label>'.$this->l('Meta keywords:').' </label>
				<div class="margin-form translatable">';
		foreach ($this->_languages AS $language)
			echo '
					<div class="lang_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="meta_keywords_'.$language['id_lang'].'" id="meta_keywords_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'meta_keywords', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Forbidden characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>';
		echo '	<p class="clear"></p>
				</div>
				<label>'.$this->l('Friendly URL:').' </label>
				<div class="margin-form translatable">';
		foreach ($this->_languages AS $language)
			echo '<div class="lang_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="link_rewrite_'.$language['id_lang'].'" id="link_rewrite_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'link_rewrite', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" onchange="this.value = str2url(this.value);" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Only letters and the minus (-) character are allowed').'<span class="hint-pointer">&nbsp;</span></span>
					</div>';
		echo '	<p class="clear"></p>
				</div>
				<label>'.$this->l('Groups access:').' </label>
				<div class="margin-form">';
					$groups = Group::getGroups((int)($this->context->language->id));
					if (sizeof($groups))
					{
						echo '
					<table cellspacing="0" cellpadding="0" class="table" style="width: 28em;">
						<tr>
							<th><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'groupBox[]\', this.checked)"'.(!isset($obj->id) ? 'checked="checked" ' : '').' /></th>
							<th>'.$this->l('ID').'</th>
							<th>'.$this->l('Group name').'</th>
						</tr>';
						$irow = 0;
						foreach ($groups AS $group)
							echo '
							<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
								<td><input type="checkbox" name="groupBox[]" class="groupBox" id="groupBox_'.$group['id_group'].'" value="'.$group['id_group'].'" '.((in_array($group['id_group'], $customer_groups) OR (!isset($obj->id))) ? 'checked="checked" ' : '').'/></td>
								<td>'.$group['id_group'].'</td>
								<td><label for="groupBox_'.$group['id_group'].'" class="t">'.$group['name'].'</label></td>
							</tr>';
						echo '
					</table>
					<p style="padding:0px; margin:10px 0px 10px 0px;">'.$this->l('Mark all groups you want to give access to this category').'</p>
					';
					} else
						echo '<p>'.$this->l('No group created').'</p>';
				echo '
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('Save and back to parent category').'" name="submitAdd'.$this->table.'AndBackToParent" class="button" />
					&nbsp;<input type="submit" class="button" name="submitAdd'.$this->table.'" value="'.$this->l('Save').'"/>
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>
		<p class="clear"></p>';
		// TinyMCE
		$iso = $this->context->language->iso_code;
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		echo '<script type="text/javascript">
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>';
	}
}
