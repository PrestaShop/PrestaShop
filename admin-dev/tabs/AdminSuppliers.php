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

class AdminSuppliers extends AdminTab
{
	protected $maxImageSize = 200000;

	public function __construct()
	{
	 	$this->table = 'supplier';
	 	$this->className = 'Supplier';
	 	$this->view = true;
	 	$this->edit = true;
	 	$this->delete = true;
		$this->_select = 'COUNT(p.`id_product`) AS products';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product` p ON (a.`id_supplier` = p.`id_supplier`)';
		$this->_group = 'GROUP BY a.`id_supplier`';
		
 		$this->fieldImageSettings = array('name' => 'logo', 'dir' => 'su');
		
		$this->fieldsDisplay = array(
			'id_supplier' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'name' => array('title' => $this->l('Name'), 'width' => 120),
			'logo' => array('title' => $this->l('Logo'), 'align' => 'center', 'image' => 'su', 'orderby' => false, 'search' => false),
			'products' => array('title' => $this->l('Number of products'), 'align' => 'right', 'filter_type' => 'int', 'tmpTableFilter' => true),
			'active' => array('title' => $this->l('Enabled'), 'width' => 25, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false)
		);
	
		parent::__construct();
	}
	
	public function viewsupplier()
	{
		global $cookie;
		if (!($supplier = $this->loadObject()))
			return;	
		echo '<h2>'.$supplier->name.'</h2>';
		
		$products = $supplier->getProductsLite((int)($cookie->id_lang));
		echo '<h3>'.$this->l('Total products:').' '.sizeof($products).'</h3>';
		foreach ($products AS $product)
		{
			$product = new Product($product['id_product'], false, (int)($cookie->id_lang));
			echo '<hr />';
			if (!$product->hasAttributes())
			{
				echo '
				<table border="0" cellpadding="0" cellspacing="0" class="table width3">
					<tr>
						<th><a href="index.php?tab=AdminCatalog&id_product='.$product->id.'&addproduct&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'" target="_blank">'.$product->name.'</a></th>
						'.(!empty($product->reference) ? '<th width="150">'.$this->l('Ref:').' '.$product->reference.'</th>' : '').'
						'.(!empty($product->ean13) ? '<th width="120">'.$this->l('EAN13:').' '.$product->ean13.'</th>' : '').'
						'.(!empty($product->upc) ? '<th width="120">'.$this->l('UPC:').' '.$product->upc.'</th>' : '').'
						'.(Configuration::get('PS_STOCK_MANAGEMENT') ? '<th class="right" width="50">'.$this->l('Qty:').' '.$product->quantity.'</th>' : '').'
					</tr>
				</table>';
			}
			else
			{
				echo '
				<h3><a href="index.php?tab=AdminCatalog&id_product='.$product->id.'&addproduct&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'" target="_blank">'.$product->name.'</a></h3>
				<table border="0" cellpadding="0" cellspacing="0" class="table" style="width: 600px;">
	                	<tr>
		                    <th>'.$this->l('Attribute name').'</th>
		                    <th width="80">'.$this->l('Reference').'</th>
		                    <th width="80">'.$this->l('EAN13').'</th>
							<th width="80">'.$this->l('UPC').'</th>
		                   '.(Configuration::get('PS_STOCK_MANAGEMENT') ? '<th class="right" width="40">'.$this->l('Quantity').'</th>' : '').'
	                	</tr>';
			     	/* Build attributes combinaisons */
				$combinaisons = $product->getAttributeCombinaisons((int)($cookie->id_lang));
				foreach ($combinaisons AS $k => $combinaison)
				{
					$combArray[$combinaison['id_product_attribute']]['reference'] = $combinaison['reference'];
					$combArray[$combinaison['id_product_attribute']]['ean13'] = $combinaison['ean13'];
					$combArray[$combinaison['id_product_attribute']]['upc'] = $combinaison['upc'];
					$combArray[$combinaison['id_product_attribute']]['quantity'] = $combinaison['quantity'];
					$combArray[$combinaison['id_product_attribute']]['attributes'][] = array($combinaison['group_name'], $combinaison['attribute_name'], $combinaison['id_attribute']);
				}
				$irow = 0;
				foreach ($combArray AS $id_product_attribute => $product_attribute)
				{
					$list = '';
					foreach ($product_attribute['attributes'] AS $attribute)
						$list .= $attribute[0].' - '.$attribute[1].', ';
					$list = rtrim($list, ', ');
					echo '
					<tr'.($irow++ % 2 ? ' class="alt_row"' : '').' >
						<td>'.stripslashes($list).'</td>
						<td>'.$product_attribute['reference'].'</td>
						'.(Configuration::get('PS_STOCK_MANAGEMENT') ? '<td>'.$product_attribute['ean13'].'</td><td>'.$product_attribute['upc'].'</td>' : '').'
						<td class="right">'.$product_attribute['quantity'].'</td>
					</tr>';
				}
				unset($combArray);
				echo '</table>';
				echo '</td></tr></table>';
			}
		}
	}
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm();
		
		if (!($supplier = $this->loadObject(true)))
			return;

		$langtags = 'description¤smeta_title¤smeta_keywords¤smeta_description';
		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post" enctype="multipart/form-data">
		'.($supplier->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$supplier->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/suppliers.gif" />'.$this->l('Suppliers').'</legend>
				<label>'.$this->l('Name:').' </label>
				<div class="margin-form">
					<input type="text" size="40" name="name" value="'.htmlentities(Tools::getValue('name', $supplier->name), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
				</div>
				<label>'.$this->l('Description:').' </label>
				<div class="margin-form">';
				foreach ($this->_languages as $language)
					echo '
					<div id="description_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="description_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($supplier, 'description', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
						<p class="clear">'.$this->l('Will appear in supplier list').'</p>
					</div>';							
				$this->displayFlags($this->_languages, $this->_defaultFormLanguage, $langtags, 'description');
		echo '	<div class="clear"></div>
				</div>
				<label>'.$this->l('Logo:').' </label>
				<div class="margin-form">';
		echo		$this->displayImage($supplier->id, _PS_SUPP_IMG_DIR_.$supplier->id.'.jpg', 350);
		echo '	<br /><input type="file" name="logo" />
					<p>'.$this->l('Upload supplier logo from your computer').'</p>
				</div>
				<label>'.$this->l('Meta title:').' </label>
				<div class="margin-form">';
		foreach ($this->_languages as $language)
			echo '
					<div id="smeta_title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="meta_title_'.$language['id_lang'].'" id="meta_title_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($supplier, 'meta_title', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Forbidden characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>';
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, $langtags, 'smeta_title');
		echo '		<div class="clear"></div>
				</div>
				<label>'.$this->l('Meta description:').' </label>
				<div class="margin-form">';
		foreach ($this->_languages as $language)
			echo '<div id="smeta_description_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="meta_description_'.$language['id_lang'].'" id="meta_description_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($supplier, 'meta_description', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Forbidden characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
				</div>';
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, $langtags, 'smeta_description');
		echo '		<div class="clear"></div>
				</div>
				<label>'.$this->l('Meta keywords:').' </label>
				<div class="margin-form">';
		foreach ($this->_languages as $language)
			echo '
					<div id="smeta_keywords_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="meta_keywords_'.$language['id_lang'].'" id="meta_keywords_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($supplier, 'meta_keywords', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Forbidden characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>';
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, $langtags, 'smeta_keywords');
		echo '		<div class="clear"></div>
				</div>
				<label>'.$this->l('Enable:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.($this->getFieldValue($supplier, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.(!$this->getFieldValue($supplier, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
	
	public function afterImageUpload()
	{
		/* Generate image with differents size */
		if (($id_supplier = (int)(Tools::getValue('id_supplier'))) AND isset($_FILES) AND count($_FILES) AND file_exists(_PS_SUPP_IMG_DIR_.$id_supplier.'.jpg'))
		{
			$imagesTypes = ImageType::getImagesTypes('suppliers');
			foreach ($imagesTypes AS $k => $imageType)
			{
				$file = _PS_SUPP_IMG_DIR_.$id_supplier.'.jpg';
				imageResize($file, _PS_SUPP_IMG_DIR_.$id_supplier.'-'.stripslashes($imageType['name']).'.jpg', (int)($imageType['width']), (int)($imageType['height']));
			}
		}
	}
}


