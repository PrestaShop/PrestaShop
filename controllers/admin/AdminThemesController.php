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

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Shop\LogoUploader;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

/**
 * @property Theme $object
 */
class AdminThemesControllerCore extends AdminController
{
    // temp
    const CACHE_FILE_CUSTOMER_THEMES_LIST = '/config/xml/customer_themes_list.xml';
    const CACHE_FILE_MUST_HAVE_THEMES_LIST = '/config/xml/must_have_themes_list.xml';

    /**
    * @var ThemeManager
    */
    public $theme_manager;

    /**
     * @var ThemeRepository
     */
    protected $theme_repository;
    protected $toolbar_scroll = false;
    protected $authAccesses = array();
    protected $img_error;

    /* @var LogoUploader $logo_uploader */
    protected $logo_uploader;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $this->theme_manager = (new ThemeManagerBuilder($this->context, Db::getInstance()))->build();
        $this->theme_repository = (new ThemeManagerBuilder($this->context, Db::getInstance()))->buildRepository();
        $this->logo_uploader = new LogoUploader($this->context->shop);
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

        try {
            $this->validateAddAuthorization();
        } catch (Exception $e) {
            return false;
        }

        if (!$this->isFresh(self::CACHE_FILE_CUSTOMER_THEMES_LIST, 86400)) {
            file_put_contents(
                _PS_ROOT_DIR_.self::CACHE_FILE_CUSTOMER_THEMES_LIST,
                Tools::addonsRequest('customer_themes')
            );
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

                    file_put_contents($sandbox.(string)$addons_theme->getName().'.zip', $zip_content);

                    if ($this->extractTheme($sandbox.(string)$addons_theme->getName().'.zip', $sandbox)) {
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
            if ($this->isAddGranted()) {
                $this->page_header_toolbar_btn['import_theme'] = array(
                    'href' => self::$currentIndex.'&action=importtheme&token='.$this->token,
                    'desc' => $this->trans('Add new theme', array(), 'Admin.Design.Feature'),
                    'icon' => 'process-icon-new'
                );
                $this->page_header_toolbar_btn['export_theme'] = array(
                    'href' => self::$currentIndex.'&action=exporttheme&token='.$this->token,
                    'desc' => $this->trans('Export current theme', array(), 'Admin.Design.Feature'),
                    'icon' => 'process-icon-export'
                );
            }

            if ($this->context->mode) {
                unset($this->toolbar_btn['new']);
            }
        }

        if ($this->display == 'importtheme') {
            $this->toolbar_title[] = $this->trans('Import theme', array(), 'Admin.Design.Feature');
        } else {
            $this->toolbar_title[] = $this->trans('Theme', array(), 'Admin.Design.Feature');
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

            $this->content .= $content;

            return parent::initContent();
        }
    }

    /**
     * Ajax request handler for displaying theme catalog from the marketplace.
     * Not used anymore.
     *
     * @deprecated since 1.7.4.0
     */
    public function ajaxProcessGetAddonsThemes()
    {
        $parent_domain = Tools::getHttpHost(true).substr($_SERVER['REQUEST_URI'], 0, -1 * strlen(basename($_SERVER['REQUEST_URI'])));
        $iso_lang = $this->context->language->iso_code;
        $iso_currency = $this->context->currency->iso_code;
        $iso_country = $this->context->country->iso_code;
        $activity = Configuration::get('PS_SHOP_ACTIVITY');
        $addons_url = Tools::getCurrentUrlProtocolPrefix().'addons.prestashop.com/iframe/search-1.7.php?psVersion='._PS_VERSION_.'&onlyThemes=1&isoLang='.$iso_lang.'&isoCurrency='.$iso_currency.'&isoCountry='.$iso_country.'&activity='.(int)$activity.'&parentUrl='.$parent_domain;

        die(Tools::file_get_contents($addons_url));
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
        if (isset($_GET['error'])) {
            $this->errors[] = $this->trans(
                'You do not have permission to edit this.',
                array(),
                'Admin.Notifications.Error'
            );
        }

        // done here, because if it is true, $_FILES & $_POST are empty, so we don't have any message.
        try {
            $this->validateUploadSize();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }

        /** Specific processes, depending on action and/or submitted form */
        switch (Tools::getValue('action')) {
            case 'importtheme':
                if (Tools::isSubmit('submitAddconfiguration')) {
                    $this->postProcessSubmitAddConfiguration();
                }
                break;

            case 'exporttheme':
                if (false === $this->postProcessExportTheme()) {
                    return false;
                }
                break;

            case 'enableTheme':
                $this->postProcessEnableTheme();
                break;

            case 'deleteTheme':
                $this->postProcessDeleteTheme();
                break;

            case 'resetToDefaults':
                $this->postProcessResetToDefaults();
                break;

            case 'submitConfigureLayouts':
                $this->postProcessSubmitConfigureLayouts();
                break;

            // Main Theme page
            default:
                if (Tools::isSubmit('submitGenerateRTL')) {
                    $this->postProcessSubmitGenerateRTL();
                }

                if (Tools::isSubmit('submitOptionsconfiguration')) {
                    $this->postProcessSubmitOptionsConfiguration();
                }
        }

        return parent::postProcess();
    }

    public function processUploadFile($dest)
    {
        try {
            $this->validateAddAuthorization();
        } catch (Exception $e) {
            $this->errors[] = $this->trans(
                'You do not have permission to upload this.',
                array(),
                'Admin.Notifications.Error'
            );

            return false;
        }

        switch ($_FILES['themearchive']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->errors[] = $this->trans('The uploaded file is too large.', array(), 'Admin.Design.Notification');
                return false;
            default:
                $this->errors[] = $this->trans('Unknown error.', array(), 'Admin.Notifications.Error');
                return false;
        }

        $tmp_name = $_FILES['themearchive']['tmp_name'];
        $mimeType = false;
        $goodMimeType = false;

        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME);
            $mimeType = @finfo_file($finfo, $tmp_name);
            @finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $mimeType = @mime_content_type($tmp_name);
        } elseif (function_exists('exec')) {
            $mimeType = trim(@exec('file -b --mime-type '.escapeshellarg($tmp_name)));
            if (!$mimeType) {
                $mimeType = trim(@exec('file --mime '.escapeshellarg($tmp_name)));
            }
            if (!$mimeType) {
                $mimeType = trim(@exec('file -bi '.escapeshellarg($tmp_name)));
            }
        }

        if (!empty($mimeType)) {
            preg_match('#application/zip#', $mimeType, $matches);
            if (!empty($matches)) {
                $goodMimeType = true;
            }
        }

        if (false === $goodMimeType) {
            $this->errors[] = $this->trans('Invalid file format.', array(), 'Admin.Design.Notification');
            return false;
        }

        $name = $_FILES['themearchive']['name'];
        if (!Validate::isFileName($name)) {
            $dest = _PS_ALL_THEMES_DIR_.sha1_file($tmp_name).'.zip';
        }

        if (!move_uploaded_file(
            $_FILES['themearchive']['tmp_name'],
            $dest
        )) {
            $this->errors[] = $this->trans('Failed to move uploaded file.', array(), 'Admin.Design.Notification');
            return false;
        }

        return $dest;
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
                'title' => $this->trans('Your current theme', array(), 'Admin.Design.Feature'),
                'icon' => 'icon-html5',
                'tabs' => array(
                    'logo' => $this->trans('Logo', array(), 'Admin.Global'),
                    'logo2' => $this->trans('Invoice & Email Logos', array(), 'Admin.Design.Feature'),
                    'icons' => $this->trans('Favicons', array(), 'Admin.Design.Feature'),
                    ),
                'fields' => array(
                    'PS_LOGO' => array(
                        'title' => $this->trans('Header logo', array(), 'Admin.Design.Feature'),
                        'hint' => $this->trans('Will appear on main page. Recommended size for the default theme: height %height% and width %width%.',
                            array(
                                '%height%' => '40px',
                                '%width%' => '200px'
                            ),
                            'Admin.Design.Help'),
                        'type' => 'file',
                        'name' => 'PS_LOGO',
                        'tab' => 'logo',
                        'thumb' => _PS_IMG_.Configuration::get('PS_LOGO')
                    ),
                    'PS_LOGO_MAIL' => array(
                        'title' => $this->trans('Mail logo', array(), 'Admin.Design.Feature'),
                        'desc' => ((Configuration::get('PS_LOGO_MAIL') === false) ? '<span class="light-warning">'.$this->trans('Warning: if no email logo is available, the main logo will be used instead.', array(), 'Admin.Design.Notification').'</span><br />' : ''),
                        'hint' => $this->trans('Will appear on email headers. If undefined, the header logo will be used.', array(), 'Admin.Design.Help'),
                        'type' => 'file',
                        'name' => 'PS_LOGO_MAIL',
                        'tab' => 'logo2',
                        'thumb' => (Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL'))) ? _PS_IMG_.Configuration::get('PS_LOGO_MAIL') : _PS_IMG_.Configuration::get('PS_LOGO')
                    ),
                    'PS_LOGO_INVOICE' => array(
                        'title' => $this->trans('Invoice logo', array(), 'Admin.Design.Feature'),
                        'desc' => ((Configuration::get('PS_LOGO_INVOICE') === false) ? '<span class="light-warning">'.$this->trans('Warning: if no invoice logo is available, the main logo will be used instead.', array(), 'Admin.Design.Help').'</span><br />' : ''),
                        'hint' => $this->trans('Will appear on invoice headers.', array(), 'Admin.Design.Help').' '.$this->trans('Warning: you can use a PNG file for transparency, but it can take up to 1 second per page for processing. Please consider using JPG instead.', array(), 'Admin.Design.Help'),
                        'type' => 'file',
                        'name' => 'PS_LOGO_INVOICE',
                        'tab' => 'logo2',
                        'thumb' => (Configuration::get('PS_LOGO_INVOICE') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE'))) ? _PS_IMG_.Configuration::get('PS_LOGO_INVOICE') : _PS_IMG_.Configuration::get('PS_LOGO')
                    ),
                    'PS_FAVICON' => array(
                        'title' => $this->trans('Favicon', array(), 'Admin.Design.Feature'),
                        'hint' => $this->trans('It is the small icon that appears in browser tabs, next to the web address', array(), 'Admin.Design.Help'),
                        'type' => 'file',
                        'name' => 'PS_FAVICON',
                        'tab' => 'icons',
                        'thumb' => _PS_IMG_.Configuration::get('PS_FAVICON').(Tools::getValue('conf') ? sprintf('?%04d', rand(0, 9999)) : '')
                    ),
                ),
                'after_tabs' => array(
                    'cur_theme' => $this->context->shop->theme,
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
                'buttons' => array(
                    'storeLink' => array(
                        'id' => 'visit-theme-catalog-link',
                        'title' => $this->trans('Visit the theme catalog', array(), 'Admin.Design.Feature'),
                        'icon' => 'process-icon-themes',
                        'href' => Tools::getCurrentUrlProtocolPrefix().'addons.prestashop.com/en/3-templates-prestashop'
                        .'?utm_source=back-office&utm_medium=theme-button'
                        .'&utm_campaign=back-office-'.$iso_lang_uc
                        .'&utm_content='.(defined('_PS_HOST_MODE_') ? 'cloud' : 'download'),
                        'js' => 'return !window.open(this.href)'
                    )
                )
            ),
        );

        if (in_array("1", array_column($this->_languages, 'is_rtl'))) {
            $themes_list = array();
            $allThemes = $this->theme_repository->getList();
            foreach ($allThemes as $theme) {
                $themeName = $theme->getName();
                $themes_list[] = array('theme' => $themeName, 'name' => $themeName);
            }
            $this->fields_options['RTL'] = array(
                'title' => $this->trans('Adaptation to Right-to-Left languages', array(), 'Admin.Design.Feature'),
                'description' => $this->trans('Be careful! Please check your theme in an RTL language before generating the RTL stylesheet: your theme could be already adapted to RTL.\nOnce you click on "Adapt to RTL", any RTL-specific file that you might have added to your theme might be deleted by the created stylesheet.', array(), 'Admin.Design.Help'),
                'fields' => array(
                    'PS_THEMES_LIST' => array(
                        'title' => $this->trans('Theme to adapt', array(), 'Admin.Design.Feature'),
                        'type' => 'select',
                        'identifier' => 'theme',
                        'list' => $themes_list,
                    ),
                    'PS_GENERATE_RTL' => array(
                        'title' => $this->trans('Generate RTL stylesheet', array(), 'Admin.Design.Feature'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'default' => 0,
                    ),
                ),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Actions'),
                    'name' => 'submitGenerateRTL',
                ),
            );
        }

        $other_themes = $this->theme_repository->getListExcluding([$this->context->shop->theme->getName()]);
        if (!empty($other_themes)) {
            $this->fields_options['theme'] = array(
                'title' => $this->trans('Select a theme for the "%name%" shop', array('%name%' => $this->context->shop->name), 'Admin.Design.Feature'),
                'description' => (!$this->can_display_themes) ? $this->trans('You must select a shop from the above list if you wish to choose a theme.', array(), 'Admin.Design.Help') : '',
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
            $helper->title = $this->trans('Theme appearance', array(), 'Admin.Design.Feature');
            $helper->toolbar_btn = array(
                'save' => array(
                    'href' => '#',
                    'desc' => $this->trans('Save', array(), 'Admin.Actions')
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
        try {
            $this->validateAddAuthorization(true);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();

            return false;
        }

        $fields_form = array();

        $toolbar_btn['save'] = array(
            'href' => '#',
            'desc' => $this->trans('Save', array(), 'Admin.Actions')
        );

        if ($this->context->mode != Context::MODE_HOST) {
            $fields_form[0] = array(
                'form' => array(
                    'tinymce' => false,
                    'legend' => array(
                        'title' => $this->trans('Import from your computer', array(), 'Admin.Design.Feature'),
                        'icon' => 'icon-picture'
                    ),
                    'input' => array(
                        array(
                            'type' => 'file',
                            'label' => $this->trans('Zip file', array(), 'Admin.Design.Feature'),
                            'desc' => $this->trans('Browse your computer files and select the Zip file for your new theme.', array(), 'Admin.Design.Help'),
                            'name' => 'themearchive'
                        ),
                    ),
                    'submit' => array(
                        'id' => 'zip',
                        'title' => $this->trans('Save', array(), 'Admin.Actions'),
                        )
                    ),
                );

            $fields_form[1] = array(
                'form' => array(
                    'tinymce' => false,
                    'legend' => array(
                        'title' => $this->trans('Import from the web', array(), 'Admin.Design.Feature'),
                        'icon' => 'icon-picture'
                    ),
                    'input' => array(
                        array(
                            'type' => 'text',
                            'label' => $this->trans('Archive URL', array(), 'Admin.Design.Feature'),
                            'desc' => $this->trans('Indicate the complete URL to an online Zip file that contains your new theme. For instance, "http://example.com/files/theme.zip".', array(), 'Admin.Design.Help'),
                            'name' => 'themearchiveUrl'
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->trans('Save', array(), 'Admin.Actions'),
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
                        'title' => $this->trans('Import from FTP', array(), 'Admin.Design.Feature'),
                        'icon' => 'icon-picture'
                    ),
                    'input' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->trans('Select the archive', array(), 'Admin.Design.Feature'),
                            'name' => 'theme_archive_server',
                            'desc' => $this->trans('This selector lists the Zip files that you uploaded in the \'/themes\' folder.', array(), 'Admin.Design.Help'),
                            'options' => array(
                                'id' => 'id',
                                'name' => 'name',
                                'query' => $theme_archive_server,
                            )
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->trans('Save', array(), 'Admin.Actions'),
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

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_JS_DIR_.'admin/themes.js');

        if ($this->context->mode == Context::MODE_HOST && Tools::getValue('action') == 'importtheme') {
            $this->addJS(_PS_JS_DIR_.'admin/addons.js');
        }
    }

    public function initConfigureLayouts()
    {
        $theme = $this->theme_repository->getInstanceByName($this->context->shop->theme->getName());

        $this->context->smarty->assign([
            'pages' => Meta::getAllMeta($this->context->language->id),
            'default_layout' => $this->translateAttributes($theme->getDefaultLayout()),
            'page_layouts' => $theme->getPageLayouts(),
            'available_layouts' => $this->translateAttributes($theme->getAvailableLayouts()),
        ]);

        $this->setTemplate('controllers/themes/configurelayouts.tpl');
    }

    /**
     * Translate attributes (from yml)
     *
     * @param $attributes
     * @return mixed
     */
    protected function translateAttributes($attributes)
    {
        if (!empty($attributes)) {
            foreach ($attributes as $key => &$layout) {
                // layout can be an array of array, or just an array :/ we just translate name & description, see theme.dist.yml
                if (is_array($layout)) {
                    if (array_key_exists('name', $layout)) {
                        $layout['name'] = $this->translator->trans($layout['name'], array(), 'Admin.Design.Feature');
                    }
                    if (array_key_exists('description', $layout)) {
                        $layout['description'] = $this->translator->trans($layout['description'], array(), 'Admin.Design.Feature');
                    }
                } else {
                    if (in_array($key, array('name', 'description'))) {
                        $attributes[$key] = $this->translator->trans($layout, array(), 'Admin.Design.Feature');
                    }
                }
            }
        }

        return $attributes;
    }

    public function processSubmitConfigureLayouts()
    {
        if ($this->isAllGranted()) {
            $this->context->shop->theme->setPageLayouts(Tools::getValue('layouts'));
            $this->theme_manager->saveTheme($this->context->shop->theme);
        }

        Tools::clearCache();
    }

    /**
     * Verify if all actions are authorized in this controller
     *
     * @return bool true if access is granted
     */
    protected function isAllGranted()
    {
        // Delete access is the highest access level
        return $this->isDeleteGranted();
    }

    /**
     * Verify if delete is authorized in this controller
     *
     * @return bool true if delete access is granted
     */
    protected function isDeleteGranted()
    {
        return $this->isAccessGranted(AdminController::LEVEL_DELETE);
    }

    /**
     * Verify if add is authorized in this controller
     *
     * @return bool true if add access is granted
     */
    protected function isAddGranted()
    {
        return $this->isAccessGranted(AdminController::LEVEL_ADD);
    }

    /**
     * Verify if edit is authorized in this controller
     *
     * @return bool true if edit access is granted
     */
    protected function isEditGranted()
    {
        return $this->isAccessGranted(AdminController::LEVEL_EDIT);
    }

    /**
     * Verify if view is authorized in this controller
     *
     * @return bool true if view access is granted
     */
    protected function isViewGranted()
    {
        return $this->isAccessGranted(AdminController::LEVEL_VIEW);
    }

    /**
     * Verify if $accessLevel is granted for this controller
     *
     * @param int $accessLevel Access level to be verified
     *
     * @return bool true if this access level is granted for this controller
     */
    protected function isAccessGranted($accessLevel)
    {
        $accessLevel = (int)$accessLevel;
        if (!in_array(
            $accessLevel,
            array(
                AdminController::LEVEL_VIEW,
                AdminController::LEVEL_EDIT,
                AdminController::LEVEL_ADD,
                AdminController::LEVEL_DELETE,
            )
        )) {
            throw new InvalidArgumentException('Unknown access level : ' . $accessLevel);
        }

        if (empty($this->authAccesses[$accessLevel])) {
            $this->authAccesses[$accessLevel] = $this->authorizationLevel() >= (int)$accessLevel;
        }

        return $this->authAccesses[$accessLevel];
    }

    /**
     * Validate access for all action types.
     *
     * If access is not granted, the thrown Exception's error message contains the translated rejection message.
     *
     * @param bool $allowDemoMode Should we grant access in demo mode ?
     *
     * @throws Exception
     */
    protected function validateAllAuthorizations($allowDemoMode = false)
    {
        // DELETE level is the max possible level. If it is granted, everything else is granted.
        if ((_PS_MODE_DEMO_ && !$allowDemoMode)
            || !$this->isDeleteGranted()
        ) {
            throw new Exception(
                $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error')
            );
        }
    }

    /**
     * Validate delete access.
     *
     * If access is not granted, the thrown Exception's error message contains the translated rejection message.
     *
     * @param bool $allowDemoMode Should we grant access in demo mode ?
     *
     * @throws Exception
     */
    protected function validateDeleteAuthorization($allowDemoMode = false)
    {
        if ((_PS_MODE_DEMO_ && !$allowDemoMode)
            || !$this->isDeleteGranted()
        ) {
            throw new Exception(
                $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error')
            );
        }
    }

    /**
     * Validate add access.
     *
     * If access is not granted, the thrown Exception's error message contains the translated rejection message.
     *
     * @param bool $allowDemoMode Should we grant access in demo mode ?
     *
     * @throws Exception
     */
    protected function validateAddAuthorization($allowDemoMode = false)
    {
        if ((_PS_MODE_DEMO_ && !$allowDemoMode)
            || !$this->isAddGranted()
        ) {
            throw new Exception(
                $this->trans('You do not have permission to add this.', array(), 'Admin.Notifications.Error')
            );
        }
    }

    /**
     * Validate edit access.
     *
     * If access is not granted, the thrown Exception's error message contains the translated rejection message.
     *
     * @param bool $allowDemoMode Should we grant access in demo mode ?
     *
     * @throws Exception
     */
    protected function validateEditAuthorization($allowDemoMode = false)
    {
        if ((_PS_MODE_DEMO_ && !$allowDemoMode)
            || !$this->isEditGranted()
        ) {
            throw new Exception(
                $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error')
            );
        }
    }

    /**
     * Validate view access.
     *
     * If access is not granted, the thrown Exception's error message contains the translated rejection message.
     *
     * @param bool $allowDemoMode Should we grant access in demo mode ?
     *
     * @throws Exception
     */
    protected function validateViewAuthorization($allowDemoMode = false)
    {
        if ((_PS_MODE_DEMO_ && !$allowDemoMode)
            || !$this->isViewGranted()
        ) {
            throw new Exception(
                $this->trans('You do not have permission to view this.', array(), 'Admin.Notifications.Error')
            );
        }
    }

    protected function validateUploadSize()
    {
        $post_max_size = Tools::getMaxUploadSize();
        if ($post_max_size
            && isset($_SERVER['CONTENT_LENGTH'])
            && $_SERVER['CONTENT_LENGTH']
            && $_SERVER['CONTENT_LENGTH'] > $post_max_size
        ) {
            throw new Exception($this->trans('The uploaded file is too large.', array(), 'Admin.Design.Notification'));
        }
    }

    /**
     * Set redirect_after to same URL, but with an "&error" flag.
     */
    protected function addErrorToRedirectAfter()
    {
        $this->redirect_after = self::$currentIndex . '&token=' . $this->token . '&error';
    }

    /**
     * Specific postProcess for "exporttheme" action
     *
     * @return bool false if access not granted
     */
    protected function postProcessExportTheme()
    {
        try {
            $this->validateAddAuthorization(true);
        } catch (Exception $e) {
            $this->errors[] = $this->trans(
                'You do not have permission to edit this.',
                array(),
                'Admin.Notifications.Error'
            );

            return false;
        }

        $exporter = SymfonyContainer::getInstance()->get('prestashop.core.addon.theme.exporter');
        $path = $exporter->export($this->context->shop->theme);
        $this->confirmations[] = $this->trans(
            'Your theme has been correctly exported: %path%',
            ['%path%' => $path],
            'Admin.Notifications.Success'
        );

        return true;
    }

    /**
     * Specific postProcess for "submitAddconfiguration" action
     */
    protected function postProcessSubmitAddConfiguration()
    {
        try {
            $this->validateAddAuthorization();

            if ($filename = Tools::getValue('theme_archive_server')) {
                $path = _PS_ALL_THEMES_DIR_ . $filename;
                $this->theme_manager->install($path);
            } elseif ($filename = Tools::getValue('themearchive')) {
                $path        = _PS_ALL_THEMES_DIR_ . $filename;
                $destination = $this->processUploadFile($path);
                if (!empty($destination)) {
                    $this->theme_manager->install($destination);
                    @unlink($destination);
                }
            } elseif ($source = Tools::getValue('themearchiveUrl')) {
                $this->theme_manager->install($source);
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }

        if (empty($this->errors)) {
            $this->redirect_after = $this->context->link->getAdminLink('AdminThemes');
        }
    }

    /**
     * Specific postProcess for "submitConfigureLayouts" action
     */
    protected function postProcessSubmitConfigureLayouts()
    {
        try {
            $this->validateAllAuthorizations();
            $this->processSubmitConfigureLayouts();
            $this->redirect_after = $this->context->link->getAdminLink('AdminThemes');
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Specific postProcess for "enableTheme" action
     */
    protected function postProcessEnableTheme()
    {
        try {
            $this->validateAllAuthorizations();

            $isThemeEnabled = $this->theme_manager->enable(Tools::getValue('theme_name'));
            // get errors if theme wasn't enabled
            if (!$isThemeEnabled) {
                $this->errors[] = $this->theme_manager->getErrors(Tools::getValue('theme_name'));
            } else {
                Tools::clearSmartyCache();
                Tools::clearCache();
                $this->redirect_after = $this->context->link->getAdminLink('AdminThemes');
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Specific postProcess for "deleteTheme" action
     */
    protected function postProcessDeleteTheme()
    {
        try {
            $this->validateDeleteAuthorization();
            $this->theme_manager->uninstall(Tools::getValue('theme_name'));
            $this->redirect_after = $this->context->link->getAdminLink('AdminThemes');
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Specific postProcess for "submitGenerateRTL" action
     */
    protected function postProcessSubmitGenerateRTL()
    {
        if ((bool)Tools::getValue('PS_GENERATE_RTL')) {
            Language::getRtlStylesheetProcessor()
            ->setProcessFOThemes(array(Tools::getValue('PS_THEMES_LIST')))
            ->setRegenerate(true)
            ->process();

            $this->confirmations[] = $this->trans(
                'Your RTL stylesheets has been generated successfully',
                array(),
                'Admin.Design.Notification'
            );
        }
    }

    /**
     * Specific postProcess for "resetToDefaults" action
     */
    protected function postProcessResetToDefaults()
    {
        try {
            $this->validateAllAuthorizations();
            if ($this->theme_manager->reset(Tools::getValue('theme_name'))) {
                $this->confirmations[] = $this->trans(
                    'Your theme has been correctly reset to its default settings. You may want to regenerate your images. See the Improve > Design > Images Settings screen for the \'Regenerate thumbnails\' button.',
                    array(),
                    'Admin.Design.Notification'
                );
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Specific postProcess for "submitOptionsconfiguration" action
     */
    protected function postProcessSubmitOptionsConfiguration()
    {
        try {
            $this->validateAllAuthorizations();

            Configuration::updateValue('PS_IMG_UPDATE_TIME', time());

            if (Tools::getValue('PS_LOGO')) {
                $this->logo_uploader->updateHeader();
            }
            if (Tools::getValue('PS_LOGO_MAIL')) {
                $this->logo_uploader->updateMail();
            }
            if (Tools::getValue('PS_LOGO_INVOICE')) {
                $this->logo_uploader->updateInvoice();
            }
            if (Tools::getValue('PS_FAVICON')) {
                $this->logo_uploader->updateFavicon();
                $this->redirect_after = self::$currentIndex . '&token=' . $this->token;
            }

            Hook::exec('actionAdminThemesControllerUpdate_optionsAfter');
        } catch (PrestaShopException $e) {
            $this->errors[] = $e->getMessage();
            $this->addErrorToRedirectAfter();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }
}
