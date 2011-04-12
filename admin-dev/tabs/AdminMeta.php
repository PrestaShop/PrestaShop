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

class AdminMeta extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'meta';
	 	$this->className = 'Meta';
		$this->lang = true;
	 	$this->edit = true;
	 	$this->delete = true;

		$this->fieldsDisplay = array(
			'id_meta' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'page' => array('title' => $this->l('Page'), 'width' => 120, 'suffix' => '.php'),
			'title' => array('title' => $this->l('Title'), 'width' => 120),
			'url_rewrite' => array('title' => $this->l('Friendly URL'), 'width' => 120)
		);
		
		global $cookie;
		$this->optionTitle = $this->l('URLs Setup');
		$this->_fieldsOptions = array(
			'__PS_BASE_URI__' => array('title' => $this->l('PS directory'), 'desc' => $this->l('Name of the PrestaShop directory on your Web server, bracketed by forward slashes (e.g., /shop/)'), 'validation' => 'isUrl', 'type' => 'text', 'size' => 20, 'default' => __PS_BASE_URI__),
			'PS_HOMEPAGE_PHP_SELF' => array('title' => $this->l('Homepage file'), 'desc' => $this->l('Usually "index.php", but may be different for a few hosts.'), 'type' => 'string', 'size' => 50),
			'PS_SHOP_DOMAIN' => array('title' => $this->l('Shop domain name'), 'desc' => $this->l('Domain name of your shop, used as a canonical URL (e.g., www.myshop.com). Keep it blank if you don\'t know what to do.'), 'validation' => 'isUrl', 'type' => 'text', 'size' => 30, 'default' => ''),
			'PS_SHOP_DOMAIN_SSL' => array('title' => $this->l('Shop domain name for SSL'), 'desc' => $this->l('Domain name for the secured area of your shop, used as a canonical URL (e.g., secure.myshop.com). Keep it blank if you don\'t know what to do.'), 'validation' => 'isUrl', 'type' => 'text', 'size' => 30, 'default' => ''),
			'PS_REWRITING_SETTINGS' => array('title' => $this->l('Friendly URL'), 'desc' => $this->l('Enable only if your server allows URL rewriting (recommended)').'<p class="hint clear" style="display: block;">'.$this->l('If you turn on this feature, you must').' <a href="?tab=AdminGenerator&token='.Tools::getAdminToken('AdminGenerator'.(int)(Tab::getIdFromClassName('AdminGenerator')).(int)$cookie->id_employee).'">'.$this->l('generate a .htaccess file').'</a></p><div class="clear"></div>', 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
			'PS_CANONICAL_REDIRECT' => array('title' => $this->l('Automatically redirect to Canonical url'), 'desc' => $this->l('Recommended but your theme must be compliant'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
		);
		if (!Tools::getValue('__PS_BASE_URI__'))
			$_POST['__PS_BASE_URI__'] = __PS_BASE_URI__;
	
		parent::__construct();
	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm();
		
		if (!($meta = $this->loadObject(true)))
			return;
		$files = Meta::getPages(true, ($meta->page ? $meta->page : false));

		echo '
		<form action="'.$currentIndex.'&token='.$this->token.'&submitAdd'.$this->table.'=1" method="post">
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
						echo'>'.$file.'.php&nbsp;</option>';
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
		
		if (Tools::isSubmit('submitOptions'.$this->table))
		{
			$baseUrls = array();
			if ($__PS_BASE_URI__ = Tools::getValue('__PS_BASE_URI__'))
				$baseUrls['__PS_BASE_URI__'] = $__PS_BASE_URI__;
			rewriteSettingsFile($baseUrls, NULL, NULL);
			unset($this->_fieldsGeneral['__PS_BASE_URI__']);
		}
		if (Tools::isSubmit('submitOptions'.$this->table) OR Tools::isSubmit('submitAddmeta'))
			Module::hookExec('afterSaveAdminMeta');
		
		return parent::postProcess();
	}
}


