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

class AdminImageResize extends AdminTab
{
	public function postProcess()
	{
		global $currentIndex, $cookie;

		if (isset($_POST['resize']))
		{
			$imagesTypes = ImageType::getImagesTypes('products');
			$sourceFile['tmp_name'] = _PS_IMG_DIR_.'/p/'.Tools::getValue('id_product').'-'.Tools::getValue('id_image').'.jpg';
			foreach ($imagesTypes AS $k => $imageType)
				if (!imageCut
				($sourceFile,
				_PS_IMG_DIR_.'p/'.Tools::getValue('id_product').'-'.Tools::getValue('id_image').'-'.stripslashes($imageType['name']).'.jpg', 
				$imageType['width'], 
				$imageType['height'], 
				'jpg',
				$_POST[$imageType['id_image_type'].'_x1'],
				$_POST[$imageType['id_image_type'].'_y1']))
					$this->_errors = Tools::displayError('An error occurred while copying image.').' '.stripslashes($imageType['name']);
				// Save and stay on same form
				if (Tools::getValue('saveandstay') == 'on')
					Tools::redirectAdmin($currentIndex.'&id_product='.Tools::getValue('id_product').'&id_category='.(int)(Tools::getValue('id_category')).'&addproduct&conf=4&tabs=1&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)));
				// Default behavior (save and back)
				Tools::redirectAdmin($currentIndex.'&id_category='.(int)(Tools::getValue('id_category')).'&conf='.(int)(Tools::getValue('conf')).'&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)));
		} else
			parent::postProcess();
	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();
		
		$imagesTypes = ImageType::getImagesTypes();

		echo '
		<script type="text/javascript" src="../js/cropper/prototype.js"></script>
		<script type="text/javascript" src="../js/cropper/scriptaculous.js"></script>
		<script type="text/javascript" src="../js/cropper/builder.js"></script>
		<script type="text/javascript" src="../js/cropper/dragdrop.js"></script>
		<script type="text/javascript" src="../js/cropper/cropper.js"></script>
		<script type="text/javascript" src="../js/cropper/loader.js"></script>
		<form enctype="multipart/form-data"  method="post" action="'.$currentIndex.'&imageresize&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'">
			<input type="hidden" name="id_product" value="'.Tools::getValue('id_product').'" />
			<input type="hidden" name="id_category" value="'.Tools::getValue('id_category').'" />
			<input type="hidden" name="saveandstay" value="'.Tools::getValue('submitAddAndStay').'" />
			<input type="hidden" name="conf" value="'.(Tools::getValue('toconf')).'" />
			<input type="hidden" name="imageresize" value="imageresize" />
			<input type="hidden" name="id_image" value="'.Tools::getValue('id_image').'" />
			<fieldset>
				<legend><img src="../img/admin/picture.gif" />'.$this->l('Image resize').'</legend>
				'.$this->l('Using your mouse, define which area of the image is to be used for generating each type of thumbnail.').'
				<br /><br />
				<img src="'._THEME_PROD_DIR_.Tools::getValue('id_product').'-'.Tools::getValue('id_image').'.jpg" id="testImage">
				<label for="imageChoice">'.$this->l('Thumbnails format').'</label>
				<div class="margin-form"">
					<select name="imageChoice" id="imageChoice">';
						foreach ($imagesTypes AS $type)
							echo '<option value="../img/p/'.Tools::getValue('id_product').'-'.Tools::getValue('id_image').'.jpg|'.$type['width'].'|'.$type['height'].'|'.$type['id_image_type'].'">'.$type['name'].'</option>';
		echo '		</select>
					<input type="submit" class="button" style="margin-left : 40px;" name="resize" value="'.$this->l('   Save all  ').'" />
				</div>';
				foreach ($imagesTypes AS $type)
					echo '
				<input type="hidden" name="'.$type['id_image_type'].'_x1" id="'.$type['id_image_type'].'_x1" value="0" />
				<input type="hidden" name="'.$type['id_image_type'].'_y1" id="'.$type['id_image_type'].'_y1" value="0" />
				<input type="hidden" name="'.$type['id_image_type'].'_x2" id="'.$type['id_image_type'].'_x2" value="0" />
				<input type="hidden" name="'.$type['id_image_type'].'_y2" id="'.$type['id_image_type'].'_y2" value="0" />';
		echo '	</fieldset>
		</form>';
	}
}