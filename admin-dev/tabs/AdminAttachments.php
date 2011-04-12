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

class AdminAttachments extends AdminTab
{
	public function __construct()
	{
		global $cookie;
		
	 	$this->table = 'attachment';
	 	$this->className = 'Attachment';
	 	$this->lang = true;
	 	$this->edit = true;
	 	$this->delete = true;
		
		$this->fieldsDisplay = array(
		'id_attachment' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'name' => array('title' => $this->l('Name')),
		'file' => array('title' => $this->l('File')));
	
		parent::__construct();
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			if ($id = (int)(Tools::getValue('id_attachment')) AND $a = new Attachment($id))
			{
				$_POST['file'] = $a->file;
				$_POST['mime'] = $a->mime;
			}
			if (!sizeof($this->_errors))
			{
				if (isset($_FILES['file']) AND is_uploaded_file($_FILES['file']['tmp_name']))
				{
					if ($_FILES['file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024))
						$this->_errors[] = $this->l('File too large, maximum size allowed:').' '.(Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024).' '.$this->l('kb').'. '.$this->l('File size you\'re trying to upload is:').number_format(($_FILES['attachment_file']['size']/1024), 2, '.', '').$this->l('kb');
					else
					{
						do $uniqid = sha1(microtime());	while (file_exists(_PS_DOWNLOAD_DIR_.$uniqid));
						if (!copy($_FILES['file']['tmp_name'], _PS_DOWNLOAD_DIR_.$uniqid))
							$this->_errors[] = $this->l('File copy failed');
						$_POST['file_name'] = $_FILES['file']['name'];
						@unlink($_FILES['file']['tmp_name']);
						$_POST['file'] = $uniqid;
						$_POST['mime'] = $_FILES['file']['type'];
					}
				}
				else if (array_key_exists('attachment_file', $_FILES) && (int)$_FILES['attachment_file']['error'] === 1) 
				{
					$max_upload = (int)(ini_get('upload_max_filesize'));
					$max_post = (int)(ini_get('post_max_size'));
					$upload_mb = min($max_upload, $max_post);
					$this->_errors[] = $this->l('the File').' <b>'.$_FILES['attachment_file']['name'].'</b> '.$this->l('exceeds the size allowed by the server. This limit is set to').' <b>'.$upload_mb.$this->l('Mb').'</b>';
				}
				else if (!empty($_FILES['file']['tmp_name']))
					$this->_errors[] = $this->l('No file or your file isn\'t uploadable, check your server configuration about the upload maximum size.');
			}
			$this->validateRules();
		}
		return parent::postProcess();
	}
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;
		
		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post" enctype="multipart/form-data">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/t/AdminAttachments.gif" />'.$this->l('Attachment').'</legend>
				<label>'.$this->l('Filename:').' </label>
				<div class="margin-form">';
		foreach ($this->_languages as $language)
			echo '	<div id="cname_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="name_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'name', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
					</div>';							
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'cname¤cdescription', 'cname');
		echo '	</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Description:').' </label>
				<div class="margin-form">';
		foreach ($this->_languages as $language)
			echo '	<div id="cdescription_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<textarea name="description_'.$language['id_lang'].'">'.htmlentities($this->getFieldValue($obj, 'description', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'</textarea>
					</div>';							
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'cname¤cdescription', 'cdescription');
		echo '	</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('File').'</label>
				<div class="margin-form">
					<p><input type="file" name="file" /></p>
					<p>'.$this->l('Upload file from your computer').'</p>
				</div>
				<div class="clear">&nbsp;</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
}
