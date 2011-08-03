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
*  @version  Release: $Revision: 7445 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminMeta extends AdminTab
{
	public $table = 'meta';
	public $className = 'Meta';
	public $lang = true;
	public $edit = true;
	public $delete = true;

	public function __construct()
	{
		parent::__construct();

		$this->fieldsDisplay = array(
			'id_meta' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'page' => array('title' => $this->l('Page'), 'width' => 120),
			'title' => array('title' => $this->l('Title'), 'width' => 120),
			'url_rewrite' => array('title' => $this->l('Friendly URL'), 'width' => 120)
		);
		$this->_group = 'GROUP BY a.id_meta';
		
		$this->optionTitle = $this->l('URLs Setup');
		$this->_fieldsOptions = array(
			'PS_REWRITING_SETTINGS' => array('title' => $this->l('Friendly URL'), 'desc' => $this->l('Enable only if your server allows URL rewriting (recommended)').'<p class="hint clear" style="display: block;">'.$this->l('If you turn on this feature, you must').' <a href="?tab=AdminGenerator&token='.Tools::getAdminToken('AdminGenerator'.(int)(Tab::getIdFromClassName('AdminGenerator')).(int)$this->context->employee->id).'">'.$this->l('generate a .htaccess file').'</a></p><div class="clear"></div>', 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
			'PS_CANONICAL_REDIRECT' => array('title' => $this->l('Automatically redirect to Canonical url'), 'desc' => $this->l('Recommended but your theme must be compliant'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
		);
		
		// Display route options only if friendly URL is activated
		$this->_fieldsRoutes = array();
		if (Configuration::get('PS_REWRITING_SETTINGS'))
		{
			$this->addFieldRoute('product_rule', $this->l('Route to products'));
			$this->addFieldRoute('category_rule', $this->l('Route to category'));
			$this->addFieldRoute('supplier_rule', $this->l('Route to supplier'));
			$this->addFieldRoute('manufacturer_rule', $this->l('Route to manufacturer'));
			$this->addFieldRoute('cms_rule', $this->l('Route to CMS page'));
			$this->addFieldRoute('cms_category_rule', $this->l('Route to CMS category'));
		}
	}
	
	public function addFieldRoute($routeID, $title)
	{
		$keywords = array();
		foreach (Dispatcher::getInstance()->defaultRoutes[$routeID]['keywords'] as $keyword => $data)
			$keywords[] = ((isset($data['param'])) ? '<span class="red">'.$keyword.'*</span>' : $keyword);

		$this->_fieldsRoutes['PS_ROUTE_'.$routeID] = array(
			'title' =>	$title,
			'desc' => sprintf($this->l('Keywords: %s'), implode(', ', $keywords)),
			'validation' => 'isString',
			'type' => 'text',
			'size' => 70,
			'defaultValue' => Dispatcher::getInstance()->defaultRoutes[$routeID]['rule'],
		);
	}
	
	protected function updateOptions($token)
	{
		if ($this->tabAccess['edit'] === '1')
		{
			$this->submitConfiguration($this->_fieldsRoutes);
			parent::updateOptions($token);
		}
		else
			$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
	}

	public function displayOptionsList($fieldsOptions = null, $optionTitle = null, $optionDescription = null)
	{
		// Desactivate <form>, we want to add our own tags
		$this->formOptions = false;
		
		$tab = Tab::getTab($this->context->language->id, $this->id);
		echo '<form action="'.self::$currentIndex.'" id="'.$tab['name'].'" name="'.$tab['name'].'" method="post">';
		parent::displayOptionsList();
		if ($this->_fieldsRoutes)
		{
			$desc = $this->l('You can change here the pattern of your links. There are some available keywords for each route listed below, keywords with * are required. To add a keyword in URL use {keyword} syntax. You can add some text before or after the keyword IF the keyword is not empty with syntax {prepend:keyword:append}, for example {-hey-:meta_title} will add "-hey-my-title" in URL if meta title is set, or nothing. Friendly URL and rewriting Apache option must be activated on your web server to use this functionality.');
			parent::displayOptionsList($this->_fieldsRoutes, $this->l('Schema of URLs'), $desc);
		}
		echo '</form>';
	}
	
	public function displayForm($isMainTab = true)
	{
		parent::displayForm();
		
		if (!($meta = $this->loadObject(true)))
			return;
		$files = Meta::getPages(true, ($meta->page ? $meta->page : false));
		echo '
		<form action="'.self::$currentIndex.'&token='.$this->token.'&submitAdd'.$this->table.'=1" method="post">
		'.($meta->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$meta->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/metatags.gif" />'.$this->l('Meta-Tags').'</legend>
				<label>'.$this->l('Page:').' </label>
				<div class="margin-form">';
				if (!sizeof($files))
					echo '<p>'.$this->l('There is no page available!').'</p>';
				else
				{
					echo '
					<select name="page">';
					foreach ($files as $file)
					{
						echo '<option value="'.$file.'"';
						echo $meta->page == $file? ' selected="selected"' : '' ;
						echo'>'.$file.'</option>';
					}
					echo '
					</select><sup> *</sup>
					<p class="clear">'.$this->l('Name of the related page').'</p>';
				}
				echo '
				</div>
				<label>'.$this->l('Page\'s title:').' </label>
				<div class="margin-form">';
				foreach ($this->_languages as $language)
					echo '
					<div id="title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="title_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($meta, 'title', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
						<p class="clear">'.$this->l('Title of this page').'</p>
					</div>';
				$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'title¤description¤keywords¤url_rewrite', 'title');
		echo '	</div>
				<div style="clear:both;">&nbsp;</div>
				<label>'.$this->l('Meta description:').' </label>
				<div class="margin-form">';
				foreach ($this->_languages as $language)
					echo '
					<div id="description_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="50" type="text" name="description_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($meta, 'description', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
						<p class="clear">'.$this->l('A short description').'</p>
					</div>';
				$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'title¤description¤keywords¤url_rewrite', 'description');
		echo '	</div>
				<div style="clear:both;">&nbsp;</div>
				<label>'.$this->l('Meta keywords:').' </label>
				<div class="margin-form">';
				foreach ($this->_languages as $language)
					echo '
					<div id="keywords_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="50" type="text" name="keywords_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($meta, 'keywords', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
						<p class="clear">'.$this->l('List of keywords').'</p>
					</div>';
				$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'title¤description¤keywords¤url_rewrite', 'keywords');
		echo '	</div>
				<div style="clear:both;">&nbsp;</div>
				<label>'.$this->l('Rewritten URL:').' </label>
				<div class="margin-form">';
				foreach ($this->_languages as $language)
					echo '
					<div id="url_rewrite_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input style="width:300px" type="text" name="url_rewrite_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($meta, 'url_rewrite', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
						<p class="clear" style="width:300px">'.$this->l('Example : "contacts" for http://mysite.com/shop/contacts to redirect to http://mysite.com/shop/contact-form.php').'</p>
					</div>';
				$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'title¤description¤keywords¤url_rewrite', 'url_rewrite');
		echo '	</div>
				<div style="clear:both;">&nbsp;</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
	
	public function postProcess()
	{
		if (Tools::isSubmit('submitAddmeta'))
		{
			$langs = Language::getLanguages(true);
			$default_language = Configuration::get('PS_LANG_DEFAULT');
			if (Tools::getValue('page') != 'index')
			{
				$defaultLangIsValidated = Validate::isLinkRewrite(Tools::getValue('url_rewrite_'.$default_language));
				$englishLangIsValidated = Validate::isLinkRewrite(Tools::getValue('url_rewrite_1'));
			}
			else
			{	// index.php can have empty rewrite rule
				$defaultLangIsValidated = !Tools::getValue('url_rewrite_'.$default_language) OR Validate::isLinkRewrite(Tools::getValue('url_rewrite_'.$default_language));
				$englishLangIsValidated = !Tools::getValue('url_rewrite_1') OR Validate::isLinkRewrite(Tools::getValue('url_rewrite_1'));
			}
			if (!$defaultLangIsValidated AND !$englishLangIsValidated)
			{
				$this->_errors[] = Tools::displayError('Url rewrite field must be filled at least in default or english language.');
				return false;
			}
			foreach ($langs as $lang)
			{
				$current = Tools::getValue('url_rewrite_'.$lang['id_lang']);
				if (strlen($current) == 0)
					// Prioritize default language first
					if ($defaultLangIsValidated)
						$_POST['url_rewrite_'.$lang['id_lang']] = Tools::getValue('url_rewrite_'.$default_language);
					else
						$_POST['url_rewrite_'.$lang['id_lang']] = Tools::getValue('url_rewrite_1');
			}
		}
		if (Tools::isSubmit('submitOptions'.$this->table) OR Tools::isSubmit('submitAddmeta'))
			Module::hookExec('afterSaveAdminMeta');
		
		return parent::postProcess();
	}
	
	public function getList($id_lang, $orderBy = NULL, $orderWay = NULL, $start = 0, $limit = NULL, $id_lang_shop = false)
	{
		parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, Context::getContext()->shop->getID());
	}
	
	/**
	 * Validate route syntax and save it in configuration
	 * 
	 * @param string $routeID
	 */
	public function checkAndUpdateRoute($routeID)
	{
		$defaultRoutes = Dispatcher::getInstance()->defaultRoutes;
		if (!isset($defaultRoutes[$routeID]))
			return ;
		
		$rule = Tools::getValue('PS_ROUTE_'.$routeID);
		if (!$rule || $rule == $defaultRoutes[$routeID]['rule'])
		{
			Configuration::updateValue('PS_ROUTE_'.$routeID, '');
			return ;
		}

		$errors = array();
		if (!Dispatcher::getInstance()->validateRoute($routeID, $rule, $errors))
		{
			foreach ($errors as $error)
				$this->_errors[] = sprintf('Keyword "{%1$s}" required for route "%2$s" (rule: "%3$s")', $error, $routeID, htmlspecialchars($rule));
		}
		else
			Configuration::updateValue('PS_ROUTE_'.$routeID, $rule);
	}

	public function updateOptionPsRouteProductRule()
	{
		$this->checkAndUpdateRoute('product_rule');
	}

	public function updateOptionPsRouteCategoryRule()
	{
		$this->checkAndUpdateRoute('category_rule');
	}
	
	public function updateOptionPsRouteSupplierRule()
	{
		$this->checkAndUpdateRoute('supplier_rule');
	}
	
	public function updateOptionPsRouteManufacturerRule()
	{
		$this->checkAndUpdateRoute('manufacturer_rule');
	}
	
	public function updateOptionPsRouteCmsRule()
	{
		$this->checkAndUpdateRoute('cms_rule');
	}
	
	public function updateOptionPsRouteCmsCategoryRule()
	{
		$this->checkAndUpdateRoute('cms_category_rule');
	}
}
