<?php 
if(!defined('_PS_VERSION_'))
exit;

class TmHtmlContent extends Module {

	protected 	$maxImageSize = 1048576;
	private 	$_html = '';
	protected 	$_defaultLanguage;
	protected 	$_languages;

	public function __construct() {
		$this->name = 'tmhtmlcontent';    // Defines module name
		$this->tab = 'other';         // Defines module tab name/module category in the admin panel
		$this->author = 'TemplateMonster';      // Defines module author
		$this->version = '1.0';       // Defines module version
		$this->secure_key = Tools::encrypt($this->name);
		
		$this->_defaultLanguage = Language::getLanguage(Configuration::get('PS_LANG_DEFAULT'));
		$this->_languages = Language::getLanguages();

		parent::__construct();

		$this->displayName = $this->l('TM HTML content');
		$this->desctiption = $this->l('Module for HTML content with images and links.');

		// Paths
		$this->module_path 		= _PS_MODULE_DIR_.$this->name.'/';
		$this->uploads_path 	= _PS_MODULE_DIR_.$this->name.'/images/';
		$this->admin_tpl_path 	= _PS_MODULE_DIR_.$this->name.'/views/templates/admin/';
		$this->hooks_tpl_path	= _PS_MODULE_DIR_.$this->name.'/views/templates/hooks/';
	}
	
	private function installDB() {
	
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'tmhtmlcontent`');

		if (!Db::getInstance()->Execute('
			CREATE TABLE `'._DB_PREFIX_.'tmhtmlcontent` (
				`id_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL,
				`id_lang` int(10) unsigned NOT NULL,
				`item_order` int(10) unsigned NOT NULL,
				`title` VARCHAR(100),
				`title_use` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
				`hook` VARCHAR(100),
				`url` VARCHAR(100),
				`target` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
				`image` VARCHAR(100),
				`image_w` VARCHAR(10),
				`image_h` VARCHAR(10),
				`html` TEXT,
				`active` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
				PRIMARY KEY (`id_item`)
			) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;'))
			return false;

		return true;
	}

	public function install() {
		/* Adds Module */
		if (!parent::install() || 
			$this->installDB() && 
			!$this->registerHook('displayHeader') ||
			!$this->registerHook('displayTop') ||
			!$this->registerHook('displayLeftColumn') ||
			!$this->registerHook('displayRightColumn') ||
			!$this->registerHook('displayHome') ||
			!$this->registerHook('displayFooter') ||
			!$this->registerHook('displayBackOfficeHeader'))
			return false;
		return true;
	}

	public function uninstall() {
		
		$images = Db::getInstance()->ExecuteS('SELECT image FROM `'._DB_PREFIX_.'tmhtmlcontent`');
		foreach ($images as $image)
			$this->_deleteImages($image);
		
	
		if (!Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'tmhtmlcontent`') OR
			!parent::uninstall())
			return false;
		return true;	
	}

	public function getContent() {
		$this->context->smarty->assign('error', 0);
		$this->context->smarty->assign('confirmation', 0);
				
		if (Tools::isSubmit('newItem')){
			$this->_addItem();
		} elseif (Tools::isSubmit('updateItem')){
			$this->_updateItem();
		} elseif (Tools::isSubmit('removeItem')) {
			$this->_removeItem();
		}
		return $this->_displayForm();
	}
	
	private function _displayForm() {	
	
		$id_shop = (int)$this->context->shop->id;
		$items = array();
								
		$this->context->smarty->assign('htmlcontent', array(
			'admin_tpl_path' => $this->admin_tpl_path,
			'hooks_tpl_path' => $this->hooks_tpl_path,
		
			'info' => array(
				'module'	=> $this->name,
				'name'	=> $this->displayName,
				'version'   => $this->version,
				'psVersion' => _PS_VERSION_,
				'context'	=> (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 0) ? 1 : ($this->context->shop->getTotalShops() != 1) ? $this->context->shop->getContext() : 1
			)
		));
		
		foreach ($this->_languages as $language) {
			$hooks[$language['id_lang']] = array('home', 'top', 'left', 'right', 'footer');
			foreach ($hooks[$language['id_lang']] as $hook) {
				$items[$language['id_lang']][$hook] =  Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tmhtmlcontent` WHERE id_shop = '.$id_shop.' AND id_lang = '.$language['id_lang'].' AND hook = \''.$hook.'\' ORDER BY item_order ASC');
			}
		}
				
		$this->context->smarty->assign('htmlitems', array(
			'items' => $items,
			'lang' => array(
				'default' => $this->_defaultLanguage,
				'all' => $this->_languages,
				'lang_dir' => _THEME_LANG_DIR_,
				'user' => $this->context->language->id
			),				
			'postAction' => 'index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&tab_module=other&module_name='.$this->name.'',
			'id_shop' => $id_shop
		));
				
		return $this->display(__FILE__, 'views/templates/admin/admin.tpl');
	}
	
	
	private function _addItem() {
		$id_shop = (int)$this->context->shop->id;
		$lastOrder = Db::getInstance()->ExecuteS('SELECT item_order FROM `'._DB_PREFIX_.'tmhtmlcontent` WHERE id_shop = '.$id_shop.' AND id_lang = '.(int)Tools::getValue('lang_id').' AND hook = \''.Tools::getValue('item_hook').'\' ORDER BY item_order DESC LIMIT 1');
		$currentOrder = ($lastOrder) ? $lastOrder[0]['item_order']+1 : 1 ;
		
		$image_w = (is_numeric(Tools::getValue('item_img_w'))) ? intval(Tools::getValue('item_img_w')) : '';
		$image_h = (is_numeric(Tools::getValue('item_img_h'))) ? intval(Tools::getValue('item_img_h')) : '';
		
		if(!empty($_FILES['item_img']['name'])){
			$image = $this->_uploadImage($_FILES['item_img'], $image_w, $image_h);
			if(empty($image))
				return false;
		} else {
			$image = '';
			$image_w = '';
			$image_h = '';
		}
	
		$insert = Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'tmhtmlcontent` ( 
				`id_shop`, `id_lang`, `item_order`, `title`, `title_use`, `hook`, `url`, `target`, `image`, `image_w`, `image_h`, `html`, `active`
			) VALUES ( 
				\''.$id_shop.'\',
				\''.(int)Tools::getValue('lang_id').'\',
				\''.$currentOrder.'\',
				\''.Tools::getValue('item_title').'\',
				\''.(int)Tools::getValue('item_title_use').'\',
				\''.Tools::getValue('item_hook').'\',
				\''.Tools::getValue('item_url').'\',
				\''.(int)Tools::getValue('item_target').'\',
				\''.$image.'\',
				\''.$image_w.'\',
				\''.$image_h.'\',
				\''.str_replace("'", "&acute;", Tools::getValue('item_html')).'\',
				1)
			');
	
		if(!$insert){
			$this->_deleteImages($image);
			$this->context->smarty->assign('error', $this->l('An error occured while saving data.'));	
			return false;	
		}	
	
		$this->context->smarty->assign('confirmation', $this->l('New item added successfull.'));
	}
		
	private function _updateItem() {
			
		$newImage = '';
		
		$image_w = (is_numeric(Tools::getValue('item_img_w'))) ? intval(Tools::getValue('item_img_w')) : '';
		$image_h = (is_numeric(Tools::getValue('item_img_h'))) ? intval(Tools::getValue('item_img_h')) : '';
	
		if(!empty($_FILES['item_img']['name'])){
			if ($oldImage = Db::getInstance()->ExecuteS('SELECT image FROM `'._DB_PREFIX_.'tmhtmlcontent` WHERE id_item = '.(int)Tools::getValue('item_id')))
				$this->_deleteImages($oldImage[0]);
				
			$image = $this->_uploadImage($_FILES['item_img'], $image_w, $image_h);
			if(empty($image))
				return false;
			$newImage = 'image = \''.$image.'\',';
		} else {
			$image_w = '';
			$image_h = '';
		}
		$update = Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'tmhtmlcontent` SET 
				title = \''.Tools::getValue('item_title').'\',
				title_use = \''.(int)Tools::getValue('item_title_use').'\',
				hook = \''.Tools::getValue('item_hook').'\',
				url = \''.Tools::getValue('item_url').'\',
				target = \''.(int)Tools::getValue('item_target').'\',
				'.$newImage.'
				image_w = \''.$image_w.'\',
				image_h = \''.$image_h.'\',
				active = \''.(int)Tools::getValue('item_active').'\',
				html = \''.str_replace("'", "&acute;", Tools::getValue('item_html')).'\'
			WHERE id_item = '.(int)Tools::getValue('item_id'));
	
		if(!$update){
			if ($newImage = Db::getInstance()->ExecuteS('SELECT image FROM `'._DB_PREFIX_.'tmhtmlcontent` WHERE id_item = '.(int)Tools::getValue('item_id')))
				$this->_deleteImages($oldImage[0]);
			$this->context->smarty->assign('error', $this->l('An error occured while saving data.'));	
			return false;			
		}
			
		$this->context->smarty->assign('confirmation', $this->l('Saved succsessfull.'));
	  
	}
	
	public function _removeItem() {
		$id_shop = (int)$this->context->shop->id;
		if ($delImage = Db::getInstance()->ExecuteS('SELECT image FROM `'._DB_PREFIX_.'tmhtmlcontent` WHERE id_item = '.(int)Tools::getValue('item_id')))
			$this->_deleteImages($delImage[0]);
			
		Db::getInstance()->delete(_DB_PREFIX_.'tmhtmlcontent', 'id_item = '.(int)Tools::getValue('item_id'));
	
		if(Db::getInstance()->Affected_Rows() == 1){
			Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'tmhtmlcontent` 
				SET item_order = item_order-1 
				WHERE (
					item_order > '.Tools::getValue('item_order').' AND 
					id_shop = '.$id_shop.' AND
					hook = \''.Tools::getValue('item_hook').'\')
			');
	
			$this->context->smarty->assign('confirmation', $this->l('Deleted succsessfull.'));
		}else{
			$this->context->smarty->assign('error', $this->l('Cant delete slide data from database.'));
		}
	}
	
	
	private function _uploadImage($image, $image_w = '', $image_h = '') {
		/* Uploads image */
	
		$type = @strtolower(substr(strrchr($image['name'], '.'), 1));
		$imagesize = array();
		$imagesize = @getimagesize($image['tmp_name']);
		Configuration::set('PS_IMAGE_QUALITY','png_all');
		$salt = sha1(microtime());
	
		if (isset($image) &&
			isset($image['tmp_name']) &&
			!empty($image['tmp_name']) &&
			!empty($imagesize) &&
			in_array(strtolower(substr(strrchr($imagesize['mime'], '/'), 1)), array('jpg', 'gif', 'jpeg', 'png')) &&
			in_array($type, array('jpg', 'gif', 'jpeg', 'png')))
		  {
			$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
			
			if ($error = ImageManager::validateUpload($image))
			  $errors[] = $error;
			elseif (!$temp_name || !move_uploaded_file($image['tmp_name'], $temp_name))
			  return false;
			elseif (ImageManager::resize($temp_name, dirname(__FILE__).'/images/'.Tools::encrypt($image['name'].$salt).'.'.$type, $image_w, $image_h))
			  return Tools::encrypt($image['name'].$salt).'.'.$type;
			else 
			  $this->context->smarty->assign('error', $this->l('An error occurred during the image upload.'));
			if (isset($temp_name))
			  @unlink($temp_name);  
		  }
	}
	
	
	private function _deleteImages($image) {
		if ($image && $image['image']!='' && is_file($this->uploads_path.$image['image']))
			unlink($this->uploads_path.$image['image']);
	}



	public function hookDisplayBackOfficeHeader() {
		// Check if module is loaded
		if (Tools::getValue('configure') != $this->name)
			return false;
	
		// CSS
		$this->context->controller->addCSS($this->_path.'views/css/admin.css');
		// JS
		$this->context->controller->addJquery();
		$this->context->controller->addJS($this->_path.'views/js/admin.js');
	}


	public function hookdisplayHeader($params) {
		$this->context->controller->addCss($this->_path.'views/css/hooks.css', 'all');
	}


	public function hookDisplayTop() {
		$id_shop = (int)$this->context->shop->id;		
		$id_lang = $this->context->language->id;
		$hook_name = 'top';
		$items = array();
		$items = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tmhtmlcontent` WHERE id_shop = '.$id_shop.' AND id_lang = '.$id_lang.' AND hook = \''.$hook_name.'\' AND active = 1 ORDER BY item_order ASC');

		$this->context->smarty->assign('htmlitems', array(
			'items' => $items
		));

	 	return $this->display(__FILE__, 'views/templates/hooks/top.tpl');
	}


	public function hookDisplayHome() {
		$id_shop = (int)$this->context->shop->id;		
		$id_lang = $this->context->language->id;
		$hook_name = 'home';
		$items = array();
		$items = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tmhtmlcontent` WHERE id_shop = '.$id_shop.' AND id_lang = '.$id_lang.' AND hook = \''.$hook_name.'\' AND active = 1 ORDER BY item_order ASC');

		$this->context->smarty->assign('htmlitems', array(
			'items' => $items
		));

	 	return $this->display(__FILE__, 'views/templates/hooks/home.tpl');
	}

	public function hookDisplayLeftColumn() {
		$id_shop = (int)$this->context->shop->id;		
		$id_lang = $this->context->language->id;
		$hook_name = 'left';
		$items = array();
		$items = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tmhtmlcontent` WHERE id_shop = '.$id_shop.' AND id_lang = '.$id_lang.' AND hook = \''.$hook_name.'\' AND active = 1 ORDER BY item_order ASC');

		$this->context->smarty->assign('htmlitems', array(
			'items' => $items
		));

	 	return $this->display(__FILE__, 'views/templates/hooks/left.tpl');
	}  

	public function hookDisplayRightColumn() {
		$id_shop = (int)$this->context->shop->id;		
		$id_lang = $this->context->language->id;
		$hook_name = 'right';
		$items = array();
		$items = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tmhtmlcontent` WHERE id_shop = '.$id_shop.' AND id_lang = '.$id_lang.' AND hook = \''.$hook_name.'\' AND active = 1 ORDER BY item_order ASC');

		$this->context->smarty->assign('htmlitems', array(
			'items' => $items
		));

	 	return $this->display(__FILE__, 'views/templates/hooks/right.tpl');
	}  
	
	public function hookDisplayFooter() {
		$id_shop = (int)$this->context->shop->id;		
		$id_lang = $this->context->language->id;
		$hook_name = 'footer';
		$items = array();
		$items = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tmhtmlcontent` WHERE id_shop = '.$id_shop.' AND id_lang = '.$id_lang.' AND hook = \''.$hook_name.'\' AND active = 1 ORDER BY item_order ASC');

		$this->context->smarty->assign('htmlitems', array(
			'items' => $items
		));

	 	return $this->display(__FILE__, 'views/templates/hooks/footer.tpl');
	}  

}