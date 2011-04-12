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

include_once(_PS_CLASS_DIR_.'../tools/pear/PEAR.php');
include_once(_PS_PEAR_XML_PARSER_PATH_.'Parser.php');

class Blockrss extends Module
{
 	function __construct()
 	{
 	 	$this->name = 'blockrss';
 	 	$this->tab = 'front_office_features';

		parent::__construct();

		$this->displayName = $this->l('RSS feed block');
		$this->description = $this->l('Adds a block displaying an RSS feed.');

		$this->version = '1.0';
		$this->author = 'PrestaShop';
		$this->error = false;
		$this->valid = false;
 	}

 	function install()
 	{
		Configuration::updateValue('RSS_FEED_TITLE', $this->l('RSS feed'));
		Configuration::updateValue('RSS_FEED_NBR', 5);
 	 	if (parent::install() == false OR $this->registerHook('leftColumn') == false OR $this->registerHook('header') == false) 
 	 		return false;
		return true;
  	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		
		if (Tools::isSubmit('submitBlockRss'))
		{
			$urlfeed = strval(Tools::getValue('urlfeed'));
			$title = strval(Tools::getValue('title'));
			$nbr = (int)(Tools::getValue('nbr'));
			if ($urlfeed AND !Validate::isUrl($urlfeed))
				$errors[] = $this->l('Invalid feed URL');
			elseif (!$title OR empty($title) OR !Validate::isGenericName($title))
				$errors[] = $this->l('Invalid title');
			elseif (!$nbr OR $nbr <= 0 OR !Validate::isInt($nbr))
				$errors[] = $this->l('Invalid number of feeds');				
			else
			{
				if (stristr($urlfeed, $_SERVER['HTTP_HOST'].__PS_BASE_URI__))
					$errors[] = $this->l('Error: You have selected a feed URL on your own website. Please choose another URL (eg. http://news.google.com/?output=rss).');
				
				Configuration::updateValue('RSS_FEED_URL', $urlfeed);
				Configuration::updateValue('RSS_FEED_TITLE', $title);
				Configuration::updateValue('RSS_FEED_NBR', $nbr);
			}
			if (isset($errors) AND sizeof($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->l('Settings updated'));
		}
		else
		{
			$errors = array();
			if (stristr(Configuration::get('RSS_FEED_URL'), $_SERVER['HTTP_HOST'].__PS_BASE_URI__))
				$errors[] = $this->l('Error: You have selected a feed URL on your own website. Please choose another URL (eg. http://news.google.com/?output=rss).');
			
			if (sizeof($errors))
				$output .= $this->displayError(implode('<br />', $errors));
		}

		return $output.$this->displayForm();
	}

	public function displayForm()
	{					
		$output = '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Block title').'</label>
				<div class="margin-form">
					<input type="text" name="title" value="'.Tools::getValue('title', Configuration::get('RSS_FEED_TITLE')).'" />
					<p class="clear">'.$this->l('Create a title for the block (default: \'RSS feed\')').'</p>

				</div>
				<label>'.$this->l('Add a feed URL').'</label>
				<div class="margin-form">
					<input type="text" size="85" name="urlfeed" value="'.Tools::getValue('urlfeed', Configuration::get('RSS_FEED_URL')).'" />
					<p class="clear">'.$this->l('Add the URL of the feed you want to use').'</p>

				</div>
				<label>'.$this->l('Number of threads displayed').'</label>
				<div class="margin-form">
					<input type="text" size="5" name="nbr" value="'.Tools::getValue('nbr', Configuration::get('RSS_FEED_NBR')).'" />
					<p class="clear">'.$this->l('Number of threads displayed by the block (default value: 5)').'</p>

				</div>
				<center><input type="submit" name="submitBlockRss" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}

	function hookLeftColumn($params)
	{
		global $smarty;
		
		// Conf
		$title = strval(Configuration::get('RSS_FEED_TITLE'));
		$url = strval(Configuration::get('RSS_FEED_URL'));
		$nb = (int)(Configuration::get('RSS_FEED_NBR'));
		
		// Getting data
		$rss_links = array();
		if ($url && ($contents = @file_get_contents($url)))
			if (@$src = new XML_Feed_Parser($contents))
				for ($i = 0; $i < ($nb ? $nb : 5); $i++)
					if (@$item = $src->getEntryByOffset($i))
						$rss_links[] = array('title' => $item->title, 'url' => $item->link);
		
		// Display smarty
		$smarty->assign(array('title' => ($title ? $title : $this->l('RSS feed')), 'rss_links' => $rss_links));

 	 	return $this->display(__FILE__, 'blockrss.tpl');
 	}

	function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}
	
	function hookHeader($params)
	{
		Tools::addCSS(($this->_path).'blockrss.css', 'all');
	}
}


