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

include_once(PS_ADMIN_DIR.'/tabs/AdminPreferences.php');

class AdminSearchConf extends AdminPreferences
{
	public function __construct()
	{
		global $cookie;

		$this->className = 'Configuration';
		$this->table = 'configuration';

 		$this->_fieldsSearch = array(
			'PS_SEARCH_AJAX' => array('title' => $this->l('Ajax search'), 'desc' => $this->l('Enable the ajax search for your visitors.'), 'validation' => 'isBool', 'type' => 'bool', 'cast' => 'intval'),
			'PS_INSTANT_SEARCH' => array('title' => $this->l('Instant search:'), 'desc' => $this->l('The fastest search solution for your shop'), 'validation' => 'isBool', 'cast' => 'intval', 'type' => 'bool'),
			'PS_SEARCH_MINWORDLEN' => array('title' => $this->l('Minimum word length'), 'desc' => $this->l('Only words from this size will be indexed.'), 'size' => 4, 'validation' => 'isUnsignedInt', 'type' => 'text', 'cast' => 'intval'),
			'PS_SEARCH_BLACKLIST' => array('title' => $this->l('Blacklisted words'), 'size' => 35, 'validation' => 'isGenericName', 'desc' => $this->l('Please enter the words separated by a "|".'), 'type' => 'textLang'),
			'PS_SEARCH_WEIGHT_PNAME' => array('title' => $this->l('Product name weight'), 'size' => 4, 'validation' => 'isUnsignedInt', 'type' => 'text', 'cast' => 'intval'),
			'PS_SEARCH_WEIGHT_REF' => array('title' => $this->l('Reference weight'), 'size' => 4, 'validation' => 'isUnsignedInt', 'type' => 'text', 'cast' => 'intval'),
			'PS_SEARCH_WEIGHT_SHORTDESC' => array('title' => $this->l('Short description weight'), 'size' => 4, 'validation' => 'isUnsignedInt', 'type' => 'text', 'cast' => 'intval'),
			'PS_SEARCH_WEIGHT_DESC' => array('title' => $this->l('Description weight'), 'size' => 4, 'validation' => 'isUnsignedInt', 'type' => 'text', 'cast' => 'intval'),
			'PS_SEARCH_WEIGHT_CNAME' => array('title' => $this->l('Category weight'), 'size' => 4, 'validation' => 'isUnsignedInt', 'type' => 'text', 'cast' => 'intval'),
			'PS_SEARCH_WEIGHT_MNAME' => array('title' => $this->l('Manufacturer weight'), 'size' => 4, 'validation' => 'isUnsignedInt', 'type' => 'text', 'cast' => 'intval'),
			'PS_SEARCH_WEIGHT_TAG' => array('title' => $this->l('Tags weight'), 'size' => 4, 'validation' => 'isUnsignedInt', 'type' => 'text', 'cast' => 'intval'),
			'PS_SEARCH_WEIGHT_ATTRIBUTE' => array('title' => $this->l('Attributes weight'), 'size' => 4, 'validation' => 'isUnsignedInt', 'type' => 'text', 'cast' => 'intval'),
			'PS_SEARCH_WEIGHT_FEATURE' => array('title' => $this->l('Features weight'), 'size' => 4, 'validation' => 'isUnsignedInt', 'type' => 'text', 'cast' => 'intval')
		);
	
		parent::__construct();
	}
	
	public function postProcess()
	{
		if (isset($_POST['submitSearch'.$this->table]))
		{
		 	if ($this->tabAccess['edit'] === '1')
				$this->_postConfig($this->_fieldsSearch);
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
	}
	
	public function display()
	{
		$currentFileName = array_reverse(explode("/", $_SERVER['SCRIPT_NAME']));
		$cronUrl = Tools::getHttpHost(true, true).__PS_BASE_URI__.substr($_SERVER['SCRIPT_NAME'], strlen(__PS_BASE_URI__), -strlen($currentFileName['0'])).'searchcron.php?full=1&token='.substr(_COOKIE_KEY_, 34, 8);
		list($total, $indexed) = Db::getInstance()->getRow('SELECT COUNT(*) as "0", SUM(indexed) as "1" FROM '._DB_PREFIX_.'product');
		echo '
		<fieldset>
			<legend><img src="../img/admin/search.gif" alt="" /> '.$this->l('Indexation').'</legend>
			'.$this->l('Indexed products:').' <b>'.(int)($indexed).' / '.(int)($total).'</b>.<br /><br />
			-&gt; <a href="searchcron.php?token='.substr(_COOKIE_KEY_, 34, 8).'" class="bold">'.$this->l('Add missing products to index.').'</a><br />
			-&gt; <a href="searchcron.php?full=1&token='.substr(_COOKIE_KEY_, 34, 8).'" class="bold">'.$this->l('Re-build entire index.').'</a><br /><br />
			'.$this->l('You can set a cron job that will re-build your index using the following URL:').' <a href="'.$cronUrl.'">'.$cronUrl.'</a>.
		</fieldset>
		<div class="clear">&nbsp;</div>';
		$this->_displayForm('search', $this->_fieldsSearch, $this->l('Search'), 'width2', 'search');
	}
}


