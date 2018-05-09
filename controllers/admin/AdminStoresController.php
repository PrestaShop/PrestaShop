<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @property Store $object
 */
class AdminStoresControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'store';
        $this->className = 'Store';
        $this->lang = false;
        $this->toolbar_scroll = false;

        parent::__construct();

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->fieldImageSettings = array(
            'name' => 'image',
            'dir' => 'st'
        );

        $this->fields_list = array(
            'id_store' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'name' => array('title' => $this->trans('Name', array(), 'Admin.Global'), 'filter_key' => 'a!name'),
            'address1' => array('title' => $this->trans('Address', array(), 'Admin.Global'), 'filter_key' => 'a!address1'),
            'city' => array('title' => $this->trans('City', array(), 'Admin.Global')),
            'postcode' => array('title' => $this->trans('Zip/postal code', array(), 'Admin.Global')),
            'state' => array('title' => $this->trans('State', array(), 'Admin.Global'), 'filter_key' => 'st!name'),
            'country' => array('title' => $this->trans('Country', array(), 'Admin.Global'), 'filter_key' => 'cl!name'),
            'phone' => array('title' => $this->trans('Phone', array(), 'Admin.Global')),
            'fax' => array('title' => $this->trans('Fax', array(), 'Admin.Global')),
            'active' => array('title' => $this->trans('Enabled', array(), 'Admin.Global'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false)
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash'
            )
        );

        $this->_buildOrderedFieldsShop($this->_getDefaultFieldsContent());
    }

    public function renderOptions()
    {
        // Set toolbar options
        $this->display = 'options';
        $this->show_toolbar = true;
        $this->toolbar_scroll = true;
        $this->initToolbar();

        return parent::renderOptions();
    }

    public function initToolbar()
    {
        parent::initToolbar();

        if ($this->display == 'options') {
            unset($this->toolbar_btn['new']);
        } elseif ($this->display != 'add' && $this->display != 'edit') {
            unset($this->toolbar_btn['save']);
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_store'] = array(
                'href' => self::$currentIndex.'&addstore&token='.$this->token,
                'desc' => $this->trans('Add new store', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        // Set toolbar options
        $this->display = null;
        $this->initToolbar();

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = 'cl.`name` country, st.`name` state, sl.*';
        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl
                ON (cl.`id_country` = a.`id_country`
                AND cl.`id_lang` = ' . (int)$this->context->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'state` st
                ON (st.`id_state` = a.`id_state`)
            LEFT JOIN `' . _DB_PREFIX_ . 'store_lang` sl
                ON (sl.`id_store` = a.`id_store`
                AND sl.`id_lang` = ' . (int)$this->context->language->id . ') ';

        return parent::renderList();
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $image = _PS_STORE_IMG_DIR_.$obj->id.'.jpg';
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
                'title' => $this->trans('Stores', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-home'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Name', array(), 'Admin.Global'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => false,
                    'hint' => array(
                        $this->trans('Store name (e.g. City Center Mall Store).', array(), 'Admin.Shopparameters.Feature'),
                        $this->trans('Allowed characters: letters, spaces and %s', array(), 'Admin.Shopparameters.Feature')
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Address', array(), 'Admin.Global'),
                    'name' => 'address1',
                    'lang' => true,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Address (2)', array(), 'Admin.Global'),
                    'name' => 'address2',
                    'lang' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Zip/postal code', array(), 'Admin.Global'),
                    'name' => 'postcode',
                    'required' => in_array('postcode', $required_fields)
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('City', array(), 'Admin.Global'),
                    'name' => 'city',
                    'required' => true
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Country', array(), 'Admin.Global'),
                    'name' => 'id_country',
                    'required' => true,
                    'default_value' => (int)$this->context->country->id,
                    'options' => array(
                        'query' => Country::getCountries($this->context->language->id),
                        'id' => 'id_country',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('State', array(), 'Admin.Global'),
                    'name' => 'id_state',
                    'required' => true,
                    'options' => array(
                        'id' => 'id_state',
                        'name' => 'name',
                        'query' => null
                    )
                ),
                array(
                    'type' => 'latitude',
                    'label' => $this->trans('Latitude / Longitude', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'latitude',
                    'required' => true,
                    'maxlength' => 12,
                    'hint' => $this->trans('Store coordinates (e.g. 45.265469/-47.226478).', array(), 'Admin.Shopparameters.Feature')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Phone', array(), 'Admin.Global'),
                    'name' => 'phone'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Fax', array(), 'Admin.Global'),
                    'name' => 'fax'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Email address', array(), 'Admin.Global'),
                    'name' => 'email'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Note', array(), 'Admin.Global'),
                    'name' => 'note',
                    'lang' => true,
                    'cols' => 42,
                    'rows' => 4
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Active', array(), 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
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
                    ),
                    'hint' => $this->trans('Whether or not to display this store.', array(), 'Admin.Shopparameters.Help')
                ),
                array(
                    'type' => 'file',
                    'label' => $this->trans('Picture', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'image',
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'hint' => $this->trans('Storefront picture.', array(), 'Admin.Shopparameters.Help')
                )
            ),
            'hours' => array(
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $days = array();
        $days[1] = $this->trans('Monday', array(), 'Admin.Shopparameters.Feature');
        $days[2] = $this->trans('Tuesday', array(), 'Admin.Shopparameters.Feature');
        $days[3] = $this->trans('Wednesday', array(), 'Admin.Shopparameters.Feature');
        $days[4] = $this->trans('Thursday', array(), 'Admin.Shopparameters.Feature');
        $days[5] = $this->trans('Friday', array(), 'Admin.Shopparameters.Feature');
        $days[6] = $this->trans('Saturday', array(), 'Admin.Shopparameters.Feature');
        $days[7] = $this->trans('Sunday', array(), 'Admin.Shopparameters.Feature');

        $hours = array();

        $hours_temp = ($this->getFieldValue($obj, 'hours'));
        if (is_array($hours_temp) && !empty($hours_temp)) {
            $langs = Language::getLanguages(false);
            $hours_temp = array_map('json_decode', $hours_temp);
            $hours = array_map(
                array($this, 'adaptHoursFormat'),
                $hours_temp
            );
            $hours = (count($langs) > 1) ? $hours : $hours[reset($langs)['id_lang']];
        }

        $this->fields_value = array(
            'latitude' => $this->getFieldValue($obj, 'latitude') ? $this->getFieldValue($obj, 'latitude') : '',
            'longitude' => $this->getFieldValue($obj, 'longitude') ? $this->getFieldValue($obj, 'longitude') : '',
            'days' => $days,
            'hours' => $hours,
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (isset($_POST['submitAdd'.$this->table])) {
            $langs = Language::getLanguages(false);
            /* Cleaning fields */
            foreach ($_POST as $kp => $vp) {
                if (!in_array($kp, array('checkBoxShopGroupAsso_store', 'checkBoxShopAsso_store', 'hours'))) {
                    $_POST[$kp] = trim($vp);
                }
                if ('hours' === $kp) {
                    foreach ($vp as $day => $value) {
                        $_POST['hours'][$day] = is_array($value) ? array_map('trim', $_POST['hours'][$day]) : trim($value);
                    }
                }
            }

            /* Rewrite latitude and longitude to 8 digits */
            $_POST['latitude'] = number_format((float)$_POST['latitude'], 8);
            $_POST['longitude'] = number_format((float)$_POST['longitude'], 8);

            /* If the selected country does not contain states */
            $id_state = (int)Tools::getValue('id_state');
            $id_country = (int)Tools::getValue('id_country');
            $country = new Country((int)$id_country);

            if ($id_country && $country && !(int)$country->contains_states && $id_state) {
                $this->errors[] = $this->trans('You\'ve selected a state for a country that does not contain states.', array(), 'Admin.Advparameters.Notification');
            }

            /* If the selected country contains states, then a state have to be selected */
            if ((int)$country->contains_states && !$id_state) {
                $this->errors[] = $this->trans('An address located in a country containing states must have a state selected.', array(), 'Admin.Shopparameters.Notification');
            }

            $latitude = (float)Tools::getValue('latitude');
            $longitude = (float)Tools::getValue('longitude');

            if (empty($latitude) || empty($longitude)) {
                $this->errors[] = $this->trans('Latitude and longitude are required.', array(), 'Admin.Shopparameters.Notification');
            }

            $postcode = Tools::getValue('postcode');
            /* Check zip code format */
            if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                $this->errors[] = $this->trans('Your Zip/postal code is incorrect.', array(), 'Admin.Notifications.Error').'<br />'.$this->trans('It must be entered as follows:', array(), 'Admin.Notifications.Error').' '.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format)));
            } elseif (empty($postcode) && $country->need_zip_code) {
                $this->errors[] = $this->trans('A Zip/postal code is required.', array(), 'Admin.Notifications.Error');
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $this->errors[] = $this->trans('The Zip/postal code is invalid.', array(), 'Admin.Notifications.Error');
            }
            /* Store hours */
            foreach ($langs as $lang) {
                $hours = array();
                for ($i = 1; $i < 8; $i++) {
                    if (1 < count($langs)) {
                        $hours[] = explode(' | ', $_POST['hours'][$i][$lang['id_lang']]);
                        unset($_POST['hours'][$i][$lang['id_lang']]);
                    } else {
                        $hours[] = explode(' | ', $_POST['hours'][$i]);
                        unset($_POST['hours'][$i]);
                    }
                }
                $encodedHours[$lang['id_lang']] = json_encode($hours);
            }
            $_POST['hours'] = (1 < count($langs)) ? $encodedHours : json_encode($hours);
        }

        if (!count($this->errors)) {
            parent::postProcess();
        } else {
            $this->display = 'add';
        }
    }

    protected function postImage($id)
    {
        $ret = parent::postImage($id);
        $generate_hight_dpi_images = (bool)Configuration::get('PS_HIGHT_DPI');

        if (($id_store = (int)Tools::getValue('id_store')) && isset($_FILES) && count($_FILES) && file_exists(_PS_STORE_IMG_DIR_.$id_store.'.jpg')) {
            $images_types = ImageType::getImagesTypes('stores');
            foreach ($images_types as $image_type) {
                ImageManager::resize(_PS_STORE_IMG_DIR_.$id_store.'.jpg',
                    _PS_STORE_IMG_DIR_.$id_store.'-'.stripslashes($image_type['name']).'.jpg',
                    (int)$image_type['width'], (int)$image_type['height']
                );

                if ($generate_hight_dpi_images) {
                    ImageManager::resize(_PS_STORE_IMG_DIR_.$id_store.'.jpg',
                        _PS_STORE_IMG_DIR_.$id_store.'-'.stripslashes($image_type['name']).'2x.jpg',
                        (int)$image_type['width']*2, (int)$image_type['height']*2
                    );
                }
            }
        }
        return $ret;
    }

    protected function _getDefaultFieldsContent()
    {
        $this->context = Context::getContext();
        $countryList = array();
        $countryList[] = array('id' => '0', 'name' => $this->trans('Choose your country', array(), 'Admin.Shopparameters.Feature'));
        foreach (Country::getCountries($this->context->language->id) as $country) {
            $countryList[] = array('id' => $country['id_country'], 'name' => $country['name']);
        }
        $stateList = array();
        $stateList[] = array('id' => '0', 'name' => $this->trans('Choose your state (if applicable)', array(), 'Admin.Shopparameters.Feature'));
        foreach (State::getStates($this->context->language->id) as $state) {
            $stateList[] = array('id' => $state['id_state'], 'name' => $state['name']);
        }

        $formFields = array(
            'PS_SHOP_NAME' => array(
                'title' => $this->trans('Shop name', array(), 'Admin.Shopparameters.Feature'),
                'hint' => $this->trans('Displayed in emails and page titles.', array(), 'Admin.Shopparameters.Feature'),
                'validation' => 'isGenericName',
                'required' => true,
                'type' => 'text',
                'no_escape' => true,
            ),
            'PS_SHOP_EMAIL' => array('title' => $this->trans('Shop email', array(), 'Admin.Shopparameters.Feature'),
                'hint' => $this->trans('Displayed in emails sent to customers.', array(), 'Admin.Shopparameters.Help'),
                'validation' => 'isEmail',
                'required' => true,
                'type' => 'text'
            ),
            'PS_SHOP_DETAILS' => array(
                'title' => $this->trans('Registration number', array(), 'Admin.Shopparameters.Feature'),
                'hint' => $this->trans('Shop registration information (e.g. SIRET or RCS).', array(), 'Admin.Shopparameters.Help'),
                'validation' => 'isGenericName',
                'type' => 'textarea',
                'cols' => 30,
                'rows' => 5
            ),
            'PS_SHOP_ADDR1' => array(
                'title' => $this->trans('Shop address line 1', array(), 'Admin.Shopparameters.Feature'),
                'validation' => 'isAddress',
                'type' => 'text'
            ),
            'PS_SHOP_ADDR2' => array(
                'title' => $this->trans('Shop address line 2', array(), 'Admin.Shopparameters.Feature'),
                'validation' => 'isAddress',
                'type' => 'text'
            ),
            'PS_SHOP_CODE' => array(
                'title' => $this->trans('Zip/postal code', array(), 'Admin.Global'),
                'validation' => 'isGenericName',
                'type' => 'text'
            ),
            'PS_SHOP_CITY' => array(
                'title' => $this->trans('City', array(), 'Admin.Global'),
                'validation' => 'isGenericName',
                'type' => 'text'
            ),
            'PS_SHOP_COUNTRY_ID' => array(
                'title' => $this->trans('Country', array(), 'Admin.Global'),
                'validation' => 'isInt',
                'type' => 'select',
                'list' => $countryList,
                'identifier' => 'id',
                'cast' => 'intval',
                'defaultValue' => (int)$this->context->country->id
            ),
            'PS_SHOP_STATE_ID' => array(
                'title' => $this->trans('State', array(), 'Admin.Global'),
                'validation' => 'isInt',
                'type' => 'select',
                'list' => $stateList,
                'identifier' => 'id',
                'cast' => 'intval'
            ),
            'PS_SHOP_PHONE' => array(
                'title' => $this->trans('Phone', array(), 'Admin.Global'),
                'validation' => 'isGenericName',
                'type' => 'text'
            ),
            'PS_SHOP_FAX' => array(
                'title' => $this->trans('Fax', array(), 'Admin.Global'),
                'validation' => 'isGenericName',
                'type' => 'text'
            ),
        );

        return $formFields;
    }

    protected function _buildOrderedFieldsShop($formFields)
    {
        // You cannot do that, because the fields must be sorted for the country you've selected.
        // Simple example: the current country is France, where we don't display the state. You choose "US" as a country in the form. The state is not dsplayed at the right place...

        // $associatedOrderKey = array(
            // 'PS_SHOP_NAME' => 'company',
            // 'PS_SHOP_ADDR1' => 'address1',
            // 'PS_SHOP_ADDR2' => 'address2',
            // 'PS_SHOP_CITY' => 'city',
            // 'PS_SHOP_STATE_ID' => 'State:name',
            // 'PS_SHOP_CODE' => 'postcode',
            // 'PS_SHOP_COUNTRY_ID' => 'Country:name',
            // 'PS_SHOP_PHONE' => 'phone');
        // $fields = array();
        // $orderedFields = AddressFormat::getOrderedAddressFields(Configuration::get('PS_SHOP_COUNTRY_ID'), false, true);
        // foreach ($orderedFields as $lineFields)
            // if (($patterns = explode(' ', $lineFields)))
                // foreach ($patterns as $pattern)
                    // if (($key = array_search($pattern, $associatedOrderKey)))
                        // $fields[$key] = $formFields[$key];
        // foreach ($formFields as $key => $value)
            // if (!isset($fields[$key]))
                // $fields[$key] = $formFields[$key];

        $fields = $formFields;
        $this->fields_options['contact'] = array(
            'title' =>    $this->trans('Contact details', array(), 'Admin.Shopparameters.Feature'),
            'icon' =>    'icon-user',
            'fields' =>    $fields,
            'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
        );
    }

    public function beforeUpdateOptions()
    {
        if (isset($_POST['PS_SHOP_STATE_ID']) && $_POST['PS_SHOP_STATE_ID'] != '0') {
            $sql = 'SELECT `active` FROM `'._DB_PREFIX_.'state`
					WHERE `id_country` = '.(int)Tools::getValue('PS_SHOP_COUNTRY_ID').'
						AND `id_state` = '.(int)Tools::getValue('PS_SHOP_STATE_ID');
            $isStateOk = Db::getInstance()->getValue($sql);
            if ($isStateOk != 1) {
                $this->errors[] = $this->trans('The specified state is not located in this country.', array(), 'Admin.Shopparameters.Notification');
            }
        }
    }

    public function updateOptionPsShopCountryId($value)
    {
        if (!$this->errors && $value) {
            $country = new Country($value, $this->context->language->id);
            if ($country->id) {
                Configuration::updateValue('PS_SHOP_COUNTRY_ID', $value);
                Configuration::updateValue('PS_SHOP_COUNTRY', pSQL($country->name));
            }
        }
    }

    public function updateOptionPsShopStateId($value)
    {
        if (!$this->errors && $value) {
            $state = new State($value);
            if ($state->id) {
                Configuration::updateValue('PS_SHOP_STATE_ID', $value);
                Configuration::updateValue('PS_SHOP_STATE', pSQL($state->name));
            }
        }
    }

    /**
     * Adapt the format of hours
     * 
     * @param array $value
     * @return array
     */
    protected function adaptHoursFormat($value)
    {
        $separator = array_fill(0, count($value), ' | ');
        
        return array_map('implode', $value, $separator);
    }
}
