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


class AdminSearchEngines extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'search_engine';
	 	$this->className = 'SearchEngine';
	 	$this->edit = true;
		$this->delete = true;
		
		$this->fieldsDisplay = array(
			'id_search_engine' => array('title' => $this->l('ID'), 'width' => 25),
			'server' => array('title' => $this->l('Server'), 'width' => 200),
			'getvar' => array('title' => $this->l('GET variable'), 'width' => 40));
			
		parent::__construct();
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
			<fieldset><legend>'.$this->l('Referrer').'</legend>
				<label>'.$this->l('Server').' </label>
				<div class="margin-form">
					<input type="text" size="20" name="server" value="'.htmlentities($this->getFieldValue($obj, 'server'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
				</div>
				<label>'.$this->l('$_GET variable').' </label>
				<div class="margin-form">
					<input type="text" size="40" name="getvar" value="'.htmlentities($this->getFieldValue($obj, 'getvar'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
}


