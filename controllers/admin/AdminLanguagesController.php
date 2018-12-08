<?php
/**
 * 2007-2018 PrestaShop.
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
 * @property Language $object
 */
class AdminLanguagesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'lang';
        $this->className = 'Language';
        $this->lang = false;
        $this->deleted = false;
        $this->multishop_context = Shop::CONTEXT_ALL;

        parent::__construct();

        $this->fieldImageSettings = [
            [
                'name' => 'flag',
                'dir' => 'l',
            ],
            [
                'name' => 'no_picture',
                'dir' => 'p',
            ],
        ];

        $this->fields_list = [
            'id_lang' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'flag' => [
                'title' => $this->trans('Flag', [], 'Admin.International.Feature'),
                'align' => 'center',
                'image' => 'l',
                'orderby' => false,
                'search' => false,
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Admin.Global'),
            ],
            'iso_code' => [
                'title' => $this->trans('ISO code', [], 'Admin.International.Feature'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'language_code' => [
                'title' => $this->trans('Language code', [], 'Admin.International.Feature'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'date_format_lite' => [
                'title' => $this->trans('Date format', [], 'Admin.International.Feature'),
            ],
            'date_format_full' => [
                'title' => $this->trans('Date format (full)', [], 'Admin.International.Feature'),
            ],
            'active' => [
                'title' => $this->trans('Enabled', [], 'Admin.Global'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-sm',
            ],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];
        $this->specificConfirmDelete = $this->trans('When you delete a language, all related translations in the database will be deleted. Are you sure you want to proceed?', [], 'Admin.International.Notification');
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_language'] = [
                'href' => self::$currentIndex . '&addlang&token=' . $this->token,
                'desc' => $this->trans('Add new language', [], 'Admin.International.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->displayWarning($this->trans('When you delete a language, all related translations in the database will be deleted.', [], 'Admin.International.Notification'));
        if (!is_writable(_PS_ROOT_DIR_ . '/.htaccess') && Configuration::get('PS_REWRITING_SETTINGS')) {
            $this->displayInformation($this->trans('Your .htaccess file must be writable.', [], 'Admin.International.Notification'));
        }

        return parent::renderList();
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Languages', [], 'Admin.Global'),
                'icon' => 'icon-globe',
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'ps_version',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                    'maxlength' => 32,
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('ISO code', [], 'Admin.International.Feature'),
                    'name' => 'iso_code',
                    'required' => true,
                    'maxlength' => 2,
                    'hint' => $this->trans('Two-letter ISO code (e.g. FR, EN, DE).', [], 'Admin.International.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Language code', [], 'Admin.International.Feature'),
                    'name' => 'language_code',
                    'required' => true,
                    'maxlength' => 5,
                    'hint' => $this->trans('IETF language tag (e.g. en-US, pt-BR).', [], 'Admin.International.Help'),
                    /* TO DO - ajouter les liens dans le hint ? */
                    /*'desc' => $this->trans('IETF language tag (e.g. en-US, pt-BR).').' '.sprintf('<a href="http://en.wikipedia.org/wiki/IETF_language_tag" target="_blank">%s <img src="../img/admin/external_link.png" class="icon-top" /></a>', $this->l('IETF on Wikipedia'))*/
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Date format', [], 'Admin.International.Feature'),
                    'name' => 'date_format_lite',
                    'required' => true,
                    'hint' => $this->trans('Short date format (e.g., Y-m-d).', [], 'Admin.International.Help'),
                    /* TO DO - ajouter les liens dans le hint ? */
                    /*'desc' => sprintf($this->trans('Short date format (e.g., %s)'), '<a href="http://php.net/date" target="_blank">Y-m-d</a>')*/
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Date format (full)', [], 'Admin.International.Feature'),
                    'name' => 'date_format_full',
                    'required' => true,
                    'hint' => $this->trans('Full date format (e.g., Y-m-d H:i:s).', [], 'Admin.International.Help'),
                    /* TO DO - ajouter les liens dans le hint ? */
                    /*'desc' => sprintf($this->trans('Full date format (e.g., %s)'), '<a href="http://php.net/date" target="_blank">Y-m-d H:i:s</a>')*/
                ],
                [
                    'type' => 'file',
                    'label' => $this->trans('Flag', [], 'Admin.International.Feature'),
                    'name' => 'flag',
                    'required' => true,
                    'hint' => $this->trans('Upload the country flag from your computer.', [], 'Admin.International.Help'),
                ],
                [
                    'type' => 'file',
                    'label' => $this->trans('"No-picture" image', [], 'Admin.International.Feature'),
                    'name' => 'no_picture',
                    'hint' => $this->trans('Image is displayed when "no picture is found".', [], 'Admin.International.Help'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Is RTL language', [], 'Admin.International.Feature'),
                    'name' => 'is_rtl',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'is_rtl_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', [], 'Admin.Global'),
                        ],
                    ],
                    'hint' => [
                        $this->trans('Enable if this language is read from right to left.', [], 'Admin.International.Help') . ' ' .
                        $this->trans('(Experimental: your theme must be compliant with RTL languages).', [], 'Admin.International.Help'),
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Status', [], 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', [], 'Admin.Global'),
                        ],
                    ],
                    'hint' => $this->trans('Activate this language.', [], 'Admin.International.Feature'),
                ],
                [
                    'type' => 'special',
                    'name' => 'resultCheckLangPack',
                    'text' => $this->trans('Check to see if a language pack is available for this ISO code.', [], 'Admin.International.Feature'),
                    'img' => 'ajax-loader.gif',
                ],
            ],
        ];

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->trans('Shop association', [], 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        $this->fields_form['submit'] = [
            'title' => $this->trans('Save', [], 'Admin.Actions'),
        ];

        /** @var Language $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        if ($obj->id && !$obj->checkFiles()) {
            $this->fields_form['new'] = [
                'legend' => [
                    'title' => $this->trans('Warning', [], 'Admin.Global'),
                    'image' => '../img/admin/warning.gif',
                ],
                'list_files' => [
                    [
                        'label' => $this->trans('Translation files', [], 'Admin.International.Feature'),
                        'files' => Language::getFilesList($obj->iso_code, _THEME_NAME_, false, false, 'tr', true),
                    ],
                    [
                        'label' => $this->trans('Theme files', [], 'Admin.International.Feature'),
                        'files' => Language::getFilesList($obj->iso_code, _THEME_NAME_, false, false, 'theme', true),
                    ],
                    [
                        'label' => $this->trans('Mail files', [], 'Admin.International.Feature'),
                        'files' => Language::getFilesList($obj->iso_code, _THEME_NAME_, false, false, 'mail', true),
                    ],
                ],
            ];
        }

        $this->fields_value = ['ps_version' => _PS_VERSION_];

        return parent::renderForm();
    }

    public function processDelete()
    {
        $object = $this->loadObject();
        if (!$this->checkDeletion($object)) {
            return false;
        }
        if (!$this->deleteNoPictureImages((int) $object->id)) {
            $this->errors[] = $this->trans('An error occurred while deleting the object.', [], 'Admin.Notifications.Error') . ' <b>' . $this->table . '</b> ';
        }

        return parent::processDelete();
    }

    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id_lang) {
                $object = new Language((int) $id_lang);
                if (!$this->checkDeletion($object)) {
                    return false;
                }
                if (!$this->deleteNoPictureImages((int) $object->id)) {
                    $this->errors[] = $this->trans('An error occurred while deleting the object.', [], 'Admin.Notifications.Error') . ' <b>' . $this->table . '</b> ';

                    return false;
                }
            }
        }

        return parent::processBulkDelete();
    }

    protected function checkDeletion($object)
    {
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');

            return;
        }

        if (Validate::isLoadedObject($object)) {
            if ($object->id == Configuration::get('PS_LANG_DEFAULT')) {
                $this->errors[] = $this->trans('You cannot delete the default language.', [], 'Admin.International.Notification');
            } elseif ($object->id == $this->context->language->id) {
                $this->errors[] = $this->trans('You cannot delete the language currently in use. Please select a different language.', [], 'Admin.International.Notification');
            } else {
                return true;
            }
        } else {
            $this->errors[] = $this->trans('(cannot load object)', [], 'Admin.Notifications.Error');
        }

        return false;
    }

    protected function checkDisableStatus($object)
    {
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');

            return;
        }
        if (!Validate::isLoadedObject($object)) {
            $this->errors[] = $this->trans('An error occurred while updating the status for an object.', [], 'Admin.Notifications.Error') . ' <b>' . $this->table . '</b> ' . $this->trans('(cannot load object)', [], 'Admin.Notifications.Error');
        } else {
            if ($object->id == (int) Configuration::get('PS_LANG_DEFAULT')) {
                $this->errors[] = $this->trans('You cannot change the status of the default language.', [], 'Admin.International.Notification');
            } else {
                return true;
            }
        }

        return false;
    }

    public function processStatus()
    {
        $object = $this->loadObject();
        if ($this->checkDisableStatus($object)) {
            $this->checkEmployeeIdLang($object->id);

            return parent::processStatus();
        }

        return false;
    }

    protected function processBulkDisableSelection()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id_lang) {
                $object = new Language((int) $id_lang);
                if (!$this->checkDisableStatus($object)) {
                    return false;
                }
                $this->checkEmployeeIdLang($object->id);
            }
        }

        return parent::processBulkDisableSelection();
    }

    public function processAdd()
    {
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');

            return;
        }

        if (isset($_POST['iso_code']) && !empty($_POST['iso_code']) && Validate::isLanguageIsoCode(Tools::getValue('iso_code')) && Language::getIdByIso($_POST['iso_code'])) {
            $this->errors[] = $this->trans('This ISO code is already linked to another language.', [], 'Admin.International.Notification');
        }
        if ((!empty($_FILES['no_picture']['tmp_name']) || !empty($_FILES['flag']['tmp_name'])) && Validate::isLanguageIsoCode(Tools::getValue('iso_code'))) {
            if ($_FILES['no_picture']['error'] == UPLOAD_ERR_OK) {
                $this->copyNoPictureImage(strtolower(Tools::getValue('iso_code')));
            }
            unset($_FILES['no_picture']);
        } else {
            $this->errors[] = $this->trans('Flag and "No picture" image fields are required.', [], 'Admin.International.Notification');
        }

        return parent::processAdd();
    }

    public function processUpdate()
    {
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');

            return;
        }

        if ((isset($_FILES['no_picture']) && !$_FILES['no_picture']['error'] || isset($_FILES['flag']) && !$_FILES['flag']['error'])
                && Validate::isLanguageIsoCode(Tools::getValue('iso_code'))) {
            if ($_FILES['no_picture']['error'] == UPLOAD_ERR_OK) {
                $this->copyNoPictureImage(strtolower(Tools::getValue('iso_code')));
            }
            // class AdminTab deal with every $_FILES content, don't do that for no_picture
            unset($_FILES['no_picture']);
        }

        /** @var Language $object */
        $object = $this->loadObject();
        if (Tools::getValue('active') != (int) $object->active) {
            if (!$this->checkDisableStatus($object)) {
                return false;
            }
        }

        $this->checkEmployeeIdLang($object->id);

        return parent::processUpdate();
    }

    /**
     * Copy a no-product image.
     *
     * @param string $language Language iso_code for no_picture image filename
     *
     * @return void|false
     */
    public function copyNoPictureImage($language)
    {
        if (isset($_FILES['no_picture']) && $_FILES['no_picture']['error'] === 0) {
            if ($error = ImageManager::validateUpload($_FILES['no_picture'], Tools::getMaxUploadSize())) {
                $this->errors[] = $error;
            } else {
                if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['no_picture']['tmp_name'], $tmp_name)) {
                    return false;
                }
                if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_ . 'p/' . $language . '.jpg')) {
                    $this->errors[] = $this->trans('An error occurred while copying "No Picture" image to your product folder.', [], 'Admin.International.Notification');
                }
                if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_ . 'c/' . $language . '.jpg')) {
                    $this->errors[] = $this->trans('An error occurred while copying "No picture" image to your category folder.', [], 'Admin.International.Notification');
                }
                if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_ . 'm/' . $language . '.jpg')) {
                    $this->errors[] = $this->trans('An error occurred while copying "No picture" image to your brand folder.', [], 'Admin.International.Notification');
                } else {
                    $images_types = ImageType::getImagesTypes('products');
                    foreach ($images_types as $image_type) {
                        if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_ . 'p/' . $language . '-default-' . stripslashes($image_type['name']) . '.jpg', $image_type['width'], $image_type['height'])) {
                            $this->errors[] = $this->trans('An error occurred while resizing "No picture" image to your product directory.', [], 'Admin.International.Notification');
                        }
                        if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_ . 'c/' . $language . '-default-' . stripslashes($image_type['name']) . '.jpg', $image_type['width'], $image_type['height'])) {
                            $this->errors[] = $this->trans('An error occurred while resizing "No picture" image to your category directory.', [], 'Admin.International.Notification');
                        }
                        if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_ . 'm/' . $language . '-default-' . stripslashes($image_type['name']) . '.jpg', $image_type['width'], $image_type['height'])) {
                            $this->errors[] = $this->trans('An error occurred while resizing "No picture" image to your brand directory.', [], 'Admin.International.Notification');
                        }
                    }
                }
                unlink($tmp_name);
            }
        }
    }

    /**
     * deleteNoPictureImages will delete all default image created for the language id_language.
     *
     * @param string $id_language
     *
     * @return bool true if no error
     */
    protected function deleteNoPictureImages($id_language)
    {
        $language = Language::getIsoById($id_language);
        $images_types = ImageType::getImagesTypes('products');
        $dirs = [_PS_PROD_IMG_DIR_, _PS_CAT_IMG_DIR_, _PS_MANU_IMG_DIR_, _PS_SUPP_IMG_DIR_, _PS_MANU_IMG_DIR_];
        foreach ($dirs as $dir) {
            foreach ($images_types as $image_type) {
                if (file_exists($dir . $language . '-default-' . stripslashes($image_type['name']) . '.jpg')) {
                    if (!unlink($dir . $language . '-default-' . stripslashes($image_type['name']) . '.jpg')) {
                        $this->errors[] = $this->trans('An error occurred during image deletion process.', [], 'Admin.International.Notification');
                    }
                }
            }

            if (file_exists($dir . $language . '.jpg')) {
                if (!unlink($dir . $language . '.jpg')) {
                    $this->errors[] = $this->trans('An error occurred during image deletion process.', [], 'Admin.International.Notification');
                }
            }
        }

        return !count($this->errors) ? true : false;
    }

    /**
     * @param Language $object
     * @param string $table
     */
    protected function copyFromPost(&$object, $table)
    {
        if ($object->id && ($object->iso_code != $_POST['iso_code'])) {
            if (Validate::isLanguageIsoCode($_POST['iso_code'])) {
                $object->moveToIso($_POST['iso_code']);
            }
        }
        parent::copyFromPost($object, $table);
    }

    public function ajaxProcessCheckLangPack()
    {
        $this->json = true;
        if (!Tools::getValue('iso_lang') || !Validate::isLanguageIsoCode(Tools::getValue('iso_lang'))) {
            $this->status = 'error';
            $this->errors[] = $this->trans('Iso code is not valid', [], 'Admin.International.Notification');

            return;
        }
        if (!Tools::getValue('ps_version') || !Validate::isPrestaShopVersion(Tools::getValue('ps_version'))) {
            $this->status = 'error';
            $this->errors[] = $this->trans('Technical Error: ps_version is not valid', [], 'Admin.International.Notification');

            return;
        }

        // Get all iso code available
        if ($lang_packs = Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/get_language_pack.php?version=' . Tools::getValue('ps_version') . '&iso_lang=' . Tools::strtolower(Tools::getValue('iso_lang')))) {
            $result = json_decode($lang_packs);
            if ($lang_packs !== '' && $result && !isset($result->error)) {
                $this->status = 'ok';
                $this->content = $lang_packs;
            } else {
                $this->status = 'error';
                $this->errors[] = $this->trans('Wrong ISO code, or the selected language pack is unavailable.', [], 'Admin.International.Notification');
            }
        } else {
            $this->status = 'error';
            $this->errors[] = $this->trans('Technical Error: translation server unreachable.', [], 'Admin.International.Notification');
        }
    }

    protected function checkEmployeeIdLang($current_id_lang)
    {
        //update employee lang if current id lang is disabled
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'employee` set `id_lang`=' . (int) Configuration::get('PS_LANG_DEFAULT') . ' WHERE `id_lang`=' . (int) $current_id_lang);
    }

    protected function afterImageUpload()
    {
        parent::afterImageUpload();

        if (($id_lang = (int) Tools::getValue('id_lang')) &&
             isset($_FILES) && count($_FILES) && file_exists(_PS_LANG_IMG_DIR_ . $id_lang . '.jpg')) {
            $current_file = _PS_TMP_IMG_DIR_ . 'lang_mini_' . $id_lang . '_' . $this->context->shop->id . '.jpg';

            if (file_exists($current_file)) {
                unlink($current_file);
            }
        }

        return true;
    }
}
