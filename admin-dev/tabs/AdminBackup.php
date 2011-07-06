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
*  @version  Release: $Revision: 7320 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');

class AdminBackup extends AdminTab
{
	/** @var string The field we are sorting on */
	protected $_sortBy = 'date';

	public function __construct()
	{
		$this->table = 'backup';
		$this->className = 'Backup';
		parent::__construct();

	 	$this->edit = false;
	 	$this->delete = true;
	 	$this->view = true;
		
	 	$this->deleted = false;
		
		$this->requiredDatabase = false;
		
		$this->fieldsDisplay = array (
			'date' => array('title' => $this->l('Date'), 'type' => 'datetime', 'width' => 120, 'align' => 'right'),
			'age' => array('title' => $this->l('Age')),
			'filename' => array('title' => $this->l('File name'), 'width' => 200),
			'filesize' => array('title' => $this->l('File size')));
		$this->optionTitle = $this->l('Backup options');
		$this->_fieldsOptions = array(
			'PS_BACKUP_ALL' => array('title' => $this->l('Ignore statistics tables:'), 
			'desc' => $this->l('The following tables will NOT be backed up if you enable this option:').'<br />'._DB_PREFIX_.'connections, '._DB_PREFIX_.'connections_page, '._DB_PREFIX_.'connections_source, '._DB_PREFIX_.'guest, '._DB_PREFIX_.'statssearch', 'cast' => 'intval', 'type' => 'bool'),
			'PS_BACKUP_DROP_TABLE' => array('title' => $this->l('Drop existing tables during import:'), 
			'desc' => $this->l('Select this option to instruct the backup file to drop your tables prior to restoring the backed up data').'<br />(ie. "DROP TABLE IF EXISTS")', 'cast' => 'intval', 'type' => 'bool'));

		$this->identifier = 'filename';
	}

	/**
	 * Load class object using identifier in $_GET (if possible)
	 * otherwise return an empty object
	 * This method overrides the one in AdminTab because AdminTab assumes the id is a UnsignedInt
	 * "Backups" Directory in admin directory must be writeable (CHMOD 777)
	 * @param boolean $opt Return an empty object if load fail
	 * @return object
	 */
	protected function loadObject($opt = false)
	{
		if ($id = Tools::getValue($this->identifier))
			return new $this->className($id);
		return new $this->className();
	}

	/**
	 * Creates a new backup, and then displays the normal menu
	 */
	public function displayForm($isMainTab = true)
	{
		if(is_writable(PS_ADMIN_DIR.'/backups/'))
		{
		if (!($object = $this->loadObject()))
			return;
			if ($object->add())
			{
				echo '<div class="conf confirm"><img src="../img/admin/ok.gif" />&nbsp;'.$this->l('It appears that the Backup was successful, however, you must download and carefully verify the Backup file.').'</div>';
				if ($this->tabAccess['view'] === '1')
					echo '
					<fieldset style="margin: 40px 0;" class="width3">
						<legend><img src="../img/admin/AdminBackup.gif" alt="" class="icon" /> '.$this->l('Download').'</legend>
						<p style="font-size: 13px;"><a href="'.$object->getBackupURL().'"><img src="../img/admin/AdminBackup.gif" alt="" class="icon" /></a><b><a href="'.$object->getBackupURL().'">'.$this->l('Download the Backup file').' ('.number_format((filesize($object->id)*0.000001), 2, '.', '').$this->l('Mb').')</a></b><br /><br />
						'.$this->l('Tip: You can also download this file by FTP, Backup files are located in "admin/backups" directory.').'</p>
					</fieldset>';
				
				$this->displayHowTo(false);
			}
			elseif ($object->error)
				$this->_errors[] = $object->error;
		}
		else
			$this->_errors[] = $this->l('"Backups" Directory in admin directory must be writeable (CHMOD 755 / 777)');
		$this->displayErrors();
	}

	/**
	 * Displays the page which allows the backup to be downloaded
	*/
	public function viewbackup()
	{
		global $currentIndex;

		if (!($object = $this->loadObject()))
			return;
		if ($object->id)
		{
			$url = $object->getBackupURL();
			echo '<div class="conf confirm"><img src="../img/admin/ok.gif" />&nbsp;'.$this->l('Beginning download ...').'</div>';
			echo '<br />'.$this->l('Back-up file should automatically download.');
			echo '<br /><br />'.$this->l('If not,').' <b><a href="'.$url.'">'.$this->l('please click here!').'</a></b>';
			echo '<iframe width="0" height="0" scrolling="no" frameborder="0" src="'.$url.'"></iframe>';
			echo '<br /><br /><br /><a href="'.$currentIndex.'&token='.$this->token.'"><img src="../img/admin/arrow2.gif" /> '.$this->l('Back to list').'</a><br />';
		}
		elseif ($object->error)
			$this->_errors[] = $object->error;
		$this->displayErrors();
	}

	public function displayHowTo($showForm = true)
	{
		global $currentIndex;

		echo '
		<div class="error width1" style="float: left; margin-right: 10px;">
			<p>'.$this->l('Disclaimer before creating a new Backup').'</p>
			<ol style="font-size: 11px; font-weight: normal; line-height: 20px; padding-left: 10px;">
				<li>'.$this->l('PrestaShop is not responsible for your database, Backups, restore and data.').'</li>
				<li>'.$this->l('PrestaShop is an Open-source software, you are using it at your own risk under the licence agreement.').'</li>
				<li>'.$this->l('You should Backup your data on a regular basis (both files and database).').'</li>
				<li>'.$this->l('This function only backs up your database, not your files.').'</li>
				<li>'.$this->l('By default, your existing database tables will be deleted during Backup restore (see options).').'</li>
				<li>'.$this->l('Always verify the quality and integrity of your Backups files.').'</li>
				<li>'.$this->l('Always verify that your Backups files are complete, up-to-date and valid. Even if you had a success message during the Backup process.').'</li>
				<li>'.$this->l('Always check your data.').'</li>
				<li>'.$this->l('Never restore a Backup on a live site.').'</li>
			</ol>';
			
		if ($showForm)
			echo '
			<form action="'.$currentIndex.'&add'.$this->table.'&token='.$this->token.'" method="post" style="text-align: center;">
				<input type="submit" class="button" value="'.$this->l('I read the disclaimer - Create a new Backup').'" style="padding: 10px; font-weight: bold; border: 1px solid;" />
			</form>';
			
		echo '
		</div>
		<div class="warn width2" style="float: left;">
			<p>'.$this->l('How-to restore a database Backup in 10 easy steps').'</p>
			<ol style="font-size: 11px; font-weight: normal; line-height: 20px;">
				<li>'.$this->l('Turn off the "Enable Shop" option in the "Preferences" tab.').'</li>
				<li>'.$this->l('Download the Backup from the list below or from your FTP server (in the folder "admin/backups").').'</li>
				<li>'.$this->l('Check the Backup integrity: look for errors, incomplete file. Verify all your data.').'</li>
				<li>'.$this->l('Ask your hosting provider for a "phpMyAdmin" access to your database').'</li>
				<li>'.$this->l('Connect to "phpMyAdmin" and select your current database').'</li>
				<li>'.$this->l('Unless you enabled the "Drop existing tables" option, you must delete all tables from your current database.').'</li>
				<li>'.$this->l('At the top of the screen select the tab "Import"').'</li>
				<li>'.$this->l('Click on the "Browse..." button and select the Backup file from your hard drive').'</li>
				<li>'.$this->l('Check the max. allowed filesize (ie. Max: 16Mb)').'<br />'.$this->l('If your Backup file exceeds this limit, contact your hosting provider').'</li>
				<li>'.$this->l('Click on the "Go" button and wait during the import, the process can take several minutes').'</li>
			</ol>
		</div>		
		<div class="clear"></div>';
	}
	
	public function displayList()
	{
		global $currentIndex;

		// Test if the backup dir is writable
		if(!is_writable(PS_ADMIN_DIR.'/backups/'))
			$this->displayWarning($this->l('"Backups" Directory in admin directory must be writeable (CHMOD 755 / 777)'));

		$this->displayErrors();
		$this->displayHowTo();	
		
		parent::displayList();
	}

	public function getList($id_lang, $orderBy = NULL, $orderWay = NULL, $start = 0, $limit = NULL, $id_lang_shop = NULL)
	{
		global $cookie;
		
		if (!Validate::isTableOrIdentifier($this->table))
			die('filter is corrupted');
		if (empty($orderBy))
			$orderBy = Tools::getValue($this->table.'Orderby', $this->_defaultOrderBy);
		if (empty($orderWay))
			$orderWay = Tools::getValue($this->table.'Orderway', 'ASC');

		// Try and obtain getList arguments from $_GET
		$orderBy = Tools::getValue($this->table.'Orderby');
		$orderWay = Tools::getValue($this->table.'Orderway');

		// Validate the orderBy and orderWay fields
		switch ($orderBy)
		{
			case 'filename':
			case 'filesize':
			case 'date':
			case 'age':
				break;
			default:
				$orderBy = 'date';
		}
		switch ($orderWay)
		{
			case 'asc':
			case 'desc':
				break;
			default:
				$orderWay = 'desc';
		}
		if (empty($limit))
			$limit = ((!isset($cookie->{$this->table.'_pagination'})) ? $this->_pagination[0] : $limit = $cookie->{$this->table.'_pagination'});
		$limit = (int)(Tools::getValue('pagination', $limit));
		$cookie->{$this->table.'_pagination'} = $limit;

		/* Determine offset from current page */
		if (!empty($_POST['submitFilter'.$this->table]) AND	is_numeric($_POST['submitFilter'.$this->table]))
			$start = (int)($_POST['submitFilter'.$this->table] - 1) * $limit;
		$this->_lang = (int)($id_lang);
		$this->_orderBy = $orderBy;	
		$this->_orderWay = strtoupper($orderWay);
		$this->_list = array();

		// Find all the backups
		$dh = @opendir(PS_ADMIN_DIR.'/backups/');
		if ($dh === false)
		{
			$this->_errors[] = Tools::displayError('Unable to open backup directory .').addslashes(PS_ADMIN_DIR.'/backups/').'"';
			return;
		}
		while (($file = readdir($dh)) !== false)
		{
			if (preg_match('/^([\d]+-[a-z\d]+)\.sql(\.gz|\.bz2)?$/', $file, $matches) == 0)
				continue;
			$timestamp = (int)($matches[1]);
			$date = date('Y-m-d h:i:s', $timestamp);
			$age = time() - $timestamp;
			if ($age < 3600)
				$age = '< 1 '.$this->l('hour');
			else if ($age < 86400)
			{
				$age = floor($age / 3600);
				$age = $age.' '.(($age == 1) ? $this->l('hour') : $this->l('hours'));
			}
			else
			{
				$age = floor($age / 86400);
				$age = $age.' '.(($age == 1) ? $this->l('day') : $this->l('days'));
			}
			$size = filesize(PS_ADMIN_DIR.'/backups/'.$file);
			$this->_list[] = array(
				'filename' => $file, 
				'age' => $age,
				'date' => $date,
				'filesize' => number_format($size / 1000, 2).' Kb',
				'timestamp' => $timestamp,
				'filesize_sort' => $size,
			);
		}
		closedir($dh);
		$this->_listTotal = count($this->_list);

		// Sort the _list based on the order requirements
		switch ($this->_orderBy)
		{
			case 'filename':
				$this->_sortBy = 'filename';
				$sorter = 'str_sort';
				break;
			case 'filesize':
				$this->_sortBy = 'filesize_sort';
				$sorter = 'int_sort';
				break;
			case 'age':
			case 'date':
				$this->_sortBy = 'timestamp';
				$sorter = 'int_sort';
				break;
		}
		usort($this->_list, array($this, $sorter));
		$this->_list = array_slice($this->_list, $start, $limit);
	}
	
	public function int_sort($a, $b)
	{
		return $this->_orderWay == 'ASC' ? $a[$this->_sortBy] - $b[$this->_sortBy] : $b[$this->_sortBy] - $a[$this->_sortBy];
	}
	
	public function str_sort($a, $b)
	{
		return $this->_orderWay == 'ASC' ? strcmp($a[ $this->_sortBy], $b[$this->_sortBy]) : strcmp($b[ $this->_sortBy], $a[$this->_sortBy]);
	}
}
