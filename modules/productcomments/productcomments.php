<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class ProductComments extends Module
{
	const INSTALL_SQL_FILE = 'install.sql';

	private $_html = '';
	private $_postErrors = array();
	
	private $_productCommentsCriterionTypes = array();
	private $_baseUrl;
	
	public function __construct()
	{
		$this->name = 'productcomments';
		$this->tab = 'front_office_features';
		$this->version = '2.1';
		$this->author = 'PrestaShop';

		parent::__construct();

		$this->displayName = $this->l('Product Comments');
		$this->description = $this->l('Allow users to post comment about a product.');
	}

	public function install()
	{
		if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return false;
		elseif (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return false;
		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);		
		$sql = preg_split("/;\s*[\r\n]+/", trim($sql));

		foreach ($sql AS $query)
			if (!Db::getInstance()->Execute(trim($query)))
				return false;
		if (parent::install() == false OR $this->registerHook('productTab') == false
		OR $this->registerHook('extraProductComparison') == false OR $this->registerHook('productTabContent') == false
		OR $this->registerHook('header') == false OR !Configuration::updateValue('PRODUCT_COMMENTS_MINIMAL_TIME', 30)
		OR !Configuration::updateValue('PRODUCT_COMMENTS_ALLOW_GUESTS', 0)
		OR !Configuration::updateValue('PRODUCT_COMMENTS_MODERATE', 1))
			return false;
		return true;
    }
	
	function uninstall()
	{
		if (!parent::uninstall() OR !Configuration::deleteByName('PRODUCT_COMMENTS_MODERATE') OR !Configuration::deleteByName('PRODUCT_COMMENTS_ALLOW_GUESTS') OR !Configuration::deleteByName('PRODUCT_COMMENTS_MINIMAL_TIME'))
			return false;
		return true;
	}
	
	protected function _postProcess()
	{		
		if (Tools::isSubmit('submitModerate'))
		{
			Configuration::updateValue('PRODUCT_COMMENTS_MODERATE', (int)Tools::getValue('moderate'));
			Configuration::updateValue('PRODUCT_COMMENTS_ALLOW_GUESTS', (int)Tools::getValue('allow_guest'));
			Configuration::updateValue('PRODUCT_COMMENTS_MINIMAL_TIME', (int)Tools::getValue('product_comments_minimal_time'));
			$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
		}
		if ($id_criterion =  (int)Tools::getValue('deleteCriterion'))
		{
			$productCommentCriterion = new ProductCommentCriterion((int)$id_criterion);
			if ($productCommentCriterion->id)
				if ($productCommentCriterion->delete())
					$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Criterion deleted').'</div>';
		}
	}
	
	public function getContent()
	{
		include_once(dirname(__FILE__).'/ProductCommentCriterion.php');
		
		$this->_setBaseUrl();		
		$this->_productCommentsCriterionTypes = ProductCommentCriterion::getTypes();
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		$this->_postProcess();
		$this->_checkModerateComment();
		$this->_checkCriterion();
		$this->_updateApplicationCriterion();

		return $this->_html.$this->_displayForm();
	}
	
	private function _setBaseUrl()
	{
		$this->_baseUrl = 'index.php?';
		foreach ($_GET AS $k => $value)
			if (!in_array($k, array('deleteCriterion', 'editCriterion')))
				$this->_baseUrl .= $k.'='.$value.'&';
		$this->_baseUrl = rtrim($this->_baseUrl, '&');
	}
	
	private function _checkModerateComment()
	{
		$action = Tools::getValue('action');
		if (empty($action) === false &&
			(int)(Configuration::get('PRODUCT_COMMENTS_MODERATE')))
		{
			$product_comments = Tools::getValue('id_product_comment');
			if (sizeof($product_comments))
			{
				require_once(dirname(__FILE__).'/ProductComment.php');
				switch ($action)
				{
					case 'accept':
						foreach ($product_comments AS $id_product_comment)
						{
							if (!$id_product_comment)
								continue;
							$comment = new ProductComment((int)$id_product_comment);
							$comment->validate();
						}
						break;
					case 'delete':
						foreach ($product_comments AS $id_product_comment)
						{
							if (!$id_product_comment)
								continue;
							$comment = new ProductComment((int)$id_product_comment);
							$comment->delete();
							ProductComment::deleteGrades((int)$id_product_comment);
						}
						break;
					default:
						;
				}
			}
		}
	}
	private function _checkCriterion()
	{
		$action_criterion = Tools::getValue('criterion_action');
		$name = Tools::getValue('criterion');
		if (Tools::isSubmit('submitAddCriterion'))
		{
			global $cookie;
			require_once(dirname(__FILE__).'/ProductCommentCriterion.php');
			$languages = Language::getLanguages();
			$id_criterion = (int)Tools::getValue('id_product_comment_criterion');
			$productCommentCriterion = new ProductCommentCriterion((int)$id_criterion);
			foreach ($languages AS $lang)
				$productCommentCriterion->name[(int)$lang['id_lang']] = Tools::getValue('criterion_'.(int)$lang['id_lang']);

			$productCommentCriterion->id_product_comment_criterion_type = (int)Tools::getValue('criterion_type');
			$productCommentCriterion->active = (int)Tools::getValue('criterion_active');

			if ($productCommentCriterion->save())
				$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.(Tools::getValue('editCriterion') ? $this->l('Criterion updated') : $this->l('Criterion added')).'</div>';
		}
		elseif (!empty($action_criterion) AND empty($name))
		{
			$id_product_comment_criterion = Tools::getValue('id_product_comment_criterion');
			require_once(dirname(__FILE__).'/ProductCommentCriterion.php');
			switch ($action_criterion)
			{
				case 'edit':
					ProductCommentCriterion::update($id_product_comment_criterion,
						Tools::getValue('criterion_id_lang'),
						Tools::getValue('criterion_name'));
					break;
				case 'delete':
					ProductCommentCriterion::delete($id_product_comment_criterion);
					break;
				default:
					;
			}
		}
	}
	
	private function _updateApplicationCriterion()
	{
		if (Tools::isSubmit('submitApplicationCriterion'))
		{
			include_once(dirname(__FILE__).'/ProductCommentCriterion.php');
			
			$id_criterion = (int)Tools::getValue('id_criterion');
			$productCommentCriterion = new ProductCommentCriterion((int)$id_criterion);
			if ($productCommentCriterion->id)
			{
				if ($productCommentCriterion->id_product_comment_criterion_type == 2)
				{
					$productCommentCriterion->deleteCategories();
					if ($categories = Tools::getValue('id_product'))
						if (sizeof($categories))
							foreach ($categories AS $id_category)
								$productCommentCriterion->addCategory((int)$id_category);
				}
				elseif ($productCommentCriterion->id_product_comment_criterion_type == 3)
				{
					$productCommentCriterion->deleteProducts();
					if ($products = Tools::getValue('id_product'))
						if (sizeof($products))
							foreach ($products AS $product)
								$productCommentCriterion->addProduct((int)$product);
				}
			}
						
			$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
		}
	}
	
	private function _displayForm()
	{
		$this->_displayFormModerate();
		$this->_displayFormConfigurationCriterion();
		$this->_displayFormApplicationCriterion();
		return $this->_html;
	}

	private function _displayFormModerate()
	{
		$this->_html = '<script type="text/javascript" src="'.$this->_path.'js/moderate.js"></script>
			<fieldset class="width2">
				<legend><img src="../img/admin/cog.gif" alt="" title="" />'.$this->l('Configuration').'</legend>
				<form action="'.$this->_baseUrl.'" method="post" name="comment_configuration">
					<label style="padding-top: 0;">'.$this->l('All comments must be validated by an employee').'</label>
					<div class="margin-form">
						<input type="radio" name="moderate" id="moderate_on" value="1" '.(Configuration::get('PRODUCT_COMMENTS_MODERATE') ? 'checked="checked" ' : '').'/>
						<label class="t" for="moderate_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
						<input type="radio" name="moderate" id="moderate_off" value="0" '.(!Configuration::get('PRODUCT_COMMENTS_MODERATE') ? 'checked="checked" ' : '').'/>
						<label class="t" for="moderate_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					</div>
					<div class="clear" style="height: 20px;"></div>
					<label style="padding-top: 0;">'.$this->l('Allow guest comments').'</label>
					<div class="margin-form">
						<input type="radio" name="allow_guest" id="allow_guest_on" value="1" '.(Configuration::get('PRODUCT_COMMENTS_ALLOW_GUESTS') ? 'checked="checked" ' : '').'/>
						<label class="t" for="allow_guest_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
						<input type="radio" name="allow_guest" id="allow_guest_off" value="0" '.(!Configuration::get('PRODUCT_COMMENTS_ALLOW_GUESTS') ? 'checked="checked" ' : '').'/>
						<label class="t" for="allow_guest_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					</div>
					<div class="clear" style="height: 20px;"></div>
					<label style="padding-top: 0;">'.$this->l('Minimum time between 2 comments from the same user').'</label>
					<div class="margin-form">
						<input name="product_comments_minimal_time" type="text" class="text" value="'.Configuration::get('PRODUCT_COMMENTS_MINIMAL_TIME').'" style="width: 40px; text-align: right;" /> '.$this->l('seconds').'
					</div>
					<div class="clear"></div>
					<div class="margin-form clear">
						<input type="submit" name="submitModerate" value="'.$this->l('Save').'" class="button" />
					</div>
				</form>
			</fieldset>
			<br />
			<fieldset class="width2">
				<legend><img src="'.$this->_path.'img/comments_delete.png" alt="" title="" />'.$this->l('Moderate Comments').'</legend>';
			if (Configuration::get('PRODUCT_COMMENTS_MODERATE'))
			{
				require_once(dirname(__FILE__).'/ProductComment.php');
				$comments = ProductComment::getByValidate();
				if (sizeof($comments))
				{
					$this->_html .= '
					<form action="'.$this->_baseUrl.'" method="post" name="comment_form">
				  	<input type="hidden" name="id_product_comment[]" id="id_product_comment" />
					 <input type="hidden" name="action" id="action" />
					 <br /><table class="table" border="0" cellspacing="0" cellpadding="0">
					 <thead>
					  <tr>
						<th><input class="noborder" type="checkbox" name="id_product_comment[]" onclick="checkDelBoxes(this.form, \'id_product_comment[]\', this.checked)" /></th>
						<th style="width:150px;">'.$this->l('Author').'</th>
						<th style="width:550px;">'.$this->l('Comment').'</th>
						<th style="width:150px;">'.$this->l('Product name').'</th>
						<th style="width:30px;">'.$this->l('Actions').'</th>
					  </tr>
					 </thead>
					 <tbody>';
					foreach ($comments AS $comment)
						$this->_html .= '<tr>
						 <td><input class="noborder" type="checkbox" value="'.$comment['id_product_comment'].'" name="id_product_comment[]" /></td>
						 <td>'.htmlspecialchars($comment['customer_name'], ENT_COMPAT, 'UTF-8').'.</td>
						 <td>'.htmlspecialchars($comment['content'], ENT_COMPAT, 'UTF-8').'</td>
						 <td>'.$comment['id_product'].' - '.htmlspecialchars($comment['name'], ENT_COMPAT, 'UTF-8').'</td>
						<td><a href="javascript:;" onclick="acceptComment(\''.(int)($comment['id_product_comment']).'\');"><img src="'.$this->_path.'img/accept.png" alt="'.$this->l('Accept').'" title="'.$this->l('Accept').'" /></a>
							  <a href="javascript:;" onclick="deleteComment(\''.(int)($comment['id_product_comment']).'\');"><img src="'.$this->_path.'img/delete.png" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a></td>
						</tr>';
						$this->_html .= '
						<tr>
							<td colspan="4" style="font-weight:bold;text-align:right">'.$this->l('Selection:').'</td>
							<td><a href="javascript:;" onclick="acceptComment(0);"><img src="'.$this->_path.'img/accept.png" alt="'.$this->l('Accept').'" title="'.$this->l('Accept').'" /></a>
							<a href="javascript:;" onclick="deleteComment(0);"><img src="'.$this->_path.'img/delete.png" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a></td>
						</tr>
						</tbody>
					</table>
					</form>';
				}
				else
					$this->_html .= $this->l('No comments to validate at this time.');
			}
		$this->_html .= '</fieldset><br />';
	}
	
	private function _displayFormConfigurationCriterion()
	{
		global $cookie;
		
		$langs = Language::getLanguages(false);
		$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$id_criterion = (int)Tools::getValue('editCriterion');
		$criterion = new ProductCommentCriterion((int)$id_criterion);
		$languageIds = 'criterion';
		$this->_html .= '
		<fieldset class="width2">
			<legend><img src="'.$this->_path.'img/note.png" alt="" />'.$this->l('Add a new comment criterion').'</legend>
				<p style="margin-bottom: 20px;">'.$this->l('You can define several criterions to help your customers during their review. For instance: efficiency, lightness, design.').'<br />
				<br />'.$this->l('You can add a new criterion below:').'</p>
				<form action="'.$this->_baseUrl.'" method="post" name="criterion_form">
				<label>'.$this->l('Name').'</label>
				<div class="margin-form">
				<input type="hidden" name="id_product_comment_criterion" value="'.(int)$criterion->id.'" />';
				foreach ($langs AS $lang)
					$this->_html .= '
					<div id="criterion_'.(int)$lang['id_lang'].'" style="display: '.($lang['id_lang'] == $id_lang_default ? 'block' : 'none').'; float: left;">
						<input value="'.$criterion->name[(int)$lang['id_lang']].'" type="text" class="text" name="criterion_'.(int)$lang['id_lang'].'" />
					</div>';
				$this->_html .= $this->displayFlags($langs, (int)$id_lang_default, $languageIds, 'criterion', true);
				$this->_html .= '
				</div>
				<div class="clear">&nbsp;</div>
				<label for="criterion_type">'.$this->l('Apply to').'</label>
				<div class="margin-form">
					<select name="criterion_type">';
				foreach ($this->_productCommentsCriterionTypes AS $k => $type)
					$this->_html.= '<option value="'.(int)$k.'" '.($k == $criterion->id_product_comment_criterion_type ? 'selected="selected"' : '').'>'.$type.'</option>';
				$this->_html .= '</select>
				</div>
				<label>'.$this->l('Active').'</label>
				<div class="margin-form">
					<input type="radio" name="criterion_active" id="active_on" value="1" '.($criterion->active ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="criterion_active" id="active_off" value="0" '.(!$criterion->active ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<div class="margin-form">
					<input type="submit" name="submitAddCriterion" value="'.(Tools::getValue('editCriterion') ? $this->l('Modify this criterion') : $this->l('Add this criterion')).'" class="button" />
				</div>
				</form>';
				require_once(dirname(__FILE__).'/ProductCommentCriterion.php');
				$criterions = ProductCommentCriterion::getCriterions((int)$cookie->id_lang);
				if (sizeof($criterions))
				{
						 $this->_html.= '<br />
						 <table class="table">
						 <thead>
						  <tr>
							<th style="width:260px;">'.$this->l('Criterion').'</th>
							<th style="width:260px;">'.$this->l('Type').'</th>
							<th style="width:50px;">'.$this->l('Status').'</th>
							<th style="width:30px;">'.$this->l('Actions').'</th>
						  </tr>
						 </thead>
						 <tbody>';
				
						foreach ($criterions AS $criterion)
						{
							$this->_html .= '<tr>
							 <td>'.$criterion['name'].'</td>
							 <td>'.$this->_productCommentsCriterionTypes[(int)$criterion['id_product_comment_criterion_type']].'</td>
							 <td style="text-align:center;"><img src="../img/admin/'.($criterion['active'] ? 'enabled' : 'disabled').'.gif" /></td>
							 <td><a href="'.$this->_baseUrl.'&editCriterion='.(int)$criterion['id_product_comment_criterion'].'"><img src="../img/admin/edit.gif" alt="'.$this->l('Edit').'" /></a>
								  <a href="'.$this->_baseUrl.'&deleteCriterion='.(int)$criterion['id_product_comment_criterion'].'"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /></a></td><tr>';
						}
					$this->_html .= '</tbody></table>';
				}
			$this->_html .= '</fieldset><br />';
	}

	private function _displayFormApplicationCriterion()
	{
		global $cookie;

		include_once(dirname(__FILE__).'/ProductCommentCriterion.php');

		$criterions = ProductCommentCriterion::getCriterions((int)$cookie->id_lang, false, true);
		$id_criterion = (int)Tools::getValue('updateCriterion');
		
		if ($id_criterion)
		{
			$criterion = new ProductCommentCriterion((int)$id_criterion);
			if ($criterion->id_product_comment_criterion_type == 2)
			{
				$categories = Category::getSimpleCategories((int)$cookie->id_lang);
				$criterion_categories = $criterion->getCategories();
			}
			elseif ($criterion->id_product_comment_criterion_type == 3)
			{
				$criterion_products = $criterion->getProducts();
				$products = Product::getSimpleProducts((int)$cookie->id_lang);
			}
		}

		foreach ($criterions AS $key => $foo)
			if ($foo['id_product_comment_criterion_type'] == 1)
				unset($criterions[$key]);
		
		if (sizeof($criterions))
		{
			$this->_html .= '
			<fieldset class="width2">
				<legend><img src="'.$this->_path.'img/note_go.png" alt="" title="" />'.$this->l('Manage criterions scope').'</legend>
				<p style="margin-bottom: 15px;">'.$this->l('Only criterions restricted to categories or products can be configured below:').'</p>
				<form action="'.$this->_baseUrl.'" method="post" name="product_criterion_form">
					<label>'.$this->l('Criterion').'</label>
					<div class="margin-form">
						<select name="id_product_comment_criterion" id="id_product_comment_criterion" onchange="window.location=\''.$this->_baseUrl.'&updateCriterion=\'+$(\'#id_product_comment_criterion option:selected\').val()">
							<option value="--">-- '.$this->l('Choose a criterion').' --</option>';
						foreach ($criterions AS $foo)
								$this->_html .= '<option value="'.(int)($foo['id_product_comment_criterion']).'" '.($foo['id_product_comment_criterion'] == $id_criterion ? 'selected="selected"' : '').'>'.$foo['name'].'</option>';
			$this->_html .= '</select>
					</div>
				</form>';

			if ($id_criterion AND $criterion->id_product_comment_criterion_type != 1)
			{
				$this->_html .='<label for="id_product_comment_criterion">'.($criterion->id_product_comment_criterion_type == 3 ? $this->l('Products') : $this->l('Categories')).'</label>
					<form action="'.$this->_baseUrl.'" method="post" name="comment_form">
						<div id="product_criterions" class="margin-form">
							<input type="hidden" name="id_criterion" id="id_criterion" value="'.(int)$id_criterion.'" />
							 <br /><table class="table" border="0" cellspacing="0" cellpadding="0">
							 <thead>
							  <tr>
								<th><input class="noborder" type="checkbox" name="id_product[]" onclick="checkDelBoxes(this.form, \'id_product[]\', this.checked);" /></th>
								<th style="width: 30px;">'.$this->l('ID').'</th>
								<th style="width: 550px;">'.($criterion->id_product_comment_criterion_type == 3 ? $this->l('Product Name') : $this->l('Category Name')).'</th>
							  </tr>
							 </thead>
							 <tbody>';
				
				if ($criterion->id_product_comment_criterion_type == 3)	
					foreach ($products AS $product)
						$this->_html .='<tr><td><input class="noborder" type="checkbox" value="'.(int)$product['id_product'].'" name="id_product[]" '.(in_array($product['id_product'], $criterion_products) ? 'checked="checked"' : '').' /></td>
											<td>'.(int)$product['id_product'].'</td><td>'.$product['name'].'</td></tr>';
				elseif ($criterion->id_product_comment_criterion_type == 2)
					foreach ($categories AS $category)
						$this->_html .='<tr><td><input class="noborder" type="checkbox" value="'.(int)$category['id_category'].'" name="id_product[]" '.(in_array($category['id_category'], $criterion_categories) ? 'checked="checked"' : '').' /></td>
											<td>'.(int)$category['id_category'].'</td><td>'.$category['name'].'</td></tr>';
				$this->_html .='</tbody>
						 </table>
						</div>
						<div class="margin-form clear">
							<input type="submit" name="submitApplicationCriterion" value="'.$this->l('Save').'" class="button" />
						</div>
					</form>';
			}
			
			$this->_html .= '</fieldset>';
		}
	}
	
	public function hookProductTab($params)
    {
		global $smarty, $cookie;
		
		require_once(dirname(__FILE__).'/ProductComment.php');
		require_once(dirname(__FILE__).'/ProductCommentCriterion.php');
		
		$smarty->assign(array(
		'allow_guests' => (int)Configuration::get('PRODUCT_COMMENTS_ALLOW_GUESTS'),
		'comments' => ProductComment::getByProduct((int)($_GET['id_product'])),
		'criterions' => ProductCommentCriterion::getByProduct((int)($_GET['id_product']), (int)($cookie->id_lang)),
		'nbComments' => (int)(ProductComment::getCommentNumber((int)($_GET['id_product'])))));

		return ($this->display(__FILE__, '/tab.tpl'));
	}

	private function _frontOfficePostProcess()
	{
		global $smarty, $cookie, $errors;

		require_once(dirname(__FILE__).'/ProductComment.php');
		require_once(dirname(__FILE__).'/ProductCommentCriterion.php');
		
		$allow_guests = (int)Configuration::get('PRODUCT_COMMENTS_ALLOW_GUESTS');
		if (Tools::isSubmit('submitMessage') AND (empty($cookie->id_customer) === false OR ($cookie->id_guest AND $allow_guests)))
		{
			$id_guest = (!$id_customer = (int)$cookie->id_customer) ? (int)$cookie->id_guest : false;
			$customerComment = ProductComment::getByCustomer((int)(Tools::getValue('id_product')), (int)$cookie->id_customer, true, (int)$id_guest);
			
			if (!$customerComment OR ($customerComment AND (strtotime($customerComment['date_add']) + Configuration::get('PRODUCT_COMMENTS_MINIMAL_TIME')) < time()))
			{
				$customer_name = false;
				if ($id_guest AND (!$customer_name = Tools::getValue('customer_name')))
					$errors[] = $this->l('Please fill your name');
				if (!sizeof($errors) AND Tools::getValue('content'))
				{
					$comment = new ProductComment();
					$comment->content = strip_tags(Tools::getValue('content'));
					$comment->id_product = (int)$_GET['id_product'];
					$comment->id_customer = (int)$cookie->id_customer;
					$comment->id_guest = (int)$id_guest;
					$comment->customer_name = pSQL($customer_name);
					$comment->title = pSQL(Tools::getValue('title'));
					$comment->grade = 0;
					$comment->validate = 0;

					if (!$comment->content)
						$errors[] = $this->l('Invalid comment text posted.');
					else
					{
						$comment->save();
						for ($i = 1, $grade = 0; isset($_POST[$i.'_grade']) === true; ++$i)
						{
							$cgrade = (int)Tools::getValue($i.'_grade');
							$grade += $cgrade;
							$productCommentCriterion = new ProductCommentCriterion((int)Tools::getValue('id_product_comment_criterion_'.$i));
							if ($productCommentCriterion->id)
								$productCommentCriterion->addGrade($comment->id, $cgrade);
						}
						if (($i - 1) > 0)
							$comment->grade = ($grade / ($i - 1));
						if (!$comment->save())
							$errors[] = $this->l('An error occurred while saving your comment.');
						else
							$smarty->assign('confirmation', $this->l('Comment posted.').((int)(Configuration::get('PRODUCT_COMMENTS_MODERATE')) ? ' '.$this->l('Awaiting moderator validation.') : ''));
					}
				}
				else
					$errors[] = $this->l('Comment text is required.');
			}
			else 
				$errors[] = $this->l('You should wait').' '.Configuration::get('PRODUCT_COMMENTS_MINIMAL_TIME').' '.$this->l('seconds before posting a new comment');
		}
	}

    public function hookProductTabContent($params)
    {
		global $smarty, $cookie;

		$id_guest = (!$id_customer = (int)$cookie->id_customer) ? (int)$cookie->id_guest : false;
		$customerComment = ProductComment::getByCustomer((int)(Tools::getValue('id_product')), (int)$cookie->id_customer, true, (int)$id_guest);
		
		$averages = ProductComment::getAveragesByProduct((int)Tools::getValue('id_product'), (int)$cookie->id_lang);
		$averageTotal = 0;
		foreach ($averages AS $average)
			$averageTotal += (float)($average);
		$averageTotal = count($averages) ? ($averageTotal / count($averages)) : 0;
		
		$smarty->assign(array(
		'logged' => (int)$cookie->id_customer,
		'action_url' => '',
		'comments' => ProductComment::getByProduct((int)Tools::getValue('id_product')),
		'criterions' => ProductCommentCriterion::getByProduct((int)Tools::getValue('id_product'), (int)$cookie->id_lang),
		'averages' => $averages,
		'product_comment_path' => $this->_path,
		'averageTotal' => $averageTotal,
		'allow_guests' => (int)Configuration::get('PRODUCT_COMMENTS_ALLOW_GUESTS'),
		'too_early' => ($customerComment AND (strtotime($customerComment['date_add']) + Configuration::get('PRODUCT_COMMENTS_MINIMAL_TIME')) > time()),
		'delay' => Configuration::get('PRODUCT_COMMENTS_MINIMAL_TIME')));

		$controller = new FrontController();
		$controller->pagination((int)ProductComment::getCommentNumber((int)Tools::getValue('id_product')));
		
		return ($this->display(__FILE__, '/productcomments.tpl'));
	}

	public function hookHeader()
	{
		$this->_frontOfficePostProcess();
	}
	
	public function hookExtraProductComparison($params)
	{
		global $smarty, $cookie;
	
		$list_grades = array();
		$list_product_grades = array();
		$list_product_average = array();
		$list_product_comment = array();
		
		foreach ($params['list_ids_product'] AS $id_product)
		{
			$grades = ProductComment::getAveragesByProduct((int)$id_product, (int)$cookie->id_lang);
			$criterions = ProductCommentCriterion::getByProduct((int)$id_product, (int)$cookie->id_lang);
			$grade_total = 0;
			if (sizeof($grades) > 0)
			{
				foreach ($criterions AS $criterion)
				{					
					$list_product_grades[$criterion['id_product_comment_criterion']][$id_product] = $grades[$criterion['id_product_comment_criterion']];
					$grade_total += (float)($grades[$criterion['id_product_comment_criterion']]);
					
					if (!array_key_exists($criterion['id_product_comment_criterion'], $list_grades))
						$list_grades[$criterion['id_product_comment_criterion']] = $criterion['name'];
				}
				
				$list_product_average[$id_product] = $grade_total / sizeof($criterion);
				$list_product_comment[$id_product] = ProductComment::getByProduct($id_product, 0, 3);
			}
		}				
		
		if (sizeof($list_grades) < 1) 
			return false;
			
		$smarty->assign(array('grades' => $list_grades,	'product_grades' => $list_product_grades, 'list_ids_product' => $params['list_ids_product'], 
		'list_product_average' => $list_product_average, 'product_comments' => $list_product_comment));
		
		return $this->display(__FILE__,'/products-comparison.tpl');
	}
}
