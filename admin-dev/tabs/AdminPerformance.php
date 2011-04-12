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

class AdminPerformance extends AdminTab
{
	public function postProcess()
	{
		global $currentIndex;
		
		if (Tools::isSubmit('submitCaching'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$settings = file_get_contents(dirname(__FILE__).'/../../config/settings.inc.php');
				if (!Tools::getValue('active'))
					$cache_active = 0;
				else	
					$cache_active = 1;
				if (!$caching_system = Tools::getValue('caching_system'))
					$this->_errors[] = Tools::displayError('Caching system is missing');
				else
					$settings = preg_replace('/define\(\'_PS_CACHING_SYSTEM_\', \'([a-z0-9=\/+-_]+)\'\);/Ui', 'define(\'_PS_CACHING_SYSTEM_\', \''.$caching_system.'\');', $settings);
				if ($cache_active AND $caching_system == 'MCached' AND !extension_loaded('memcache'))
					$this->_errors[] = Tools::displayError('To use Memcached, you must to install the Memcache PECL extension on your server.').' <a href="http://www.php.net/manual/en/memcache.installation.php">http://www.php.net/manual/en/memcache.installation.php</a>';
				elseif ($cache_active AND $caching_system == 'CacheFS' AND !is_writable(_PS_CACHEFS_DIRECTORY_))
					$this->_errors[] = Tools::displayError('To use CacheFS the directory').' '.realpath(_PS_CACHEFS_DIRECTORY_).' '.Tools::displayError('must be writable');

				if ($caching_system == 'CacheFS')
				{
					if (!($depth = Tools::getValue('ps_cache_fs_directory_depth')))
						$this->_errors[] = Tools::displayError('Please set a directory depth');
					if (!sizeof($this->_errors))
					{	
						CacheFS::deleteCacheDirectory();
						CacheFS::createCacheDirectories((int)$depth);
						Configuration::updateValue('PS_CACHEFS_DIRECTORY_DEPTH', (int)$depth);
					}
				}
				if (!sizeof($this->_errors))
				{
					$settings = preg_replace('/define\(\'_PS_CACHE_ENABLED_\', \'([0-9])\'\);/Ui', 'define(\'_PS_CACHE_ENABLED_\', \''.(int)$cache_active.'\');', $settings);
					if (file_put_contents(dirname(__FILE__).'/../../config/settings.inc.php', $settings))
						Tools::redirectAdmin($currentIndex.'&token='.Tools::getValue('token').'&conf=4');
					else
						$this->_errors[] = Tools::displayError('Cannot overwrite settings file.');
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		if (Tools::isSubmit('submitAddServer'))
		{
			if ($this->tabAccess['add'] === '1')
			{
				if (!Tools::getValue('memcachedIp'))
					$this->_errors[] = Tools::displayError('Memcached IP is missing');
				if (!Tools::getValue('memcachedPort'))
					$this->_errors[] = Tools::displayError('Memcached port is missing');
				if (!Tools::getValue('memcachedWeight'))
					$this->_errors[] = Tools::displayError('Memcached weight is missing');
				if (!sizeof($this->_errors))
				{
					if (MCached::addServer(pSQL(Tools::getValue('memcachedIp')), (int)Tools::getValue('memcachedPort'), (int)Tools::getValue('memcachedWeight')))
						Tools::redirectAdmin($currentIndex.'&token='.Tools::getValue('token').'&conf=4');
					else
						$this->_errors[] = Tools::displayError('Cannot add Memcached server');
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		if (Tools::getValue('deleteMemcachedServer'))
		{
			if ($this->tabAccess['add'] === '1')
			{
				if (MCached::deleteServer((int)Tools::getValue('deleteMemcachedServer')))
					Tools::redirectAdmin($currentIndex.'&token='.Tools::getValue('token').'&conf=4');
				else
					$this->_errors[] = Tools::displayError('Error in deleting Memcached server');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		
		if (Tools::isSubmit('submitCiphering') AND Configuration::get('PS_CIPHER_ALGORITHM') != (int)Tools::getValue('PS_CIPHER_ALGORITHM'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$algo = (int)Tools::getValue('PS_CIPHER_ALGORITHM');
				$settings = file_get_contents(dirname(__FILE__).'/../../config/settings.inc.php');
				if ($algo)
				{
					if (!function_exists('mcrypt_encrypt'))
						$this->_errors[] = Tools::displayError('Mcrypt is not activated on this server.');
					else
					{
						if (!strstr($settings, '_RIJNDAEL_KEY_'))
						{
							$key_size = mcrypt_get_key_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
							$key = Tools::passwdGen($key_size);
							$settings = preg_replace('/define\(\'_COOKIE_KEY_\', \'([a-z0-9=\/+-_]+)\'\);/i', 'define(\'_COOKIE_KEY_\', \'\1\');'."\n".'define(\'_RIJNDAEL_KEY_\', \''.$key.'\');', $settings);
						}
						if (!strstr($settings, '_RIJNDAEL_IV_'))
						{
							$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
							$iv = base64_encode(mcrypt_create_iv($iv_size, MCRYPT_RAND));
							$settings = preg_replace('/define\(\'_COOKIE_IV_\', \'([a-z0-9=\/+-_]+)\'\);/i', 'define(\'_COOKIE_IV_\', \'\1\');'."\n".'define(\'_RIJNDAEL_IV_\', \''.$iv.'\');', $settings);
						}
					}
				}
				if (!count($this->_errors))
				{
					if (file_put_contents(dirname(__FILE__).'/../../config/settings.inc.php', $settings))
					{
						Configuration::updateValue('PS_CIPHER_ALGORITHM', $algo);
						Tools::redirectAdmin($currentIndex.'&token='.Tools::getValue('token').'&conf=4');
					}
					else
						$this->_errors[] = Tools::displayError('Cannot overwrite settings file.');
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		
		if (Tools::isSubmit('submitCCC'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (
					!Configuration::updateValue('PS_CSS_THEME_CACHE', (int)Tools::getValue('PS_CSS_THEME_CACHE')) OR
					!Configuration::updateValue('PS_JS_THEME_CACHE', (int)Tools::getValue('PS_JS_THEME_CACHE')) OR
					!Configuration::updateValue('PS_HTML_THEME_COMPRESSION', (int)Tools::getValue('PS_HTML_THEME_COMPRESSION')) OR
					!Configuration::updateValue('PS_JS_HTML_THEME_COMPRESSION', (int)Tools::getValue('PS_JS_HTML_THEME_COMPRESSION')) OR
					!Configuration::updateValue('PS_HIGH_HTML_THEME_COMPRESSION', (int)Tools::getValue('PS_HIGH_HTML_THEME_COMPRESSION'))
				)
					$this->_errors[] = Tools::displayError('Unknown error.');
				else
					Tools::redirectAdmin($currentIndex.'&token='.Tools::getValue('token').'&conf=4');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		if (Tools::isSubmit('submitMediaServers'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Tools::getValue('_MEDIA_SERVER_1_') != NULL AND !Validate::isFileName(Tools::getValue('_MEDIA_SERVER_1_')))
					$this->_errors[] = Tools::displayError('Media server #1 is invalid');
				if (Tools::getValue('_MEDIA_SERVER_2_') != NULL AND !Validate::isFileName(Tools::getValue('_MEDIA_SERVER_2_')))
					$this->_errors[] = Tools::displayError('Media server #2 is invalid');
				if (Tools::getValue('_MEDIA_SERVER_3_') != NULL AND !Validate::isFileName(Tools::getValue('_MEDIA_SERVER_3_')))
					$this->_errors[] = Tools::displayError('Media server #3 is invalid');
				if (!sizeof($this->_errors))
				{
					$baseUrls = array();
					$baseUrls['_MEDIA_SERVER_1_'] = Tools::getValue('_MEDIA_SERVER_1_');
					$baseUrls['_MEDIA_SERVER_2_'] = Tools::getValue('_MEDIA_SERVER_2_');
					$baseUrls['_MEDIA_SERVER_3_'] = Tools::getValue('_MEDIA_SERVER_3_');
					rewriteSettingsFile($baseUrls, NULL, NULL);
					unset($this->_fieldsGeneral['_MEDIA_SERVER_1_']);
					unset($this->_fieldsGeneral['_MEDIA_SERVER_2_']);
					unset($this->_fieldsGeneral['_MEDIA_SERVER_3_']);
					Tools::redirectAdmin($currentIndex.'&token='.Tools::getValue('token').'&conf=4');
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		if (Tools::isSubmit('submitSmartyConfig'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				Configuration::updateValue('PS_SMARTY_FORCE_COMPILE', Tools::getValue('smarty_force_compile', 0));
				Configuration::updateValue('PS_SMARTY_CACHE', Tools::getValue('smarty_cache', 0));
				Tools::redirectAdmin($currentIndex.'&token='.Tools::getValue('token').'&conf=4');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		return parent::postProcess();
	}

	public function display()
	{
		global $currentIndex;

		$warnings = array();
		if (!extension_loaded('memcache'))
			$warnings[] = $this->l('To use Memcached, you must to install the Memcache PECL extension on your server.').' <a href="http://www.php.net/manual/en/memcache.installation.php">http://www.php.net/manual/en/memcache.installation.php</a>';
		if(!is_writable(_PS_CACHEFS_DIRECTORY_))
			$warnings[] = $this->l('To use CacheFS the directory').' '.realpath(_PS_CACHEFS_DIRECTORY_).' '.$this->l('must be writable');
	
		if ($warnings)
			$this->displayWarning($warnings);
	
		echo '<script type="text/javascript">
						$(document).ready(function() {
							showMemcached();
							$(\'#caching_system\').change(function() {
								showMemcached();
							});
							function showMemcached()
							{
								if ($(\'#caching_system option:selected\').val() == \'MCached\')
								{
									$(\'#memcachedServers\').show();
									$(\'#directory_depth\').hide();
								}
								else
								{
									$(\'#memcachedServers\').hide();
									$(\'#directory_depth\').show();
								}
							}
							$(\'#addMemcachedServer\').click(function() {
								$(\'#formMemcachedServer\').show();
								return false;
							});
						});
		</script>
		';
		
		echo '
		<form action="'.$currentIndex.'&token='.Tools::getValue('token').'" method="post" style="margin-top:10px;">
			<fieldset>
				<legend><img src="../img/admin/prefs.gif" /> '.$this->l('Smarty').'</legend>
				
				<label>'.$this->l('Force compile:').'</label>
				<div class="margin-form">
					<input type="radio" name="smarty_force_compile" id="smarty_force_compile_1" value="1" '.(Configuration::get('PS_SMARTY_FORCE_COMPILE') ? 'checked="checked"' : '').' /> <label class="t"><img src="../img/admin/enabled.gif" alt="" /> '.$this->l('Yes').'</label>
					<input type="radio" name="smarty_force_compile" id="smarty_force_compile_0" value="0" '.(!Configuration::get('PS_SMARTY_FORCE_COMPILE') ? 'checked="checked"' : '').' /> <label class="t"><img src="../img/admin/disabled.gif" alt="" /> '.$this->l('No').'</label>
					<p>'.$this->l('This forces Smarty to (re)compile templates on every invocation. This is handy for development and debugging. It should never be used in a production environment.').'</p>
				</div>
				<label>'.$this->l('Cache:').'</label>
				<div class="margin-form">
					<input type="radio" name="smarty_cache" id="smarty_cache_1" value="1" '.(Configuration::get('PS_SMARTY_CACHE') ? 'checked="checked"' : '').' /> <label class="t"><img src="../img/admin/enabled.gif" alt="" /> '.$this->l('Yes').'</label>
					<input type="radio" name="smarty_cache" id="smarty_cache_0" value="0" '.(!Configuration::get('PS_SMARTY_CACHE') ? 'checked="checked"' : '').' /> <label class="t"><img src="../img/admin/disabled.gif" alt="" /> '.$this->l('No').'</label>
					<p>'.$this->l('Should be enabled except for debugging.').'</p>
				</div>

				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitSmartyConfig" class="button" />
				</div>
			</fieldset>
		</form>';
		
		echo '
		<form action="'.$currentIndex.'&token='.Tools::getValue('token').'" method="post" style="margin-top:10px;">
			<fieldset>
				<legend><img src="../img/admin/arrow_in.png" /> '.$this->l('CCC (Combine, Compress and Cache)').'</legend>
				<p>'.$this->l('CCC allows you to reduce the loading time of your page. With these settings you will gain performance without even touching the code of your theme. Make sure, however, that your theme is compatible with PrestaShop 1.4+. Otherwise, CCC will cause problems.').'</p>
				<label>'.$this->l('Smart cache for CSS').' </label>
				<div class="margin-form">
					<input type="radio" value="1" name="PS_CSS_THEME_CACHE" id="PS_CSS_THEME_CACHE_1" '.(Configuration::get('PS_CSS_THEME_CACHE') ? 'checked="checked"' : '').' />
					<label class="t" for="PS_CSS_THEME_CACHE_1">'.$this->l('Use CCC for CSS.').'</label>
					<br />
					<input type="radio" value="0" name="PS_CSS_THEME_CACHE" id="PS_CSS_THEME_CACHE_0" '.(Configuration::get('PS_CSS_THEME_CACHE') ? '' : 'checked="checked"').' />
					<label class="t" for="PS_CSS_THEME_CACHE_0">'.$this->l('Keep CSS as original').'</label>
				</div>
				
				<label>'.$this->l('Smart cache for JavaScript').' </label>
				<div class="margin-form">
					<input type="radio" value="1" name="PS_JS_THEME_CACHE" id="PS_JS_THEME_CACHE_1" '.(Configuration::get('PS_JS_THEME_CACHE') ? 'checked="checked"' : '').' />
					<label class="t" for="PS_JS_THEME_CACHE_1">'.$this->l('Use CCC for JavaScript.').'</label>
					<br />
					<input type="radio" value="0" name="PS_JS_THEME_CACHE" id="PS_JS_THEME_CACHE_0" '.(Configuration::get('PS_JS_THEME_CACHE') ? '' : 'checked="checked"').' />
					<label class="t" for="PS_JS_THEME_CACHE_0">'.$this->l('Keep JavaScript as original').'</label>
				</div>
				
				<label>'.$this->l('Minify HTML').' </label>
				<div class="margin-form">
					<input type="radio" value="1" name="PS_HTML_THEME_COMPRESSION" id="PS_HTML_THEME_COMPRESSION_1" '.(Configuration::get('PS_HTML_THEME_COMPRESSION') ? 'checked="checked"' : '').' />
					<label class="t" for="PS_HTML_THEME_COMPRESSION_1">'.$this->l('Minify HTML after "smarty compile" execution.').'</label>
					<br />
					<input type="radio" value="0" name="PS_HTML_THEME_COMPRESSION" id="PS_HTML_THEME_COMPRESSION_0" '.(Configuration::get('PS_HTML_THEME_COMPRESSION') ? '' : 'checked="checked"').' />
					<label class="t" for="PS_HTML_THEME_COMPRESSION_0">'.$this->l('Keep HTML as original').'</label>
				</div>
				
				<label>'.$this->l('Compress inline JavaScript in HTML').' </label>
				<div class="margin-form">
					<input type="radio" value="1" name="PS_JS_HTML_THEME_COMPRESSION" id="PS_JS_HTML_THEME_COMPRESSION_1" '.(Configuration::get('PS_JS_HTML_THEME_COMPRESSION') ? 'checked="checked"' : '').' />
					<label class="t" for="PS_JS_HTML_THEME_COMPRESSION_1">'.$this->l('Compress inline JavaScript in HTML after "smarty compile" execution').'</label>
					<br />
					<input type="radio" value="0" name="PS_JS_HTML_THEME_COMPRESSION" id="PS_JS_HTML_THEME_COMPRESSION_0" '.(Configuration::get('PS_JS_HTML_THEME_COMPRESSION') ? '' : 'checked="checked"').' />
					<label class="t" for="PS_JS_HTML_THEME_COMPRESSION_0">'.$this->l('Keep inline JavaScript in HTML as original').'</label>
				</div>
				
				<label>'.$this->l('High risk HTML compression').' </label>
				<div class="margin-form">
					<input type="radio" value="1" name="PS_HIGH_HTML_THEME_COMPRESSION" id="PS_HIGH_HTML_THEME_COMPRESSION_1" '.(Configuration::get('PS_HIGH_HTML_THEME_COMPRESSION') ? 'checked="checked"' : '').' />
					<label class="t" for="PS_HIGH_HTML_THEME_COMPRESSION_1">'.$this->l('HTML is compressed but cancels the W3C validation (only when "Minify HTML" is enabled)').'</label>
					<br />
					<input type="radio" value="0" name="PS_HIGH_HTML_THEME_COMPRESSION" id="PS_HIGH_HTML_THEME_COMPRESSION_0" '.(Configuration::get('PS_HIGH_HTML_THEME_COMPRESSION') ? '' : 'checked="checked"').' />
					<label class="t" for="PS_HIGH_HTML_THEME_COMPRESSION_0">'.$this->l('Keep W3C validation').'</label>
				</div>
				
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitCCC" class="button" />
				</div>
			</fieldset>
		</form>';
		
		echo '<form action="'.$currentIndex.'&token='.Tools::getValue('token').'" method="post" style="margin-top:10px;">
			<fieldset>
				<legend><img src="../img/admin/subdomain.gif" /> '.$this->l('Media servers (used only with CCC)').'</legend>
				<p>'.$this->l('You must enter another domain or subdomain in order to use cookieless static content.').'</p>
				<label for="_MEDIA_SERVER_1_">'.$this->l('Media server #1').'</label>
				<div class="margin-form">
					<input type="text" name="_MEDIA_SERVER_1_" id="_MEDIA_SERVER_1_" value="'.htmlentities(Tools::getValue('_MEDIA_SERVER_1_', _MEDIA_SERVER_1_), ENT_QUOTES, 'UTF-8').'" size="30" />
					<p>'.$this->l('Name of the second domain of your shop, (e.g., myshop-media-server-1.com). If you do not have another domain, leave this field blank').'</p>
				</div>
				<label for="_MEDIA_SERVER_2_">'.$this->l('Media server #2').'</label>
				<div class="margin-form">
					<input type="text" name="_MEDIA_SERVER_2_" id="_MEDIA_SERVER_2_" value="'.htmlentities(Tools::getValue('_MEDIA_SERVER_2_', _MEDIA_SERVER_2_), ENT_QUOTES, 'UTF-8').'" size="30" />
					<p>'.$this->l('Name of the third domain of your shop, (e.g., myshop-media-server-2.com). If you do not have another domain, leave this field blank').'</p>
				</div>
				<label for="_MEDIA_SERVER_3_">'.$this->l('Media server #3').'</label>
				<div class="margin-form">
					<input type="text" name="_MEDIA_SERVER_3_" id="_MEDIA_SERVER_3_" value="'.htmlentities(Tools::getValue('_MEDIA_SERVER_3_', _MEDIA_SERVER_3_), ENT_QUOTES, 'UTF-8').'" size="30" />
					<p>'.$this->l('Name of the fourth domain of your shop, (e.g., myshop-media-server-3.com). If you do not have another domain, leave this field blank').'</p>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitMediaServers" class="button" />
				</div>
			</fieldset>
		</form>';

		echo '
		<fieldset style="margin-top:10px;">
			<legend><img src="../img/admin/computer_key.png" /> '.$this->l('Ciphering').'</legend>
			<form action="'.$currentIndex.'&token='.Tools::getValue('token').'" method="post">
				<p>'.$this->l('Mcrypt is faster than our custom BlowFish class, but requires the PHP extension "mcrypt". If you change this configuration, all cookies will be reset.').'</p>
				<label>'.$this->l('Algorithm').' </label>
				<div class="margin-form">
					<input type="radio" value="1" name="PS_CIPHER_ALGORITHM" id="PS_CIPHER_ALGORITHM_1" '.(Configuration::get('PS_CIPHER_ALGORITHM') ? 'checked="checked"' : '').' />
					<label class="t" for="PS_CIPHER_ALGORITHM_1">'.$this->l('Use Rijndael with mcrypt lib.').'</label>
					<br />
					<input type="radio" value="0" name="PS_CIPHER_ALGORITHM" id="PS_CIPHER_ALGORITHM_0" '.(Configuration::get('PS_CIPHER_ALGORITHM') ? '' : 'checked="checked"').' />
					<label class="t" for="PS_CIPHER_ALGORITHM_0">'.$this->l('Keep the custom BlowFish class.').'</label>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitCiphering" class="button" />
				</div>
			</form>
		</fieldset>
		';
		
		$depth = Configuration::get('PS_CACHEFS_DIRECTORY_DEPTH');
		echo '<fieldset style="margin-top: 10px;">
				<legend><img src="../img/admin/computer_key.png" /> '.$this->l('Caching').'</legend>
				<form action="'.$currentIndex.'&token='.Tools::getValue('token').'"  method="post">
					<label>'.$this->l('Use cache:').' </label>
					<div class="margin-form">
						<input type="radio" name="active" id="active_on" value="1" '.(_PS_CACHE_ENABLED_ ? 'checked="checked" ' : '').'/>
						<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
						<input type="radio" name="active" id="active_off" value="0" '.(!_PS_CACHE_ENABLED_ ? 'checked="checked" ' : '').'/>
						<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
						<p>'.$this->l('Enable or disable caching system').'</p>
					</div>
					<label>'.$this->l('Caching system:').' </label>
					<div class="margin-form">
						<select name="caching_system" id="caching_system">
							<option value="MCached" '.(_PS_CACHING_SYSTEM_ == 'MCached' ? 'selected="selected"' : '' ).'>'.$this->l('Memcached').'</option>
							<option value="CacheFS" '.(_PS_CACHING_SYSTEM_ == 'CacheFS' ? 'selected="selected"' : '' ).'>'.$this->l('File System').'</option>
						</select>
					</div>
					<div id="directory_depth">
						<label>'.$this->l('Directory depth:').' </label>
						<div class="margin-form">
							<input type="text" name="ps_cache_fs_directory_depth" value="'.($depth ? $depth : 1).'" />
						</div>
					</div>
					<div class="margin-form">
						<input type="submit" value="'.$this->l('   Save   ').'" name="submitCaching" class="button" />
					</div>
				</form>
				<div id="memcachedServers">
					<div class="margin-form">
						<a id="addMemcachedServer" href="#" ><img src="../img/admin/add.gif" />'.$this->l('Add server').'</a>
					</div>
					<div id="formMemcachedServer" style="margin-top: 10px; display:none;">
						<form action="'.$currentIndex.'&token='.Tools::getValue('token').'" method="post">
							<label>'.$this->l('IP Address:').' </label>
							<div class="margin-form">
								<input type="text" name="memcachedIp" />
							</div>
							<label>'.$this->l('Port:').' </label>
							<div class="margin-form">
								<input type="text" name="memcachedPort" value="11211" />
							</div>
							<label>'.$this->l('Weight:').' </label>
							<div class="margin-form">
								<input type="text" name="memcachedWeight" value="1" />
							</div>
							<div class="margin-form">
								<input type="submit" value="'.$this->l('   Add Server   ').'" name="submitAddServer" class="button" />
							</div>
						</form>
					</div>';
			$servers = MCached::getMemcachedServers();
			if ($servers)					
			{
				echo '<div class="margin-form">
					<table style="width: 320px;" cellspacing="0" cellpadding="0" class="table">
					<tr>
						<th style="width: 20px; text-align: center">'.$this->l('Id').'</th>
						<th style="width: 200px; text-align: center">'.$this->l('Ip').'</th>
						<th style="width: 50px; text-align: center">'.$this->l('Port').'</th>
						<th style="width: 30px; text-align: right; font-weight: bold;">'.$this->l('Weight').'</th>
						<th style="width: 20px; text-align: right;">&nbsp;</th>
					</tr>';
				foreach($servers AS $server)
					echo '<tr>
							<td>'.$server['id_memcached_server'].'</td>
							<td>'.$server['ip'].'</td>
							<td>'.$server['port'].'</td>
							<td>'.$server['weight'].'</td>
							<td>
								<a href="'.$currentIndex.'&token='.Tools::getValue('token').'&deleteMemcachedServer='.(int)$server['id_memcached_server'].'" ><img src="../img/admin/delete.gif" /></a>
							</td>
						</tr>';
				echo '
					</table>
				</div>';
			}
			echo '
				</div>
			</fieldset>';
	}
}


