<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminLogsControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'log';
	 	$this->className = 'Logger';
	 	$this->lang = false;
		$this->noLink = true;

		$this->fields_list = array(
			'id_log' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'employee' => array('title' => $this->l('Employee'), 'align' => 'center', 'width' => 100),
			'severity' => array('title' => $this->l('Severity (1-4)'), 'align' => 'center', 'width' => 50),
			'message' => array('title' => $this->l('Message')),
			'object_type' => array('title' => $this->l('Object type'), 'width' => 75),
			'object_id' => array('title' => $this->l('Object ID'), 'width' => 50),
			'error_code' => array('title' => $this->l('Error code'), 'width' => 75, 'prefix' => '0x'),
			'date_add' => array('title' => $this->l('Date'), 'width' => 150, 'align' => 'right', 'type' => 'datetime')
		);

		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('Logs by email'),
				'fields' =>	array(
					'PS_LOGS_BY_EMAIL' => array(
						'title' => $this->l('Minimum severity level'),
						'desc' => $this->l('Enter "5" if you do not want to receive any emails.').'<br />'.$this->l('Emails will be sent to the shop owner.'),
						'cast' => 'intval',
						'type' => 'text',
						'size' => 5
					)
				),
				'submit' => array()
			)
		);
		$this->list_no_link = true;
		$this->_select .= 'CONCAT(LEFT(e.firstname, 1), \'. \', e.lastname) employee';
		$this->_join .= ' LEFT JOIN '._DB_PREFIX_.'employee e ON (a.id_employee = e.id_employee)';
		
		// Set up GUI to clean up logs
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected log entries?')));
		parent::__construct();
	}
	
	public function processDelete()
	{
		return Logger::eraseAllLogs();
	}

	public function initToolbar()
	{
		parent::initToolbar();
		$this->toolbar_btn['delete'] = array(
			'short' => 'Erase',
			'desc' => $this->l('Erase all'),
			'js' => 'if (confirm(\''.$this->l('Are you sure?').'\')) document.location = \''.$this->context->link->getAdminLink('AdminLogs').'&amp;token='.$this->token.'&deletelog=1\';'
		);
		unset($this->toolbar_btn['new']);
	}
	
	/**
	 * Delete multiple logs without reporting it into the logger
	 *
	 * @return boolean true if succcess
	 */
	public function processBulkDelete()
	{
		if (is_array($this->boxes) && !empty($this->boxes))
		{
			$object = new $this->className();

			if (isset($object->noZeroObject))
			{
				$objects_count = count(call_user_func(array($this->className, $object->noZeroObject)));

				// Check if all object will be deleted
				if ($objects_count <= 1 || count($this->boxes) == $objects_count)
					$this->errors[] = Tools::displayError('You need at least one object.').
						' <b>'.$this->table.'</b><br />'.
						Tools::displayError('You cannot delete all of the items.');
			}
			else
			{
				$result = true;
				foreach ($this->boxes as $id)
				{
					$to_delete = new $this->className($id);
					$delete_ok = true;
					if ($this->deleted)
					{
						$to_delete->deleted = 1;
						if (!$to_delete->update())
						{
							$result = false;
							$delete_ok = false;
						}
					}
					else
						if (!$to_delete->delete())
						{
							$result = false;
							$delete_ok = false;
						}

					if (!$delete_ok)
						$this->errors[] = sprintf(Tools::displayError('Can\'t delete #%d'), $id);
				}
				if ($result)
					$this->redirect_after = self::$currentIndex.'&conf=2&token='.$this->token;
				$this->errors[] = Tools::displayError('An error occurred while deleting this selection.');
			}
		}
		else
			$this->errors[] = Tools::displayError('You must select at least one element to delete.');

		if (isset($result))
			return $result;
		else
			return false;
	}

}
