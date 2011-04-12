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

class AdminOrderMessage extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'order_message';
	 	$this->className = 'OrderMessage';
	 	$this->lang = true;
	 	$this->edit = true;
	 	$this->delete = true;
		
		$this->fieldsDisplay = array(
		'id_order_message' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25), 
		'name' => array('title' => $this->l('Name'), 'width' => 140),
		'message' => array('title' => $this->l('Message'), 'width' => 600, 'maxlength' => 300));
	
		parent::__construct();
	}
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;

		echo '
		<form action="'.$currentIndex.'&token='.$this->token.'&submitAdd'.$this->table.'=1" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/email.gif" />'.$this->l('Order messages').'</legend>
				<label>'.$this->l('Name:').' </label>
				<div class="margin-form">';
				foreach ($this->_languages as $language)
					echo '
					<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" size="53" name="name_'.$language['id_lang'].'" value="'.$this->getFieldValue($obj, 'name', (int)($language['id_lang'])).'" /><sup> *</sup>
					</div>';
				$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'name¤message', 'name');
				echo '</div>
				<div class="clear"></div><br />
				<label>'.$this->l('Message:').' </label>
				<div class="margin-form">';
				foreach ($this->_languages as $language)
					echo '
					<div id="message_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<textarea rows="15" cols="50" name="message_'.$language['id_lang'].'">'.$this->getFieldValue($obj, 'message', (int)($language['id_lang'])).'</textarea><sup> *</sup>
					</div>';
				$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'name¤message', 'message');
				echo '</div>
				<div class="clear"></div><br />
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required fields').'</div>
			</fieldset>
		</form>';
	}
}


