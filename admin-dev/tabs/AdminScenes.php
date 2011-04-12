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

class AdminScenes extends AdminTab
{
	protected $maxImageSize = 1000000;

	public function __construct()
	{
	 	$this->table = 'scene';
	 	$this->className = 'Scene';
	 	$this->lang = true;
	 	$this->edit = true;
	 	$this->delete = true;
		
		$this->fieldImageSettings = array(
			array('name' => 'image', 'dir' => 'scenes'),
			array('name' => 'thumb', 'dir' => 'scenes/thumbs')
		);
				
		$this->fieldsDisplay = array(
		'id_scene' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'name' => array('title' => $this->l('Image Maps'), 'width' => 150),
		'active' => array('title' => $this->l('Activated'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false)
		);
	
		parent::__construct();
	}
	
	
	public function delete()
	{
		removeZoneProducts();
	}
	
	function afterImageUpload()
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
	
	
	
	/**
	 * Build a categories tree
	 *
	 * @param array $indexedCategories Array with categories where product is indexed (in order to check checkbox)
	 * @param array $categories Categories to list
	 * @param array $current Current category
	 * @param integer $id_category Current category id
	 */
	public function recurseCategoryForInclude($indexedCategories, $categories, $current, $id_category = 1, $id_category_default = NULL, $has_suite = array())
	{
		global $done;
		static $irow;
		
		$id_obj = (int)(Tools::getValue($this->id));

		if (!isset($done[$current['infos']['id_parent']]))
			$done[$current['infos']['id_parent']] = 0;
		$done[$current['infos']['id_parent']] += 1;

		$todo = sizeof($categories[$current['infos']['id_parent']]);
		$doneC = $done[$current['infos']['id_parent']];

		$level = $current['infos']['level_depth'] + 1;

		echo '
		<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
			<td>
				<input type="checkbox" name="categoryBox[]" class="categoryBox'.($id_category_default == $id_category ? ' id_category_default' : '').'" id="categoryBox_'.$id_category.'" value="'.$id_category.'"'.((in_array($id_category, $indexedCategories) OR ((int)(Tools::getValue('id_category')) == $id_category AND !(int)($id_obj))) ? ' checked="checked"' : '').' />
			</td>
			<td>
				'.$id_category.'
			</td>
			<td>';
			for ($i = 2; $i < $level; $i++)
				echo '<img src="../img/admin/lvl_'.$has_suite[$i - 2].'.gif" alt="" style="vertical-align: middle;"/>';
			echo '<img src="../img/admin/'.($level == 1 ? 'lv1.gif' : 'lv2_'.($todo == $doneC ? 'f' : 'b').'.gif').'" alt="" style="vertical-align: middle;"/> &nbsp;
			<label for="categoryBox_'.$id_category.'" class="t">'.stripslashes($current['infos']['name']).'</label></td>
		</tr>';

		if ($level > 1)
			$has_suite[] = ($todo == $doneC ? 0 : 1);
		if (isset($categories[$id_category]))
			foreach ($categories[$id_category] AS $key => $row)
				if ($key != 'infos')
					$this->recurseCategoryForInclude($indexedCategories, $categories, $categories[$id_category][$key], $key, $id_category_default, $has_suite);
	}
	
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;
		
		$langtags = 'name';
		$active = $this->getFieldValue($obj, 'active');
		
		echo '
		<script type="text/javascript">';
			echo 'startingData = new Array();'."\n";
			foreach ($obj->getProducts() as $key => $product)
			{
				$productObj = new Product((int)($product['id_product']), true, (int)($cookie->id_lang));
				echo 'startingData['.$key.'] = new Array(\''.$productObj->name.'\', '.$product['id_product'].', '.$product['x_axis'].', '.$product['y_axis'].', '.$product['zone_width'].', '.$product['zone_height'].');';
			}
			
		echo
		'</script>
		<form id="scenesForm" action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post" enctype="multipart/form-data">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/photo.gif" />'.$this->l('Image Maps').'</legend>';
			
		
				echo '
					<label>'.$this->l('How to map products in the image:').' </label>
					<div class="margin-form">
						'.$this->l('When a customer hovers over the image with the mouse, a pop-up appears displaying a brief description of the product. The customer can then click to open the product\'s full product page. To achieve this, please define the \'mapping zone\' that, when hovered over, will display the pop-up. Left-click with your mouse to draw the four-sided mapping zone, then release. Then, begin typing the name of the associated product. A list of products appears. Click the appropriate product, then click OK. Repeat these steps for each mapping zone you wish to create. When you have finished mapping zones, click Save Image Map.').'
					</div>
					';
		
		
		echo '<label>'.$this->l('Image map name:').' </label>
				<div class="margin-form">';
		foreach ($this->_languages as $language)
			echo '
					<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" style="width: 260px" name="name_'.$language['id_lang'].'" id="name_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'name', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
					</div>';
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, $langtags, 'name');
		echo '		<div class="clear"></div>
				</div>';
			
			
			 	echo '<label>'.$this->l('Status:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.((!$obj->id OR Tools::getValue('active', $obj->active)) ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Activated').'" title="'.$this->l('Activated').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.((!Tools::getValue('active', $obj->active) AND $obj->id) ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Deactivated').'" title="'.$this->l('Deactivated').'" /></label>
					<p>'.$this->l('Activate or deactivate the image map').'</p>
				</div>';
				
				
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
					
	
 	echo '<label>'.$this->l('Image to be mapped:').' </label>
				<div class="margin-form">
					<input type="hidden" id="stay_here" name="stay_here" value="" />
					<input type="file" name="image" id="image_input" /> <input type="button" value="'.$this->l('Upload image').'" onclick="{$(\'#stay_here\').val(\'true\');$(\'#scenesForm\').submit();}" class="button" /><br/>
					<p>'.$this->l('Format:').' JPG, GIF, PNG. '.$this->l('File size:').' '.($this->maxImageSize / 1000).''.$this->l('KB max.').' '.$this->l('If larger than the image size setting, the image will be reduced to ').' '.$largeSceneImageType['width'].'x'.$largeSceneImageType['height'].'px '.$this->l('(width x height). If smaller than the image-size setting, a white background will be added in order to achieve the correct image size.').'.<br />'.$this->l('Note: To change image dimensions, please change the \'large_scene\' image type settings to the desired size (in Back Office > Preferences > Images).').'</p>';
					
	if ($obj->id && file_exists(_PS_SCENE_IMG_DIR_.$obj->id.'-large_scene.jpg'))
	{

		echo '<img id="large_scene_image" style="clear:both;border:1px solid black;" alt="" src="'._THEME_SCENE_DIR_.$obj->id.'-large_scene.jpg" /><br />';
		
		echo '
					<div id="ajax_choose_product" style="display:none; padding:6px; padding-top:2px; width:600px;">
						'.$this->l('Begin typing the first letters of the product name, then select the product from the drop-down list:').'<br /><input type="text" value="" id="product_autocomplete_input" /> <input type="button" class="button" value="'.$this->l('OK').'" onclick="$(this).prev().search();" /><input type="button" class="button" value="'.$this->l('Delete').'" onclick="undoEdit();" />
					</div>
			';
		
		echo '
					<link rel="stylesheet" type="text/css" href="'.__PS_BASE_URI__.'css/jquery.autocomplete.css" />
					<link rel="stylesheet" type="text/css" href="'.__PS_BASE_URI__.'js/jquery/imgareaselect/imgareaselect-default.css" />
					<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/jquery.autocomplete.js"></script>
					<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/imgareaselect/jquery.imgareaselect.pack.js"></script>
					<script type="text/javascript" src="'.__PS_BASE_URI__.'js/admin-scene-cropping.js"></script>
		';
				
		echo '</div>';


 	echo '<label>'.$this->l('Alternative thumbnail:').' </label>
				<div class="margin-form">
					<input type="file" name="thumb" id="thumb_input" />&nbsp;&nbsp;'.$this->l('(optional)').'
					<p>'.$this->l('If you want to use a thumbnail other than one generated from simply reducing the mapped image, please upload it here.').'<br />'.$this->l('Format:').' JPG, GIF, PNG. '.$this->l('Filesize:').' '.($this->maxImageSize / 1000).''.$this->l('Kb max.').' '.$this->l('Automatically resized to').' '.$thumbSceneImageType['width'].'x'.$thumbSceneImageType['height'].'px '.$this->l('(width x height)').'.<br />'.$this->l('Note: To change image dimensions, please change the \'thumb_scene\' image type settings to the desired size (in Back Office > Preferences > Images).').'</p>
					';
	if ($obj->id && file_exists(_PS_SCENE_IMG_DIR_.'thumbs/'.$obj->id.'-thumb_scene.jpg'))
		echo '<img id="large_scene_image" style="clear:both;border:1px solid black;" alt="" src="'._THEME_SCENE_DIR_.'thumbs/'.$obj->id.'-thumb_scene.jpg" /><br />';
	echo '</div>
			 ';
					
		echo '<label>'.$this->l('Category:').' </label>
				<div class="margin-form">
					<div style="overflow: auto; min-height: 300px; padding-top: 0.6em;" id="categoryList">
						<table cellspacing="0" cellpadding="0" class="table" style="width: 600px;">
								<tr>
									<th><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'categoryBox[]\', this.checked)" /></th>
									<th>'.$this->l('ID').'</th>
									<th>'.$this->l('Image map name:').'</th>
								</tr>';
					$categories = Category::getCategories((int)($cookie->id_lang), false);
					$done = array();
					$index = array();
					if (Tools::isSubmit('categoryBox'))
						foreach (Tools::getValue('categoryBox') AS $k => $row)
							$index[] = $row;
					elseif ($obj->id)
						foreach (Scene::getIndexedCategories($obj->id) AS $k => $row)
							$index[] = $row['id_category'];
					$this->recurseCategoryForInclude($index, $categories, $categories[0][1], 1, null);
			echo '</table>
						<p style="padding:0px; margin:0px 0px 10px 0px;">'.$this->l('Mark all checkbox(es) of the categories for which the image map is to appear.').'<sup> *</sup></p>
					</div>
				</div>';
				
				
				
				

		echo '
					<div id="save_scene" class="margin-form" '.(($obj->id && file_exists(_PS_SCENE_IMG_DIR_.$obj->id.'-large_scene.jpg')) ? '' : 'style="display:none;"') .'>
						<input type="submit" value="'.$this->l('Save Image Map(s)').'" class="button" />
					</div>';
	} else {
	echo '
					<br/><span class="bold">'.$this->l('Please add a picture to continue mapping the image...').'</span><br/><br/>';
	}
	echo '<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
}


