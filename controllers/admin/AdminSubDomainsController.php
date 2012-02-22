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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminSubDomainsControllerCore extends AdminController
{
	public function __construct()
	{
		$this->table = 'subdomain';
		$this->className = 'SubDomain';
		$this->edit = true;
		$this->delete = true;

		$this->fieldsDisplay = array(
			'id_subdomain' => array('title' => $this->l('ID'), 'width' => 25),
			'name' => array('title' => $this->l('Subdomain'), 'width' => 200)
		);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Subdomains'),
				'image' => '../img/admin/subdomain.gif',
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Subdomains:'),
						'name' => 'name',
						'size' => '33',
						'required' => true,
						'hint' => $this->l('Invalid characters:').' <>;=#{}',
					),
				),
				'submit' => array(
					'title' => $this->l('   Save   '),
					'class' => 'button'
				)
			);


		parent::__construct();
	}
	
	public function renderList()
	{
		$this->addRowAction('delete');
		$this->warnings[] = $this->l('Cookies are different on each subdomain of your Website. If you want to use the same cookie, please add here the subdomains used by your shop. The most common is "www".');
		return parent::renderList();
	}

	
	public function postProcess()
	{
		$result = Db::getInstance()->executeS('
			SELECT `id_subdomain`
			FROM `'._DB_PREFIX_.'subdomain`
		');
		if (count($result) === 1)
			foreach ($result as $row)
				$this->_listSkipDelete = array($row['id_subdomain']);
		
		return parent::postProcess();
	}
}


