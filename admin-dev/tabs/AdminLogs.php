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

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');

class AdminLogs extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'log';
	 	$this->className = 'Logger';
	 	$this->lang = false;
	 	$this->edit = false;
	 	$this->delete = true;
		$this->noLink = true;
		$this->view = false;
				
		$this->fieldsDisplay = array(
		'id_log' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'severity' => array('title' => $this->l('Severity (1-4)'), 'align' => 'center', 'width' => 50),
		'message' => array('title' => $this->l('Message'), 'width' => 377),
		'object_type' => array('title' => $this->l('Object type'), 'width' => 75),
		'object_id' => array('title' => $this->l('Object ID'), 'width' => 50),
		'error_code' => array('title' => $this->l('Error code'), 'width' => 75, 'prefix' => '0x'),
		'date_add' => array('title' => $this->l('Date'), 'width' => 35, 'align' => 'right', 'type' => 'datetime'));
		
		$this->optionTitle = $this->l('Logs by e-mail');
		$this->_fieldsOptions = array(
			'PS_LOGS_BY_EMAIL' => array(
				'title' => $this->l('Minimum severity level:'), 
				'desc' => $this->l('Put "5" if you don\'t want to receive any emails.').'<br />'.$this->l('Emails will be sent to the shop owner.'), 
				'cast' => 'intval', 
				'type' => 'text', 
				'size' => 5
			)
		);
		
		parent::__construct();
	}
	
	public function displayListHeader($token = NULL)
	{			
			echo '
			<fieldset>
				<legend>'.$this->l('Severity levels').'</legend>
				<p>'.$this->l('Here\'s the meaning of severity levels:').'</p>
				<ol>
					<li style="color: green;">'.$this->l('Informative only').'</li>
					<li style="color: orange;">'.$this->l('Warning').'</li>
					<li style="color: orange;">'.$this->l('Error').'</li>
					<li style="color: red;">'.$this->l('Major issue (crash)').'</li>
				</ol>
			</fieldset>';
			
			parent::displayListHeader();
	}
}

?>
