<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
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
		$this->version = '2.3';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);

		parent::__construct();

		$this->displayName = $this->l('Product Comments');
		$this->description = $this->l('Allows users to post reviews.');
	}

	public function install()
	{
		if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return false;
		else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return false;
		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
		$sql = preg_split("/;\s*[\r\n]+/", trim($sql));

		foreach ($sql as $query)
			if (!Db::getInstance()->execute(trim($query)))
				return false;
		if (parent::install() == false ||
			!$this->registerHook('productTab') ||
			!$this->registerHook('extraProductComparison') ||
			!$this->registerHook('productTabContent') ||
			!$this->registerHook('header') ||
			!$this->registerHook('productOutOfStock') ||
			!Configuration::updateValue('PRODUCT_COMMENTS_MINIMAL_TIME', 30) ||
			!Configuration::updateValue('PRODUCT_COMMENTS_ALLOW_GUESTS', 0) ||
			!Configuration::updateValue('PRODUCT_COMMENTS_MODERATE', 1))
				return false;
		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall() || !$this->deleteTables() ||
			!Configuration::deleteByName('PRODUCT_COMMENTS_MODERATE') ||
			!Configuration::deleteByName('PRODUCT_COMMENTS_ALLOW_GUESTS') ||
			!Configuration::deleteByName('PRODUCT_COMMENTS_MINIMAL_TIME') ||
			!$this->unregisterHook('extraProductComparison') ||
			!$this->unregisterHook('productOutOfStock') ||
			!$this->unregisterHook('productTabContent') ||
			!$this->unregisterHook('header') ||
			!$this->unregisterHook('productTab'))
				return false;
		return true;
	}

	public function deleteTables()
	{
		return Db::getInstance()->execute('
			DROP TABLE IF EXISTS
			`'._DB_PREFIX_.'product_comment`,
			`'._DB_PREFIX_.'product_comment_criterion`,
			`'._DB_PREFIX_.'product_comment_criterion_product`,
			`'._DB_PREFIX_.'product_comment_criterion_lang`,
			`'._DB_PREFIX_.'product_comment_criterion_category`,
			`'._DB_PREFIX_.'product_comment_grade`,
			`'._DB_PREFIX_.'product_comment_usefulness`,
			`'._DB_PREFIX_.'product_comment_report`');
	}

	protected function _postProcess()
	{
		if (Tools::isSubmit('submitModerate'))
		{
			Configuration::updateValue('PRODUCT_COMMENTS_MODERATE', (int)Tools::getValue('moderate'));
			Configuration::updateValue('PRODUCT_COMMENTS_ALLOW_GUESTS', (int)Tools::getValue('allow_guest'));
			Configuration::updateValue('PRODUCT_COMMENTS_MINIMAL_TIME', (int)Tools::getValue('product_comments_minimal_time'));
			$this->_html .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
		}
		if ($id_criterion = (int)Tools::getValue('deleteCriterion'))
		{
			$productCommentCriterion = new ProductCommentCriterion((int)$id_criterion);
			if ($productCommentCriterion->id)
				if ($productCommentCriterion->delete())
					$this->_html .= '<div class="conf confirm">'.$this->l('Criterion deleted').'</div>';
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
		$this->_checkReportedComment();
		$this->_checkCriterion();
		$this->_updateApplicationCriterion();
		$this->_checkDeleteComment();

		return $this->_html.$this->_displayForm();
	}

	private function _setBaseUrl()
	{
		$this->_baseUrl = 'index.php?';
		foreach ($_GET as $k => $value)
			if (!in_array($k, array('deleteCriterion', 'editCriterion')))
				$this->_baseUrl .= $k.'='.$value.'&';
		$this->_baseUrl = rtrim($this->_baseUrl, '&');
	}

	private function _checkModerateComment()
	{
		$action = Tools::getValue('action');
		if (empty($action) === false && (int)Configuration::get('PRODUCT_COMMENTS_MODERATE'))
		{
			$product_comments = Tools::getValue('id_product_comment');

			if (count($product_comments))
			{
				require_once(dirname(__FILE__).'/ProductComment.php');
				switch ($action)
				{
					case 'accept':
						foreach ($product_comments as $id_product_comment)
						{
							if (!$id_product_comment)
								continue;
							$comment = new ProductComment((int)$id_product_comment);
							$comment->validate();
						}
						break;

					case 'delete':
						foreach ($product_comments as $id_product_comment)
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

	private function _checkReportedComment()
	{
		$action = Tools::getValue('action');
		if (empty($action) === false)
		{
			$product_comments = Tools::getValue('id_product_comment');

			if (count($product_comments))
			{
				require_once(dirname(__FILE__).'/ProductComment.php');
				switch ($action)
				{
					case 'accept':
						foreach ($product_comments as $id_product_comment)
						{
							if (!$id_product_comment)
								continue;
							$comment = new ProductComment((int)$id_product_comment);
							$comment->validate();
							ProductComment::deleteReports((int)$id_product_comment);
						}
						break;
					case 'delete':
						foreach ($product_comments as $id_product_comment)
						{
							if (!$id_product_comment)
								continue;
							$comment = new ProductComment((int)$id_product_comment);
							$comment->delete();
							ProductComment::deleteGrades((int)$id_product_comment);
							ProductComment::deleteReports((int)$id_product_comment);
							ProductComment::deleteUsefulness((int)$id_product_comment);
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
			require_once(dirname(__FILE__).'/ProductCommentCriterion.php');
			$languages = Language::getLanguages();
			$id_criterion = (int)Tools::getValue('id_product_comment_criterion');
			$productCommentCriterion = new ProductCommentCriterion((int)$id_criterion);
			foreach ($languages as $lang)
				$productCommentCriterion->name[(int)$lang['id_lang']] = Tools::getValue('criterion_'.(int)$lang['id_lang']);

			// Check default language criterion name
			$defaultLanguage = new Language((int)(Configuration::get('PS_LANG_DEFAULT')));
			if (!Tools::getValue('criterion_'.$defaultLanguage->id))
			{
				$this->_html .= '<div class="error"><img src="../img/admin/error2.png" />'.$this->l('The field <b>Name</b> is required at least in').' '.$defaultLanguage->name.'</div>';
				return;
			}

			$productCommentCriterion->id_product_comment_criterion_type = (int)Tools::getValue('criterion_type');
			$productCommentCriterion->active = (int)Tools::getValue('criterion_active');

			if ($productCommentCriterion->save())
				$this->_html .= '<div class="conf confirm">'.(Tools::getValue('editCriterion') ? $this->l('Criterion updated') : $this->l('Criterion added')).'</div>';
		}
		else if (!empty($action_criterion) && empty($name))
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
						if (count($categories))
							foreach ($categories as $id_category)
								$productCommentCriterion->addCategory((int)$id_category);
				}
				else if ($productCommentCriterion->id_product_comment_criterion_type == 3)
				{
					$productCommentCriterion->deleteProducts();
					if ($products = Tools::getValue('id_product'))
						if (count($products))
							foreach ($products as $product)
								$productCommentCriterion->addProduct((int)$product);
				}
			}

			$this->_html .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
		}
	}

	private function _displayForm()
	{
		$this->_displayFormModerate();
		$this->_displayFormReported();
		$this->_displayFormConfigurationCriterion();
		$this->_displayFormApplicationCriterion();
		$this->_displayFormDelete();

		return $this->_html;
	}

	private function _displayFormModerate()
	{
		$this->_html = '<script type="text/javascript" src="'.$this->_path.'js/moderate.js"></script>
			<fieldset class="width2">
				<legend><img src="../img/admin/cog.gif" alt="" title="" />'.$this->l('Configuration').'</legend>
				<form action="'.Tools::safeOutput($this->_baseUrl).'" method="post" name="comment_configuration">
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
				if (count($comments))
				{
					$this->_html .= '
					<form action="'.Tools::safeOutput($this->_baseUrl).'" method="post" name="comment_form">
					<input type="hidden" name="id_product_comment[]" id="id_product_comment" />
					<input type="hidden" name="action" id="action" />
					<br /><table class="table" border="0" cellspacing="0" cellpadding="0">
					<thead>
					<tr>
						<th><input class="noborder" type="checkbox" onclick="checkDelBoxes(this.form, \'id_product_comment[]\', this.checked)" /></th>
						<th style="width:150px;">'.$this->l('Author').'</th>
						<th style="width:550px;">'.$this->l('Comment').'</th>
						<th style="width:150px;">'.$this->l('Product name').'</th>
						<th style="width:30px;">'.$this->l('Actions').'</th>
					</tr>
					</thead>
					<tbody>';
					foreach ($comments as $comment)
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

	private function _displayFormReported()
	{
		$this->_html .= '<fieldset class="width2">
				<legend><img src="'.$this->_path.'img/comments_delete.png" alt="" title="" />'.$this->l('Reported Comments').'</legend>';

				require_once(dirname(__FILE__).'/ProductComment.php');
				$comments = ProductComment::getReportedComments();
				if (count($comments))
				{
					$this->_html .= '
					<form action="'.Tools::safeOutput($this->_baseUrl).'" method="post" name="comment_form">
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
					foreach ($comments as $comment)
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
					$this->_html .= $this->l('No reported comments at this time.');
		$this->_html .= '</fieldset><br />';
	}

	private function _displayFormConfigurationCriterion()
	{
		$langs = Language::getLanguages(false);
		$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');

		$id_criterion = (int)Tools::getValue('editCriterion');
		$criterion = new ProductCommentCriterion((int)$id_criterion);
		$languageIds = 'criterion';
		$this->_html .= '
		<fieldset class="width2">
			<legend><img src="'.Tools::safeOutput($this->_path).'img/note.png" alt="" />'.$this->l('Add a new comment criterion').'</legend>
				<p style="margin-bottom: 20px;">'.$this->l('You can define several criterions to help your customers during their review. For instance: efficiency, lightness, design.').'<br />
				<br />'.$this->l('You can add a new criterion below:').'</p>
				<form action="'.Tools::safeOutput($this->_baseUrl).'" method="post" name="criterion_form">
				<label>'.$this->l('Name').'</label>
				<div class="margin-form">
				<input type="hidden" name="id_product_comment_criterion" value="'.(int)$criterion->id.'" />';
				foreach ($langs as $lang)
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
				foreach ($this->_productCommentsCriterionTypes as $k => $type)
					$this->_html .= '<option value="'.(int)$k.'" '.($k == $criterion->id_product_comment_criterion_type ? 'selected="selected"' : '').'>'.$type.'</option>';
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
				$criterions = ProductCommentCriterion::getCriterions($this->context->language->id);
				if (count($criterions))
				{
						$this->_html .= '<br />
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

						foreach ($criterions as $criterion)
						{
							$this->_html .= '<tr>
							<td>'.$criterion['name'].'</td>
							<td>'.$this->_productCommentsCriterionTypes[(int)$criterion['id_product_comment_criterion_type']].'</td>
							<td style="text-align:center;"><img src="../img/admin/'.($criterion['active'] ? 'enabled' : 'disabled').'.gif" /></td>
							<td><a href="'.Tools::safeOutput($this->_baseUrl).'&editCriterion='.(int)$criterion['id_product_comment_criterion'].'"><img src="../img/admin/edit.gif" alt="'.$this->l('Edit').'" /></a>
								<a href="'.Tools::safeOutput($this->_baseUrl).'&deleteCriterion='.(int)$criterion['id_product_comment_criterion'].'"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /></a></td><tr>';
						}
					$this->_html .= '</tbody></table>';
				}
			$this->_html .= '</fieldset><br />';
	}

	private function _displayFormApplicationCriterion()
	{
		include_once(dirname(__FILE__).'/ProductCommentCriterion.php');

		$criterions = ProductCommentCriterion::getCriterions($this->context->language->id, false, true);
		$id_criterion = (int)Tools::getValue('updateCriterion');

		if ($id_criterion)
		{
			$criterion = new ProductCommentCriterion((int)$id_criterion);
			if ($criterion->id_product_comment_criterion_type == 2)
			{
				$categories = Category::getSimpleCategories($this->context->language->id);
				$criterion_categories = $criterion->getCategories();
			}
			else if ($criterion->id_product_comment_criterion_type == 3)
			{
				$criterion_products = $criterion->getProducts();
				$products = Product::getSimpleProducts($this->context->language->id);
			}
		}

		foreach ($criterions as $key => $foo)
			if ($foo['id_product_comment_criterion_type'] == 1)
				unset($criterions[$key]);

		if (count($criterions))
		{
			$this->_html .= '
			<fieldset class="width2">
				<legend><img src="'.$this->_path.'img/note_go.png" alt="" title="" />'.$this->l('Manage criterions scope').'</legend>
				<p style="margin-bottom: 15px;">'.$this->l('Only criterions restricted to categories or products can be configured below:').'</p>
				<form action="'.Tools::safeOutput($this->_baseUrl).'" method="post" name="product_criterion_form">
					<label>'.$this->l('Criterion').'</label>
					<div class="margin-form">
">
						<select name="id_product_comment_criterion" id="id_product_comment_criterion" onchange="window.location=\''.Tools::safeOutput($this->_baseUrl).'&updateCriterion=\'+$(\'#id_product_comment_criterion option:selected\').val()">
							<option value="--">-- '.$this->l('Choose a criterion').' --</option>';
						foreach ($criterions as $foo)
								$this->_html .= '<option value="'.(int)($foo['id_product_comment_criterion']).'" '.($foo['id_product_comment_criterion'] == $id_criterion ? 'selected="selected"' : '').'>'.$foo['name'].'</option>';
			$this->_html .= '</select>
					</div>
				</form>';

			if ($id_criterion && $criterion->id_product_comment_criterion_type != 1)
			{
				$this->_html .='<label for="id_product_comment_criterion">'.($criterion->id_product_comment_criterion_type == 3 ? $this->l('Products') : $this->l('Categories')).'</label>
					<form action="'.Tools::safeOutput($this->_baseUrl).'" method="post" name="comment_form">
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
					foreach ($products as $product)
						$this->_html .='<tr><td><input class="noborder" type="checkbox" value="'.(int)$product['id_product'].'" name="id_product[]" '.(in_array($product['id_product'], $criterion_products) ? 'checked="checked"' : '').' /></td>
											<td>'.(int)$product['id_product'].'</td><td>'.$product['name'].'</td></tr>';
				else if ($criterion->id_product_comment_criterion_type == 2)
					foreach ($categories as $category)
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

	private function _displayFormDelete()
	{
		$this->_html .= '
			<fieldset class="width2">
				<legend><img src="'.$this->_path.'img/comments_delete.png" alt="" title="" />'.$this->l('Manage Comments').'</legend>';

				require_once(dirname(__FILE__).'/ProductComment.php');
				$comments = ProductComment::getAll();
				if (count($comments))
				{
					$this->_html .= '
					<form action="'.Tools::safeOutput($this->_baseUrl).'" method="post" name="delete_comment_form">
					<input type="hidden" name="delete_id_product_comment[]" id="delete_id_product_comment" />
					<input type="hidden" name="delete_action" id="delete_action" />
					<br /><table class="table" border="0" cellspacing="0" cellpadding="0">
					<thead>
					<tr>
						<th><input class="noborder" type="checkbox" name="delete_id_product_comment[]" onclick="checkDelBoxes(this.form, \'delete_id_product_comment[]\', this.checked)" /></th>
						<th style="width:150px;">'.$this->l('Author').'</th>
						<th style="width:550px;">'.$this->l('Comment').'</th>
						<th style="width:30px;">'.$this->l('Actions').'</th>
					</tr>
					</thead>
					<tbody>';
					foreach ($comments as $comment)
						$this->_html .= '<tr>
						<td><input class="noborder" type="checkbox" value="'.$comment['id_product_comment'].'" name="delete_id_product_comment[]" /></td>
						<td>'.htmlspecialchars($comment['customer_name'], ENT_COMPAT, 'UTF-8').'.</td>
						<td>'.htmlspecialchars($comment['content'], ENT_COMPAT, 'UTF-8').'</td>
						<td><a href="javascript:;" onclick="delComment(\''.(int)($comment['id_product_comment']).'\',\''.$this->l('Are you sure?').'\');"><img src="'.$this->_path.'img/delete.png" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a></td>
						</tr>';
						$this->_html .= '
						<tr>
							<td colspan="3" style="font-weight:bold;text-align:right">'.$this->l('Selection:').'</td>
							<td><a href="javascript:;" onclick="delComment(0);"><img src="'.$this->_path.'img/delete.png" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a></td>
						</tr>
						</tbody>
					</table>
					</form>';
				}
				else
					$this->_html .= $this->l('No comments to manage at this time.');

		$this->_html .= '</fieldset><br />';
	}

	private function _checkDeleteComment()
	{
		$action = Tools::getValue('delete_action');
		if (empty($action) === false)
		{
			$product_comments = Tools::getValue('delete_id_product_comment');

			if (count($product_comments))
			{
				require_once(dirname(__FILE__).'/ProductComment.php');
				if ($action == 'delete')
				{
						foreach ($product_comments as $id_product_comment)
						{
							if (!$id_product_comment)
								continue;
							$comment = new ProductComment((int)$id_product_comment);
							$comment->delete();
							ProductComment::deleteGrades((int)$id_product_comment);
						}
				}
			}
		}
	}

	public function hookProductTab($params)
	{
    	require_once(dirname(__FILE__).'/ProductComment.php');
		require_once(dirname(__FILE__).'/ProductCommentCriterion.php');

		$this->context->smarty->assign(array(
			'allow_guests' => (int)Configuration::get('PRODUCT_COMMENTS_ALLOW_GUESTS'),
			'comments' => ProductComment::getByProduct((int)(Tools::getValue('id_product'))),
			'criterions' => ProductCommentCriterion::getByProduct((int)(Tools::getValue('id_product')), $this->context->language->id),
			'nbComments' => (int)(ProductComment::getCommentNumber((int)(Tools::getValue('id_product'))))
		));

		return ($this->display(__FILE__, '/tab.tpl'));
	}

	public function hookproductOutOfStock($params)
	{
		require_once(dirname(__FILE__).'/ProductComment.php');
		require_once(dirname(__FILE__).'/ProductCommentCriterion.php');

		$id_guest = (!$id_customer = (int)$this->context->cookie->id_customer) ? (int)$this->context->cookie->id_guest : false;
		$customerComment = ProductComment::getByCustomer((int)(Tools::getValue('id_product')), (int)$this->context->cookie->id_customer, true, (int)$id_guest);

		$average = ProductComment::getAverageGrade((int)Tools::getValue('id_product'));

		$image = Product::getCover((int)Tools::getValue('id_product'));

		$this->context->smarty->assign(array(
			'id_product_comment_form' => (int)Tools::getValue('id_product'),
			'product' => new Product((int)Tools::getValue('id_product'), false, $this->context->language->id),
			'secure_key' => $this->secure_key,
			'logged' => (int)$this->context->customer->isLogged(true),
			'allow_guests' => (int)Configuration::get('PRODUCT_COMMENTS_ALLOW_GUESTS'),
			'productcomment_cover' => (int)Tools::getValue('id_product').'-'.(int)$image['id_image'],
			'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
			'criterions' => ProductCommentCriterion::getByProduct((int)Tools::getValue('id_product'), $this->context->language->id),
			'action_url' => '',
			'averageTotal' => (int)$average['grade'],
			'too_early' => ($customerComment && (strtotime($customerComment['date_add']) + Configuration::get('PRODUCT_COMMENTS_MINIMAL_TIME')) > time()),
			'nbComments' => (int)(ProductComment::getCommentNumber((int)Tools::getValue('id_product')))
		));

		return ($this->display(__FILE__, '/productcomments-extra.tpl'));
	}

    public function hookProductTabContent($params)
    {
		$this->context->controller->addJS($this->_path.'js/jquery.rating.pack.js');
		$this->context->controller->addJS($this->_path.'js/jquery.textareaCounter.plugin.js');
		$this->context->controller->addJS($this->_path.'js/productcomments.js');

		$id_guest = (!$id_customer = (int)$this->context->cookie->id_customer) ? (int)$this->context->cookie->id_guest : false;
		$customerComment = ProductComment::getByCustomer((int)(Tools::getValue('id_product')), (int)$this->context->cookie->id_customer, true, (int)$id_guest);

		$averages = ProductComment::getAveragesByProduct((int)Tools::getValue('id_product'), $this->context->language->id);
		$averageTotal = 0;
		foreach ($averages as $average)
			$averageTotal += (float)($average);
		$averageTotal = count($averages) ? ($averageTotal / count($averages)) : 0;

		$image = Product::getCover((int)Tools::getValue('id_product'));

		$this->context->smarty->assign(array(
			'logged' => (int)$this->context->customer->isLogged(true),
			'action_url' => '',
			'comments' => ProductComment::getByProduct((int)Tools::getValue('id_product'), 1, null, $this->context->cookie->id_customer),
			'criterions' => ProductCommentCriterion::getByProduct((int)Tools::getValue('id_product'), $this->context->language->id),
			'averages' => $averages,
			'product_comment_path' => $this->_path,
			'averageTotal' => $averageTotal,
			'allow_guests' => (int)Configuration::get('PRODUCT_COMMENTS_ALLOW_GUESTS'),
			'too_early' => ($customerComment && (strtotime($customerComment['date_add']) + Configuration::get('PRODUCT_COMMENTS_MINIMAL_TIME')) > time()),
			'delay' => Configuration::get('PRODUCT_COMMENTS_MINIMAL_TIME'),
			'id_product_comment_form' => (int)Tools::getValue('id_product'),
			'secure_key' => $this->secure_key,
			'productcomment_cover' => (int)Tools::getValue('id_product').'-'.(int)$image['id_image'],
			'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
			'nbComments' => (int)ProductComment::getCommentNumber((int)Tools::getValue('id_product')),
			'productcomments_controller_url' => $this->context->link->getModuleLink('productcomments'),
			'productcomments_url_rewriting_activated' => Configuration::get('PS_REWRITING_SETTINGS', 0)
		));

		$this->context->controller->pagination((int)ProductComment::getCommentNumber((int)Tools::getValue('id_product')));

		return ($this->display(__FILE__, '/productcomments.tpl'));
	}

	public function hookHeader()
	{
		$this->context->controller->addCSS($this->_path.'productcomments.css', 'all');
	}

	public function hookExtraProductComparison($params)
	{
		require_once(dirname(__FILE__).'/ProductComment.php');
		require_once(dirname(__FILE__).'/ProductCommentCriterion.php');

		$list_grades = array();
		$list_product_grades = array();
		$list_product_average = array();
		$list_product_comment = array();

		foreach ($params['list_ids_product'] as $id_product)
		{
			$grades = ProductComment::getAveragesByProduct($id_product, $this->context->language->id);
			$criterions = ProductCommentCriterion::getByProduct($id_product, $this->context->language->id);
			$grade_total = 0;
			if (count($grades) > 0)
			{
				foreach ($criterions as $criterion)
				{
					if (isset($grades[$criterion['id_product_comment_criterion']]))
					{
					$list_product_grades[$criterion['id_product_comment_criterion']][$id_product] = $grades[$criterion['id_product_comment_criterion']];
					$grade_total += (float)($grades[$criterion['id_product_comment_criterion']]);
					}
					else
						$list_product_grades[$criterion['id_product_comment_criterion']][$id_product] = 0;

					if (!array_key_exists($criterion['id_product_comment_criterion'], $list_grades))
						$list_grades[$criterion['id_product_comment_criterion']] = $criterion['name'];
				}

				$list_product_average[$id_product] = $grade_total / count($criterions);
				$list_product_comment[$id_product] = ProductComment::getByProduct($id_product, 0, 3);
			}
		}

		if (count($list_grades) < 1)
			return false;

		$this->context->smarty->assign(array('grades' => $list_grades,	'product_grades' => $list_product_grades, 'list_ids_product' => $params['list_ids_product'],
		'list_product_average' => $list_product_average, 'product_comments' => $list_product_comment));

		return $this->display(__FILE__, '/products-comparison.tpl');
	}
}
