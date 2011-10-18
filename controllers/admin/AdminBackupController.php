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

class AdminBackupControllerCore extends AdminController
{
	/** @var string The field we are sorting on */
	protected $_sortBy = 'date';

	public function __construct()
	{
		$this->table = 'backup';
		$this->className = 'Backup';
		parent::__construct();

		$this->addRowAction('delete');
		$this->addRowAction('view');

	 	$this->deleted = false;

		$this->requiredDatabase = false;

		$this->fieldsDisplay = array (
			'date' => array('title' => $this->l('Date'), 'type' => 'datetime', 'width' => 120, 'align' => 'right'),
			'age' => array('title' => $this->l('Age')),
			'filename' => array('title' => $this->l('File name'), 'width' => 200),
			'filesize' => array('title' => $this->l('File size'))
		);

		$this->options = array(
			'general' => array(
				'title' =>	$this->l('Backup options'),
				'fields' =>	array(
					'PS_BACKUP_ALL' => array('title' => $this->l('Ignore statistics tables:'),
						'desc' => $this->l('The following tables will NOT be backed up if you enable this option:').'<br />'._DB_PREFIX_.'connections, '._DB_PREFIX_.'connections_page, '._DB_PREFIX_.'connections_source, '._DB_PREFIX_.'guest, '._DB_PREFIX_.'statssearch', 'cast' => 'intval', 'type' => 'bool'),
					'PS_BACKUP_DROP_TABLE' => array('title' => $this->l('Drop existing tables during import:'),
						'desc' => $this->l('Select this option to instruct the backup file to drop your tables prior to restoring the backed up data').'<br />(ie. "DROP TABLE IF EXISTS")', 'cast' => 'intval', 'type' => 'bool'),
				),
			),
		);
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

	public function initContent()
	{
		if ($this->display == 'view')
		{
			if (!($object = $this->loadObject()))
				$this->_errors[] = Tools::displayError('The object could not be loaded.');
			if ($object->id)
			{
				$this->context->smarty->assign(array(
					'url_backup' => $object->getBackupURL()
				));
			}
			elseif ($object->error)
				$this->_errors[] = $object->error;
		}
		else
		{
			if ($this->display == 'add')
			{
				$download = true;
				$show_form = false;
			}
			else
			{
				$this->display = 'list';
				$show_form = true;
			}

			$this->context->smarty->assign(array(
				'current' => self::$currentIndex,
				'show_form' => $show_form,
				'download' => isset($download) ? $download : null,
				'how_to' => true,
			));
		}
		parent::initContent();

		if ($this->display == 'list')
		{
			$helper = new HelperOptions();
			$helper->id = $this->id;
			$helper->currentIndex = self::$currentIndex;
			$this->content .= $helper->generateOptions($this->options);
		}
	}

	public function postProcess()
	{
		// Test if the backup dir is writable
		if(!is_writable(_PS_ADMIN_DIR_.'/backups/'))
			$this->warnings[] = $this->l('"Backups" Directory in admin directory must be writeable (CHMOD 755 / 777)');

		if($this->action == 'new' && is_writable(_PS_ADMIN_DIR_.'/backups/'))
		{
			if (($object = $this->loadObject()))
			{
				if ($object->add())
				{
					$this->context->smarty->assign(array(
						'conf' => $this->l('It appears that the Backup was successful, however, you must download and carefully verify the Backup file.'),
						'backup_url' => $object->getBackupURL(),
						'backup_weight' => number_format((filesize($object->id)*0.000001), 2, '.', '')
					));
				}
				elseif ($object->error)
					$this->_errors[] = $object->error;
			}
		}

		parent::postProcess();
	}

	public function getList($id_lang, $orderBy = NULL, $orderWay = NULL, $start = 0, $limit = NULL, $id_lang_shop = NULL)
	{

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
			$limit = ((!isset($this->context->cookie->{$this->table.'_pagination'})) ? $this->_pagination[0] : $limit = $this->context->cookie->{$this->table.'_pagination'});
		$limit = (int)(Tools::getValue('pagination', $limit));
		$this->context->cookie->{$this->table.'_pagination'} = $limit;

		/* Determine offset from current page */
		if (!empty($_POST['submitFilter'.$this->table]) AND	is_numeric($_POST['submitFilter'.$this->table]))
			$start = (int)($_POST['submitFilter'.$this->table] - 1) * $limit;
		$this->_lang = (int)($id_lang);
		$this->_orderBy = $orderBy;
		$this->_orderWay = strtoupper($orderWay);
		$this->_list = array();

		// Find all the backups
		$dh = @opendir(_PS_ADMIN_DIR_.'/backups/');
		if ($dh === false)
		{
			$this->_errors[] = Tools::displayError('Unable to open backup directory .').addslashes(_PS_ADMIN_DIR_.'/backups/').'"';
			return;
		}
		while (($file = readdir($dh)) !== false)
		{
			if (preg_match('/^([\d]+-[a-z\d]+)\.sql(\.gz|\.bz2)?$/', $file, $matches) == 0)
				continue;
			$timestamp = (int)($matches[1]);
			$date = date('Y-m-d H:i:s', $timestamp);
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
			$size = filesize(_PS_ADMIN_DIR_.'/backups/'.$file);
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
