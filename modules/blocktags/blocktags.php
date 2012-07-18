<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

define('BLOCKTAGS_MAX_LEVEL', 3);

class BlockTags extends Module
{
	function __construct()
	{
		$this->name = 'blocktags';
		$this->tab = 'front_office_features';
		$this->version = '1.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Tags block');
		$this->description = $this->l('Adds a block containing a tag cloud.');
	}

	function install()
	{
		if (parent::install() == false
				|| $this->registerHook('leftColumn') == false
				|| $this->registerHook('header') == false
				|| Configuration::updateValue('BLOCKTAGS_NBR', 10) == false)
			return false;
		return true;
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitBlockTags'))
		{
			if (!($tagsNbr = Tools::getValue('tagsNbr')) || empty($tagsNbr))
				$output .= '<div class="alert error">'.$this->l('Please fill in the "tags displayed" field.').'</div>';
			elseif ((int)($tagsNbr) == 0)
				$output .= '<div class="alert error">'.$this->l('Invalid number.').'</div>';
			else
			{
				Configuration::updateValue('BLOCKTAGS_NBR', (int)$tagsNbr);
				$output .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
			}
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Tags displayed').'</label>
				<div class="margin-form">
					<input type="text" name="tagsNbr" value="'.(int)(Configuration::get('BLOCKTAGS_NBR')).'" />
					<p class="clear">'.$this->l('Set the number of tags to be displayed in this block').'</p>
				</div>
				<center><input type="submit" name="submitBlockTags" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}

	/**
	* Returns module content for left column
	*
	* @param array $params Parameters
	* @return string Content
	*
	*/
	function hookLeftColumn($params)
	{
		$tags = Tag::getMainTags((int)($params['cookie']->id_lang), (int)(Configuration::get('BLOCKTAGS_NBR')));
		
		$max = -1;
		$min = -1;
		foreach ($tags as $tag)
		{
			if ($tag['times'] > $max)
				$max = $tag['times'];
			if ($tag['times'] < $min || $min == -1)
				$min = $tag['times'];
		}
		
		if ($min == $max)
			$coef = $max;
		else
		{
			$coef = (BLOCKTAGS_MAX_LEVEL - 1) / ($max - $min);
		}
		
		if (!sizeof($tags))
			return false;
		foreach ($tags AS &$tag)
			$tag['class'] = 'tag_level'.(int)(($tag['times'] - $min) * $coef + 1);
		$this->smarty->assign('tags', $tags);

		return $this->display(__FILE__, 'blocktags.tpl');
	}

	function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}

	function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blocktags.css', 'all');
	}

}
