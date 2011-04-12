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

class AdminGenerator extends AdminTab
{
	public function __construct()
	{
		$this->_htFile = dirname(__FILE__).'/../../.htaccess';
		$this->_rbFile = dirname(__FILE__).'/../../robots.txt';
		$this->_smFile = dirname(__FILE__).'/../../sitemap.xml';
		$this->_rbData = $this->_getRobotsContent();
		return parent::__construct();
	}

	public function display()
	{
		global $currentIndex;

		$languages = Language::getLanguages(false);

		// Htaccess
		echo '
		<form action="'.$currentIndex.'&token='.$this->token.'" method="post" enctype="multipart/form-data">
		<fieldset><legend><img src="../img/admin/htaccess.gif" />'.$this->l('Htaccess file generation').'</legend>
		<p><b>'.$this->l('Warning:').'</b> '.$this->l('this tool can ONLY be used if you are hosted by an Apache web server. Please ask your webhost.').'</p>
		<p>'.$this->l('This tool will automatically generate a ".htaccess" file that will give you the ability to do URL rewriting and to catch 404 errors.').'</p>
		<p>'.$this->l('If you do not have "Friendly URL" enabled when generating the ".htaccess" file, this feature won\'t be available.').'</p>';
		if ($this->_checkConfiguration($this->_htFile))
			echo '
			<div class="clear">&nbsp;</div>
			<label for="imageCacheControl">'.$this->l('Optimization').'</label>
			<div class="margin-form">
				<input type="checkbox" name="PS_HTACCESS_CACHE_CONTROL" id="PS_HTACCESS_CACHE_CONTROL" value="1" '.(Configuration::get('PS_HTACCESS_CACHE_CONTROL') == 1 ? 'checked="checked"' : '').' />
				<p>'.$this->l('This will add directives to your .htaccess file which should improve caching and compression.').'</p>
			</div>
			<div class="clear">&nbsp;</div>
			<label for="imageCacheControl">'.$this->l('Friendly URL').'</label>
			<div class="margin-form">
				<input type="checkbox" name="PS_REWRITING_SETTINGS" id="PS_REWRITING_SETTINGS" value="1" '.(Configuration::get('PS_REWRITING_SETTINGS') ? 'checked="checked"' : '').' />
				<p>'.$this->l('Enable only if your server allows URL rewriting.').'</p>
			</div>
			<div class="clear">&nbsp;</div>
			<label for="specific_configuration">'.$this->l('Specific configuration').'</label>
			<div class="margin-form">
				<textarea rows="10" class="width3" id="specific_configuration" name="ps_htaccess_specific">'.Configuration::get('PS_HTACCESS_SPECIFIC').'</textarea>
				<p>'.$this->l('Add here the specifical directives of your hosting (SetEnv PHP_VER 5, AddType x-mapp-php5 .php...).').'</p>
			</div>
			<p class="clear" style="font-weight:bold;">'.$this->l('Generate your ".htaccess" file by clicking on the following button:').'<br /><br />
			<input type="submit" value="'.$this->l('Generate .htaccess file').'" name="submitHtaccess" class="button" /></p>
			<p>'.$this->l('This will erase your').'<b> '.$this->l('old').'</b> '.$this->l('.htaccess file!').'</p>';
		else
			echo '
			<p style="color:red; font-weight:bold;">'.$this->l('Before being able to use this tool, you need to:').'</p>
			<p>'.$this->l('- create a').' <b>'. $this->l('.htaccess').'</b> '.$this->l('blank file in directory').' <b>'.__PS_BASE_URI__.'</b>
			<br />'.$this->l('- give it write permissions (CHMOD 666 on Unix system)').'</p>';
		echo '</p></fieldset></form>';

		// Robots
		echo '<br /><br />
		<form action="'.$currentIndex.'&token='.$this->token.'" method="post" enctype="multipart/form-data">
		<fieldset><legend><img src="../img/admin/robots.gif" />'.$this->l('Robots file generation').'</legend>
		<p><b>'.$this->l('Warning:').' </b>'.$this->l('Your file robots.txt MUST be in your website\'s root directory and nowhere else.').'</p>
		<p>'.$this->l('eg: http://www.yoursite.com/robots.txt').'.</p>
		<p>'.$this->l('This tool will automatically generate a "robots.txt" file that you can configure to deny access to search engines for some pages.').'</p>';
		if ($this->_checkConfiguration($this->_rbFile))
			echo '
			<p style="font-weight:bold;">'.$this->l('Generate your "robots.txt" file by clicking on the following button:').'<br /><br />
			<input type="submit" value="'.$this->l('Generate robots.txt file').'" name="submitRobots" class="button" /></p>
			<p>'.$this->l('This will erase your').'<b> '.$this->l('old').'</b> '.$this->l('robots.txt file!').'</p>';
		else
			echo '
			<p style="color:red; font-weight:bold;">'.$this->l('Before being able to use this tool, you need to:').'</p>
			<p>'.$this->l('- create a').' <b>'. $this->l('robots.txt').'</b> '.$this->l('blank file in dir:').' <b>'.__PS_BASE_URI__.'</b>
			<br />'.$this->l('- give it write permissions (CHMOD 666 on Unix system)').'</p>';
		echo '</p></fieldset></form>';
	}

	public function _checkConfiguration($file)
	{
		if (file_exists($file))
			return is_writable($file);
		return is_writable(dirname($file));
	}

	function postProcess()
	{
		global $currentIndex;

		if (Tools::isSubmit('submitHtaccess'))
		{
			if ($this->tabAccess['edit'] === '1')
			{		
				Configuration::updateValue('PS_HTACCESS_CACHE_CONTROL', (int)Tools::getValue('PS_HTACCESS_CACHE_CONTROL'));
				Configuration::updateValue('PS_REWRITING_SETTINGS', (int)Tools::getValue('PS_REWRITING_SETTINGS'));
				Configuration::updateValue('PS_HTACCESS_SPECIFIC',  Tools::getValue('ps_htaccess_specific'), true);
				if (Tools::generateHtaccess($this->_htFile, Configuration::get('PS_REWRITING_SETTINGS'), Configuration::get('PS_HTACCESS_CACHE_CONTROL'), Tools::getValue('ps_htaccess_specific')))
					Tools::redirectAdmin($currentIndex.'&conf=4&token='.$this->token);
				$this->_errors[] = $this->l('Cannot write into file:').' <b>'.$this->_htFile.'</b><br />'.$this->l('Please check write permissions.');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if (Tools::isSubmit('submitRobots'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (!$writeFd = @fopen($this->_rbFile, 'w'))
					die ($this->l('Cannot write into file:').' <b>'.$this->_rbFile.'</b><br />'.$this->l('Please check write permissions.'));
				else
				{
					// PS Comments
					fwrite($writeFd, "# robots.txt automaticaly generated by PrestaShop e-commerce open-source solution\n");
					fwrite($writeFd, "# http://www.prestashop.com - http://www.prestashop.com/forums\n\n");
					fwrite($writeFd, "# This file is to prevent the crawling and indexing of certain parts\n");
					fwrite($writeFd, "# of your site by web crawlers and spiders run by sites like Yahoo!\n");
					fwrite($writeFd, "# and Google. By telling these \"robots\" where not to go on your site,\n");
					fwrite($writeFd, "# you save bandwidth and server resources.\n\n");
					fwrite($writeFd, "# For more information about the robots.txt standard, see:\n");
					fwrite($writeFd, "# http://www.robotstxt.org/wc/robots.html\n\n");

					// User-Agent
					fwrite($writeFd, "User-agent: *\n\n");

					// Directories
					fwrite($writeFd, "# Directories\n");
					foreach ($this->_rbData['Directories'] as $dir)
						fwrite($writeFd, 'Disallow: '.__PS_BASE_URI__.$dir."\n");
					fwrite($writeFd, "\n");

					// Files
					fwrite($writeFd, "# Files\n");
					foreach ($this->_rbData['Files'] as $file)
						fwrite($writeFd, 'Disallow: '.__PS_BASE_URI__.$file."\n");
					fwrite($writeFd, "\n");

					// Sitemap
					fwrite($writeFd, "# Sitemap\n");
					if (file_exists($this->_smFile))
						if (filesize($this->_smFile))
							fwrite($writeFd, 'Sitemap: '.(Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$_SERVER['SERVER_NAME'].__PS_BASE_URI__.'sitemap.xml'."\n");
					fwrite($writeFd, "\n");

					fclose($writeFd);
					Tools::redirectAdmin($currentIndex.'&conf=4&token='.$this->token);
				}
			} else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
	}

	public function _getRobotsContent()
	{
		$tab = array();

		$lang_dir = 'lang-'.Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')).'/';
		
		// Directories
		$tab['Directories'] = array('classes/', 'config/', 'download/', 'mails/', 'modules/', 'translations/', 'tools/', $lang_dir);

		// Files
		$tab['Files'] = array('addresses.php', 'address.php', 'authentication.php', 'cart.php', 'discount.php', 'footer.php',
		'get-file.php', 'header.php', 'history.php', 'identity.php', 'images.inc.php', 'init.php', 'my-account.php', 'order.php', 'order-opc.php',
		'order-slip.php', 'order-detail.php', 'order-follow.php', 'order-return.php', 'order-confirmation.php', 'pagination.php', 'password.php',
		'pdf-invoice.php', 'pdf-order-return.php', 'pdf-order-slip.php', 'product-sort.php', 'search.php', 'statistics.php',
		'*orderby=','*orderway=','*p=','*tag=','*id_currency=','*search_query=','*id_lang=','*back=','*utm_source=','*utm_medium=','*utm_campaign=','*n=');

		return $tab;
	}
}


