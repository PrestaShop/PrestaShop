<?php
/**
 * 2007-2015 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Adapter\Configuration as AdapterConfiguration;

/**
 * @property Theme $object
 */
class AdminThemesControllerCore extends AdminController
{
    /**
    * @var object ThemeManager
    */
    public $theme_manager;

    protected $toolbar_scroll = false;
    private $img_error;

    // temp
    const CACHE_FILE_CUSTOMER_THEMES_LIST = '/config/xml/customer_themes_list.xml';
    const CACHE_FILE_MUST_HAVE_THEMES_LIST = '/config/xml/must_have_themes_list.xml';

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $this->theme_manager = (new ThemeManagerBuilder($this->context, Db::getInstance()))
                                ->build();
    }

    public function init()
    {
        // No cache for auto-refresh uploaded logo
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        parent::init();
        $this->can_display_themes = (!Shop::isFeatureActive() || Shop::getContext() == Shop::CONTEXT_SHOP);

        if (Tools::getValue('display') == 'configureLayouts') {
            $this->initConfigureLayouts();
        } elseif (Tools::getValue('action') == 'importtheme') {
            $this->display = 'importtheme';
        }

        libxml_use_internal_errors(true);
    }

    public function downloadAddonsThemes()
    {
        if (!$this->logged_on_addons) {
            return false;
        }

        if (!$this->isFresh(self::CACHE_FILE_CUSTOMER_THEMES_LIST, 86400)) {
            file_put_contents(_PS_ROOT_DIR_.self::CACHE_FILE_CUSTOMER_THEMES_LIST, Tools::addonsRequest('customer_themes'));
        }

        $customer_themes_list = file_get_contents(_PS_ROOT_DIR_.self::CACHE_FILE_CUSTOMER_THEMES_LIST);
        if (!empty($customer_themes_list) && $customer_themes_list_xml = @simplexml_load_string($customer_themes_list)) {
            foreach ($customer_themes_list_xml->theme as $addons_theme) {
                //get addons theme if folder does not exist
                $ids_themes = Tools::unSerialize(Configuration::get('PS_ADDONS_THEMES_IDS'));

                if (!is_array($ids_themes) || (is_array($ids_themes) && !in_array((string)$addons_theme->id, $ids_themes))) {
                    $zip_content = Tools::addonsRequest(
                        'module',
                        array(
                            'id_module' => pSQL($addons_theme->id),
                            'username_addons' => pSQL(trim($this->context->cookie->username_addons)),
                            'password_addons' => pSQL(trim($this->context->cookie->password_addons))
                        )
                    );

                    $uniqid = uniqid();
                    $sandbox = _PS_CACHE_DIR_.'sandbox'.DIRECTORY_SEPARATOR.$uniqid.DIRECTORY_SEPARATOR;
                    mkdir($sandbox);

                    file_put_contents($sandbox.(string)$addons_theme->name.'.zip', $zip_content);

                    if ($this->extractTheme($sandbox.(string)$addons_theme->name.'.zip', $sandbox)) {
                        if ($theme_directory = $this->installTheme(self::UPLOADED_THEME_DIR_NAME, $sandbox, false)) {
                            $ids_themes[$theme_directory] = (string)$addons_theme->id;
                        }
                    }

                    Tools::deleteDirectory($sandbox);
                }
                Configuration::updateValue('PS_ADDONS_THEMES_IDS', serialize($ids_themes));
            }
        }
    }

    protected function processUpdateOptions()
    {
        parent::processUpdateOptions();

        if (!count($this->errors)) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminThemes').'&conf=6');
        }
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if (empty($this->display)) {
            $this->page_header_toolbar_btn['import_theme'] = array(
                'href' => self::$currentIndex.'&action=importtheme&token='.$this->token,
                'desc' => $this->l('Add new theme', null, null, false),
                'icon' => 'process-icon-new'
            );

            if ($this->context->mode) {
                unset($this->toolbar_btn['new']);
            }
        }

        if ($this->display == 'importtheme') {
            $this->toolbar_title[] = $this->l('Import theme');
        } else {
            $this->toolbar_title[] = $this->l('Theme');
        }

        $title = implode(' '.Configuration::get('PS_NAVIGATION_PIPE').' ', $this->toolbar_title);
        $this->page_header_toolbar_title = $title;
    }

    public function initContent()
    {
        if ($this->display == 'list') {
            $this->display = '';
        }
        if (isset($this->display) && method_exists($this, 'render'.$this->display)) {
            $this->content .= $this->initPageHeaderToolbar();

            $this->content .= $this->{'render'.$this->display}();
            $this->context->smarty->assign(array(
                'content' => $this->content,
                'show_page_header_toolbar' => $this->show_page_header_toolbar,
                'page_header_toolbar_title' => $this->page_header_toolbar_title,
                'page_header_toolbar_btn' => $this->page_header_toolbar_btn
            ));
        } else {
            $content = '';
            if (Configuration::hasKey('PS_LOGO') && trim(Configuration::get('PS_LOGO')) != ''
                && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO')) && filesize(_PS_IMG_DIR_.Configuration::get('PS_LOGO'))) {
                list($width, $height, $type, $attr) = getimagesize(_PS_IMG_DIR_.Configuration::get('PS_LOGO'));
                Configuration::updateValue('SHOP_LOGO_HEIGHT', (int)round($height));
                Configuration::updateValue('SHOP_LOGO_WIDTH', (int)round($width));
            }
            if (Configuration::get('PS_LOGO_MOBILE') && trim(Configuration::get('PS_LOGO_MOBILE')) != ''
                && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MOBILE')) && filesize(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MOBILE'))) {
                list($width, $height, $type, $attr) = getimagesize(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MOBILE'));
                Configuration::updateValue('SHOP_LOGO_MOBILE_HEIGHT', (int)round($height));
                Configuration::updateValue('SHOP_LOGO_MOBILE_WIDTH', (int)round($width));
            }

            $this->content .= $content;

            return parent::initContent();
        }
    }

    public function ajaxProcessGetAddonsThemes()
    {
        $parent_domain = Tools::getHttpHost(true).substr($_SERVER['REQUEST_URI'], 0, -1 * strlen(basename($_SERVER['REQUEST_URI'])));
        $iso_lang = $this->context->language->iso_code;
        $iso_currency = $this->context->currency->iso_code;
        $iso_country = $this->context->country->iso_code;
        $activity = Configuration::get('PS_SHOP_ACTIVITY');
        $addons_url = 'http://addons.prestashop.com/iframe/search-1.6.php?psVersion='._PS_VERSION_.'&onlyThemes=1&isoLang='.$iso_lang.'&isoCurrency='.$iso_currency.'&isoCountry='.$iso_country.'&activity='.(int)$activity.'&parentUrl='.$parent_domain;

        die(Tools::file_get_contents($addons_url));
    }

    private function updateImages($xml)
    {
        $return = array();

        if (isset($xml->images->image)) {
            foreach ($xml->images->image as $row) {
                Db::getInstance()->delete('image_type', '`name` = \''.pSQL($row['name']).'\'');
                Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'image_type` (`name`, `width`, `height`, `products`, `categories`, `manufacturers`, `suppliers`, `scenes`)
					VALUES (\''.pSQL($row['name']).'\',
						'.(int)$row['width'].',
						'.(int)$row['height'].',
						'.($row['products'] == 'true' ? 1 : 0).',
						'.($row['categories'] == 'true' ? 1 : 0).',
						'.($row['manufacturers'] == 'true' ? 1 : 0).',
						'.($row['suppliers'] == 'true' ? 1 : 0).',
						'.($row['scenes'] == 'true' ? 1 : 0).')');

                $return['ok'][] = array(
                        'name' => strval($row['name']),
                        'width' => (int)$row['width'],
                        'height' => (int)$row['height']
                    );
            }
        }

        return $return;
    }

    public function renderView()
    {
        $this->tpl_view_vars = array(
            'doc' => $this->doc,
            'theme_name' => $this->theme_name,
            'img_error' => $this->img_error,
            'modules_errors' => $this->modules_errors,
            'back_link' => Context::getContext()->link->getAdminLink('AdminThemes'),
            'image_link' => Context::getContext()->link->getAdminLink('AdminImages')
        );

        return parent::renderView();
    }

    /**
     * This functions make checks about AdminThemes configuration edition only.
     *
     * @since 1.4
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitAddconfiguration')) {
            if ($filename = Tools::getValue('theme_archive_server')) {
                $path = _PS_ALL_THEMES_DIR_.$filename;
                $this->theme_manager->install($path);
            } elseif ($filename = Tools::getValue('filename')) {
                $path = _PS_ALL_THEMES_DIR_.$filename;
                if ($this->processUploadFile($path)) {
                    $this->theme_manager->install($path);
                }
            } elseif ($source = Tools::getValue('themearchiveUrl')) {
                $this->theme_manager->install($path);
            }
            $this->redirect_after = $this->context->link->getAdminLink('AdminThemes');
        } elseif (Tools::getValue('action') == 'submitConfigureLayouts') {
            $this->processSubmitConfigureLayouts();
            $this->redirect_after = $this->context->link->getAdminLink('AdminThemes');
        } elseif (Tools::getValue('action') == 'enableTheme') {
            $this->theme_manager->enable(Tools::getValue('theme_name'));
            $this->redirect_after = $this->context->link->getAdminLink('AdminThemes');
        } elseif (Tools::getValue('action') == 'deleteTheme') {
            $this->theme_manager->uninstall(Tools::getValue('theme_name'));
            $this->redirect_after = $this->context->link->getAdminLink('AdminThemes');
        }

        return parent::postProcess();
    }

    public function processUploadFile($dest)
    {
        switch ($_FILES['themearchive']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->errors[] = $this->l('The uploaded file is too large.');
                return false;
            default:
                $this->errors[] = $this->l('Unknown error.');
                return false;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $ext = array_search(
            $finfo->file($_FILES['themearchive']['tmp_name']),
            array(
                'zip' => 'application/zip',
            ),
            true
        );
        if ($ext === false) {
            $this->errors[] = $this->l('Invalid file format.');
            return false;
        }

        $name = $_FILES['themearchive']['name'];
        if (!Validate::isFileName($name)) {
            $name = sha1_file($name).$ext;
        }
        if (!move_uploaded_file(
            $_FILES['themearchive']['tmp_name'],
            _PS_ALL_THEMES_DIR_.$name
        )) {
            $this->errors[] = $this->l('Failed to move uploaded file.');
            return false;
        }

        return true;
    }

    /**
     * Update PS_LOGO
     */
    public function updateOptionPsLogo()
    {
        $this->updateLogo('PS_LOGO', 'logo');
    }

    /**
     * Update PS_LOGO_MOBILE
     */
    public function updateOptionPsLogoMobile()
    {
        $this->updateLogo('PS_LOGO_MOBILE', 'logo_mobile');
    }

    /**
     * Update PS_LOGO_MAIL
     */
    public function updateOptionPsLogoMail()
    {
        $this->updateLogo('PS_LOGO_MAIL', 'logo_mail');
    }

    /**
     * Update PS_LOGO_INVOICE
     */
    public function updateOptionPsLogoInvoice()
    {
        $this->updateLogo('PS_LOGO_INVOICE', 'logo_invoice');
    }

    /**
     * Update PS_STORES_ICON
     */
    public function updateOptionPsStoresIcon()
    {
        $this->updateLogo('PS_STORES_ICON', 'logo_stores');
    }

    /**
     * Generic function which allows logo upload
     *
     * @param $field_name
     * @param $logo_prefix
     *
     * @return bool
     */
    protected function updateLogo($field_name, $logo_prefix)
    {
        $id_shop = Context::getContext()->shop->id;
        if (isset($_FILES[$field_name]['tmp_name']) && $_FILES[$field_name]['tmp_name'] && $_FILES[$field_name]['size']) {
            if ($error = ImageManager::validateUpload($_FILES[$field_name], Tools::getMaxUploadSize())) {
                $this->errors[] = $error;
                return false;
            }
            $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');

            if (!$tmp_name || !move_uploaded_file($_FILES[$field_name]['tmp_name'], $tmp_name)) {
                return false;
            }

            $ext = ($field_name == 'PS_STORES_ICON') ? '.gif' : '.jpg';
            $logo_name = Tools::link_rewrite(Context::getContext()->shop->name).'-'
                .$logo_prefix.'-'.(int)Configuration::get('PS_IMG_UPDATE_TIME').(int)$id_shop.$ext;

            if (Context::getContext()->shop->getContext() == Shop::CONTEXT_ALL || $id_shop == 0
                || Shop::isFeatureActive() == false) {
                $logo_name = Tools::link_rewrite(Context::getContext()->shop->name).'-'
                .$logo_prefix.'-'.(int)Configuration::get('PS_IMG_UPDATE_TIME').$ext;
            }

            if ($field_name == 'PS_STORES_ICON') {
                if (!@ImageManager::resize($tmp_name, _PS_IMG_DIR_.$logo_name, null, null, 'gif', true)) {
                    $this->errors[] = Tools::displayError('An error occurred while attempting to copy your logo.');
                }
            } else {
                if (!@ImageManager::resize($tmp_name, _PS_IMG_DIR_.$logo_name)) {
                    $this->errors[] = Tools::displayError('An error occurred while attempting to copy your logo.');
                }
            }
            $id_shop = null;
            $id_shop_group = null;
            if (!count($this->errors) && @filemtime(_PS_IMG_DIR_.Configuration::get($field_name))) {
                if (Shop::isFeatureActive()) {
                    if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                        $id_shop = Shop::getContextShopID();
                        $id_shop_group = Shop::getContextShopGroupID();
                        Shop::setContext(Shop::CONTEXT_ALL);
                        $logo_all = Configuration::get($field_name);
                        Shop::setContext(Shop::CONTEXT_GROUP);
                        $logo_group = Configuration::get($field_name);
                        Shop::setContext(Shop::CONTEXT_SHOP);
                        $logo_shop = Configuration::get($field_name);
                        if ($logo_all != $logo_shop && $logo_group != $logo_shop && $logo_shop != false) {
                            @unlink(_PS_IMG_DIR_.Configuration::get($field_name));
                        }
                    } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
                        $id_shop_group = Shop::getContextShopGroupID();
                        Shop::setContext(Shop::CONTEXT_ALL);
                        $logo_all = Configuration::get($field_name);
                        Shop::setContext(Shop::CONTEXT_GROUP);
                        if ($logo_all != Configuration::get($field_name)) {
                            @unlink(_PS_IMG_DIR_.Configuration::get($field_name));
                        }
                    }
                } else {
                    @unlink(_PS_IMG_DIR_.Configuration::get($field_name));
                }
            }
            Configuration::updateValue($field_name, $logo_name, false, $id_shop_group, $id_shop);
            Hook::exec('actionAdminThemesControllerUpdate_optionsAfter');
            @unlink($tmp_name);
        }
    }

    /**
     * Update PS_FAVICON
     */
    public function updateOptionPsFavicon()
    {
        $id_shop = Context::getContext()->shop->id;
        if ($id_shop == Configuration::get('PS_SHOP_DEFAULT')) {
            $this->uploadIco('PS_FAVICON', _PS_IMG_DIR_.'favicon.ico');
        }
        if ($this->uploadIco('PS_FAVICON', _PS_IMG_DIR_.'favicon-'.(int)$id_shop.'.ico')) {
            Configuration::updateValue('PS_FAVICON', 'favicon-'.(int)$id_shop.'.ico');
        }

        Configuration::updateGlobalValue('PS_FAVICON', 'favicon.ico');
        $this->redirect_after = self::$currentIndex.'&token='.$this->token;
    }

    protected function uploadIco($name, $dest)
    {
        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name'])) {
            // Check ico validity
            if ($error = ImageManager::validateIconUpload($_FILES[$name])) {
                $this->errors[] = $error;
            } elseif (!copy($_FILES[$name]['tmp_name'], $dest)) {
                // Copy new ico
                $this->errors[] = sprintf(Tools::displayError('An error occurred while uploading the favicon: cannot copy file "%s" to folder "%s".'), $_FILES[$name]['tmp_name'], $dest);
            }
        }

        return !count($this->errors);
    }

    /**
     * Function used to render the options for this controller
     */
    public function renderOptions()
    {
        // Download user themes from Addons
        if ($this->logged_on_addons) {
            $this->downloadAddonsThemes();
        }

        // Employee languages used for link and utm_source
        $lang = new Language($this->context->language->id);
        $iso_lang_uc = strtoupper($lang->iso_code);

        $this->fields_options = array(
            'appearance' => array(
                'title' => $this->l('Your current theme'),
                'icon' => 'icon-html5',
                'tabs' => array(
                    'logo' => $this->l('Logo'),
                    'logo2' => $this->l('Invoice & Email Logos'),
                    'icons' => $this->l('Icons'),
                    ),
                'fields' => array(
                    'PS_LOGO' => array(
                        'title' => $this->l('Header logo'),
                        'hint' => $this->l('Will appear on main page. Recommended height: 52px. Maximum height on default theme: 65px.'),
                        'type' => 'file',
                        'name' => 'PS_LOGO',
                        'tab' => 'logo',
                        'thumb' => _PS_IMG_.Configuration::get('PS_LOGO')
                    ),
                    'PS_LOGO_MAIL' => array(
                        'title' => $this->l('Mail logo'),
                        'desc' => ((Configuration::get('PS_LOGO_MAIL') === false) ? '<span class="light-warning">'.$this->l('Warning: if no email logo is available, the main logo will be used instead.').'</span><br />' : ''),
                        'hint' => $this->l('Will appear on email headers. If undefined, the header logo will be used.'),
                        'type' => 'file',
                        'name' => 'PS_LOGO_MAIL',
                        'tab' => 'logo2',
                        'thumb' => (Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL'))) ? _PS_IMG_.Configuration::get('PS_LOGO_MAIL') : _PS_IMG_.Configuration::get('PS_LOGO')
                    ),
                    'PS_LOGO_INVOICE' => array(
                        'title' => $this->l('Invoice logo'),
                        'desc' => ((Configuration::get('PS_LOGO_INVOICE') === false) ? '<span class="light-warning">'.$this->l('Warning: if no invoice logo is available, the main logo will be used instead.').'</span><br />' : ''),
                        'hint' => $this->l('Will appear on invoice headers.').' '.$this->l('Warning: you can use a PNG file for transparency, but it can take up to 1 second per page for processing. Please consider using JPG instead.'),
                        'type' => 'file',
                        'name' => 'PS_LOGO_INVOICE',
                        'tab' => 'logo2',
                        'thumb' => (Configuration::get('PS_LOGO_INVOICE') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE'))) ? _PS_IMG_.Configuration::get('PS_LOGO_INVOICE') : _PS_IMG_.Configuration::get('PS_LOGO')
                    ),
                    'PS_FAVICON' => array(
                        'title' => $this->l('Favicon'),
                        'hint' => $this->l('Will appear in the address bar of your web browser.'),
                        'type' => 'file',
                        'name' => 'PS_FAVICON',
                        'tab' => 'icons',
                        'thumb' => _PS_IMG_.Configuration::get('PS_FAVICON').(Tools::getValue('conf') ? sprintf('?%04d', rand(0, 9999)) : '')
                    ),
                ),
                'after_tabs' => array(
                    'cur_theme' => $this->context->shop->theme,
                ),
                'submit' => array('title' => $this->l('Save')),
                'buttons' => array(
                    'storeLink' => array(
                        'title' => $this->l('Visit the theme catalog'),
                        'icon' => 'process-icon-themes',
                        'href' => 'http://addons.prestashop.com/en/3-templates-prestashop'
                        .'?utm_source=back-office&utm_medium=theme-button'
                        .'&utm_campaign=back-office-'.$iso_lang_uc
                        .'&utm_content='.(defined('_PS_HOST_MODE_') ? 'cloud' : 'download'),
                        'js' => 'return !window.open(this.href)'
                    )
                )
            ),
        );

        $other_themes = $this->theme_manager->getThemeListExcluding([$this->context->shop->theme->name]);
        if (!empty($other_themes)) {
            $this->fields_options['theme'] = array(
                'title' => sprintf($this->l('Select a theme for the "%s" shop'), $this->context->shop->name),
                'description' => (!$this->can_display_themes) ? $this->l('You must select a shop from the above list if you wish to choose a theme.') : '',
                'fields' => array(
                    'theme_for_shop' => array(
                        'type' => 'theme',
                        'themes' => $other_themes,
                        'can_display_themes' => $this->can_display_themes,
                        'no_multishop_checkbox' => true
                    ),
                ),
            );
        }


        if (isset($this->display) && method_exists($this, 'render'.$this->display)) {
            return $this->{'render'.$this->display}();
        }
        if ($this->fields_options && is_array($this->fields_options)) {
            $helper = new HelperOptions($this);
            $this->setHelperDisplay($helper);
            $helper->toolbar_scroll = true;
            $helper->title = $this->l('Theme appearance');
            $helper->toolbar_btn = array(
                'save' => array(
                    'href' => '#',
                    'desc' => $this->l('Save')
                )
            );
            $helper->id = $this->id;
            $helper->tpl_vars = $this->tpl_option_vars;
            $options = $helper->generateOptions($this->fields_options);

            return $options;
        }
    }

    public function renderImportTheme()
    {
        $fields_form = array();

        $toolbar_btn['save'] = array(
            'href' => '#',
            'desc' => $this->l('Save')
        );

        if ($this->context->mode != Context::MODE_HOST) {
            $fields_form[0] = array(
                'form' => array(
                    'tinymce' => false,
                    'legend' => array(
                        'title' => $this->l('Import from your computer'),
                        'icon' => 'icon-picture'
                    ),
                    'input' => array(
                        array(
                            'type' => 'file',
                            'label' => $this->l('Zip file'),
                            'desc' => $this->l('Browse your computer files and select the Zip file for your new theme.'),
                            'name' => 'themearchive'
                        ),
                    ),
                    'submit' => array(
                        'id' => 'zip',
                        'title' => $this->l('Save'),
                        )
                    ),
                );

            $fields_form[1] = array(
                'form' => array(
                    'tinymce' => false,
                    'legend' => array(
                        'title' => $this->l('Import from the web'),
                        'icon' => 'icon-picture'
                    ),
                    'input' => array(
                        array(
                            'type' => 'text',
                            'label' => $this->l('Archive URL'),
                            'desc' => $this->l('Indicate the complete URL to an online Zip file that contains your new theme. For instance, "http://example.com/files/theme.zip".'),
                            'name' => 'themearchiveUrl'
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                        )
                    ),
                );

            $theme_archive_server = array();
            $files = scandir(_PS_ALL_THEMES_DIR_);
            $theme_archive_server[] = '-';

            foreach ($files as $file) {
                if (is_file(_PS_ALL_THEMES_DIR_.$file) && substr(_PS_ALL_THEMES_DIR_.$file, -4) == '.zip') {
                    $theme_archive_server[] = array(
                        'id' => basename(_PS_ALL_THEMES_DIR_.$file),
                        'name' => basename(_PS_ALL_THEMES_DIR_.$file)
                    );
                }
            }

            $fields_form[2] = array(
                'form' => array(
                    'tinymce' => false,
                    'legend' => array(
                        'title' => $this->l('Import from FTP'),
                        'icon' => 'icon-picture'
                    ),
                    'input' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select the archive'),
                            'name' => 'theme_archive_server',
                            'desc' => $this->l('This selector lists the Zip files that you uploaded in the \'/themes\' folder.'),
                            'options' => array(
                                'id' => 'id',
                                'name' => 'name',
                                'query' => $theme_archive_server,
                            )
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                        )
                    ),
                );
        }

        $this->context->smarty->assign(
            array(
                'import_theme' => true,
                'logged_on_addons' => $this->logged_on_addons,
                'iso_code' => $this->context->language->iso_code,
                )
            );

        $helper = new HelperForm();

        $helper->currentIndex = $this->context->link->getAdminLink('AdminThemes', false).'&action=importtheme';
        $helper->token = Tools::getAdminTokenLite('AdminThemes');
        $helper->show_toolbar = true;
        $helper->toolbar_btn = $toolbar_btn;
        $helper->fields_value['themearchiveUrl'] = '';
        $helper->fields_value['theme_archive_server'] = array();
        $helper->multiple_fieldsets = true;
        $helper->override_folder = $this->tpl_folder;
        $helper->languages = $this->getLanguages();
        $helper->default_form_language = (int)$this->context->language->id;

        return $helper->generateForm($fields_form);
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_PS_JS_DIR_.'admin/themes.js');

        if ($this->context->mode == Context::MODE_HOST && Tools::getValue('action') == 'importtheme') {
            $this->addJS(_PS_JS_DIR_.'admin/addons.js');
        }
    }

    public function initConfigureLayouts()
    {
        $this->context->smarty->assign([
            'pages' => Meta::getAllMeta($this->context->language->id),
            'page_layouts' => $this->context->shop->theme->settings['page_layouts'],
            'available_layouts' => $this->context->shop->theme->meta['available_layouts'],
        ]);

        $this->setTemplate('controllers/themes/configurelayouts.tpl');
    }

    public function processSubmitConfigureLayouts()
    {
        $this->context->shop->theme->setPageLayouts(Tools::getValue('layouts'));
        $this->theme_manager->saveTheme($this->context->shop->theme);
        Tools::clearCache();
    }
}
