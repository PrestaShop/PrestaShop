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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminAliasesControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'alias';
		$this->className = 'Alias';
	 	$this->lang = false;
		$this->requiredDatabase = true;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fieldsDisplay = array(
			'alias' => array('title' => $this->l('Aliases'), 'width' => 'auto'),
			'search' => array('title' => $this->l('Search'), 'width' => 100),
			'active' => array('title' => $this->l('Status'), 'width' => 25, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false)
		);

		parent::__construct();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Aliases'),
				'image' => '../img/admin/search.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Alias:'),
					'name' => 'alias',
					'size' => 40,
					'required' => true,
					'desc' => array(
						$this->l('Enter each alias separated by a comma (\',\') (e.g., \'prestshop,preztashop,prestasohp\')'),
						$this->l('Forbidden characters: <>;=#{}')
					)
				),
				array(
					'type' => 'text',
					'label' => $this->l('Result:'),
					'name' => 'search',
					'size' => 15,
					'required' => true,
					'desc' => $this->l('Search this word instead.')
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		$this->fields_value = array('alias' => $this->object->getAliases());

		return parent::renderForm();
	}

	public function postProcess()
	{
		if (isset($_POST['submitAdd'.$this->table]))
		{
			$search = strval(Tools::getValue('search'));
			$string = strval(Tools::getValue('alias'));
		 	$aliases = explode(',', $string);
			if (empty($search) || empty($string))
				$this->errors[] = $this->l('aliases and result are both required');
			if (!Validate::isValidSearch($search))
				$this->errors[] = $search.' '.$this->l('is not a valid result');
		 	foreach ($aliases as $alias)
				if (!Validate::isValidSearch($alias))
					$this->errors[] = $alias.' '.$this->l('is not a valid alias');

			if (!count($this->errors))
			{
			 	foreach ($aliases as $alias)
			 	{
					$obj = new Alias(null, trim($alias), trim($search));
						$obj->save();
			 	}
			}
		}
		else
			parent::postProcess();
	}
}


