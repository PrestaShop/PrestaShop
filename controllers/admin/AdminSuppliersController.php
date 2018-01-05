<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @property Supplier $object
 */
class AdminSuppliersControllerCore extends AdminController
{
    public $bootstrap = true ;

    public function __construct()
    {
        $this->table = 'supplier';
        $this->className = 'Supplier';

        parent::__construct();

        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = true;

        $this->_defaultOrderBy = 'name';
        $this->_defaultOrderWay = 'ASC';

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning')
            )
        );

        $this->_select = 'COUNT(DISTINCT ps.`id_product`) AS products';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (a.`id_supplier` = ps.`id_supplier`)';
        $this->_group = 'GROUP BY a.`id_supplier`';

        $this->fieldImageSettings = array('name' => 'logo', 'dir' => 'su');

        $this->fields_list = array(
            'id_supplier' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'logo' => array('title' => $this->trans('Logo', array(), 'Admin.Global'), 'align' => 'center', 'image' => 'su', 'orderby' => false, 'search' => false),
            'name' => array('title' => $this->trans('Name', array(), 'Admin.Global')),
            'products' => array('title' => $this->trans('Number of products', array(), 'Admin.Catalog.Feature'), 'align' => 'right', 'filter_type' => 'int', 'tmpTableFilter' => true),
            'active' => array('title' => $this->trans('Enabled', array(), 'Admin.Global'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'class' => 'fixed-width-xs')
        );
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_supplier'] = array(
                'href' => self::$currentIndex.'&addsupplier&token='.$this->token,
                'desc' => $this->trans('Add new supplier', array(), 'Admin.Catalog.Feature'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        // loads current warehouse
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $image = _PS_SUPP_IMG_DIR_.$obj->id.'.jpg';
        $image_url = ImageManager::thumbnail($image, $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 350,
            $this->imageType, true, true);
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

        $tmp_addr = new Address();
        $res = $tmp_addr->getFieldsRequiredDatabase();
        $required_fields = array();
        foreach ($res as $row) {
            $required_fields[(int)$row['id_required_field']] = $row['field_name'];
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Suppliers', array(), 'Admin.Global'),
                'icon' => 'icon-truck'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_address',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Name', array(), 'Admin.Global'),
                    'name' => 'name',
                    'required' => true,
                    'col' => 4,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}',
                ),
                (in_array('company', $required_fields) ?
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Company', array(), 'Admin.Global'),
                        'name' => 'company',
                        'display' => in_array('company', $required_fields),
                        'required' => in_array('company', $required_fields),
                        'maxlength' => 16,
                        'col' => 4,
                        'hint' => $this->trans('Company name for this supplier', array(), 'Admin.Catalog.Help')
                    )
                    : null
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Description', array(), 'Admin.Global'),
                    'name' => 'description',
                    'lang' => true,
                    'hint' => array(
                        $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}',
                        $this->trans('Will appear in the list of suppliers.', array(), 'Admin.Catalog.Help')
                    ),
                    'autoload_rte' => 'rte' //Enable TinyMCE editor for short description
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Phone', array(), 'Admin.Global'),
                    'name' => 'phone',
                    'required' => in_array('phone', $required_fields),
                    'maxlength' => 16,
                    'col' => 4,
                    'hint' => $this->trans('Phone number for this supplier', array(), 'Admin.Catalog.Help')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Mobile phone', array(), 'Admin.Global'),
                    'name' => 'phone_mobile',
                    'required' => in_array('phone_mobile', $required_fields),
                    'maxlength' => 16,
                    'col' => 4,
                    'hint' => $this->trans('Mobile phone number for this supplier.', array(), 'Admin.Catalog.Help')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Address', array(), 'Admin.Global'),
                    'name' => 'address',
                    'maxlength' => 128,
                    'col' => 6,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Address (2)', array(), 'Admin.Global'),
                    'name' => 'address2',
                    'required' => in_array('address2', $required_fields),
                    'col' => 6,
                    'maxlength' => 128,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Zip/postal code', array(), 'Admin.Global'),
                    'name' => 'postcode',
                    'required' => in_array('postcode', $required_fields),
                    'maxlength' => 12,
                    'col' => 2,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('City', array(), 'Admin.Global'),
                    'name' => 'city',
                    'maxlength' => 32,
                    'col' => 4,
                    'required' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Country', array(), 'Admin.Global'),
                    'name' => 'id_country',
                    'required' => true,
                    'col' => 4,
                    'default_value' => (int)$this->context->country->id,
                    'options' => array(
                        'query' => Country::getCountries($this->context->language->id, false),
                        'id' => 'id_country',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('State', array(), 'Admin.Global'),
                    'name' => 'id_state',
                    'col' => 4,
                    'options' => array(
                        'id' => 'id_state',
                        'query' => array(),
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'file',
                    'label' => $this->trans('Logo', array(), 'Admin.Global'),
                    'name' => 'logo',
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'hint' => $this->trans('Upload a supplier logo from your computer.', array(), 'Admin.Catalog.Help')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Meta title', array(), 'Admin.Global'),
                    'name' => 'meta_title',
                    'lang' => true,
                    'col' => 4,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Meta description', array(), 'Admin.Global'),
                    'name' => 'meta_description',
                    'lang' => true,
                    'col' => 6,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->trans('Meta keywords', array(), 'Admin.Global'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'col' => 6,
                    'hint' => array(
                        $this->trans('To add "tags" click in the field, write something and then press "Enter".', array(), 'Admin.Catalog.Help'),
                        $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Enable', array(), 'Admin.Actions'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global')
                        )
                    )
                )
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            )
        );

        // loads current address for this supplier - if possible
        $address = null;
        if (isset($obj->id)) {
            $id_address = Address::getAddressIdBySupplierId($obj->id);

            if ($id_address > 0) {
                $address = new Address((int)$id_address);
            }
        }

        // force specific fields values (address)
        if ($address != null) {
            $this->fields_value = array(
                'id_address' => $address->id,
                'phone' => $address->phone,
                'phone_mobile' => $address->phone_mobile,
                'address' => $address->address1,
                'address2' => $address->address2,
                'postcode' => $address->postcode,
                'city' => $address->city,
                'id_country' => $address->id_country,
                'id_state' => $address->id_state,
            );
        } else {
            $this->fields_value = array(
                'id_address' => 0,
                'id_country' => Configuration::get('PS_COUNTRY_DEFAULT')
            );
        }


        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            );
        }

        return parent::renderForm();
    }

    /**
     * AdminController::initToolbar() override
     * @see AdminController::initToolbar()
     *
     */
    public function initToolbar()
    {
        parent::initToolbar();
        $this->addPageHeaderToolBarModulesListButton();

        if (empty($this->display) && $this->can_import) {
            $this->toolbar_btn['import'] = array(
                'href' => $this->context->link->getAdminLink('AdminImport', true).'&import_type=suppliers',
                'desc' => $this->trans('Import', array(), 'Admin.Actions')
            );
        }
    }

    public function renderView()
    {
        $this->initTabModuleList();
        $this->toolbar_title = $this->object->name;
        $products = $this->object->getProductsLite($this->context->language->id);
        $total_product = count($products);

        for ($i = 0; $i < $total_product; $i++) {
            $products[$i] = new Product($products[$i]['id_product'], false, $this->context->language->id);
            $products[$i]->loadStockData();
            // Build attributes combinations
            $combinations = $products[$i]->getAttributeCombinations($this->context->language->id);
            foreach ($combinations as $k => $combination) {
                $comb_infos = Supplier::getProductInformationsBySupplier($this->object->id,
                                                                         $products[$i]->id,
                                                                         $combination['id_product_attribute']);
                $comb_array[$combination['id_product_attribute']]['product_supplier_reference'] = $comb_infos['product_supplier_reference'];
                $comb_array[$combination['id_product_attribute']]['product_supplier_price_te'] = Tools::displayPrice($comb_infos['product_supplier_price_te'], new Currency($comb_infos['id_currency']));
                $comb_array[$combination['id_product_attribute']]['reference'] = $combination['reference'];
                $comb_array[$combination['id_product_attribute']]['ean13'] = $combination['ean13'];
                $comb_array[$combination['id_product_attribute']]['upc'] = $combination['upc'];
                $comb_array[$combination['id_product_attribute']]['quantity'] = $combination['quantity'];
                $comb_array[$combination['id_product_attribute']]['attributes'][] = array(
                    $combination['group_name'],
                    $combination['attribute_name'],
                    $combination['id_attribute']
                );
            }

            if (isset($comb_array)) {
                foreach ($comb_array as $key => $product_attribute) {
                    $list = '';
                    foreach ($product_attribute['attributes'] as $attribute) {
                        $list .= $attribute[0].' - '.$attribute[1].', ';
                    }
                    $comb_array[$key]['attributes'] = rtrim($list, ', ');
                }
                isset($comb_array) ? $products[$i]->combination = $comb_array : '';
                unset($comb_array);
            } else {
                $product_infos = Supplier::getProductInformationsBySupplier($this->object->id,
                                                                            $products[$i]->id,
                                                                            0);
                $products[$i]->product_supplier_reference = $product_infos['product_supplier_reference'];
                $products[$i]->product_supplier_price_te = Tools::displayPrice($product_infos['product_supplier_price_te'], new Currency($product_infos['id_currency']));
            }
        }

        $this->tpl_view_vars = array(
            'supplier' => $this->object,
            'products' => $products,
            'stock_management' => Configuration::get('PS_STOCK_MANAGEMENT'),
            'shopContext' => Shop::getContext(),
        );

        return parent::renderView();
    }

    protected function afterImageUpload()
    {
        $return = true;
        $generate_hight_dpi_images = (bool)Configuration::get('PS_HIGHT_DPI');

        /* Generate image with differents size */
        if (($id_supplier = (int)Tools::getValue('id_supplier')) &&
             isset($_FILES) && count($_FILES) && file_exists(_PS_SUPP_IMG_DIR_.$id_supplier.'.jpg')) {
            $images_types = ImageType::getImagesTypes('suppliers');
            foreach ($images_types as $k => $image_type) {
                $file = _PS_SUPP_IMG_DIR_.$id_supplier.'.jpg';
                if (!ImageManager::resize($file, _PS_SUPP_IMG_DIR_.$id_supplier.'-'.stripslashes($image_type['name']).'.jpg', (int)$image_type['width'], (int)$image_type['height'])) {
                    $return = false;
                }

                if ($generate_hight_dpi_images) {
                    if (!ImageManager::resize($file, _PS_SUPP_IMG_DIR_.$id_supplier.'-'.stripslashes($image_type['name']).'2x.jpg', (int)$image_type['width']*2, (int)$image_type['height']*2)) {
                        $return = false;
                    }
                }
            }

            $current_logo_file = _PS_TMP_IMG_DIR_.'supplier_mini_'.$id_supplier.'_'.$this->context->shop->id.'.jpg';

            if (file_exists($current_logo_file)) {
                unlink($current_logo_file);
            }
        }
        return $return;
    }

    /**
     * AdminController::postProcess() override
     * @see AdminController::postProcess()
     */
    public function postProcess()
    {
        // checks access
        if (Tools::isSubmit('submitAdd'.$this->table) && !($this->access('add'))) {
            $this->errors[] = $this->trans('You do not have permission to add suppliers.', array(), 'Admin.Catalog.Notification');
            return parent::postProcess();
        }

        if (Tools::isSubmit('submitAdd'.$this->table)) {
            if (Tools::isSubmit('id_supplier') && !($obj = $this->loadObject(true))) {
                return;
            }

            // updates/creates address if it does not exist
            if (Tools::isSubmit('id_address') && (int)Tools::getValue('id_address') > 0) {
                $address = new Address((int)Tools::getValue('id_address'));
            } // updates address
            else {
                $address = new Address();
            } // creates address

            $address->alias = Tools::getValue('name', null);
            $address->lastname = 'supplier'; // skip problem with numeric characters in supplier name
            $address->firstname = 'supplier'; // skip problem with numeric characters in supplier name
            $address->address1 = Tools::getValue('address', null);
            $address->address2 = Tools::getValue('address2', null);
            $address->postcode = Tools::getValue('postcode', null);
            $address->phone = Tools::getValue('phone', null);
            $address->phone_mobile = Tools::getValue('phone_mobile', null);
            $address->id_country = Tools::getValue('id_country', null);
            $address->id_state = Tools::getValue('id_state', null);
            $address->city = Tools::getValue('city', null);

            $validation = $address->validateController();

            // checks address validity
            if (count($validation) > 0) {
                foreach ($validation as $item) {
                    $this->errors[] = $item;
                }
                $this->errors[] = $this->trans('The address is not correct. Please make sure all of the required fields are completed.', array(), 'Admin.Catalog.Notification');
            } else {
                if (Tools::isSubmit('id_address') && Tools::getValue('id_address') > 0) {
                    $address->update();
                } else {
                    $address->save();
                    $_POST['id_address'] = $address->id;
                }
            }
            return parent::postProcess();
        } elseif (Tools::isSubmit('delete'.$this->table)) {
            if (!($obj = $this->loadObject(true))) {
                return;
            } elseif (SupplyOrder::supplierHasPendingOrders($obj->id)) {
                $this->errors[] = $this->trans('It is not possible to delete a supplier if there are pending supplier orders.', array(), 'Admin.Catalog.Notification');
            } else {
                //delete all product_supplier linked to this supplier
                Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_supplier` WHERE `id_supplier`='.(int)$obj->id);

                $id_address = Address::getAddressIdBySupplierId($obj->id);
                $address = new Address($id_address);
                if (Validate::isLoadedObject($address)) {
                    $address->deleted = 1;
                    $address->save();
                }
                return parent::postProcess();
            }
        } else {
            return parent::postProcess();
        }
    }

    /**
     * @see AdminController::afterAdd()
     *
     * @param Supplier $object
     *
     * @return bool
     */
    protected function afterAdd($object)
    {
        $id_address = (int)$_POST['id_address'];
        $address = new Address($id_address);
        if (Validate::isLoadedObject($address)) {
            $address->id_supplier = $object->id;
            $address->save();
        }

        return true;
    }

    /**
     * @see AdminController::afterUpdate()
     *
     * @param Supplier $object
     *
     * @return bool
     */
    protected function afterUpdate($object)
    {
        $id_address = (int)$_POST['id_address'];
        $address = new Address($id_address);
        if (Validate::isLoadedObject($address)) {
            if ($address->id_supplier != $object->id) {
                $address->id_supplier = $object->id;
                $address->save();
            }
        }
        return true;
    }
}
