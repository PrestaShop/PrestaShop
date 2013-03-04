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

include_once(_PS_CLASS_DIR_.'../tools/pear/PEAR.php');
include_once(_PS_PEAR_XML_PARSER_PATH_.'Parser.php');

class Blockrss extends Module
{
 	function __construct()
 	{
 	 	$this->name = 'blockrss';
 	 	$this->tab = 'front_office_features';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('RSS feed block');
		$this->description = $this->l('Adds a block displaying an RSS feed.');

		$this->version = '1.1';
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
			$errors = array();
			$urlfeed = Tools::getValue('urlfeed');
			$title = Tools::getValue('title');
			$nbr = (int)Tools::getValue('nbr');

			if ($urlfeed AND !Validate::isAbsoluteUrl($urlfeed))
				$errors[] = $this->l('Invalid feed URL');
			elseif (!$title OR empty($title) OR !Validate::isGenericName($title))
				$errors[] = $this->l('Invalid title');
			elseif (!$nbr OR $nbr <= 0 OR !Validate::isInt($nbr))
				$errors[] = $this->l('Invalid number of feeds');				
			elseif (stristr($urlfeed, $_SERVER['HTTP_HOST'].__PS_BASE_URI__))
				$errors[] = $this->l('You have selected a feed URL on your own website. Please choose another URL');
			elseif (!($contents = Tools::file_get_contents($urlfeed)))
				$errors[] = $this->l('Feed is unreachable, check your URL');
			/* Even if the feed was reachable, We need to make sure that the feed is well formated */
			else
			{
				try {	
					$xmlFeed = new XML_Feed_Parser($contents);
				} catch (XML_Feed_Parser_Exception $e) {
					$errors[] = $this->l('Invalid feed:').' '.$e->getMessage();
				}
			}
			
			if (!sizeof($errors))
			{
				Configuration::updateValue('RSS_FEED_URL', $urlfeed);
				Configuration::updateValue('RSS_FEED_TITLE', $title);
				Configuration::updateValue('RSS_FEED_NBR', $nbr);

				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
			else
				$output .= $this->displayError(implode('<br />', $errors));
		}
		else
		{
			$errors = array();
			if (stristr(Configuration::get('RSS_FEED_URL'), $_SERVER['HTTP_HOST'].__PS_BASE_URI__))
				$errors[] = $this->l('You have selected a feed URL on your own website. Please choose another URL');
			
			if (sizeof($errors))
				$output .= $this->displayError(implode('<br />', $errors));
		}

		return $output.$this->displayForm();
	}

	public function displayForm()
	{					
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Block title').'</label>
				<div class="margin-form">
					<input type="text" name="title" value="'.Tools::safeOutput(Tools::getValue('title', Configuration::get('RSS_FEED_TITLE'))).'" />
					<p class="clear">'.$this->l('Create a title for the block (default: \'RSS feed\')').'</p>

				</div>
				<label>'.$this->l('Add a feed URL').'</label>
				<div class="margin-form">
					<input type="text" size="85" name="urlfeed" value="'.Tools::safeOutput(Tools::getValue('urlfeed', Configuration::get('RSS_FEED_URL'))).'" />
					<p class="clear">'.$this->l('Add the URL of the feed you want to use (sample: http://news.google.com/?output=rss)').'</p>

				</div>
				<label>'.$this->l('Number of threads displayed').'</label>
				<div class="margin-form">
					<input type="text" size="5" name="nbr" value="'.(int)Tools::getValue('nbr', Configuration::get('RSS_FEED_NBR')).'" />
					<p class="clear">'.$this->l('Number of threads displayed by the block (default value: 5)').'</p>

				</div>
				<center><input type="submit" name="submitBlockRss" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}

	function hookLeftColumn($params)
	{
		// Conf
		$title = strval(Configuration::get('RSS_FEED_TITLE'));
		$url = strval(Configuration::get('RSS_FEED_URL'));
		$nb = (int)(Configuration::get('RSS_FEED_NBR'));

		// Getting data
		$rss_links = array();
		if ($url && ($contents = @file_get_contents($url)))
			try
			{
			if (@$src = new XML_Feed_Parser($contents))
				for ($i = 0; $i < ($nb ? $nb : 5); $i++)
					if (@$item = $src->getEntryByOffset($i))
						$rss_links[] = array('title' => $item->title, 'url' => $item->link);
			}
			catch (XML_Feed_Parser_Exception $e)
			{
				Tools::dieOrLog(sprintf($this->l('Error: invalid RSS feed in "blockrss" module: %s'), $e->getMessage()), false);
			}

		// Display smarty
		$this->smarty->assign(array('title' => ($title ? $title : $this->l('RSS feed')), 'rss_links' => $rss_links));

 	 	return $this->display(__FILE__, 'blockrss.tpl');
 	}

	function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}
	
	function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockrss.css', 'all');
	}
}