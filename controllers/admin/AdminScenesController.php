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
*  @version  Release: $Revision: 7040 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminScenesControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'scene';
	 	$this->className = 'Scene';
	 	$this->lang = true;
	 	$this->edit = true;
	 	$this->delete = true;

		$this->identifier = 'id_scene';
		$this->fieldImageSettings = array(
			array('name' => 'image', 'dir' => 'scenes'),
			array('name' => 'thumb', 'dir' => 'scenes/thumbs')
		);

		$this->fieldsDisplay = array(
			'id_scene' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'name' => array('title' => $this->l('Image Maps'), 'width' => 150, 'filter_key' => 'b!name'),
			'active' => array('title' => $this->l('Activated'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false)
		);

		parent::__construct();
	}

	public function afterImageUpload()
	{
		/* Generate image with differents size */
		if (!($obj = $this->loadObject(true)))
			return;
		if ($obj->id AND (isset($_FILES['image']) OR isset($_FILES['thumb'])))
		{
			$imagesTypes = ImageType::getImagesTypes('scenes');
			foreach ($imagesTypes AS $k => $imageType)
			{
				if ($imageType['name'] == 'large_scene' AND isset($_FILES['image']))
					imageResize($_FILES['image']['tmp_name'], _PS_SCENE_IMG_DIR_.$obj->id.'-'.stripslashes($imageType['name']).'.jpg', (int)($imageType['width']), (int)($imageType['height']));
				elseif ($imageType['name'] == 'thumb_scene')
				{
					if (isset($_FILES['thumb'])  AND !$_FILES['thumb']['error'])
						$tmpName = $_FILES['thumb']['tmp_name'];
					else
						$tmpName = $_FILES['image']['tmp_name'];
					imageResize($tmpName, _PS_SCENE_THUMB_IMG_DIR_.$obj->id.'-'.stripslashes($imageType['name']).'.jpg', (int)($imageType['width']), (int)($imageType['height']));
				}
			}
		}
		return true;
	}

	public function initForm()
	{
		$this->initFieldsForm();
		$content = '';

		if (!($obj = $this->loadObject(true)))
			return;

		$langtags = 'name';
		$active = $this->getFieldValue($obj, 'active');

		$products = $obj->getProducts(true, $this->context->language->id, false, $this->context);
		$this->tpl_form_vars['products'] = $obj->getProducts(true, $this->context->language->id, false, $this->context);


		return parent::initForm();
	}

	public function initFieldsForm()
	{
		$obj = $this->loadObject(true);
		$sceneImageTypes = ImageType::getImagesTypes('scenes');
		$largeSceneImageType = NULL;
		$thumbSceneImageType = NULL;
		foreach ($sceneImageTypes as $sceneImageType)
		{
			if ($sceneImageType['name'] == 'large_scene')
				$largeSceneImageType = $sceneImageType;
			if ($sceneImageType['name'] == 'thumb_scene')
				$thumbSceneImageType = $sceneImageType;
		}
		$fields_form = array(
			'legend' => array(
				'title' => $this->l('Image Maps'),
				'image' => '../img/admin/photo.gif',
				),
			'description' => $this->l('When a customer hovers over the image with the mouse, a pop-up appears displaying a brief description of the product. The customer can then click to open the product\'s full product page. To achieve this, please define the \'mapping zone\' that, when hovered over, will display the pop-up. Left-click with your mouse to draw the four-sided mapping zone, then release. Then, begin typing the name of the associated product. A list of products appears. Click the appropriate product, then click OK. Repeat these steps for each mapping zone you wish to create. When you have finished mapping zones, click Save Image Map.'),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Image map name:'),
					'name' => 'name',
					'lang' => true,
					'size' => 48,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
					array(
					'type' => 'radio',
					'label' => $this->l('Status:'),
					'name' => 'active',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
			),
		);
		$this->fields_form = $fields_form;

		$image_to_map_desc = '';
		$image_to_map_desc = $this->l('Format:').' JPG, GIF, PNG. '.$this->l('File size:').' '
				.(Tools::getMaxUploadSize() / 1024).''.$this->l('KB max.').' '
				.$this->l('If larger than the image size setting, the image will be reduced to ')
				.' '.$largeSceneImageType['width'].'x'.$largeSceneImageType['height'].'px '
				.$this->l('(width x height). If smaller than the image-size setting, a white background will be added in order to achieve the correct image size.').'.<br />'.$this->l('Note: To change image dimensions, please change the \'large_scene\' image type settings to the desired size (in Back Office > Preferences > Images).');
		if ($obj->id && file_exists(_PS_SCENE_IMG_DIR_.$obj->id.'-large_scene.jpg'))
		{
			$this->addJqueryPlugin('autocomplete');
			$this->addJqueryPlugin('imgareaselect');
			$this->addJs(_PS_JS_DIR_.'admin-scene-cropping.js' );
			$image_to_map_desc .= '<br /><img id="large_scene_image" style="clear:both;border:1px solid black;" alt="" src="'._THEME_SCENE_DIR_.$obj->id.'-large_scene.jpg" /><br />';

			$image_to_map_desc .= '
						<div id="ajax_choose_product" style="display:none; padding:6px; padding-top:2px; width:600px;">
							'.$this->l('Begin typing the first letters of the product name, then select the product from the drop-down list:').'<br /><input type="text" value="" id="product_autocomplete_input" /> <input type="button" class="button" value="'.$this->l('OK').'" onclick="$(this).prev().search();" /><input type="button" class="button" value="'.$this->l('Delete').'" onclick="undoEdit();" />
						</div>
				';


			if ($obj->id && file_exists(_PS_SCENE_IMG_DIR_.'thumbs/'.$obj->id.'-thumb_scene.jpg'))
				$image_to_map_desc .= '<br/><img id="large_scene_image" style="clear:both;border:1px solid black;" alt="" src="'._THEME_SCENE_DIR_.'thumbs/'.$obj->id.'-thumb_scene.jpg" /><br />';
				
			$img_alt_desc = '';
			$img_alt_desc .= $this->l('If you want to use a thumbnail other than one generated from simply reducing the mapped image, please upload it here.')
				.'<br />'.$this->l('Format:').' JPG, GIF, PNG. '
				.$this->l('Filesize:').' '.(Tools::getMaxUploadSize() / 1024).''.$this->l('Kb max.').' '
				.$this->l('Automatically resized to')
				.' '.$thumbSceneImageType['width'].'x'.$thumbSceneImageType['height'].'px '.$this->l('(width x height)').'.<br />'
				.$this->l('Note: To change image dimensions, please change the \'thumb_scene\' image type settings to the desired size (in Back Office > Preferences > Images).');

			$input_img_alt = array(
				'type' => 'file',
				'label' => $this->l('Alternative thumbnail:'),
				'name' => 'thumb',
				'p' => $img_alt_desc
			);

			$selectedCat = array();
			if (Tools::isSubmit('categories'))
				foreach (Tools::getValue('categories') as $k => $row)
					$selectedCat[] = $row;
			else if ($obj->id)
				foreach (Scene::getIndexedCategories($obj->id) as $k => $row)
					$selectedCat[] = $row['id_category'];

			$trads = array(
							'Home' => $this->l('Home'),
							'selected' => $this->l('selected'),
							'Check all' => $this->l('Check all'),
							'Check All' => $this->l('Check All'),
							'Uncheck All'  => $this->l('Uncheck All'),
							'Collapse All' => $this->l('Collapse All'),
							'Expand All' => $this->l('Expand All'),
							'search' => $this->l('Search a category')

						);
			$this->fields_form['input'][] = array(
					'type' => 'categories',
					'label' => $this->l('Categories:'),
					'name' => 'categories',
					'values' => array('trads' => $trads,
						'selected_cat' => $selectedCat,
						'input_name' => 'categories[]',
						'use_radio' => false,
						'use_search' => true,
						'disabled_categories' => array(4),
					)
				);
		}
		else
		{
			$image_to_map_desc .= '<br/><span class="bold">'.$this->l('Please add a picture to continue mapping the image...').'</span><br/><br/>';
			$image_to_map_desc .= '</div>';
		}
		if (Shop::isFeatureActive())
		{
			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association:'),
				'name' => 'checkBoxShopAsso',
				'values' => Shop::getTree()
			);
		}

		$this->fields_form['input'][] = array(
			'type' => 'file',
			'label' => $this->l('Image to be mapped:'),
			'name' => 'image',
			'display_image' => true,
			'p' => $image_to_map_desc,
		);

		if(isset($input_img_alt))
			$this->fields_form['input'][] = $input_img_alt;
	}

	public function postProcess()
	{
		if (Tools::isSubmit('save_image_map'))
		{
			if (!Tools::isSubmit('categories') || !sizeof(Tools::getValue('categories')))
				$this->_errors[] = Tools::displayError('You should select at least one category');
			if (!Tools::isSubmit('zones') || !sizeof(Tools::getValue('zones')))
				$this->_errors[] = Tools::displayError('You should make at least one zone');
		}
		parent::postProcess();
	}
}


