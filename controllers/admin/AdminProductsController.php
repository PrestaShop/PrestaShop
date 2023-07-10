<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;

/**
 * @deprecated since 8.1 and will be removed in next major.
 *
 * @property Product|null $object
 */
class AdminProductsControllerCore extends AdminController
{
    /**
     * @var int Max image size for upload
     *          As of 1.5 it is recommended to not set a limit to max image size
     */
    protected $max_file_size = null;
    protected $max_image_size = null;

    protected $_category;
    /**
     * @var string name of the tab to display
     */
    protected $tab_display;
    protected $tab_display_module;

    /**
     * The order in the array decides the order in the list of tab. If an element's value is a number, it will be preloaded.
     * The tabs are preloaded from the smallest to the highest number.
     *
     * @var array product tabs
     */
    protected $available_tabs = [];

    protected $default_tab = 'Informations';

    protected $available_tabs_lang = [];

    /** @var string */
    protected $position_identifier = 'id_product';

    protected $submitted_tabs;

    protected $id_current_category;

    public function __construct($theme_name = 'default')
    {
        $this->bootstrap = true;
        $this->table = 'product';
        $this->className = 'Product';
        parent::__construct('', $theme_name);
    }

    public function init()
    {
        if (Tools::getIsset('id_product')) {
            if (Tools::getIsset('addproduct') || Tools::getIsset('updateproduct')) {
                $sfContainer = SymfonyContainer::getInstance();
                if (null !== $sfContainer) {
                    $sfRouter = $sfContainer->get('router');
                    Tools::redirectAdmin($sfRouter->generate(
                        'admin_product_form',
                        ['id' => Tools::getValue('id_product')]
                    ));
                }
            }
        }

        return parent::init();
    }

    public static function getQuantities($echo, $tr)
    {
        if ((int) $tr['is_virtual'] == 1 && $tr['nb_downloadable'] == 0) {
            return '&infin;';
        } else {
            return $echo;
        }
    }

    protected function _cleanMetaKeywords($keywords)
    {
        if (!empty($keywords) && $keywords != '') {
            $out = [];
            $words = explode(',', $keywords);
            foreach ($words as $word_item) {
                $word_item = trim($word_item);
                if (!empty($word_item)) {
                    $out[] = $word_item;
                }
            }

            return (count($out) > 0) ? implode(',', $out) : '';
        } else {
            return '';
        }
    }

    /**
     * @param Product|ObjectModel $object
     * @param string $table
     */
    protected function copyFromPost(&$object, $table)
    {
        parent::copyFromPost($object, $table);
        if (get_class($object) != 'Product') {
            return;
        }

        /* Additional fields */
        foreach (Language::getIDs(false) as $id_lang) {
            if (isset($_POST['meta_keywords_' . $id_lang])) {
                $_POST['meta_keywords_' . $id_lang] = $this->_cleanMetaKeywords(Tools::strtolower($_POST['meta_keywords_' . $id_lang]));
                $object->meta_keywords[$id_lang] = $_POST['meta_keywords_' . $id_lang];
            }
        }
        $_POST['width'] = empty($_POST['width']) ? '0' : str_replace(',', '.', $_POST['width']);
        $_POST['height'] = empty($_POST['height']) ? '0' : str_replace(',', '.', $_POST['height']);
        $_POST['depth'] = empty($_POST['depth']) ? '0' : str_replace(',', '.', $_POST['depth']);
        $_POST['weight'] = empty($_POST['weight']) ? '0' : str_replace(',', '.', $_POST['weight']);

        if (Tools::getIsset('unit_price') != null) {
            $object->unit_price = (float) str_replace(',', '.', Tools::getValue('unit_price'));
        }
        if (Tools::getIsset('ecotax') != null) {
            $object->ecotax = (float) str_replace(',', '.', Tools::getValue('ecotax'));
        }

        if ($this->isTabSubmitted('Informations')) {
            if ($this->checkMultishopBox('available_for_order', $this->context)) {
                $object->available_for_order = (bool) Tools::getValue('available_for_order');
            }

            if ($this->checkMultishopBox('show_price', $this->context)) {
                $object->show_price = $object->available_for_order || (bool) Tools::getValue('show_price');
            }

            if ($this->checkMultishopBox('online_only', $this->context)) {
                $object->online_only = (bool) Tools::getValue('online_only');
            }

            if ($this->checkMultishopBox('show_condition', $this->context)) {
                $object->show_condition = (bool) Tools::getValue('show_condition');
            }
        }
        if ($this->isTabSubmitted('Prices')) {
            $object->on_sale = (bool) Tools::getValue('on_sale');
        }
    }

    public function checkMultishopBox($field, $context = null)
    {
        static $checkbox = null;
        static $shop_context = null;

        if ($context == null && $shop_context == null) {
            $context = Context::getContext();
        }

        if ($shop_context == null) {
            $shop_context = $context->shop->getContext();
        }

        if ($checkbox == null) {
            $checkbox = Tools::getValue('multishop_check', []);
        }

        if ($shop_context == Shop::CONTEXT_SHOP) {
            return true;
        }

        if (isset($checkbox[$field]) && $checkbox[$field] == 1) {
            return true;
        }

        return false;
    }

    /**
     * @param int $id_lang
     * @param string $orderBy
     * @param string $orderWay
     * @param int $start
     * @param int $limit
     * @param int|null $id_lang_shop
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * @deprecated
     */
    public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = null)
    {
        $orderByPriceFinal = (empty($orderBy) ? ($this->context->cookie->__get($this->table . 'Orderby') ? $this->context->cookie->__get($this->table . 'Orderby') : 'id_' . $this->table) : $orderBy);
        $orderWayPriceFinal = (empty($orderWay) ? ($this->context->cookie->__get($this->table . 'Orderway') ? $this->context->cookie->__get($this->table . 'Orderby') : 'ASC') : $orderWay);
        if ($orderByPriceFinal == 'price_final') {
            $orderBy = 'id_' . $this->table;
            $orderWay = 'ASC';
        }
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $this->context->shop->id);

        /* update product quantity with attributes ...*/
        $nb = count($this->_list);
        if ($this->_list) {
            $context = $this->context->cloneContext();
            $context->shop = clone $context->shop;
            /* update product final price */
            for ($i = 0; $i < $nb; ++$i) {
                if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP) {
                    $context->shop = new Shop((int) $this->_list[$i]['id_shop_default']);
                }

                // convert price with the currency from context
                $this->_list[$i]['price'] = Tools::convertPrice($this->_list[$i]['price'], $this->context->currency, true, $this->context);
                $this->_list[$i]['price_tmp'] = (float) Product::getPriceStatic(
                    $this->_list[$i]['id_product'],
                    true,
                    null,
                    (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION'),
                    null,
                    false,
                    true,
                    1,
                    true,
                    null,
                    null,
                    null,
                    $nothing,
                    true,
                    true,
                    $context
                );
            }
        }

        if ($orderByPriceFinal == 'price_final') {
            if (strtolower($orderWayPriceFinal) == 'desc') {
                uasort($this->_list, 'cmpPriceDesc');
            } else {
                uasort($this->_list, 'cmpPriceAsc');
            }
        }
        for ($i = 0; $this->_list && $i < $nb; ++$i) {
            $this->_list[$i]['price_final'] = $this->_list[$i]['price_tmp'];
            unset($this->_list[$i]['price_tmp']);
        }
    }

    protected function loadObject($opt = false)
    {
        $result = parent::loadObject($opt);
        if ($result && Validate::isLoadedObject($this->object)) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive() && !$this->object->isAssociatedToShop()) {
                $default_product = new Product((int) $this->object->id, false, null, (int) $this->object->id_shop_default);
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

    public function ajaxProcessGetCategoryTree()
    {
        $category = Tools::getValue('category', Category::getRootCategory()->id);
        $full_tree = Tools::getValue('fullTree', 0);
        $use_check_box = Tools::getValue('useCheckBox', 1);
        $selected = Tools::getValue('selected', []);
        $id_tree = Tools::getValue('type');
        $input_name = str_replace(['[', ']'], '', Tools::getValue('inputName', null));

        $tree = new HelperTreeCategories('subtree_associated_categories');
        $tree->setTemplate('subtree_associated_categories.tpl')
            ->setUseCheckBox($use_check_box)
            ->setUseSearch(true)
            ->setIdTree($id_tree)
            ->setSelectedCategories($selected)
            ->setFullTree($full_tree)
            ->setChildrenOnly(true)
            ->setNoJS(true)
            ->setRootCategory($category);

        if ($input_name) {
            $tree->setInputName($input_name);
        }

        die($tree->render());
    }

    public function ajaxProcessGetCountriesOptions()
    {
        if (!$res = Country::getCountriesByIdShop((int) Tools::getValue('id_shop'), (int) $this->context->language->id)) {
            return;
        }

        $tpl = $this->createTemplate('specific_prices_shop_update.tpl');
        $tpl->assign(
            [
                'option_list' => $res,
                'key_id' => 'id_country',
                'key_value' => 'name',
            ]
        );

        $this->content = $tpl->fetch();
    }

    public function ajaxProcessGetCurrenciesOptions()
    {
        if (!$res = Currency::getCurrenciesByIdShop((int) Tools::getValue('id_shop'))) {
            return;
        }

        $tpl = $this->createTemplate('specific_prices_shop_update.tpl');
        $tpl->assign(
            [
                'option_list' => $res,
                'key_id' => 'id_currency',
                'key_value' => 'name',
            ]
        );

        $this->content = $tpl->fetch();
    }

    public function ajaxProcessGetGroupsOptions()
    {
        if (!$res = Group::getGroups((int) $this->context->language->id, (int) Tools::getValue('id_shop'))) {
            return;
        }

        $tpl = $this->createTemplate('specific_prices_shop_update.tpl');
        $tpl->assign(
            [
                'option_list' => $res,
                'key_id' => 'id_group',
                'key_value' => 'name',
            ]
        );

        $this->content = $tpl->fetch();
    }

    public function processDeleteVirtualProduct()
    {
        if (!($id_product_download = ProductDownload::getIdFromIdProduct((int) Tools::getValue('id_product')))) {
            $this->errors[] = $this->trans('Cannot retrieve file.', [], 'Admin.Notifications.Error');
        } else {
            $product_download = new ProductDownload((int) $id_product_download);

            if (!$product_download->deleteFile((int) $id_product_download)) {
                $this->errors[] = $this->trans('Cannot delete file', [], 'Admin.Notifications.Error');
            } else {
                $this->redirect_after = self::$currentIndex . '&id_product=' . (int) Tools::getValue('id_product') . '&updateproduct&key_tab=VirtualProduct&conf=1&token=' . $this->token;
            }
        }

        $this->display = 'edit';
        $this->tab_display = 'VirtualProduct';
    }

    public function ajaxProcessAddAttachment()
    {
        if (!$this->access('edit')) {
            return die(json_encode(['error' => 'You do not have the right permission']));
        }
        if (isset($_FILES['attachment_file'])) {
            if ((int) $_FILES['attachment_file']['error'] === 1) {
                $_FILES['attachment_file']['error'] = [];

                $max_upload = (int) ini_get('upload_max_filesize');
                $max_post = (int) ini_get('post_max_size');
                $upload_mb = min($max_upload, $max_post);
                $_FILES['attachment_file']['error'][] = sprintf(
                    'File %1$s exceeds the size allowed by the server. The limit is set to %2$d MB.',
                    '<b>' . $_FILES['attachment_file']['name'] . '</b> ',
                    '<b>' . $upload_mb . '</b>'
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
                $language = Language::getLanguage((int) $lang);

                if (Tools::strlen($name) > 0) {
                    $is_attachment_name_valid = true;
                }

                if (!Validate::isGenericName($name)) {
                    $_FILES['attachment_file']['error'][] = $this->trans('Invalid name for %s language', [$language['name']], 'Admin.Notifications.Error');
                } elseif (Tools::strlen($name) > 32) {
                    $_FILES['attachment_file']['error'][] = $this->trans('The name for %1s language is too long (%2d chars max).', [$language['name'], 32], 'Admin.Notifications.Error');
                }
            }

            foreach ($attachment_descriptions as $lang => $description) {
                $language = Language::getLanguage((int) $lang);

                if (!Validate::isCleanHtml($description)) {
                    $_FILES['attachment_file']['error'][] = $this->trans('Invalid description for %s language.', [$language['name']], 'Admin.Catalog.Notification');
                }
            }

            if (!$is_attachment_name_valid) {
                $_FILES['attachment_file']['error'][] = $this->trans('An attachment name is required.', [], 'Admin.Catalog.Notification');
            }

            if (empty($_FILES['attachment_file']['error'])) {
                if (is_uploaded_file($_FILES['attachment_file']['tmp_name'])) {
                    if ($_FILES['attachment_file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024)) {
                        $_FILES['attachment_file']['error'][] = sprintf(
                            'The file is too large. Maximum size allowed is: %1$d kB. The file you are trying to upload is %2$d kB.',
                            (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024),
                            number_format(($_FILES['attachment_file']['size'] / 1024), 2, '.', '')
                        );
                    } else {
                        do {
                            $uniqid = sha1(microtime());
                        } while (file_exists(_PS_DOWNLOAD_DIR_ . $uniqid));
                        if (!copy($_FILES['attachment_file']['tmp_name'], _PS_DOWNLOAD_DIR_ . $uniqid)) {
                            $_FILES['attachment_file']['error'][] = 'File copy failed';
                        }
                        @unlink($_FILES['attachment_file']['tmp_name']);
                    }
                } else {
                    $_FILES['attachment_file']['error'][] = $this->trans('The file is missing.', [], 'Admin.Notifications.Error');
                }

                if (empty($_FILES['attachment_file']['error']) && isset($uniqid)) {
                    $attachment = new Attachment();

                    foreach ($attachment_names as $lang => $name) {
                        $attachment->name[(int) $lang] = $name;
                    }

                    foreach ($attachment_descriptions as $lang => $description) {
                        $attachment->description[(int) $lang] = $description;
                    }

                    $attachment->file = $uniqid;
                    $attachment->mime = $_FILES['attachment_file']['type'];
                    $attachment->file_name = $_FILES['attachment_file']['name'];

                    if (empty($attachment->mime) || Tools::strlen($attachment->mime) > 128) {
                        $_FILES['attachment_file']['error'][] = $this->trans('Invalid file extension', [], 'Admin.Notifications.Error');
                    }
                    if (!Validate::isGenericName($attachment->file_name)) {
                        $_FILES['attachment_file']['error'][] = $this->trans('Invalid file name', [], 'Admin.Notifications.Error');
                    }
                    if (Tools::strlen($attachment->file_name) > 128) {
                        $_FILES['attachment_file']['error'][] = $this->trans('The file name is too long.', [], 'Admin.Notifications.Error');
                    }
                    if (empty($this->errors)) {
                        $res = $attachment->add();
                        if (!$res) {
                            $_FILES['attachment_file']['error'][] = $this->trans('This attachment was unable to be loaded into the database.', [], 'Admin.Catalog.Notification');
                        } else {
                            $_FILES['attachment_file']['id_attachment'] = $attachment->id;
                            $_FILES['attachment_file']['filename'] = $attachment->name[$this->context->employee->id_lang];
                            $id_product = (int) Tools::getValue($this->identifier);
                            $res = $attachment->attachProduct($id_product);
                            if (!$res) {
                                $_FILES['attachment_file']['error'][] = $this->trans('We were unable to associate this attachment to a product.', [], 'Admin.Catalog.Notification');
                            }
                        }
                    } else {
                        $_FILES['attachment_file']['error'][] = $this->trans('Invalid file', [], 'Admin.Notifications.Error');
                    }
                }
            }

            die(json_encode($_FILES));
        }
    }

    /**
     * Attach an existing attachment to the product.
     */
    public function processAttachments()
    {
        if ($id = (int) Tools::getValue($this->identifier)) {
            $attachments = trim(Tools::getValue('arrayAttachments'), ',');
            $attachments = explode(',', $attachments);
            if (!Attachment::attachToProduct($id, $attachments)) {
                $this->errors[] = $this->trans('An error occurred while saving product attachments.', [], 'Admin.Catalog.Notification');
            }
        }
    }

    public function processDuplicate()
    {
        if (Validate::isLoadedObject($product = new Product((int) Tools::getValue('id_product')))) {
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
            unset(
                $product->id,
                $product->id_product
            );

            $product->indexed = false;
            $product->active = false;
            if ($product->add()
            && Category::duplicateProductCategories($id_product_old, $product->id)
            && Product::duplicateSuppliers($id_product_old, $product->id)
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
                    $this->errors[] = $this->trans('An error occurred while copying the image.', [], 'Admin.Notifications.Error');
                } else {
                    Hook::exec('actionProductAdd', ['id_product_old' => $id_product_old, 'id_product' => (int) $product->id, 'product' => $product]);
                    if (in_array($product->visibility, ['both', 'search']) && Configuration::get('PS_SEARCH_INDEXATION')) {
                        Search::indexation(false, $product->id);
                    }
                    $this->redirect_after = self::$currentIndex . (Tools::getIsset('id_category') ? '&id_category=' . (int) Tools::getValue('id_category') : '') . '&conf=19&token=' . $this->token;
                }
            } else {
                $this->errors[] = $this->trans('An error occurred while creating an object.', [], 'Admin.Notifications.Error');
            }
        }
    }

    /**
     * @return bool|ObjectModel|void|null
     *
     * @throws PrestaShopException
     */
    public function processDelete()
    {
        $object = $this->loadObject();
        if (Validate::isLoadedObject($object)) {
            /** @var Product $object */
            // check if request at least one object with noZeroObject
            if (isset($object->noZeroObject) && count($taxes = call_user_func([$this->className, $object->noZeroObject])) <= 1) {
                $this->errors[] = $this->trans('You need at least one object.', [], 'Admin.Notifications.Error') . ' <b>' . $this->table . '</b><br />' . $this->trans('You cannot delete all of the items.', [], 'Admin.Notifications.Error');
            } else {
                /*
                 * @since 1.5.0
                 * It is NOT possible to delete a product if there are currently:
                 * - physical stock for this product
                 * - supply order(s) for this product
                 */
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $object->advanced_stock_management) {
                    $stock_manager = StockManagerFactory::getManager();
                    $physical_quantity = $stock_manager->getProductPhysicalQuantities($object->id, 0);
                    $real_quantity = $stock_manager->getProductRealQuantities($object->id, 0);
                    if ($physical_quantity > 0 || $real_quantity > $physical_quantity) {
                        $this->errors[] = $this->trans('You cannot delete this product because there is physical stock left.', [], 'Admin.Catalog.Notification');
                    }
                }

                if (!count($this->errors)) {
                    if ($object->delete()) {
                        $id_category = (int) Tools::getValue('id_category');
                        $category_url = empty($id_category) ? '' : '&id_category=' . (int) $id_category;
                        PrestaShopLogger::addLog(sprintf('%s deletion', $this->className), 1, null, $this->className, (int) $object->id, true, (int) $this->context->employee->id);
                        $this->redirect_after = self::$currentIndex . '&conf=1&token=' . $this->token . $category_url;
                    } else {
                        $this->errors[] = $this->trans('An error occurred during deletion.', [], 'Admin.Notifications.Error');
                    }
                }
            }
        } else {
            $this->errors[] = $this->trans('An error occurred while deleting the object.', [], 'Admin.Notifications.Error') . ' <b>' . $this->table . '</b> ' . $this->trans('(cannot load object)', [], 'Admin.Notifications.Error');
        }
    }

    public function processImage()
    {
        $id_image = (int) Tools::getValue('id_image');
        $image = new Image((int) $id_image);
        if (Validate::isLoadedObject($image)) {
            /* Update product image/legend */
            // @todo : move in processEditProductImage
            if (Tools::getIsset('editImage')) {
                if ($image->cover) {
                    $_POST['cover'] = 1;
                }

                $_POST['id_image'] = $image->id;
            } elseif (Tools::getIsset('coverImage')) {
                /* Choose product cover image */
                Image::deleteCover($image->id_product);
                $image->cover = true;
                if (!$image->update()) {
                    $this->errors[] = $this->trans('You cannot change the product\'s cover image.', [], 'Admin.Catalog.Notification');
                } else {
                    $productId = (int) Tools::getValue('id_product');
                    @unlink(_PS_TMP_IMG_DIR_ . 'product_' . $productId . '.jpg');
                    @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . $productId . '_' . $this->context->shop->id . '.jpg');
                    $this->redirect_after = self::$currentIndex . '&id_product=' . $image->id_product . '&id_category=' . (Tools::getIsset('id_category') ? '&id_category=' . (int) Tools::getValue('id_category') : '') . '&action=Images&addproduct' . '&token=' . $this->token;
                }
            } elseif (Tools::getIsset('imgPosition') && Tools::getIsset('imgDirection')) {
                /* Choose product image position */
                $image->updatePosition(Tools::getValue('imgDirection'), Tools::getValue('imgPosition'));
                $this->redirect_after = self::$currentIndex . '&id_product=' . $image->id_product . '&id_category=' . (Tools::getIsset('id_category') ? '&id_category=' . (int) Tools::getValue('id_category') : '') . '&add' . $this->table . '&action=Images&token=' . $this->token;
            }
        } else {
            $this->errors[] = $this->trans('The image could not be found. ', [], 'Admin.Catalog.Notification');
        }
    }

    /**
     * @return bool|void
     *
     * @throws PrestaShopException
     */
    protected function processBulkDelete()
    {
        if ($this->access('delete')) {
            if (is_array($this->boxes) && !empty($this->boxes)) {
                $object = new $this->className();

                if (isset($object->noZeroObject) &&
                    // Check if all object will be deleted
                    (count(call_user_func([$this->className, $object->noZeroObject])) <= 1 || count($_POST[$this->table . 'Box']) == count(call_user_func([$this->className, $object->noZeroObject])))) {
                    $this->errors[] = $this->trans('You need at least one object.', [], 'Admin.Notifications.Error') . ' <b>' . $this->table . '</b><br />' . $this->trans('You cannot delete all of the items.', [], 'Admin.Notifications.Error');
                } else {
                    $success = 1;
                    $products = Tools::getValue($this->table . 'Box');
                    if (is_array($products) && ($count = count($products))) {
                        // Deleting products can be quite long on a cheap server. Let's say 1.5 seconds by product (I've seen it!).
                        if ((int) (ini_get('max_execution_time')) < round($count * 1.5)) {
                            ini_set('max_execution_time', (string) round($count * 1.5));
                        }

                        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                            $stock_manager = StockManagerFactory::getManager();
                        }

                        foreach ($products as $id_product) {
                            $product = new Product((int) $id_product);
                            /*
                             * @since 1.5.0
                             * It is NOT possible to delete a product if there are currently:
                             * - physical stock for this product
                             * - supply order(s) for this product
                             */
                            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management && isset($stock_manager)) { // @phpstan-ignore-line
                                $physical_quantity = $stock_manager->getProductPhysicalQuantities($product->id, 0);
                                $real_quantity = $stock_manager->getProductRealQuantities($product->id, 0);
                                if ($physical_quantity > 0 || $real_quantity > $physical_quantity) {
                                    $this->errors[] = $this->trans('You cannot delete the product #%d because there is physical stock left.', [$product->id], 'Admin.Catalog.Notification');
                                }
                            }
                            if (!count($this->errors)) {
                                if ($product->delete()) {
                                    PrestaShopLogger::addLog(sprintf('%s deletion', $this->className), 1, null, $this->className, (int) $product->id, true, (int) $this->context->employee->id);
                                } else {
                                    $success = false;
                                }
                            } else {
                                $success = 0;
                            }
                        }
                    }

                    if ($success) {
                        $id_category = (int) Tools::getValue('id_category');
                        $category_url = empty($id_category) ? '' : '&id_category=' . (int) $id_category;
                        $this->redirect_after = self::$currentIndex . '&conf=2&token=' . $this->token . $category_url;
                    } else {
                        $this->errors[] = $this->trans('An error occurred while deleting this selection.', [], 'Admin.Notifications.Error');
                    }
                }
            } else {
                $this->errors[] = $this->trans('You must select at least one element to delete.', [], 'Admin.Notifications.Error');
            }
        } else {
            $this->errors[] = $this->trans('You do not have permission to delete this.', [], 'Admin.Notifications.Error');
        }
    }

    public function processProductAttribute()
    {
        // Don't process if the combination fields have not been submitted
        if (!Combination::isFeatureActive() || !Tools::getValue('attribute_combination_list')) {
            return;
        }

        if (Validate::isLoadedObject($product = $this->object)) {
            if ($this->isProductFieldUpdated('attribute_price') && !Tools::getIsset('attribute_price')) {
                $this->errors[] = $this->trans('The price attribute is required.', [], 'Admin.Catalog.Notification');
            }
            if (!Tools::getIsset('attribute_combination_list') || Tools::isEmpty(Tools::getValue('attribute_combination_list'))) {
                $this->errors[] = $this->trans('You must add at least one attribute.', [], 'Admin.Catalog.Notification');
            }

            $array_checks = [
                'reference' => 'isReference',
                'supplier_reference' => 'isReference',
                'location' => 'isReference',
                'ean13' => 'isEan13',
                'isbn' => 'isIsbn',
                'upc' => 'isUpc',
                'mpn' => 'isMpn',
                'wholesale_price' => 'isPrice',
                'price' => 'isPrice',
                'ecotax' => 'isPrice',
                'quantity' => 'isInt',
                'weight' => 'isUnsignedFloat',
                'unit_price_impact' => 'isPrice',
                'default_on' => 'isBool',
                'minimal_quantity' => 'isUnsignedInt',
                'available_date' => 'isDateFormat',
            ];
            foreach ($array_checks as $property => $check) {
                if (Tools::getValue('attribute_' . $property) !== false && !call_user_func(['Validate', $check], Tools::getValue('attribute_' . $property))) {
                    $this->errors[] = $this->trans('The %s field is not valid', [$property], 'Admin.Notifications.Error');
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

                // Change existing one
                if (($id_product_attribute = (int) Tools::getValue('id_product_attribute')) || ($id_product_attribute = $product->productAttributeExists(Tools::getValue('attribute_combination_list'), false, null, true, true))) {
                    if ($this->access('edit')) {
                        if ($this->isProductFieldUpdated('available_date_attribute') && (Tools::getValue('available_date_attribute') != '' && !Validate::isDateFormat(Tools::getValue('available_date_attribute')))) {
                            $this->errors[] = $this->trans('Invalid date format.', [], 'Admin.Notifications.Error');
                        } else {
                            $product->updateAttribute(
                                (int) $id_product_attribute,
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
                                $this->isProductFieldUpdated('available_date_attribute') ? Tools::getValue('available_date_attribute') : null,
                                false,
                                [],
                                Tools::getValue('attribute_isbn'),
                                Tools::getValue('attribute_low_stock_threshold'),
                                Tools::getValue('attribute_low_stock_alert'),
                                Tools::getValue('attribute_mpn')
                            );
                            StockAvailable::setProductDependsOnStock((int) $product->id, $product->depends_on_stock, null, (int) $id_product_attribute);
                            StockAvailable::setProductOutOfStock((int) $product->id, $product->out_of_stock, null, (int) $id_product_attribute);
                        }
                    } else {
                        $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
                    }
                } else {
                    // Add new
                    if ($this->access('add')) {
                        if ($product->productAttributeExists(Tools::getValue('attribute_combination_list'))) {
                            $this->errors[] = $this->trans('This combination already exists.', [], 'Admin.Catalog.Notification');
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
                                0,
                                Tools::getValue('attribute_ean13'),
                                Tools::getValue('attribute_default'),
                                Tools::getValue('attribute_location'),
                                Tools::getValue('attribute_upc'),
                                Tools::getValue('attribute_minimal_quantity'),
                                [],
                                Tools::getValue('available_date_attribute'),
                                Tools::getValue('attribute_isbn'),
                                Tools::getValue('attribute_low_stock_threshold'),
                                Tools::getValue('attribute_low_stock_alert'),
                                Tools::getValue('attribute_mpn')
                            );
                            StockAvailable::setProductDependsOnStock((int) $product->id, $product->depends_on_stock, null, (int) $id_product_attribute);
                            StockAvailable::setProductOutOfStock((int) $product->id, $product->out_of_stock, null, (int) $id_product_attribute);
                        }
                    } else {
                        $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
                    }
                }
                if (!count($this->errors)) {
                    $combination = new Combination((int) $id_product_attribute);
                    $combination->setAttributes(Tools::getValue('attribute_combination_list'));

                    // images could be deleted before
                    $id_images = Tools::getValue('id_image_attr');
                    if (!empty($id_images)) {
                        $combination->setImages($id_images);
                    }

                    $product->checkDefaultAttributes();
                    if (Tools::getValue('attribute_default')) {
                        Product::updateDefaultAttribute((int) $product->id);
                        $product->cache_default_attribute = (int) $id_product_attribute;

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

    public function processFeatures($id_product = null)
    {
        if (!Feature::isFeatureActive()) {
            return;
        }

        $id_product = (int) $id_product ? $id_product : (int) Tools::getValue('id_product');

        if (Validate::isLoadedObject($product = new Product($id_product))) {
            // delete all objects
            $product->deleteFeatures();

            // add new objects
            $languages = Language::getLanguages(false);
            $form = Tools::getValue('form', false);
            if (false !== $form) {
                $features = isset($form['step1']['features']) ? $form['step1']['features'] : [];
                if (is_array($features)) {
                    foreach ($features as $feature) {
                        if (!empty($feature['value'])) {
                            $product->addFeaturesToDB($feature['feature'], $feature['value']);
                        } elseif ($defaultValue = $this->checkFeatures($languages, $feature)) {
                            $idValue = $product->addFeaturesToDB($feature['feature'], 0, 1);
                            foreach ($languages as $language) {
                                $valueToAdd = (isset($feature['custom_value'][$language['id_lang']]))
                                    ? $feature['custom_value'][$language['id_lang']]
                                    : $defaultValue;

                                $product->addFeaturesCustomToDB($idValue, (int) $language['id_lang'], $valueToAdd);
                            }
                        }
                    }
                }
            }
        } else {
            $this->errors[] = $this->trans('A product must be created before adding features.', [], 'Admin.Catalog.Notification');
        }
    }

    /**
     * This function is never called at the moment (specific prices cannot be edited).
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
            if ($reduction_types[$key] == 'percentage' && ((float) $reductions[$key] <= 0 || (float) $reductions[$key] > 100)) {
                $this->errors[] = $this->trans('The submitted reduction value (0-100) is out-of-range.', [], 'Admin.Catalog.Notification');
            } elseif ($this->_validateSpecificPrice($id_shops[$key], $id_currencies[$key], $id_countries[$key], $id_groups[$key], $id_customers[$key], $prices[$key], $from_quantities[$key], $reductions[$key], $reduction_types[$key], $froms[$key], $tos[$key], $id_combinations[$key])) {
                $specific_price = new SpecificPrice((int) ($id_specific_price));
                $specific_price->id_shop = (int) $id_shops[$key];
                $specific_price->id_product_attribute = (int) $id_combinations[$key];
                $specific_price->id_currency = (int) ($id_currencies[$key]);
                $specific_price->id_country = (int) ($id_countries[$key]);
                $specific_price->id_group = (int) ($id_groups[$key]);
                $specific_price->id_customer = (int) $id_customers[$key];
                $specific_price->price = (float) ($prices[$key]);
                $specific_price->from_quantity = (int) ($from_quantities[$key]);
                $specific_price->reduction = (float) ($reduction_types[$key] == 'percentage' ? ($reductions[$key] / 100) : $reductions[$key]);
                $specific_price->reduction_type = !$reductions[$key] ? 'amount' : $reduction_types[$key];
                $specific_price->from = !$froms[$key] ? '0000-00-00 00:00:00' : $froms[$key];
                $specific_price->to = !$tos[$key] ? '0000-00-00 00:00:00' : $tos[$key];
                if (!$specific_price->update()) {
                    $this->errors[] = $this->trans('An error occurred while updating the specific price.', [], 'Admin.Catalog.Notification');
                }
            }
        }
        if (!count($this->errors)) {
            $this->redirect_after = self::$currentIndex . '&id_product=' . (int) (Tools::getValue('id_product')) . (Tools::getIsset('id_category') ? '&id_category=' . (int) Tools::getValue('id_category') : '') . '&update' . $this->table . '&action=Prices&token=' . $this->token;
        }
    }

    public function processPriceAddition()
    {
        // Check if a specific price has been submitted
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
        $reduction = (float) (Tools::getValue('sp_reduction'));
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

        if (($price == '-1') && ((float) $reduction == '0')) {
            $this->errors[] = $this->trans('No reduction value has been submitted.', [], 'Admin.Catalog.Notification');
        } elseif ($to != '0000-00-00 00:00:00' && strtotime($to) < strtotime($from)) {
            $this->errors[] = $this->trans('Invalid date range', [], 'Admin.Notifications.Error');
        } elseif ($reduction_type == 'percentage' && ((float) $reduction <= 0 || (float) $reduction > 100)) {
            $this->errors[] = $this->trans('The submitted reduction value (0-100) is out-of-range.', [], 'Admin.Catalog.Notification');
        } elseif ($this->_validateSpecificPrice($id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_product_attribute)) {
            $specificPrice = new SpecificPrice();
            $specificPrice->id_product = (int) $id_product;
            $specificPrice->id_product_attribute = (int) $id_product_attribute;
            $specificPrice->id_shop = (int) $id_shop;
            $specificPrice->id_currency = (int) ($id_currency);
            $specificPrice->id_country = (int) ($id_country);
            $specificPrice->id_group = (int) ($id_group);
            $specificPrice->id_customer = (int) $id_customer;
            $specificPrice->price = (float) ($price);
            $specificPrice->from_quantity = (int) ($from_quantity);
            $specificPrice->reduction = (float) ($reduction_type == 'percentage' ? $reduction / 100 : $reduction);
            $specificPrice->reduction_tax = $reduction_tax;
            $specificPrice->reduction_type = $reduction_type;
            $specificPrice->from = $from;
            $specificPrice->to = $to;
            if (!$specificPrice->add()) {
                $this->errors[] = $this->trans('An error occurred while updating the specific price.', [], 'Admin.Catalog.Notification');
            }
        }
    }

    public function ajaxProcessDeleteSpecificPrice()
    {
        if ($this->access('delete')) {
            $id_specific_price = (int) Tools::getValue('id_specific_price');
            if (!$id_specific_price || !Validate::isUnsignedId($id_specific_price)) {
                $error = $this->trans('The specific price ID is invalid.', [], 'Admin.Catalog.Notification');
            } else {
                $specificPrice = new SpecificPrice((int) $id_specific_price);
                if (!$specificPrice->delete()) {
                    $error = $this->trans('An error occurred while attempting to delete the specific price.', [], 'Admin.Catalog.Notification');
                }
            }
        } else {
            $error = $this->trans('You do not have permission to delete this.', [], 'Admin.Notifications.Error');
        }

        if (isset($error)) {
            $json = [
                'status' => 'error',
                'message' => $error,
            ];
        } else {
            $json = [
                'status' => 'ok',
                'message' => $this->_conf[1],
            ];
        }

        die(json_encode($json));
    }

    public function processSpecificPricePriorities()
    {
        if (!($obj = $this->loadObject())) {
            return;
        }
        if (!$priorities = Tools::getValue('specificPricePriority')) {
            $this->errors[] = $this->trans('Please specify priorities.', [], 'Admin.Catalog.Notification');
        } elseif (Tools::isSubmit('specificPricePriorityToAll') && Tools::getValue('specificPricePriorityToAll')) {
            if (!SpecificPrice::setPriorities($priorities)) {
                $this->errors[] = $this->trans('An error occurred while updating priorities.', [], 'Admin.Catalog.Notification');
            } else {
                $this->confirmations[] = 'The price rule has successfully updated';
            }
        } elseif (!SpecificPrice::setSpecificPriority((int) $obj->id, $priorities)) {
            $this->errors[] = $this->trans('An error occurred while setting priorities.', [], 'Admin.Catalog.Notification');
        }
    }

    public function processCustomizationConfiguration()
    {
        $product = $this->object;
        // Get the number of existing customization fields ($product->text_fields is the updated value, not the existing value)
        $current_customization = $product->getCustomizationFieldIds();
        $files_count = 0;
        $text_count = 0;
        if (is_array($current_customization)) {
            foreach ($current_customization as $field) {
                if ($field['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                    ++$text_count;
                } else {
                    ++$files_count;
                }
            }
        }

        if (!$product->createLabels((int) $product->uploadable_files - $files_count, (int) $product->text_fields - $text_count)) {
            $this->errors[] = $this->trans('An error occurred while creating customization fields.', [], 'Admin.Catalog.Notification');
        }
        if (!count($this->errors) && !$product->updateLabels()) {
            $this->errors[] = $this->trans('An error occurred while updating customization fields.', [], 'Admin.Catalog.Notification');
        }
        $product->customizable = ($product->uploadable_files > 0 || $product->text_fields > 0) ? 1 : 0;
        if (($product->uploadable_files != $files_count || $product->text_fields != $text_count) && !count($this->errors) && !$product->update()) {
            $this->errors[] = $this->trans('An error occurred while updating the custom configuration.', [], 'Admin.Catalog.Notification');
        }
    }

    public function processProductCustomization()
    {
        if (Validate::isLoadedObject($product = new Product((int) Tools::getValue('id_product')))) {
            foreach ($_POST as $field => $value) {
                if (strncmp($field, 'label_', 6) == 0 && !Validate::isLabel($value)) {
                    $this->errors[] = $this->trans('The label fields defined are invalid.', [], 'Admin.Catalog.Notification');
                }
            }
            if (empty($this->errors) && !$product->updateLabels()) {
                $this->errors[] = $this->trans('An error occurred while updating customization fields.', [], 'Admin.Catalog.Notification');
            }
            if (empty($this->errors)) {
                $this->confirmations[] = 'Update successful';
            }
        } else {
            $this->errors[] = $this->trans('A product must be created before adding customization.', [], 'Admin.Catalog.Notification');
        }
    }

    /**
     * Overrides parent for custom redirect link.
     *
     * @return bool|ObjectModel|void|null
     *
     * @throws PrestaShopException
     */
    public function processPosition()
    {
        $object = $this->loadObject();
        /** @var Product $object */
        if (!Validate::isLoadedObject($object)) {
            $this->errors[] = $this->trans('An error occurred while updating the status for an object.', [], 'Admin.Notifications.Error') .
                ' <b>' . $this->table . '</b> ' . $this->trans('(cannot load object)', [], 'Admin.Notifications.Error');
        } elseif (!$object->updatePosition((bool) Tools::getValue('way'), (int) Tools::getValue('position'))) {
            $this->errors[] = $this->trans('Failed to update the position.', [], 'Admin.Notifications.Error');
        } else {
            $category = new Category((int) Tools::getValue('id_category'));
            if (Validate::isLoadedObject($category)) {
                Hook::exec('actionCategoryUpdate', ['category' => $category]);
            }
            $this->redirect_after = self::$currentIndex . '&' . $this->table . 'Orderby=position&' . $this->table . 'Orderway=asc&action=Customization&conf=5' . (($id_category = (Tools::getIsset('id_category') ? (int) Tools::getValue('id_category') : '')) ? ('&id_category=' . $id_category) : '') . '&token=' . Tools::getAdminTokenLite('AdminProducts');
        }
    }

    public function initProcess()
    {
        if (Tools::isSubmit('submitAddproductAndStay') || Tools::isSubmit('submitAddproduct')) {
            $this->id_object = (int) Tools::getValue('id_product');
            $this->object = new Product($this->id_object);

            if ($this->isTabSubmitted('Informations') && $this->object->is_virtual && (int) Tools::getValue('type_product') != 2) {
                if ($id_product_download = (int) ProductDownload::getIdFromIdProduct($this->id_object)) {
                    $product_download = new ProductDownload($id_product_download);
                    if (!$product_download->deleteFile($id_product_download)) {
                        $this->errors[] = $this->trans('Cannot delete file', [], 'Admin.Notifications.Error');
                    }
                }
            }
        }

        // Delete a product in the download folder
        if (Tools::getValue('deleteVirtualProduct')) {
            if ($this->access('delete')) {
                $this->action = 'deleteVirtualProduct';
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitAddProductAndPreview')) {
            // Product preview
            $this->display = 'edit';
            $this->action = 'save';
            if (Tools::getValue('id_product')) {
                $this->id_object = Tools::getValue('id_product');
                $this->object = new Product((int) Tools::getValue('id_product'));
            }
        } elseif (Tools::isSubmit('submitAttachments')) {
            if ($this->access('edit')) {
                $this->action = 'attachments';
                $this->tab_display = 'attachments';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::getIsset('duplicate' . $this->table)) {
            // Product duplication
            if ($this->access('add')) {
                $this->action = 'duplicate';
            } else {
                $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::getValue('id_image') && Tools::getValue('ajax')) {
            // Product images management
            if ($this->access('edit')) {
                $this->action = 'image';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitProductAttribute')) {
            // Product attributes management
            if ($this->access('edit')) {
                $this->action = 'productAttribute';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitFeatures') || Tools::isSubmit('submitFeaturesAndStay')) {
            // Product features management
            if ($this->access('edit')) {
                $this->action = 'features';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitPricesModification')) {
            // Product specific prices management NEVER USED
            if ($this->access('add')) {
                $this->action = 'pricesModification';
            } else {
                $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('deleteSpecificPrice')) {
            if ($this->access('delete')) {
                $this->action = 'deleteSpecificPrice';
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitSpecificPricePriorities')) {
            if ($this->access('edit')) {
                $this->action = 'specificPricePriorities';
                $this->tab_display = 'prices';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitCustomizationConfiguration')) {
            // Customization management
            if ($this->access('edit')) {
                $this->action = 'customizationConfiguration';
                $this->tab_display = 'customization';
                $this->display = 'edit';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitProductCustomization')) {
            if ($this->access('edit')) {
                $this->action = 'productCustomization';
                $this->tab_display = 'customization';
                $this->display = 'edit';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('id_product')) {
            $post_max_size = Tools::getMaxUploadSize(Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1024 * 1024);
            if ($post_max_size && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] && $_SERVER['CONTENT_LENGTH'] > $post_max_size) {
                $this->errors[] = $this->trans(
                    'The uploaded file exceeds the "Maximum size for a downloadable product" set in preferences (%1$dMB) or the post_max_size/ directive in php.ini (%2$dMB).',
                    [
                        number_format((float) Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE')),
                        ($post_max_size / 1024 / 1024),
                    ],
                    'Admin.Catalog.Notification'
                );
            }
        }

        if (!$this->action) {
            parent::initProcess();
        } else {
            $this->id_object = (int) Tools::getValue($this->identifier);
        }

        if (isset($this->available_tabs[Tools::getValue('key_tab')])) {
            $this->tab_display = Tools::getValue('key_tab');
        }

        // Set tab to display if not decided already
        if (!$this->tab_display && $this->action) {
            if (in_array($this->action, array_keys($this->available_tabs))) {
                $this->tab_display = $this->action;
            }
        }

        // And if still not set, use default
        if (!$this->tab_display) {
            if (in_array($this->default_tab, $this->available_tabs)) {
                $this->tab_display = $this->default_tab;
            } else {
                $this->tab_display = key($this->available_tabs);
            }
        }
    }

    /**
     * postProcess for new form archi (need object return).
     *
     * @return ObjectModel|false
     */
    public function postCoreProcess()
    {
        return parent::postProcess();
    }

    /**
     * postProcess handle every checks before saving products information.
     */
    public function postProcess()
    {
        if (!$this->redirect_after) {
            parent::postProcess();
        }

        if ($this->display == 'edit' || $this->display == 'add') {
            $this->addJqueryUI([
                'ui.core',
                'ui.widget',
            ]);

            $this->addjQueryPlugin([
                'autocomplete',
                'tablednd',
                'thickbox',
                'ajaxfileupload',
                'date',
                'tagify',
                'select2',
                'validate',
            ]);

            $this->addJS([
                _PS_JS_DIR_ . 'admin/products.js',
                _PS_JS_DIR_ . 'admin/price.js',
                _PS_JS_DIR_ . 'tiny_mce/tiny_mce.js',
                _PS_JS_DIR_ . 'admin/tinymce.inc.js',
                _PS_JS_DIR_ . 'admin/dnd.js',
                _PS_JS_DIR_ . 'jquery/ui/jquery.ui.progressbar.min.js',
                _PS_JS_DIR_ . 'vendor/spin.js',
                _PS_JS_DIR_ . 'vendor/ladda.js',
            ]);

            $this->addJS(_PS_JS_DIR_ . 'jquery/plugins/select2/select2_locale_' . $this->context->language->iso_code . '.js');
            $this->addJS(_PS_JS_DIR_ . 'jquery/plugins/validate/localization/messages_' . $this->context->language->iso_code . '.js');

            $this->addCSS([
                _PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css',
            ]);
        }
    }

    public function ajaxProcessDeleteProductAttribute()
    {
        if (!Combination::isFeatureActive()) {
            return;
        }

        if ($this->access('delete')) {
            $id_product = (int) Tools::getValue('id_product');
            $id_product_attribute = (int) Tools::getValue('id_product_attribute');

            if ($id_product && Validate::isUnsignedId($id_product) && Validate::isLoadedObject($product = new Product($id_product))) {
                if (($depends_on_stock = StockAvailable::dependsOnStock($id_product)) && StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute)) {
                    $json = [
                        'status' => 'error',
                        'message' => 'It is not possible to delete a combination while it still has some quantities in the Advanced Stock Management. You must delete its stock first.',
                    ];
                } else {
                    $product->deleteAttributeCombination((int) $id_product_attribute);
                    $product->checkDefaultAttributes();
                    Tools::clearColorListCache((int) $product->id);
                    if (!$product->hasAttributes()) {
                        $product->cache_default_attribute = 0;
                        $product->update();
                    } else {
                        Product::updateDefaultAttribute($id_product);
                    }

                    if ($depends_on_stock && !Stock::deleteStockByIds($id_product, $id_product_attribute)) {
                        $json = [
                            'status' => 'error',
                            'message' => 'Error while deleting the stock',
                        ];
                    } else {
                        $json = [
                            'status' => 'ok',
                            'message' => $this->_conf[1],
                            'id_product_attribute' => (int) $id_product_attribute,
                        ];
                    }
                }
            } else {
                $json = [
                    'status' => 'error',
                    'message' => 'You cannot delete this attribute.',
                ];
            }
        } else {
            $json = [
                'status' => 'error',
                'message' => 'You do not have permission to delete this.',
            ];
        }

        die(json_encode($json));
    }

    public function ajaxProcessDefaultProductAttribute()
    {
        if ($this->access('edit')) {
            if (!Combination::isFeatureActive()) {
                return;
            }

            if (Validate::isLoadedObject($product = new Product((int) Tools::getValue('id_product')))) {
                $product->deleteDefaultAttributes();
                $product->setDefaultAttribute((int) Tools::getValue('id_product_attribute'));
                $json = [
                    'status' => 'ok',
                    'message' => $this->_conf[4],
                ];
            } else {
                $json = [
                    'status' => 'error',
                    'message' => 'You cannot make this the default attribute.',
                ];
            }

            die(json_encode($json));
        }
    }

    public function ajaxProcessEditProductAttribute()
    {
        if ($this->access('edit')) {
            $id_product = (int) Tools::getValue('id_product');
            $id_product_attribute = (int) Tools::getValue('id_product_attribute');
            if ($id_product && Validate::isUnsignedId($id_product) && Validate::isLoadedObject($product = new Product((int) $id_product))) {
                $combinations = $product->getAttributeCombinationsById($id_product_attribute, $this->context->language->id);
                foreach ($combinations as $key => $combination) {
                    $combinations[$key]['attributes'][] = [$combination['group_name'], $combination['attribute_name'], $combination['id_attribute']];
                }

                die(json_encode($combinations));
            }
        }
    }

    public function ajaxPreProcess()
    {
        if (Tools::getIsset('update' . $this->table) && Tools::getIsset('id_' . $this->table)) {
            $this->display = 'edit';
            $this->action = Tools::getValue('action');
        }
    }

    public function ajaxProcessUpdateProductImageShopAsso()
    {
        $id_product = (int) Tools::getValue('id_product');
        $id_image = (int) Tools::getValue('id_image');
        $id_shop = (int) Tools::getValue('id_shop');

        if ($id_image && $id_shop) {
            if (Tools::getValue('active') == 'true') {
                $res = Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'image_shop (`id_product`, `id_image`, `id_shop`, `cover`) VALUES(' . $id_product . ', ' . $id_image . ', ' . $id_shop . ', NULL)');
            } else {
                $res = Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'image_shop WHERE `id_image` = ' . $id_image . ' AND `id_shop` = ' . $id_shop);
            }
        }

        // Clean covers in image table
        $count_cover_image = Db::getInstance()->getValue('
			SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'image i
			INNER JOIN ' . _DB_PREFIX_ . 'image_shop ish ON (i.id_image = ish.id_image AND ish.id_shop = ' . $id_shop . ')
			WHERE i.cover = 1 AND i.`id_product` = ' . $id_product);

        if (!$id_image) {
            $id_image = Db::getInstance()->getValue('
                SELECT i.`id_image` FROM ' . _DB_PREFIX_ . 'image i
                INNER JOIN ' . _DB_PREFIX_ . 'image_shop ish ON (i.id_image = ish.id_image AND ish.id_shop = ' . $id_shop . ')
                WHERE i.`id_product` = ' . $id_product);
        }

        if ($count_cover_image < 1) {
            Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'image i SET i.cover = 1 WHERE i.id_image = ' . $id_image . ' AND i.`id_product` = ' . $id_product . ' LIMIT 1');
        }

        // Clean covers in image_shop table
        $count_cover_image_shop = Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM ' . _DB_PREFIX_ . 'image_shop ish
			WHERE ish.`id_product` = ' . $id_product . ' AND ish.id_shop = ' . $id_shop . ' AND ish.cover = 1');

        if ($count_cover_image_shop < 1) {
            Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'image_shop ish SET ish.cover = 1 WHERE ish.id_image = ' . $id_image . ' AND ish.`id_product` = ' . $id_product . ' AND ish.id_shop =  ' . (int) $id_shop . ' LIMIT 1');
        }

        if (isset($res) && $res) {
            $this->jsonConfirmation($this->_conf[27]);
        } else {
            $this->jsonError($this->trans('An error occurred while attempting to associate this image with your shop. ', [], 'Admin.Catalog.Notification'));
        }
    }

    public function ajaxProcessUpdateImagePosition()
    {
        if (!$this->access('edit')) {
            return die(json_encode(['error' => 'You do not have the right permission']));
        }
        $res = false;
        if ($json = Tools::getValue('json')) {
            $res = true;
            $json = stripslashes($json);
            $images = json_decode($json, true);
            foreach ($images as $id => $position) {
                $img = new Image((int) $id);
                $img->position = (int) $position;
                $res &= $img->update();
            }
        }
        if ($res) {
            $this->jsonConfirmation($this->_conf[25]);
        } else {
            $this->jsonError($this->trans('An error occurred while attempting to move this picture.', [], 'Admin.Catalog.Notification'));
        }
    }

    public function ajaxProcessUpdateCover()
    {
        if (!$this->access('edit')) {
            return die(json_encode(['error' => 'You do not have the right permission']));
        }
        Image::deleteCover((int) Tools::getValue('id_product'));
        $img = new Image((int) Tools::getValue('id_image'));
        $img->cover = true;

        @unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int) $img->id_product . '.jpg');
        @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $img->id_product . '_' . $this->context->shop->id . '.jpg');

        if ($img->update()) {
            $this->jsonConfirmation($this->_conf[26]);
        } else {
            $this->jsonError($this->trans('An error occurred while attempting to update the cover picture.', [], 'Admin.Catalog.Notification'));
        }
    }

    public function ajaxProcessDeleteProductImage($id_image = null)
    {
        $this->display = 'content';
        $res = true;
        /* Delete product image */
        $id_image = $id_image ? $id_image : (int) Tools::getValue('id_image');

        $image = new Image($id_image);
        $res &= $image->delete();
        // if deleted image was the cover, change it to the first one
        if (!Image::getCover($image->id_product)) {
            $res &= Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'image_shop` image_shop
			SET image_shop.`cover` = 1
			WHERE image_shop.`id_product` = ' . (int) $image->id_product . '
			AND id_shop=' . (int) $this->context->shop->id . ' LIMIT 1');
        }

        if (!Image::getGlobalCover($image->id_product)) {
            $res &= Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'image` i
			SET i.`cover` = 1
			WHERE i.`id_product` = ' . (int) $image->id_product . ' LIMIT 1');
        }

        if (file_exists(_PS_TMP_IMG_DIR_ . 'product_' . $image->id_product . '.jpg')) {
            $res &= @unlink(_PS_TMP_IMG_DIR_ . 'product_' . $image->id_product . '.jpg');
        }
        if (file_exists(_PS_TMP_IMG_DIR_ . 'product_mini_' . $image->id_product . '_' . $this->context->shop->id . '.jpg')) {
            $res &= @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . $image->id_product . '_' . $this->context->shop->id . '.jpg');
        }

        if ($res) {
            $this->jsonConfirmation($this->_conf[7]);
        } else {
            $this->jsonError($this->trans('An error occurred while attempting to delete the product image.', [], 'Admin.Catalog.Notification'));
        }
    }

    protected function _validateSpecificPrice($id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_combination = 0)
    {
        if (!Validate::isUnsignedId($id_shop) || !Validate::isUnsignedId($id_currency) || !Validate::isUnsignedId($id_country) || !Validate::isUnsignedId($id_group) || !Validate::isUnsignedId($id_customer)) {
            $this->errors[] = $this->trans('Wrong IDs', [], 'Admin.Catalog.Notification');
        } elseif ((!isset($price) && !isset($reduction)) || (isset($price) && !Validate::isNegativePrice($price)) || (isset($reduction) && !Validate::isPrice($reduction))) {
            $this->errors[] = $this->trans('Invalid price/discount amount', [], 'Admin.Catalog.Notification');
        } elseif (!Validate::isUnsignedInt($from_quantity)) {
            $this->errors[] = $this->trans('Invalid quantity', [], 'Admin.Catalog.Notification');
        } elseif ($reduction && !Validate::isReductionType($reduction_type)) {
            $this->errors[] = $this->trans('Please select a discount type (amount or percentage).', [], 'Admin.Catalog.Notification');
        } elseif ($from && $to && (!Validate::isDateFormat($from) || !Validate::isDateFormat($to))) {
            $this->errors[] = $this->trans('The from/to date is invalid.', [], 'Admin.Catalog.Notification');
        } elseif (SpecificPrice::exists((int) $this->object->id, $id_combination, $id_shop, $id_group, $id_country, $id_currency, $id_customer, $from_quantity, $from, $to, false)) {
            $this->errors[] = $this->trans('A specific price already exists for these parameters.', [], 'Admin.Catalog.Notification');
        } else {
            return true;
        }

        return false;
    }

    /**
     * Checking customs feature.
     *
     * @param array $languages
     * @param array $featureInfo
     *
     * @return int|string
     */
    protected function checkFeatures($languages, $featureInfo)
    {
        $rules = call_user_func(['FeatureValue', 'getValidationRules'], 'FeatureValue');
        $feature = Feature::getFeature((int) Configuration::get('PS_LANG_DEFAULT'), $featureInfo['feature']);

        foreach ($languages as $language) {
            if (isset($featureInfo['custom_value'][$language['id_lang']])) {
                $val = $featureInfo['custom_value'][$language['id_lang']];
                $current_language = new Language($language['id_lang']);
                if (Tools::strlen($val) > $rules['sizeLang']['value']) {
                    $this->errors[] = $this->trans(
                        'The name for feature %1$s is too long in %2$s.',
                        [
                            ' <b>' . $feature['name'] . '</b>',
                            $current_language->name,
                        ],
                        'Admin.Catalog.Notification'
                    );
                } elseif (!call_user_func(['Validate', $rules['validateLang']['value']], $val)) {
                    $this->errors[] = $this->trans(
                        'A valid name required for feature. %1$s in %2$s.',
                        [
                            ' <b>' . $feature['name'] . '</b>',
                            $current_language->name,
                        ],
                        'Admin.Catalog.Notification'
                    );
                }
                if (count($this->errors)) {
                    return 0;
                }
                // Getting default language
                if ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT')) {
                    return $val;
                }
            }
        }

        return 0;
    }

    /**
     * Add or update a product image.
     *
     * @param Product $product Product object to add image
     * @param string $method
     *
     * @return int|false
     */
    public function addProductImage($product, $method = 'auto')
    {
        /* Updating an existing product image */
        if ($id_image = (int) Tools::getValue('id_image')) {
            $image = new Image((int) $id_image);
            if (!Validate::isLoadedObject($image)) {
                $this->errors[] = $this->trans('An error occurred while uploading the image.', [], 'Admin.Notifications.Error');
            } else {
                if (($cover = Tools::getValue('cover')) == 1) {
                    Image::deleteCover($product->id);
                }
                $image->cover = $cover;
                $this->validateRules('Image');
                $this->copyFromPost($image, 'image');
                if (count($this->errors) || !$image->update()) {
                    $this->errors[] = $this->trans('An error occurred while updating the image.', [], 'Admin.Notifications.Error');
                } elseif (isset($_FILES['image_product']['tmp_name']) && $_FILES['image_product']['tmp_name'] != null) {
                    $this->copyImage($product->id, $image->id, $method);
                }
            }
        }
        if (isset($image) && Validate::isLoadedObject($image) && !file_exists(_PS_PRODUCT_IMG_DIR_ . $image->getExistingImgPath() . '.' . $image->image_format)) {
            $image->delete();
        }
        if (count($this->errors)) {
            return false;
        }
        @unlink(_PS_TMP_IMG_DIR_ . 'product_' . $product->id . '.jpg');
        @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . $product->id . '_' . $this->context->shop->id . '.jpg');

        return (is_int($id_image) && $id_image) ? $id_image : false;
    }

    /**
     * Copy a product image.
     *
     * @param int $id_product Product Id for product image filename
     * @param int $id_image Image Id for product image filename
     * @param string $method
     *
     * @return void|false
     *
     * @throws PrestaShopException
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
                $this->errors[] = $this->trans('An error occurred while attempting to create a new folder.', [], 'Admin.Notifications.Error');
            }
            if (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image_product']['tmp_name'], $tmpName)) {
                $this->errors[] = $this->trans('An error occurred while uploading the image.', [], 'Admin.Notifications.Error');
            } elseif (!ImageManager::resize($tmpName, $new_path . '.' . $image->image_format)) {
                $this->errors[] = $this->trans('An error occurred while copying the image.', [], 'Admin.Notifications.Error');
            } elseif ($method == 'auto') {
                $imagesTypes = ImageType::getImagesTypes('products');
                foreach ($imagesTypes as $k => $image_type) {
                    if (!ImageManager::resize($tmpName, $new_path . '-' . stripslashes($image_type['name']) . '.' . $image->image_format, $image_type['width'], $image_type['height'], $image->image_format)) {
                        $this->errors[] = $this->trans('An error occurred while copying this image: %s', [stripslashes($image_type['name'])], 'Admin.Notifications.Error');
                    }
                }
            }

            @unlink($tmpName);
            Hook::exec('actionWatermark', ['id_image' => $id_image, 'id_product' => $id_product]);
        }
    }

    protected function updateAssoShop($id_object)
    {
        //override AdminController::updateAssoShop() specifically for products because shop association is set with the context in ObjectModel
    }

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
            PrestaShopLogger::addLog(sprintf('%s addition', $this->className), 1, null, $this->className, (int) $this->object->id, true, (int) $this->context->employee->id);
            $this->addCarriers($this->object);
            $this->updateAccessories($this->object);
            $this->updatePackItems($this->object);
            $this->updateDownloadProduct($this->object);

            if (Configuration::get('PS_FORCE_ASM_NEW_PRODUCT') && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $this->object->getType() != Product::PTYPE_VIRTUAL) {
                $this->object->advanced_stock_management = 1;
                $this->object->save();
                $id_shops = Shop::getContextListShopID();
                foreach ($id_shops as $id_shop) {
                    StockAvailable::setProductDependsOnStock($this->object->id, true, (int) $id_shop, 0);
                }
            }

            if (empty($this->errors)) {
                $languages = Language::getLanguages(false);
                if ($this->isProductFieldUpdated('category_box') && !$this->object->updateCategories(Tools::getValue('categoryBox'))) {
                    $this->errors[] = $this->trans(
                        'An error occurred while linking the object %table_name% to categories.',
                        [
                            '%table_name%' => ' <b>' . $this->table . '</b> ',
                        ],
                        'Admin.Notifications.Error'
                    );
                } elseif (!$this->updateTags($languages, $this->object)) {
                    $this->errors[] = $this->trans('An error occurred while adding tags.', [], 'Admin.Catalog.Notification');
                } else {
                    Hook::exec('actionProductAdd', ['id_product_old' => null, 'id_product' => (int) $this->object->id, 'product' => $this->object]);
                    if (in_array($this->object->visibility, ['both', 'search']) && Configuration::get('PS_SEARCH_INDEXATION')) {
                        Search::indexation(false, $this->object->id);
                    }
                }

                if (Configuration::get('PS_DEFAULT_WAREHOUSE_NEW_PRODUCT') != 0 && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    $warehouse_location_entity = new WarehouseProductLocation();
                    $warehouse_location_entity->id_product = $this->object->id;
                    $warehouse_location_entity->id_product_attribute = 0;
                    $warehouse_location_entity->id_warehouse = (int) Configuration::get('PS_DEFAULT_WAREHOUSE_NEW_PRODUCT');
                    $warehouse_location_entity->location = '';
                    $warehouse_location_entity->save();
                }

                // Apply groups reductions
                $this->object->setGroupReduction();

                // Save and preview
                if (Tools::isSubmit('submitAddProductAndPreview')) {
                    $this->redirect_after = $this->getPreviewUrl($this->object);
                }

                // Save and stay on same form
                if ($this->display == 'edit') {
                    $this->redirect_after = self::$currentIndex . '&id_product=' . (int) $this->object->id
                        . (Tools::getIsset('id_category') ? '&id_category=' . (int) Tools::getValue('id_category') : '')
                        . '&updateproduct&conf=3&key_tab=' . Tools::safeOutput(Tools::getValue('key_tab')) . '&token=' . $this->token;
                } else {
                    // Default behavior (save and back)
                    $this->redirect_after = self::$currentIndex
                        . (Tools::getIsset('id_category') ? '&id_category=' . (int) Tools::getValue('id_category') : '')
                        . '&conf=3&token=' . $this->token;
                }
            } else {
                $this->object->delete();
                // if errors : stay on edit page
                $this->display = 'edit';
            }
        } else {
            $this->errors[] = $this->trans('An error occurred while creating an object.', [], 'Admin.Notifications.Error') . ' <b>' . $this->table . '</b>';
        }

        return $this->object;
    }

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

        $query = trim(Tools::getValue('bo_query'));
        $searchType = (int) Tools::getValue('bo_search_type');

        if ($query) {
            $this->redirect_after = preg_replace('/[\?|&](bo_query|bo_search_type)=([^&]*)/i', '', $this->redirect_after);
            $this->redirect_after .= '&bo_query=' . $query . '&bo_search_type=' . $searchType;
        }

        return $res;
    }

    public function processUpdate()
    {
        $existing_product = $this->object;

        $this->checkProduct();

        if (!empty($this->errors)) {
            $this->display = 'edit';

            return false;
        }

        $id = (int) Tools::getValue('id_' . $this->table);
        /* Update an existing product */
        if (!empty($id)) {
            /** @var Product $object */
            $object = new $this->className((int) $id);
            $this->object = $object;

            if (Validate::isLoadedObject($object)) {
                $this->_removeTaxFromEcotax();
                $product_type_before = $object->getType();
                $this->copyFromPost($object, $this->table);
                $object->indexed = 0;

                if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
                    $values = (array) Tools::getValue('multishop_check', []);
                    $values['state'] = Product::STATE_SAVED;
                    $values['id_manufacturer'] = true;
                    $values['ean13'] = true;
                    $values['mpn'] = true;
                    $values['isbn'] = true;
                    $values['upc'] = true;
                    $values['reference'] = true;
                    $values['weight'] = true;
                    $values['depth'] = true;
                    $values['width'] = true;
                    $values['height'] = true;

                    $object->setFieldsToUpdate($values);
                }

                // Duplicate combinations if not associated to shop
                if ($this->context->shop->getContext() == Shop::CONTEXT_SHOP && !$object->isAssociatedToShop()) {
                    $is_associated_to_shop = false;
                    $combinations = Product::getProductAttributesIds($object->id);
                    if ($combinations) {
                        foreach ($combinations as $id_combination) {
                            $combination = new Combination((int) $id_combination['id_product_attribute']);
                            $default_combination = new Combination((int) $id_combination['id_product_attribute'], null, (int) $this->object->id_shop_default);

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
                    // If the product doesn't exist in the current shop but exists in another shop
                    if (Shop::getContext() == Shop::CONTEXT_SHOP && !$existing_product->isAssociatedToShop($this->context->shop->id)) {
                        $out_of_stock = StockAvailable::outOfStock($existing_product->id, $existing_product->id_shop_default);
                        $depends_on_stock = StockAvailable::dependsOnStock($existing_product->id, $existing_product->id_shop_default);
                        StockAvailable::setProductOutOfStock((int) $this->object->id, $out_of_stock, $this->context->shop->id);
                        StockAvailable::setProductDependsOnStock((int) $this->object->id, $depends_on_stock, $this->context->shop->id);
                    }

                    PrestaShopLogger::addLog(sprintf('%s modification', $this->className), 1, null, $this->className, (int) $this->object->id, true, (int) $this->context->employee->id);
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
                        // Disallow avanced stock management if the product become a pack
                        if ($product_type_before == Product::PTYPE_SIMPLE && $object->getType() == Product::PTYPE_PACK) {
                            StockAvailable::setProductDependsOnStock((int) $object->id, false);
                        }
                        $this->updateDownloadProduct($object, 1);
                        $this->updateTags(Language::getLanguages(false), $object);

                        if ($this->isProductFieldUpdated('category_box') && !$object->updateCategories(Tools::getValue('categoryBox'))) {
                            $this->errors[] = $this->trans(
                                'An error occurred while linking the object %table_name% to categories.',
                                [
                                    '%table_name%' => ' <b>' . $this->table . '</b> ',
                                ],
                                'Admin.Notifications.Error'
                            );
                        }
                    }

                    if ($this->isTabSubmitted('Warehouses')) {
                        $this->processWarehouses();
                    }
                    if (empty($this->errors)) {
                        if (in_array($object->visibility, ['both', 'search']) && Configuration::get('PS_SEARCH_INDEXATION')) {
                            Search::indexation(false, $object->id);
                        }

                        // Save and preview
                        if (Tools::isSubmit('submitAddProductAndPreview')) {
                            $this->redirect_after = $this->getPreviewUrl($object);
                        } else {
                            $page = (int) Tools::getValue('page');
                            // Save and stay on same form
                            if ($this->display == 'edit') {
                                $this->confirmations[] = 'Update successful';
                                $this->redirect_after = self::$currentIndex . '&id_product=' . (int) $this->object->id
                                    . (Tools::getIsset('id_category') ? '&id_category=' . (int) Tools::getValue('id_category') : '')
                                    . '&updateproduct&conf=4&key_tab=' . Tools::safeOutput(Tools::getValue('key_tab')) . ($page > 1 ? '&page=' . (int) $page : '') . '&token=' . $this->token;
                            } else {
                                // Default behavior (save and back)
                                $this->redirect_after = self::$currentIndex . (Tools::getIsset('id_category') ? '&id_category=' . (int) Tools::getValue('id_category') : '') . '&conf=4' . ($page > 1 ? '&submitFilterproduct=' . (int) $page : '') . '&token=' . $this->token;
                            }
                        }
                    } else {
                        // if errors: stay on edit page
                        $this->display = 'edit';
                    }
                } else {
                    if (!$is_associated_to_shop && $combinations) {
                        foreach ($combinations as $id_combination) {
                            $combination = new Combination((int) $id_combination['id_product_attribute']);
                            $combination->delete();
                        }
                    }
                    $this->errors[] = $this->trans('An error occurred while updating an object.', [], 'Admin.Notifications.Error') . ' <b>' . $this->table . '</b> (' . Db::getInstance()->getMsgError() . ')';
                }
            } else {
                $this->errors[] = $this->trans('An error occurred while updating an object.', [], 'Admin.Notifications.Error') . ' <b>' . $this->table . '</b> (' . $this->trans('The object cannot be loaded. ', [], 'Admin.Notifications.Error') . ')';
            }

            return $object;
        }
    }

    /**
     * Check that a saved product is valid.
     */
    public function checkProduct()
    {
        /** @todo : the call_user_func seems to contains only statics values (className = 'Product') */
        $rules = call_user_func([$this->className, 'getValidationRules'], $this->className);
        $default_language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);

        // Check required fields
        foreach ($rules['required'] as $field) {
            if (!$this->isProductFieldUpdated($field)) {
                continue;
            }

            if (($value = Tools::getValue($field)) == false && $value != '0') {
                if (Tools::getValue('id_' . $this->table) && $field == 'passwd') {
                    continue;
                }
                $this->errors[] = $this->trans('The %name% field is required.', ['%name%' => call_user_func([$this->className, 'displayFieldName'], $field, $this->className)], 'Admin.Notifications.Error');
            }
        }

        // Check multilingual required fields
        foreach ($rules['requiredLang'] as $fieldLang) {
            if ($this->isProductFieldUpdated($fieldLang, $default_language->id) && !Tools::getValue($fieldLang . '_' . $default_language->id)) {
                $this->errors[] = $this->trans(
                    'This %1$s field is required at least in %2$s',
                    [
                        call_user_func([$this->className, 'displayFieldName'], $fieldLang, $this->className),
                        $default_language->name,
                    ],
                    'Admin.Catalog.Notification'
                );
            }
        }

        // Check fields sizes
        foreach ($rules['size'] as $field => $maxLength) {
            if ($this->isProductFieldUpdated($field) && ($value = Tools::getValue($field)) && Tools::strlen($value) > $maxLength) {
                $this->errors[] = $this->trans(
                    'The %1$s field is too long (%2$d chars max).',
                    [
                        call_user_func([$this->className, 'displayFieldName'], $field, $this->className),
                        $maxLength,
                    ],
                    'Admin.Catalog.Notification'
                );
            }
        }

        if (Tools::getIsset('description_short') && $this->isProductFieldUpdated('description_short')) {
            $saveShort = Tools::getValue('description_short');
            $_POST['description_short'] = strip_tags(Tools::getValue('description_short'));
        }

        // Check description short size without html
        $limit = (int) Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
        if ($limit <= 0) {
            $limit = 400;
        }
        foreach ($languages as $language) {
            if ($this->isProductFieldUpdated('description_short', $language['id_lang']) && ($value = Tools::getValue('description_short_' . $language['id_lang']))) {
                // This validation computation actually comes from TinyMceMaxLengthValidator if you modify it here you
                // should keep the validator in sync (along with other parts of the code, more info in the
                // TinyMceMaxLengthValidator comments).
                $replaceArray = [
                    "\n",
                    "\r",
                    "\n\r",
                    "\r\n",
                ];
                $str = str_replace($replaceArray, [''], strip_tags($value));
                $shortDescriptionLength = iconv_strlen($str);
                if ($shortDescriptionLength > $limit) {
                    $this->errors[] = $this->trans(
                        'This %1$s field (%2$s) is too long: %3$d chars max (current count %4$d).',
                        [
                            call_user_func([$this->className, 'displayFieldName'], 'description_short'),
                            $language['name'],
                            $limit,
                            Tools::strlen(strip_tags($value)),
                        ],
                        'Admin.Catalog.Notification'
                    );
                }
            }
        }

        // Check multilingual fields sizes
        foreach ($rules['sizeLang'] as $fieldLang => $maxLength) {
            foreach ($languages as $language) {
                $value = Tools::getValue($fieldLang . '_' . $language['id_lang']);
                if ($value && Tools::strlen($value) > $maxLength) {
                    $this->errors[] = $this->trans(
                        'The %1$s field is too long (%2$d chars max).',
                        [
                            call_user_func([$this->className, 'displayFieldName'], $fieldLang, $this->className),
                            $maxLength,
                        ],
                        'Admin.Catalog.Notification'
                    );
                }
            }
        }

        if ($this->isProductFieldUpdated('description_short') && isset($_POST['description_short'], $saveShort)) {
            $_POST['description_short'] = $saveShort;
        }

        // Check fields validity
        foreach ($rules['validate'] as $field => $function) {
            if ($this->isProductFieldUpdated($field) && ($value = Tools::getValue($field))) {
                $res = true;
                if (Tools::strtolower($function) == 'iscleanhtml') {
                    if (!Validate::$function($value, (int) Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                        $res = false;
                    }
                } elseif (!Validate::$function($value)) {
                    $res = false;
                }

                if (!$res) {
                    $this->errors[] = $this->trans(
                        'The %s field is invalid.',
                        [
                            call_user_func([$this->className, 'displayFieldName'], $field, $this->className),
                        ],
                        'Admin.Notifications.Error'
                    );
                }
            }
        }
        // Check multilingual fields validity
        foreach ($rules['validateLang'] as $fieldLang => $function) {
            foreach ($languages as $language) {
                if ($this->isProductFieldUpdated($fieldLang, $language['id_lang']) && ($value = Tools::getValue($fieldLang . '_' . $language['id_lang']))) {
                    if (!Validate::$function($value, (int) Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                        $this->errors[] = $this->trans(
                            'The %1$s field (%2$s) is invalid.',
                            [
                                call_user_func([$this->className, 'displayFieldName'], $fieldLang, $this->className),
                                $language['name'],
                            ],
                            'Admin.Notifications.Error'
                        );
                    }
                }
            }
        }

        // Categories
        if ($this->isProductFieldUpdated('id_category_default') && (!Tools::isSubmit('categoryBox') || !count(Tools::getValue('categoryBox')))) {
            $this->errors[] = 'Products must be in at least one category.';
        }

        if ($this->isProductFieldUpdated('id_category_default') && (!is_array(Tools::getValue('categoryBox')) || !in_array(Tools::getValue('id_category_default'), Tools::getValue('categoryBox')))) {
            $this->errors[] = 'This product must be in the default category.';
        }

        // Tags
        foreach ($languages as $language) {
            if ($value = Tools::getValue('tags_' . $language['id_lang'])) {
                if (!Validate::isTagsList($value)) {
                    $this->errors[] = $this->trans(
                        'The tags list (%s) is invalid.',
                        [
                            $language['name'],
                        ],
                        'Admin.Notifications.Error'
                    );
                }
            }
        }
    }

    /**
     * Check if a field is edited (if the checkbox is checked)
     * This method will do something only for multishop with a context all / group.
     *
     * @param string $field Name of field
     * @param int $id_lang
     *
     * @return bool
     */
    protected function isProductFieldUpdated($field, $id_lang = null)
    {
        // Cache this condition to improve performances
        static $is_activated = null;
        if (null === $is_activated) {
            $is_activated = Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP && $this->id_object;
        }

        if (!$is_activated) {
            return true;
        }

        $def = ObjectModel::getDefinition($this->object);
        if (!$this->object->isMultiShopField($field) && null === $id_lang && isset($def['fields'][$field])) {
            return true;
        }

        if (null === $id_lang) {
            return !empty($_POST['multishop_check'][$field]);
        } else {
            return !empty($_POST['multishop_check'][$field][$id_lang]);
        }
    }

    protected function _removeTaxFromEcotax()
    {
        if ($ecotax = Tools::getValue('ecotax')) {
            $_POST['ecotax'] = Tools::ps_round($ecotax / (1 + Tax::getProductEcotaxRate() / 100), 6);
        }
    }

    protected function _applyTaxToEcotax($product)
    {
        if ($product->ecotax) {
            $product->ecotax = Tools::ps_round($product->ecotax * (1 + Tax::getProductEcotaxRate() / 100), 2);
        }
    }

    /**
     * Update product download.
     *
     * @param Product $product
     * @param int $edit
     *
     * @return bool
     */
    public function updateDownloadProduct($product, $edit = 0)
    {
        //legacy/sf2 form workaround
        //if is_virtual_file parameter was not send (SF2 form case), don't process virtual file
        if (Tools::getValue('is_virtual_file') === false) {
            return false;
        }

        if ((int) Tools::getValue('is_virtual_file') == 1) {
            if (isset($_FILES['virtual_product_file_uploader']) && $_FILES['virtual_product_file_uploader']['size'] > 0) {
                $virtual_product_filename = ProductDownload::getNewFilename();
                $helper = new HelperUploader('virtual_product_file_uploader');
                $helper->setPostMaxSize(Tools::getOctets(ini_get('upload_max_filesize')))
                    ->setSavePath(_PS_DOWNLOAD_DIR_)->upload($_FILES['virtual_product_file_uploader'], $virtual_product_filename);
            } else {
                $virtual_product_filename = Tools::getValue('virtual_product_filename', ProductDownload::getNewFilename());
            }

            $product->setDefaultAttribute(0); //reset cache_default_attribute

            // Trick's
            if ($edit == 1) {
                $id_product_download = (int) ProductDownload::getIdFromIdProduct((int) $product->id, false);
                if (!$id_product_download) {
                    $id_product_download = (int) Tools::getValue('virtual_product_id');
                }
            } else {
                $id_product_download = Tools::getValue('virtual_product_id');
            }

            $is_shareable = (bool) Tools::getValue('virtual_product_is_shareable');
            $virtual_product_name = Tools::getValue('virtual_product_name');
            $virtual_product_nb_days = Tools::getValue('virtual_product_nb_days');
            $virtual_product_nb_downloable = Tools::getValue('virtual_product_nb_downloable');
            $virtual_product_expiration_date = Tools::getValue('virtual_product_expiration_date');

            $download = new ProductDownload((int) $id_product_download);
            $download->id_product = (int) $product->id;
            $download->display_filename = $virtual_product_name;
            $download->filename = $virtual_product_filename;
            $download->date_add = date('Y-m-d H:i:s');
            $download->date_expiration = $virtual_product_expiration_date ? $virtual_product_expiration_date . ' 23:59:59' : '';
            $download->nb_days_accessible = (int) $virtual_product_nb_days;
            $download->nb_downloadable = (int) $virtual_product_nb_downloable;
            $download->active = true;
            $download->is_shareable = $is_shareable;
            if ($download->save()) {
                return true;
            }
        } else {
            /* unactive download product if checkbox not checked */
            if ($edit == 1) {
                $id_product_download = (int) ProductDownload::getIdFromIdProduct((int) $product->id);
                if (!$id_product_download) {
                    $id_product_download = (int) Tools::getValue('virtual_product_id');
                }
            } else {
                $id_product_download = ProductDownload::getIdFromIdProduct($product->id);
            }

            if (!empty($id_product_download)) {
                $product_download = new ProductDownload((int) $id_product_download);
                $product_download->date_expiration = date('Y-m-d H:i:s', time() - 1);
                $product_download->active = false;

                return $product_download->save();
            }
        }

        return false;
    }

    /**
     * Update product accessories.
     *
     * @param object $product Product
     */
    public function updateAccessories($product)
    {
        $product->deleteAccessories();
        if ($accessories = Tools::getValue('inputAccessories')) {
            $accessories_id = array_unique(explode('-', $accessories));
            array_pop($accessories_id);
            $product->changeAccessories($accessories_id);
        }
    }

    /**
     * Update product tags.
     *
     * @param array $languages Array languages
     * @param object $product Product
     *
     * @return bool Update result
     */
    public function updateTags($languages, $product)
    {
        $tag_success = true;
        /* Reset all tags for THIS product */
        if (!Tag::deleteTagsForProduct((int) $product->id)) {
            $this->errors[] = $this->trans('An error occurred while attempting to delete previous tags.', [], 'Admin.Catalog.Notification');
        }
        /* Assign tags to this product */
        foreach ($languages as $language) {
            if ($value = Tools::getValue('tags_' . $language['id_lang'])) {
                $tag_success &= Tag::addTags($language['id_lang'], (int) $product->id, $value);
            }
        }

        if (!$tag_success) {
            $this->errors[] = $this->trans('An error occurred while adding tags.', [], 'Admin.Catalog.Notification');
        }

        return $tag_success;
    }

    public function ajaxProcessProductManufacturers()
    {
        $manufacturers = Manufacturer::getManufacturers(false, 0, true, false, false, false, true);
        $jsonArray = [];

        if ($manufacturers) {
            foreach ($manufacturers as $manufacturer) {
                $tmp = ['optionValue' => $manufacturer['id_manufacturer'], 'optionDisplay' => htmlspecialchars(trim($manufacturer['name']))];
                $jsonArray[] = json_encode($tmp);
            }
        }

        die('[' . implode(',', $jsonArray) . ']');
    }

    public function getPreviewUrl(Product $product)
    {
        $id_lang = (int) Configuration::get('PS_LANG_DEFAULT', null, null, Context::getContext()->shop->id);

        if (!ShopUrl::getMainShopDomain()) {
            return false;
        }

        $is_rewrite_active = (bool) Configuration::get('PS_REWRITING_SETTINGS');
        $preview_url = $this->context->link->getProductLink(
            $product,
            $this->getFieldValue($product, 'link_rewrite', $this->context->language->id),
            Category::getLinkRewrite($this->getFieldValue($product, 'id_category_default'), $this->context->language->id),
            null,
            $id_lang,
            (int) Context::getContext()->shop->id,
            0,
            $is_rewrite_active
        );

        if (!$product->active) {
            $admin_dir = dirname($_SERVER['PHP_SELF']);
            $admin_dir = substr($admin_dir, strrpos($admin_dir, '/') + 1);
            $preview_url .= ((strpos($preview_url, '?') === false) ? '?' : '&') . 'adtoken=' . $this->token . '&ad=' . $admin_dir . '&id_employee=' . (int) $this->context->employee->id;
        }

        return $preview_url;
    }

    /**
     * Post treatment for suppliers.
     *
     * @param int|null $id_product
     */
    public function processSuppliers($id_product = null)
    {
        $id_product = (int) $id_product ? $id_product : (int) Tools::getValue('id_product');

        if ((int) Tools::getValue('supplier_loaded') === 1 && Validate::isLoadedObject($product = new Product($id_product))) {
            // Get all id_product_attribute
            $attributes = $product->getAttributesResume($this->context->language->id);
            if (empty($attributes)) {
                $attributes[] = [
                    'id_product_attribute' => 0,
                    'attribute_designation' => '',
                ];
            }

            // Get all available suppliers
            $suppliers = Supplier::getSuppliers();

            // Get already associated suppliers
            $associated_suppliers = ProductSupplier::getSupplierCollection($product->id);

            $suppliers_to_associate = [];
            $new_default_supplier = 0;
            $defaultWholeslePrice = (float) 0;
            $defaultReference = '';

            if (Tools::isSubmit('default_supplier')) {
                $new_default_supplier = (int) Tools::getValue('default_supplier');
            }

            // Get new associations
            foreach ($suppliers as $supplier) {
                if (Tools::isSubmit('check_supplier_' . $supplier['id_supplier'])) {
                    $suppliers_to_associate[] = $supplier['id_supplier'];
                }
            }

            // Delete already associated suppliers if needed
            foreach ($associated_suppliers as $key => $associated_supplier) {
                /** @var ProductSupplier $associated_supplier */
                if (!in_array($associated_supplier->id_supplier, $suppliers_to_associate)) {
                    // Code taken from https://github.com/PrestaShop/PrestaShop/pull/26609/commits/e966aa7d3c2204ddb7318dd7203639845739137b
                    // ProductSupplier objectModel is shared between v1 & v2 product pages.
                    // This code ensures keeping old behavior in v1 product page without breaking v2 product page.

                    $res = $associated_supplier->delete();

                    if ($res && $associated_supplier->id_product_attribute == 0) {
                        $items = ProductSupplier::getSupplierCollection($associated_supplier->id_product, false);
                        foreach ($items as $item) {
                            /** @var ProductSupplier $item */
                            if ($item->id_product_attribute > 0) {
                                $item->delete();
                            }
                        }
                    }

                    unset($associated_suppliers[$key]);
                }
            }

            // Associate suppliers
            foreach ($suppliers_to_associate as $id) {
                $to_add = true;
                foreach ($associated_suppliers as $as) {
                    /** @var ProductSupplier $as */
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
                        $product_supplier->id_currency = (int) $this->context->currency->id;
                    } else {
                        $product_supplier->id_currency = Currency::getDefaultCurrencyId();
                    }
                    $product_supplier->save();

                    $associated_suppliers[] = $product_supplier;
                    foreach ($attributes as $attribute) {
                        if ((int) $attribute['id_product_attribute'] > 0) {
                            $product_supplier = new ProductSupplier();
                            $product_supplier->id_product = $product->id;
                            $product_supplier->id_product_attribute = (int) $attribute['id_product_attribute'];
                            $product_supplier->id_supplier = $id;
                            $product_supplier->save();
                        }
                    }
                }
            }

            // Manage references and prices
            foreach ($attributes as $attribute) {
                foreach ($associated_suppliers as $supplier) {
                    /** @var ProductSupplier $supplier */
                    if (Tools::isSubmit('supplier_reference_' . $product->id . '_' . $attribute['id_product_attribute'] . '_' . $supplier->id_supplier) ||
                        (Tools::isSubmit('product_price_' . $product->id . '_' . $attribute['id_product_attribute'] . '_' . $supplier->id_supplier) &&
                         Tools::isSubmit('product_price_currency_' . $product->id . '_' . $attribute['id_product_attribute'] . '_' . $supplier->id_supplier))) {
                        $reference = pSQL(
                            Tools::getValue(
                                'supplier_reference_' . $product->id . '_' . $attribute['id_product_attribute'] . '_' . $supplier->id_supplier,
                                ''
                            )
                        );

                        $price = (float) str_replace(
                            [' ', ','],
                            ['', '.'],
                            Tools::getValue(
                                'product_price_' . $product->id . '_' . $attribute['id_product_attribute'] . '_' . $supplier->id_supplier,
                                0
                            )
                        );

                        $price = Tools::ps_round($price, 6);

                        $id_currency = (int) Tools::getValue(
                            'product_price_currency_' . $product->id . '_' . $attribute['id_product_attribute'] . '_' . $supplier->id_supplier,
                            0
                        );

                        if ($id_currency <= 0 || !($result = Currency::getCurrency($id_currency))) {
                            $this->errors[] = $this->trans('The selected currency is not valid', [], 'Admin.Catalog.Notification');
                        }

                        // Save product-supplier data
                        $product_supplier_id = (int) ProductSupplier::getIdByProductAndSupplier($product->id, $attribute['id_product_attribute'], $supplier->id_supplier);

                        if (!$product_supplier_id) {
                            $product->addSupplierReference($supplier->id_supplier, (int) $attribute['id_product_attribute'], $reference, (float) $price, (int) $id_currency);
                        } else {
                            $product_supplier = new ProductSupplier($product_supplier_id);
                            $product_supplier->id_currency = (int) $id_currency;
                            $product_supplier->product_supplier_price_te = (float) $price;
                            $product_supplier->product_supplier_reference = pSQL($reference);
                            $product_supplier->update();
                        }

                        if ($new_default_supplier == $supplier->id_supplier) {
                            if ((int) $attribute['id_product_attribute'] > 0) {
                                $data = [
                                    'supplier_reference' => pSQL($reference),
                                    'wholesale_price' => (float) Tools::convertPrice($price, $id_currency),
                                ];
                                $where = '
                                    a.id_product = ' . (int) $product->id . '
                                    AND a.id_product_attribute = ' . (int) $attribute['id_product_attribute'];
                                ObjectModel::updateMultishopTable('Combination', $data, $where);
                            } else {
                                // @deprecated 1.7.7.0 This condition block will be remove in the next major, use ProductSupplier instead
                                $defaultWholeslePrice = (float) Tools::convertPrice($price, $id_currency);
                                $defaultReference = $reference;
                            }
                        }
                    }
                }
            }

            if ($this->object) {
                $product->updateDefaultSupplierData(
                    $new_default_supplier,
                    $defaultReference,
                    $defaultWholeslePrice
                );
            }
        }
    }

    /**
     * Post treatment for warehouses.
     */
    public function processWarehouses()
    {
        if ((int) Tools::getValue('warehouse_loaded') === 1 && Validate::isLoadedObject($product = new Product((int) $id_product = Tools::getValue('id_product')))) {
            // Get all id_product_attribute
            $attributes = $product->getAttributesResume($this->context->language->id);
            if (empty($attributes)) {
                $attributes[] = [
                    'id_product_attribute' => 0,
                    'attribute_designation' => '',
                ];
            }

            // Get all available warehouses
            $warehouses = Warehouse::getWarehouses(true);

            // Get already associated warehouses
            $associated_warehouses_collection = WarehouseProductLocation::getCollection($product->id);

            $elements_to_manage = [];

            // get form information
            foreach ($attributes as $attribute) {
                foreach ($warehouses as $warehouse) {
                    $key = $warehouse['id_warehouse'] . '_' . $product->id . '_' . $attribute['id_product_attribute'];

                    // get elements to manage
                    if (Tools::isSubmit('check_warehouse_' . $key)) {
                        $location = Tools::getValue('location_warehouse_' . $key, '');
                        $elements_to_manage[$key] = $location;
                    }
                }
            }

            // Delete entry if necessary
            foreach ($associated_warehouses_collection as $awc) {
                /** @var WarehouseProductLocation $awc */
                if (!array_key_exists($awc->id_warehouse . '_' . $awc->id_product . '_' . $awc->id_product_attribute, $elements_to_manage)) {
                    $awc->delete();
                }
            }

            // Manage locations
            foreach ($elements_to_manage as $key => $location) {
                $params = explode('_', $key);

                $wpl_id = (int) WarehouseProductLocation::getIdByProductAndWarehouse((int) $params[1], (int) $params[2], (int) $params[0]);

                if (empty($wpl_id)) {
                    //create new record
                    $warehouse_location_entity = new WarehouseProductLocation();
                    $warehouse_location_entity->id_product = (int) $params[1];
                    $warehouse_location_entity->id_product_attribute = (int) $params[2];
                    $warehouse_location_entity->id_warehouse = (int) $params[0];
                    $warehouse_location_entity->location = pSQL($location);
                    $warehouse_location_entity->save();
                } else {
                    $warehouse_location_entity = new WarehouseProductLocation((int) $wpl_id);

                    $location = pSQL($location);

                    if ($location != $warehouse_location_entity->location) {
                        $warehouse_location_entity->location = pSQL($location);
                        $warehouse_location_entity->update();
                    }
                }
            }
            StockAvailable::synchronize((int) $id_product);
        }
    }

    /**
     * Get an array of pack items for display from the product object if specified, else from POST/GET values.
     *
     * @param Product $product
     *
     * @return array of pack items
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
            // ids is an array of string with format : QTYxID
            $ids = array_unique(explode('-', $ids_input));
            $names = array_unique(explode('¤', $names_input));

            $length = count($ids);
            for ($i = 0; $i < $length; ++$i) {
                if (isset($ids[$i]) && !empty($names[$i])) {
                    list($pack_items[$i]['pack_quantity'], $pack_items[$i]['id']) = explode('x', $ids[$i]);
                    $exploded_name = explode('x', $names[$i]);
                    $pack_items[$i]['name'] = $exploded_name[1];
                }
            }
        } else {
            $i = 0;
            foreach ($product->packItems as $pack_item) {
                $pack_items[$i]['id'] = $pack_item->id;
                $pack_items[$i]['pack_quantity'] = $pack_item->pack_quantity;
                $pack_items[$i]['name'] = $pack_item->name;
                $pack_items[$i]['reference'] = $pack_item->reference;
                $pack_items[$i]['id_product_attribute'] = isset($pack_item->id_pack_product_attribute) && $pack_item->id_pack_product_attribute ? $pack_item->id_pack_product_attribute : 0;
                $cover = $pack_item->id_pack_product_attribute ? Product::getCombinationImageById($pack_item->id_pack_product_attribute, Context::getContext()->language->id) : Product::getCover($pack_item->id);
                $pack_items[$i]['image'] = Context::getContext()->link->getImageLink($pack_item->link_rewrite, $cover['id_image'], 'home_default');
                // @todo: don't rely on 'home_default'
                //$path_to_image = _PS_IMG_DIR_.'p/'.Image::getImgFolderStatic($cover['id_image']).(int)$cover['id_image'].'.jpg';
                //$pack_items[$i]['image'] = ImageManager::thumbnail($path_to_image, 'pack_mini_'.$pack_item->id.'_'.$this->context->shop->id.'.jpg', 120);
                ++$i;
            }
        }

        return $pack_items;
    }

    protected function _getFinalPrice($specific_price, $product_price, $tax_rate)
    {
        return $this->object->getPrice(false, $specific_price['id_product_attribute'], 2);
    }

    protected function _getCustomizationFieldIds($labels, $alreadyGenerated, $obj)
    {
        $customizableFieldIds = [];
        if (isset($labels[Product::CUSTOMIZE_FILE])) {
            foreach ($labels[Product::CUSTOMIZE_FILE] as $id_customization_field => $label) {
                $customizableFieldIds[] = 'label_' . Product::CUSTOMIZE_FILE . '_' . (int) ($id_customization_field);
            }
        }
        if (isset($labels[Product::CUSTOMIZE_TEXTFIELD])) {
            foreach ($labels[Product::CUSTOMIZE_TEXTFIELD] as $id_customization_field => $label) {
                $customizableFieldIds[] = 'label_' . Product::CUSTOMIZE_TEXTFIELD . '_' . (int) ($id_customization_field);
            }
        }
        $j = 0;
        for ($i = $alreadyGenerated[Product::CUSTOMIZE_FILE]; $i < (int) ($this->getFieldValue($obj, 'uploadable_files')); ++$i) {
            $customizableFieldIds[] = 'newLabel_' . Product::CUSTOMIZE_FILE . '_' . $j++;
        }
        $j = 0;
        for ($i = $alreadyGenerated[Product::CUSTOMIZE_TEXTFIELD]; $i < (int) ($this->getFieldValue($obj, 'text_fields')); ++$i) {
            $customizableFieldIds[] = 'newLabel_' . Product::CUSTOMIZE_TEXTFIELD . '_' . $j++;
        }

        return implode('¤', $customizableFieldIds);
    }

    protected function getCarrierList()
    {
        $carrier_list = Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS);

        if ($product = $this->loadObject(true)) {
            /** @var Product $product */
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

    protected function addCarriers($product = null)
    {
        if (!isset($product)) {
            $product = new Product((int) Tools::getValue('id_product'));
        }

        if (Validate::isLoadedObject($product)) {
            $carriers = [];

            if (Tools::getValue('selectedCarriers')) {
                $carriers = Tools::getValue('selectedCarriers');
            }

            $product->setCarriers($carriers);
        }
    }

    /**
     * Ajax process upload images.
     *
     * @param int|null $idProduct
     * @param string $inputFileName
     * @param bool $die If method must die or return values
     *
     * @return array
     */
    public function ajaxProcessaddProductImage($idProduct = null, $inputFileName = 'file', $die = true)
    {
        $idProduct = $idProduct ? $idProduct : Tools::getValue('id_product');

        self::$currentIndex = 'index.php?tab=AdminProducts';
        $product = new Product((int) $idProduct);
        $legends = Tools::getValue('legend');

        if (!is_array($legends)) {
            $legends = (array) $legends;
        }

        if (!Validate::isLoadedObject($product)) {
            $files = [];
            $files[0]['error'] = $this->trans('Cannot add image because product creation failed.', [], 'Admin.Catalog.Notification');
        }

        $image_uploader = new HelperImageUploader($inputFileName);
        $image_uploader->setAcceptTypes(['jpeg', 'gif', 'png', 'jpg', 'webp'])->setMaxSize($this->max_image_size);
        $files = $image_uploader->process();

        foreach ($files as &$file) {
            $image = new Image();
            $image->id_product = (int) ($product->id);
            $image->position = Image::getHighestPosition($product->id) + 1;

            foreach ($legends as $key => $legend) {
                if (!empty($legend)) {
                    $image->legend[(int) $key] = $legend;
                }
            }

            $image->cover = !Image::getCover($image->id_product);

            if (($validate = $image->validateFieldsLang(false, true)) !== true) {
                $file['error'] = $validate;
            }

            if (isset($file['error']) && (!is_numeric($file['error']) || $file['error'] != 0)) {
                continue;
            }

            if (!$image->add()) {
                $file['error'] = $this->trans('Error while creating additional image', [], 'Admin.Catalog.Notification');
            } else {
                if (!$new_path = $image->getPathForCreation()) {
                    $file['error'] = $this->trans('An error occurred while attempting to create a new folder.', [], 'Admin.Notifications.Error');

                    continue;
                }

                $error = 0;

                if (!ImageManager::resize($file['save_path'], $new_path . '.' . $image->image_format, null, null, 'jpg', false, $error)) {
                    switch ($error) {
                        case ImageManager::ERROR_FILE_NOT_EXIST:
                            $file['error'] = $this->trans('An error occurred while copying image, the file does not exist anymore.', [], 'Admin.Catalog.Notification');

                            break;

                        case ImageManager::ERROR_FILE_WIDTH:
                            $file['error'] = $this->trans('An error occurred while copying image, the file width is 0px.', [], 'Admin.Catalog.Notification');

                            break;

                        case ImageManager::ERROR_MEMORY_LIMIT:
                            $file['error'] = $this->trans('An error occurred while copying image, check your memory limit.', [], 'Admin.Catalog.Notification');

                            break;

                        default:
                            $file['error'] = $this->trans('An error occurred while copying the image.', [], 'Admin.Catalog.Notification');

                            break;
                    }

                    continue;
                } else {
                    $imagesTypes = ImageType::getImagesTypes('products');

                    // Should we generate high DPI images?
                    $generate_hight_dpi_images = (bool) Configuration::get('PS_HIGHT_DPI');

                    $sfContainer = SymfonyContainer::getInstance();

                    /*
                    * Let's resolve which formats we will use for image generation.
                    * In new image system, it's multiple formats. In case of legacy, it's only .jpg.
                    *
                    * In case of .jpg images, the actual format inside is decided by ImageManager.
                    */
                    if ($sfContainer->get('prestashop.core.admin.feature_flag.repository')->isEnabled(FeatureFlagSettings::FEATURE_FLAG_MULTIPLE_IMAGE_FORMAT)) {
                        $configuredImageFormats = $sfContainer->get(ImageFormatConfiguration::class)->getGenerationFormats();
                    } else {
                        $configuredImageFormats = ['jpg'];
                    }
                    foreach ($imagesTypes as $imageType) {
                        foreach ($configuredImageFormats as $imageFormat) {
                            // For JPG images, we let Imagemanager decide what to do and choose between JPG/PNG.
                            // For webp and avif extensions, we want it to follow our command and ignore the original format.
                            $forceFormat = ($imageFormat !== 'jpg');
                            if (!ImageManager::resize(
                                $file['save_path'],
                                $new_path . '-' . stripslashes($imageType['name']) . '.' . $imageFormat,
                                $imageType['width'],
                                $imageType['height'],
                                $imageFormat,
                                $forceFormat
                            )) {
                                $file['error'] = $this->trans('An error occurred while copying this image:', [], 'Admin.Notifications.Error') . ' ' . stripslashes($imageType['name']);

                                continue;
                            }

                            if ($generate_hight_dpi_images) {
                                if (!ImageManager::resize(
                                    $file['save_path'],
                                    $new_path . '-' . stripslashes($imageType['name']) . '2x.' . $imageFormat,
                                    (int) $imageType['width'] * 2,
                                    (int) $imageType['height'] * 2,
                                    $imageFormat,
                                    $forceFormat
                                )) {
                                    $file['error'] = $this->trans('An error occurred while copying this image:', [], 'Admin.Notifications.Error') . ' ' . stripslashes($imageType['name']);

                                    continue;
                                }
                            }
                        }
                    }
                }

                unlink($file['save_path']);
                //Necesary to prevent hacking
                unset($file['save_path']);
                Hook::exec('actionWatermark', ['id_image' => $image->id, 'id_product' => $product->id]);

                if (!$image->update()) {
                    $file['error'] = $this->trans('Error while updating the status.', [], 'Admin.Notifications.Error');

                    continue;
                }

                // Associate image to shop from context
                $shops = Shop::getContextListShopID();
                $image->associateTo($shops);
                $json_shops = [];

                foreach ($shops as $id_shop) {
                    $json_shops[$id_shop] = true;
                }

                $file['status'] = 'ok';
                $file['id'] = $image->id;
                $file['position'] = $image->position;
                $file['cover'] = $image->cover;
                $file['legend'] = $image->legend;
                $file['path'] = $image->getExistingImgPath();
                $file['shops'] = $json_shops;

                @unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int) $product->id . '.jpg');
                @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $product->id . '_' . $this->context->shop->id . '.jpg');
            }
        }

        if ($die) {
            die(json_encode([$image_uploader->getName() => $files]));
        } else {
            return $files;
        }
    }

    public function ajaxProcessProductQuantity()
    {
        if (!$this->access('edit')) {
            return die(json_encode(['error' => 'You do not have the right permission']));
        }
        if (!Tools::getValue('actionQty')) {
            return json_encode(['error' => 'Undefined action']);
        }

        $product = new Product((int) Tools::getValue('id_product'), true);
        switch (Tools::getValue('actionQty')) {
            case 'depends_on_stock':
                if (Tools::getValue('value') === false) {
                    die(json_encode(['error' => 'Undefined value']));
                }
                if ((int) Tools::getValue('value') != 0 && (int) Tools::getValue('value') != 1) {
                    die(json_encode(['error' => 'Incorrect value']));
                }
                if (!$product->advanced_stock_management && (int) Tools::getValue('value') == 1) {
                    die(json_encode(['error' => 'Not possible if advanced stock management is disabled.']));
                }
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
                    && (int) Tools::getValue('value') == 1
                    && (
                        Pack::isPack($product->id)
                        && !Pack::allUsesAdvancedStockManagement($product->id)
                        && (
                            $product->pack_stock_type == Pack::STOCK_TYPE_PACK_BOTH
                            || $product->pack_stock_type == Pack::STOCK_TYPE_PRODUCTS_ONLY
                            || (
                                $product->pack_stock_type == Pack::STOCK_TYPE_DEFAULT
                                && (Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PRODUCTS_ONLY
                                    || Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PACK_BOTH)
                            )
                        )
                    )
                ) {
                    die(json_encode(['error' => 'You cannot use advanced stock management for this pack because' . '<br />' .
                        '- advanced stock management is not enabled for these products' . '<br />' .
                        '- you have chosen to decrement products quantities.', ]));
                }

                StockAvailable::setProductDependsOnStock($product->id, (bool) Tools::getValue('value'));

                break;

            case 'pack_stock_type':
                $value = Tools::getValue('value');
                if ($value === false) {
                    die(json_encode(['error' => 'Undefined value']));
                }
                if ((int) $value != 0 && (int) $value != 1
                    && (int) $value != 2 && (int) $value != 3) {
                    die(json_encode(['error' => 'Incorrect value']));
                }
                if ($product->depends_on_stock
                    && !Pack::allUsesAdvancedStockManagement($product->id)
                    && (
                        (int) $value == 1
                        || (int) $value == 2
                        || (
                            (int) $value == 3
                            && (Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PRODUCTS_ONLY
                                || Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PACK_BOTH)
                        )
                    )
                ) {
                    die(json_encode(['error' => 'You cannot use this stock management option because:' . '<br />' .
                        '- advanced stock management is not enabled for these products' . '<br />' .
                        '- advanced stock management is enabled for the pack', ]));
                }

                Product::setPackStockType($product->id, $value);

                break;

            case 'out_of_stock':
                if (Tools::getValue('value') === false) {
                    die(json_encode(['error' => 'Undefined value']));
                }
                if (!in_array((int) Tools::getValue('value'), [0, 1, 2])) {
                    die(json_encode(['error' => 'Incorrect value']));
                }

                StockAvailable::setProductOutOfStock($product->id, (int) Tools::getValue('value'));

                break;

            case 'set_qty':
                if (Tools::getValue('value') === false || (!is_numeric(trim(Tools::getValue('value'))))) {
                    die(json_encode(['error' => 'Undefined value']));
                }
                if (Tools::getValue('id_product_attribute') === false) {
                    die(json_encode(['error' => 'Undefined id product attribute']));
                }

                StockAvailable::setQuantity($product->id, (int) Tools::getValue('id_product_attribute'), (int) Tools::getValue('value'));
                Hook::exec('actionProductUpdate', ['id_product' => (int) $product->id, 'product' => $product]);

                // Catch potential echo from modules
                // This echoed error is kept for legacy controllers, but is dropped during sf refactoring of the hook.
                $error = ob_get_contents();
                if (!empty($error)) {
                    ob_end_clean();
                    die(json_encode(['error' => $error]));
                }

                break;
            case 'advanced_stock_management':
                if (Tools::getValue('value') === false) {
                    die(json_encode(['error' => 'Undefined value']));
                }
                if ((int) Tools::getValue('value') != 1 && (int) Tools::getValue('value') != 0) {
                    die(json_encode(['error' => 'Incorrect value']));
                }
                if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int) Tools::getValue('value') == 1) {
                    die(json_encode(['error' => 'Not possible if advanced stock management is disabled. ']));
                }

                $product->setAdvancedStockManagement((bool) Tools::getValue('value'));
                if (StockAvailable::dependsOnStock($product->id) == 1 && (int) Tools::getValue('value') == 0) {
                    StockAvailable::setProductDependsOnStock($product->id, false);
                }

                break;
        }
        die(json_encode(['error' => false]));
    }

    public function getCombinationImagesJS()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        /** @var Product $obj */
        $content = 'var combination_images = new Array();';
        if (!$allCombinationImages = $obj->getCombinationImages($this->context->language->id)) {
            return $content;
        }
        foreach ($allCombinationImages as $id_product_attribute => $combination_images) {
            $i = 0;
            $content .= 'combination_images[' . (int) $id_product_attribute . '] = new Array();';
            foreach ($combination_images as $combination_image) {
                $content .= 'combination_images[' . (int) $id_product_attribute . '][' . $i++ . '] = ' . (int) $combination_image['id_image'] . ';';
            }
        }

        return $content;
    }

    public function haveThisAccessory($accessory_id, $accessories)
    {
        foreach ($accessories as $accessory) {
            if ((int) $accessory['id_product'] == (int) $accessory_id) {
                return true;
            }
        }

        return false;
    }

    protected function initPack(Product $product)
    {
        $this->tpl_form_vars['is_pack'] = ($product->id && Pack::isPack($product->id)) || Tools::getValue('type_product') == Product::PTYPE_PACK;
        $product->packItems = Pack::getItems($product->id, $this->context->language->id);

        $input_pack_items = '';
        if (Tools::getValue('inputPackItems')) {
            $input_pack_items = Tools::getValue('inputPackItems');
        } else {
            foreach ($product->packItems as $pack_item) {
                $input_pack_items .= $pack_item->pack_quantity . 'x' . $pack_item->id . '-';
            }
        }
        $this->tpl_form_vars['input_pack_items'] = $input_pack_items;

        $input_namepack_items = '';
        if (Tools::getValue('namePackItems')) {
            $input_namepack_items = Tools::getValue('namePackItems');
        } else {
            foreach ($product->packItems as $pack_item) {
                $input_namepack_items .= $pack_item->pack_quantity . ' x ' . $pack_item->name . '¤';
            }
        }
        $this->tpl_form_vars['input_namepack_items'] = $input_namepack_items;
    }

    /**
     * delete all items in pack, then check if type_product value is 2.
     * if yes, add the pack items from input "inputPackItems".
     *
     * @param Product $product
     */
    public function updatePackItems($product)
    {
        Pack::deleteItems($product->id);
        // lines format: QTY x ID-QTY x ID
        if (Tools::getValue('type_product') == Product::PTYPE_PACK) {
            $product->setDefaultAttribute(0); //reset cache_default_attribute
            $items = Tools::getValue('inputPackItems');
            $lines = array_unique(explode('-', $items));

            // lines is an array of string with format : QTYxIDxID_PRODUCT_ATTRIBUTE
            foreach ($lines as $line) {
                if (!empty($line)) {
                    $item_id_attribute = 0;
                    count($array = explode('x', $line)) == 3 ? list($qty, $item_id, $item_id_attribute) = $array : list($qty, $item_id) = $array;
                    if ($qty > 0) {
                        if (Pack::isPack((int) $item_id)) {
                            $this->errors[] = $this->trans('You can\'t add product packs into a pack', [], 'Admin.Catalog.Notification');
                        } elseif (!Pack::addItem((int) $product->id, (int) $item_id, (int) $qty, (int) $item_id_attribute)) {
                            $this->errors[] = $this->trans('An error occurred while attempting to add products to the pack.', [], 'Admin.Catalog.Notification');
                        }
                    }
                }
            }
        }
    }

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
					FROM `' . _DB_PREFIX_ . 'product` p
					LEFT JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop =' . (int) Context::getContext()->shop->id . ')
					LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
						ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = ' . (int) $id_lang . ')
					WHERE pl.`name` LIKE "%' . pSQL($search) . '%" AND ps.id_product IS NULL
					GROUP BY pl.`id_product`
					LIMIT ' . (int) $limit);
            }
            die(json_encode($result));
        }
    }

    public function ajaxProcessUpdatePositions()
    {
        if ($this->access('edit')) {
            $way = (bool) (Tools::getValue('way'));
            $id_product = (int) Tools::getValue('id_product');
            $id_category = (int) Tools::getValue('id_category');
            $positions = Tools::getValue('product');
            $page = (int) Tools::getValue('page');
            $selected_pagination = (int) Tools::getValue('selected_pagination');

            if (is_array($positions)) {
                foreach ($positions as $position => $value) {
                    $pos = explode('_', $value);

                    if ((isset($pos[1], $pos[2])) && ($pos[1] == $id_category && (int) $pos[2] === $id_product)) {
                        if ($page > 1) {
                            $position = $position + (($page - 1) * $selected_pagination);
                        }

                        $product = new Product((int) $pos[2]);
                        if (Validate::isLoadedObject($product)) {
                            if ($product->updatePosition($way, $position)) {
                                $category = new Category((int) $id_category);
                                if (Validate::isLoadedObject($category)) {
                                    Hook::exec('actionCategoryUpdate', ['category' => $category]);
                                }
                                echo 'ok position ' . (int) $position . ' for product ' . (int) $pos[2] . "\r\n";
                            } else {
                                echo '{"hasError" : true, "errors" : "Can not update product ' . (int) $id_product . ' to position ' . (int) $position . ' "}';
                            }
                        } else {
                            echo '{"hasError" : true, "errors" : "This product (' . (int) $id_product . ') can t be loaded"}';
                        }

                        break;
                    }
                }
            }
        }
    }

    public function ajaxProcessPublishProduct()
    {
        if ($this->access('edit')) {
            if ($id_product = (int) Tools::getValue('id_product')) {
                $bo_product_url = dirname($_SERVER['PHP_SELF']) . '/index.php?tab=AdminProducts&id_product=' . $id_product . '&updateproduct&token=' . $this->token;

                if (Tools::getValue('redirect')) {
                    die($bo_product_url);
                }

                $product = new Product((int) $id_product);
                if (!Validate::isLoadedObject($product)) {
                    die('error: invalid id');
                }

                $product->active = true;

                if ($product->save()) {
                    die($bo_product_url);
                } else {
                    die('error: saving');
                }
            }
        }
    }

    public function processImageLegends()
    {
        if (Tools::getValue('key_tab') == 'Images' && Tools::getValue('submitAddproductAndStay') == 'update_legends' && Validate::isLoadedObject($product = new Product((int) Tools::getValue('id_product')))) {
            $id_image = (int) Tools::getValue('id_caption');
            $language_ids = Language::getIDs(false);
            foreach ($_POST as $key => $val) {
                if (preg_match('/^legend_([0-9]+)/i', $key, $match)) {
                    foreach ($language_ids as $id_lang) {
                        if ($val && $id_lang == $match[1]) {
                            Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'image_lang SET legend = "' . pSQL($val) . '" WHERE ' . ($id_image ? 'id_image = ' . (int) $id_image : 'EXISTS (SELECT 1 FROM ' . _DB_PREFIX_ . 'image WHERE ' . _DB_PREFIX_ . 'image.id_image = ' . _DB_PREFIX_ . 'image_lang.id_image AND id_product = ' . (int) $product->id . ')') . ' AND id_lang = ' . (int) $id_lang);
                        }
                    }
                }
            }
        }
    }

    /**
     * Returns in an homemade JSON with the content of a products pack.
     */
    public function displayAjaxProductPackItems()
    {
        $jsonArray = [];
        $products = Db::getInstance()->executeS('
            SELECT p.`id_product`, pl.`name`
            FROM `' . _DB_PREFIX_ . 'product` p
            NATURAL LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
            WHERE pl.`id_lang` = ' . (int) (Tools::getValue('id_lang')) . '
            ' . Shop::addSqlRestrictionOnLang('pl') . '
            AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'pack` WHERE `id_product_pack` = p.`id_product`)
            AND p.`id_product` != ' . (int) (Tools::getValue('id_product')));

        foreach ($products as $packItem) {
            $jsonArray[] = '{"value": "' . (int) ($packItem['id_product']) . '-' . addslashes($packItem['name'])
                . '", "text":"' . (int) ($packItem['id_product']) . ' - ' . addslashes($packItem['name']) . '"}';
        }
        $this->ajaxRender('[' . implode(',', $jsonArray) . ']');
    }

    /**
     * Displays a list of products when their name matches a given query
     * Optional parameters allow products to be excluded from the results.
     */
    public function displayAjaxProductsList()
    {
        $query = Tools::getValue('q', false);
        if (empty($query)) {
            return;
        }

        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
         * they are no return values just because string:"(ref : #ref_pattern#)"
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if ($pos = strpos($query, ' (ref:')) {
            $query = substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        // Excluding downloadable products from packs because download from pack is not supported
        $forceJson = Tools::getValue('forceJson', false);
        $disableCombination = Tools::getValue('disableCombination', false);
        $excludeVirtuals = (bool) Tools::getValue('excludeVirtuals', true);
        $exclude_packs = (bool) Tools::getValue('exclude_packs', true);

        $context = Context::getContext();

        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`cache_default_attribute`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int) $context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $context->language->id . ')
                WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
                (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ') .
                ($excludeVirtuals ? 'AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
                ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
                ' GROUP BY p.id_product';

        $items = Db::getInstance()->executeS($sql);

        if ($items && ($disableCombination || $excludeIds)) {
            $results = $resultsJson = [];
            /** @var array{id_product: int, link_rewrite: string, reference: string, name: string, id_image: string} $item */
            foreach ($items as $item) {
                if (!$forceJson) {
                    $item['name'] = str_replace('|', '&#124;', $item['name']);
                    $results[] = trim($item['name']) . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : '') . '|' . (int) ($item['id_product']);
                } else {
                    $resultsJson[] = [
                        'id' => $item['id_product'],
                        'name' => $item['name'] . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : ''),
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], 'home_default')),
                    ];
                }
            }

            if (!$forceJson) {
                return $this->ajaxRender(implode(PHP_EOL, $results));
            }

            return $this->ajaxRender(json_encode($resultsJson));
        }
        if ($items) {
            // packs
            $results = [];
            foreach ($items as $item) {
                // check if product have combination
                if (Combination::isFeatureActive() && $item['cache_default_attribute']) {
                    $sql = 'SELECT pa.`id_product_attribute`, pa.`reference`, ag.`id_attribute_group`, pai.`id_image`, agl.`name` AS group_name, al.`name` AS attribute_name,
                                a.`id_attribute`
                            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                            WHERE pa.`id_product` = ' . (int) $item['id_product'] . '
                            GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                            ORDER BY pa.`id_product_attribute`';

                    $combinations = Db::getInstance()->executeS($sql);
                    if (!empty($combinations)) {
                        foreach ($combinations as $k => $combination) {
                            $results[$combination['id_product_attribute']]['id'] = $item['id_product'];
                            $results[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
                            !empty($results[$combination['id_product_attribute']]['name']) ? $results[$combination['id_product_attribute']]['name'] .= ' ' . $combination['group_name'] . '-' . $combination['attribute_name']
                            : $results[$combination['id_product_attribute']]['name'] = $item['name'] . ' ' . $combination['group_name'] . '-' . $combination['attribute_name'];
                            if (!empty($combination['reference'])) {
                                $results[$combination['id_product_attribute']]['ref'] = $combination['reference'];
                            } else {
                                $results[$combination['id_product_attribute']]['ref'] = !empty($item['reference']) ? $item['reference'] : '';
                            }
                            if (empty($results[$combination['id_product_attribute']]['image'])) {
                                $results[$combination['id_product_attribute']]['image'] = str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $combination['id_image'], 'home_default'));
                            }
                        }
                    } else {
                        $results[] = [
                            'id' => $item['id_product'],
                            'name' => $item['name'],
                            'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                            'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], 'home_default')),
                        ];
                    }
                } else {
                    $results[] = [
                        'id' => $item['id_product'],
                        'name' => $item['name'],
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], 'home_default')),
                    ];
                }
            }

            return $this->ajaxRender(json_encode(array_values($results)));
        }

        return $this->ajaxRender(json_encode([]));
    }
}
