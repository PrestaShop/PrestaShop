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

class AdminSubDomains extends AdminTab
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
		parent::__construct();
	}
	
	public function displayList()
	{
		$this->displayWarning($this->l('Cookies are different on each subdomain of your Website. If you want to use the same cookie, please add here the subdomains used by your shop. The most common is "www".'));
		return parent::displayList();
	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;

		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/subdomain.gif" /> '.$this->l('Subdomains').'</legend>
				<label>'.$this->l('Subdomain:').' </label>
				<div class="margin-form">
					<input type="text" size="15" name="name" value="'.htmlentities($this->getFieldValue($obj, 'name'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					<p class="clear">'.$this->l('Additional subdomain').'</p>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
	
	public function postProcess()
	{
		$result = Db::getInstance()->ExecuteS('
			SELECT `id_subdomain`
			FROM `'._DB_PREFIX_.'subdomain`
		');
		if (sizeof($result) === 1)
			foreach ($result AS $row)
				$this->_listSkipDelete = array($row['id_subdomain']);
		
		return parent::postProcess();
	}
}


