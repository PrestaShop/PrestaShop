<?php
/*
* 2007-2012 PrestaShop
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
*  @version  Release: $Revision: 7465 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminPdfControllerCore extends AdminController
{
	private $encoding_list = array();

	private $font_list = array();

	public function __construct()
	{
		$this->className = 'Configuration';
		$this->table = 'configuration';
		$this->lang = true;
		$this->context = Context::getContext();

		/* Collect all encoding map files and build array for combo box */
		$encoding_files = scandir(_PS_FPDF_PATH_.'font/makefont');
		$arr = array();

		foreach ($encoding_files as $file)
			if (substr($file, -4) == '.map')
			{
				$arr['mode'] = substr($file, 0, -4);
				$arr['name'] = substr($file, 0, -4);
				array_push($this->encoding_list, $arr);
			}

		/* Collect all font files and build array for combo box */
		$font_files = scandir(_PS_FPDF_PATH_.'font');
		$arr = array();

		foreach ($font_files as $file)
			if (substr($file, -4) == '.php' &&
					$file != 'index.php' &&
					substr($file, -6) != 'bi.php' &&
					substr($file, -5) != 'b.php' &&
					substr($file, -5) != 'i.php')
			{
				$arr['mode'] = substr($file, 0, -4);
				$arr['name'] = substr($file, 0, -4);
				array_push($this->font_list, $arr);
			}

		$this->options = array(
			'PDF' => array(
				'title' =>	$this->l('PDF settings for the current language:').' '.$this->context->language->name,
				'icon' =>	'pdf',
				'class' =>	'width2',
				'fields' =>	array(
                    'PS_PDF_USE_CACHE' => array(
                        'title' => $this->l('Use disk as cache'),
                        'desc' => $this->l('Save memory but slow down the rendering process.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    )
				),
				'submit' => array()
			)
		);

		parent::__construct();
	}
}
