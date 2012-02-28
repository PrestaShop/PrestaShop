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
*  @version  Release: $Revision: 7445 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminMetaControllerCore extends AdminController
{
	public $table = 'meta';
	public $className = 'Meta';
	public $lang = true;
	protected $toolbar_fix = false;

	public function __construct()
	{
		parent::__construct();

		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fieldsDisplay = array(
			'id_meta' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'page' => array('title' => $this->l('Page'), 'width' => 120),
			'title' => array('title' => $this->l('Title'), 'width' => 120),
			'url_rewrite' => array('title' => $this->l('Friendly URL'), 'width' => 120)
		);
		$this->_group = 'GROUP BY a.id_meta';

		$this->options = array(
			'general' => array(
				'title' =>	$this->l('URLs Setup'),
				'fields' =>	array(
					'PS_REWRITING_SETTINGS' => array(
						'title' => $this->l('Friendly URL'),
						'desc' => $this->l('Enable only if your server allows URL rewriting (recommended)'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
						),
					'PS_CANONICAL_REDIRECT' => array(
						'title' => $this->l('Automatically redirect to Canonical url'),
						'desc' => $this->l('Recommended but your theme must be compliant'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
						),
				),
				'submit' => array()
			),
			'routes' => array(
				'title' =>	$this->l('Schema of URLs'),
				'description' => $this->l('You can change here the pattern of your links. There are some available keywords for each route listed below, keywords with * are required. To add a keyword in URL use {keyword} syntax. You can add some text before or after the keyword IF the keyword is not empty with syntax {prepend:keyword:append}, for example {-hey-:meta_title} will add "-hey-my-title" in URL if meta title is set, or nothing. Friendly URL and rewriting Apache option must be activated on your web server to use this functionality.'),
				'fields' => array(),
			),
		);

		// Display route options only if friendly URL is activated
		if (Configuration::get('PS_REWRITING_SETTINGS'))
		{
			$this->addFieldRoute('product_rule', $this->l('Route to products'));
			$this->addFieldRoute('category_rule', $this->l('Route to category'));
			$this->addFieldRoute('supplier_rule', $this->l('Route to supplier'));
			$this->addFieldRoute('manufacturer_rule', $this->l('Route to manufacturer'));
			$this->addFieldRoute('cms_rule', $this->l('Route to CMS page'));
			$this->addFieldRoute('cms_category_rule', $this->l('Route to CMS category'));
			$this->addFieldRoute('module', $this->l('Route to modules'));
		}
	}

	public function initProcess()
	{
		parent::initProcess();
		// This is a composite page, we don't want the "options" display mode
		if ($this->display == 'options')
			$this->display = '';
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryUi('ui.widget');
		$this->addJqueryPlugin('tagify');
	}
	
	public function addFieldRoute($routeID, $title)
	{
		$keywords = array();
		foreach (Dispatcher::getInstance()->default_routes[$routeID]['keywords'] as $keyword => $data)
			$keywords[] = ((isset($data['param'])) ? '<span class="red">'.$keyword.'*</span>' : $keyword);

		$this->options['routes']['fields']['PS_ROUTE_'.$routeID] = array(
			'title' =>	$title,
			'desc' => sprintf($this->l('Keywords: %s'), implode(', ', $keywords)),
			'validation' => 'isString',
			'type' => 'text',
			'size' => 70,
			'defaultValue' => Dispatcher::getInstance()->default_routes[$routeID]['rule'],
		);
	}

	public function renderForm()
	{
		$files = Meta::getPages(true, ($this->object->page ? $this->object->page : false));
		$pages = array();
		foreach ($files as $file)
			$pages[] = array('page' => $file);

 		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Meta-Tags'),
				'image' => '../img/admin/metatags.gif'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'id_meta',
				),
				array(
					'type' => 'select',
					'label' => $this->l('Page:'),
					'name' => 'page',
					'options' => array(
						'query' => $pages,
						'id' => 'page',
						'name' => 'page',
					),
					'desc' => $this->l('Name of the related page'),
					'required' => true,
					'empty_message' => '<p>'.$this->l('There is no page available!').'</p>',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Page\'s title:'),
					'name' => 'title',
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'desc' => $this->l('Title of this page'),
					'size' => 30
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta description:'),
					'name' => 'description',
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'desc' => $this->l('A short description'),
					'size' => 50
				),
				array(
					'type' => 'tags',
					'label' => $this->l('Meta keywords:'),
					'name' => 'keywords',
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'desc' => $this->l('List of keywords'),
					'size' => 50
				),
				array(
					'type' => 'text',
					'label' => $this->l('Rewritten URL:'),
					'name' => 'url_rewrite',
					'lang' => true,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'desc' => $this->l('Example : "contacts" for http://mysite.com/shop/contacts to redirect to http://mysite.com/shop/contact-form.php'),
					'size' => 50
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);
		return parent::renderForm();
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
				$defaultLangIsValidated = !Tools::getValue('url_rewrite_'.$default_language) || Validate::isLinkRewrite(Tools::getValue('url_rewrite_'.$default_language));
				$englishLangIsValidated = !Tools::getValue('url_rewrite_1') || Validate::isLinkRewrite(Tools::getValue('url_rewrite_1'));
			}

			if (!$defaultLangIsValidated && !$englishLangIsValidated)
			{
				$this->errors[] = Tools::displayError('Url rewrite field must be filled at least in default or english language.');
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
			Hook::exec('actionAdminMetaSave');
		}

		return parent::postProcess();
	}

	public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, Context::getContext()->shop->id);
	}

	/**
	 * Validate route syntax and save it in configuration
	 *
	 * @param string $routeID
	 */
	public function checkAndUpdateRoute($routeID)
	{
		$default_routes = Dispatcher::getInstance()->default_routes;
		if (!isset($default_routes[$routeID]))
			return;

		$rule = Tools::getValue('PS_ROUTE_'.$routeID);
		if (!$rule || $rule == $default_routes[$routeID]['rule'])
		{
			Configuration::updateValue('PS_ROUTE_'.$routeID, '');
			return;
		}

		$errors = array();
		if (!Dispatcher::getInstance()->validateRoute($routeID, $rule, $errors))
		{
			foreach ($errors as $error)
				$this->errors[] = sprintf('Keyword "{%1$s}" required for route "%2$s" (rule: "%3$s")', $error, $routeID, htmlspecialchars($rule));
		}
		else
			Configuration::updateValue('PS_ROUTE_'.$routeID, $rule);
	}

	/**
	 * Called when PS_REWRITING_SETTINGS option is saved
	 */
	public function updateOptionPsRewritingSettings()
	{
		Configuration::updateValue('PS_REWRITING_SETTINGS', (int)Tools::getValue('PS_REWRITING_SETTINGS'));
		Tools::generateHtaccess();
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

	/**
	 * Function used to render the options for this controller
	 */
	public function renderOptions()
	{
		if (!Configuration::get('PS_REWRITING_SETTINGS'))
			unset($this->options['routes']);

		if ($this->options && is_array($this->options))
		{
			$helper = new HelperOptions($this);
			$this->setHelperDisplay($helper);
			$helper->toolbar_fix = true;
			$helper->toolbar_btn = array('save' => array(
								'href' => '#',
								'desc' => $this->l('Save')
							));
			$helper->id = $this->id;
			$helper->tpl_vars = $this->tpl_option_vars;
			$options = $helper->generateOptions($this->options);

			return $options;
		}
	}
}
