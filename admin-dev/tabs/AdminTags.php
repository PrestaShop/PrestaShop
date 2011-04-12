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

class AdminTags extends AdminTab
{
	public function  __construct()
	{
		global $cookie;
		
		$this->table = 'tag';
		$this->className = 'Tag';
		$this->edit = true;
		$this->delete = true;

		$this->fieldsDisplay = array(
		'id_tag' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25, 'filter_key' => 'a!id_seller_message'),
		'lang' => array('title' => $this->l('Language'), 'filter_key' => 'l!name'),
		'name' => array('title' => $this->l('Name'), 'width' => 200, 'filter_key' => 'a!name'),
		'products' => array('title' => $this->l('Products'), 'align' => 'right', 'havingFilter' => true));

		$this->_select = 'l.name as lang, COUNT(pt.id_product) as products';
		$this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'product_tag` pt ON (a.`id_tag` = pt.`id_tag`)
		LEFT JOIN `'._DB_PREFIX_.'lang` l ON (l.`id_lang` = a.`id_lang`)';
		$this->_group = 'GROUP BY a.name, a.id_lang';

		parent::__construct();
	}
	
	public function postProcess()
	{		
		if ($this->tabAccess['edit'] === '1' AND Tools::getValue('submitAdd'.$this->table))
			if ($id = (int)(Tools::getValue($this->identifier)) AND $obj = new $this->className($id) AND Validate::isLoadedObject($obj))
				$obj->setProducts($_POST['products']);
		return parent::postProcess();
	}
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;
		$products1 = $obj->getProducts(true);
		$products2 = $obj->getProducts(false);
		
		echo '
		<form id="formTag" action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post" class="width5">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/t/AdminTags.gif" />'.$this->l('Tag').'</legend>
				<label>'.$this->l('Name').' </label>
				<div class="margin-form">
					<input type="text" size="33" name="name" value="'.htmlentities($this->getFieldValue($obj, 'name'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
				</div>
				<label>'.$this->l('Language').' </label>
				<div class="margin-form">
					<select name="id_lang">
						<option value="">-</option>';
		foreach ($this->_languages as $language)
			echo '		<option value="'.$language['id_lang'].'" '.($language['id_lang'] == $this->getFieldValue($obj, 'id_lang') ? 'selected="selected"' : '').'>'.$language['name'].'</option>';
		echo '		</select> <sup>*</sup>
				</div>
				<h3>'.$this->l('Products').' </h3>
				<table>
					<tr>
						<td>
							<select multiple id="select1" name="products[]" style="width:300px;height:160px;">';
		foreach ($products1 as $product)
			echo '				<option value="'.$product['id_product'].'">'.$product['name'].'</option>';
		echo '				</select><br /><br />
							<a href="#" id="add"
							style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px">
								'.$this->l('Remove').' &gt;&gt;
							</a>
						</td>
						<td style="padding-left:20px;">
							<select multiple id="select2" style="width:300px;height:160px;">';
		foreach ($products2 as $product)
			echo '				<option value="'.$product['id_product'].'">'.$product['name'].'</option>';
		echo '				</select><br /><br />
							<a href="#" id="remove"
							style="text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px">
								&lt;&lt; '.$this->l('Add').'
							</a>
						</div>
						</td>
					</tr>
				</table>
				<div class="clear">&nbsp;</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>
		<script type="text/javascript">  
			$().ready(function() {  
				$(\'#add\').click(function() {  
					return !$(\'#select1 option:selected\').remove().appendTo(\'#select2\');  
				});  
				$(\'#remove\').click(function() {  
					return !$(\'#select2 option:selected\').remove().appendTo(\'#select1\');  
				});  
			});
			$(\'#formTag\').submit(function() {  
				$(\'#select1 option\').each(function(i) {  
					$(this).attr("selected", "selected");  
				});  
			}); 
		</script>';
	}
}


