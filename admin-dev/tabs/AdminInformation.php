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

class AdminInformation extends AdminTab
{
	private function _getTestResultHtml()
	{
		$html = '';
		// Functions list to test with 'test_system'
		$funcs = array('fopen', 'fclose', 'fread', 'fwrite', 'rename', 'file_exists', 'unlink', 'rmdir', 'mkdir', 'getcwd', 'chdir', 'chmod');
		
		// Test list to execute (function/args)
		$tests = array(
			'phpversion' => false,
			'upload' => false,
			'system' => $funcs,
			'gd' => false,
			'mysql_support' => false,
			'config_dir' => PS_ADMIN_DIR.'/../config/',
			'tools_dir' => PS_ADMIN_DIR.'/../tools/smarty/compile',
			'cache_dir' => PS_ADMIN_DIR.'/../tools/smarty/cache/',
			'sitemap' => PS_ADMIN_DIR.'/../sitemap.xml',
			'img_dir' => PS_ADMIN_DIR.'/../img/',
			'mails_dir' => PS_ADMIN_DIR.'/../mails/',
			'module_dir' => PS_ADMIN_DIR.'/../modules/',
			'theme_lang_dir' => PS_ADMIN_DIR.'/../themes/'._THEME_NAME_.'/lang/',
			'translations_dir' => PS_ADMIN_DIR.'/../translations/',
			'customizable_products_dir' => PS_ADMIN_DIR.'/../upload/',
			'virtual_products_dir' => PS_ADMIN_DIR.'/../download/'
		);

		$tests_op = array(
			'fopen' => false,
			'register_globals' => false,
			'gz' => false
		);

		$testsErrors = array(
			'phpversion' => $this->l('Update your PHP version'),
			'upload' => $this->l('Configure your server to allow the upload file'),
			'system' => $this->l('Configure your server to allow the creation of directories and write to files'),
			'gd' => $this->l('Enable the GD library on your server'),
			'mysql_support' => $this->l('Enable the MySQL support on your server'),
			'config_dir' => $this->l('Set write permissions for config folder'),
			'tools_dir' => $this->l('Set write permissions for tools folder'),
			'cache_dir' => $this->l('Set write permissions for cache folder'),
			'sitemap' => $this->l('Set write permissions for sitemap.xml file'),
			'img_dir' => $this->l('Set write permissions for img folder and subfolders/recursively'),
			'mails_dir' => $this->l('Set write permissions for mails folder and subfolders/recursively'),
			'module_dir' => $this->l('Set write permissions for modules folder and subfolders/recursively'),
			'theme_lang_dir' => $this->l('Set write permissions for themes/')._THEME_NAME_.$this->l('/lang/ folder and subfolders/recursively'),
			'translations_dir' => $this->l('Set write permissions for translations folder and subfolders/recursively'),
			'customizable_products_dir' => $this->l('Set write permissions for upload folder and subfolders/recursively'),
			'virtual_products_dir' => $this->l('Set write permissions for download folder and subfolders/recursively'),
			'fopen' => $this->l('Enable fopen on your server'),
			'register_globals' => $this->l('Set PHP register global option to off'),
			'gz' => $this->l('Enable GZIP compression on your server')
		);

		$paramsRequiredResults = self::check($tests);
		$paramsOptionalResults = self::check($tests_op);

		$html .= '
			<p>
				<b>'.$this->l('Required parameters').':</b>';
		if (!in_array('fail', $paramsRequiredResults))
				$html .= ' <span style="color:green;font-weight:bold;">OK</span>
			</p>
			';
		else
		{
			$html .= ' <span style="color:red">'.$this->l('Please consult the following error(s)').'</span>
			</p>
			<ul>
			';
			foreach ($paramsRequiredResults AS $key => $value)
				if ($value == 'fail')
					$html .= '<li>'.$testsErrors[$key].'</li>';
			$html .= '</ul>';
		}
		
		$html .= '
			<p>
				<b>'.$this->l('Optional parameters').':</b>';
		if (!in_array('fail', $paramsOptionalResults))
				$html .= ' <span style="color:green;font-weight:bold;">OK</span>
			</p>
			';
		else
		{
			$html .= ' <span style="color:red">'.$this->l('Please consult the following error(s)').'</span>
			</p>
			<ul>
			';
			foreach ($paramsOptionalResults AS $key => $value)
				if ($value == 'fail')
					$html .= '<li>'.$testsErrors[$key].'</li>';
			$html .= '</ul>';
		}
		
		return $html;
	}
	
	public function display()
	{
		global $currentIndex;
		
		echo '
		<h2>'.$this->l('Information').'</h2>
		<fieldset>
			<legend><img src="../img/t/AdminInformation.gif" alt="" /> '.$this->l('Help').'</legend>
			<p>'.$this->l('This information must be indicated when you report a bug on our bug tracker or if you report a problem on our forum.').'</p>
		</fieldset>
		<br />
		<fieldset>
			<legend><img src="../img/t/AdminInformation.gif" alt="" /> '.$this->l('Information about your configuration').'</legend>
			<h3>'.$this->l('Server information').'</h3>
			<p>
				<b>'.$this->l('Prestashop Version').':</b> 
				'._PS_VERSION_.'
			</p>';
			
		if (function_exists('php_uname'))
			echo '
			<p>
				<b>'.$this->l('Server information').':</b> 
				'.php_uname('s').' '.php_uname('v').' '.php_uname('m').'
			</p>';
			
		echo '
			<p>
				<b>'.$this->l('Server software Version').':</b> 
				'.$_SERVER['SERVER_SOFTWARE'].'
			</p>
			<p>
				<b>'.$this->l('PHP Version').':</b> 
				'.phpversion().'
			</p>
			<p>
				<b>'.$this->l('MySQL Version').':</b> 
				'.mysql_get_server_info().'
			</p>';
		if (function_exists('apache_get_modules') AND in_array('mod_instaweb', apache_get_modules()))
			echo '<p style="color:red;font-weight:700">'.$this->l('PageSpeed module for Apache installed (mod_instaweb)').'</p>';
		echo '
			<hr />
			<h3>'.$this->l('Store information').'</h3>
			<p>
				<b>'.$this->l('URL of your website').':</b> 
				'.Tools::getHttpHost(true).__PS_BASE_URI__.'
			</p>
			<p>
				<b>'.$this->l('Theme name used').':</b> 
				'._THEME_NAME_.'
			</p>
			<hr />
			<h3>'.$this->l('Mail information').'</h3>
			<p>
				<b>'.$this->l('Mail method').':</b>
		';
		if (Configuration::get('PS_MAIL_METHOD') == 1)
			echo $this->l('You use PHP mail() function.').'</p>';
		else
		{
			echo $this->l('You use your own SMTP parameters').'</p>';
			echo '
			<p>
				<b>'.$this->l('SMTP server').':</b> 
				'.Configuration::get('PS_MAIL_SERVER').'
			</p>
			<p>
				<b>'.$this->l('SMTP user').':</b> 
				'.(Configuration::get('PS_MAIL_USER') ? $this->l('Defined') : '<span style="color:red;">'.$this->l('Not defined').'</span>').'
			</p>
			<p>
				<b>'.$this->l('SMTP password').':</b> 
				'.(Configuration::get('PS_MAIL_PASSWD') ? $this->l('Defined') : '<span style="color:red;">'.$this->l('Not defined').'</span>').'
			</p>
			<p>
				<b>'.$this->l('Encryption').':</b> 
				'.Configuration::get('PS_MAIL_SMTP_ENCRYPTION').'
			</p>
			<p>
				<b>'.$this->l('Port').':</b> 
				'.Configuration::get('PS_MAIL_SMTP_PORT').'
			</p>
			';
		}
		echo '
			<hr />
			<h3>'.$this->l('Your information').'</h3>
			<p>
				<b>'.$this->l('Information from you').':</b> 
				'.$_SERVER["HTTP_USER_AGENT"].'
			</p>
		</fieldset>
		<br />
		<fieldset id="checkConfiguration">
			<legend><img src="../img/t/AdminInformation.gif" alt="" /> '.$this->l('Check your configuration').'</legend>
			'.self::_getTestResultHtml().'
		</fieldset>
		';
	}
	
	static private function		check($tests)
	{
		$res = array();
		foreach ($tests AS $key => $test)
			$res[$key] = self::run($key, $test);
		return $res;
	}
	
	static private function		run($ptr, $arg = 0)
	{
		if (call_user_func(array('self', 'test_'.$ptr), $arg))
			return ('ok');
		return ('fail');
	}
	
	// Misc functions	
	static private function		test_phpversion()
	{
		return PHP_VERSION_ID >= 50000; /* PHP version > 5.0 */
	}
	
	static private function		test_mysql_support()
	{
		return function_exists('mysql_connect');
	}

	static private function		test_upload()
	{
		return  ini_get('file_uploads');
	}

	static private function		test_fopen()
	{
		return ini_get('allow_url_fopen');
	}

	static private function		test_system($funcs)
	{
		foreach ($funcs AS $func)
			if (!function_exists($func))
				return false;
		return true;
	}

	static private function		test_gd()
	{
		return function_exists('imagecreatetruecolor');
	}
	
	static private function		test_register_globals()
	{
		return !ini_get('register_globals');
	}
	
	static private function		test_gz()
	{
		if (function_exists('gzencode'))
			return !(@gzencode('dd') === false); 
		return false;
	}
	
	// is_writable dirs	
	static private function		test_dir($dir, $recursive = false)
	{
		if (!is_writable($dir) OR !$dh = opendir($dir))
			return false;
		if ($recursive)
		{
			while (($file = readdir($dh)) !== false)
				if (@filetype($dir.$file) == 'dir' AND $file != '.' AND $file != '..')
					if (!self::test_dir($dir.$file, true))
						return false;
		}
		closedir($dh);
		return true;
	}
	
	// is_writable files	
	static private function		test_file($file)
	{
		return (file_exists($file) AND is_writable($file));
	}
	
	static private function		test_config_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static private function		test_sitemap($dir)
	{
		return self::test_file($dir);
	}
	
	static private function		test_root_dir($dir)
	{
		return self::test_dir($dir);
	}

	static private function		test_admin_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static private function		test_img_dir($dir)
	{
		return self::test_dir($dir, true);
	}
	
	static private function		test_module_dir($dir)
	{
		return self::test_dir($dir, true);
	}
	
	static private function		test_tools_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static function		test_cache_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static private function		test_download_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static private function		test_mails_dir($dir)
	{
		return self::test_dir($dir, true);
	}
	
	static private function		test_translations_dir($dir)
	{
		return self::test_dir($dir, true);
	}
	
	static private function		test_theme_lang_dir($dir)
	{
		return self::test_dir($dir, true);
	}

	static private function		test_customizable_products_dir($dir)
	{
		return self::test_dir($dir);
	}
	
	static private function		test_virtual_products_dir($dir)
	{
		return self::test_dir($dir);
	}
}
