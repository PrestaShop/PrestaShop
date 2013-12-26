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

class AdminQuickAccessesControllerCore extends AdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
	 	$this->table = 'quick_access';
		$this->className = 'QuickAccess';
	 	$this->lang = true;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fields_list = array(
			'id_quick_access' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'name' => array(
				'title' => $this->l('Name')
			),
			'link' => array(
				'title' => $this->l('Link')
			),
			'new_window' => array(
				'title' => $this->l('New window'),
				'align' => 'center',
				'type' => 'bool',
				'activeVisu' => 'new_window',
				'class' => 'fixed-width-sm'
			)
		);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Quick Access menu'),
				'icon' => 'icon-align-justify'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'lang' => true,
					'maxlength' => 32,
					'required' => true,
					'hint' => $this->l('Forbidden characters:').' &lt;&gt;;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('URL:'),
					'name' => 'link',
					'maxlength' => 128,
					'required' => true,
					'hint' => $this->l('If it\'s a URL that comes from your Back Office, you must NOT use a security token.')
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Open in new window:'),
					'name' => 'new_window',
					'required' => false,
					'values' => array(
						array(
							'id' => 'new_window_on',
							'value' => 1,
							'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" />'
						),
						array(
							'id' => 'new_window_off',
							'value' => 0,
							'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" />'
						)
					)
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
			)
		);

		parent::__construct();
	}

	public function initPageHeaderToolbar()
	{
		if (empty($this->display))
			$this->page_header_toolbar_btn['new_quick_access'] = array(
				'href' => self::$currentIndex.'&amp;addquick_access&amp;token='.$this->token,
				'desc' => $this->l('Add new quick access'),
				'icon' => 'process-icon-new'
			);

		parent::initPageHeaderToolbar();
	}
}


