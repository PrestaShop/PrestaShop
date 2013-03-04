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

class AdminAttachmentsControllerCore extends AdminController
{

	protected $product_attachements = array();

	public function __construct()
	{
	 	$this->table = 'attachment';
		$this->className = 'Attachment';
	 	$this->lang = true;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->fields_list = array(
			'id_attachment' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name')
			),
			'file' => array(
				'title' => $this->l('File')
			)
		);

		parent::__construct();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Attachment'),
				'image' => '../img/t/AdminAttachments.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Filename:'),
					'name' => 'name',
					'size' => 33,
					'required' => true,
					'lang' => true,
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Description:'),
					'name' => 'description',
					'cols' => 40,
					'rows' => 10,
					'lang' => true,
				),
				array(
					'type' => 'file',
					'label' => $this->l('File:'),
					'name' => 'file',
					'desc' => $this->l('Upload a file from your computer.')
				),
			),
			'submit' => array(
				'title' => $this->l('Save   '),
				'class' => 'button'
			)
		);

		return parent::renderForm();
	}

	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList((int)$id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		if (count($this->_list))
		{
			$this->product_attachements = Attachment::getProductAttached((int)$id_lang, $this->_list);

			$list_product_list = array();
			foreach ($this->_list as $list)
			{
				$product_list = '';
				if (isset($this->product_attachements[$list['id_attachment']]))
				{
					foreach ($this->product_attachements[$list['id_attachment']] as $product)
						$product_list .= $product.', ';
				}
				$list_product_list[$list['id_attachment']] = $product_list;
			}

			// Assign array in list_action_delete.tpl
			$this->tpl_delete_link_vars = array(
				'product_list' => $list_product_list,
				'product_attachements' => $this->product_attachements
			);
		}
	}

	public function postProcess()
	{
		if (_PS_MODE_DEMO_)
		{
			$this->errors[] = Tools::displayError('This functionality has been disabled.');
			return;
		}

		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			$id = (int)Tools::getValue('id_attachment');
			if ($id && $a = new Attachment($id))
			{
				$_POST['file'] = $a->file;
				$_POST['mime'] = $a->mime;
			}
			if (!count($this->errors))
			{
				if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name']))
				{
					if ($_FILES['file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024))
						$this->errors[] = sprintf(
							$this->l('The file is too large. Maximum size allowed is: %1$d kB. The file you\'re trying to upload is:  %2$d kB.'),
							(Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024),
							number_format(($_FILES['file']['size'] / 1024), 2, '.', '')
						);
					else
					{
						do $uniqid = sha1(microtime());
						while (file_exists(_PS_DOWNLOAD_DIR_.$uniqid));
						if (!copy($_FILES['file']['tmp_name'], _PS_DOWNLOAD_DIR_.$uniqid))
							$this->errors[] = $this->l('Failed to copy the file.');
						$_POST['file_name'] = $_FILES['file']['name'];
						@unlink($_FILES['file']['tmp_name']);
						$_POST['file'] = $uniqid;
						$_POST['mime'] = $_FILES['file']['type'];
					}
				}
				else if (array_key_exists('file', $_FILES) && (int)$_FILES['file']['error'] === 1)
				{
					$max_upload = (int)ini_get('upload_max_filesize');
					$max_post = (int)ini_get('post_max_size');
					$upload_mb = min($max_upload, $max_post);
					$this->errors[] = sprintf(
						$this->l('The file %1$s exceeds the size allowed by the server. The limit is set to %2$d MB.'),
						'<b>'.$_FILES['file']['name'].'</b> ',
						'<b>'.$upload_mb.'</b>'
					);
				}
				else if (!empty($_FILES['file']['tmp_name']))
					$this->errors[] = $this->l('Upload error.  Please check your server configurations for the maximum upload size allowed.');
			}
			$this->validateRules();
		}
		return parent::postProcess();
	}
}
