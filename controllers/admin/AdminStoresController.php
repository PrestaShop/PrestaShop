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

        $this->fieldImageSettings = [
            'name' => 'image',
            'dir' => 'st',
        ];

        $this->fields_list = [
            'id_store' => ['title' => $this->trans('ID', [], 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'],
            'name' => ['title' => $this->trans('Name', [], 'Admin.Global'), 'filter_key' => 'sl!name'],
            'address1' => ['title' => $this->trans('Address', [], 'Admin.Global'), 'filter_key' => 'sl!address1'],
            'city' => ['title' => $this->trans('City', [], 'Admin.Global')],
            'postcode' => ['title' => $this->trans('Zip/Postal code', [], 'Admin.Global')],
            'state' => ['title' => $this->trans('State', [], 'Admin.Global'), 'filter_key' => 'st!name'],
            'country' => ['title' => $this->trans('Country', [], 'Admin.Global'), 'filter_key' => 'cl!name'],
            'phone' => ['title' => $this->trans('Phone', [], 'Admin.Global')],
            'fax' => ['title' => $this->trans('Fax', [], 'Admin.Global')],
            'active' => ['title' => $this->trans('Enabled', [], 'Admin.Global'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];

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
            $this->page_header_toolbar_btn['new_store'] = [
                'href' => self::$currentIndex . '&addstore&token=' . $this->token,
                'desc' => $this->trans('Add new store', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'process-icon-new',
            ];
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
                AND cl.`id_lang` = ' . (int) $this->context->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'state` st
                ON (st.`id_state` = a.`id_state`)
            LEFT JOIN `' . _DB_PREFIX_ . 'store_lang` sl
                ON (sl.`id_store` = a.`id_store`
                AND sl.`id_lang` = ' . (int) $this->context->language->id . ') ';

        return parent::renderList();
    }

    /**
     * @return string|void
     *
     * @throws PrestaShopDatabaseException
     * @throws SmartyException
     */
    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $image = _PS_STORE_IMG_DIR_ . $obj->id . '.jpg';
        $image_url = ImageManager::thumbnail(
            $image,
            $this->table . '_' . (int) $obj->id . '.' . $this->imageType,
            350,
            $this->imageType,
            true,
            true
        );
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

        $tmp_addr = new Address();
        $res = $tmp_addr->getFieldsRequiredDatabase();
        $required_fields = [];
        foreach ($res as $row) {
            $required_fields[(int) $row['id_required_field']] = $row['field_name'];
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Stores', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-home',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => false,
                    'hint' => [
                        $this->trans('Store name (e.g. City Center Mall Store).', [], 'Admin.Shopparameters.Feature'),
                        $this->trans('Allowed characters: letters, spaces and %s', [], 'Admin.Shopparameters.Feature'),
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Address', [], 'Admin.Global'),
                    'name' => 'address1',
                    'lang' => true,
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Address (2)', [], 'Admin.Global'),
                    'name' => 'address2',
                    'lang' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Zip/Postal code', [], 'Admin.Global'),
                    'name' => 'postcode',
                    'required' => in_array('postcode', $required_fields),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('City', [], 'Admin.Global'),
                    'name' => 'city',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Country', [], 'Admin.Global'),
                    'name' => 'id_country',
                    'required' => true,
                    'default_value' => (int) $this->context->country->id,
                    'options' => [
                        'query' => Country::getCountries($this->context->language->id),
                        'id' => 'id_country',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('State', [], 'Admin.Global'),
                    'name' => 'id_state',
                    'required' => true,
                    'options' => [
                        'id' => 'id_state',
                        'name' => 'name',
                        'query' => null,
                    ],
                ],
                [
                    'type' => 'latitude',
                    'label' => $this->trans('Latitude / Longitude', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'latitude',
                    'required' => true,
                    'maxlength' => 12,
                    'hint' => $this->trans('Store coordinates (e.g. 45.265469/-47.226478).', [], 'Admin.Shopparameters.Feature'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Phone', [], 'Admin.Global'),
                    'name' => 'phone',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Fax', [], 'Admin.Global'),
                    'name' => 'fax',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Email address', [], 'Admin.Global'),
                    'name' => 'email',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Note', [], 'Admin.Global'),
                    'name' => 'note',
                    'lang' => true,
                    'cols' => 42,
                    'rows' => 4,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Active', [], 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    ],
                    'hint' => $this->trans('Whether or not to display this store.', [], 'Admin.Shopparameters.Help'),
                ],
                [
                    'type' => 'file',
                    'label' => $this->trans('Picture', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'image',
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'hint' => $this->trans('Storefront picture.', [], 'Admin.Shopparameters.Help'),
                ],
            ],
            'hours' => [
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->trans('Store association', [], 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        $days = [];
        $days[1] = $this->trans('Monday', [], 'Admin.Shopparameters.Feature');
        $days[2] = $this->trans('Tuesday', [], 'Admin.Shopparameters.Feature');
        $days[3] = $this->trans('Wednesday', [], 'Admin.Shopparameters.Feature');
        $days[4] = $this->trans('Thursday', [], 'Admin.Shopparameters.Feature');
        $days[5] = $this->trans('Friday', [], 'Admin.Shopparameters.Feature');
        $days[6] = $this->trans('Saturday', [], 'Admin.Shopparameters.Feature');
        $days[7] = $this->trans('Sunday', [], 'Admin.Shopparameters.Feature');

        $hours = [];

        $hours_temp = $this->getFieldValue($obj, 'hours');
        if (is_array($hours_temp) && !empty($hours_temp)) {
            $langs = Language::getLanguages(false);
            $hours_temp = array_map('json_decode', $hours_temp);
            $hours = array_map(
                [$this, 'adaptHoursFormat'],
                $hours_temp
            );
            $hours = (count($langs) > 1) ? $hours : $hours[reset($langs)['id_lang']];
        }

        $this->fields_value = [
            'latitude' => $this->getFieldValue($obj, 'latitude') ? $this->getFieldValue($obj, 'latitude') : '',
            'longitude' => $this->getFieldValue($obj, 'longitude') ? $this->getFieldValue($obj, 'longitude') : '',
            'days' => $days,
            'hours' => $hours,
        ];

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (isset($_POST['submitAdd' . $this->table])) {
            $langs = Language::getLanguages(false);
            /* Cleaning fields */
            foreach ($_POST as $kp => $vp) {
                if (is_string($vp)) {
                    $_POST[$kp] = trim($vp);
                }
                if ('hours' === $kp) {
                    foreach ($vp as $day => $value) {
                        $_POST['hours'][$day] = is_array($value) ? array_map('trim', $_POST['hours'][$day]) : trim($value);
                    }
                }
            }

            /* Rewrite latitude and longitude to 8 digits */
            $_POST['latitude'] = number_format((float) $_POST['latitude'], 8);
            $_POST['longitude'] = number_format((float) $_POST['longitude'], 8);

            /* If the selected country does not contain states */
            $id_state = (int) Tools::getValue('id_state');
            $id_country = (int) Tools::getValue('id_country');
            $country = new Country($id_country);

            if ($id_country && !(int) $country->contains_states && $id_state) {
                $this->errors[] = $this->trans('You\'ve selected a state for a country that does not contain states.', [], 'Admin.Advparameters.Notification');
            }

            /* If the selected country contains states, then a state have to be selected */
            if ((int) $country->contains_states && !$id_state) {
                $this->errors[] = $this->trans('An address located in a country containing states must have a state selected.', [], 'Admin.Shopparameters.Notification');
            }

            $latitude = (float) Tools::getValue('latitude');
            $longitude = (float) Tools::getValue('longitude');

            if (empty($latitude) || empty($longitude)) {
                $this->errors[] = $this->trans('Latitude and longitude are required.', [], 'Admin.Shopparameters.Notification');
            }

            $postcode = Tools::getValue('postcode');
            /* Check zip code format */
            if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                $this->errors[] = $this->trans('Your Zip/Postal code is incorrect.', [], 'Admin.Notifications.Error') . '<br />' . $this->trans('It must be entered as follows:', [], 'Admin.Notifications.Error') . ' ' . str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format)));
            } elseif (empty($postcode) && $country->need_zip_code) {
                $this->errors[] = $this->trans('A Zip/Postal code is required.', [], 'Admin.Notifications.Error');
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $this->errors[] = $this->trans('The Zip/Postal code is invalid.', [], 'Admin.Notifications.Error');
            }
            /* Store hours */
            $encodedHours = [];
            foreach ($langs as $lang) {
                $hours = [];
                for ($i = 1; $i < 8; ++$i) {
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
            $_POST['hours'] = (1 < count($langs)) ? $encodedHours : json_encode($hours ?? []);
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

        // Should we generate high DPI images?
        $generate_hight_dpi_images = (bool) Configuration::get('PS_HIGHT_DPI');

        /*
        * Let's resolve which formats we will use for image generation.
        * In new image system, it's multiple formats. In case of legacy, it's only .jpg.
        *
        * In case of .jpg images, the actual format inside is decided by ImageManager.
        */
        $sfContainer = SymfonyContainer::getInstance();
        if ($sfContainer->get('prestashop.core.admin.feature_flag.repository')->isEnabled(FeatureFlagSettings::FEATURE_FLAG_MULTIPLE_IMAGE_FORMAT)) {
            $configuredImageFormats = $sfContainer->get(ImageFormatConfiguration::class)->getGenerationFormats();
        } else {
            $configuredImageFormats = ['jpg'];
        }

        if (($id_store = (int) Tools::getValue('id_store')) && count($_FILES) && file_exists(_PS_STORE_IMG_DIR_ . $id_store . '.jpg')) {
            $images_types = ImageType::getImagesTypes('stores');
            foreach ($images_types as $image_type) {
                foreach ($configuredImageFormats as $imageFormat) {
                    // For JPG images, we let Imagemanager decide what to do and choose between JPG/PNG.
                    // For webp and avif extensions, we want it to follow our command and ignore the original format.
                    $forceFormat = ($imageFormat !== 'jpg');
                    ImageManager::resize(
                        _PS_STORE_IMG_DIR_ . $id_store . '.jpg',
                        _PS_STORE_IMG_DIR_ . $id_store . '-' . stripslashes($image_type['name']) . '.' . $imageFormat,
                        (int) $image_type['width'],
                        (int) $image_type['height'],
                        $imageFormat,
                        $forceFormat
                    );

                    if ($generate_hight_dpi_images) {
                        ImageManager::resize(
                            _PS_STORE_IMG_DIR_ . $id_store . '.jpg',
                            _PS_STORE_IMG_DIR_ . $id_store . '-' . stripslashes($image_type['name']) . '2x.' . $imageFormat,
                            (int) $image_type['width'] * 2,
                            (int) $image_type['height'] * 2,
                            $imageFormat,
                            $forceFormat
                        );
                    }
                }
            }
        }

        return $ret;
    }

    protected function _getDefaultFieldsContent()
    {
        $this->context = Context::getContext();
        $countryList = [];
        $countryList[] = ['id' => '0', 'name' => $this->trans('Choose your country', [], 'Admin.Shopparameters.Feature')];
        foreach (Country::getCountries($this->context->language->id) as $country) {
            $countryList[] = ['id' => $country['id_country'], 'name' => $country['name']];
        }
        $stateList = [];
        $stateList[] = ['id' => '0', 'name' => $this->trans('Choose your state (if applicable)', [], 'Admin.Shopparameters.Feature')];
        foreach (State::getStates($this->context->language->id) as $state) {
            $stateList[] = ['id' => $state['id_state'], 'name' => $state['name']];
        }

        $formFields = [
            'PS_SHOP_NAME' => [
                'title' => $this->trans('Store name', [], 'Admin.Shopparameters.Feature'),
                'hint' => $this->trans('Displayed in emails and page titles.', [], 'Admin.Shopparameters.Feature'),
                'validation' => 'isGenericName',
                'required' => true,
                'type' => 'text',
                'no_escape' => true,
            ],
            'PS_SHOP_EMAIL' => ['title' => $this->trans('Shop email', [], 'Admin.Shopparameters.Feature'),
                'hint' => $this->trans('Displayed in emails sent to customers.', [], 'Admin.Shopparameters.Help'),
                'validation' => 'isEmail',
                'required' => true,
                'type' => 'text',
            ],
            'PS_SHOP_DETAILS' => [
                'title' => $this->trans('Registration number', [], 'Admin.Shopparameters.Feature'),
                'hint' => $this->trans('Shop registration information (e.g. SIRET or RCS).', [], 'Admin.Shopparameters.Help'),
                'validation' => 'isGenericName',
                'type' => 'textarea',
                'cols' => 30,
                'rows' => 5,
            ],
            'PS_SHOP_ADDR1' => [
                'title' => $this->trans('Shop address line 1', [], 'Admin.Shopparameters.Feature'),
                'validation' => 'isAddress',
                'type' => 'text',
            ],
            'PS_SHOP_ADDR2' => [
                'title' => $this->trans('Shop address line 2', [], 'Admin.Shopparameters.Feature'),
                'validation' => 'isAddress',
                'type' => 'text',
            ],
            'PS_SHOP_CODE' => [
                'title' => $this->trans('Zip/Postal code', [], 'Admin.Global'),
                'validation' => 'isGenericName',
                'type' => 'text',
            ],
            'PS_SHOP_CITY' => [
                'title' => $this->trans('City', [], 'Admin.Global'),
                'validation' => 'isGenericName',
                'type' => 'text',
            ],
            'PS_SHOP_COUNTRY_ID' => [
                'title' => $this->trans('Country', [], 'Admin.Global'),
                'validation' => 'isInt',
                'type' => 'select',
                'list' => $countryList,
                'identifier' => 'id',
                'cast' => 'intval',
                'defaultValue' => (int) $this->context->country->id,
            ],
            'PS_SHOP_STATE_ID' => [
                'title' => $this->trans('State', [], 'Admin.Global'),
                'validation' => 'isInt',
                'type' => 'select',
                'list' => $stateList,
                'identifier' => 'id',
                'cast' => 'intval',
            ],
            'PS_SHOP_PHONE' => [
                'title' => $this->trans('Phone', [], 'Admin.Global'),
                'validation' => 'isGenericName',
                'type' => 'text',
            ],
            'PS_SHOP_FAX' => [
                'title' => $this->trans('Fax', [], 'Admin.Global'),
                'validation' => 'isGenericName',
                'type' => 'text',
            ],
        ];

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
        $this->fields_options['contact'] = [
            'title' => $this->trans('Contact details', [], 'Admin.Shopparameters.Feature'),
            'icon' => 'icon-user',
            'fields' => $fields,
            'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
        ];
    }

    public function beforeUpdateOptions()
    {
        if (isset($_POST['PS_SHOP_STATE_ID']) && $_POST['PS_SHOP_STATE_ID'] != '0') {
            $sql = 'SELECT `active` FROM `' . _DB_PREFIX_ . 'state`
					WHERE `id_country` = ' . (int) Tools::getValue('PS_SHOP_COUNTRY_ID') . '
						AND `id_state` = ' . (int) Tools::getValue('PS_SHOP_STATE_ID');
            $isStateOk = Db::getInstance()->getValue($sql);
            if ($isStateOk != 1) {
                $this->errors[] = $this->trans('The specified state is not located in this country.', [], 'Admin.Shopparameters.Notification');
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
     * Adapt the format of hours.
     *
     * @param array $value
     *
     * @return array
     */
    protected function adaptHoursFormat($value)
    {
        $separator = array_fill(0, count($value), ' | ');

        return array_map('implode', $separator, $value);
    }
}
