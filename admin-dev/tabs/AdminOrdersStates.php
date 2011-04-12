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

class AdminOrdersStates extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'order_state';
	 	$this->className = 'OrderState';
	 	$this->lang = true;
	 	$this->edit = true;
	 	$this->delete = true;
		$this->colorOnBackground = true;
 
		$this->fieldImageSettings = array('name' => 'icon', 'dir' => 'os');
		$this->imageType = 'gif';

		$this->fieldsDisplay = array(
		'id_order_state' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'name' => array('title' => $this->l('Name'), 'width' => 130),
		'logo' => array('title' => $this->l('Icon'), 'align' => 'center', 'image' => 'os', 'orderby' => false, 'search' => false),
		'send_email' => array('title' => $this->l('Send e-mail to customer'), 'align' => 'center', 'icon' => array('1' => 'enabled.gif', '0' => 'disabled.gif'), 'type' => 'bool', 'orderby' => false),
		'invoice' => array('title' => $this->l('Invoice'), 'align' => 'center', 'icon' => array('1' => 'enabled.gif', '0' => 'disabled.gif'), 'type' => 'bool', 'orderby' => false),
		'template' => array('title' => $this->l('E-mail template'), 'width' => 100));
		
		parent::__construct();
	}
	
	public function postProcess()
	{
		global $cookie;
		
		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			$_POST['invoice'] = Tools::getValue('invoice');
			$_POST['logable'] = Tools::getValue('logable');
			$_POST['send_email'] = Tools::getValue('send_email');
			$_POST['hidden'] = Tools::getValue('hidden');
			if (!$_POST['send_email'])
			{
				$languages = Language::getLanguages(false);
				foreach ($languages AS $language)
					$_POST['template_'.$language['id_lang']] = '';
			}
			parent::postProcess();
		}
		elseif (isset($_GET['delete'.$this->table]))
		{
		 	$orderState = new OrderState((int)($_GET['id_order_state']), $cookie->id_lang);
		 	if (!$orderState->isRemovable())
		 		$this->_errors[] = $this->l('For security reasons, you cannot delete default order statuses.');
		 	else
		 		parent::postProcess();
		}
		elseif (isset($_POST['submitDelorder_state']))
		{
		 	foreach ($_POST[$this->table.'Box'] AS $selection)
		 	{
			 	$orderState = new OrderState((int)($selection), $cookie->id_lang);
			 	if (!$orderState->isRemovable())
			 	{
			 		$this->_errors[] = $this->l('For security reasons, you cannot delete default order statuses.');
			 		break;
			 	}
			}
			if (empty($this->_errors))
				parent::postProcess();
		}
		else
			parent::postProcess();
	}
	
	private function getTemplates($iso_code)
	{
		$array = array();
		if (!file_exists(PS_ADMIN_DIR.'/../mails/'.$iso_code))
			return false;
		$templates = scandir(PS_ADMIN_DIR.'/../mails/'.$iso_code);
		foreach ($templates AS $template)
			if (!strncmp(strrev($template), 'lmth.', 5))
				$array[] = substr($template, 0, -5);
		return $array;
	}
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;

		echo '<script type="text/javascript" src="../js/jquery/jquery-colorpicker.js"></script>
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post" enctype="multipart/form-data">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/time.gif" />'.$this->l('Order statuses').'</legend>
				<label>'.$this->l('Status name:').' </label>
				<div class="margin-form">';

				foreach ($this->_languages as $language)
					echo '
					<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="40" type="text" name="name_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'name', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" style="width: 150px;" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters: numbers and').' !<>,;?=+()@#"�{}_$%:<span class="hint-pointer">&nbsp;</span></span>
						</div>';							
				$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'name¤template', 'name');

		echo '		<p class="clear">'.$this->l('Order status (e.g., \'Pending\')').'</p>
				</div>
				<div class="clear"></div>
				<label>'.$this->l('Icon:').' </label>
				<div class="margin-form">
					<input type="file" name="icon" />
					<p>'.$this->l('Upload an icon from your computer (File type: .gif, suggested size: 16x16)').'</p>
				</div>
				<label>'.$this->l('Color:').' </label>
				<div class="margin-form">
					<input type="color" data-hex="true" class="color mColorPickerInput" name="color" value="'.htmlentities($this->getFieldValue($obj, 'color'), ENT_COMPAT, 'UTF-8').'" />
					<p>'.$this->l('Status will be highlighted in this color. HTML colors only (e.g.,').' "lightblue", "#CC6600")</p>
				</div>
				<div class="margin-form">
					<p>
						<input type="checkbox" style="vertical-align: text-bottom;" name="logable"'.(($this->getFieldValue($obj, 'logable') == 1) ? ' checked="checked"' : '').' id="logable_on" value="1" />
						<label class="t" for="logable_on"> '.$this->l('Consider the associated order as validated').'</label>
					</p>
				</div>
				<div class="margin-form">
					<p>
						<input type="checkbox" style="vertical-align: text-bottom;" name="invoice"'.(($this->getFieldValue($obj, 'invoice') == 1) ? ' checked="checked"' : '').' id="invoice_on" value="1" />
						<label class="t" for="invoice_on"> '.$this->l('Allow customer to download and view PDF version of invoice').'</label>
					</p>
				</div>
				<div class="margin-form">
					<p>
						<input type="checkbox" style="vertical-align: text-bottom;" name="hidden"'.(($this->getFieldValue($obj, 'hidden') == 1) ? ' checked="checked"' : '').' id="hidden_on" value="1" />
						<label class="t" for="hidden_on"> '.$this->l('Hide this state in order for customer').'</label>
					</p>
				</div>
				<div class="margin-form">
					<p>
						<input type="checkbox" style="vertical-align: text-bottom;" id="send_email" name="send_email" onclick="$(\'#tpl\').slideToggle();"'.
					(($this->getFieldValue($obj, 'send_email')) ? 'checked="checked"' : '').' value="1" />
						<label class="t" for="send_email"> '.$this->l('Send e-mail to customer when order is changed to this status').'</label>
					</p>
				</div>				
				<div id="tpl" style="display: '.($this->getFieldValue($obj, 'send_email') ? 'block' : 'none').';">
					<label>'.$this->l('Template').'</label>
					<div class="margin-form">';
			foreach ($this->_languages as $language)
			{
				$templates = $this->getTemplates($language['iso_code']);
				echo '	<div id="template_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">';
				if (!$templates)
					echo '<strong>'.$this->l('Please first copy your e-mail templates in the directory').' mails/'.$language['iso_code'].'.</strong>';
				else
				{
					echo '		<select	name="template_'.$language['id_lang'].'" id="template_select_'.$language['id_lang'].'">';
					foreach ($templates AS $template)
						echo '		<option value="'.$template.'" '.(($this->getFieldValue($obj, 'template', (int)($language['id_lang'])) == $template) ? 'selected="selected"' : '').'>'.$template.'</option>';
					echo '		</select>';
				}
				echo '			<span class="hint" name="help_box">'.$this->l('Only letters, number and -_ are allowed').'<span class="hint-pointer">&nbsp;</span></span>
								<img onclick="viewTemplates(\'template_select_'.$language['id_lang'].'\', '.$language['id_lang'].', \'../mails/'.$language['iso_code'].'/\', \'.html\');" src="../img/t/AdminFeatures.gif" class="pointer" alt="'.$this->l('Preview').'" title="'.$this->l('Preview').'" />
						</div>';
			}
			$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'name¤template', 'template');
			echo '<p style="clear: both">'.$this->l('E-mail template for both .html and .txt').'</p>
					</div>
				</div>
				<script type="text/javascript">if (getE(\'send_email\').checked) getE(\'tpl\').style.display = \'block\'; else getE(\'tpl\').style.display = \'none\';</script>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
}


