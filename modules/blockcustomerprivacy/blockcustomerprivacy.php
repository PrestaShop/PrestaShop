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
	
class Blockcustomerprivacy extends Module
{
	public function __construct()
	{
		$this->name = 'blockcustomerprivacy';
		if (version_compare(_PS_VERSION_, '1.4.0.0') >= 0)
			$this->tab = 'front_office_features';
		else
			$this->tab = 'Blocks';
		$this->version = '1.0';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
			
		parent::__construct();

		$this->displayName = $this->l('Customer data privacy block.');
		$this->description = $this->l('Adds a block displaying a message about a customer\'s privacy data. ');
	}
	
	public function install()
	{	
		$return = (parent::install() && $this->registerHook('createAccountForm') && $this->registerHook('header') && $this->registerHook('actionBeforeSubmitAccount'));
		Configuration::updateValue('CUSTPRIV_MESSAGE', array($this->context->language->id => 
			$this->l('The personal data you provide is used to answer queries, process orders or allow access to specific information.').' '.
			$this->l('You have the right to modify and delete all the personal information found in the "My Account" page. ')
		));
		return $return;
	}
	
	public function getContent()
	{
		$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
		$languages = Language::getLanguages(false);
		$iso = $this->context->language->iso_code;

		$output = '';
		if (Tools::isSubmit('submitCustPrivMess'))
		{
			$message_trads = array();
			foreach ($_POST as $key => $value)
				if (preg_match('/custpriv_message_/i', $key))
				{
					$id_lang = preg_split('/custpriv_message_/i', $key);
					$message_trads[(int)$id_lang[1]] = $value;
				}
			Configuration::updateValue('CUSTPRIV_MESSAGE', $message_trads, true);
			$this->_clearCache('blockcustomerprivacy.tpl');
			$output = '<div class="conf confirm">'.$this->l('Configuration updated').'</div>';
		}
		
		$content = '';
		if (version_compare(_PS_VERSION_, '1.4.0.0') >= 0)
			$content .= '
			<script type="text/javascript">	
				var iso = \''.(file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'\' ;
				var pathCSS = \''._THEME_CSS_DIR_.'\' ;
				var ad = \''.dirname($_SERVER['PHP_SELF']).'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
			<script language="javascript" type="text/javascript">
				id_language = Number('.$id_lang_default.');
				tinySetup();
			</script>';
		else
		{
			$content .= '
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript">
				tinyMCE.init({
					mode : "textareas",
					theme : "advanced",
					plugins : "safari,pagebreak,style,layer,table,advimage,advlink,inlinepopups,media,searchreplace,contextmenu,paste,directionality,fullscreen",
					theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
					theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
					theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
					theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,pagebreak",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : false,
					content_css : "'.__PS_BASE_URI__.'themes/'._THEME_NAME_.'/css/global.css",
					document_base_url : "'.__PS_BASE_URI__.'",
					width: "600",
					height: "auto",
					font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
					template_external_list_url : "lists/template_list.js",
					external_link_list_url : "lists/link_list.js",
					external_image_list_url : "lists/image_list.js",
					media_external_list_url : "lists/media_list.js",
					elements : "nourlconvert",
					entity_encoding: "raw",
					convert_urls : false,
					language : "'.(file_exists(_PS_ROOT_DIR_.'/js/tinymce/jscripts/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'"
				});
				id_language = Number('.$id_lang_default.');
			</script>';
		}
		
		$values = Configuration::getInt('CUSTPRIV_MESSAGE');
		$content .= $output;
		$content .= '
		<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</legend>
			<form action="'.htmlentities($_SERVER['REQUEST_URI']).'" method="post">				
				<label>'.$this->l('Customer data privacy message.').'</label>
				<div class="margin-form">';
		foreach ($languages as $language)
			$content .= '					
					<div id="ccont_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $id_lang_default ? 'block' : 'none').';float: left;">
						<textarea class="rte" cols="70" rows="30" id="custpriv_message_'.$language['id_lang'].'" name="custpriv_message_'.$language['id_lang'].'">'.(isset($values[$language['id_lang']]) ? $values[$language['id_lang']] : '').'</textarea>
					</div>';		
		$content .= $this->displayFlags($languages, $id_lang_default, 'ccont', 'ccont', true).'
					<div class="clear">
				</div>
					<p>
						'.$this->l('The customer data privacy message will be displayed in the account creation form.').'<br />
						'.$this->l('Tip: If the customer privacy message is too long to be written directly in the form, you can add a link to one of your pages. This can easily be created via the "CMS" page under the "Preferences" menu.').'
					</p>
				</div>
				<div class="clear">&nbsp;</div>
				<div class="margin-form">
					<input type="submit" class="button" name="submitCustPrivMess" value="'.$this->l('Save').'" />
				</div>
			</form>
		</fieldset>';
		
		return $content;
	}
	
	public function checkConfig()
	{
		if (!$this->active)
			return false;
		
		$message = Configuration::get('CUSTPRIV_MESSAGE', $this->context->language->id);
		if (empty($message))
			return false;
		
		return true;
	}
	
	public function hookHeader($params)
	{
		if (!$this->checkConfig())
			return;
		$this->context->controller->addJS($this->_path.'blockcustomerprivacy.js');
	}
	
	public function hookActionBeforeSubmitAccount($params)
	{
		if (!$this->checkConfig())
			return;
		
		if (!Tools::getValue('customer_privacy'))
			$this->context->controller->errors[] = $this->l('If you agree to the terms in the Customer Data Privacy message, please click the check box below.');
	}
	
	public function hookCreateAccountForm($params)
	{
		if (!$this->checkConfig())
			return;
		if (!$this->isCached('blockcustomerprivacy.tpl', $this->getCacheId()))
			$this->smarty->assign('privacy_message', Configuration::get('CUSTPRIV_MESSAGE', $this->context->language->id));
		
		return $this->display(__FILE__, 'blockcustomerprivacy.tpl', $this->getCacheId());
	}
} 
