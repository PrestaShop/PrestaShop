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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2011 PrestaShop SA
*	@version	Release: $Revision$
*	@license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*/

require_once(_PS_ADMIN_DIR_.'/tabs/AdminPreferences.php');

class AdminUpgrade extends AdminPreferences
{
	public $ajax = false;
	public $nextResponseType = 'json'; // json, xml
	public $next = 'N/A';
	
	/**
	 * set to false if the current step is a loop
	 *
	 * @var boolean
	 */
	public $stepDone = true;
	public $error ='0';
	public $nextDesc = '.';
	public $nextParams = array();
	public $nextQuickInfo = array();
	public $currentParams = array();
	public $autoupgradePath = '';
	/**
	 * autoupgradeDir 
	 * 
	 * @var string directory relative to admin dir
	 */
	public $autoupgradeDir = 'autoupgrade';
	public $latestRootDir = '';
	public $prodRootDir = '';
	public $rootWritable = false;
	public $svnDir = 'svn';
	public $destDownloadFilename = 'prestashop.zip';
	public $toUpgradeFileList = array();
	public $backupFileList = array();
	public $sampleFileList = array();
	private $backupIgnoreFiles = array();
	private $backupIgnoreAbsoluteFiles = array();
	private $excludeFilesFromUpgrade = array();
	private $excludeAbsoluteFilesFromUpgrade = array();
	
/**
 * int loopBackupFiles : if your server has a low memory size, lower this value
 * @TODO remove the static, add a const, and use it like this : min(AdminUpgrade::DEFAULT_LOOP_ADD_FILE_TO_ZIP,Configuration::get('LOOP_ADD_FILE_TO_ZIP');
 */
	public static $loopBackupFiles = 1000;
/**
 * int loopUpgradeFiles : if your server has a low memory size, lower this value
 */
	public static $loopUpgradeFiles = 1000;
/**
 * intloopRemoveSamples : if your server has a low memory size, lower this value
 */
	public static $loopRemoveSamples = 1000;

//	public static $skipAction = array('unzip'=>'listSampleFiles');
	public static $skipAction;
	public $useSvn;

	protected $_includeContainer = false;

	public function __construct()
	{
		Tools::displayAsDeprecated();
		@set_time_limit(0);
		@ini_set('max_execution_time', '0');
		
		$this->init();
		parent::__construct();
	}

	/**
	 * _setFields function to set fields (only when we need it).
	 * 
	 * @return void
	 */
	private function _setFields()
	{
		$this->_fieldsAutoUpgrade['PS_AUTOUP_DONT_SAVE_IMAGES'] = array(
			'title' => $this->l('Don\'t save images'), 'cast' => 'intval', 'validation' => 'isBool',
			'type' => 'bool', 'desc'=>$this->l('You can exclude the image directory from backup if you already saved it by another method (not recommended)'),
		);

		$this->_fieldsAutoUpgrade['PS_AUTOUP_KEEP_DEFAULT_THEME'] = array(
			'title' => $this->l('Keep theme "prestashop"'), 'cast' => 'intval', 'validation' => 'isBool',
			'type' => 'bool', 'desc'=>$this->l('If you have customized PrestaShop default theme, you can protect it from upgrade (not recommended)'),
		);

		$this->_fieldsAutoUpgrade['PS_AUTOUP_KEEP_TRAD'] = array(
			'title' => $this->l('Keep translations'), 'cast' => 'intval', 'validation' => 'isBool',
			'type' => 'bool', 'desc'=>$this->l('If set too yes, you will keep all your translations'),
		);
		// allow manual mode only for dev
		if (defined('_PS_MODE_DEV_') AND _PS_MODE_DEV_)
			$this->_fieldsAutoUpgrade['PS_AUTOUP_MANUAL_MODE'] = array(
				'title' => $this->l('Manual mode'),	'cast' => 'intval',	'validation' => 'isBool',
				'type' => 'bool',	'desc'=>$this->l('Check this if you want to stop after each step'),
			);

		if (defined('_PS_ALLOW_UPGRADE_UNSTABLE_') AND _PS_ALLOW_UPGRADE_UNSTABLE_ AND function_exists('svn_checkout'))
		{
			$this->_fieldsAutoUpgrade['PS_AUTOUP_USE_SVN'] = array(
				'title' => $this->l('Use Subversion'), 'cast' => 'intval', 'validation' => 'isBool',
				'type' => 'bool',	'desc' => $this->l('check this if you want to use unstable svn instead of official release'),
			);
		}
	}
	
	/**
	 * isUpgradeAllowed checks if all server configuration is valid for upgrade
	 * 
	 * @return void
	 */
	public function isUpgradeAllowed()
	{
		$allowed = (ConfigurationTest::test_fopen() && $this->rootWritable);

		if (!defined('_PS_MODE_DEV_') OR !_PS_MODE_DEV_)
			$allowed &= $this->upgrader->autoupgrade;

		return $allowed;
	}

	/**
	 * init to build informations we need
	 *
	 * @return void
	 */
	public function init()
	{
		// For later use, let's set up prodRootDir and adminDir
		// This way it will be easier to upgrade a different path if needed
		$this->prodRootDir = _PS_ROOT_DIR_;
		$this->adminDir = _PS_ADMIN_DIR_;

		// test writable recursively
		if (ConfigurationTest::test_dir($this->prodRootDir,true))
			$this->rootWritable = true;

		// checkPSVersion will be not 
		$this->upgrader = new Upgrader(true);
		$this->upgrader->checkPSVersion();
		// If you have defined this somewhere, you know what you do
		if (defined('_PS_ALLOW_UPGRADE_UNSTABLE_') AND _PS_ALLOW_UPGRADE_UNSTABLE_ AND function_exists('svn_checkout'))
		{
			$this->useSvn = Configuration::get('PS_AUTOUP_USE_SVN');
		}
		else
			$this->useSvn = false;
		
		// from $_POST or $_GET
		$this->action = Tools::getValue('action');
		$this->currentParams = Tools::getValue('params');

		// If not exists in this sessions, "create"
		// session handling : from current to next params
		if (isset($this->currentParams['removeList']))
			$this->nextParams['removeList'] = $this->currentParams['removeList'];

		if (isset($this->currentParams['filesToUpgrade']))
			$this->nextParams['filesToUpgrade'] = $this->currentParams['filesToUpgrade'];

		$this->backupDbFilename = Configuration::get('UPGRADER_BACKUPDB_FILENAME');
		if(!file_exists($this->backupDbFilename))
		{
			$this->backupDbFilename = '';
			Configuration::updateValue('UPGRADER_BACKUPDB_FILENAME','');
		}
		$this->backupFilesFilename = Configuration::get('UPGRADER_BACKUPFILES_FILENAME');
		if(!file_exists($this->backupFilesFilename))
		{
			$this->backupFilesFilename = '';
			Configuration::updateValue('UPGRADER_BACKUPFILES_FILENAME','');
		}


		$this->autoupgradePath = $this->adminDir.DIRECTORY_SEPARATOR.$this->autoupgradeDir;

		if (!file_exists($this->autoupgradePath))
			if (!@mkdir($this->autoupgradePath,0777))
				$this->_errors[] = Tools::displayError(sprintf($this->l('unable to create directory %s'),$this->autoupgradePath));

		$latest = $this->autoupgradePath.DIRECTORY_SEPARATOR.'latest';
		if (!file_exists($latest))
			if (!@mkdir($latest,0777))
				$this->_errors[] = Tools::displayError(sprintf($this->l('unable to create directory %s'),$latest));

		$this->latestRootDir = $latest.DIRECTORY_SEPARATOR.'prestashop';
		$this->adminDir = str_replace($this->prodRootDir,'',$this->adminDir);
		// @TODO future option
		//	$this->testRootDir = $this->autoupgradePath.DIRECTORY_SEPARATOR.'test';

		/* optional skips */
		$this->dontBackupImages = Configuration::get('PS_AUTOUP_DONT_SAVE_IMAGES');
		$this->keepDefaultTheme = Configuration::get('PS_AUTOUP_KEEP_DEFAULT_THEME');
		$this->keepTrad = Configuration::get('PS_AUTOUP_KEEP_TRAD');
		$this->manualMode = Configuration::get('PS_AUTOUP_MANUAL_MODE');		
		// We can add any file or directory in the exclude dir : theses files will be not removed or overwritten	
		// @TODO cache should be ignored recursively, but we have to reconstruct it after upgrade
		// - compiled from smarty
		// - .svn
		$this->backupIgnoreAbsoluteFiles[] = "/tools/smarty_v2/compile";
		$this->backupIgnoreAbsoluteFiles[] = "/tools/smarty_v2/cache";
		$this->backupIgnoreAbsoluteFiles[] = "/tools/smarty/compile";
		$this->backupIgnoreAbsoluteFiles[] = "/tools/smarty/cache";

		$this->excludeFilesFromUpgrade[] = '.';
		$this->excludeFilesFromUpgrade[] = '..';
		$this->excludeFilesFromUpgrade[] = '.svn';
		$this->excludeFilesFromUpgrade[] = 'install';
		$this->excludeFilesFromUpgrade[] = 'settings.inc.php';
		$this->excludeFilesFromUpgrade[] = 'autoupgrade';
		$this->backupIgnoreFiles[] = '.';
		$this->backupIgnoreFiles[] = '..';
		$this->backupIgnoreFiles[] = '.svn';
		$this->backupIgnoreFiles[] = 'autoupgrade';

		if ($this->dontBackupImages)
			$this->backupIgnoreAbsoluteFiles[] = "/img";
		
		
		if ($this->keepDefaultTheme)
			$this->excludeAbsoluteFilesFromUpgrade[] = "/themes/prestashop";
			
		if ($this->keepTrad)
			$this->excludeFilesFromUpgrade[] = "translations";
	}

	/**
	 * getFilePath return the path to the zipfile containing prestashop.
	 * 
	 * @return void
	 */
	private function getFilePath()
	{
		return $this->autoupgradePath.DIRECTORY_SEPARATOR.$this->destDownloadFilename;
	}

	public function postProcess()
	{
		$this->_setFields();

		if (!empty($_POST))
			$this->_postConfig($this->_fieldsAutoUpgrade);
	}

	public function ajaxProcessUpgradeComplete()
	{
		$this->nextDesc = $this->l('Upgrade process done. Congratulations ! You can now reactive your shop.');
		$this->next = '';
	}

	public function ajaxProcessUpgradeNow()
	{
		$this->nextDesc = $this->l('Starting upgrade ...');
		$this->next = 'desactiveShop';
	}
	public function ajaxProcessSvnExport()
	{
		if ($this->useSvn)
		{
			// first of all, delete the content of the latest root dir just in case
			if (is_dir($this->latestRootDir))
				Tools::deleteDirectory($this->latestRootDir, false);

			if (!file_exists($this->latestRootDir))
			{
				@mkdir($this->latestRootDir);
			}

			if (svn_export($this->autoupgradePath . DIRECTORY_SEPARATOR . $this->svnDir, $this->latestRootDir))
			{

				// export means svn means install-dev and admin-dev.
				// let's rename admin to the correct admin dir
				// and rename install-dev to install
				$adminDir = str_replace($this->prodRootDir, '', $this->adminDir);
				rename($this->latestRootDir.DIRECTORY_SEPARATOR.'install-dev', $this->latestRootDir.DIRECTORY_SEPARATOR.'install');
				rename($this->latestRootDir.DIRECTORY_SEPARATOR.'admin-dev', $this->latestRootDir.DIRECTORY_SEPARATOR.$adminDir);

				// Unsetting to force listing
				unset($this->nextParams['removeList']);
				$this->next = "removeSamples";
				$this->nextDesc = $this->l('Export svn complete. removing sample files...');
				return true;
			}
			else
			{
				$this->next = 'error';
				$this->nextDesc = $this->l('error when svn export ');
			}
		}
	}

	public function ajaxProcessUnzip(){
		// @TODO : not require_once like that.
		$filepath = $this->getFilePath();
		$destExtract = $this->autoupgradePath.DIRECTORY_SEPARATOR.'latest';
		if (file_exists($destExtract))
			Tools::deletedirectory($destExtract);

		if (Tools::ZipExtract($filepath,$destExtract))
		{
				$adminDir = str_replace($this->prodRootDir, '', $this->adminDir);
				rename($this->latestRootDir.DIRECTORY_SEPARATOR.'admin', $this->latestRootDir.DIRECTORY_SEPARATOR.$adminDir);
				// Unsetting to force listing
				unset($this->nextParams['removeList']);
				$this->next = "removeSamples";
				$this->nextDesc = $this->l('Extract complete. removing sample files...');
				return true;
		}
		else{
				$this->next = "error";
				$this->nextDesc = sprintf($this->l('unable to extract %1$s into %2$s ...'),$filepath,$destExtract);
				return true;
		}
	}


	/**
	 * _listSampleFiles will make a recursive call to scandir() function
	 * and list all file which match to the $fileext suffixe (this can be an extension or whole filename)
	 *
	 * @TODO maybe $regex instead of $fileext ?
	 * @param string $dir directory to look in
	 * @param string $fileext suffixe filename
	 * @return void
	 */
	private function _listSampleFiles($dir, $fileext = '.jpg'){
		$res = true;
		$dir = rtrim($dir,'/').DIRECTORY_SEPARATOR;

		$toDel = scandir($dir);
		// copied (and kind of) adapted from AdminImages.php
		foreach ($toDel AS $file)
		{
			if ($file!='.' AND $file != '..' AND $file != '.svn')
			{

				if (preg_match('#'.preg_quote($fileext,'#').'$#i',$file))
				{
					$this->sampleFileList[] = $dir.$file;
				}
				else if (is_dir($dir.$file))
				{
					$res &= $this->_listSampleFiles($dir.$file);
				}
			}
		}
		return $res;
	}

	public function _listBackupFiles($dir)
	{
		$allFiles = scandir($dir);
		foreach ($allFiles as $file)
		{
			$fullPath = $dir.DIRECTORY_SEPARATOR.$file;
			
			if (!$this->_skipFile($file, $fullPath,'backup'))
			{
					if (is_dir($fullPath))
						$this->_listBackupFiles($fullPath);
					else
						$this->backupFileList[] = $fullPath;					
			}
			else
					$this->backupIgnoreFiles[] = $fullPath;

		}
	}

	public function _listFilesToUpgrade($dir)
	{
		$allFiles = scandir($dir);
		foreach ($allFiles as $file)
		{
			$fullPath = $dir.DIRECTORY_SEPARATOR.$file;
			
			if (!$this->_skipFile($file, $fullPath, "upgrade"))
			{
				if (is_dir($fullPath))
				{
						// if is_dir, we will create it :)e it :)
						$this->toUpgradeFileList[] = $fullPath;
							if (strpos($dir.DIRECTORY_SEPARATOR.$file, 'install') === false)
							{
								$this->_listFilesToUpgrade($fullPath);
							}
				}
				else
						$this->toUpgradeFileList[] = $fullPath;
			}
		}

		$this->nextParams['filesToUpgrade'] = $this->toUpgradeFileList;
	}

	public function ajaxProcessDesactiveShop()
	{
		$this->ShopActiveValue = Configuration::get('PS_SHOP_ENABLE');
		Configuration::updateValue('PS_SHOP_ENABLE',0);
		if ($this->useSvn)
		{
			$this->next = 'svnCheckout';
			$this->nextDesc = $this->l('switching to svn checkout (useSvn set to true)');
		}
		else
		{
			$this->next = 'download';
			$this->nextDesc = $this->l('Shop desactivated. Now downloading (this can takes some times )...');
		}
	}

	public function ajaxProcessUpgradeFiles(){
		// @TODO :
		$this->nextParams = $this->currentParams;
		if (!isset($this->nextParams['filesToUpgrade']))
			$this->_listFilesToUpgrade($this->latestRootDir);

		// later we could choose between _PS_ROOT_DIR_ or _PS_TEST_DIR_
		$this->destUpgradePath = $this->prodRootDir;

		// upgrade files one by one like for the backup
		// with a 1000 loop because it's funny
		// @TODO :
		// foreach files in latest, copy
		$this->next = 'upgradeFiles';
		if (!is_array($this->nextParams['filesToUpgrade']))
		{
			error($this->nextParams);
			$this->next = 'error';
			$this->nextDesc = $this->l('filesToUpgrade is not an array');
			$this->nextQuickInfo[] = $this->l('filesToUpgrade is not an array');
			return false;
		}

		// @TODO : does not upgrade files in modules, translations if they have not a correct md5 (or crc32, or whatever) from previous version
		for ($i=0;$i<self::$loopUpgradeFiles;$i++)
		{
			if (sizeof($this->nextParams['filesToUpgrade'])<=0)
			{
				$this->next = 'upgradeDb';
				$this->nextDesc = $this->l('All files upgraded. Now upgrading database');
				$this->nextResponseType = 'xml';
				break;
			}

			//$file = array_shift($this->nextParams['filesToUpgrade']);
			$file = array_shift($this->nextParams['filesToUpgrade']);
			if (!$this->upgradeThisFile($file))
			{
				// put the file back to the begin of the list
				$totalFiles = array_unshift($this->nextParams['filesToUpgrade'],$file);
				$this->next = 'error';
				$this->nextQuickInfo[] = sprintf($this->l('error when trying to upgrade %s'),$file);
				break;
			}
			else{
				// @TODO : maybe put several files at the same times ?
				$this->nextDesc = sprintf($this->l('%2$s files left to upgrade.'),$file,sizeof($this->nextParams['filesToUpgrade']));
			}
		}
	}

	/**
	 * model_doUpgrade prepare the call to doUpgrade.php file (like model.php)
	 *
	 * @return void
	 */
	public function _modelDoUpgrade()
	{
		// a. set logger
		// it will be used later
		global $logger;
		$logger = new FileLogger();
		if (function_exists('date_default_timezone_set'))
			date_default_timezone_set('Europe/Paris');
		// use autoupgrade as log dir
		$logger->setFilename($this->latestRootDir.'/'.date('Ymd').'_autoupgrade.log');

		// init env.
		@set_time_limit(0);
		@ini_set('max_execution_time', '0');
		// setting the memory limit to 128M only if current is lower
		$memory_limit = ini_get('memory_limit');
		if (substr($memory_limit,-1) != 'G'
			AND ((substr($memory_limit,-1) == 'M' AND substr($memory_limit,0,-1) < 128)
			OR is_numeric($memory_limit) AND (intval($memory_limit) < 131072))
		){
			@ini_set('memory_limit','128M');
		}
		
		/* Redefine REQUEST_URI if empty (on some webservers...) */
		if (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] == '')
			$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		
		if ($tmp = strpos($_SERVER['REQUEST_URI'], '?'))
			$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, $tmp);
			
		$_SERVER['REQUEST_URI'] = str_replace('//', '/', $_SERVER['REQUEST_URI']);

		///////////////////////
		// Copy from model.php
		///////////////////////
		$upgrader = $this->upgrader;
		$upgrader->checkPSVersion();

		define('INSTALL_VERSION', $upgrader->version_num);
		// now the install dir to use is in a subdirectory of the admin dir
		define('INSTALL_PATH', realpath($this->latestRootDir.DIRECTORY_SEPARATOR.'install'));

		define('PS_INSTALLATION_IN_PROGRESS', true);
		// Note : we don't need ToolsInstall.php
		// include_once(INSTALL_PATH.'/classes/ToolsInstall.php');

		define('SETTINGS_FILE', $this->prodRootDir . '/config/settings.inc.php');
		define('DEFINES_FILE',	$this->prodRootDir .'/config/defines.inc.php');
		define('INSTALLER__PS_BASE_URI', substr($_SERVER['REQUEST_URI'], 0, -1 * (strlen($_SERVER['REQUEST_URI']) - strrpos($_SERVER['REQUEST_URI'], '/')) - strlen(substr(dirname($_SERVER['REQUEST_URI']), strrpos(dirname($_SERVER['REQUEST_URI']), '/')+1))));

		// Note : INSTALLER__PS_BASE_URI_ABSOLUTE is not used for upgrade
		// define('INSTALLER__PS_BASE_URI_ABSOLUTE', 'http://'.ToolsInstall::getHttpHost(false, true).INSTALLER__PS_BASE_URI);

		// XML Header
		header('Content-Type: text/xml');
		require_once(INSTALL_PATH.DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'doUpgrade.php');
		
		//////////////////////////////
		// End of copy from model.php
		//////////////////////////////
		
	}

	public function ajaxProcessUpgradeDb(){
		// @TODO : 1/2/3 have to be done at the beginning !!!!!!!!!!!!!!!!!!!!!!

		// use something like actual in install-dev
		// Notice : xml used here ...

		// 1) confirm version is correct(DB)
		// install/model.php?method=getVersionFromDb&language=0
		// later

		// 2) confirm config is correct (r/w rights)
		//	install/model.php?method=checkConfig&firsttime=0
		// later

		// 3) save current activated modules in nextParams, or don't desactivate them ?
		// @TODO
		// 4) upgrade
		//	install/model.php?_=1309193641470&method=doUpgrade&customModule=desactivate
		if (isset($this->currentParams['customModule']))
			$_GET['customModule'] = $this->currentParams['customModule'];

		if (!$this->_modelDoUpgrade())
		{
			$this->next = 'error';
			$this->nextDesc = $this->l('error during upgrade Db');
		}

		// 5) compare activated modules and reactivate them
		// @TODO

	}


	/**
	 * upgradeThisFile
	 *
	 * @param mixed $file
	 * @return void
	 */
	public function upgradeThisFile($file)
	{
		// @TODO : later, we could handle customization with some kind of diff functions
		// for now, just copy $file in str_replace($this->latestRootDir,_PS_ROOT_DIR_)
		// $file comes from scandir function, no need to lost time and memory with file_exists()
		if ($this->_skipFile('', $file,'upgrade'))
		{
			$this->nextQuickInfo[] = $this->l('%s ignored');
			return true;
		}
		else
		{
			$dest = str_replace($this->latestRootDir, $this->destUpgradePath,$file);

			if (is_dir($file))
			{
				if (!file_exists($dest))
				{
					if (@mkdir($dest))
					{
						$this->nextQuickInfo[] = sprintf($this->l('created dir %2$s. %3$s files left to upgrade.'),$file, $dest, sizeof($this->nextParams['filesToUpgrade']));
						return true;
					}
					else
					{
						$this->next = 'error';
						$this->nextQuickInfo[] = sprintf($this->l('error when creating directory %s'),$dest);
						$this->nextDesc = sprintf($this->l('error when creating directory %s'),$dest);
						return false;
					}
				}
				else
				{
					// directory already exists
					return true;
				}
			}
			else
			{
				if (copy($file,$dest))
				{
					$this->nextQuickInfo[] = sprintf($this->l('copied %1$s in %2$s. %3$s files left to upgrade.'),$file, $dest, sizeof($this->nextParams['filesToUpgrade']));
					return true;
				}
				else
				{
					$this->next = 'error';
					$this->nextQuickInfo[] = sprintf($this->l('error for copy %1$s in %2$s'),$file,$dest);
					$this->nextDesc = sprintf($this->l('error for copy %1$s in %2$s'),$file,$dest);
					return false;
				}
			}
		}

	}

	public function ajaxProcessRollback()
	{
		// 1st, need to analyse what was wrong.

		$this->nextParams = $this->currentParams;
		if (!empty($this->backupFilesFilename))
		{
			$this->next = 'restoreFiles';
			$this->status = 'ok';
			$this->nextDesc = $this->l('Files restored, now restoring database.');
		}
		else
		{
			if (!empty($this->backupDbFilename))
			{
				$this->next = 'restoreDb';
				$this->status = 'ok';
				$this->nextDesc = $this->l('Database restored');
			}
			else
			{
				// 2nd case if upgradeFiles made an error
				// 3rd case if no upgrade has been done
				// all theses cases are handled by the method ajaxRequestRollback()
				$this->next = ''; // next is empty : nothing next :)
				$this->status = 'ok';
				$this->nextDesc = $this->l('All your site is restored... ');
			}
		}
	}

	/**
	 * ajaxProcessRestoreFiles restore the previously saved files.
	 * 
	 * @return boolean true if succeed
	 */
	public function ajaxProcessRestoreFiles()
	{
		// @TODO : workaround max_execution_time / ajax batch unzip
		if (!empty($this->backupFilesFilename))
		{
			// cleanup current PS tree
			$list = $this->_listArchivedFiles();
			if (count($list) > 0)
			{
				$this->_cleanUp($this->prodRootDir.'/');
				$this->nextQuickInfo[] = $this->l('root directory cleaned.');

				$filepath = $this->backupFilesFilename;
				$destExtract = $this->prodRootDir;

				if (self::ZipExtract($filepath, $destExtract))
				{
					// once it's restored, delete the file !
					unlink($this->backupFilesFilename);
					Configuration::updateValue('UPGRADER_BACKUPFILES_FILENAME', '');
					if (!empty($this->backupDbFilename))
					{
						$this->nextDesc = $this->l('Files restored. No database backup found. Restoration done.');
						$this->next = '';
					}
					else
					{
						$this->nextDesc = $this->l('Files restored.');
						$this->next = 'rollback';
					}
					return true;
				}
				else
				{
					$this->next = "error";
					$this->nextDesc = sprintf($this->l('unable to extract $1$s into %2$s .'), $filepath, $destExtract);
					return false;
				}
			}
		}
		else
		{
			$this->next = 'error';
			$this->nextDesc = $this->l('no known backup. nothing to restore.');
			return false;
		}
	}

	/**
	* try to restore db backup file
	* @return type : hey , what you expect ? well mysql errors array .....
	* @TODO : maybe this could be in the Backup class
	*/
	public function ajaxProcessRestoreDb()
	{
		$exts = explode('.', $this->backupDbFilename);
		$fileext = $exts[count($exts)-1];
		$requests = array();
		$errors = array();
		$content = '';
		switch ($fileext)
		{
			case 'bz':
			case 'bz2':
				if ($fp = bzopen($this->backupDbFilename, 'r'))
				{
					while(!feof($fp))
						$content .= bzread($fp, filesize($this->backupDbFilename));
					bzclose($fp);
				}
				break;
			case 'gz':
				if ($fp = gzopen($this->backupDbFilename, 'r'))
				{
					while(!feof($fp))
						$content = gzread($fp, filesize($this->backupDbFilename));
					gzclose($fp);
				}
				break;
			// default means sql ?
			default :
				if ($fp = fopen($this->backupDbFilename, 'r'))
				{
					while(!feof($fp))
						$content = fread($fp, filesize($this->backupDbFilename));
						fclose($fp);
				}
		}

		if ($content=='')
			return false;

		// preg_match_all is better than preg_split (what is used in doUpgrade.php)
		// This way we avoid extra blank lines
		// option s (PCRE_DOTALL) added
		// @TODO need to check if a ";" in description could block that (I suppose it can at the end of a line)
		preg_match_all('/.*;[\n]\+/s', $content, $requests);
		/* @TODO maybe improve regex pattern ... */
		$db = Db::getInstance();	
		if (count($requests)>0)
		{
			foreach ($requests as $request)
				if (!empty($request))
					if (!$db->Execute($request))
						$this->nextQuickInfo[] = $db->getMsgError();

			// once it's restored, delete the file !
			unlink($this->backupDbFilename);
			Configuration::updateValue('UPGRADER_BACKUPDB_FILENAME','');
		}
		else
			$this->nextQuickInfo[] = $this->l('Nothing to restore (no request found)');

		$this->next = 'rollback';
		$this->nextDesc = 'Database restore done.';
	}

	public function ajaxProcessBackupDb()
	{
		$backup = new Backup();
		// for backup db, use autoupgrade directory
		// @TODO : autoupgrade must not be static
		$backup->setCustomBackupPath('autoupgrade');
		// maybe for big tables we should save them in more than one file ?
		$res = $backup->add();
		if ($res)
		{
			Configuration::updateValue('UPGRADER_BACKUPDB_FILENAME', $backup->id);

			$this->next = 'upgradeFiles';
			$this->nextDesc = sprintf($this->l('Database backup done in %s. Now updating files'),$backup->id);
		}
		// if an error occur, we assume the file is not saved
	}

	public function ajaxProcessBackupFiles()
	{
		$this->nextParams = $this->currentParams;
		$this->stepDone = false;
		/////////////////////

		if (!isset($this->nextParams['filesForBackup']))
		{
			$list = $this->_listBackupFiles($this->prodRootDir);
			$this->nextQuickInfo[] = sprintf($this->l('%s Files to backup.'), sizeof($this->backupFileList));
			$this->nextParams['filesForBackup'] = $this->backupFileList;

			// delete old backup, create new
			if (file_exists($this->backupFilesFilename))
				unlink($this->backupFilesFilename);

			$time = time();
			$this->backupFilesFilename = $this->autoupgradePath . DIRECTORY_SEPARATOR . 'backupfile-'.date('Y-m-d').'-'.$time.'.zip';

			Configuration::updateValue('UPGRADER_BACKUPFILES_FILENAME', $this->backupFilesFilename);
			$this->nextQuickInfo[]	= sprintf($this->l('backup files initialized in %s'), $this->backupFilesFilename);
		}

		/////////////////////
		$this->next = 'backupFiles';
		// @TODO : display % instead of this
		$this->nextDesc = sprintf($this->l('Backup files in progress. %s files left'), sizeof($this->nextParams['filesForBackup']));
		if (is_array($this->nextParams['filesForBackup']))
		{
			// @TODO later
			// 1) calculate crc32 of next file
			// 2) use the provided xml with crc32 calculated from previous versions ?
			// or simply use the latest dir ?
			//$current = crc32(file_get_contents($file));
			//$file = $this->nextParams['filesForBackup'][0];
			//$latestFile = str_replace(_PS_ROOT_DIR_,$this->latestRootDir,$file);
	
			//	if (file_exists($latestFile))
			//		$latest = crc32($latestFile);
			//	else
			//		$latest = '';
			
				$zip = new ZipArchive();
				if ($zip->open($this->backupFilesFilename, ZIPARCHIVE::CREATE))
				{
					$this->next = 'backupFiles';
					// @TODO all in one time will be probably too long
					// 1000 ok during test, but 10 by 10 to be sure
					$this->stepok = false;
					// @TODO min(self::$loopBackupFiles, sizeof())
					for($i=0;$i<self::$loopBackupFiles;$i++)
					{
						if (sizeof($this->nextParams['filesForBackup'])<=0)
						{
							$this->stepok = true;
							$this->status = 'ok';
							$this->next = 'backupDb';
							$this->nextDesc = $this->l('All files saved. Now backup Database');
							$this->nextQuickInfo[] = $this->l('all files have been added to archive.');
							break;
						}
						// filesForBackup already contains all the correct files
						$file = array_shift($this->nextParams['filesForBackup']);
						$archiveFilename = str_replace($this->prodRootDir,'',$file);
						// @TODO : maybe put several files at the same times ?
						if ($zip->addFile($file,$archiveFilename))
							$this->nextQuickInfo[] = sprintf($this->l('%1$s added to archive. %2$s left.'),$file, sizeof($this->nextParams['filesForBackup']));
						else
						{
						// if an error occur, it's more safe to delete the corrupted backup
							if (file_exists($this->backupFilesFilename))
								unlink($this->backupFilesFilename);
							$this->next = 'error';
							$this->nextDesc = sprintf($this->l('error when trying to add %1$s to archive %2$s.'),$file, $backupFilePath);
							break;
						}
					}
					$zip->close();
					return true;
				}
				else{
					$this->next = 'error';
					$this->nextDesc = $this->l('unable to open archive');
					return false;
				}
		}
		else
		{
			$this->next = 'backupDb';
			$this->nextDesc = 'All files saved. Now backup Database';
			return true;
		}
		// 4) save for display.
	}


	private function _removeOneSample($removeList)
	{
		if (is_array($removeList) AND sizeof($removeList)>0)
		{
			if (file_exists($removeList[0]) AND unlink($removeList[0]))
			{
				$item = array_shift($removeList);
				$this->next = 'removeSamples';
				$this->nextParams['removeList'] = $removeList;
				$this->nextQuickInfo[] = sprintf($this->l('%1$s removed. %2$s items left'), $item, sizeof($removeList));
			}
			else
			{
				$this->next = 'error';
				$this->nextParams['removeList'] = $removeList;
				$this->nextQuickInfo[] = sprintf($this->l('error when removing %1$s, %2$s items left'), $removeList[0], sizeof($removeList));
				return false;
			}
		}
		return true;
	}

	public function ajaxProcessRemoveSamples(){
		$this->stepDone = false;
		// @TODO : list exaustive list of files to remove :
		// all images from img dir exept admin ?
		// all images like logo, favicon, ?.
		// all custom image from modules ?
		// all custom image from theme ?
		if (!isset($this->currentParams['removeList']))
		{
			$this->_listSampleFiles($this->autoupgradePath.'/latest/prestashop/img', 'jpg');
			$this->_listSampleFiles($this->autoupgradePath.'/latest/prestashop/modules/editorial/', 'homepage_logo.jpg');
			// @TODO handle this bad thing
			$this->nextQuickInfo[] = sprintf($this->l('Starting to remove %1$s sample files'), sizeof($this->sampleFileList));
			$this->nextParams['removeList'] = $this->sampleFileList;
		}


		// @TODO : removing @, adding if file_exists
//		@unlink(_PS_ROOT_DIR_.'modules'.DIRECTORY_SEPARATOR.'editorial'.DIRECTORY_SEPARATOR.'editorial.xml');
//		@unlink(_PS_ROOT_DIR_.'modules'.DIRECTORY_SEPARATOR.'editorial'.DIRECTORY_SEPARATOR.'homepage_logo.jpg'); // homepage custom ?
//		@unlink(_PS_ROOT_DIR_.'img'.DIRECTORY_SEPARATOR.'logo.jpg');
//		@unlink(_PS_ROOT_DIR_.'img'.DIRECTORY_SEPARATOR.'favicon.ico');
		$resRemove = true;
		for($i=0;$i<self::$loopRemoveSamples;$i++)
		{
			if (sizeof($this->nextParams['removeList']) <= 0 )
			{
				$this->stepDone = true;
				$this->next = 'backupFiles';
				$this->nextDesc = $this->l('All sample files removed. Now backup files.');
				// break the loop, all sample already removed
				return true;
			}
			$resRemove &= $this->_removeOneSample($this->nextParams['removeList']);
			if (!$resRemove)
				break;
		}

		return $resRemove;
	}
	
	public function ajaxProcessSvnCheckout()
	{
		$this->nextParams = $this->currentParams;
		if ($this->useSvn){
			$svnLink = 'http://svn.prestashop.com/trunk';
			$dest = $this->autoupgradePath . DIRECTORY_SEPARATOR . $this->svnDir;
			
			$svnStatus = svn_status($dest);
			if (is_array($svnStatus))
			{
				if (sizeof($svnStatus) == 0)
				{
					$this->next = 'svnExport';
					$this->nextDesc = sprintf($this->l('working copy already %s up-to-date. now exporting it into latest dir'),$dest);
				}
				else
				{
					// we assume no modification has been done
					// @TODO a svn revert ?
					if ($svnUpdate = svn_update($dest))
					{
						$this->next = 'svnExport';
						$this->nextDesc = sprintf($this->l('SVN Update done for working copy %s . now exporting it into latest...'),$dest);
					}
				}
			}
			else
			{
					// no valid status found
					// @TODO : is 0777 good idea ?
					if (!file_exists($dest))
						if (!@mkdir($dest,0777))
						{
							$this->next = 'error';
							$this->nextDesc = sprintf($this->l('unable to create directory %s'),$dest);
							return false;
						}

					if (svn_checkout($svnLink, $dest))
					{
						$this->next = 'svnExport';
						$this->nextDesc = sprintf($this->l('SVN Checkout done from %s . now exporting it into latest...'),$svnLink);
						return true;
					}
					else
					{
						$this->next = 'error';
						$this->nextDesc = $this->l('SVN Checkout error...');
					}
				}
		}
		else
		{
			$this->next = 'error';
			$this->nextDesc = $this->l('not allowed to use svn');
		}
	}

	public function ajaxProcessDownload()
	{
		if (@ini_get('allow_url_fopen'))
		{
			$res = $this->upgrader->downloadLast($this->autoupgradePath,$this->destDownloadFilename);
			if ($res){
				$this->next = 'unzip';
				$this->nextDesc = $this->l('Download complete. Now extracting');
			}
			else
			{
				$this->next = 'error';
				$this->nextDesc = $this->l('Error during download');
			}
		}
		else
		{
			// @TODO : ftp mode
			$this->next = 'error';
			$this->nextDesc = sprintf($this->l('you need allow_url_fopen for automatic download. You can also manually upload it in %s'),$this->autoupgradePath.$this->destDownloadFilename);
		}
	}

	public function buildAjaxResult()
	{
		$return['error'] = $this->error;
		$return['stepDone'] = $this->stepDone;
		$return['next'] = $this->next;
		$return['status'] = $this->next == 'error' ? 'error' : 'ok';
		$return['nextDesc'] = $this->nextDesc;
		if (!empty($this->nextParams))
			$return['nextParams'] = $this->nextParams;
		else
			$return['nextParams'] = array();
		
		$return['nextParams']['typeResult'] = $this->nextResponseType;

		$return['nextQuickInfo'] = $this->nextQuickInfo;
		return Tools::jsonEncode($return);
	}

	/**
	 * displayConf
	 *
	 * @return void
	 */
	public function displayConf()
	{
		if (version_compare(_PS_VERSION_,'1.4.4.0','<') AND false)
		{
			$this->_errors[] = Tools::displayError('This class depends of several files modified in 1.4.4.0 version and should not be used in an older version');
		}
		parent::displayConf();
		
	}

	public function ajaxPreProcess()
	{
		if (Tools::getValue('responseType') == 'json')
			header('Content-Type: application/json');
			$action = (Tools::getValue('action'));

			if (isset(self::$skipAction[strtolower($action)]))
			{
				$this->next = self::$skipAction[$action];
				$this->nextDesc = sprintf($this->l('action %s skipped'),$action);
				$this->nextQuickInfo[] = sprintf($this->l('action %s skipped'),$action);
				unset($_POST['action']);
			}
			else if (!method_exists(get_class($this), 'ajaxProcess'.$action))
			{
				$this->nextDesc = sprintf($this->l('action "%1$s" non trouvée '), $action);
				$this->next = 'error';
				$this->error = '1';
			}

		if ($this->apacheModExists('mod_evasive'))
			sleep(1);
	}
	/**
	 * apacheModExists return true if the apache module $name is loaded
	 * @TODO move this method in class Information (when it will exist)
	 *
	 * @param string $name module name
	 * @return boolean true if exists
	 */
	function apacheModExists($name)
	{
		static $apacheModuleList = null;

		if (!is_array($apacheModuleList))
			$apacheModuleList = apache_get_modules();

		// we need strpos (example can be evasive20
		foreach($apacheModuleList as $module)
			if (strpos($name, $module)!==false)
				return true;

		return false;
	}

	private function _getJsErrorMsgs()
	{
		$INSTALL_VERSION = $this->upgrader->version_num;
		$ret = '
var txtError = new Array();
txtError[0] = "'.$this->l('Required field').'";
txtError[1] = "'.$this->l('Too long!').'";
txtError[2] = "'.$this->l('Fields are different!').'";
txtError[3] = "'.$this->l('This email adress is wrong!').'";
txtError[4] = "'.$this->l('Impossible to send the email!').'";
txtError[5] = "'.$this->l('Can\'t create settings file, if /config/settings.inc.php exists, please give the public write permissions to this file, else please create a file named settings.inc.php in config directory.').'";
txtError[6] = "'.$this->l('Can\'t write settings file, please create a file named settings.inc.php in config directory.').'";
txtError[7] = "'.$this->l('Impossible to upload the file!').'";
txtError[8] = "'.$this->l('Data integrity is not valided. Hack attempt?').'";
txtError[9] = "'.$this->l('Impossible to read the content of a MySQL content file.').'";
txtError[10] = "'.$this->l('Impossible the access the a MySQL content file.').'";
txtError[11] = "'.$this->l('Error while inserting data in the database:').'";
txtError[12] = "'.$this->l('The password is incorrect (alphanumeric string at least 8 characters).').'";
txtError[14] = "'.$this->l('A Prestashop database already exists, please drop it or change the prefix.').'";
txtError[15] = "'.$this->l('This is not a valid file name.').'";
txtError[16] = "'.$this->l('This is not a valid image file.').'";
txtError[17] = "'.$this->l('Error while creating the /config/settings.inc.php file.').'";
txtError[18] = "'.$this->l('Error:').'";
txtError[19] = "'.$this->l('This PrestaShop database already exists. Please revalidate your authentication informations to the database.').'";
txtError[22] = "'.$this->l('An error occurred while resizing the picture.').'";
txtError[23] = "'.$this->l('Database connection is available!').'";
txtError[24] = "'.$this->l('Database Server is available but database is not found').'";
txtError[25] = "'.$this->l('Database Server is not found. Please verify the login, password and server fields.').'";
txtError[26] = "'.$this->l('An error occurred while sending email, please verify your parameters.').'";
txtError[37] = "'.$this->l('Impossible to write the image /img/logo.jpg. If this image already exists, please delete it.').'";
txtError[38] = "'.$this->l('The uploaded file exceeds the upload_max_filesize directive in php.ini').'";
txtError[39] = "'.$this->l('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form').'";
txtError[40] = "'.$this->l('The uploaded file was only partially uploaded').'";
txtError[41] = "'.$this->l('No file was uploaded.').'";
txtError[42] = "'.$this->l('Missing a temporary folder').'";
txtError[43] = "'.$this->l('Failed to write file to disk').'";
txtError[44] = "'.$this->l('File upload stopped by extension').'";
txtError[45] = "'.$this->l('Cannot convert your database\'s data to utf-8.').'";
txtError[46] = "'.$this->l('Invalid shop name').'";
txtError[47] = "'.$this->l('Your firstname contains some invalid characters').'";
txtError[48] = "'.$this->l('Your lastname contains some invalid characters').'";
txtError[49] = "'.$this->l('Your database server does not support the utf-8 charset.').'";
txtError[50] = "'.$this->l('Your MySQL server doesn\'t support this engine, please use another one like MyISAM').'";
txtError[51] = "'.$this->l('The file /img/logo.jpg is not writable, please CHMOD 755 this file or CHMOD 777').'";
txtError[52] = "'.$this->l('Invalid catalog mode').'";
txtError[999] = "'.$this->l('No error code available').'";
//upgrader
txtError[27] = "'.$this->l('This installer is too old.').'";
txtError[28] = "'.sprintf($this->l('You already have the %s version.'),$INSTALL_VERSION).'";
txtError[29] = "'.$this->l('There is no older version. Did you delete or rename the config/settings.inc.php file?').'";
txtError[30] = "'.$this->l('The config/settings.inc.php file was not found. Did you delete or rename this file?').'";
txtError[31] = "'.$this->l('Can\'t find the sql upgrade files. Please verify that the /install/sql/upgrade folder is not empty)').'";
txtError[32] = "'.$this->l('No upgrade is possible.').'";
txtError[33] = "'.$this->l('Error while loading sql upgrade file.').'";
txtError[34] = "'.$this->l('Error while inserting content into the database').'";
txtError[35] = "'.$this->l('Unfortunately,').'";
txtError[36] = "'.$this->l('SQL errors have occurred.').'";
txtError[37] = "'.$this->l('The config/defines.inc.php file was not found. Where did you move it?').'";';
		return $ret;
	}

	public function displayAjax(){
		echo $this->buildAjaxResult();
	}

	private function _displayRollbackForm()
	{
		echo '<fieldset><legend>'.$this->l('Rollback').'</legend>
		<div id="rollbackForm">';
		if (empty($this->backupFilesFilename) AND empty($this->backupDbFilename))
			echo $this->l('No rollback available');
		else if (!empty($this->backupFilesFilename) OR !empty($this->backupDbFilename))
		{
			echo '<div id="rollbackContainer"><a class="upgradestep button" href="" id="rollback">'.$this->l('rollback').'</a></div><br/>';
		}
		if (!empty($this->backupFilesFilename))
			echo '<div id="restoreFilesContainer"><a href="" class="upgradestep button" id="restoreFiles">restoreFiles</a> '.sprintf($this->l('click to restore %s'),$this->backupFilesFilename).'</div><br/>';
		if (!empty($this->backupDbFilename))
			echo '<div id="restoreDbContainer"><a href="" class="upgradestep button" id="restoreDb">restoreDb</a> '.sprintf($this->l('click to restore %s'), $this->backupDbFilename).'</div><br/>';

		echo '</div></fieldset>';
	}

	private function _displayUpgraderForm()
	{
			$pleaseUpdate = $this->upgrader->checkPSVersion();

			echo '<fieldset class="width autoupgrade " >';
			echo '<legend>'.$this->l('Your current configuration').'</legend>';
			echo '<b>'.$this->l('Root directory').' : </b>'.$this->prodRootDir.'<br/><br/>';

			if ($this->rootWritable)
				$srcRootWritable = '../img/admin/enabled.gif';
			else
				$srcRootWritable = '../img/admin/disabled.gif';
			echo '<b>'.$this->l('Root directory status').' : </b>'.'<img src="'.$srcRootWritable.'" /> '.($this->rootWritable?$this->l('fully writable'):$this->l('not writable recursively')).'<br/><br/>';
			
			if ($this->upgrader->needUpgrade)
			{
				if ($this->upgrader->autoupgrade)
					$srcAutoupgrade = '../img/admin/enabled.gif';
				else
					$srcAutoupgrade = '../img/admin/disabled.gif';
				echo '<b>'.$this->l('Autoupgrade allowed').' : </b>'.'<img src="'.$srcAutoupgrade.'" /> '.($this->upgrader->autoupgrade?$this->l('This release allow autoupgrade.'):$this->l('This release does not allow autoupgrade')).'. <br/><br/>';
			}

			if (Configuration::get('PS_SHOP_ENABLE'))
			{
				$srcShopStatus = '../img/admin/enabled.gif';
				$label = $this->l('Active');
			}
			else
			{
				$srcShopStatus = '../img/admin/disabled.gif';
				$label = $this->l('Inactive');
			}
			echo '<b>'.$this->l('Shop status').' : </b>'.'<img src="'.$srcShopStatus.'" />'.$label.'<br/><br/>';

			$max_exec_time = ini_get('max_execution_time');
			if ($max_exec_time == 0)
				$srcExecTime = '../img/admin/enabled.gif';
			else
				$srcExecTime = '../img/admin/warning.gif';
			echo '<b>'.$this->l('PHP time limit').' : </b>'.'<img src="'.$srcExecTime.'" />'.($max_exec_time == 0?$this->l('disabled'):$max_exec_time.' '.$this->l('seconds')).' <br/><br/>';

			if ($this->rootWritable)
				$srcRootWritable = '../img/admin/enabled.gif';
			else
				$srcRootWritable = '../img/admin/disabled.gif';
			echo '<b>'.$this->l('Root directory').' : </b>'.'<img src="'.$srcRootWritable.'" /> '.($this->rootWritable?$this->l('writable recursively'):$this->l('not writable recursively')).'. <br/><br/>';

			echo '<a class="button" id="scrollToOptions" href="#options">'.$this->l('Modify your options').'</a>';
			echo '</fieldset>';

			echo '<br/>';

			echo '<fieldset class=""><legend>'.$this->l('Update').'</legend>';


		echo '<div style="float:left">
		<h1>'.sprintf($this->l('Your current prestashop version : %s '),_PS_VERSION_).'</h1>';
		echo '<p>'.sprintf($this->l('Last version is %1$s (%2$s) '), $this->upgrader->version_name, $this->upgrader->version_num).'</p>';

		// @TODO : this should be checked when init()
		if ($this->isUpgradeAllowed()) {
			if ($pleaseUpdate) {
				echo '<li><img src="'._PS_ADMIN_IMG_.'information.png" alt="information"/> '.$this->l('Latest Prestashop version available is:').' <b>'.$pleaseUpdate['name'].'</b></li>';
			}
//			echo '<input class="button" type="submit" name="sumbitUpdateVersion" value="'.$this->l('Backup Database, backup files and update right now and in one click !').'"/>';
//			echo '<input class="button" type="submit" id="refreshCurrent" value="'.$this->l("refresh update dir / current").'"/>';
			echo '<br/>';
		if ($this->upgrader->needUpgrade)
		{
			echo '<a href="" id="upgradeNow" class="button-autoupgrade upgradestep">'.$this->l('Upgrade PrestaShop now !').'</a>';
		}
		else
		{
			echo '<p>'.$this->l('Your shop is already up to date').'</p>';
		}
		
			echo'</div>
			<div id="currentlyProcessing" style="display:none;float:right"><h4>Currently processing :</h4>
			<div id="infoStep" class="processing" style=height:50px;width:400px;" >I\'m waiting for your command, sir</div>';
			echo '</div>';

			echo '</fieldset>';
		
		
			echo '<br class="clear"/>';

			if (defined('_PS_MODE_DEV_') AND _PS_MODE_DEV_)
			{
				echo '<fieldset class="autoupgradeSteps"><legend>'.$this->l('Step').'</legend>';
				echo '<h4>'.$this->l('Upgrade steps').'</h4>';
				echo '<div>';
				echo '<a href="" id="desactiveShop" class="button upgradestep" >desactiveShop</a>';
				echo '<a href="" id="download" class="button upgradestep" >download</a>';
				echo '<a href="" id="unzip" class="button upgradestep" >unzip</a>'; // unzip in autoupgrade/latest
				echo '<a href="" id="removeSamples" class="button upgradestep" >removeSamples</a>'; // remove samples (iWheel images)
				echo '<a href="" id="backupFiles" class="button upgradestep" >backupFiles</a>'; // backup files
				echo '<a href="" id="backupDb" class="button upgradestep" >backupDb</a>';
				echo '<a href="" id="upgradeFiles" class="button upgradestep" >upgradeFiles</a>';
				echo '<a href="" id="upgradeDb" class="button upgradestep" >upgradeDb</a>';
				echo '</div>';

				if (defined('_PS_ALLOW_UPGRADE_UNSTABLE_') AND _PS_ALLOW_UPGRADE_UNSTABLE_ )
				{
					echo '<h4>Development tools </h4><div>
					<a href="" name="action" id="svnCheckout"	class="button upgradestep" type="submit" >svnCheckout</a>
					<a href="" name="action" id="svnUpdate"	class="button upgradestep" type="submit" >svnUpdate</a>
					<a href="" name="action" id="svnExport"	class="button upgradestep" type="submit" >svnExport</a>
					</div>';
				}
			}

			echo '<br class="clear"/>';
			echo'	<div id="quickInfo" class="processing" style="height:100px;">quick info</div>';
			// for upgradeDb
			echo '<p id="dbResultCheck"></p>';
			echo '<p id="dbCreateResultCheck"></p>';
		}
		else
			echo '<p>'.$this->l('Your current configuration does not allow upgrade.').'</p>';

		echo '</fieldset>';
/*		echo '<fieldset class="right">
		<legend>Error</legend>
		<div id="errorWindow" > no error yet</div>
		</fieldset>';
		*
		*/
		// information to keep will be in #infoStep
		// temporary infoUpdate will be in #tmpInformation
		echo '<script type="text/javascript">';
		// _PS_MODE_DEV_ is available in js
		if (defined('_PS_MODE_DEV_' AND _PS_MODE_DEV_))
			echo 'var _PS_MODE_DEV_ = true;';
		echo $this->_getJsErrorMsgs();

echo '</script>';
	}

	public function display()
	{

		global $cookie, $currentIndex;
		// update['name'] = version name
		// update['num'] = only the version
		// update['link'] = download link
		echo '<fieldset class="error"><legend><img src="'.__PS_BASE_URI__.'modules/autoupgrade/logo.gif" />'.$this->l('New upgrade module').'</legend>';
		echo '<p>'.$this->l('The tab AdminUpgrade present in 1.4.4.0 and 1.4.4.1 version has been removed and that functions is now available as module.').'</p>';
		$tokenModule = Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)$cookie->id_employee);
		$tokenAdminTabs =  Tools::getAdminToken('AdminTabs'.(int)(Tab::getIdFromClassName('AdminTabs')).(int)$cookie->id_employee);
		$tokenAdminTools =  Tools::getAdminToken('AdminTools'.(int)(Tab::getIdFromClassName('AdminTools')).(int)$cookie->id_employee);
		echo '<p id="ContainerActivateButton">
			<a class="button" id="activateAutoupgradeModule" href="index.php?tab=AdminModules&token='.$tokenModule.'&amp;install=autoupgrade&amp;module_name=autoupgrade">'
			.$this->l('Click to remove that tab and activate the autoupgrade module').'</a></p>';
		echo '<script type="text/javascript">
			$("#activateAutoupgradeModule").click(function(e){
				e.preventDefault();
				console.warn($(this).attr("href"));
				req = $.ajax({
					type:"POST",
					url : "index.php",
				async: true,
				data : {
					ajaxMode : "1",
					token : "'.$tokenModule.'",
					tab : "AdminModules",
					install: "autoupgrade",
					module_name:"autoupgrade",
				},
			success : function(res,textStatus,jqXHR)
			{
				console.log(res);
				$.get("index.php?token='.$tokenAdminTabs.'&tab=AdminTabs&id_tab='.Tab::getIdFromClassName('AdminUpgrade').'&deletetab",
				function(e){
					alert("'.$this->l('Previous UpgradeTab has been removed. Please go to the new Upgrade tab, or install the module first.').'");
					window.location = "'.'index.php?token='.$tokenAdminTools.'&tab=AdminTools&id_tab='.Tab::getIdFromClassName('AdminTools').'";

				}
				);
			},
			error: function(res,textStatus,jqXHR)
			{
				alert("'.$this->l("Unable to install the module automatically. Please manually remove the Upgrade AdminTab and install the autoupgrade module.").'");
			}
		});
	});
				
		</script>
	</fieldset>';
		$this->displayWarning($this->l('This function is experimental. It\'s highly recommended to make a backup of your files and database before starting the upgrade process.'));
		echo '</fieldset>';

		if ($this->isUpgradeAllowed())
		{
			if ($this->useSvn)
				echo '<div class="error"><h1>'.$this->l('Unstable upgrade').'</h1>
				<p class="warning">'.$this->l('Your current configuration indicate you want to upgrade your system from the unstable development branch, with no version number. If you upgrade, you will not be able to follow the official release process anymore').'.</p>
				</div>';
			$this->_displayUpgraderForm();
		
			echo '<br/>';
			$this->_displayRollbackForm();
	
			echo '<br/>';
			$this->_displayForm('autoUpgradeOptions',$this->_fieldsAutoUpgrade,'<a href="" name="options" id="options">'.$this->l('Options').'</a>', '','prefs');
			echo '<script type="text/javascript">'.$this->_getJsInit().'</script>';
		}
		else
		{
			echo '<fieldset>
			<legend>'.$this->l('Update').'</legend>';
			echo '<p>'.$this->l('You currently don\'t need to use this feature.').'</p>';
			echo '</fieldset>';
		}

	}

	private function _getJsInit()
	{
		global $currentIndex;
		$js = '';
		$js .= '
function ucFirst(str) {
	if (str.length > 0) {
		return str[0].toUpperCase() + str.substring(1);
	}
	else {
		return str;
	}
}

function cleanInfo(){
	$("#infoStep").html("reset<br/>");
}

function updateInfoStep(msg){
	if (msg)
	{
		$("#infoStep").html(msg);
		$("#infoStep").attr({ scrollTop: $("#infoStep").attr("scrollHeight") });
	}
}

function addError(msg){
	if (msg)
		$("#errorWindow").html(msg);
}

function addQuickInfo(arrQuickInfo){
	if (arrQuickInfo)
	{
		$("#quickInfo").show();
		for(i=0;i<arrQuickInfo.length;i++)
			$("#quickInfo").append(arrQuickInfo[i]+"<br/>");
		
		$("#quickInfo").attr({ scrollTop: $("#quickInfo").attr("scrollHeight") });
	}
}';

	if ($this->manualMode)
		$js .= 'var manualMode = true;';
	else
		$js .= 'var manualMode = false;';

		$js .= '

$(document).ready(function(){
	$(".upgradestep").click(function(e)
	{
		e.preventDefault();
		// $.scrollTo("#options")
	});

	// more convenient to have that param for handling success and error
	var requestParams;
		
		// set timeout to 5 minutes (download can be long)?
		$.ajaxSetup({timeout:300000});

	
	// prepare available button here, without params ?
	prepareNextButton("#upgradeNow",{});
	prepareNextButton("#rollback",{});
	prepareNextButton("#restoreDb",{});
	prepareNextButton("#restoreFiles",{});

});

/**
 * parseXMLResult is used to handle the return value of the doUpgrade method
 *
 */
function parseXMLResult(xmlRet)
{
	ret = $(xmlRet);
	ret = ret.find("action")[0];
	if (ret.getAttribute("result") == "ok")
	{
		$("#dbResultCheck")
			.addClass("ok")
			.removeClass("fail")
			.html("<p>'.$this->l('upgrade complete. Please check your front-office (try to make an order, check theme)').'</p>")
			.show("slow");
		$("#dbCreateResultCheck")
			.hide("slow");

		// difference with the original function
		ret = {next:"upgradeComplete",nextParams:{typeResult:"json"},status:"ok"};

	}
	else
	{
		$("#dbResultCheck")
			.addClass("fail")
			.removeClass("ok")
			.html(txtError[parseInt(ret.getAttribute("error"))])
			.show("slow");
		$("#dbCreateResultCheck")
			.hide("slow");
		
		// propose rollback if there is an error
		if (confirm(txtError[parseInt(ret.getAttribute("error"))]+"\r\n\r\n'.$this->l('Do you want to rollback ?').'"))
			ret = {next:"rollback",nextParams:{typeResult:"json"},status:"error"};
	}

	return ret
};

/**
 * afterBackupDb display the button 
 * 
 */
function afterBackupDb()
{
	$("#restoreDbContainer").html("<a href=\"\" class=\"upgradestep button\" id=\"restoreDb\">restoreDb</a> '.$this->l('click to restore database').'");
	prepareNextButton("#restoreDb",{});
}

function afterRestoreDb()
{
	$("#restoreDbContainer").html("");
}

function afterRestoreFiles()
{
	$("#restoreFilesContainer").html("");
}

function afterBackupFiles()
{
	$("#restoreFilesContainer").html("<div id=\"restoreFilesContainer\"><a href=\"\" class=\"upgradestep button\" id=\"restoreFiles\">restoreFiles</a> '.$this->l('click to restore files').'");
	prepareNextButton("#restoreFiles",{});

}

function doAjaxRequest(action, nextParams){
		req = $.ajax({
			type:"POST",
			url : "'.str_replace('index','ajax-tab',$currentIndex).'",
			async: true,
			data : {
				dir:"'.$this->adminDir.'",
				ajaxMode : "1",
				token : "'.$this->token.'",
				tab : "AdminUpgrade",
				action : action,
				params : nextParams
			},
			success : function(res,textStatus,jqXHR)
			{
				if(eval("typeof nextParams") == "undefined")
				{
					nextParams = {typeResult : "json"};
				}

				if (nextParams.typeResult == "xml")
				{
					xmlRes = parseXMLResult(res);
					res = {};
					res.next = xmlRes.next;
					// if xml, we keep the next params
					nextParams = myNext;
					res.status = xmlRes.status;
				}
				else
				{
					res = $.parseJSON(res);
					nextParams = res.nextParams;
				}

				if (res.status == "ok")
				{
					// a
					$("#"+action).addClass("done");
					if (res.stepDone)
						$("#"+action).addClass("stepok");
					
					// if a function "after[action name]" exists, it should be called.
					// This is used for enabling restore buttons for example
					funcName = "after"+ucFirst(action);
					if (typeof funcName == "string" &&
						eval("typeof " + funcName) == "function") {

						eval(funcName+"()");
					}

					handleSuccess(res,nextParams.typeResult);
				}
				else
				{
					// display progression
					$("#"+action).addClass("done");
					$("#"+action).addClass("steperror");
					handleError(res);
				}
			},
			error: function(res,textStatus,jqXHR)
			{
				if (textStatus == "timeout" && action == "download")
				{
					updateInfoStep("'.$this->l('Your server can\'t download the file. Please upload it first by ftp in your admin/autoupgrade directory').'");
				}
				else
				{
					//console.log(res);
					//console.log(jqXHR);
					updateInfoStep("[Server Error] Status message : " + textStatus);
				}
			}
		});
	};

/**
 * prepareNextButton make the button button_selector available, and update the nextParams values
 *
 * @param button_selector $button_selector
 * @param nextParams $nextParams
 * @return void
 */
function prepareNextButton(button_selector, nextParams)
{
//	myNext;
	myNext = nextParams;
	$(button_selector).unbind();
	$(button_selector).click(function(e){
		e.preventDefault();
		$("#currentlyProcessing").show();
';
		if (defined('_PS_MODE_DEV_') AND _PS_MODE_DEV_)
			$js .= 'addQuickInfo(["[DEV] request : "+$(this).attr("id")]);';
	$js .= '
	action = button_selector.substr(1);
	res = doAjaxRequest(action, nextParams);
	});
}

/**
 * handleSuccess
 * res = {error:, next:, nextDesc:, nextParams:, nextQuickInfo:,status:"ok"}
 * @param res $res
 * @return void
 */
function handleSuccess(res)
{
	updateInfoStep(res.nextDesc);
	if (res.next != "")
	{
		addQuickInfo(res.nextQuickInfo);

		$("#"+res.next).addClass("nextStep");
		if (manualMode)
		{
			prepareNextButton("#"+res.next,res.nextParams);
			alert("manually go to "+res.next+" button ");
		}
		else
		{
			// @TODO :
			// 1) instead of click(), call a function.
			doAjaxRequest(res.next,res.nextParams);
			// 2) remove all step link (or show them only in dev mode)
			// 3) when steps link displayed, they should change color when passed
		}
	}
	else
	{
		// Way To Go, end of upgrade process
		addQuickInfo(["End of upgrade process"]);
	}
}

// res = {nextParams, NextDesc}
function handleError(res)
{
	// display error message in the main process thing
	updateInfoStep(res.nextDesc);
	addQuickInfo(res.nextQuickInfo);
	// In case the rollback button has been desactivated, just re-enable it
	prepareNextButton("#rollback",res.nextParams);
	// ask if you want to rollback
	// @TODO !!!
	if (confirm(res.NextDesc+"\r\r'.$this->l('Do you want to rollback ?').'"))
	{
		if (manualMode)
			alert("'.$this->l('Please go manually go to rollback button').'");
		else
		{
			$("#rollback").click();
		}
			
	}
}
';
		return $js;
	}
	private function _cleanUp($path)
	{
		// as we need theses files for restore operation, we can't remove them.
		// They will be overwritten
		$skipDirs = array('backups', 'pclzip', 'autoupgrade', '.', '..', '.svn');
		$skipFiles = array('autoload.php', 'init.php', 'settings.inc.php', 'config.inc.php', 'Tools.php', 'AdminUpgrade.php', 'ajax-tab.php');		
		if (is_dir($path))
		{
			$fp = opendir($path);
			while ($file = readdir($fp))
			{
				if (!in_array($file, $skipDirs) AND !$this->_skipFile('', $path.$file, 'backup'))
				{
					$fullpath = $path.$file;
					if (is_dir($fullpath))
						$this->_cleanUp($fullpath.'/');
					else
					{
						if (!in_array($file, $skipFiles))
						{
							unlink($fullpath);
						}
					}
				}
			}
			closedir($fp);
			/* fortunately not empty dir won't be removed by the following */
			@rmdir($path);
		}
		else
			if (!$this->_skipFile($file, '', 'backup')) unlink($path);

		return true;
	}
	
	/**
	 * @desc extract a zip file to the given directory
	 * @return bool success
	 * we need a copy of it to be able to restore without keeping Tools and Autoload stuff
	 */
	private static function ZipExtract($fromFile, $toDir)
	{
		if (!file_exists($toDir))
			if (!@mkdir($toDir,0777))
			{
				$this->next = 'error';
				$this->nextDesc = sprintf($this->l('unable to create directory %s'),$toDir);
				return false;
			}

		if (class_exists('ZipArchive', false))
		{
			$zip = new ZipArchive();
			if ($zip->open($fromFile) === true AND $zip->extractTo($toDir) AND $zip->close())
				return true;
			return false;
		}
		else
		{

			if (!class_exists('PclZip',false))
				require_once($this->prodRootDir.'/tools/pclzip/pclzip.lib.php');

			$zip = new PclZip($fromFile);
			$list = $zip->extract(PCLZIP_OPT_PATH, $toDir);
			foreach ($list as $extractedFile)
				if ($extractedFile['status'] != 'ok')
					return false;

			return true;
		}
	}
	
	private function _listArchivedFiles()
	{
		if (!empty($this->nextParams['backupFilesFilepath']))
		{
			if (class_exists('ZipArchive', false))
			{
				$files=array();
				if ($zip = zip_open($this->currentParams['backupFilesFilepath']))
				{
					while ($entry=zip_read($zip))
						$files[] = zip_entry_name($entry);
	
					zip_close($zip);
				}
			}
			else
			{
				require_once($this->prodRootDir.'/tools/pclzip/pclzip.lib.php');
				if ($zip = new PclZip($this->currentParams['backupFilesFilepath']))
					return $zip->listContent();
			}
		}
		return false;
	}
	
	/**
	 *	bool _skipFile : check whether a file is in backup or restore skip list
	 *
	 * @param type $file : current file or directory name eg:'.svn' , 'settings.inc.php'
	 * @param type $fullpath : current file or directory fullpath eg:'/home/web/www/prestashop/img'
	 * @param type $way : 'backup' , 'upgrade'
	 */
	private function _skipFile($file,$fullpath,$way='backup')
	{
		$fullpath = str_replace('\\','/', $fullpath); // wamp compliant
		$rootpath = str_replace('\\','/', $this->prodRootDir);
		switch ($way)
		{
			case 'backup':
				if (in_array($file, $this->backupIgnoreFiles))
					return true;

				foreach($this->backupIgnoreAbsoluteFiles as $path)
					if ($file == 'img')
						if (strpos($fullpath, $rootpath.$path) !== false)
							return true;
				break;

			case 'upgrade':
				if (in_array($file, $this->excludeFilesFromUpgrade))
					return true;
			
				foreach ($this->excludeAbsoluteFilesFromUpgrade as $path)
					if (strpos($fullpath, $rootpath.$path) !== false)
						return true;
				break;
			// default : if it's not a backup or an upgrade, juste skip the file
			default:
				return false;
		}
	}
}

