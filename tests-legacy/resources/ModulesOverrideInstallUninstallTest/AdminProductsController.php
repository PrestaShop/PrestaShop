<?php

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class AdminProductsController extends AdminProductsControllerCore
{
    /*
    * module: pscsx3241
    * date: 2015-07-13 16:05:30
    * version: 1
    */
    protected $max_file_size = null;
    /*
    * module: pscsx3241
    * date: 2015-07-13 16:05:30
    * version: 1
    */
    protected $max_image_size = null;
    /*
    * module: pscsx3241
    * date: 2015-07-13 16:05:30
    * version: 1
    */
    protected $_category;

    /*
    * module: pscsx3241
    * date: 2015-07-13 16:05:30
    * version: 1
    */
    protected $tab_display;
    /*
    * module: pscsx3241
    * date: 2015-07-13 16:05:30
    * version: 1
    */
    protected $tab_display_module;

    /*
    * module: pscsx3241
    * date: 2015-07-13 16:05:30
    * version: 1
    */
    protected $available_tabs = [];
    /*
    * module: pscsx3241
    * date: 2015-07-13 16:05:30
    * version: 1
    */
    protected $default_tab = 'Informations';
    /*
    * module: pscsx3241
    * date: 2015-07-13 16:05:30
    * version: 1
    */
    protected $available_tabs_lang = [];
    /*
    * module: pscsx3241
    * date: 2015-07-13 16:05:30
    * version: 1
    */
    protected $position_identifier = 'id_product';
    /*
    * module: pscsx3241
    * date: 2015-07-13 16:05:30
    * version: 1
    */
    protected $submitted_tabs;
    /*
    * module: pscsx3241
    * date: 2015-07-13 16:05:30
    * version: 1
    */
    protected $id_current_category;
    /*
    * module: pscsx3241
    * date: 2015-07-13 16:05:30
    * version: 1
    */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'product';
        $this->className = 'Product';
        $this->lang = true;
        $this->explicitSelect = true;
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            ]
        ];
        if (!Tools::getValue('id_product')) {
            $this->multishop_context_group = false;
        }
        parent::__construct();
        $this->imageType = 'jpg';
        $this->_defaultOrderBy = 'position';
        $this->max_file_size = (int)(Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1000000);
        $this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $this->allow_export = true;
        $this->available_tabs_lang = [
            'Informations' => $this->l('Information'),
            'Pack' => $this->l('Pack'),
            'VirtualProduct' => $this->l('Virtual Product'),
            'Prices' => $this->l('Prices'),
            'Seo' => $this->l('SEO'),
            'Images' => $this->l('Images'),
            'Associations' => $this->l('Associations'),
            'Shipping' => $this->l('Shipping'),
            'Combinations' => $this->l('Combinations'),
            'Features' => $this->l('Features'),
            'Customization' => $this->l('Customization'),
            'Attachments' => $this->l('Attachments'),
            'Quantities' => $this->l('Quantities'),
            'Suppliers' => $this->l('Suppliers'),
            'Warehouses' => $this->l('Warehouses'),
        ];
        $this->available_tabs = ['Quantities' => 6, 'Warehouses' => 14];
        if ($this->context->shop->getContext() != Shop::CONTEXT_GROUP) {
            $this->available_tabs = array_merge($this->available_tabs, [
                'Informations' => 0,
                'Pack' => 7,
                'VirtualProduct' => 8,
                'Prices' => 1,
                'Seo' => 2,
                'Associations' => 3,
                'Images' => 9,
                'Shipping' => 4,
                'Combinations' => 5,
                'Features' => 10,
                'Customization' => 11,
                'Attachments' => 12,
                'Suppliers' => 13,
            ]);
        }
        asort($this->available_tabs, SORT_NUMERIC);

        $modules_list = Hook::getHookModuleExecList('displayAdminProductsExtra');
        if (is_array($modules_list) && count($modules_list) > 0) {
            foreach ($modules_list as $m) {
                $this->available_tabs['Module'.ucfirst($m['module'])] = 23;
                $this->available_tabs_lang['Module'.ucfirst($m['module'])] = Module::getModuleName($m['module']);
            }
        }
        if (Tools::getValue('reset_filter_category')) {
            $this->context->cookie->id_category_products_filter = false;
        }
        if (Shop::isFeatureActive() && $this->context->cookie->id_category_products_filter) {
            $category = new Category((int)$this->context->cookie->id_category_products_filter);
            if (!$category->inShop()) {
                $this->context->cookie->id_category_products_filter = false;
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts'));
            }
        }

        if ($id_category = (int)Tools::getValue('productFilter_cl!name')) {
            $this->_category = new Category((int)$id_category);
            $_POST['productFilter_cl!name'] = $this->_category->name[$this->context->language->id];
        } else {
            if ($id_category = (int)Tools::getValue('id_category')) {
                $this->id_current_category = $id_category;
                $this->context->cookie->id_category_products_filter = $id_category;
            } elseif ($id_category = $this->context->cookie->id_category_products_filter) {
                $this->id_current_category = $id_category;
            }
            if ($this->id_current_category) {
                $this->_category = new Category((int)$this->id_current_category);
            } else {
                $this->_category = new Category();
            }
        }
        $join_category = false;
        if (Validate::isLoadedObject($this->_category) && empty($this->_filter)) {
            $join_category = true;
        }
        $this->_join .= '
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = a.`id_product`)
		LEFT JOIN `'._DB_PREFIX_.'stock_available` sav ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0
		'.StockAvailable::addSqlShopRestriction(null, null, 'sav').') ';
        $alias = 'sa';
        $alias_image = 'image_shop';
        $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP? (int)$this->context->shop->id : 'a.id_shop_default';
        $this->_join .= ' JOIN `'._DB_PREFIX_.'product_shop` sa ON (a.`id_product` = sa.`id_product` AND sa.id_shop = '.$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON ('.$alias.'.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = '.$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'shop` shop ON (shop.id_shop = '.$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_image` = i.`id_image` AND image_shop.`cover` = 1 AND image_shop.id_shop = '.$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'product_download` pd ON (pd.`id_product` = a.`id_product`)';
        $this->_select .= 'shop.name as shopname, a.id_shop_default, ';
        $this->_select .= 'MAX('.$alias_image.'.id_image) id_image, cl.name `name_category`, '.$alias.'.`price`, 0 AS price_final, a.`is_virtual`, pd.`nb_downloadable`, sav.`quantity` as sav_quantity, '.$alias.'.`active`, IF(sav.`quantity`<=0, 1, 0) badge_danger';
        if ($join_category) {
            $this->_join .= ' INNER JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = a.`id_product` AND cp.`id_category` = '.(int)$this->_category->id.') ';
            $this->_select .= ' , cp.`position`, ';
        }
        $this->_group = 'GROUP BY '.$alias.'.id_product';
        $this->fields_list = [];
        $this->fields_list['id_product'] = [
            'title' => $this->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int'
        ];
        $this->fields_list['image'] = [
            'title' => $this->l('Image'),
            'align' => 'center',
            'image' => 'p',
            'orderby' => false,
            'filter' => false,
            'search' => false
        ];
        $this->fields_list['name'] = [
            'title' => $this->l('Name'),
            'filter_key' => 'b!name'
        ];
        $this->fields_list['reference'] = [
            'title' => $this->l('Reference'),
            'align' => 'left',
        ];
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->fields_list['shopname'] = [
                'title' => $this->l('Default shop'),
                'filter_key' => 'shop!name',
            ];
        } else {
            $this->fields_list['name_category'] = [
                'title' => $this->l('Category'),
                'filter_key' => 'cl!name',
            ];
        }
        $this->fields_list['price'] = [
            'title' => $this->l('Base price'),
            'type' => 'price',
            'align' => 'text-right',
            'filter_key' => 'a!price'
        ];
        $this->fields_list['price_final'] = [
            'title' => $this->l('Final price'),
            'type' => 'price',
            'align' => 'text-right',
            'havingFilter' => true,
            'orderby' => false,
            'search' => false
        ];
        if (Configuration::get('PS_STOCK_MANAGEMENT')) {
            $this->fields_list['sav_quantity'] = [
                'title' => $this->l('Quantity'),
                'type' => 'int',
                'align' => 'text-right',
                'filter_key' => 'sav!quantity',
                'orderby' => true,
                'badge_danger' => true,
            ];
        }
        $this->fields_list['active'] = [
            'title' => $this->l('Status'),
            'active' => 'status',
            'filter_key' => $alias.'!active',
            'align' => 'text-center',
            'type' => 'bool',
            'class' => 'fixed-width-sm',
            'orderby' => false
        ];
        if ($join_category && (int)$this->id_current_category) {
            $this->fields_list['position'] = [
                'title' => $this->l('Position'),
                'filter_key' => 'cp!position',
                'align' => 'center',
                'position' => 'position'
            ];
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:31
    * version: 1
    */
    public static function getQuantities($echo, $tr)
    {
        if ((int)$tr['is_virtual'] == 1 && $tr['nb_downloadable'] == 0) {
            return '&infin;';
        } else {
            return $echo;
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:31
    * version: 1
    */
    public function setMedia()
    {
        parent::setMedia();
        $bo_theme = ((Validate::isLoadedObject($this->context->employee)
            && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');
        if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR
            .'template')) {
            $bo_theme = 'default';
        }
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.iframe-transport.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-process.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-validate.js');
        $this->addJs(__PS_BASE_URI__.'js/vendor/spin.js');
        $this->addJs(__PS_BASE_URI__.'js/vendor/ladda.js');
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:31
    * version: 1
    */
    protected function _cleanMetaKeywords($keywords)
    {
        if (!empty($keywords) && $keywords != '') {
            $out = [];
            $words = explode(',', $keywords);
            foreach ($words as $word_item) {
                $word_item = trim($word_item);
                if (!empty($word_item) && $word_item != '') {
                    $out[] = $word_item;
                }
            }
            return ((count($out) > 0) ? implode(',', $out) : '');
        } else {
            return '';
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:31
    * version: 1
    */
    protected function copyFromPost(&$object, $table)
    {
        parent::copyFromPost($object, $table);
        if (get_class($object) != 'Product') {
            return;
        }

        foreach (Language::getIDs(false) as $id_lang) {
            if (isset($_POST['meta_keywords_'.$id_lang])) {
                $_POST['meta_keywords_'.$id_lang] = $this->_cleanMetaKeywords(Tools::strtolower($_POST['meta_keywords_'.$id_lang]));
                $object->meta_keywords[$id_lang] = $_POST['meta_keywords_'.$id_lang];
            }
        }
        $_POST['width'] = empty($_POST['width']) ? '0' : str_replace(',', '.', $_POST['width']);
        $_POST['height'] = empty($_POST['height']) ? '0' : str_replace(',', '.', $_POST['height']);
        $_POST['depth'] = empty($_POST['depth']) ? '0' : str_replace(',', '.', $_POST['depth']);
        $_POST['weight'] = empty($_POST['weight']) ? '0' : str_replace(',', '.', $_POST['weight']);
        if (Tools::getIsset('unit_price') != null) {
            $object->unit_price = str_replace(',', '.', Tools::getValue('unit_price'));
        }
        if (Tools::getIsset('ecotax') != null) {
            $object->ecotax = str_replace(',', '.', Tools::getValue('ecotax'));
        }
        if ($this->isTabSubmitted('Informations')) {
            $object->available_for_order = (int)Tools::getValue('available_for_order');
            $object->show_price = $object->available_for_order ? 1 : (int)Tools::getValue('show_price');
            $object->online_only = (int)Tools::getValue('online_only');
        }
        if ($this->isTabSubmitted('Prices')) {
            $object->on_sale = (int)Tools::getValue('on_sale');
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:31
    * version: 1
    */
    public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = null)
    {
        $orderByPriceFinal = (empty($orderBy) ? ($this->context->cookie->__get($this->table.'Orderby') ? $this->context->cookie->__get($this->table.'Orderby') : 'id_'.$this->table) : $orderBy);
        $orderWayPriceFinal = (empty($orderWay) ? ($this->context->cookie->__get($this->table.'Orderway') ? $this->context->cookie->__get($this->table.'Orderby') : 'ASC') : $orderWay);
        if ($orderByPriceFinal == 'price_final') {
            $orderBy = 'id_'.$this->table;
            $orderWay = 'ASC';
        }
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $this->context->shop->id);

        $nb = count($this->_list);
        if ($this->_list) {
            $context = $this->context->cloneContext();
            $context->shop = clone($context->shop);

            for ($i = 0; $i < $nb; $i++) {
                if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP) {
                    $context->shop = new Shop((int)$this->_list[$i]['id_shop_default']);
                }
                $this->_list[$i]['price'] = Tools::convertPrice($this->_list[$i]['price'], $this->context->currency, true, $this->context);
                $this->_list[$i]['price_tmp'] = Product::getPriceStatic($this->_list[$i]['id_product'], true, null, 2, null, false, true, 1, true, null, null, null, $nothing, true, true, $context);
            }
        }
        if ($orderByPriceFinal == 'price_final') {
            if (strtolower($orderWayPriceFinal) == 'desc') {
                uasort($this->_list, 'cmpPriceDesc');
            } else {
                uasort($this->_list, 'cmpPriceAsc');
            }
        }
        for ($i = 0; $this->_list && $i < $nb; $i++) {
            $this->_list[$i]['price_final'] = $this->_list[$i]['price_tmp'];
            unset($this->_list[$i]['price_tmp']);
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:31
    * version: 1
    */
    protected function loadObject($opt = false)
    {
        $result = parent::loadObject($opt);
        if ($result && Validate::isLoadedObject($this->object)) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive() && !$this->object->isAssociatedToShop()) {
                $default_product = new Product((int)$this->object->id, false, null, (int)$this->object->id_shop_default);
                $def = ObjectModel::getDefinition($this->object);
                foreach ($def['fields'] as $field_name => $row) {
                    if (is_array($default_product->$field_name)) {
                        foreach ($default_product->$field_name as $key => $value) {
                            $this->object->{$field_name}[$key] = $value;
                        }
                    } else {
                        $this->object->$field_name = $default_product->$field_name;
                    }
                }
            }
            $this->object->loadStockData();
        }
        return $result;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:31
    * version: 1
    */
    public function ajaxProcessGetCountriesOptions()
    {
        if (!$res = Country::getCountriesByIdShop((int)Tools::getValue('id_shop'), (int)$this->context->language->id)) {
            return ;
        }
        $tpl = $this->createTemplate('specific_prices_shop_update.tpl');
        $tpl->assign([
            'option_list' => $res,
            'key_id' => 'id_country',
            'key_value' => 'name'
            ]
        );
        $this->content = $tpl->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:31
    * version: 1
    */
    public function ajaxProcessGetCurrenciesOptions()
    {
        if (!$res = Currency::getCurrenciesByIdShop((int)Tools::getValue('id_shop'))) {
            return ;
        }
        $tpl = $this->createTemplate('specific_prices_shop_update.tpl');
        $tpl->assign([
            'option_list' => $res,
            'key_id' => 'id_currency',
            'key_value' => 'name'
            ]
        );
        $this->content = $tpl->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:31
    * version: 1
    */
    public function ajaxProcessGetGroupsOptions()
    {
        if (!$res = Group::getGroups((int)$this->context->language->id, (int)Tools::getValue('id_shop'))) {
            return ;
        }
        $tpl = $this->createTemplate('specific_prices_shop_update.tpl');
        $tpl->assign([
            'option_list' => $res,
            'key_id' => 'id_group',
            'key_value' => 'name'
            ]
        );
        $this->content = $tpl->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:31
    * version: 1
    */
    public function processDeleteVirtualProduct()
    {
        if (!($id_product_download = ProductDownload::getIdFromIdProduct((int)Tools::getValue('id_product')))) {
            $this->errors[] = Tools::displayError('Cannot retrieve file');
        } else {
            $product_download = new ProductDownload((int)$id_product_download);
            if (!$product_download->deleteFile((int)$id_product_download)) {
                $this->errors[] = Tools::displayError('Cannot delete file');
            } else {
                $this->redirect_after = self::$currentIndex.'&id_product='.(int)Tools::getValue('id_product').'&updateproduct&key_tab=VirtualProduct&conf=1&token='.$this->token;
            }
        }
        $this->display = 'edit';
        $this->tab_display = 'VirtualProduct';
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:31
    * version: 1
    */
    public function ajaxProcessAddAttachment()
    {
        if (isset($_FILES['attachment_file'])) {
            if ((int)$_FILES['attachment_file']['error'] === 1) {
                $_FILES['attachment_file']['error'] = [];
                $max_upload = (int)ini_get('upload_max_filesize');
                $max_post = (int)ini_get('post_max_size');
                $upload_mb = min($max_upload, $max_post);
                $_FILES['attachment_file']['error'][] = sprintf(
                    $this->l('File %1$s exceeds the size allowed by the server. The limit is set to %2$d MB.'),
                    '<b>'.$_FILES['attachment_file']['name'].'</b> ',
                    '<b>'.$upload_mb.'</b>'
                );
            }
            $_FILES['attachment_file']['error'] = [];
            $is_attachment_name_valid = false;
            $attachment_names = Tools::getValue('attachment_name');
            $attachment_descriptions = Tools::getValue('attachment_description');
            if (!isset($attachment_names) || !$attachment_names) {
                $attachment_names = [];
            }
            if (!isset($attachment_descriptions) || !$attachment_descriptions) {
                $attachment_descriptions = [];
            }
            foreach ($attachment_names as $lang => $name) {
                $language = Language::getLanguage((int)$lang);
                if (Tools::strlen($name) > 0) {
                    $is_attachment_name_valid = true;
                }
                if (!Validate::isGenericName($name)) {
                    $_FILES['attachment_file']['error'][] = sprintf(Tools::displayError('Invalid name for %s language'), $language['name']);
                } elseif (Tools::strlen($name) > 32) {
                    $_FILES['attachment_file']['error'][] = sprintf(Tools::displayError('The name for %1s language is too long (%2d chars max).'), $language['name'], 32);
                }
            }
            foreach ($attachment_descriptions as $lang => $description) {
                $language = Language::getLanguage((int)$lang);
                if (!Validate::isCleanHtml($description)) {
                    $_FILES['attachment_file']['error'][] = sprintf(Tools::displayError('Invalid description for %s language'), $language['name']);
                }
            }
            if (!$is_attachment_name_valid) {
                $_FILES['attachment_file']['error'][] = Tools::displayError('An attachment name is required.');
            }
            if (empty($_FILES['attachment_file']['error'])) {
                if (is_uploaded_file($_FILES['attachment_file']['tmp_name'])) {
                    if ($_FILES['attachment_file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024)) {
                        $_FILES['attachment_file']['error'][] = sprintf(
                            $this->l('The file is too large. Maximum size allowed is: %1$d kB. The file you are trying to upload is %2$d kB.'),
                            (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024),
                            number_format(($_FILES['attachment_file']['size'] / 1024), 2, '.', '')
                        );
                    } else {
                        do {
                            $uniqid = sha1(microtime());
                        } while (file_exists(_PS_DOWNLOAD_DIR_.$uniqid));
                        if (!copy($_FILES['attachment_file']['tmp_name'], _PS_DOWNLOAD_DIR_.$uniqid)) {
                            $_FILES['attachment_file']['error'][] = $this->l('File copy failed');
                        }
                        @unlink($_FILES['attachment_file']['tmp_name']);
                    }
                } else {
                    $_FILES['attachment_file']['error'][] = Tools::displayError('The file is missing.');
                }
                if (empty($_FILES['attachment_file']['error']) && isset($uniqid)) {
                    $attachment = new Attachment();
                    foreach ($attachment_names as $lang => $name) {
                        $attachment->name[(int)$lang] = $name;
                    }
                    foreach ($attachment_descriptions as $lang => $description) {
                        $attachment->description[(int)$lang] = $description;
                    }
                    $attachment->file = $uniqid;
                    $attachment->mime = $_FILES['attachment_file']['type'];
                    $attachment->file_name = $_FILES['attachment_file']['name'];
                    if (empty($attachment->mime) || Tools::strlen($attachment->mime) > 128) {
                        $_FILES['attachment_file']['error'][] = Tools::displayError('Invalid file extension');
                    }
                    if (!Validate::isGenericName($attachment->file_name)) {
                        $_FILES['attachment_file']['error'][] = Tools::displayError('Invalid file name');
                    }
                    if (Tools::strlen($attachment->file_name) > 128) {
                        $_FILES['attachment_file']['error'][] = Tools::displayError('The file name is too long.');
                    }
                    if (empty($this->errors)) {
                        $res = $attachment->add();
                        if (!$res) {
                            $_FILES['attachment_file']['error'][] = Tools::displayError('This attachment was unable to be loaded into the database.');
                        } else {
                            $_FILES['attachment_file']['id_attachment'] = $attachment->id;
                            $_FILES['attachment_file']['filename'] = $attachment->name[$this->context->employee->id_lang];
                            $id_product = (int)Tools::getValue($this->identifier);
                            $res = $attachment->attachProduct($id_product);
                            if (!$res) {
                                $_FILES['attachment_file']['error'][] = Tools::displayError('We were unable to associate this attachment to a product.');
                            }
                        }
                    } else {
                        $_FILES['attachment_file']['error'][] = Tools::displayError('Invalid file');
                    }
                }
            }
            die(json_encode($_FILES));
        }
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:31
    * version: 1
    */
    public function processAttachments()
    {
        if ($id = (int)Tools::getValue($this->identifier)) {
            $attachments = trim(Tools::getValue('arrayAttachments'), ',');
            $attachments = explode(',', $attachments);
            if (!Attachment::attachToProduct($id, $attachments)) {
                $this->errors[] = Tools::displayError('An error occurred while saving product attachments.');
            }
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processDuplicate()
    {
        if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
            $id_product_old = $product->id;
            if (empty($product->price) && Shop::getContext() == Shop::CONTEXT_GROUP) {
                $shops = ShopGroup::getShopsFromGroup(Shop::getContextShopGroupID());
                foreach ($shops as $shop) {
                    if ($product->isAssociatedToShop($shop['id_shop'])) {
                        $product_price = new Product($id_product_old, false, null, $shop['id_shop']);
                        $product->price = $product_price->price;
                    }
                }
            }
            unset($product->id);
            unset($product->id_product);
            $product->indexed = 0;
            $product->active = 0;
            if ($product->add()
            && Category::duplicateProductCategories($id_product_old, $product->id)
            && ($combination_images = Product::duplicateAttributes($id_product_old, $product->id)) !== false
            && GroupReduction::duplicateReduction($id_product_old, $product->id)
            && Product::duplicateAccessories($id_product_old, $product->id)
            && Product::duplicateFeatures($id_product_old, $product->id)
            && Product::duplicateSpecificPrices($id_product_old, $product->id)
            && Pack::duplicate($id_product_old, $product->id)
            && Product::duplicateCustomizationFields($id_product_old, $product->id)
            && Product::duplicateTags($id_product_old, $product->id)
            && Product::duplicateDownload($id_product_old, $product->id)) {
                if ($product->hasAttributes()) {
                    Product::updateDefaultAttribute($product->id);
                }
                if (!Tools::getValue('noimage') && !Image::duplicateProductImages($id_product_old, $product->id, $combination_images)) {
                    $this->errors[] = Tools::displayError('An error occurred while copying images.');
                } else {
                    Hook::exec('actionProductAdd', ['id_product' => (int)$product->id, 'product' => $product]);
                    if (in_array($product->visibility, ['both', 'search']) && Configuration::get('PS_SEARCH_INDEXATION')) {
                        Search::indexation(false, $product->id);
                    }
                    $this->redirect_after = self::$currentIndex.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&conf=19&token='.$this->token;
                }
            } else {
                $this->errors[] = Tools::displayError('An error occurred while creating an object.');
            }
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processDelete()
    {
        if (Validate::isLoadedObject($object = $this->loadObject()) && isset($this->fieldImageSettings)) {
            if (isset($object->noZeroObject) && count($taxes = call_user_func([$this->className, $object->noZeroObject])) <= 1) {
                $this->errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
            } else {
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $object->advanced_stock_management) {
                    $stock_manager = StockManagerFactory::getManager();
                    $physical_quantity = $stock_manager->getProductPhysicalQuantities($object->id, 0);
                    $real_quantity = $stock_manager->getProductRealQuantities($object->id, 0);
                    if ($physical_quantity > 0 || $real_quantity > $physical_quantity) {
                        $this->errors[] = Tools::displayError('You cannot delete this product because there is physical stock left.');
                    }
                }
                if (!count($this->errors)) {
                    if ($object->delete()) {
                        $id_category = (int)Tools::getValue('id_category');
                        $category_url = empty($id_category) ? '' : '&id_category='.(int)$id_category;
                        PrestaShopLogger::addLog(sprintf($this->l('%s deletion', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$object->id, true, (int)$this->context->employee->id);
                        $this->redirect_after = self::$currentIndex.'&conf=1&token='.$this->token.$category_url;
                    } else {
                        $this->errors[] = Tools::displayError('An error occurred during deletion.');
                    }
                }
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while deleting the object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processImage()
    {
        $id_image = (int)Tools::getValue('id_image');
        $image = new Image((int)$id_image);
        if (Validate::isLoadedObject($image)) {
            if (Tools::getIsset('editImage')) {
                if ($image->cover) {
                    $_POST['cover'] = 1;
                }
                $_POST['id_image'] = $image->id;
            } elseif (Tools::getIsset('coverImage')) {
                Image::deleteCover($image->id_product);
                $image->cover = 1;
                if (!$image->update()) {
                    $this->errors[] = Tools::displayError('You cannot change the product\'s cover image.');
                } else {
                    $productId = (int)Tools::getValue('id_product');
                    @unlink(_PS_TMP_IMG_DIR_.'product_'.$productId.'.jpg');
                    @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.$productId.'_'.$this->context->shop->id.'.jpg');
                    $this->redirect_after = self::$currentIndex.'&id_product='.$image->id_product.'&id_category='.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&action=Images&addproduct'.'&token='.$this->token;
                }
            } elseif (Tools::getIsset('imgPosition') && Tools::getIsset('imgDirection')) {
                $image->updatePosition(Tools::getValue('imgDirection'), Tools::getValue('imgPosition'));
                $this->redirect_after = self::$currentIndex.'&id_product='.$image->id_product.'&id_category='.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&add'.$this->table.'&action=Images&token='.$this->token;
            }
        } else {
            $this->errors[] = Tools::displayError('The image could not be found. ');
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    protected function processBulkDelete()
    {
        if ($this->access('delete')) {
            if (is_array($this->boxes) && !empty($this->boxes)) {
                $object = new $this->className();
                if (isset($object->noZeroObject) &&
                    (count(call_user_func([$this->className, $object->noZeroObject])) <= 1 || count($_POST[$this->table.'Box']) == count(call_user_func([$this->className, $object->noZeroObject])))) {
                    $this->errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
                } else {
                    $success = 1;
                    $products = Tools::getValue($this->table.'Box');
                    if (is_array($products) && ($count = count($products))) {
                        if (intval(ini_get('max_execution_time')) < round($count * 1.5)) {
                            ini_set('max_execution_time', round($count * 1.5));
                        }
                        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                            $stock_manager = StockManagerFactory::getManager();
                        }
                        foreach ($products as $id_product) {
                            $product = new Product((int)$id_product);

                            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management) {
                                $physical_quantity = $stock_manager->getProductPhysicalQuantities($product->id, 0);
                                $real_quantity = $stock_manager->getProductRealQuantities($product->id, 0);
                                if ($physical_quantity > 0 || $real_quantity > $physical_quantity) {
                                    $this->errors[] = sprintf(Tools::displayError('You cannot delete the product #%d because there is physical stock left.'), $product->id);
                                }
                            }
                            if (!count($this->errors)) {
                                if ($product->delete()) {
                                    PrestaShopLogger::addLog(sprintf($this->l('%s deletion', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$product->id, true, (int)$this->context->employee->id);
                                } else {
                                    $success = false;
                                }
                            } else {
                                $success = 0;
                            }
                        }
                    }
                    if ($success) {
                        $id_category = (int)Tools::getValue('id_category');
                        $category_url = empty($id_category) ? '' : '&id_category='.(int)$id_category;
                        $this->redirect_after = self::$currentIndex.'&conf=2&token='.$this->token.$category_url;
                    } else {
                        $this->errors[] = Tools::displayError('An error occurred while deleting this selection.');
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You must select at least one element to delete.');
            }
        } else {
            $this->errors[] = Tools::displayError('You do not have permission to delete this.');
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processProductAttribute()
    {
        if (!Combination::isFeatureActive() || !Tools::getValue('attribute_combination_list')) {
            return;
        }
        if (Validate::isLoadedObject($product = $this->object)) {
            if ($this->isProductFieldUpdated('attribute_price') && (!Tools::getIsset('attribute_price') || Tools::getIsset('attribute_price') == null)) {
                $this->errors[] = Tools::displayError('The price attribute is required.');
            }
            if (!Tools::getIsset('attribute_combination_list') || Tools::isEmpty(Tools::getValue('attribute_combination_list'))) {
                $this->errors[] = Tools::displayError('You must add at least one attribute.');
            }
            $array_checks = [
                'reference' => 'isReference',
                'supplier_reference' => 'isReference',
                'location' => 'isReference',
                'ean13' => 'isEan13',
                'upc' => 'isUpc',
                'wholesale_price' => 'isPrice',
                'price' => 'isPrice',
                'ecotax' => 'isPrice',
                'quantity' => 'isInt',
                'weight' => 'isUnsignedFloat',
                'unit_price_impact' => 'isPrice',
                'default_on' => 'isBool',
                'minimal_quantity' => 'isUnsignedInt',
                'available_date' => 'isDateFormat'
            ];
            foreach ($array_checks as $property => $check) {
                if (Tools::getValue('attribute_'.$property) !== false && !call_user_func(['Validate', $check], Tools::getValue('attribute_'.$property))) {
                    $this->errors[] = sprintf(Tools::displayError('Field %s is not valid'), $property);
                }
            }
            if (!count($this->errors)) {
                if (!isset($_POST['attribute_wholesale_price'])) {
                    $_POST['attribute_wholesale_price'] = 0;
                }
                if (!isset($_POST['attribute_price_impact'])) {
                    $_POST['attribute_price_impact'] = 0;
                }
                if (!isset($_POST['attribute_weight_impact'])) {
                    $_POST['attribute_weight_impact'] = 0;
                }
                if (!isset($_POST['attribute_ecotax'])) {
                    $_POST['attribute_ecotax'] = 0;
                }
                if (Tools::getValue('attribute_default')) {
                    $product->deleteDefaultAttributes();
                }
                if (($id_product_attribute = (int)Tools::getValue('id_product_attribute')) || ($id_product_attribute = $product->productAttributeExists(Tools::getValue('attribute_combination_list'), false, null, true, true))) {
                    if ($this->access('edit')) {
                        if ($this->isProductFieldUpdated('available_date_attribute') && (Tools::getValue('available_date_attribute') != '' &&!Validate::isDateFormat(Tools::getValue('available_date_attribute')))) {
                            $this->errors[] = Tools::displayError('Invalid date format.');
                        } else {
                            $product->updateAttribute((int)$id_product_attribute,
                                $this->isProductFieldUpdated('attribute_wholesale_price') ? Tools::getValue('attribute_wholesale_price') : null,
                                $this->isProductFieldUpdated('attribute_price_impact') ? Tools::getValue('attribute_price') * Tools::getValue('attribute_price_impact') : null,
                                $this->isProductFieldUpdated('attribute_weight_impact') ? Tools::getValue('attribute_weight') * Tools::getValue('attribute_weight_impact') : null,
                                $this->isProductFieldUpdated('attribute_unit_impact') ? Tools::getValue('attribute_unity') * Tools::getValue('attribute_unit_impact') : null,
                                $this->isProductFieldUpdated('attribute_ecotax') ? Tools::getValue('attribute_ecotax') : null,
                                Tools::getValue('id_image_attr'),
                                Tools::getValue('attribute_reference'),
                                Tools::getValue('attribute_ean13'),
                                $this->isProductFieldUpdated('attribute_default') ? Tools::getValue('attribute_default') : null,
                                Tools::getValue('attribute_location'),
                                Tools::getValue('attribute_upc'),
                                $this->isProductFieldUpdated('attribute_minimal_quantity') ? Tools::getValue('attribute_minimal_quantity') : null,
                                $this->isProductFieldUpdated('available_date_attribute') ? Tools::getValue('available_date_attribute') : null, false);
                            StockAvailable::setProductDependsOnStock((int)$product->id, $product->depends_on_stock, null, (int)$id_product_attribute);
                            StockAvailable::setProductOutOfStock((int)$product->id, $product->out_of_stock, null, (int)$id_product_attribute);
                        }
                    } else {
                        $this->errors[] = Tools::displayError('You do not have permission to add this.');
                    }
                } else {
                    if ($this->access('add')) {
                        if ($product->productAttributeExists(Tools::getValue('attribute_combination_list'))) {
                            $this->errors[] = Tools::displayError('This combination already exists.');
                        } else {
                            $id_product_attribute = $product->addCombinationEntity(
                                Tools::getValue('attribute_wholesale_price'),
                                Tools::getValue('attribute_price') * Tools::getValue('attribute_price_impact'),
                                Tools::getValue('attribute_weight') * Tools::getValue('attribute_weight_impact'),
                                Tools::getValue('attribute_unity') * Tools::getValue('attribute_unit_impact'),
                                Tools::getValue('attribute_ecotax'),
                                0,
                                Tools::getValue('id_image_attr'),
                                Tools::getValue('attribute_reference'),
                                null,
                                Tools::getValue('attribute_ean13'),
                                Tools::getValue('attribute_default'),
                                Tools::getValue('attribute_location'),
                                Tools::getValue('attribute_upc'),
                                Tools::getValue('attribute_minimal_quantity'),
                                [],
                                Tools::getValue('available_date_attribute'),
                                Tools::getValue('attribute_isbn')
                            );
                            StockAvailable::setProductDependsOnStock((int)$product->id, $product->depends_on_stock, null, (int)$id_product_attribute);
                            StockAvailable::setProductOutOfStock((int)$product->id, $product->out_of_stock, null, (int)$id_product_attribute);
                        }
                    } else {
                        $this->errors[] = Tools::displayError('You do not have permission to').'<hr>'.Tools::displayError('edit here.');
                    }
                }
                if (!count($this->errors)) {
                    $combination = new Combination((int)$id_product_attribute);
                    $combination->setAttributes(Tools::getValue('attribute_combination_list'));
                    $id_images = Tools::getValue('id_image_attr');
                    if (!empty($id_images)) {
                        $combination->setImages($id_images);
                    }
                    $product->checkDefaultAttributes();
                    if (Tools::getValue('attribute_default')) {
                        Product::updateDefaultAttribute((int)$product->id);
                        if (isset($id_product_attribute)) {
                            $product->cache_default_attribute = (int)$id_product_attribute;
                        }
                        if ($available_date = Tools::getValue('available_date_attribute')) {
                            $product->setAvailableDate($available_date);
                        } else {
                            $product->setAvailableDate();
                        }
                    }
                }
            }
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processFeatures()
    {
        if (!Feature::isFeatureActive()) {
            return;
        }
        if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
            $product->deleteFeatures();
            $languages = Language::getLanguages(false);
            foreach ($_POST as $key => $val) {
                if (preg_match('/^feature_([0-9]+)_value/i', $key, $match)) {
                    if ($val) {
                        $product->addFeaturesToDB($match[1], $val);
                    } else {
                        if ($default_value = $this->checkFeatures($languages, $match[1])) {
                            $id_value = $product->addFeaturesToDB($match[1], 0, 1);
                            foreach ($languages as $language) {
                                if ($cust = Tools::getValue('custom_'.$match[1].'_'.(int)$language['id_lang'])) {
                                    $product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $cust);
                                } else {
                                    $product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $default_value);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $this->errors[] = Tools::displayError('A product must be created before adding features.');
        }
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processPricesModification()
    {
        $id_specific_prices = Tools::getValue('spm_id_specific_price');
        $id_combinations = Tools::getValue('spm_id_product_attribute');
        $id_shops = Tools::getValue('spm_id_shop');
        $id_currencies = Tools::getValue('spm_id_currency');
        $id_countries = Tools::getValue('spm_id_country');
        $id_groups = Tools::getValue('spm_id_group');
        $id_customers = Tools::getValue('spm_id_customer');
        $prices = Tools::getValue('spm_price');
        $from_quantities = Tools::getValue('spm_from_quantity');
        $reductions = Tools::getValue('spm_reduction');
        $reduction_types = Tools::getValue('spm_reduction_type');
        $froms = Tools::getValue('spm_from');
        $tos = Tools::getValue('spm_to');
        foreach ($id_specific_prices as $key => $id_specific_price) {
            if ($reduction_types[$key] == 'percentage' && ((float)$reductions[$key] <= 0 || (float)$reductions[$key] > 100)) {
                $this->errors[] = Tools::displayError('Submitted reduction value (0-100) is out-of-range');
            } elseif ($this->_validateSpecificPrice($id_shops[$key], $id_currencies[$key], $id_countries[$key], $id_groups[$key], $id_customers[$key], $prices[$key], $from_quantities[$key], $reductions[$key], $reduction_types[$key], $froms[$key], $tos[$key], $id_combinations[$key])) {
                $specific_price = new SpecificPrice((int)($id_specific_price));
                $specific_price->id_shop = (int)$id_shops[$key];
                $specific_price->id_product_attribute = (int)$id_combinations[$key];
                $specific_price->id_currency = (int)($id_currencies[$key]);
                $specific_price->id_country = (int)($id_countries[$key]);
                $specific_price->id_group = (int)($id_groups[$key]);
                $specific_price->id_customer = (int)$id_customers[$key];
                $specific_price->price = (float)($prices[$key]);
                $specific_price->from_quantity = (int)($from_quantities[$key]);
                $specific_price->reduction = (float)($reduction_types[$key] == 'percentage' ? ($reductions[$key] / 100) : $reductions[$key]);
                $specific_price->reduction_type = !$reductions[$key] ? 'amount' : $reduction_types[$key];
                $specific_price->from = !$froms[$key] ? '0000-00-00 00:00:00' : $froms[$key];
                $specific_price->to = !$tos[$key] ? '0000-00-00 00:00:00' : $tos[$key];
                if (!$specific_price->update()) {
                    $this->errors[] = Tools::displayError('An error occurred while updating the specific price.');
                }
            }
        }
        if (!count($this->errors)) {
            $this->redirect_after = self::$currentIndex.'&id_product='.(int)(Tools::getValue('id_product')).(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&update'.$this->table.'&action=Prices&token='.$this->token;
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processPriceAddition()
    {
        if (!Tools::getIsset('submitPriceAddition')) {
            return;
        }
        $id_product = Tools::getValue('id_product');
        $id_product_attribute = Tools::getValue('sp_id_product_attribute');
        $id_shop = Tools::getValue('sp_id_shop');
        $id_currency = Tools::getValue('sp_id_currency');
        $id_country = Tools::getValue('sp_id_country');
        $id_group = Tools::getValue('sp_id_group');
        $id_customer = Tools::getValue('sp_id_customer');
        $price = Tools::getValue('leave_bprice') ? '-1' : Tools::getValue('sp_price');
        $from_quantity = Tools::getValue('sp_from_quantity');
        $reduction = (float)(Tools::getValue('sp_reduction'));
        $reduction_tax = Tools::getValue('sp_reduction_tax');
        $reduction_type = !$reduction ? 'amount' : Tools::getValue('sp_reduction_type');
        $reduction_type = $reduction_type == '-' ? 'amount' : $reduction_type;
        $from = Tools::getValue('sp_from');
        if (!$from) {
            $from = '0000-00-00 00:00:00';
        }
        $to = Tools::getValue('sp_to');
        if (!$to) {
            $to = '0000-00-00 00:00:00';
        }
        if (($price == '-1') && ((float)$reduction == '0')) {
            $this->errors[] = Tools::displayError('No reduction value has been submitted');
        } elseif (strtotime($to) < strtotime($from)) {
            $this->errors[] = Tools::displayError('Invalid date range');
        } elseif ($reduction_type == 'percentage' && ((float)$reduction <= 0 || (float)$reduction > 100)) {
            $this->errors[] = Tools::displayError('Submitted reduction value (0-100) is out-of-range');
        } elseif ($this->_validateSpecificPrice($id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_product_attribute)) {
            $specificPrice = new SpecificPrice();
            $specificPrice->id_product = (int)$id_product;
            $specificPrice->id_product_attribute = (int)$id_product_attribute;
            $specificPrice->id_shop = (int)$id_shop;
            $specificPrice->id_currency = (int)($id_currency);
            $specificPrice->id_country = (int)($id_country);
            $specificPrice->id_group = (int)($id_group);
            $specificPrice->id_customer = (int)$id_customer;
            $specificPrice->price = (float)($price);
            $specificPrice->from_quantity = (int)($from_quantity);
            $specificPrice->reduction = (float)($reduction_type == 'percentage' ? $reduction / 100 : $reduction);
            $specificPrice->reduction_tax = $reduction_tax;
            $specificPrice->reduction_type = $reduction_type;
            $specificPrice->from = $from;
            $specificPrice->to = $to;
            if (!$specificPrice->add()) {
                $this->errors[] = Tools::displayError('An error occurred while updating the specific price.');
            }
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function ajaxProcessDeleteSpecificPrice()
    {
        if ($this->access('delete')) {
            $id_specific_price = (int)Tools::getValue('id_specific_price');
            if (!$id_specific_price || !Validate::isUnsignedId($id_specific_price)) {
                $error = Tools::displayError('The specific price ID is invalid.');
            } else {
                $specificPrice = new SpecificPrice((int)$id_specific_price);
                if (!$specificPrice->delete()) {
                    $error = Tools::displayError('An error occurred while attempting to delete the specific price.');
                }
            }
        } else {
            $error = Tools::displayError('You do not have permission to delete this.');
        }
        if (isset($error)) {
            $json = [
                'status' => 'error',
                'message'=> $error
            ];
        } else {
            $json = [
                'status' => 'ok',
                'message'=> $this->_conf[1]
            ];
        }
        die(json_encode($json));
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processSpecificPricePriorities()
    {
        if (!($obj = $this->loadObject())) {
            return;
        }
        if (!$priorities = Tools::getValue('specificPricePriority')) {
            $this->errors[] = Tools::displayError('Please specify priorities.');
        } elseif (Tools::isSubmit('specificPricePriorityToAll')) {
            if (!SpecificPrice::setPriorities($priorities)) {
                $this->errors[] = Tools::displayError('An error occurred while updating priorities.');
            } else {
                $this->confirmations[] = $this->l('The price rule has successfully updated');
            }
        } elseif (!SpecificPrice::setSpecificPriority((int)$obj->id, $priorities)) {
            $this->errors[] = Tools::displayError('An error occurred while setting priorities.');
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processCustomizationConfiguration()
    {
        $product = $this->object;
        $current_customization = $product->getCustomizationFieldIds();
        $files_count = 0;
        $text_count = 0;
        if (is_array($current_customization)) {
            foreach ($current_customization as $field) {
                if ($field['type'] == 1) {
                    $text_count++;
                } else {
                    $files_count++;
                }
            }
        }
        if (!$product->createLabels((int)$product->uploadable_files - $files_count, (int)$product->text_fields - $text_count)) {
            $this->errors[] = Tools::displayError('An error occurred while creating customization fields.');
        }
        if (!count($this->errors) && !$product->updateLabels()) {
            $this->errors[] = Tools::displayError('An error occurred while updating customization fields.');
        }
        $product->customizable = ($product->uploadable_files > 0 || $product->text_fields > 0) ? 1 : 0;
        if (($product->uploadable_files != $files_count || $product->text_fields != $text_count) && !count($this->errors) && !$product->update()) {
            $this->errors[] = Tools::displayError('An error occurred while updating the custom configuration.');
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processProductCustomization()
    {
        if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
            foreach ($_POST as $field => $value) {
                if (strncmp($field, 'label_', 6) == 0 && !Validate::isLabel($value)) {
                    $this->errors[] = Tools::displayError('The label fields defined are invalid.');
                }
            }
            if (empty($this->errors) && !$product->updateLabels()) {
                $this->errors[] = Tools::displayError('An error occurred while updating customization fields.');
            }
            if (empty($this->errors)) {
                $this->confirmations[] = $this->l('Update successful');
            }
        } else {
            $this->errors[] = Tools::displayError('A product must be created before adding customization.');
        }
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processPosition()
    {
        if (!Validate::isLoadedObject($object = $this->loadObject())) {
            $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').
                ' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        } elseif (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position'))) {
            $this->errors[] = Tools::displayError('Failed to update the position.');
        } else {
            $category = new Category((int)Tools::getValue('id_category'));
            if (Validate::isLoadedObject($category)) {
                Hook::exec('actionCategoryUpdate', ['category' => $category]);
            }
            $this->redirect_after = self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&action=Customization&conf=5'.(($id_category = (Tools::getIsset('id_category') ? (int)Tools::getValue('id_category') : '')) ? ('&id_category='.$id_category) : '').'&token='.Tools::getAdminTokenLite('AdminProducts');
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function initProcess()
    {
        if (Tools::isSubmit('submitAddproductAndStay') || Tools::isSubmit('submitAddproduct')) {
            $this->id_object = (int)Tools::getValue('id_product');
            $this->object = new Product($this->id_object);
            if ($this->isTabSubmitted('Informations') && $this->object->is_virtual && (int)Tools::getValue('type_product') != 2) {
                if ($id_product_download = (int)ProductDownload::getIdFromIdProduct($this->id_object)) {
                    $product_download = new ProductDownload($id_product_download);
                    if (!$product_download->deleteFile($id_product_download)) {
                        $this->errors[] = Tools::displayError('Cannot delete file');
                    }
                }
            }
        }
        if (Tools::getValue('deleteVirtualProduct')) {
            if ($this->access('delete')) {
                $this->action = 'deleteVirtualProduct';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
        } elseif (Tools::isSubmit('submitAddProductAndPreview')) {
            $this->display = 'edit';
            $this->action = 'save';
            if (Tools::getValue('id_product')) {
                $this->id_object = Tools::getValue('id_product');
                $this->object = new Product((int)Tools::getValue('id_product'));
            }
        } elseif (Tools::isSubmit('submitAttachments')) {
            if ($this->access('edit')) {
                $this->action = 'attachments';
                $this->tab_display = 'attachments';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::getIsset('duplicate'.$this->table)) {
            if ($this->access('add')) {
                $this->action = 'duplicate';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to add this.');
            }
        } elseif (Tools::getValue('id_image') && Tools::getValue('ajax')) {
            if ($this->access('edit')) {
                $this->action = 'image';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('submitProductAttribute')) {
            if ($this->access('edit')) {
                $this->action = 'productAttribute';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('submitFeatures') || Tools::isSubmit('submitFeaturesAndStay')) {
            if ($this->access('edit')) {
                $this->action = 'features';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('submitPricesModification')) {
            if ($this->access('add')) {
                $this->action = 'pricesModification';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to add this.');
            }
        } elseif (Tools::isSubmit('deleteSpecificPrice')) {
            if ($this->access('delete')) {
                $this->action = 'deleteSpecificPrice';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
        } elseif (Tools::isSubmit('submitSpecificPricePriorities')) {
            if ($this->access('edit')) {
                $this->action = 'specificPricePriorities';
                $this->tab_display = 'prices';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('submitCustomizationConfiguration')) {
            if ($this->access('edit')) {
                $this->action = 'customizationConfiguration';
                $this->tab_display = 'customization';
                $this->display = 'edit';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('submitProductCustomization')) {
            if ($this->access('edit')) {
                $this->action = 'productCustomization';
                $this->tab_display = 'customization';
                $this->display = 'edit';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('id_product')) {
            $post_max_size = Tools::getMaxUploadSize(Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1024 * 1024);
            if ($post_max_size && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] && $_SERVER['CONTENT_LENGTH'] > $post_max_size) {
                $this->errors[] = sprintf(Tools::displayError('The uploaded file exceeds the "Maximum size for a downloadable product" set in preferences (%1$dMB) or the post_max_size/ directive in php.ini (%2$dMB).'), number_format((Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE'))), ($post_max_size / 1024 / 1024));
            }
        }
        if (!$this->action) {
            parent::initProcess();
        } else {
            $this->id_object = (int)Tools::getValue($this->identifier);
        }
        if (isset($this->available_tabs[Tools::getValue('key_tab')])) {
            $this->tab_display = Tools::getValue('key_tab');
        }
        if (!$this->tab_display && $this->action) {
            if (in_array($this->action, array_keys($this->available_tabs))) {
                $this->tab_display = $this->action;
            }
        }
        if (!$this->tab_display) {
            if (in_array($this->default_tab, $this->available_tabs)) {
                $this->tab_display = $this->default_tab;
            } else {
                $this->tab_display = key($this->available_tabs);
            }
        }
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function postProcess()
    {
        if (!$this->redirect_after) {
            parent::postProcess();
        }
        if ($this->display == 'edit' || $this->display == 'add') {
            $this->addJqueryUI([
                'ui.core',
                'ui.widget'
            ]);
            $this->addjQueryPlugin([
                'autocomplete',
                'tablednd',
                'thickbox',
                'ajaxfileupload',
                'date',
                'tagify',
                'select2',
                'validate'
            ]);
            $this->addJS([
                _PS_JS_DIR_.'admin/products.js',
                _PS_JS_DIR_.'admin/attributes.js',
                _PS_JS_DIR_.'admin/price.js',
                _PS_JS_DIR_.'tiny_mce/tiny_mce.js',
                _PS_JS_DIR_.'admin/tinymce.inc.js',
                _PS_JS_DIR_.'admin/dnd.js',
                _PS_JS_DIR_.'jquery/ui/jquery.ui.progressbar.min.js',
                _PS_JS_DIR_.'vendor/spin.js',
                _PS_JS_DIR_.'vendor/ladda.js'
            ]);
            $this->addJS(_PS_JS_DIR_.'jquery/plugins/select2/select2_locale_'.$this->context->language->iso_code.'.js');
            $this->addJS(_PS_JS_DIR_.'jquery/plugins/validate/localization/messages_'.$this->context->language->iso_code.'.js');
            $this->addCSS([
                _PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css'
            ]);
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function ajaxProcessDeleteProductAttribute()
    {
        if (!Combination::isFeatureActive()) {
            return;
        }
        if ($this->access('delete')) {
            $id_product = (int)Tools::getValue('id_product');
            $id_product_attribute = (int)Tools::getValue('id_product_attribute');
            if ($id_product && Validate::isUnsignedId($id_product) && Validate::isLoadedObject($product = new Product($id_product))) {
                if (($depends_on_stock = StockAvailable::dependsOnStock($id_product)) && StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute)) {
                    $json = [
                        'status' => 'error',
                        'message'=> $this->l('It is not possible to delete a combination while it still has some quantities in the Advanced Stock Management. You must delete its stock first.')
                    ];
                } else {
                    $product->deleteAttributeCombination((int)$id_product_attribute);
                    $product->checkDefaultAttributes();
                    Tools::clearColorListCache((int)$product->id);
                    if (!$product->hasAttributes()) {
                        $product->cache_default_attribute = 0;
                        $product->update();
                    } else {
                        Product::updateDefaultAttribute($id_product);
                    }
                    if ($depends_on_stock && !Stock::deleteStockByIds($id_product, $id_product_attribute)) {
                        $json = [
                            'status' => 'error',
                            'message'=> $this->l('Error while deleting the stock')
                        ];
                    } else {
                        $json = [
                            'status' => 'ok',
                            'message'=> $this->_conf[1],
                            'id_product_attribute' => (int)$id_product_attribute
                        ];
                    }
                }
            } else {
                $json = [
                    'status' => 'error',
                    'message'=> $this->l('You cannot delete this attribute.')
                ];
            }
        } else {
            $json = [
                'status' => 'error',
                'message'=> $this->l('You do not have permission to delete this.')
            ];
        }
        die(json_encode($json));
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function ajaxProcessDefaultProductAttribute()
    {
        if ($this->access('edit')) {
            if (!Combination::isFeatureActive()) {
                return;
            }
            if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
                $product->deleteDefaultAttributes();
                $product->setDefaultAttribute((int)Tools::getValue('id_product_attribute'));
                $json = [
                    'status' => 'ok',
                    'message'=> $this->_conf[4]
                ];
            } else {
                $json = [
                    'status' => 'error',
                    'message'=> $this->l('You cannot make this the default attribute.')
                ];
            }
            die(json_encode($json));
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function ajaxProcessEditProductAttribute()
    {
        if ($this->access('edit')) {
            $id_product = (int)Tools::getValue('id_product');
            $id_product_attribute = (int)Tools::getValue('id_product_attribute');
            if ($id_product && Validate::isUnsignedId($id_product) && Validate::isLoadedObject($product = new Product((int)$id_product))) {
                $combinations = $product->getAttributeCombinationsById($id_product_attribute, $this->context->language->id);
                foreach ($combinations as $key => $combination) {
                    $combinations[$key]['attributes'][] = [$combination['group_name'], $combination['attribute_name'], $combination['id_attribute']];
                }
                die(json_encode($combinations));
            }
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function ajaxPreProcess()
    {
        if (Tools::getIsset('update'.$this->table) && Tools::getIsset('id_'.$this->table)) {
            $this->display = 'edit';
            $this->action = Tools::getValue('action');
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function ajaxProcessUpdateProductImageShopAsso()
    {
        $id_product = Tools::getValue('id_product');
        if (($id_image = Tools::getValue('id_image')) && ($id_shop = (int)Tools::getValue('id_shop'))) {
            if (Tools::getValue('active') == 'true') {
                $res = Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'image_shop (`id_image`, `id_shop`, `cover`) VALUES('.(int)$id_image.', '.(int)$id_shop.', \'0\')');
            } else {
                $res = Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'image_shop WHERE `id_image` = '.(int)$id_image.' AND `id_shop` = '.(int)$id_shop);
            }
        }
        $count_cover_image = Db::getInstance()->getValue('
			SELECT COUNT(*) FROM '._DB_PREFIX_.'image i
			INNER JOIN '._DB_PREFIX_.'image_shop ish ON (i.id_image = ish.id_image AND ish.id_shop = '.(int)$id_shop.')
			WHERE i.cover = 1 AND `id_product` = '.(int)$id_product);
        $id_image = Db::getInstance()->getValue('
			SELECT i.`id_image` FROM '._DB_PREFIX_.'image i
			INNER JOIN '._DB_PREFIX_.'image_shop ish ON (i.id_image = ish.id_image AND ish.id_shop = '.(int)$id_shop.')
			WHERE `id_product` = '.(int)$id_product);
        if ($count_cover_image < 1) {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image i SET i.cover = 1 WHERE i.id_image = '.(int)$id_image.' AND i.`id_product` = '.(int)$id_product.' LIMIT 1');
        }
        if ($count_cover_image > 1) {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image i SET i.cover = 0 WHERE i.id_image <> '.(int)$id_image.' AND i.`id_product` = '.(int)$id_product);
        }
        $count_cover_image_shop = Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM '._DB_PREFIX_.'image_shop ish
			INNER JOIN '._DB_PREFIX_.'image i ON (i.id_image = ish.id_image AND i.`id_product` = '.(int)$id_product.')
			WHERE ish.id_shop = '.(int)$id_shop.' AND ish.cover = 1');
        if ($count_cover_image_shop < 1) {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image_shop ish SET ish.cover = 1 WHERE ish.id_image = '.(int)$id_image.' AND ish.id_shop =  '.(int)$id_shop.' LIMIT 1');
        }
        if ($count_cover_image_shop > 1) {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image_shop ish SET ish.cover = 0 WHERE ish.id_image <> '.(int)$id_image.' AND ish.cover = 1 AND ish.id_shop = '.(int)$id_shop.' LIMIT '.intval($count_cover_image_shop - 1));
        }
        if ($res) {
            $this->jsonConfirmation($this->_conf[27]);
        } else {
            $this->jsonError(Tools::displayError('An error occurred while attempting to associate this image with your shop. '));
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function ajaxProcessUpdateImagePosition()
    {
        $res = false;
        if ($json = Tools::getValue('json')) {
            $res = true;
            $json = stripslashes($json);
            $images = json_decode($json, true);
            foreach ($images as $id => $position) {
                $img = new Image((int)$id);
                $img->position = (int)$position;
                $res &= $img->update();
            }
        }
        if ($res) {
            $this->jsonConfirmation($this->_conf[25]);
        } else {
            $this->jsonError(Tools::displayError('An error occurred while attempting to move this picture.'));
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function ajaxProcessUpdateCover()
    {
        Image::deleteCover((int)Tools::getValue('id_product'));
        $img = new Image((int)Tools::getValue('id_image'));
        $img->cover = 1;
        @unlink(_PS_TMP_IMG_DIR_.'product_'.(int)$img->id_product.'.jpg');
        @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$img->id_product.'_'.$this->context->shop->id.'.jpg');
        if ($img->update()) {
            $this->jsonConfirmation($this->_conf[26]);
        } else {
            $this->jsonError(Tools::displayError('An error occurred while attempting to move this picture.'));
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function ajaxProcessDeleteProductImage()
    {
        $this->display = 'content';
        $res = true;

        $image = new Image((int)Tools::getValue('id_image'));
        $this->content['id'] = $image->id;
        $res &= $image->delete();
        if (!Image::getCover($image->id_product)) {
            $res &= Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'image_shop` image_shop, '._DB_PREFIX_.'image i
			SET image_shop.`cover` = 1,
			i.cover = 1
			WHERE image_shop.`id_image` = (SELECT id_image FROM
														(SELECT image_shop.id_image
															FROM '._DB_PREFIX_.'image i'.
                                                            Shop::addSqlAssociation('image', 'i').'
															WHERE i.id_product ='.(int)$image->id_product.' LIMIT 1
														) tmpImage)
			AND id_shop='.(int)$this->context->shop->id.'
			AND i.id_image = image_shop.id_image
			');
        }
        if (file_exists(_PS_TMP_IMG_DIR_.'product_'.$image->id_product.'.jpg')) {
            $res &= @unlink(_PS_TMP_IMG_DIR_.'product_'.$image->id_product.'.jpg');
        }
        if (file_exists(_PS_TMP_IMG_DIR_.'product_mini_'.$image->id_product.'_'.$this->context->shop->id.'.jpg')) {
            $res &= @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.$image->id_product.'_'.$this->context->shop->id.'.jpg');
        }
        if ($res) {
            $this->jsonConfirmation($this->_conf[7]);
        } else {
            $this->jsonError(Tools::displayError('An error occurred while attempting to delete the product image.'));
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    protected function _validateSpecificPrice($id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_combination = 0)
    {
        if (!Validate::isUnsignedId($id_shop) || !Validate::isUnsignedId($id_currency) || !Validate::isUnsignedId($id_country) || !Validate::isUnsignedId($id_group) || !Validate::isUnsignedId($id_customer)) {
            $this->errors[] = Tools::displayError('Wrong IDs');
        } elseif ((!isset($price) && !isset($reduction)) || (isset($price) && !Validate::isNegativePrice($price)) || (isset($reduction) && !Validate::isPrice($reduction))) {
            $this->errors[] = Tools::displayError('Invalid price/discount amount');
        } elseif (!Validate::isUnsignedInt($from_quantity)) {
            $this->errors[] = Tools::displayError('Invalid quantity');
        } elseif ($reduction && !Validate::isReductionType($reduction_type)) {
            $this->errors[] = Tools::displayError('Please select a discount type (amount or percentage).');
        } elseif ($from && $to && (!Validate::isDateFormat($from) || !Validate::isDateFormat($to))) {
            $this->errors[] = Tools::displayError('The from/to date is invalid.');
        } elseif (SpecificPrice::exists((int)$this->object->id, $id_combination, $id_shop, $id_group, $id_country, $id_currency, $id_customer, $from_quantity, $from, $to, false)) {
            $this->errors[] = Tools::displayError('A specific price already exists for these parameters.');
        } else {
            return true;
        }
        return false;
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    protected function checkFeatures($languages, $feature_id)
    {
        $rules = call_user_func(['FeatureValue', 'getValidationRules'], 'FeatureValue');
        $feature = Feature::getFeature((int)Configuration::get('PS_LANG_DEFAULT'), $feature_id);
        foreach ($languages as $language) {
            if ($val = Tools::getValue('custom_'.$feature_id.'_'.$language['id_lang'])) {
                $current_language = new Language($language['id_lang']);
                if (Tools::strlen($val) > $rules['sizeLang']['value']) {
                    $this->errors[] = sprintf(
                        Tools::displayError('The name for feature %1$s is too long in %2$s.'),
                        ' <b>'.$feature['name'].'</b>',
                        $current_language->name
                    );
                } elseif (!call_user_func(['Validate', $rules['validateLang']['value']], $val)) {
                    $this->errors[] = sprintf(
                        Tools::displayError('A valid name required for feature. %1$s in %2$s.'),
                        ' <b>'.$feature['name'].'</b>',
                        $current_language->name
                    );
                }
                if (count($this->errors)) {
                    return 0;
                }
                if ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT')) {
                    return $val;
                }
            }
        }
        return 0;
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function addProductImage($product, $method = 'auto')
    {
        if ($id_image = (int)Tools::getValue('id_image')) {
            $image = new Image((int)$id_image);
            if (!Validate::isLoadedObject($image)) {
                $this->errors[] = Tools::displayError('An error occurred while loading the object image.');
            } else {
                if (($cover = Tools::getValue('cover')) == 1) {
                    Image::deleteCover($product->id);
                }
                $image->cover = $cover;
                $this->validateRules('Image');
                $this->copyFromPost($image, 'image');
                if (count($this->errors) || !$image->update()) {
                    $this->errors[] = Tools::displayError('An error occurred while updating the image.');
                } elseif (isset($_FILES['image_product']['tmp_name']) && $_FILES['image_product']['tmp_name'] != null) {
                    $this->copyImage($product->id, $image->id, $method);
                }
            }
        }
        if (isset($image) && Validate::isLoadedObject($image) && !file_exists(_PS_PROD_IMG_DIR_.$image->getExistingImgPath().'.'.$image->image_format)) {
            $image->delete();
        }
        if (count($this->errors)) {
            return false;
        }
        @unlink(_PS_TMP_IMG_DIR_.'product_'.$product->id.'.jpg');
        @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.$product->id.'_'.$this->context->shop->id.'.jpg');
        return ((isset($id_image) && is_int($id_image) && $id_image) ? $id_image : false);
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function copyImage($id_product, $id_image, $method = 'auto')
    {
        if (!isset($_FILES['image_product']['tmp_name'])) {
            return false;
        }
        if ($error = ImageManager::validateUpload($_FILES['image_product'])) {
            $this->errors[] = $error;
        } else {
            $image = new Image($id_image);
            if (!$new_path = $image->getPathForCreation()) {
                $this->errors[] = Tools::displayError('An error occurred while attempting to create a new folder.');
            }
            if (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image_product']['tmp_name'], $tmpName)) {
                $this->errors[] = Tools::displayError('An error occurred during the image upload process.');
            } elseif (!ImageManager::resize($tmpName, $new_path.'.'.$image->image_format)) {
                $this->errors[] = Tools::displayError('An error occurred while copying the image.');
            } elseif ($method == 'auto') {
                $imagesTypes = ImageType::getImagesTypes('products');
                foreach ($imagesTypes as $k => $image_type) {
                    if (!ImageManager::resize($tmpName, $new_path.'-'.stripslashes($image_type['name']).'.'.$image->image_format, $image_type['width'], $image_type['height'], $image->image_format)) {
                        $this->errors[] = Tools::displayError('An error occurred while copying this image:').' '.stripslashes($image_type['name']);
                    }
                }
            }
            @unlink($tmpName);
            Hook::exec('actionWatermark', ['id_image' => $id_image, 'id_product' => $id_product]);
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    protected function updateAssoShop($id_object)
    {
        return;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processAdd()
    {
        $this->checkProduct();
        if (!empty($this->errors)) {
            $this->display = 'add';
            return false;
        }
        $this->object = new $this->className();
        $this->_removeTaxFromEcotax();
        $this->copyFromPost($this->object, $this->table);
        if ($this->object->add()) {
            PrestaShopLogger::addLog(sprintf($this->l('%s addition', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$this->object->id, true, (int)$this->context->employee->id);
            $this->addCarriers($this->object);
            $this->updateAccessories($this->object);
            $this->updatePackItems($this->object);
            $this->updateDownloadProduct($this->object);
            if (Configuration::get('PS_FORCE_ASM_NEW_PRODUCT') && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                $this->object->advanced_stock_management = 1;
                StockAvailable::setProductDependsOnStock($this->object->id, true, (int)$this->context->shop->id, 0);
                $this->object->save();
            }
            if (empty($this->errors)) {
                $languages = Language::getLanguages(false);
                if ($this->isProductFieldUpdated('category_box') && !$this->object->updateCategories(Tools::getValue('categoryBox'))) {
                    $this->errors[] = Tools::displayError('An error occurred while linking the object.').' <b>'.$this->table.'</b> '.Tools::displayError('To categories');
                } elseif (!$this->updateTags($languages, $this->object)) {
                    $this->errors[] = Tools::displayError('An error occurred while adding tags.');
                } else {
                    Hook::exec('actionProductAdd', ['id_product' => (int)$this->object->id, 'product' => $this->object]);
                    if (in_array($this->object->visibility, ['both', 'search']) && Configuration::get('PS_SEARCH_INDEXATION')) {
                        Search::indexation(false, $this->object->id);
                    }
                }
                if (Configuration::get('PS_DEFAULT_WAREHOUSE_NEW_PRODUCT') != 0 && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    $warehouse_location_entity = new WarehouseProductLocation();
                    $warehouse_location_entity->id_product = $this->object->id;
                    $warehouse_location_entity->id_product_attribute = 0;
                    $warehouse_location_entity->id_warehouse = Configuration::get('PS_DEFAULT_WAREHOUSE_NEW_PRODUCT');
                    $warehouse_location_entity->location = pSQL('');
                    $warehouse_location_entity->save();
                }
                $this->object->setGroupReduction();
                if (Tools::isSubmit('submitAddProductAndPreview')) {
                    $this->redirect_after = $this->getPreviewUrl($this->object);
                }
                if ($this->display == 'edit') {
                    $this->redirect_after = self::$currentIndex.'&id_product='.(int)$this->object->id
                        .(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '')
                        .'&updateproduct&conf=3&key_tab='.Tools::safeOutput(Tools::getValue('key_tab')).'&token='.$this->token;
                } else {
                    $this->redirect_after = self::$currentIndex
                        .(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '')
                        .'&conf=3&token='.$this->token;
                }
            } else {
                $this->object->delete();
                $this->display = 'edit';
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while creating an object.').' <b>'.$this->table.'</b>';
        }
        return $this->object;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    protected function isTabSubmitted($tab_name)
    {
        if (!is_array($this->submitted_tabs)) {
            $this->submitted_tabs = Tools::getValue('submitted_tabs');
        }
        if (is_array($this->submitted_tabs) && in_array($tab_name, $this->submitted_tabs)) {
            return true;
        }
        return false;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processStatus()
    {
        $this->loadObject(true);
        if (!Validate::isLoadedObject($this->object)) {
            return false;
        }
        if (($error = $this->object->validateFields(false, true)) !== true) {
            $this->errors[] = $error;
        }
        if (($error = $this->object->validateFieldsLang(false, true)) !== true) {
            $this->errors[] = $error;
        }
        if (count($this->errors)) {
            return false;
        }
        $res = parent::processStatus();
        return $res;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processUpdate()
    {
        $existing_product = $this->object;
        $this->checkProduct();
        if (!empty($this->errors)) {
            $this->display = 'edit';
            return false;
        }
        $id = (int)Tools::getValue('id_'.$this->table);

        if (isset($id) && !empty($id)) {
            $object = new $this->className((int)$id);
            $this->object = $object;
            if (Validate::isLoadedObject($object)) {
                $this->_removeTaxFromEcotax();
                $product_type_before = $object->getType();
                $this->copyFromPost($object, $this->table);
                $object->indexed = 0;
                if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
                    $object->setFieldsToUpdate((array)Tools::getValue('multishop_check', []));
                }
                if ($this->context->shop->getContext() == Shop::CONTEXT_SHOP && !$object->isAssociatedToShop()) {
                    $is_associated_to_shop = false;
                    $combinations = Product::getProductAttributesIds($object->id);
                    if ($combinations) {
                        foreach ($combinations as $id_combination) {
                            $combination = new Combination((int)$id_combination['id_product_attribute']);
                            $default_combination = new Combination((int)$id_combination['id_product_attribute'], null, (int)$this->object->id_shop_default);
                            $def = ObjectModel::getDefinition($default_combination);
                            foreach ($def['fields'] as $field_name => $row) {
                                $combination->$field_name = ObjectModel::formatValue($default_combination->$field_name, $def['fields'][$field_name]['type']);
                            }
                            $combination->save();
                        }
                    }
                } else {
                    $is_associated_to_shop = true;
                }
                if ($object->update()) {
                    if (Shop::getContext() == Shop::CONTEXT_SHOP && !$existing_product->isAssociatedToShop($this->context->shop->id)) {
                        $out_of_stock = StockAvailable::outOfStock($existing_product->id, $existing_product->id_shop_default);
                        $depends_on_stock = StockAvailable::dependsOnStock($existing_product->id, $existing_product->id_shop_default);
                        StockAvailable::setProductOutOfStock((int)$this->object->id, $out_of_stock, $this->context->shop->id);
                        StockAvailable::setProductDependsOnStock((int)$this->object->id, $depends_on_stock, $this->context->shop->id);
                    }
                    PrestaShopLogger::addLog(sprintf($this->l('%s modification', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$this->object->id, true, (int)$this->context->employee->id);
                    if (in_array($this->context->shop->getContext(), [Shop::CONTEXT_SHOP, Shop::CONTEXT_ALL])) {
                        if ($this->isTabSubmitted('Shipping')) {
                            $this->addCarriers();
                        }
                        if ($this->isTabSubmitted('Associations')) {
                            $this->updateAccessories($object);
                        }
                        if ($this->isTabSubmitted('Suppliers')) {
                            $this->processSuppliers();
                        }
                        if ($this->isTabSubmitted('Features')) {
                            $this->processFeatures();
                        }
                        if ($this->isTabSubmitted('Combinations')) {
                            $this->processProductAttribute();
                        }
                        if ($this->isTabSubmitted('Prices')) {
                            $this->processPriceAddition();
                            $this->processSpecificPricePriorities();
                        }
                        if ($this->isTabSubmitted('Customization')) {
                            $this->processCustomizationConfiguration();
                        }
                        if ($this->isTabSubmitted('Attachments')) {
                            $this->processAttachments();
                        }
                        if ($this->isTabSubmitted('Images')) {
                            $this->processImageLegends();
                        }
                        $this->updatePackItems($object);
                        if ($product_type_before == Product::PTYPE_SIMPLE && $object->getType() == Product::PTYPE_PACK) {
                            StockAvailable::setProductDependsOnStock((int)$object->id, false);
                        }
                        $this->updateDownloadProduct($object, 1);
                        $this->updateTags(Language::getLanguages(false), $object);
                        if ($this->isProductFieldUpdated('category_box') && !$object->updateCategories(Tools::getValue('categoryBox'))) {
                            $this->errors[] = Tools::displayError('An error occurred while linking the object.').' <b>'.$this->table.'</b> '.Tools::displayError('To categories');
                        }
                    }
                    if ($this->isTabSubmitted('Warehouses')) {
                        $this->processWarehouses();
                    }
                    if (empty($this->errors)) {
                        if (in_array($object->visibility, ['both', 'search']) && Configuration::get('PS_SEARCH_INDEXATION')) {
                            Search::indexation(false, $object->id);
                        }
                        if (Tools::isSubmit('submitAddProductAndPreview')) {
                            $this->redirect_after = $this->getPreviewUrl($object);
                        } else {
                            if ($this->display == 'edit') {
                                $this->confirmations[] = $this->l('Update successful');
                                $this->redirect_after = self::$currentIndex.'&id_product='.(int)$this->object->id
                                    .(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '')
                                    .'&updateproduct&conf=4&key_tab='.Tools::safeOutput(Tools::getValue('key_tab')).'&token='.$this->token;
                            } else {
                                $this->redirect_after = self::$currentIndex.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&conf=4&token='.$this->token;
                            }
                        }
                    } else {
                        $this->display = 'edit';
                    }
                } else {
                    if (!$is_associated_to_shop && $combinations) {
                        foreach ($combinations as $id_combination) {
                            $combination = new Combination((int)$id_combination['id_product_attribute']);
                            $combination->delete();
                        }
                    }
                    $this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.'</b> ('.Db::getInstance()->getMsgError().')';
                }
            } else {
                $this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.'</b> ('.Tools::displayError('The object cannot be loaded. ').')';
            }
            return $object;
        }
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function checkProduct()
    {
        $className = 'Product';
        $rules = call_user_func([$this->className, 'getValidationRules'], $this->className);
        $default_language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);
        foreach ($rules['required'] as $field) {
            if (!$this->isProductFieldUpdated($field)) {
                continue;
            }
            if (($value = Tools::getValue($field)) == false && $value != '0') {
                if (Tools::getValue('id_'.$this->table) && $field == 'passwd') {
                    continue;
                }
                $this->errors[] = sprintf(
                    Tools::displayError('The %s field is required.'),
                    call_user_func([$className, 'displayFieldName'], $field, $className)
                );
            }
        }
        foreach ($rules['requiredLang'] as $fieldLang) {
            if ($this->isProductFieldUpdated($fieldLang, $default_language->id) && !Tools::getValue($fieldLang.'_'.$default_language->id)) {
                $this->errors[] = sprintf(
                    Tools::displayError('This %1$s field is required at least in %2$s'),
                    call_user_func([$className, 'displayFieldName'], $fieldLang, $className),
                    $default_language->name
                );
            }
        }
        foreach ($rules['size'] as $field => $maxLength) {
            if ($this->isProductFieldUpdated($field) && ($value = Tools::getValue($field)) && Tools::strlen($value) > $maxLength) {
                $this->errors[] = sprintf(
                    Tools::displayError('The %1$s field is too long (%2$d chars max).'),
                    call_user_func([$className, 'displayFieldName'], $field, $className),
                    $maxLength
                );
            }
        }
        if (Tools::getIsset('description_short') && $this->isProductFieldUpdated('description_short')) {
            $saveShort = Tools::getValue('description_short');
            $_POST['description_short'] = strip_tags(Tools::getValue('description_short'));
        }
        $limit = (int)Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
        if ($limit <= 0) {
            $limit = 400;
        }
        foreach ($languages as $language) {
            if ($this->isProductFieldUpdated('description_short', $language['id_lang']) && ($value = Tools::getValue('description_short_'.$language['id_lang']))) {
                if (Tools::strlen(strip_tags($value)) > $limit) {
                    $this->errors[] = sprintf(
                        Tools::displayError('This %1$s field (%2$s) is too long: %3$d chars max (current count %4$d).'),
                        call_user_func([$className, 'displayFieldName'], 'description_short'),
                        $language['name'],
                        $limit,
                        Tools::strlen(strip_tags($value))
                    );
                }
            }
        }
        foreach ($rules['sizeLang'] as $fieldLang => $maxLength) {
            foreach ($languages as $language) {
                $value = Tools::getValue($fieldLang.'_'.$language['id_lang']);
                if ($value && Tools::strlen($value) > $maxLength) {
                    $this->errors[] = sprintf(
                        Tools::displayError('The %1$s field is too long (%2$d chars max).'),
                        call_user_func([$className, 'displayFieldName'], $fieldLang, $className),
                        $maxLength
                    );
                }
            }
        }
        if ($this->isProductFieldUpdated('description_short') && isset($_POST['description_short'])) {
            $_POST['description_short'] = $saveShort;
        }
        foreach ($rules['validate'] as $field => $function) {
            if ($this->isProductFieldUpdated($field) && ($value = Tools::getValue($field))) {
                $res = true;
                if (Tools::strtolower($function) == 'iscleanhtml') {
                    if (!Validate::$function($value, (int)Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                        $res = false;
                    }
                } elseif (!Validate::$function($value)) {
                    $res = false;
                }
                if (!$res) {
                    $this->errors[] = sprintf(
                        Tools::displayError('The %s field is invalid.'),
                        call_user_func([$className, 'displayFieldName'], $field, $className)
                    );
                }
            }
        }
        foreach ($rules['validateLang'] as $fieldLang => $function) {
            foreach ($languages as $language) {
                if ($this->isProductFieldUpdated($fieldLang, $language['id_lang']) && ($value = Tools::getValue($fieldLang.'_'.$language['id_lang']))) {
                    if (!Validate::$function($value, (int)Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                        $this->errors[] = sprintf(
                            Tools::displayError('The %1$s field (%2$s) is invalid.'),
                            call_user_func([$className, 'displayFieldName'], $fieldLang, $className),
                            $language['name']
                        );
                    }
                }
            }
        }
        if ($this->isProductFieldUpdated('id_category_default') && (!Tools::isSubmit('categoryBox') || !count(Tools::getValue('categoryBox')))) {
            $this->errors[] = $this->l('Products must be in at least one category.');
        }
        if ($this->isProductFieldUpdated('id_category_default') && (!is_array(Tools::getValue('categoryBox')) || !in_array(Tools::getValue('id_category_default'), Tools::getValue('categoryBox')))) {
            $this->errors[] = $this->l('This product must be in the default category.');
        }
        foreach ($languages as $language) {
            if ($value = Tools::getValue('tags_'.$language['id_lang'])) {
                if (!Validate::isTagsList($value)) {
                    $this->errors[] = sprintf(
                        Tools::displayError('The tags list (%s) is invalid.'),
                        $language['name']
                    );
                }
            }
        }
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    protected function isProductFieldUpdated($field, $id_lang = null)
    {
        static $is_activated = null;
        if (is_null($is_activated)) {
            $is_activated = Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP && $this->id_object;
        }
        if (!$is_activated) {
            return true;
        }
        if (is_null($id_lang)) {
            return !empty($_POST['multishop_check'][$field]);
        } else {
            return !empty($_POST['multishop_check'][$field][$id_lang]);
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    protected function _removeTaxFromEcotax()
    {
        if ($ecotax = Tools::getValue('ecotax')) {
            $_POST['ecotax'] = Tools::ps_round($ecotax / (1 + Tax::getProductEcotaxRate() / 100), 6);
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    protected function _applyTaxToEcotax($product)
    {
        if ($product->ecotax) {
            $product->ecotax = Tools::ps_round($product->ecotax * (1 + Tax::getProductEcotaxRate() / 100), 2);
        }
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function updateDownloadProduct($product, $edit = 0)
    {
        if ((int)Tools::getValue('is_virtual_file') == 1) {
            if (isset($_FILES['virtual_product_file_uploader']) && $_FILES['virtual_product_file_uploader']['size'] > 0) {
                $virtual_product_filename = ProductDownload::getNewFilename();
                $helper = new HelperUploader('virtual_product_file_uploader');
                $helper->setPostMaxSize(Tools::getOctets(ini_get('upload_max_filesize')))
                    ->setSavePath(_PS_DOWNLOAD_DIR_)->upload($_FILES['virtual_product_file_uploader'], $virtual_product_filename);
            } else {
                $virtual_product_filename = Tools::getValue('virtual_product_filename', ProductDownload::getNewFilename());
            }
            $product->setDefaultAttribute(0);//reset cache_default_attribute
            if (Tools::getValue('virtual_product_expiration_date') && !Validate::isDate(Tools::getValue('virtual_product_expiration_date'))) {
                if (!Tools::getValue('virtual_product_expiration_date')) {
                    $this->errors[] = Tools::displayError('The expiration-date attribute is required.');
                    return false;
                }
            }
            if ($edit == 1) {
                $id_product_download = (int)ProductDownload::getIdFromIdProduct((int)$product->id);
                if (!$id_product_download) {
                    $id_product_download = (int)Tools::getValue('virtual_product_id');
                }
            } else {
                $id_product_download = Tools::getValue('virtual_product_id');
            }
            $is_shareable = Tools::getValue('virtual_product_is_shareable');
            $virtual_product_name = Tools::getValue('virtual_product_name');
            $virtual_product_nb_days = Tools::getValue('virtual_product_nb_days');
            $virtual_product_nb_downloable = Tools::getValue('virtual_product_nb_downloable');
            $virtual_product_expiration_date = Tools::getValue('virtual_product_expiration_date');
            $download = new ProductDownload((int)$id_product_download);
            $download->id_product = (int)$product->id;
            $download->display_filename = $virtual_product_name;
            $download->filename = $virtual_product_filename;
            $download->date_add = date('Y-m-d H:i:s');
            $download->date_expiration = $virtual_product_expiration_date ? $virtual_product_expiration_date.' 23:59:59' : '';
            $download->nb_days_accessible = (int)$virtual_product_nb_days;
            $download->nb_downloadable = (int)$virtual_product_nb_downloable;
            $download->active = 1;
            $download->is_shareable = (int)$is_shareable;
            if ($download->save()) {
                return true;
            }
        } else {
            if ($edit == 1) {
                $id_product_download = (int)ProductDownload::getIdFromIdProduct((int)$product->id);
                if (!$id_product_download) {
                    $id_product_download = (int)Tools::getValue('virtual_product_id');
                }
            } else {
                $id_product_download = ProductDownload::getIdFromIdProduct($product->id);
            }
            if (!empty($id_product_download)) {
                $product_download = new ProductDownload((int)$id_product_download);
                $product_download->date_expiration = date('Y-m-d H:i:s', time() - 1);
                $product_download->active = 0;
                return $product_download->save();
            }
        }
        return false;
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function updateAccessories($product)
    {
        $product->deleteAccessories();
        if ($accessories = Tools::getValue('inputAccessories')) {
            $accessories_id = array_unique(explode('-', $accessories));
            if (count($accessories_id)) {
                array_pop($accessories_id);
                $product->changeAccessories($accessories_id);
            }
        }
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function updateTags($languages, $product)
    {
        $tag_success = true;

        if (!Tag::deleteTagsForProduct((int)$product->id)) {
            $this->errors[] = Tools::displayError('An error occurred while attempting to delete previous tags.');
        }

        foreach ($languages as $language) {
            if ($value = Tools::getValue('tags_'.$language['id_lang'])) {
                $tag_success &= Tag::addTags($language['id_lang'], (int)$product->id, $value);
            }
        }
        if (!$tag_success) {
            $this->errors[] = Tools::displayError('An error occurred while adding tags.');
        }
        return $tag_success;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function initContent($token = null)
    {
        if ($this->display == 'edit' || $this->display == 'add') {
            $this->fields_form = [];
            if (substr($this->tab_display, 0, 6) == 'Module') {
                $this->tab_display_module = strtolower(substr($this->tab_display, 6, Tools::strlen($this->tab_display) - 6));
                $this->tab_display = 'Modules';
            }
            if (method_exists($this, 'initForm'.$this->tab_display)) {
                $this->tpl_form = strtolower($this->tab_display).'.tpl';
            }
            if ($this->ajax) {
                $this->content_only = true;
            } else {
                $product_tabs = [];
                if (!method_exists($this, 'initForm'.$this->tab_display)) {
                    $this->tab_display = $this->default_tab;
                }
                $advanced_stock_management_active = Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');
                foreach ($this->available_tabs as $product_tab => $value) {
                    if ($advanced_stock_management_active == 0 && $product_tab == 'Warehouses') {
                        continue;
                    }
                    $product_tabs[$product_tab] = [
                        'id' => $product_tab,
                        'selected' => (strtolower($product_tab) == strtolower($this->tab_display) || (isset($this->tab_display_module) && 'module'.$this->tab_display_module == Tools::strtolower($product_tab))),
                        'name' => $this->available_tabs_lang[$product_tab],
                        'href' => $this->context->link->getAdminLink('AdminProducts').'&id_product='.(int)Tools::getValue('id_product').'&action='.$product_tab,
                    ];
                }
                $this->tpl_form_vars['product_tabs'] = $product_tabs;
            }
        } else {
            if ($id_category = (int)$this->id_current_category) {
                self::$currentIndex .= '&id_category='.(int)$this->id_current_category;
            }
            if (!$id_category) {
                $this->_defaultOrderBy = $this->identifier;
                if ($this->context->cookie->{$this->table.'Orderby'} == 'position') {
                    unset($this->context->cookie->{$this->table.'Orderby'});
                    unset($this->context->cookie->{$this->table.'Orderway'});
                }
            }
            if (!$id_category) {
                $id_category = Configuration::get('PS_ROOT_CATEGORY');
            }
            $this->tpl_list_vars['is_category_filter'] = (bool)$this->id_current_category;
            $tree = new HelperTreeCategories('categories-tree', $this->l('Filter by category'));
            $tree->setAttribute('is_category_filter', (bool)$this->id_current_category)
                ->setAttribute('base_url', preg_replace('#&id_category=[0-9]*#', '', self::$currentIndex).'&token='.$this->token)
                ->setInputName('id-category')
                ->setSelectedCategories([(int)$id_category]);
            $this->tpl_list_vars['category_tree'] = $tree->render();
            $this->tpl_list_vars['base_url'] = preg_replace('#&id_category=[0-9]*#', '', self::$currentIndex).'&token='.$this->token;
        }
        $this->tpl_form_vars['vat_number'] = file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php');
        parent::initContent();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function renderKpis()
    {
        $time = time();
        $kpis = [];

        if (Configuration::get('PS_STOCK_MANAGEMENT')) {
            $helper = new HelperKpi();
            $helper->id = 'box-products-stock';
            $helper->icon = 'icon-archive';
            $helper->color = 'color1';
            $helper->title = $this->l('Out of stock items', null, null, false);
            if (ConfigurationKPI::get('PERCENT_PRODUCT_OUT_OF_STOCK') !== false) {
                $helper->value = ConfigurationKPI::get('PERCENT_PRODUCT_OUT_OF_STOCK');
            }
            $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=percent_product_out_of_stock';
            $helper->tooltip = $this->l('X% of your products for sale are out of stock.', null, null, false);
            $helper->refresh = (bool)(ConfigurationKPI::get('PERCENT_PRODUCT_OUT_OF_STOCK_EXPIRE') < $time);
            $helper->href = Context::getContext()->link->getAdminLink('AdminProducts').'&productFilter_sav!quantity=0&productFilter_active=1&submitFilterproduct=1';
            $kpis[] = $helper->generate();
        }
        $helper = new HelperKpi();
        $helper->id = 'box-avg-gross-margin';
        $helper->icon = 'icon-tags';
        $helper->color = 'color2';
        $helper->title = $this->l('Average Gross Margin', null, null, false);
        if (ConfigurationKPI::get('PRODUCT_AVG_GROSS_MARGIN') !== false) {
            $helper->value = ConfigurationKPI::get('PRODUCT_AVG_GROSS_MARGIN');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=product_avg_gross_margin';
        $helper->tooltip = $this->l('The gross margin is the difference between the retail price and the wholesale price, on all your products for sale.', null, null, false);
        $helper->refresh = (bool)(ConfigurationKPI::get('PRODUCT_AVG_GROSS_MARGIN_EXPIRE') < $time);
        $kpis[] = $helper->generate();
        $helper = new HelperKpi();
        $helper->id = 'box-8020-sales-catalog';
        $helper->icon = 'icon-beaker';
        $helper->color = 'color3';
        $helper->title = $this->l('Purchased references', null, null, false);
        $helper->subtitle = $this->l('30 days', null, null, false);
        if (ConfigurationKPI::get('8020_SALES_CATALOG') !== false) {
            $helper->value = ConfigurationKPI::get('8020_SALES_CATALOG');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=8020_sales_catalog';
        $helper->tooltip = $this->l('X% of your references have been purchased for the past 30 days', null, null, false);
        $helper->refresh = (bool)(ConfigurationKPI::get('8020_SALES_CATALOG_EXPIRE') < $time);
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        if ($moduleManager->isInstalled('statsbestproducts')) {
            $helper->href = Context::getContext()->link->getAdminLink('AdminStats').'&module=statsbestproducts&datepickerFrom='.date('Y-m-d', strtotime('-30 days')).'&datepickerTo='.date('Y-m-d');
        }
        $kpis[] = $helper->generate();
        $helper = new HelperKpi();
        $helper->id = 'box-disabled-products';
        $helper->icon = 'icon-off';
        $helper->color = 'color4';
        $helper->href = $this->context->link->getAdminLink('AdminProducts');
        $helper->title = $this->l('Disabled Products', null, null, false);
        if (ConfigurationKPI::get('DISABLED_PRODUCTS') !== false) {
            $helper->value = ConfigurationKPI::get('DISABLED_PRODUCTS');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=disabled_products';
        $helper->refresh = (bool)(ConfigurationKPI::get('DISABLED_PRODUCTS_EXPIRE') < $time);
        $helper->tooltip = $this->l('X% of your products are disabled and not visible to your customers', null, null, false);
        $helper->href = Context::getContext()->link->getAdminLink('AdminProducts').'&productFilter_active=0&submitFilterproduct=1';
        $kpis[] = $helper->generate();
        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;
        return $helper->generate();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');
        return parent::renderList();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function ajaxProcessProductManufacturers()
    {
        $manufacturers = Manufacturer::getManufacturers(false, 0, true, false, false, false, true);
        $jsonArray = [];
        if ($manufacturers) {
            foreach ($manufacturers as $manufacturer) {
                $jsonArray[] = '{"optionValue": "'.(int)$manufacturer['id_manufacturer'].'", "optionDisplay": "'.htmlspecialchars(trim($manufacturer['name'])).'"}';
            }
        }
        die('['.implode(',', $jsonArray).']');
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public static function recurseCategoryForInclude($id_obj, $indexedCategories, $categories, $current, $id_category = null, $id_category_default = null, $has_suite = [])
    {
        global $done;
        static $irow;
        $content = '';
        if (!$id_category) {
            $id_category = (int)Configuration::get('PS_ROOT_CATEGORY');
        }
        if (!isset($done[$current['infos']['id_parent']])) {
            $done[$current['infos']['id_parent']] = 0;
        }
        $done[$current['infos']['id_parent']] += 1;
        $todo = count($categories[$current['infos']['id_parent']]);
        $doneC = $done[$current['infos']['id_parent']];
        $level = $current['infos']['level_depth'] + 1;
        $content .= '
		<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
			<td>
				<input type="checkbox" name="categoryBox[]" class="categoryBox'.($id_category_default == $id_category ? ' id_category_default' : '').'" id="categoryBox_'.$id_category.'" value="'.$id_category.'"'.((in_array($id_category, $indexedCategories) || ((int)(Tools::getValue('id_category')) == $id_category && !(int)($id_obj))) ? ' checked="checked"' : '').' />
			</td>
			<td>
				'.$id_category.'
			</td>
			<td>';
        for ($i = 2; $i < $level; $i++) {
            $content .= '<img src="../img/admin/lvl_'.$has_suite[$i - 2].'.gif" alt="" />';
        }
        $content .= '<img src="../img/admin/'.($level == 1 ? 'lv1.gif' : 'lv2_'.($todo == $doneC ? 'f' : 'b').'.gif').'" alt="" /> &nbsp;
			<label for="categoryBox_'.$id_category.'" class="t">'.stripslashes($current['infos']['name']).'</label></td>
		</tr>';
        if ($level > 1) {
            $has_suite[] = ($todo == $doneC ? 0 : 1);
        }
        if (isset($categories[$id_category])) {
            foreach ($categories[$id_category] as $key => $row) {
                if ($key != 'infos') {
                    $content .= AdminProductsController::recurseCategoryForInclude($id_obj, $indexedCategories, $categories, $categories[$id_category][$key], $key, $id_category_default, $has_suite);
                }
            }
        }
        return $content;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    protected function _displayDraftWarning($active)
    {
        $content = '<div class="warn draft" style="'.($active ? 'display:none' : '').'">
				<span>'.$this->l('Your product will be saved as a draft.').'</span>
				<a href="#" class="btn btn-default pull-right" onclick="submitAddProductAndPreview()" ><i class="icon-external-link-sign"></i> '.$this->l('Save and preview').'</a>
				<input type="hidden" name="fakeSubmitAddProductAndPreview" id="fakeSubmitAddProductAndPreview" />
	 		</div>';
        $this->tpl_form_vars['draft_warning'] = $content;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_product'] = [
                    'href' => self::$currentIndex.'&addproduct&token='.$this->token,
                    'desc' => $this->l('Add new product', null, null, false),
                    'icon' => 'process-icon-new'
                ];
        }
        if ($this->display == 'edit') {
            if (($product = $this->loadObject(true)) && $product->isAssociatedToShop()) {
                if ($url_preview = $this->getPreviewUrl($product)) {
                    $this->page_header_toolbar_btn['preview'] = [
                        'short' => $this->l('Preview', null, null, false),
                        'href' => $url_preview,
                        'desc' => $this->l('Preview', null, null, false),
                        'target' => true,
                        'class' => 'previewUrl'
                    ];
                }
                $js = (bool)Image::getImages($this->context->language->id, (int)$product->id) ?
                    'confirm_link(\'\', \''.$this->l('This will copy the images too. If you wish to proceed, click "Yes". If not, click "No".', null, true, false).'\', \''.$this->l('Yes', null, true, false).'\', \''.$this->l('No', null, true, false).'\', \''.$this->context->link->getAdminLink('AdminProducts', true).'&id_product='.(int)$product->id.'&duplicateproduct'.'\', \''.$this->context->link->getAdminLink('AdminProducts', true).'&id_product='.(int)$product->id.'&duplicateproduct&noimage=1'.'\')'
                    :
                    'document.location = \''.$this->context->link->getAdminLink('AdminProducts', true).'&id_product='.(int)$product->id.'&duplicateproduct&noimage=1'.'\'';
                if ($this->access('add')) {
                    $this->page_header_toolbar_btn['duplicate'] = [
                        'short' => $this->l('Duplicate', null, null, false),
                        'desc' => $this->l('Duplicate', null, null, false),
                        'confirm' => 1,
                        'js' => $js
                    ];
                }
                if (file_exists(_PS_MODULE_DIR_.'statsproduct/statsproduct.php')) {
                    $this->page_header_toolbar_btn['stats'] = [
                    'short' => $this->l('Statistics', null, null, false),
                    'href' => $this->context->link->getAdminLink('AdminStats').'&module=statsproduct&id_product='.(int)$product->id,
                    'desc' => $this->l('Product sales', null, null, false),
                ];
                }
                if ($this->access('delete')) {
                    $this->page_header_toolbar_btn['delete'] = [
                        'short' => $this->l('Delete', null, null, false),
                        'href' => $this->context->link->getAdminLink('AdminProducts').'&id_product='.(int)$product->id.'&deleteproduct',
                        'desc' => $this->l('Delete this product', null, null, false),
                        'confirm' => 1,
                        'js' => 'if (confirm(\''.$this->l('Delete product?', null, true, false).'\')){return true;}else{event.preventDefault();}'
                    ];
                }
            }
        }
        parent::initPageHeaderToolbar();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function initToolbar()
    {
        parent::initToolbar();
        if ($this->display == 'edit' || $this->display == 'add') {
            $this->toolbar_btn['save'] = [
                'short' => 'Save',
                'href' => '#',
                'desc' => $this->l('Save'),
            ];
            $this->toolbar_btn['save-and-stay'] = [
                'short' => 'SaveAndStay',
                'href' => '#',
                'desc' => $this->l('Save and stay'),
            ];
            $this->toolbar_btn['newCombination'] = [
                'short' => 'New combination',
                'desc' => $this->l('New combination'),
                'class' => 'toolbar-new'
            ];
        } else {
            $this->toolbar_btn['import'] = [
                'href' => $this->context->link->getAdminLink('AdminImport', true).'&import_type=products',
                'desc' => $this->l('Import')
            ];
        }
        $this->context->smarty->assign('toolbar_scroll', 1);
        $this->context->smarty->assign('show_toolbar', 1);
        $this->context->smarty->assign('toolbar_btn', $this->toolbar_btn);
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function renderForm()
    {
        $this->product_name = $this->object->name[$this->context->language->id];
        if (!method_exists($this, 'initForm'.$this->tab_display)) {
            return;
        }
        $product = $this->object;
        $this->context->smarty->assign('bullet_common_field', '');
        if (Shop::isFeatureActive() && $this->display == 'edit') {
            if (Shop::getContext() != Shop::CONTEXT_SHOP) {
                $this->context->smarty->assign([
                    'display_multishop_checkboxes' => true,
                    'multishop_check' => Tools::getValue('multishop_check'),
                ]);
            }
            if (Shop::getContext() != Shop::CONTEXT_ALL) {
                $this->context->smarty->assign('bullet_common_field', '<i class="icon-circle text-orange"></i>');
                $this->context->smarty->assign('display_common_field', true);
            }
        }
        $this->tpl_form_vars['tabs_preloaded'] = $this->available_tabs;
        $this->tpl_form_vars['product_type'] = (int)Tools::getValue('type_product', $product->getType());
        $this->getLanguages();
        $this->tpl_form_vars['id_lang_default'] = Configuration::get('PS_LANG_DEFAULT');
        $this->tpl_form_vars['currentIndex'] = self::$currentIndex;
        $this->tpl_form_vars['display_multishop_checkboxes'] = (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP && $this->display == 'edit');
        $this->fields_form = [''];
        $this->tpl_form_vars['token'] = $this->token;
        $this->tpl_form_vars['combinationImagesJs'] = $this->getCombinationImagesJs();
        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
        $this->tpl_form_vars['post_data'] = json_encode($_POST);
        $this->tpl_form_vars['save_error'] = !empty($this->errors);
        $this->tpl_form_vars['mod_evasive'] = Tools::apacheModExists('evasive');
        $this->tpl_form_vars['mod_security'] = Tools::apacheModExists('security');
        $this->tpl_form_vars['ps_force_friendly_product'] = Configuration::get('PS_FORCE_FRIENDLY_PRODUCT');
        $this->tpl_form_vars['tinymce'] = true;
        $iso = $this->context->language->iso_code;
        $this->tpl_form_vars['iso'] = file_exists(_PS_CORE_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en';
        $this->tpl_form_vars['path_css'] = _THEME_CSS_DIR_;
        $this->tpl_form_vars['ad'] = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
        if (Validate::isLoadedObject(($this->object))) {
            $id_product = (int)$this->object->id;
        } else {
            $id_product = (int)Tools::getvalue('id_product');
        }
        $this->tpl_form_vars['form_action'] = $this->context->link->getAdminLink('AdminProducts').'&'.($id_product ? 'id_product='.(int)$id_product : 'addproduct');
        $this->tpl_form_vars['id_product'] = $id_product;
        $upload_max_filesize = Tools::getOctets(ini_get('upload_max_filesize'));
        $upload_max_filesize = ($upload_max_filesize / 1024) / 1024;
        $this->tpl_form_vars['upload_max_filesize'] = $upload_max_filesize;
        $this->tpl_form_vars['country_display_tax_label'] = $this->context->country->display_tax_label;
        $this->tpl_form_vars['has_combinations'] = $this->object->hasAttributes();
        $this->product_exists_in_shop = true;
        if ($this->display == 'edit' && Validate::isLoadedObject($product) && Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP && !$product->isAssociatedToShop($this->context->shop->id)) {
            $this->product_exists_in_shop = false;
            if ($this->tab_display == 'Informations') {
                $this->displayWarning($this->l('Warning: The product does not exist in this shop'));
            }
            $default_product = new Product();
            $definition = ObjectModel::getDefinition($product);
            foreach ($definition['fields'] as $field_name => $field) {
                if (isset($field['shop']) && $field['shop']) {
                    $product->$field_name = ObjectModel::formatValue($default_product->$field_name, $field['type']);
                }
            }
        }
        if (!Validate::isLoadedObject($this->object) && Tools::getValue('id_product')) {
            $this->errors[] = 'Unable to load object';
        } else {
            $this->_displayDraftWarning($this->object->active);
            if (!empty($this->errors)) {
                $this->copyFromPost($this->object, $this->table);
            }
            $this->initPack($this->object);
            $this->{'initForm'.$this->tab_display}($this->object);
            $this->tpl_form_vars['product'] = $this->object;
            if ($this->ajax) {
                if (!isset($this->tpl_form_vars['custom_form'])) {
                    throw new PrestaShopException('custom_form empty for action '.$this->tab_display);
                } else {
                    return $this->tpl_form_vars['custom_form'];
                }
            }
        }
        $parent = parent::renderForm();
        $this->addJqueryPlugin(['autocomplete', 'fancybox', 'typewatch']);
        return $parent;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function getPreviewUrl(Product $product)
    {
        $id_lang = Configuration::get('PS_LANG_DEFAULT', null, null, Context::getContext()->shop->id);
        if (!ShopUrl::getMainShopDomain()) {
            return false;
        }
        $is_rewrite_active = (bool)Configuration::get('PS_REWRITING_SETTINGS');
        $preview_url = $this->context->link->getProductLink(
            $product,
            $this->getFieldValue($product, 'link_rewrite', $this->context->language->id),
            Category::getLinkRewrite($this->getFieldValue($product, 'id_category_default'), $this->context->language->id),
            null,
            $id_lang,
            (int)Context::getContext()->shop->id,
            0,
            $is_rewrite_active
        );
        if (!$product->active) {
            $admin_dir = dirname($_SERVER['PHP_SELF']);
            $admin_dir = substr($admin_dir, strrpos($admin_dir, '/') + 1);
            $preview_url .= ((strpos($preview_url, '?') === false) ? '?' : '&').'adtoken='.$this->token.'&ad='.$admin_dir.'&id_employee='.(int)$this->context->employee->id;
        }
        return $preview_url;
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processSuppliers()
    {
        if ((int)Tools::getValue('supplier_loaded') === 1 && Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
            $attributes = $product->getAttributesResume($this->context->language->id);
            if (empty($attributes)) {
                $attributes[] = [
                    'id_product_attribute' => 0,
                    'attribute_designation' => ''
                ];
            }
            $suppliers = Supplier::getSuppliers();
            $associated_suppliers = ProductSupplier::getSupplierCollection($product->id);
            $suppliers_to_associate = [];
            $new_default_supplier = 0;
            if (Tools::isSubmit('default_supplier')) {
                $new_default_supplier = (int)Tools::getValue('default_supplier');
            }
            foreach ($suppliers as $supplier) {
                if (Tools::isSubmit('check_supplier_'.$supplier['id_supplier'])) {
                    $suppliers_to_associate[] = $supplier['id_supplier'];
                }
            }
            foreach ($associated_suppliers as $key => $associated_supplier) {
                if (!in_array($associated_supplier->id_supplier, $suppliers_to_associate)) {
                    $associated_supplier->delete();
                    unset($associated_suppliers[$key]);
                }
            }
            foreach ($suppliers_to_associate as $id) {
                $to_add = true;
                foreach ($associated_suppliers as $as) {
                    if ($id == $as->id_supplier) {
                        $to_add = false;
                    }
                }
                if ($to_add) {
                    $product_supplier = new ProductSupplier();
                    $product_supplier->id_product = $product->id;
                    $product_supplier->id_product_attribute = 0;
                    $product_supplier->id_supplier = $id;
                    if ($this->context->currency->id) {
                        $product_supplier->id_currency = (int)$this->context->currency->id;
                    } else {
                        $product_supplier->id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                    }
                    $product_supplier->save();
                    $associated_suppliers[] = $product_supplier;
                    foreach ($attributes as $attribute) {
                        if ((int)$attribute['id_product_attribute'] > 0) {
                            $product_supplier = new ProductSupplier();
                            $product_supplier->id_product = $product->id;
                            $product_supplier->id_product_attribute = (int)$attribute['id_product_attribute'];
                            $product_supplier->id_supplier = $id;
                            $product_supplier->save();
                        }
                    }
                }
            }
            foreach ($attributes as $attribute) {
                foreach ($associated_suppliers as $supplier) {
                    if (Tools::isSubmit('supplier_reference_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier) ||
                        (Tools::isSubmit('product_price_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier) &&
                         Tools::isSubmit('product_price_currency_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier))) {
                        $reference = pSQL(
                            Tools::getValue(
                                'supplier_reference_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier,
                                ''
                            )
                        );
                        $price = (float)str_replace(
                            [' ', ','],
                            ['', '.'],
                            Tools::getValue(
                                'product_price_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier,
                                0
                            )
                        );
                        $price = Tools::ps_round($price, 6);
                        $id_currency = (int)Tools::getValue(
                            'product_price_currency_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier,
                            0
                        );
                        if ($id_currency <= 0 || (!($result = Currency::getCurrency($id_currency)) || empty($result))) {
                            $this->errors[] = Tools::displayError('The selected currency is not valid');
                        }
                        $product_supplier_id = (int)ProductSupplier::getIdByProductAndSupplier($product->id, $attribute['id_product_attribute'], $supplier->id_supplier);
                        if (!$product_supplier_id) {
                            $product->addSupplierReference($supplier->id_supplier, (int)$attribute['id_product_attribute'], $reference, (float)$price, (int)$id_currency);
                            if ($product->id_supplier == $supplier->id_supplier) {
                                if ((int)$attribute['id_product_attribute'] > 0) {
                                    $data = [
                                        'supplier_reference' => pSQL($reference),
                                        'wholesale_price' => (float)Tools::convertPrice($price, $id_currency)
                                    ];
                                    $where = '
										a.id_product = '.(int)$product->id.'
										AND a.id_product_attribute = '.(int)$attribute['id_product_attribute'];
                                    ObjectModel::updateMultishopTable('Combination', $data, $where);
                                } else {
                                    $product->wholesale_price = (float)Tools::convertPrice($price, $id_currency); //converted in the default currency
                                    $product->supplier_reference = pSQL($reference);
                                    $product->update();
                                }
                            }
                        } else {
                            $product_supplier = new ProductSupplier($product_supplier_id);
                            $product_supplier->id_currency = (int)$id_currency;
                            $product_supplier->product_supplier_price_te = (float)$price;
                            $product_supplier->product_supplier_reference = pSQL($reference);
                            $product_supplier->update();
                        }
                    } elseif (Tools::isSubmit('supplier_reference_'.$product->id.'_'.$attribute['id_product_attribute'].'_'.$supplier->id_supplier)) {
                        if ((int)$attribute['id_product_attribute'] > 0) {
                            $product_supplier = new ProductSupplier();
                            $product_supplier->id_product = $product->id;
                            $product_supplier->id_product_attribute = (int)$attribute['id_product_attribute'];
                            $product_supplier->id_supplier = $supplier->id_supplier;
                            $product_supplier->save();
                        }
                    }
                }
            }
            if ($new_default_supplier != $product->id_supplier) {
                $this->object->id_supplier = $new_default_supplier;
                $this->object->update();
            }
        }
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function processWarehouses()
    {
        if ((int)Tools::getValue('warehouse_loaded') === 1 && Validate::isLoadedObject($product = new Product((int)$id_product = Tools::getValue('id_product')))) {
            $attributes = $product->getAttributesResume($this->context->language->id);
            if (empty($attributes)) {
                $attributes[] = [
                    'id_product_attribute' => 0,
                    'attribute_designation' => ''
                ];
            }
            $warehouses = Warehouse::getWarehouses(true);
            $associated_warehouses_collection = WarehouseProductLocation::getCollection($product->id);
            $elements_to_manage = [];
            foreach ($attributes as $attribute) {
                foreach ($warehouses as $warehouse) {
                    $key = $warehouse['id_warehouse'].'_'.$product->id.'_'.$attribute['id_product_attribute'];
                    if (Tools::isSubmit('check_warehouse_'.$key)) {
                        $location = Tools::getValue('location_warehouse_'.$key, '');
                        $elements_to_manage[$key] = $location;
                    }
                }
            }
            foreach ($associated_warehouses_collection as $awc) {
                if (!array_key_exists($awc->id_warehouse.'_'.$awc->id_product.'_'.$awc->id_product_attribute, $elements_to_manage)) {
                    $awc->delete();
                }
            }
            foreach ($elements_to_manage as $key => $location) {
                $params = explode('_', $key);
                $wpl_id = (int)WarehouseProductLocation::getIdByProductAndWarehouse((int)$params[1], (int)$params[2], (int)$params[0]);
                if (empty($wpl_id)) {
                    $warehouse_location_entity = new WarehouseProductLocation();
                    $warehouse_location_entity->id_product = (int)$params[1];
                    $warehouse_location_entity->id_product_attribute = (int)$params[2];
                    $warehouse_location_entity->id_warehouse = (int)$params[0];
                    $warehouse_location_entity->location = pSQL($location);
                    $warehouse_location_entity->save();
                } else {
                    $warehouse_location_entity = new WarehouseProductLocation((int)$wpl_id);
                    $location = pSQL($location);
                    if ($location != $warehouse_location_entity->location) {
                        $warehouse_location_entity->location = pSQL($location);
                        $warehouse_location_entity->update();
                    }
                }
            }
            StockAvailable::synchronize((int)$id_product);
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:32
    * version: 1
    */
    public function initFormAssociations($obj)
    {
        $product = $obj;
        $data = $this->createTemplate($this->tpl_form);
        $root = Category::getRootCategory();
        $default_category = $this->context->cookie->id_category_products_filter ? $this->context->cookie->id_category_products_filter : Context::getContext()->shop->id_category;
        if (!$product->id || !$product->isAssociatedToShop()) {
            $selected_cat = Category::getCategoryInformations(Tools::getValue('categoryBox', [$default_category]), $this->default_form_language);
        } else {
            if (Tools::isSubmit('categoryBox')) {
                $selected_cat = Category::getCategoryInformations(Tools::getValue('categoryBox', [$default_category]), $this->default_form_language);
            } else {
                $selected_cat = Product::getProductCategoriesFull($product->id, $this->default_form_language);
            }
        }
        $data->assign('feature_shop_active', Shop::isFeatureActive());
        $helper = new HelperForm();
        if ($this->object && $this->object->id) {
            $helper->id = $this->object->id;
        } else {
            $helper->id = null;
        }
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;
        $accessories = Product::getAccessoriesLight($this->context->language->id, $product->id);
        if ($post_accessories = Tools::getValue('inputAccessories')) {
            $post_accessories_tab = explode('-', $post_accessories);
            foreach ($post_accessories_tab as $accessory_id) {
                if (!$this->haveThisAccessory($accessory_id, $accessories) && $accessory = Product::getAccessoryById($accessory_id)) {
                    $accessories[] = $accessory;
                }
            }
        }
        $data->assign('accessories', $accessories);
        $product->manufacturer_name = Manufacturer::getNameById($product->id_manufacturer);
        $categories = [];
        foreach ($selected_cat as $key => $category) {
            $categories[] = $key;
        }
        $tree = new HelperTreeCategories('associated-categories-tree', 'Associated categories');
        $tree->setTemplate('tree_associated_categories.tpl')
            ->setHeaderTemplate('tree_associated_header.tpl')
            ->setRootCategory($root->id)
            ->setUseCheckBox(true)
            ->setUseSearch(true)
            ->setSelectedCategories($categories);
        $data->assign(['default_category' => $default_category,
                    'selected_cat_ids' => implode(',', array_keys($selected_cat)),
                    'selected_cat' => $selected_cat,
                    'id_category_default' => $product->getDefaultCategory(),
                    'category_tree' => $tree->render(),
                    'product' => $product,
                    'link' => $this->context->link,
                    'is_shop_context' => Shop::getContext() == Shop::CONTEXT_SHOP
        ]);
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormPrices($obj)
    {
        $data = $this->createTemplate($this->tpl_form);
        $product = $obj;
        if ($obj->id) {
            $shops = Shop::getShops();
            $countries = Country::getCountries($this->context->language->id);
            $groups = Group::getGroups($this->context->language->id);
            $currencies = Currency::getCurrencies();
            $attributes = $obj->getAttributesGroups((int)$this->context->language->id);
            $combinations = [];
            foreach ($attributes as $attribute) {
                $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
                if (!isset($combinations[$attribute['id_product_attribute']]['attributes'])) {
                    $combinations[$attribute['id_product_attribute']]['attributes'] = '';
                }
                $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'].' - ';
                $combinations[$attribute['id_product_attribute']]['price'] = Tools::displayPrice(
                    Tools::convertPrice(
                        Product::getPriceStatic((int)$obj->id, false, $attribute['id_product_attribute']),
                        $this->context->currency
                    ), $this->context->currency
                );
            }
            foreach ($combinations as &$combination) {
                $combination['attributes'] = rtrim($combination['attributes'], ' - ');
            }
            $data->assign('specificPriceModificationForm', $this->_displaySpecificPriceModificationForm(
                $this->context->currency, $shops, $currencies, $countries, $groups)
            );
            $data->assign('ecotax_tax_excl', $obj->ecotax);
            $this->_applyTaxToEcotax($obj);
            $data->assign([
                'shops' => $shops,
                'admin_one_shop' => count($this->context->employee->getAssociatedShops()) == 1,
                'currencies' => $currencies,
                'countries' => $countries,
                'groups' => $groups,
                'combinations' => $combinations,
                'multi_shop' => Shop::isFeatureActive(),
                'link' => new Link(),
                'pack' => new Pack()
            ]);
        } else {
            $this->displayWarning($this->l('You must save this product before adding specific pricing'));
            $product->id_tax_rules_group = (int)Product::getIdTaxRulesGroupMostUsed();
            $data->assign('ecotax_tax_excl', 0);
        }
        $address = new Address();
        $address->id_country = (int)$this->context->country->id;
        $tax_rules_groups = TaxRulesGroup::getTaxRulesGroups(true);
        $tax_rates = [
            0 => [
                'id_tax_rules_group' => 0,
                'rates' => [0],
                'computation_method' => 0
            ]
        ];
        foreach ($tax_rules_groups as $tax_rules_group) {
            $id_tax_rules_group = (int)$tax_rules_group['id_tax_rules_group'];
            $tax_calculator = TaxManagerFactory::getManager($address, $id_tax_rules_group)->getTaxCalculator();
            $tax_rates[$id_tax_rules_group] = [
                'id_tax_rules_group' => $id_tax_rules_group,
                'rates' => [],
                'computation_method' => (int)$tax_calculator->computation_method
            ];
            if (isset($tax_calculator->taxes) && count($tax_calculator->taxes)) {
                foreach ($tax_calculator->taxes as $tax) {
                    $tax_rates[$id_tax_rules_group]['rates'][] = (float)$tax->rate;
                }
            } else {
                $tax_rates[$id_tax_rules_group]['rates'][] = 0;
            }
        }
        $data->assign([
            'link' => $this->context->link,
            'currency' => $currency = $this->context->currency,
            'tax_rules_groups' => $tax_rules_groups,
            'taxesRatesByGroup' => $tax_rates,
            'ecotaxTaxRate' => Tax::getProductEcotaxRate(),
            'tax_exclude_taxe_option' => Tax::excludeTaxeOption(),
            'ps_use_ecotax' => Configuration::get('PS_USE_ECOTAX'),
        ]);
        $product->price = Tools::convertPrice($product->price, $this->context->currency, true, $this->context);
        if ($product->unit_price_ratio != 0) {
            $data->assign('unit_price', Tools::ps_round($product->price / $product->unit_price_ratio, 6));
        } else {
            $data->assign('unit_price', 0);
        }
        $data->assign('ps_tax', Configuration::get('PS_TAX'));
        $data->assign('country_display_tax_label', $this->context->country->display_tax_label);
        $data->assign([
            'currency', $this->context->currency,
            'product' => $product,
            'token' => $this->token
        ]);
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormSeo($product)
    {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }
        $data = $this->createTemplate($this->tpl_form);
        $context = Context::getContext();
        $rewritten_links = [];
        foreach ($this->_languages as $language) {
            $category = Category::getLinkRewrite((int)$product->id_category_default, (int)$language['id_lang']);
            $rewritten_links[(int)$language['id_lang']] = explode(
                '[REWRITE]',
                $context->link->getProductLink($product, '[REWRITE]', $category, null, (int)$language['id_lang'])
            );
        }
        $data->assign([
            'product' => $product,
            'languages' => $this->_languages,
            'id_lang' => $this->context->language->id,
            'ps_ssl_enabled' => Configuration::get('PS_SSL_ENABLED'),
            'curent_shop_url' => $this->context->shop->getBaseURL(),
            'default_form_language' => $this->default_form_language,
            'rewritten_links' => $rewritten_links
        ]);
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function getPackItems($product = null)
    {
        $pack_items = [];
        if (!$product) {
            $names_input = Tools::getValue('namePackItems');
            $ids_input = Tools::getValue('inputPackItems');
            if (!$names_input || !$ids_input) {
                return [];
            }
            $ids = array_unique(explode('-', $ids_input));
            $names = array_unique(explode('¤', $names_input));
            if (!empty($ids)) {
                $length = count($ids);
                for ($i = 0; $i < $length; $i++) {
                    if (!empty($ids[$i]) && !empty($names[$i])) {
                        list($pack_items[$i]['pack_quantity'], $pack_items[$i]['id']) = explode('x', $ids[$i]);
                        $exploded_name = explode('x', $names[$i]);
                        $pack_items[$i]['name'] = $exploded_name[1];
                    }
                }
            }
        } else {
            $i = 0;
            foreach ($product->packItems as $pack_item) {
                $pack_items[$i]['id'] = $pack_item->id;
                $pack_items[$i]['pack_quantity'] = $pack_item->pack_quantity;
                $pack_items[$i]['name']    = $pack_item->name;
                $pack_items[$i]['reference'] = $pack_item->reference;
                $pack_items[$i]['id_product_attribute'] = isset($pack_item->id_pack_product_attribute) && $pack_item->id_pack_product_attribute ? $pack_item->id_pack_product_attribute : 0;
                $cover = $pack_item->id_pack_product_attribute ? Product::getCombinationImageById($pack_item->id_pack_product_attribute, Context::getContext()->language->id) : Product::getCover($pack_item->id);
                $pack_items[$i]['image'] = Context::getContext()->link->getImageLink($pack_item->link_rewrite, $cover['id_image'], 'home_default');
                $i++;
            }
        }
        return $pack_items;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormPack($product)
    {
        $data = $this->createTemplate($this->tpl_form);
        if (Tools::getValue('namePackItems')) {
            $input_pack_items = Tools::getValue('inputPackItems');
            $input_namepack_items = Tools::getValue('namePackItems');
            $pack_items = $this->getPackItems();
        } else {
            $product->packItems = Pack::getItems($product->id, $this->context->language->id);
            $pack_items = $this->getPackItems($product);
            $input_namepack_items = '';
            $input_pack_items = '';
            foreach ($pack_items as $pack_item) {
                $input_pack_items .= $pack_item['pack_quantity'].'x'.$pack_item['id'].'x'.$pack_item['id_product_attribute'].'-';
                $input_namepack_items .= $pack_item['pack_quantity'].' x '.$pack_item['name'].'¤';
            }
        }
        $data->assign([
            'input_pack_items' => $input_pack_items,
            'input_namepack_items' => $input_namepack_items,
            'pack_items' => $pack_items,
            'product_type' => (int)Tools::getValue('type_product', $product->getType())
        ]);
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormVirtualProduct($product)
    {
        $data = $this->createTemplate($this->tpl_form);
        $currency = $this->context->currency;

        $product_download = new ProductDownload();
        if ($id_product_download = $product_download->getIdFromIdProduct($this->getFieldValue($product, 'id'))) {
            $product_download = new ProductDownload($id_product_download);
        }
        $product->{'productDownload'} = $product_download;
        if ($product->productDownload->id && empty($product->productDownload->display_filename)) {
            $this->errors[] = Tools::displayError('A file name is required in order to associate a file');
            $this->tab_display = 'VirtualProduct';
        }
        $exists_file = realpath(_PS_DOWNLOAD_DIR_).'/'.$product->productDownload->filename;
        $data->assign('product_downloaded', $product->productDownload->id);
        if (!file_exists($exists_file)
            && !empty($product->productDownload->display_filename)
            && empty($product->cache_default_attribute)) {
            $msg = sprintf(Tools::displayError('File "%s" is missing'),
                $product->productDownload->display_filename);
        } else {
            $msg = '';
        }
        $virtual_product_file_uploader = new HelperUploader('virtual_product_file_uploader');
        $virtual_product_file_uploader->setMultiple(false)->setUrl(
            Context::getContext()->link->getAdminLink('AdminProducts').'&ajax=1&id_product='.(int)$product->id
            .'&action=AddVirtualProductFile')->setPostMaxSize(Tools::getOctets(ini_get('upload_max_filesize')))
            ->setTemplate('virtual_product.tpl');
        $data->assign([
            'download_product_file_missing' => $msg,
            'download_dir_writable' => ProductDownload::checkWritableDir(),
            'up_filename' => strval(Tools::getValue('virtual_product_filename'))
        ]);
        $product->productDownload->nb_downloadable = ($product->productDownload->id > 0) ? $product->productDownload->nb_downloadable : htmlentities(Tools::getValue('virtual_product_nb_downloable'), ENT_COMPAT, 'UTF-8');
        $product->productDownload->date_expiration = ($product->productDownload->id > 0) ? ((!empty($product->productDownload->date_expiration) && $product->productDownload->date_expiration != '0000-00-00 00:00:00') ? date('Y-m-d', strtotime($product->productDownload->date_expiration)) : '') : htmlentities(Tools::getValue('virtual_product_expiration_date'), ENT_COMPAT, 'UTF-8');
        $product->productDownload->nb_days_accessible = ($product->productDownload->id > 0) ? $product->productDownload->nb_days_accessible : htmlentities(Tools::getValue('virtual_product_nb_days'), ENT_COMPAT, 'UTF-8');
        $product->productDownload->is_shareable = $product->productDownload->id > 0 && $product->productDownload->is_shareable;
        $iso_tiny_mce = $this->context->language->iso_code;
        $iso_tiny_mce = (file_exists(_PS_JS_DIR_.'tiny_mce/langs/'.$iso_tiny_mce.'.js') ? $iso_tiny_mce : 'en');
        $data->assign([
            'ad' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_),
            'iso_tiny_mce' => $iso_tiny_mce,
            'product' => $product,
            'token' => $this->token,
            'currency' => $currency,
            'link' => $this->context->link,
            'is_file' => $product->productDownload->checkFile(),
            'virtual_product_file_uploader' => $virtual_product_file_uploader->render()
        ]);
        $data->assign($this->tpl_form_vars);
        $this->tpl_form_vars['product'] = $product;
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    protected function _getFinalPrice($specific_price, $product_price, $tax_rate)
    {
        return $this->object->getPrice(false, $specific_price['id_product_attribute'], 2);
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    protected function _displaySpecificPriceModificationForm($defaultCurrency, $shops, $currencies, $countries, $groups)
    {
        if (!($obj = $this->loadObject())) {
            return;
        }
        $content = '';
        $specific_prices = SpecificPrice::getByProductId((int)$obj->id);
        $specific_price_priorities = SpecificPrice::getPriority((int)$obj->id);
        $tmp = [];
        foreach ($shops as $shop) {
            $tmp[$shop['id_shop']] = $shop;
        }
        $shops = $tmp;
        $tmp = [];
        foreach ($currencies as $currency) {
            $tmp[$currency['id_currency']] = $currency;
        }
        $currencies = $tmp;
        $tmp = [];
        foreach ($countries as $country) {
            $tmp[$country['id_country']] = $country;
        }
        $countries = $tmp;
        $tmp = [];
        foreach ($groups as $group) {
            $tmp[$group['id_group']] = $group;
        }
        $groups = $tmp;
        $length_before = strlen($content);
        if (is_array($specific_prices) && count($specific_prices)) {
            $i = 0;
            foreach ($specific_prices as $specific_price) {
                $id_currency = $specific_price['id_currency'] ? $specific_price['id_currency'] : $defaultCurrency->id;
                if (!isset($currencies[$id_currency])) {
                    continue;
                }
                $current_specific_currency = $currencies[$id_currency];
                if ($specific_price['reduction_type'] == 'percentage') {
                    $impact = '- '.($specific_price['reduction'] * 100).' %';
                } elseif ($specific_price['reduction'] > 0) {
                    $impact = '- '.Tools::displayPrice(Tools::ps_round($specific_price['reduction'], 2), $current_specific_currency).' ';
                    if ($specific_price['reduction_tax']) {
                        $impact .= '('.$this->l('Tax incl.').')';
                    } else {
                        $impact .= '('.$this->l('Tax excl.').')';
                    }
                } else {
                    $impact = '--';
                }
                if ($specific_price['from'] == '0000-00-00 00:00:00' && $specific_price['to'] == '0000-00-00 00:00:00') {
                    $period = $this->l('Unlimited');
                } else {
                    $period = $this->l('From').' '.($specific_price['from'] != '0000-00-00 00:00:00' ? $specific_price['from'] : '0000-00-00 00:00:00').'<br />'.$this->l('To').' '.($specific_price['to'] != '0000-00-00 00:00:00' ? $specific_price['to'] : '0000-00-00 00:00:00');
                }
                if ($specific_price['id_product_attribute']) {
                    $combination = new Combination((int)$specific_price['id_product_attribute']);
                    $attributes = $combination->getAttributesName((int)$this->context->language->id);
                    $attributes_name = '';
                    foreach ($attributes as $attribute) {
                        $attributes_name .= $attribute['name'].' - ';
                    }
                    $attributes_name = rtrim($attributes_name, ' - ');
                } else {
                    $attributes_name = $this->l('All combinations');
                }
                $rule = new SpecificPriceRule((int)$specific_price['id_specific_price_rule']);
                $rule_name = ($rule->id ? $rule->name : '--');
                if ($specific_price['id_customer']) {
                    $customer = new Customer((int)$specific_price['id_customer']);
                    if (Validate::isLoadedObject($customer)) {
                        $customer_full_name = $customer->firstname.' '.$customer->lastname;
                    }
                    unset($customer);
                }
                if (!$specific_price['id_shop'] || in_array($specific_price['id_shop'], Shop::getContextListShopID())) {
                    $content .= '
					<tr '.($i % 2 ? 'class="alt_row"' : '').'>
						<td>'.$rule_name.'</td>
						<td>'.$attributes_name.'</td>';
                    $can_delete_specific_prices = true;
                    if (Shop::isFeatureActive()) {
                        $id_shop_sp = $specific_price['id_shop'];
                        $can_delete_specific_prices = (count($this->context->employee->getAssociatedShops()) > 1 && !$id_shop_sp) || $id_shop_sp;
                        $content .= '
						<td>'.($id_shop_sp ? $shops[$id_shop_sp]['name'] : $this->l('All shops')).'</td>';
                    }
                    $price = Tools::ps_round($specific_price['price'], 2);
                    $fixed_price = ($price == Tools::ps_round($obj->price, 2) || $specific_price['price'] == -1) ? '--' : Tools::displayPrice($price, $current_specific_currency);
                    $content .= '
						<td>'.($specific_price['id_currency'] ? $currencies[$specific_price['id_currency']]['name'] : $this->l('All currencies')).'</td>
						<td>'.($specific_price['id_country'] ? $countries[$specific_price['id_country']]['name'] : $this->l('All countries')).'</td>
						<td>'.($specific_price['id_group'] ? $groups[$specific_price['id_group']]['name'] : $this->l('All groups')).'</td>
						<td title="'.$this->l('ID:').' '.$specific_price['id_customer'].'">'.(isset($customer_full_name) ? $customer_full_name : $this->l('All customers')).'</td>
						<td>'.$fixed_price.'</td>
						<td>'.$impact.'</td>
						<td>'.$period.'</td>
						<td>'.$specific_price['from_quantity'].'</th>
						<td>'.((!$rule->id && $can_delete_specific_prices) ? '<a class="btn btn-default" name="delete_link" href="'.self::$currentIndex.'&id_product='.(int)Tools::getValue('id_product').'&action=deleteSpecificPrice&id_specific_price='.(int)($specific_price['id_specific_price']).'&token='.Tools::getValue('token').'"><i class="icon-trash"></i></a>': '').'</td>
					</tr>';
                    $i++;
                    unset($customer_full_name);
                }
            }
        }
        if ($length_before === strlen($content)) {
            $content .= '
				<tr>
					<td class="text-center" colspan="13"><i class="icon-warning-sign"></i>&nbsp;'.$this->l('No specific prices.').'</td>
				</tr>';
        }
        $content .= '
				</tbody>
			</table>
			</div>
			<div class="panel-footer">
				<a href="'.$this->context->link->getAdminLink('AdminProducts').'" class="btn btn-default"><i class="process-icon-cancel"></i> '.$this->l('Cancel').'</a>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save') .'</button>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save and stay') .'</button>
			</div>
		</div>';
        $content .= '
		<script type="text/javascript">
			var currencies = new Array();
			currencies[0] = new Array();
			currencies[0]["sign"] = "'.$defaultCurrency->sign.'";
			currencies[0]["format"] = '.intval($defaultCurrency->format).';
			';
        foreach ($currencies as $currency) {
            $content .= '
				currencies['.$currency['id_currency'].'] = new Array();
				currencies['.$currency['id_currency'].']["sign"] = "'.$currency['sign'].'";
				currencies['.$currency['id_currency'].']["format"] = '.intval($currency['format']).';
				';
        }
        $content .= '
		</script>
		';
        if ($specific_price_priorities[0] == 'id_customer') {
            unset($specific_price_priorities[0]);
        }
        $specific_price_priorities = array_values($specific_price_priorities);
        $content .= '<div class="panel">
		<h3>'.$this->l('Priority management').'</h3>
		<div class="alert alert-info">
				'.$this->l('Sometimes one customer can fit into multiple price rules. Priorities allow you to define which rule applies to the customer.').'
		</div>';
        $content .= '
		<div class="form-group">
			<label class="control-label col-lg-3" for="specificPricePriority1">'.$this->l('Priorities').'</label>
			<div class="input-group col-lg-9">
				<select id="specificPricePriority1" name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[0] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[0] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[0] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[0] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[1] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[1] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[1] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[1] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[2] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[2] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[2] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[2] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[3] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[3] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[3] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[3] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-9 col-lg-offset-3">
				<p class="checkbox">
					<label for="specificPricePriorityToAll"><input type="checkbox" name="specificPricePriorityToAll" id="specificPricePriorityToAll" />'.$this->l('Apply to all products').'</label>
				</p>
			</div>
		</div>
		<div class="panel-footer">
				<a href="'.$this->context->link->getAdminLink('AdminProducts').'" class="btn btn-default"><i class="process-icon-cancel"></i> '.$this->l('Cancel').'</a>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save') .'</button>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save and stay') .'</button>
			</div>
		</div>
		';
        return $content;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    protected function _getCustomizationFieldIds($labels, $alreadyGenerated, $obj)
    {
        $customizableFieldIds = [];
        if (isset($labels[Product::CUSTOMIZE_FILE])) {
            foreach ($labels[Product::CUSTOMIZE_FILE] as $id_customization_field => $label) {
                $customizableFieldIds[] = 'label_'.Product::CUSTOMIZE_FILE.'_'.(int)($id_customization_field);
            }
        }
        if (isset($labels[Product::CUSTOMIZE_TEXTFIELD])) {
            foreach ($labels[Product::CUSTOMIZE_TEXTFIELD] as $id_customization_field => $label) {
                $customizableFieldIds[] = 'label_'.Product::CUSTOMIZE_TEXTFIELD.'_'.(int)($id_customization_field);
            }
        }
        $j = 0;
        for ($i = $alreadyGenerated[Product::CUSTOMIZE_FILE]; $i < (int)($this->getFieldValue($obj, 'uploadable_files')); $i++) {
            $customizableFieldIds[] = 'newLabel_'.Product::CUSTOMIZE_FILE.'_'.$j++;
        }
        $j = 0;
        for ($i = $alreadyGenerated[Product::CUSTOMIZE_TEXTFIELD]; $i < (int)($this->getFieldValue($obj, 'text_fields')); $i++) {
            $customizableFieldIds[] = 'newLabel_'.Product::CUSTOMIZE_TEXTFIELD.'_'.$j++;
        }
        return implode('¤', $customizableFieldIds);
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    protected function _displayLabelField(&$label, $languages, $default_language, $type, $fieldIds, $id_customization_field)
    {
        foreach ($languages as $language) {
            $input_value[$language['id_lang']] = (isset($label[(int)($language['id_lang'])])) ? $label[(int)($language['id_lang'])]['name'] : '';
        }
        $required = (isset($label[(int)($language['id_lang'])])) ? $label[(int)($language['id_lang'])]['required'] : false;
        $template = $this->context->smarty->createTemplate('controllers/products/input_text_lang.tpl',
            $this->context->smarty);
        return '<div class="form-group">'
            .'<div class="col-lg-6">'
            .$template->assign([
                'languages' => $languages,
                'input_name'  => 'label_'.$type.'_'.(int)($id_customization_field),
                'input_value' => $input_value
            ])->fetch()
            .'</div>'
            .'<div class="col-lg-6">'
            .'<div class="checkbox">'
            .'<label for="require_'.$type.'_'.(int)($id_customization_field).'">'
            .'<input type="checkbox" name="require_'.$type.'_'.(int)($id_customization_field).'" id="require_'.$type.'_'.(int)($id_customization_field).'" value="1" '.($required ? 'checked="checked"' : '').'/>'
            .$this->l('Required')
            .'</label>'
            .'</div>'
            .'</div>'
            .'</div>';
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    protected function _displayLabelFields(&$obj, &$labels, $languages, $default_language, $type)
    {
        $content = '';
        $type = (int)($type);
        $labelGenerated = [Product::CUSTOMIZE_FILE => (isset($labels[Product::CUSTOMIZE_FILE]) ? count($labels[Product::CUSTOMIZE_FILE]) : 0), Product::CUSTOMIZE_TEXTFIELD => (isset($labels[Product::CUSTOMIZE_TEXTFIELD]) ? count($labels[Product::CUSTOMIZE_TEXTFIELD]) : 0)];
        $fieldIds = $this->_getCustomizationFieldIds($labels, $labelGenerated, $obj);
        if (isset($labels[$type])) {
            foreach ($labels[$type] as $id_customization_field => $label) {
                $content .= $this->_displayLabelField($label, $languages, $default_language, $type, $fieldIds, (int)($id_customization_field));
            }
        }
        return $content;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormCustomization($obj)
    {
        $data = $this->createTemplate($this->tpl_form);
        if ((bool)$obj->id) {
            if ($this->product_exists_in_shop) {
                $labels = $obj->getCustomizationFields();
                $has_file_labels = (int)$this->getFieldValue($obj, 'uploadable_files');
                $has_text_labels = (int)$this->getFieldValue($obj, 'text_fields');
                $data->assign([
                    'obj' => $obj,
                    'table' => $this->table,
                    'languages' => $this->_languages,
                    'has_file_labels' => $has_file_labels,
                    'display_file_labels' => $this->_displayLabelFields($obj, $labels, $this->_languages, Configuration::get('PS_LANG_DEFAULT'), Product::CUSTOMIZE_FILE),
                    'has_text_labels' => $has_text_labels,
                    'display_text_labels' => $this->_displayLabelFields($obj, $labels, $this->_languages, Configuration::get('PS_LANG_DEFAULT'), Product::CUSTOMIZE_TEXTFIELD),
                    'uploadable_files' => (int)($this->getFieldValue($obj, 'uploadable_files') ? (int)$this->getFieldValue($obj, 'uploadable_files') : '0'),
                    'text_fields' => (int)($this->getFieldValue($obj, 'text_fields') ? (int)$this->getFieldValue($obj, 'text_fields') : '0'),
                ]);
            } else {
                $this->displayWarning($this->l('You must save the product in this shop before adding customization.'));
            }
        } else {
            $this->displayWarning($this->l('You must save this product before adding customization.'));
        }
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormAttachments($obj)
    {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }
        $data = $this->createTemplate($this->tpl_form);
        $data->assign('default_form_language', $this->default_form_language);
        if ((bool)$obj->id) {
            if ($this->product_exists_in_shop) {
                $attachment_name = [];
                $attachment_description = [];
                foreach ($this->_languages as $language) {
                    $attachment_name[$language['id_lang']] = '';
                    $attachment_description[$language['id_lang']] = '';
                }
                $iso_tiny_mce = $this->context->language->iso_code;
                $iso_tiny_mce = (file_exists(_PS_JS_DIR_.'tiny_mce/langs/'.$iso_tiny_mce.'.js') ? $iso_tiny_mce : 'en');
                $attachment_uploader = new HelperUploader('attachment_file');
                $attachment_uploader->setMultiple(false)->setUseAjax(true)->setUrl(
                    Context::getContext()->link->getAdminLink('AdminProducts').'&ajax=1&id_product='.(int)$obj->id
                    .'&action=AddAttachment')->setPostMaxSize((Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024))
                    ->setTemplate('attachment_ajax.tpl');
                $data->assign([
                    'obj' => $obj,
                    'table' => $this->table,
                    'ad' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_),
                    'iso_tiny_mce' => $iso_tiny_mce,
                    'languages' => $this->_languages,
                    'id_lang' => $this->context->language->id,
                    'attach1' => Attachment::getAttachments($this->context->language->id, $obj->id, true),
                    'attach2' => Attachment::getAttachments($this->context->language->id, $obj->id, false),
                    'default_form_language' => (int)Configuration::get('PS_LANG_DEFAULT'),
                    'attachment_name' => $attachment_name,
                    'attachment_description' => $attachment_description,
                    'attachment_uploader' => $attachment_uploader->render()
                ]);
            } else {
                $this->displayWarning($this->l('You must save the product in this shop before adding attachements.'));
            }
        } else {
            $this->displayWarning($this->l('You must save this product before adding attachements.'));
        }
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormInformations($product)
    {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }
        $data = $this->createTemplate($this->tpl_form);
        $currency = $this->context->currency;
        $data->assign('languages', $this->_languages);
        $data->assign('default_form_language', $this->default_form_language);
        $data->assign('currency', $currency);
        $this->object = $product;
        $data->assign('product_name_redirected', Product::getProductName((int)$product->id_type_redirected, null, (int)$this->context->language->id));

        $product_download = new ProductDownload();
        if ($id_product_download = $product_download->getIdFromIdProduct($this->getFieldValue($product, 'id'))) {
            $product_download = new ProductDownload($id_product_download);
        }
        $product->{'productDownload'} = $product_download;
        $product_props = [];
        array_push($product_props, 'reference', 'ean13', 'upc',
        'available_for_order', 'show_price', 'online_only',
        'id_manufacturer'
        );
        array_push($product_props,
        'width', 'height', 'weight', 'active',
        'is_virtual', 'cache_default_attribute',
        'uploadable_files', 'text_fields'
        );
        array_push($product_props,
            'price', 'wholesale_price', 'id_tax_rules_group', 'unit_price_ratio', 'on_sale',
            'unity', 'minimum_quantity', 'additional_shipping_cost',
            'available_now', 'available_later', 'available_date'
        );
        if (Configuration::get('PS_USE_ECOTAX')) {
            array_push($product_props, 'ecotax');
        }
        foreach ($product_props as $prop) {
            $product->$prop = $this->getFieldValue($product, $prop);
        }
        $product->name['class'] = 'updateCurrentText';
        if (!$product->id || Configuration::get('PS_FORCE_FRIENDLY_PRODUCT')) {
            $product->name['class'] .= ' copy2friendlyUrl';
        }
        $images = Image::getImages($this->context->language->id, $product->id);
        if (is_array($images)) {
            foreach ($images as $k => $image) {
                $images[$k]['src'] = $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id], $product->id.'-'.$image['id_image'], ImageType::getFormattedName('small'));
            }
            $data->assign('images', $images);
        }
        $data->assign('imagesTypes', ImageType::getImagesTypes('products'));
        $product->tags = Tag::getProductTags($product->id);
        $data->assign('product_type', (int)Tools::getValue('type_product', $product->getType()));
        $data->assign('is_in_pack', (int)Pack::isPacked($product->id));
        $check_product_association_ajax = false;
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL) {
            $check_product_association_ajax = true;
        }
        $iso_tiny_mce = $this->context->language->iso_code;
        $iso_tiny_mce = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso_tiny_mce.'.js') ? $iso_tiny_mce : 'en');
        $data->assign('ad', dirname($_SERVER['PHP_SELF']));
        $data->assign('iso_tiny_mce', $iso_tiny_mce);
        $data->assign('check_product_association_ajax', $check_product_association_ajax);
        $data->assign('id_lang', $this->context->language->id);
        $data->assign('product', $product);
        $data->assign('token', $this->token);
        $data->assign('currency', $currency);
        $data->assign($this->tpl_form_vars);
        $data->assign('link', $this->context->link);
        $data->assign('PS_PRODUCT_SHORT_DESC_LIMIT', Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') ? Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') : 400);
        $this->tpl_form_vars['product'] = $product;
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormShipping($obj)
    {
        $data = $this->createTemplate($this->tpl_form);
        $data->assign([
                          'product' => $obj,
                          'ps_dimension_unit' => Configuration::get('PS_DIMENSION_UNIT'),
                          'ps_weight_unit' => Configuration::get('PS_WEIGHT_UNIT'),
                          'carrier_list' => $this->getCarrierList(),
                          'currency' => $this->context->currency,
                          'country_display_tax_label' =>  $this->context->country->display_tax_label
                      ]);
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    protected function getCarrierList()
    {
        $carrier_list = Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS);
        if ($product = $this->loadObject(true)) {
            $carrier_selected_list = $product->getCarriers();
            foreach ($carrier_list as &$carrier) {
                foreach ($carrier_selected_list as $carrier_selected) {
                    if ($carrier_selected['id_reference'] == $carrier['id_reference']) {
                        $carrier['selected'] = true;
                        continue;
                    }
                }
            }
        }
        return $carrier_list;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    protected function addCarriers($product = null)
    {
        if (!isset($product)) {
            $product = new Product((int)Tools::getValue('id_product'));
        }
        if (Validate::isLoadedObject($product)) {
            $carriers = [];
            if (Tools::getValue('selectedCarriers')) {
                $carriers = Tools::getValue('selectedCarriers');
            }
            $product->setCarriers($carriers);
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function ajaxProcessaddProductImage()
    {
        self::$currentIndex = 'index.php?tab=AdminProducts';
        $product = new Product((int)Tools::getValue('id_product'));
        $legends = Tools::getValue('legend');
        if (!is_array($legends)) {
            $legends = (array)$legends;
        }
        if (!Validate::isLoadedObject($product)) {
            $files = [];
            $files[0]['error'] = Tools::displayError('Cannot add image because product creation failed.');
        }
        $image_uploader = new HelperImageUploader('file');
        $image_uploader->setAcceptTypes(['jpeg', 'gif', 'png', 'jpg'])->setMaxSize($this->max_image_size);
        $files = $image_uploader->process();
        foreach ($files as &$file) {
            $image = new Image();
            $image->id_product = (int)($product->id);
            $image->position = Image::getHighestPosition($product->id) + 1;
            foreach ($legends as $key => $legend) {
                if (!empty($legend)) {
                    $image->legend[(int)$key] = $legend;
                }
            }
            if (!Image::getCover($image->id_product)) {
                $image->cover = 1;
            } else {
                $image->cover = 0;
            }
            if (($validate = $image->validateFieldsLang(false, true)) !== true) {
                $file['error'] = Tools::displayError($validate);
            }
            if (isset($file['error']) && (!is_numeric($file['error']) || $file['error'] != 0)) {
                continue;
            }
            if (!$image->add()) {
                $file['error'] = Tools::displayError('Error while creating additional image');
            } else {
                if (!$new_path = $image->getPathForCreation()) {
                    $file['error'] = Tools::displayError('An error occurred during new folder creation');
                    continue;
                }
                $error = 0;
                if (!ImageManager::resize($file['save_path'], $new_path.'.'.$image->image_format, null, null, 'jpg', false, $error)) {
                    switch ($error) {
                        case ImageManager::ERROR_FILE_NOT_EXIST :
                            $file['error'] = Tools::displayError('An error occurred while copying image, the file does not exist anymore.');
                            break;
                        case ImageManager::ERROR_FILE_WIDTH :
                            $file['error'] = Tools::displayError('An error occurred while copying image, the file width is 0px.');
                            break;
                        case ImageManager::ERROR_MEMORY_LIMIT :
                            $file['error'] = Tools::displayError('An error occurred while copying image, check your memory limit.');
                            break;
                        default:
                            $file['error'] = Tools::displayError('An error occurred while copying image.');
                            break;
                    }
                    continue;
                } else {
                    $imagesTypes = ImageType::getImagesTypes('products');
                    foreach ($imagesTypes as $imageType) {
                        if (!ImageManager::resize($file['save_path'], $new_path.'-'.stripslashes($imageType['name']).'.'.$image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
                            $file['error'] = Tools::displayError('An error occurred while copying image:').' '.stripslashes($imageType['name']);
                            continue;
                        }
                    }
                }
                unlink($file['save_path']);
                unset($file['save_path']);
                Hook::exec('actionWatermark', ['id_image' => $image->id, 'id_product' => $product->id]);
                if (!$image->update()) {
                    $file['error'] = Tools::displayError('Error while updating status');
                    continue;
                }
                $shops = Shop::getContextListShopID();
                $image->associateTo($shops);
                $json_shops = [];
                foreach ($shops as $id_shop) {
                    $json_shops[$id_shop] = true;
                }
                $file['status']   = 'ok';
                $file['id']       = $image->id;
                $file['position'] = $image->position;
                $file['cover']    = $image->cover;
                $file['legend']   = $image->legend;
                $file['path']     = $image->getExistingImgPath();
                $file['shops']    = $json_shops;
                @unlink(_PS_TMP_IMG_DIR_.'product_'.(int)$product->id.'.jpg');
                @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$product->id.'_'.$this->context->shop->id.'.jpg');
            }
        }
        die(json_encode([$image_uploader->getName() => $files]));
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormImages($obj)
    {
        $data = $this->createTemplate($this->tpl_form);
        if ((bool)$obj->id) {
            if ($this->product_exists_in_shop) {
                $data->assign('product', $this->loadObject());
                $shops = false;
                if (Shop::isFeatureActive()) {
                    $shops = Shop::getShops();
                }
                if ($shops) {
                    foreach ($shops as $key => $shop) {
                        if (!$obj->isAssociatedToShop($shop['id_shop'])) {
                            unset($shops[$key]);
                        }
                    }
                }
                $data->assign('shops', $shops);
                $count_images = Db::getInstance()->getValue('
					SELECT COUNT(id_product)
					FROM '._DB_PREFIX_.'image
					WHERE id_product = '.(int)$obj->id
                );
                $images = Image::getImages($this->context->language->id, $obj->id);
                foreach ($images as $k => $image) {
                    $images[$k] = new Image($image['id_image']);
                }
                if ($this->context->shop->getContext() == Shop::CONTEXT_SHOP) {
                    $current_shop_id = (int)$this->context->shop->id;
                } else {
                    $current_shop_id = 0;
                }
                $languages = Language::getLanguages(true);
                $image_uploader = new HelperImageUploader('file');
                $image_uploader->setMultiple(!(Tools::getUserBrowser() == 'Apple Safari' && Tools::getUserPlatform() == 'Windows'))
                    ->setUseAjax(true)->setUrl(
                    Context::getContext()->link->getAdminLink('AdminProducts').'&ajax=1&id_product='.(int)$obj->id
                    .'&action=addProductImage');
                $data->assign([
                        'countImages' => $count_images,
                        'id_product' => (int)Tools::getValue('id_product'),
                        'id_category_default' => (int)$this->_category->id,
                        'images' => $images,
                        'iso_lang' => $languages[0]['iso_code'],
                        'token' =>  $this->token,
                        'table' => $this->table,
                        'max_image_size' => $this->max_image_size / 1024 / 1024,
                        'up_filename' => (string)Tools::getValue('virtual_product_filename_attribute'),
                        'currency' => $this->context->currency,
                        'current_shop_id' => $current_shop_id,
                        'languages' => $this->_languages,
                        'default_language' => (int)Configuration::get('PS_LANG_DEFAULT'),
                        'image_uploader' => $image_uploader->render()
                ]);
                $type = ImageType::getByNameNType('%', 'products', 'height');
                if (isset($type['name'])) {
                    $data->assign('imageType', $type['name']);
                } else {
                    $data->assign('imageType', ImageType::getFormattedName('small'));
                }
            } else {
                $this->displayWarning($this->l('You must save the product in this shop before adding images.'));
            }
        } else {
            $this->displayWarning($this->l('You must save this product before adding images.'));
        }
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormCombinations($obj)
    {
        return $this->initFormAttributes($obj);
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormAttributes($product)
    {
        $data = $this->createTemplate($this->tpl_form);
        if (!Combination::isFeatureActive()) {
            $this->displayWarning($this->l('This feature has been disabled. ').
                ' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
        } elseif (Validate::isLoadedObject($product)) {
            if ($this->product_exists_in_shop) {
                if ($product->is_virtual) {
                    $data->assign('product', $product);
                    $this->displayWarning($this->l('A virtual product cannot have combinations.'));
                } else {
                    $attribute_js = [];
                    $attributes = Attribute::getAttributes($this->context->language->id, true);
                    foreach ($attributes as $k => $attribute) {
                        $attribute_js[$attribute['id_attribute_group']][$attribute['id_attribute']] = $attribute['name'];
                        natsort($attribute_js[$attribute['id_attribute_group']]);
                    }
                    $currency = $this->context->currency;
                    $data->assign('attributeJs', $attribute_js);
                    $data->assign('attributes_groups', AttributeGroup::getAttributesGroups($this->context->language->id));
                    $data->assign('currency', $currency);
                    $images = Image::getImages($this->context->language->id, $product->id);
                    $data->assign('tax_exclude_option', Tax::excludeTaxeOption());
                    $data->assign('ps_weight_unit', Configuration::get('PS_WEIGHT_UNIT'));
                    $data->assign('ps_use_ecotax', Configuration::get('PS_USE_ECOTAX'));
                    $data->assign('field_value_unity', $this->getFieldValue($product, 'unity'));
                    $data->assign('reasons', $reasons = StockMvtReason::getStockMvtReasons($this->context->language->id));
                    $data->assign('ps_stock_mvt_reason_default', $ps_stock_mvt_reason_default = Configuration::get('PS_STOCK_MVT_REASON_DEFAULT'));
                    $data->assign('minimal_quantity', $this->getFieldValue($product, 'minimal_quantity') ? $this->getFieldValue($product, 'minimal_quantity') : 1);
                    $data->assign('available_date', ($this->getFieldValue($product, 'available_date') != 0) ? stripslashes(htmlentities($this->getFieldValue($product, 'available_date'), $this->context->language->id)) : '0000-00-00');
                    $i = 0;
                    $type = ImageType::getByNameNType('%', 'products', 'height');
                    if (isset($type['name'])) {
                        $data->assign('imageType', $type['name']);
                    } else {
                        $data->assign('imageType', ImageType::getFormattedName('small'));
                    }
                    $data->assign('imageWidth', (isset($image_type['width']) ? (int)($image_type['width']) : 64) + 25);
                    foreach ($images as $k => $image) {
                        $images[$k]['obj'] = new Image($image['id_image']);
                        ++$i;
                    }
                    $data->assign('images', $images);
                    $data->assign($this->tpl_form_vars);
                    $data->assign([
                        'list' => $this->renderListAttributes($product, $currency),
                        'product' => $product,
                        'id_category' => $product->getDefaultCategory(),
                        'token_generator' => Tools::getAdminTokenLite('AdminAttributeGenerator'),
                        'combination_exists' => (Shop::isFeatureActive() && (Shop::getContextShopGroup()->share_stock) && count(AttributeGroup::getAttributesGroups($this->context->language->id)) > 0 && $product->hasAttributes())
                    ]);
                }
            } else {
                $this->displayWarning($this->l('You must save the product in this shop before adding combinations.'));
            }
        } else {
            $data->assign('product', $product);
            $this->displayWarning($this->l('You must save this product before adding combinations.'));
        }
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function renderListAttributes($product, $currency)
    {
        $this->bulk_actions = ['delete' => ['text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')]];
        $this->addRowAction('edit');
        $this->addRowAction('default');
        $this->addRowAction('delete');
        $default_class = 'highlighted';
        $this->fields_list = [
            'attributes' => ['title' => $this->l('Attribute - value pair'), 'align' => 'left'],
            'price' => ['title' => $this->l('Impact on price'), 'type' => 'price', 'align' => 'left'],
            'weight' => ['title' => $this->l('Impact on weight'), 'align' => 'left'],
            'reference' => ['title' => $this->l('Reference'), 'align' => 'left'],
            'ean13' => ['title' => $this->l('EAN-13'), 'align' => 'left'],
            'upc' => ['title' => $this->l('UPC'), 'align' => 'left']
        ];
        $comb_array = [];
        if ($product->id) {
            $combinations = $product->getAttributeCombinations($this->context->language->id);
            $groups = [];
            if (is_array($combinations)) {
                $combination_images = $product->getCombinationImages($this->context->language->id);
                foreach ($combinations as $k => $combination) {
                    $price_to_convert = Tools::convertPrice($combination['price'], $currency);
                    $price = Tools::displayPrice($price_to_convert, $currency);
                    $container['id_product_attribute'] = $combination['id_product_attribute'];
                    $container['attributes'][] = [$combination['group_name'], $combination['attribute_name'], $combination['id_attribute']];
                    $container['wholesale_price'] = $combination['wholesale_price'];
                    $container['price'] = $price;
                    $container['weight'] = $combination['weight'].Configuration::get('PS_WEIGHT_UNIT');
                    $container['unit_impact'] = $combination['unit_price_impact'];
                    $container['reference'] = $combination['reference'];
                    $container['ean13'] = $combination['ean13'];
                    $container['upc'] = $combination['upc'];
                    $container['id_image'] = isset($combination_images[$combination['id_product_attribute']][0]['id_image']) ? $combination_images[$combination['id_product_attribute']][0]['id_image'] : 0;
                    $container['available_date'] = strftime($combination['available_date']);
                    $container['default_on'] = $combination['default_on'];
                    if ($combination['is_color_group']) {
                        $groups[$combination['id_attribute_group']] = $combination['group_name'];
                    }
                    $comb_array[$combination['id_product_attribute']] = $container;
                }
            }
            foreach ($comb_array as $id_product_attribute => $product_attribute) {
                $list = '';

                asort($product_attribute['attributes']);
                foreach ($product_attribute['attributes'] as $attribute) {
                    $list .= $attribute[0].' - '.$attribute[1].', ';
                }
                $list = rtrim($list, ', ');
                $comb_array[$id_product_attribute]['image'] = $product_attribute['id_image'] ? new Image($product_attribute['id_image']) : false;
                $comb_array[$id_product_attribute]['available_date'] = $product_attribute['available_date'] != 0 ? date('Y-m-d', strtotime($product_attribute['available_date'])) : '0000-00-00';
                $comb_array[$id_product_attribute]['attributes'] = $list;
                $comb_array[$id_product_attribute]['name'] = $list;
                if ($product_attribute['default_on']) {
                    $comb_array[$id_product_attribute]['class'] = $default_class;
                }
            }
        }
        foreach ($this->actions_available as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }
        $helper = new HelperList();
        $helper->identifier = 'id_product_attribute';
        $helper->table_id = 'combinations-list';
        $helper->token = $this->token;
        $helper->currentIndex = self::$currentIndex;
        $helper->no_link = true;
        $helper->simple_header = true;
        $helper->show_toolbar = false;
        $helper->shopLinkType = $this->shopLinkType;
        $helper->actions = $this->actions;
        $helper->list_skip_actions = $this->list_skip_actions;
        $helper->colorOnBackground = true;
        $helper->override_folder = $this->tpl_folder.'combination/';
        return $helper->generateList($comb_array, $this->fields_list);
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormQuantities($obj)
    {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }
        $data = $this->createTemplate($this->tpl_form);
        $data->assign('default_form_language', $this->default_form_language);
        if ($obj->id) {
            if ($this->product_exists_in_shop) {
                $attributes = $obj->getAttributesResume($this->context->language->id);
                if (empty($attributes)) {
                    $attributes[] = [
                        'id_product_attribute' => 0,
                        'attribute_designation' => ''
                    ];
                }
                $available_quantity = [];
                $product_designation = [];
                foreach ($attributes as $attribute) {
                    $available_quantity[$attribute['id_product_attribute']] = StockAvailable::getQuantityAvailableByProduct((int)$obj->id,
                                                                                                                            $attribute['id_product_attribute']);
                    $product_designation[$attribute['id_product_attribute']] = rtrim(
                        $obj->name[$this->context->language->id].' - '.$attribute['attribute_designation'],
                        ' - '
                    );
                }
                $show_quantities = true;
                $shop_context = Shop::getContext();
                $shop_group = new ShopGroup((int)Shop::getContextShopGroupID());
                if (Shop::isFeatureActive() && $shop_context == Shop::CONTEXT_ALL) {
                    $show_quantities = false;
                } elseif (Shop::isFeatureActive() && $shop_context == Shop::CONTEXT_GROUP) {
                    if (!$shop_group->share_stock) {
                        $show_quantities = false;
                    }
                } else {
                    if ($shop_group->share_stock) {
                        $show_quantities = false;
                    }
                }
                $data->assign('ps_stock_management', Configuration::get('PS_STOCK_MANAGEMENT'));
                $data->assign('has_attribute', $obj->hasAttributes());
                if (Combination::isFeatureActive()) {
                    $data->assign('countAttributes', (int)Db::getInstance()->getValue('SELECT COUNT(id_product) FROM '._DB_PREFIX_.'product_attribute WHERE id_product = '.(int)$obj->id));
                } else {
                    $data->assign('countAttributes', false);
                }
                $advanced_stock_management_warning = false;
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $obj->advanced_stock_management) {
                    $p_attributes = Product::getProductAttributesIds($obj->id);
                    $warehouses = [];
                    if (!$p_attributes) {
                        $warehouses[] = Warehouse::getProductWarehouseList($obj->id, 0);
                    }
                    foreach ($p_attributes as $p_attribute) {
                        $ws = Warehouse::getProductWarehouseList($obj->id, $p_attribute['id_product_attribute']);
                        if ($ws) {
                            $warehouses[] = $ws;
                        }
                    }
                    $warehouses = Tools::arrayUnique($warehouses);
                    if (empty($warehouses)) {
                        $advanced_stock_management_warning = true;
                    }
                }
                if ($advanced_stock_management_warning) {
                    $this->displayWarning($this->l('If you wish to use the advanced stock management, you must:'));
                    $this->displayWarning('- '.$this->l('associate your products with warehouses.'));
                    $this->displayWarning('- '.$this->l('associate your warehouses with carriers.'));
                    $this->displayWarning('- '.$this->l('associate your warehouses with the appropriate shops.'));
                }
                $pack_quantity = null;
                if (Pack::isPack($obj->id)) {
                    $items = Pack::getItems((int)$obj->id, Configuration::get('PS_LANG_DEFAULT'));
                    $pack_quantities = [];
                    foreach ($items as $item) {
                        if (!$item->isAvailableWhenOutOfStock((int)$item->out_of_stock)) {
                            $pack_id_product_attribute = Product::getDefaultAttribute($item->id, 1);
                            $pack_quantities[] = Product::getQuantity($item->id, $pack_id_product_attribute) / ($item->pack_quantity !== 0 ? $item->pack_quantity : 1);
                        }
                    }
                    if (count($pack_quantities)) {
                        $pack_quantity = $pack_quantities[0];
                        foreach ($pack_quantities as $value) {
                            if ($pack_quantity > $value) {
                                $pack_quantity = $value;
                            }
                        }
                    }
                    if (!Warehouse::getPackWarehouses((int)$obj->id)) {
                        $this->displayWarning($this->l('You must have a common warehouse between this pack and its product.'));
                    }
                }
                $data->assign([
                    'attributes' => $attributes,
                    'available_quantity' => $available_quantity,
                    'pack_quantity' => $pack_quantity,
                    'stock_management_active' => Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
                    'product_designation' => $product_designation,
                    'product' => $obj,
                    'show_quantities' => $show_quantities,
                    'order_out_of_stock' => Configuration::get('PS_ORDER_OUT_OF_STOCK'),
                    'pack_stock_type' => Configuration::get('PS_PACK_STOCK_TYPE'),
                    'token_preferences' => Tools::getAdminTokenLite('AdminPPreferences'),
                    'token' => $this->token,
                    'languages' => $this->_languages,
                    'id_lang' => $this->context->language->id
                ]);
            } else {
                $this->displayWarning($this->l('You must save the product in this shop before managing quantities.'));
            }
        } else {
            $this->displayWarning($this->l('You must save this product before managing quantities.'));
        }
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormSuppliers($obj)
    {
        $data = $this->createTemplate($this->tpl_form);
        if ($obj->id) {
            if ($this->product_exists_in_shop) {
                $attributes = $obj->getAttributesResume($this->context->language->id);
                if (empty($attributes)) {
                    $attributes[] = [
                        'id_product' => $obj->id,
                        'id_product_attribute' => 0,
                        'attribute_designation' => ''
                    ];
                }
                $product_designation = [];
                foreach ($attributes as $attribute) {
                    $product_designation[$attribute['id_product_attribute']] = rtrim(
                        $obj->name[$this->context->language->id].' - '.$attribute['attribute_designation'],
                        ' - '
                    );
                }
                $suppliers = Supplier::getSuppliers();
                $associated_suppliers = ProductSupplier::getSupplierCollection($obj->id);
                $product_supplier_collection = ProductSupplier::getSupplierCollection($obj->id, false);
                $default_supplier = 0;
                foreach ($suppliers as &$supplier) {
                    $supplier['is_selected'] = false;
                    $supplier['is_default'] = false;
                    foreach ($associated_suppliers as $associated_supplier) {
                        if ($associated_supplier->id_supplier == $supplier['id_supplier']) {
                            $associated_supplier->name = $supplier['name'];
                            $supplier['is_selected'] = true;
                            if ($obj->id_supplier == $supplier['id_supplier']) {
                                $supplier['is_default'] = true;
                                $default_supplier = $supplier['id_supplier'];
                            }
                        }
                    }
                }
                $data->assign([
                    'attributes' => $attributes,
                    'suppliers' => $suppliers,
                    'default_supplier' => $default_supplier,
                    'associated_suppliers' => $associated_suppliers,
                    'associated_suppliers_collection' => $product_supplier_collection,
                    'product_designation' => $product_designation,
                    'currencies' => Currency::getCurrencies(),
                    'product' => $obj,
                    'link' => $this->context->link,
                    'token' => $this->token,
                    'id_default_currency' => Configuration::get('PS_CURRENCY_DEFAULT'),
                ]);
            } else {
                $this->displayWarning($this->l('You must save the product in this shop before managing suppliers.'));
            }
        } else {
            $this->displayWarning($this->l('You must save this product before managing suppliers.'));
        }
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormWarehouses($obj)
    {
        $data = $this->createTemplate($this->tpl_form);
        if ($obj->id) {
            if ($this->product_exists_in_shop) {
                $attributes = $obj->getAttributesResume($this->context->language->id);
                if (empty($attributes)) {
                    $attributes[] = [
                        'id_product' => $obj->id,
                        'id_product_attribute' => 0,
                        'attribute_designation' => ''
                    ];
                }
                $product_designation = [];
                foreach ($attributes as $attribute) {
                    $product_designation[$attribute['id_product_attribute']] = rtrim(
                        $obj->name[$this->context->language->id].' - '.$attribute['attribute_designation'],
                        ' - '
                    );
                }
                $warehouses = Warehouse::getWarehouses(true);
                $associated_warehouses_collection = WarehouseProductLocation::getCollection($obj->id);
                $data->assign([
                    'attributes' => $attributes,
                    'warehouses' => $warehouses,
                    'associated_warehouses' => $associated_warehouses_collection,
                    'product_designation' => $product_designation,
                    'product' => $obj,
                    'link' => $this->context->link,
                    'token' => $this->token
                ]);
            } else {
                $this->displayWarning($this->l('You must save the product in this shop before managing warehouses.'));
            }
        } else {
            $this->displayWarning($this->l('You must save this product before managing warehouses.'));
        }
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormFeatures($obj)
    {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }
        $data = $this->createTemplate($this->tpl_form);
        $data->assign('default_form_language', $this->default_form_language);
        $data->assign('languages', $this->_languages);
        if (!Feature::isFeatureActive()) {
            $this->displayWarning($this->l('This feature has been disabled. ').' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
        } else {
            if ($obj->id) {
                if ($this->product_exists_in_shop) {
                    $features = Feature::getFeatures($this->context->language->id, (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP));
                    foreach ($features as $k => $tab_features) {
                        $features[$k]['current_item'] = false;
                        $features[$k]['val'] = [];
                        $custom = true;
                        foreach ($obj->getFeatures() as $tab_products) {
                            if ($tab_products['id_feature'] == $tab_features['id_feature']) {
                                $features[$k]['current_item'] = $tab_products['id_feature_value'];
                            }
                        }
                        $features[$k]['featureValues'] = FeatureValue::getFeatureValuesWithLang($this->context->language->id, (int)$tab_features['id_feature']);
                        if (count($features[$k]['featureValues'])) {
                            foreach ($features[$k]['featureValues'] as $value) {
                                if ($features[$k]['current_item'] == $value['id_feature_value']) {
                                    $custom = false;
                                }
                            }
                        }
                        if ($custom) {
                            $features[$k]['val'] = FeatureValue::getFeatureValueLang($features[$k]['current_item']);
                        }
                    }
                    $data->assign('available_features', $features);
                    $data->assign('product', $obj);
                    $data->assign('link', $this->context->link);
                    $data->assign('default_form_language', $this->default_form_language);
                } else {
                    $this->displayWarning($this->l('You must save the product in this shop before adding features.'));
                }
            } else {
                $this->displayWarning($this->l('You must save this product before adding features.'));
            }
        }
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function ajaxProcessProductQuantity()
    {
        if (!Tools::getValue('actionQty')) {
            return json_encode(['error' => $this->l('Undefined action')]);
        }
        $product = new Product((int)Tools::getValue('id_product'), true);
        switch (Tools::getValue('actionQty')) {
            case 'depends_on_stock':
                if (Tools::getValue('value') === false) {
                    die(json_encode(['error' =>  $this->l('Undefined value')]));
                }
                if ((int)Tools::getValue('value') != 0 && (int)Tools::getValue('value') != 1) {
                    die(json_encode(['error' =>  $this->l('Incorrect value')]));
                }
                if (!$product->advanced_stock_management && (int)Tools::getValue('value') == 1) {
                    die(json_encode(['error' =>  $this->l('Not possible if advanced stock management is disabled. ')]));
                }
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
                    && (int)Tools::getValue('value') == 1
                    && (Pack::isPack($product->id)
                        && !Pack::allUsesAdvancedStockManagement($product->id)
                        && ($product->pack_stock_type == Pack::STOCK_TYPE_PACK_BOTH
                            || $product->pack_stock_type == Pack::STOCK_TYPE_PRODUCTS_ONLY
                            || ($product->pack_stock_type == Pack::STOCK_TYPE_DEFAULT
                                && (Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PRODUCTS_ONLY
                                    || Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PACK_BOTH)
                            )
                        )
                    )
                ) {
                    die(json_encode(['error' => $this->l('You cannot use advanced stock management for this pack because').'</br>'.
                        $this->l('- advanced stock management is not enabled for these products').'</br>'.
                        $this->l('- you have chosen to decrement products quantities.')]));
                }
                StockAvailable::setProductDependsOnStock($product->id, (int)Tools::getValue('value'));
                break;
            case 'pack_stock_type':
                $value = Tools::getValue('value');
                if ($value === false) {
                    die(json_encode(['error' =>  $this->l('Undefined value')]));
                }
                if ((int)$value != 0 && (int)$value != 1
                    && (int)$value != 2 && (int)$value != 3) {
                    die(json_encode(['error' =>  $this->l('Incorrect value')]));
                }
                if ($product->depends_on_stock
                    && !Pack::allUsesAdvancedStockManagement($product->id)
                    && ((int)$value == 1
                        || (int)$value == 2
                        || ((int)$value == 3
                            && (Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PRODUCTS_ONLY
                                || Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PACK_BOTH)
                        )
                    )
                ) {
                    die(json_encode(['error' => $this->l('You cannot use this stock management option because:').'</br>'.
                        $this->l('- advanced stock management is not enabled for these products').'</br>'.
                        $this->l('- advanced stock management is enabled for the pack')]));
                }
                Product::setPackStockType($product->id, $value);
                break;
            case 'out_of_stock':
                if (Tools::getValue('value') === false) {
                    die(json_encode(['error' =>  $this->l('Undefined value')]));
                }
                if (!in_array((int)Tools::getValue('value'), [0, 1, 2])) {
                    die(json_encode(['error' =>  $this->l('Incorrect value')]));
                }
                StockAvailable::setProductOutOfStock($product->id, (int)Tools::getValue('value'));
                break;
            case 'set_qty':
                if (Tools::getValue('value') === false || (!is_numeric(trim(Tools::getValue('value'))))) {
                    die(json_encode(['error' =>  $this->l('Undefined value')]));
                }
                if (Tools::getValue('id_product_attribute') === false) {
                    die(json_encode(['error' =>  $this->l('Undefined id product attribute')]));
                }
                StockAvailable::setQuantity($product->id, (int)Tools::getValue('id_product_attribute'), (int)Tools::getValue('value'));
                Hook::exec('actionProductUpdate', ['id_product' => (int)$product->id, 'product' => $product]);
                $error = ob_get_contents();
                if (!empty($error)) {
                    ob_end_clean();
                    die(json_encode(['error' => $error]));
                }
                break;
            case 'advanced_stock_management' :
                if (Tools::getValue('value') === false) {
                    die(json_encode(['error' =>  $this->l('Undefined value')]));
                }
                if ((int)Tools::getValue('value') != 1 && (int)Tools::getValue('value') != 0) {
                    die(json_encode(['error' =>  $this->l('Incorrect value')]));
                }
                if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)Tools::getValue('value') == 1) {
                    die(json_encode(['error' =>  $this->l('Not possible if advanced stock management is disabled. ')]));
                }
                $product->setAdvancedStockManagement((int)Tools::getValue('value'));
                if (StockAvailable::dependsOnStock($product->id) == 1 && (int)Tools::getValue('value') == 0) {
                    StockAvailable::setProductDependsOnStock($product->id, 0);
                }
                break;
        }
        die(json_encode(['error' => false]));
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function getCombinationImagesJS()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        $content = 'var combination_images = new Array();';
        if (!$allCombinationImages = $obj->getCombinationImages($this->context->language->id)) {
            return $content;
        }
        foreach ($allCombinationImages as $id_product_attribute => $combination_images) {
            $i = 0;
            $content .= 'combination_images['.(int)$id_product_attribute.'] = new Array();';
            foreach ($combination_images as $combination_image) {
                $content .= 'combination_images['.(int)$id_product_attribute.']['.$i++.'] = '.(int)$combination_image['id_image'].';';
            }
        }
        return $content;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function haveThisAccessory($accessory_id, $accessories)
    {
        foreach ($accessories as $accessory) {
            if ((int)$accessory['id_product'] == (int)$accessory_id) {
                return true;
            }
        }
        return false;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    protected function initPack(Product $product)
    {
        $this->tpl_form_vars['is_pack'] = ($product->id && Pack::isPack($product->id)) || Tools::getValue('type_product') == Product::PTYPE_PACK;
        $product->packItems = Pack::getItems($product->id, $this->context->language->id);
        $input_pack_items = '';
        if (Tools::getValue('inputPackItems')) {
            $input_pack_items = Tools::getValue('inputPackItems');
        } else {
            foreach ($product->packItems as $pack_item) {
                $input_pack_items .= $pack_item->pack_quantity.'x'.$pack_item->id.'-';
            }
        }
        $this->tpl_form_vars['input_pack_items'] = $input_pack_items;
        $input_namepack_items = '';
        if (Tools::getValue('namePackItems')) {
            $input_namepack_items = Tools::getValue('namePackItems');
        } else {
            foreach ($product->packItems as $pack_item) {
                $input_namepack_items .= $pack_item->pack_quantity.' x '.$pack_item->name.'¤';
            }
        }
        $this->tpl_form_vars['input_namepack_items'] = $input_namepack_items;
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function initFormModules($obj)
    {
        $id_module = Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name` = \''.pSQL($this->tab_display_module).'\'');
        $this->tpl_form_vars['custom_form'] = Hook::exec('displayAdminProductsExtra', [], (int)$id_module);
    }

    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function updatePackItems($product)
    {
        Pack::deleteItems($product->id);
        if (Tools::getValue('type_product') == Product::PTYPE_PACK) {
            $product->setDefaultAttribute(0);//reset cache_default_attribute
            $items = Tools::getValue('inputPackItems');
            $lines = array_unique(explode('-', $items));
            if (count($lines)) {
                foreach ($lines as $line) {
                    if (!empty($line)) {
                        $item_id_attribute = 0;
                        count($array = explode('x', $line)) == 3 ? list($qty, $item_id, $item_id_attribute) = $array : list($qty, $item_id) = $array;
                        if ($qty > 0 && isset($item_id)) {
                            if (Pack::isPack((int)$item_id)) {
                                $this->errors[] = Tools::displayError('You can\'t add product packs into a pack');
                            } elseif (!Pack::addItem((int)$product->id, (int)$item_id, (int)$qty, (int)$item_id_attribute)) {
                                $this->errors[] = Tools::displayError('An error occurred while attempting to add products to the pack.');
                            }
                        }
                    }
                }
            }
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function getL($key)
    {
        $trad = [
            'Default category:' => $this->l('Default category'),
            'Catalog:' => $this->l('Catalog'),
            'Consider changing the default category.' => $this->l('Consider changing the default category.'),
            'ID' => $this->l('ID'),
            'Name' => $this->l('Name'),
            'Mark all checkbox(es) of categories in which product is to appear' => $this->l('Mark the checkbox of each categories in which this product will appear.')
        ];
        return $trad[$key];
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    protected function _displayUnavailableProductWarning()
    {
        $content = '<div class="alert">
			<span>'.$this->l('Your product will be saved as a draft.').'</span>
				<a href="#" class="btn btn-default pull-right" onclick="submitAddProductAndPreview()" ><i class="icon-external-link-sign"></i> '.$this->l('Save and preview').'</a>
				<input type="hidden" name="fakeSubmitAddProductAndPreview" id="fakeSubmitAddProductAndPreview" />
			</div>';
        $this->tpl_form_vars['warning_unavailable_product'] = $content;
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function ajaxProcessCheckProductName()
    {
        if ($this->access('view')) {
            $search = Tools::getValue('q');
            $id_lang = Tools::getValue('id_lang');
            $limit = Tools::getValue('limit');
            if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP) {
                $result = false;
            } else {
                $result = Db::getInstance()->executeS('
					SELECT DISTINCT pl.`name`, p.`id_product`, pl.`id_shop`
					FROM `'._DB_PREFIX_.'product` p
					LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop ='.(int)Context::getContext()->shop->id.')
					LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
						ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int)$id_lang.')
					WHERE pl.`name` LIKE "%'.pSQL($search).'%" AND ps.id_product IS NULL
					GROUP BY pl.`id_product`
					LIMIT '.(int)$limit);
            }
            die(json_encode($result));
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function ajaxProcessUpdatePositions()
    {
        if ($this->access('edit')) {
            $way = (int)(Tools::getValue('way'));
            $id_product = (int)(Tools::getValue('id_product'));
            $id_category = (int)(Tools::getValue('id_category'));
            $positions = Tools::getValue('product');
            if (is_array($positions)) {
                foreach ($positions as $position => $value) {
                    $pos = explode('_', $value);
                    if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_category && (int)$pos[2] === $id_product)) {
                        if ($product = new Product((int)$pos[2])) {
                            if (isset($position) && $product->updatePosition($way, $position)) {
                                $category = new Category((int)$id_category);
                                if (Validate::isLoadedObject($category)) {
                                    hook::Exec('categoryUpdate', ['category' => $category]);
                                }
                                echo 'ok position '.(int)$position.' for product '.(int)$pos[2]."\r\n";
                            } else {
                                echo '{"hasError" : true, "errors" : "Can not update product '.(int)$id_product.' to position '.(int)$position.' "}';
                            }
                        } else {
                            echo '{"hasError" : true, "errors" : "This product ('.(int)$id_product.') can t be loaded"}';
                        }
                        break;
                    }
                }
            }
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function ajaxProcessPublishProduct()
    {
        if ($this->access('edit')) {
            if ($id_product = (int)Tools::getValue('id_product')) {
                $bo_product_url = dirname($_SERVER['PHP_SELF']).'/index.php?tab=AdminProducts&id_product='.$id_product.'&updateproduct&token='.$this->token;
                if (Tools::getValue('redirect')) {
                    die($bo_product_url);
                }
                $product = new Product((int)$id_product);
                if (!Validate::isLoadedObject($product)) {
                    die('error: invalid id');
                }
                $product->active = 1;
                if ($product->save()) {
                    die($bo_product_url);
                } else {
                    die('error: saving');
                }
            }
        }
    }
    /*
    * module: pscsx32412
    * date: 2015-07-13 16:05:33
    * version: 1
    */
    public function processImageLegends()
    {
        if (Tools::getValue('key_tab') == 'Images' && Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
            $language_ids = Language::getIDs(false);
            foreach ($_POST as $key => $val) {
                if (preg_match('/^legend_([0-9]+)/i', $key, $match)) {
                    foreach ($language_ids as $id_lang) {
                        if ($val && $id_lang == $match[1]) {
                            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image_lang SET legend = "'.pSQL($val).'" WHERE id_image IN (SELECT id_image FROM '._DB_PREFIX_.'image WHERE id_product = '.(int)$product->id.') AND id_lang = '.(int)$id_lang);
                        }
                    }
                }
            }
        }
    }
}
