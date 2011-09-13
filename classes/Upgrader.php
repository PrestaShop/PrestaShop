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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class UpgraderCore{
	const DEFAULT_CHECK_VERSION_DELAY_HOURS = 24;
	public $rss_version_link = 'http://www.prestashop.com/xml/version.xml';
	/**
	 * link contains hte url where to download the file
	 * 
	 * @var string 
	 */
	private $needUpgrade = false;
	private $noRefresh = false;

	public $version_name;
	public $version_num;
	public $link;
	public $autoupgrade;
	public $autoupgrade_module;
	public $changelog;
	public $md5;

	public function __get($var)
	{
		if($var == 'needUpgrade')
			return $this->isLastVersion();
	}
	/**
	 * we need to checkPSVersion when we use that class
	 * @param boolean noRefresh if true, checkPSVersion will not refresh its information
	 * @return object Upgrader 
	 */
	public function __construct($noRefresh = false)
	{
		$this->noRefresh = (bool)$noRefresh;
	}

	/**
	 * downloadLast download the last version of PrestaShop and save it in $dest/$filename
	 * 
	 * @param string $dest directory where to save the file
	 * @param string $filename new filename
	 * @return boolean
	 *
	 * @TODO ftp if copy is not possible (safe_mode for example)
	 */
	public function downloadLast($dest, $filename = 'prestashop.zip')
	{
		if (empty($this->link))
			$this->checkPSVersion();

		if (@copy($this->link, realpath($dest).DIRECTORY_SEPARATOR.$filename))
			return true;
		else
			return false;
	}
	public function isLastVersion()
	{
		if (empty($this->link))
			$this->checkPSVersion();

		return $this->needUpgrade;

	}

	/**
	 * checkPSVersion ask to prestashop.com if there is a new version. return an array if yes, false otherwise
	 * 
	 * @return mixed
	 */
	public function checkPSVersion($force = false)
	{
		if (empty($this->link))
		{

			$lastCheck = Configuration::get('PS_LAST_VERSION_CHECK');
			// if we use the autoupgrade process, we will never refresh it
			// except if no check has been done before
			if ($force OR ($lastCheck < time() - (3600 * Upgrader::DEFAULT_CHECK_VERSION_DELAY_HOURS)) )
			{
				libxml_set_streams_context(stream_context_create(array('http' => array('timeout' => 3))));
				if ($feed = @simplexml_load_file($this->rss_version_link))
				{

					$this->version_name = (string)$feed->version->name;
					$this->version_num = (string)$feed->version->num;
					$this->link = (string)$feed->download->link;
					$this->md5 = (string)$feed->download->md5;
					$this->changelog = (string)$feed->download->changelog;
					$this->autoupgrade = (int)$feed->autoupgrade;
					$this->autoupgrade_module = (int)$feed->autoupgrade_module;
					$this->desc = (string)$feed->desc ;
					$configLastVersion = array(
						'name' => $this->version_name,
						'num' => $this->version_num,
						'link' => $this->link,
						'md5' => $this->md5,
						'autoupgrade' => $this->autoupgrade,
						'autoupgrade_module' => $this->autoupgrade_module,
						'changelog' => $this->changelog,
						'desc' => $this->desc
					);

					Configuration::updateValue('PS_LAST_VERSION',serialize($configLastVersion));
					Configuration::updateValue('PS_LAST_VERSION_CHECK',time());
				}
			}
			else
			{
				$lastVersionCheck = @unserialize(Configuration::get('PS_LAST_VERSION'));
				$this->version_name = $lastVersionCheck['name'];
				$this->version_num = $lastVersionCheck['num'];
				$this->link = $lastVersionCheck['link'];
				$this->autoupgrade = $lastVersionCheck['autoupgrade'];
				$this->autoupgrade_module = $lastVersionCheck['autoupgrade_module'];
				$this->md5 = $lastVersionCheck['md5'];
				$this->desc = $lastVersionCheck['desc'];
				$this->changelog = $lastVersionCheck['changelog'];
			}
		}
		// retro-compatibility :
		// return array(name,link) if you don't use the last version
		// false otherwise
		if (version_compare(_PS_VERSION_, $this->version_num, '<'))
		{
			$this->needUpgrade = true;
			return array('name' => $this->version_name, 'link' => $this->link);
		}
		else
			return false;
	}

}
