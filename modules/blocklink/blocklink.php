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

class BlockLink extends Module
{
	/* @var boolean error */
	protected $error = false;
	
	public function __construct()
	{
	 	$this->name = 'blocklink';
	 	$this->tab = 'front_office_features';
	 	$this->version = '1.4';
		$this->author = 'PrestaShop';

	 	parent::__construct();

        $this->displayName = $this->l('Link block');
        $this->description = $this->l('Adds a block with additional links.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete all your links ?');
	}
	
	public function install()
	{
	 	if (!parent::install() OR
	 		!$this->registerHook('leftColumn') OR
	 		!Db::getInstance()->Execute('
	 		CREATE TABLE '._DB_PREFIX_.'blocklink (
	 		`id_link` int(2) NOT NULL AUTO_INCREMENT, 
	 		`url` varchar(255) NOT NULL,
	 		`new_window` TINYINT(1) NOT NULL,
	 		PRIMARY KEY(`id_link`))
	 		ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8') OR
	 		!Db::getInstance()->Execute('
	 		CREATE TABLE '._DB_PREFIX_.'blocklink_lang (
	 		`id_link` int(2) NOT NULL,
	 		`id_lang` int(2) NOT NULL,
	 		`text` varchar(64) NOT NULL,
	 		PRIMARY KEY(`id_link`, `id_lang`))
	 		ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8') OR
		 	!Configuration::updateValue('PS_BLOCKLINK_TITLE', array('1' => 'Block link', '2' => 'Bloc lien')))
	 		return false;
	 	return true;
	}
	
	public function uninstall()
	{
	 	if (!parent::uninstall() OR
	 		!Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'blocklink') OR
	 		!Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'blocklink_lang') OR
	 		!Configuration::deleteByName('PS_BLOCKLINK_TITLE') OR
	 		!Configuration::deleteByName('PS_BLOCKLINK_URL'))
	 		return false;
	 	return true;
	}
	
	public function hookLeftColumn($params)
	{
	 	global $cookie, $smarty;
	 	$links = $this->getLinks();
		
		$smarty->assign(array(
			'blocklink_links' => $links,
			'title' => Configuration::get('PS_BLOCKLINK_TITLE', $cookie->id_lang),
			'url' => Configuration::get('PS_BLOCKLINK_URL'),
			'lang' => 'text_'.$cookie->id_lang
		));
	 	if (!$links)
			return false;
		return $this->display(__FILE__, 'blocklink.tpl');
	}
	
	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}

	public function getLinks()
	{
	 	$result = array();
	 	/* Get id and url */
	 	if (!$links = Db::getInstance()->ExecuteS('SELECT `id_link`, `url`, `new_window` FROM '._DB_PREFIX_.'blocklink'.((int)(Configuration::get('PS_BLOCKLINK_ORDERWAY')) == 1 ? ' ORDER BY `id_link` DESC' : '')))
	 		return false;
	 	$i = 0;
	 	foreach ($links AS $link)
	 	{
		 	$result[$i]['id'] = $link['id_link'];
			$result[$i]['url'] = $link['url'];
			$result[$i]['newWindow'] = $link['new_window'];
			/* Get multilingual text */
			if (!$texts = Db::getInstance()->ExecuteS('SELECT `id_lang`, `text` FROM '._DB_PREFIX_.'blocklink_lang WHERE `id_link`='.(int)($link['id_link'])))
				return false;
			foreach ($texts AS $text)
				$result[$i]['text_'.$text['id_lang']] = $text['text'];
			$i++;
		}
	 	return $result;
	}
	
	public function addLink()
	{
	 	/* Url registration */
	 	if (!Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'blocklink VALUES (NULL, \''.pSQL($_POST['url']).'\', '.((isset($_POST['newWindow']) AND $_POST['newWindow']) == 'on' ? 1 : 0).')') OR !$lastId = mysql_insert_id())
	 		return false;
	 	/* Multilingual text */
	 	$languages = Language::getLanguages();
	 	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
	 	if (!$languages)
	 		return false;
	 	foreach ($languages AS $language)
	 	 	if (!empty($_POST['text_'.$language['id_lang']]))
	 	 	{
	 	 		if (!Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'blocklink_lang VALUES ('.(int)($lastId).', '.(int)($language['id_lang']).', \''.pSQL($_POST['text_'.$language['id_lang']]).'\')'))
	 	 			return false;
	 	 	}
	 	 	else
	 	 		if (!Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'blocklink_lang VALUES ('.(int)($lastId).', '.(int)($language['id_lang']).', \''.pSQL($_POST['text_'.$defaultLanguage]).'\')'))
	 	 			return false;
	 	return true;
	}
	
	public function updateLink()
	{
	 	/* Url registration */
	 	if (!Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'blocklink SET `url`=\''.pSQL($_POST['url']).'\', `new_window`='.(isset($_POST['newWindow']) ? 1 : 0).' WHERE `id_link`='.(int)($_POST['id'])))
	 		return false;
	 	/* Multilingual text */
	 	$languages = Language::getLanguages();
	 	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
	 	if (!$languages)
			 return false;
		if (!Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blocklink_lang WHERE `id_link` = '.(int)($_POST['id'])))
			return false ;
	 	foreach ($languages AS $language)
	 	 	if (!empty($_POST['text_'.$language['id_lang']]))
	 	 	{
	 	 		if (!Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'blocklink_lang VALUES ('.(int)($_POST['id']).', '.(int)($language['id_lang']).', \''.pSQL($_POST['text_'.$language['id_lang']]).'\')'))
	 	 			return false;
	 	 	}
	 	 	else
				if (!Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'blocklink_lang VALUES ('.(int)($_POST['id']).', '.$language['id_lang'].', \''.pSQL($_POST['text_'.$defaultLanguage]).'\')'))
	 	 			return false;
	 	return true;
	}
	
	public function deleteLink()
	{
	 	return Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blocklink WHERE `id_link`='.(int)($_GET['id']));
	}
	
	public function updateTitle()
	{
		$languages = Language::getLanguages();
		$result = array();
		foreach ($languages AS $language)
			$result[$language['id_lang']] = $_POST['title_'.$language['id_lang']];
	 	if (!Configuration::updateValue('PS_BLOCKLINK_TITLE', $result))
	 		return false;
	 	return Configuration::updateValue('PS_BLOCKLINK_URL', $_POST['title_url']);
	}
	
	public function getContent()
    {
     	$this->_html = '<h2>'.$this->displayName.'</h2>
		<script type="text/javascript" src="'.$this->_path.'blocklink.js"></script>';

     	/* Add a link */
     	if (isset($_POST['submitLinkAdd']))
     	{
     	 	if (empty($_POST['text_'.Configuration::get('PS_LANG_DEFAULT')]) OR empty($_POST['url']))
     	 		$this->_html .= $this->displayError($this->l('You must fill in all fields'));
     	 	elseif (!Validate::isUrl(str_replace('http://', '', $_POST['url'])))
     	 			$this->_html .= $this->displayError($this->l('Bad URL'));
	     	else
	     	  	if ($this->addLink())
	     	  		$this->_html .= $this->displayConfirmation($this->l('The link has been added.'));
	     	  	else
	     	 		$this->_html .= $this->displayError($this->l('An error occurred during link creation.'));
     	}
     	/* Update a link */
     	elseif (isset($_POST['submitLinkUpdate']))
     	{
     	 	if (empty($_POST['text_'.Configuration::get('PS_LANG_DEFAULT')]) OR empty($_POST['url']))
     	 		$this->_html .= $this->displayError($this->l('You must fill in all fields'));
     	 	elseif (!Validate::isUrl(str_replace('http://', '', $_POST['url'])))
     	 		$this->_html .= $this->displayError($this->l('Bad URL'));
	     	else
	     	 	if (empty($_POST['id']) OR !is_numeric($_POST['id']) OR !$this->updateLink())
	     	 		$this->_html .= $this->displayError($this->l('An error occurred during link updating.'));
	     	 	else
	     	 		$this->_html .= $this->displayConfirmation($this->l('The link has been updated.'));
     	}
     	/* Update the block title */
     	elseif (isset($_POST['submitTitle']))
     	{
     	 	if (empty($_POST['title_'.Configuration::get('PS_LANG_DEFAULT')]))
     	 		$this->_html .= $this->displayError($this->l('"title" field cannot be empty.'));
     	 	elseif (!empty($_POST['title_url']) AND !Validate::isUrl(str_replace('http://', '', $_POST['title_url'])))
     	 		$this->_html .= $this->displayError($this->l('The \'title\' field is invalid'));
     	 	elseif (!Validate::isGenericName($_POST['title_'.Configuration::get('PS_LANG_DEFAULT')]))
     	 		$this->_html .= $this->displayError($this->l('The \'title\' field is invalid'));
     	 	elseif (!$this->updateTitle())
     	 		$this->_html .= $this->displayError($this->l('An error occurred during title updating.'));
     	 	else
     	 		$this->_html .= $this->displayConfirmation($this->l('The block title has been updated.'));
     	}
     	/* Delete a link*/
     	elseif (isset($_GET['id']))
     	{
     	 	if (!is_numeric($_GET['id']) OR !$this->deleteLink())
     	 	 	$this->_html .= $this->displayError($this->l('An error occurred during link deletion.'));
     	 	else
     	 	 	$this->_html .= $this->displayConfirmation($this->l('The link has been deleted.'));
     	}
     	if (isset($_POST['submitOrderWay']))
		{
			if (Configuration::updateValue('PS_BLOCKLINK_ORDERWAY', (int)(Tools::getValue('orderWay'))))
				$this->_html .= $this->displayConfirmation($this->l('Sort order updated'));
			else
				$this->_html .= $this->displayError($this->l('An error occurred during sort order set-up.'));
		}

     	$this->_displayForm();
     	$this->_list();

        return $this->_html;
    }
	
	private function _displayForm()
	{
	 	global $cookie;
	 	/* Language */
	 	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$divLangName = 'text¤title';
		/* Title */
	 	$title_url = Configuration::get('PS_BLOCKLINK_URL');

	 	$this->_html .= '
		<script type="text/javascript">
			id_language = Number('.$defaultLanguage.');
		</script>
	 	<fieldset>
			<legend><img src="'.$this->_path.'add.png" alt="" title="" /> '.$this->l('Add a new link').'</legend>
			<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
				<label>'.$this->l('Text:').'</label>
				<div class="margin-form">';
			foreach ($languages as $language)
				$this->_html .= '
					<div id="text_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="text_'.$language['id_lang'].'" id="textInput_'.$language['id_lang'].'" value="'.(($this->error AND isset($_POST['text_'.$language['id_lang']])) ? $_POST['text_'.$language['id_lang']] : '').'" /><sup> *</sup>
					</div>';
			$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'text', true);
			$this->_html .= '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('URL:').'</label>
				<div class="margin-form"><input type="text" name="url" id="url" value="'.(($this->error AND isset($_POST['url'])) ? $_POST['url'] : '').'" /></div>
				<label>'.$this->l('Open in a new window:').'</label>
				<div class="margin-form"><input type="checkbox" name="newWindow" id="newWindow" '.(($this->error AND isset($_POST['newWindow'])) ? 'checked="checked"' : '').' /></div>
				<div class="margin-form">
					<input type="hidden" name="id" id="id" value="'.($this->error AND isset($_POST['id']) ? $_POST['id'] : '').'" />
					<input type="submit" class="button" name="submitLinkAdd" value="'.$this->l('Add this link').'" />
					<input type="submit" class="button disable" name="submitLinkUpdate" value="'.$this->l('Edit this link').'" disabled="disbaled" id="submitLinkUpdate" />
				</div>
			</form>
		</fieldset>
		<fieldset class="space">
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Block title').'</legend>
			<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
				<label>'.$this->l('Block title:').'</label>
				<div class="margin-form">';
		foreach ($languages as $language)
			$this->_html .= '
					<div id="title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="title_'.$language['id_lang'].'" value="'.(($this->error AND isset($_POST['title'])) ? $_POST['title'] : Configuration::get('PS_BLOCKLINK_TITLE', $language['id_lang'])).'" /><sup> *</sup>
					</div>';
		$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'title', true);
		$this->_html .= '
				<div class="clear"></div>
				</div>
				<label>'.$this->l('Block URL:').'</label>
				<div class="margin-form"><input type="text" name="title_url" value="'.(($this->error AND isset($_POST['title_url'])) ? $_POST['title_url'] : $title_url).'" /></div>
				<div class="margin-form"><input type="submit" class="button" name="submitTitle" value="'.$this->l('Update').'" /></div>
			</form>
		</fieldset>
		<fieldset class="space">
			<legend><img src="'.$this->_path.'prefs.gif" alt="" title="" /> '.$this->l('Settings').'</legend>
			<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
				<label>'.$this->l('Order list:').'</label>
				<div class="margin-form">
					<select name="orderWay">
						<option value="0"'.(!Configuration::get('PS_BLOCKLINK_ORDERWAY') ? 'selected="selected"' : '').'>'.$this->l('by most recent links').'</option>
						<option value="1"'.(Configuration::get('PS_BLOCKLINK_ORDERWAY') ? 'selected="selected"' : '').'>'.$this->l('by oldest links').'</option>
					</select>
				</div>
				<div class="margin-form"><input type="submit" class="button" name="submitOrderWay" value="'.$this->l('Update').'" /></div>
			</form>
		</fieldset>';
	}
	
	private function _list()
	{
	 	$links = $this->getLinks();
	 
	 	global $currentIndex, $cookie, $adminObj;
	 	$languages = Language::getLanguages();
	 	if ($links)
	 	{
	 		$this->_html .= '
			<script type="text/javascript">
				var currentUrl = \''.$currentIndex.'&configure='.$this->name.'\';
				var token=\''.$adminObj->token.'\';
				var links = new Array();';
	 		foreach ($links AS $link)
	 		{
	 			$this->_html .= 'links['.$link['id'].'] = new Array(\''.addslashes($link['url']).'\', '.$link['newWindow'];
	 			foreach ($languages AS $language)
					if (isset($link['text_'.$language['id_lang']]))
						$this->_html .= ', \''.addslashes($link['text_'.$language['id_lang']]).'\'';
					else
						$this->_html .= ', \'\'';
	 			$this->_html .= ');';
	 		}
	 		$this->_html .= '</script>';
	 	}
	 	$this->_html .= '
	 	<h3 class="blue space">'.$this->l('Link list').'</h3>
		<table class="table">
			<tr>
				<th>'.$this->l('ID').'</th>
				<th>'.$this->l('Text').'</th>
				<th>'.$this->l('URL').'</th>
				<th>'.$this->l('Actions').'</th>
			</tr>';
			
		if (!$links)
			$this->_html .= '
			<tr>
				<td colspan="3">'.$this->l('There are no links.').'</td>
			</tr>';
		else
			foreach ($links AS $link)
				$this->_html .= '
				<tr>
					<td>'.$link['id'].'</td>
					<td>'.$link['text_'.$cookie->id_lang].'</td>
					<td>'.$link['url'].'</td>
					<td>
						<img src="../img/admin/edit.gif" alt="" title="" onclick="linkEdition('.$link['id'].')" style="cursor: pointer" />
						<img src="../img/admin/delete.gif" alt="" title="" onclick="linkDeletion('.$link['id'].')" style="cursor: pointer" />
					</td>
				</tr>';
		$this->_html .= '
		</table>
		<input type="hidden" id="languageFirst" value="'.$languages[0]['id_lang'].'" />
		<input type="hidden" id="languageNb" value="'.sizeof($languages).'" />';
	}
}
