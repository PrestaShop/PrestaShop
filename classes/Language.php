<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Adapter\Language\LanguageImageManager;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\RTL\Processor as RtlStylesheetProcessor;
use PrestaShopBundle\Translation\TranslatorLanguageLoader;

class LanguageCore extends ObjectModel implements LanguageInterface
{
    const ALL_LANGUAGES_FILE = '/app/Resources/all_languages.json';
    const SF_LANGUAGE_PACK_URL = 'https://i18n.prestashop.com/translations/%version%/%locale%/%locale%.zip';
    const EMAILS_LANGUAGE_PACK_URL = 'https://i18n.prestashop.com/mails/%version%/%locale%/%locale%.zip';

    /**
     * Timeout for downloading a translation pack, in seconds
     */
    const PACK_DOWNLOAD_TIMEOUT = 20;

    public $id;

    /** @var string Name */
    public $name;

    /** @var string 2-letter iso code */
    public $iso_code;

    /** @var string 5-letter iso code */
    public $locale;

    /** @var string 5-letter iso code */
    public $language_code;

    /** @var string date format http://http://php.net/manual/en/function.date.php with the date only */
    public $date_format_lite = 'Y-m-d';

    /** @var string date format http://http://php.net/manual/en/function.date.php with hours and minutes */
    public $date_format_full = 'Y-m-d H:i:s';

    /** @var bool true if this language is right to left language */
    public $is_rtl = false;

    /** @var bool Status */
    public $active = true;

    protected static $_cache_language_installation = null;
    protected static $_cache_language_installation_by_locale = null;
    protected static $_cache_all_language_json = null;

    public static $locale_crowdin_lang = 'en-UD';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'lang',
        'primary' => 'id_lang',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
            'iso_code' => ['type' => self::TYPE_STRING, 'validate' => 'isLanguageIsoCode', 'required' => true, 'size' => 2],
            'locale' => ['type' => self::TYPE_STRING, 'validate' => 'isLocale', 'size' => 5],
            'language_code' => ['type' => self::TYPE_STRING, 'validate' => 'isLanguageCode', 'size' => 5],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'is_rtl' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_format_lite' => ['type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat', 'required' => true, 'size' => 32],
            'date_format_full' => ['type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat', 'required' => true, 'size' => 32],
        ],
    ];

    /** @var array Languages cache */
    protected static $_checkedLangs;
    /**
     * @var array[] language information, indexed by id_language
     *
     * @see loadLanguages()
     */
    protected static $_LANGUAGES;
    protected static $countActiveLanguages = [];

    protected $webserviceParameters = [
        'objectNodeName' => 'language',
        'objectsNodeName' => 'languages',
    ];

    protected $translationsFilesAndVars = [
        'fields' => '_FIELDS',
        'errors' => '_ERRORS',
        'admin' => '_LANGADM',
        'pdf' => '_LANGPDF',
        'tabs' => 'tabs',
    ];

    public static function resetCache()
    {
        self::$_checkedLangs = null;
        self::$_LANGUAGES = null;
        self::$countActiveLanguages = null;
        self::$_cache_language_installation = null;
        self::$_cache_language_installation_by_locale = null;
        self::$_cache_all_language_json = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        $this->iso_code = strtolower($this->iso_code);
        if (empty($this->language_code)) {
            $this->language_code = $this->iso_code;
        }

        return parent::getFields();
    }

    /**
     * Move translations files after editing language iso code.
     */
    public function moveToIso($newIso)
    {
        if ($newIso == $this->iso_code) {
            return true;
        }

        if (file_exists(_PS_TRANSLATIONS_DIR_ . $this->iso_code)) {
            rename(_PS_TRANSLATIONS_DIR_ . $this->iso_code, _PS_TRANSLATIONS_DIR_ . $newIso);
        }

        if (file_exists(_PS_MAIL_DIR_ . $this->iso_code)) {
            rename(_PS_MAIL_DIR_ . $this->iso_code, _PS_MAIL_DIR_ . $newIso);
        }

        $modulesList = Module::getModulesDirOnDisk();
        foreach ($modulesList as $moduleDir) {
            if (file_exists(_PS_MODULE_DIR_ . $moduleDir . '/mails/' . $this->iso_code)) {
                rename(_PS_MODULE_DIR_ . $moduleDir . '/mails/' . $this->iso_code, _PS_MODULE_DIR_ . $moduleDir . '/mails/' . $newIso);
            }

            if (file_exists(_PS_MODULE_DIR_ . $moduleDir . '/' . $this->iso_code . '.php')) {
                rename(_PS_MODULE_DIR_ . $moduleDir . '/' . $this->iso_code . '.php', _PS_MODULE_DIR_ . $moduleDir . '/' . $newIso . '.php');
            }
        }

        $themes = (new ThemeManagerBuilder(Context::getContext(), Db::getInstance()))
            ->buildRepository()
            ->getList();
        foreach ($themes as $theme) {
            /** @var Theme $theme */
            $theme_dir = $theme->getDirectory();
            if (file_exists(_PS_ALL_THEMES_DIR_ . $theme_dir . '/lang/' . $this->iso_code . '.php')) {
                rename(_PS_ALL_THEMES_DIR_ . $theme_dir . '/lang/' . $this->iso_code . '.php', _PS_ALL_THEMES_DIR_ . $theme_dir . '/lang/' . $newIso . '.php');
            }

            if (file_exists(_PS_ALL_THEMES_DIR_ . $theme_dir . '/mails/' . $this->iso_code)) {
                rename(_PS_ALL_THEMES_DIR_ . $theme_dir . '/mails/' . $this->iso_code, _PS_ALL_THEMES_DIR_ . $theme_dir . '/mails/' . $newIso);
            }

            foreach ($modulesList as $module) {
                if (file_exists(_PS_ALL_THEMES_DIR_ . $theme_dir . '/modules/' . $module . '/' . $this->iso_code . '.php')) {
                    rename(_PS_ALL_THEMES_DIR_ . $theme_dir . '/modules/' . $module . '/' . $this->iso_code . '.php', _PS_ALL_THEMES_DIR_ . $theme_dir . '/modules/' . $module . '/' . $newIso . '.php');
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add($autodate = true, $nullValues = false, $only_add = false)
    {
        if (!parent::add($autodate, $nullValues)) {
            return false;
        }

        if ($this->is_rtl) {
            self::getRtlStylesheetProcessor()
                ->setIsInstall(defined('PS_INSTALLATION_IN_PROGRESS'))
                ->setProcessBOTheme(true)
                ->setProcessDefaultModules(true)
                ->process();
        }

        if ($only_add) {
            return true;
        }

        // @todo Since a lot of modules are not in right format with their primary keys name, just get true ...
        $this->loadUpdateSQL();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function update($nullValues = false)
    {
        if (!parent::update($nullValues)) {
            return false;
        }

        // Generate RTL stylesheets if language is_rtl parameter changes
        if ($this->is_rtl) {
            self::getRtlStylesheetProcessor()
                ->setProcessBOTheme(true)
                ->setProcessDefaultModules(true)
                ->process();
        }

        return true;
    }

    /**
     * Checks if every files exists for this language
     *
     * @see checkFilesWithIsoCode()
     *
     * @return bool
     */
    public function checkFiles()
    {
        return Language::checkFilesWithIsoCode($this->iso_code);
    }

    /**
     * This functions checks if every files exists for the language $iso_code.
     * Concerned files are those located in translations/$iso_code/
     * and translations/mails/$iso_code .
     *
     * @param string $iso_code 2-letter ISO code
     *
     * @return bool True if all files exists
     */
    public static function checkFilesWithIsoCode($iso_code)
    {
        if (isset(self::$_checkedLangs[$iso_code]) && self::$_checkedLangs[$iso_code]) {
            return true;
        }

        foreach (array_keys(Language::getFilesList($iso_code, _THEME_NAME_, false, false, false, true)) as $key) {
            if (!file_exists($key)) {
                return false;
            }
        }
        self::$_checkedLangs[$iso_code] = true;

        return true;
    }

    /**
     * @param string $iso_from
     * @param string $theme_from
     * @param bool $iso_to
     * @param bool $theme_to
     * @param bool $select
     * @param bool $check
     * @param bool $modules
     *
     * @return string[]
     *
     * @throws PrestaShopException
     */
    public static function getFilesList($iso_from, $theme_from, $iso_to = false, $theme_to = false, $select = false, $check = false, $modules = false)
    {
        if (empty($iso_from)) {
            throw new PrestaShopException(sprintf('Invalid language ISO code: %s', $iso_from));
        }

        $copy = ($iso_to && $theme_to);

        $lPath_from = _PS_TRANSLATIONS_DIR_ . (string) $iso_from . '/';
        $tPath_from = _PS_ROOT_DIR_ . '/themes/' . (string) $theme_from . '/';
        $pPath_from = _PS_ROOT_DIR_ . '/themes/' . (string) $theme_from . '/pdf/';
        $mPath_from = _PS_MAIL_DIR_ . (string) $iso_from . '/';

        if ($copy) {
            $lPath_to = _PS_TRANSLATIONS_DIR_ . (string) $iso_to . '/';
            $tPath_to = _PS_ROOT_DIR_ . '/themes/' . (string) $theme_to . '/';
            $pPath_to = _PS_ROOT_DIR_ . '/themes/' . (string) $theme_to . '/pdf/';
            $mPath_to = _PS_MAIL_DIR_ . (string) $iso_to . '/';
        }

        $lFiles = ['admin.php', 'errors.php', 'fields.php', 'pdf.php', 'tabs.php'];

        // Added natives mails files
        $mFiles = [
            'account.html', 'account.txt',
            'backoffice_order.html', 'backoffice_order.txt',
            'bankwire.html', 'bankwire.txt',
            'cheque.html', 'cheque.txt',
            'contact.html', 'contact.txt',
            'contact_form.html', 'contact_form.txt',
            'credit_slip.html', 'credit_slip.txt',
            'download_product.html', 'download_product.txt',
            'employee_password.html', 'employee_password.txt',
            'forward_msg.html', 'forward_msg.txt',
            'guest_to_customer.html', 'guest_to_customer.txt',
            'import.html', 'import.txt',
            'in_transit.html', 'in_transit.txt',
            'log_alert.html', 'log_alert.txt',
            'newsletter.html', 'newsletter.txt',
            'order_canceled.html', 'order_canceled.txt',
            'order_changed.html', 'order_changed.txt',
            'order_conf.html', 'order_conf.txt',
            'order_customer_comment.html', 'order_customer_comment.txt',
            'order_merchant_comment.html', 'order_merchant_comment.txt',
            'order_return_state.html', 'order_return_state.txt',
            'outofstock.html', 'outofstock.txt',
            'password.html', 'password.txt',
            'password_query.html', 'password_query.txt',
            'payment.html', 'payment.txt',
            'payment_error.html', 'payment_error.txt',
            'preparation.html', 'preparation.txt',
            'refund.html', 'refund.txt',
            'reply_msg.html', 'reply_msg.txt',
            'shipped.html', 'shipped.txt',
            'test.html', 'test.txt',
            'voucher.html', 'voucher.txt',
            'voucher_new.html', 'voucher_new.txt',
        ];

        $number = -1;

        $files = [];
        $files_tr = [];
        $files_theme = [];
        $files_mail = [];
        $files_modules = [];

        // When a copy is made from a theme in specific language
        // to an other theme for the same language,
        // it's avoid to copy Translations, Mails files
        // and modules files which are not override by theme.
        if (!$copy || $iso_from != $iso_to) {
            // Translations files
            if (!$check || ($check && (string) $iso_from != 'en')) {
                foreach ($lFiles as $file) {
                    $files_tr[$lPath_from . $file] = ($copy ? $lPath_to . $file : ++$number);
                }
            }
            if ($select == 'tr') {
                return $files_tr;
            }
            $files = array_merge($files, $files_tr);

            // Mail files
            if (!$check || ($check && (string) $iso_from != 'en')) {
                $files_mail[$mPath_from . 'lang.php'] = ($copy ? $mPath_to . 'lang.php' : ++$number);
            }
            foreach ($mFiles as $file) {
                $files_mail[$mPath_from . $file] = ($copy ? $mPath_to . $file : ++$number);
            }
            if ($select == 'mail') {
                return $files_mail;
            }
            $files = array_merge($files, $files_mail);

            // Modules
            if ($modules) {
                $modList = Module::getModulesDirOnDisk();
                foreach ($modList as $mod) {
                    $modDir = _PS_MODULE_DIR_ . $mod;
                    // Lang file
                    if (file_exists($modDir . '/translations/' . (string) $iso_from . '.php')) {
                        $files_modules[$modDir . '/translations/' . (string) $iso_from . '.php'] = ($copy ? $modDir . '/translations/' . (string) $iso_to . '.php' : ++$number);
                    } elseif (file_exists($modDir . '/' . (string) $iso_from . '.php')) {
                        $files_modules[$modDir . '/' . (string) $iso_from . '.php'] = ($copy ? $modDir . '/' . (string) $iso_to . '.php' : ++$number);
                    }
                    // Mails files
                    $modMailDirFrom = $modDir . '/mails/' . (string) $iso_from;
                    $modMailDirTo = $modDir . '/mails/' . (string) $iso_to;
                    if (file_exists($modMailDirFrom)) {
                        $dirFiles = scandir($modMailDirFrom, SCANDIR_SORT_NONE);
                        foreach ($dirFiles as $file) {
                            if (file_exists($modMailDirFrom . '/' . $file) && $file != '.' && $file != '..' && $file != '.svn') {
                                $files_modules[$modMailDirFrom . '/' . $file] = ($copy ? $modMailDirTo . '/' . $file : ++$number);
                            }
                        }
                    }
                }
                if ($select == 'modules') {
                    return $files_modules;
                }
                $files = array_merge($files, $files_modules);
            }
        } elseif ($select == 'mail' || $select == 'tr') {
            return $files;
        }

        // Theme files
        if (!$check || ($check && (string) $iso_from != 'en')) {
            $files_theme[$tPath_from . 'lang/' . (string) $iso_from . '.php'] = ($copy ? $tPath_to . 'lang/' . (string) $iso_to . '.php' : ++$number);

            // Override for pdf files in the theme
            if (file_exists($pPath_from . 'lang/' . (string) $iso_from . '.php')) {
                $files_theme[$pPath_from . 'lang/' . (string) $iso_from . '.php'] = ($copy ? $pPath_to . 'lang/' . (string) $iso_to . '.php' : ++$number);
            }

            $module_theme_files = (file_exists($tPath_from . 'modules/') ? scandir($tPath_from . 'modules/', SCANDIR_SORT_NONE) : []);
            foreach ($module_theme_files as $module) {
                if ($module !== '.' && $module != '..' && $module !== '.svn' && file_exists($tPath_from . 'modules/' . $module . '/translations/' . (string) $iso_from . '.php')) {
                    $files_theme[$tPath_from . 'modules/' . $module . '/translations/' . (string) $iso_from . '.php'] = ($copy ? $tPath_to . 'modules/' . $module . '/translations/' . (string) $iso_to . '.php' : ++$number);
                }
            }
        }
        if ($select == 'theme') {
            return $files_theme;
        }
        $files = array_merge($files, $files_theme);

        // Return
        return $files;
    }

    /**
     * loadUpdateSQL will create default lang values when you create a new lang, based on current lang id.
     *
     * @return bool True if success
     */
    public function loadUpdateSQL()
    {
        $tables = Db::getInstance()->executeS('SHOW TABLES LIKE \'' . str_replace('_', '\\_', _DB_PREFIX_) . '%\_lang\' ');
        $langTables = [];

        foreach ($tables as $table) {
            foreach ($table as $t) {
                $langTables[] = $t;
            }
        }

        $return = true;

        /** @var Shop[] $shops */
        $shops = Shop::getShopsCollection(false);
        foreach ($shops as $shop) {
            // retrieve default language to duplicate database rows
            // this language is used later to untranslate/retranslate rows
            $shopDefaultLangId = Configuration::get('PS_LANG_DEFAULT', null, $shop->id_shop_group, $shop->id);

            foreach ($langTables as $name) {
                $return &= $this->duplicateRowsFromDefaultShopLang($name, $shopDefaultLangId, $shop->id);
            }
        }

        return $return;
    }

    /**
     * duplicate translated rows from xxx_lang tables
     * from the shop default language.
     *
     * @param $tableName
     * @param $shopDefaultLangId
     * @param $shopId
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     */
    private function duplicateRowsFromDefaultShopLang($tableName, $shopDefaultLangId, $shopId)
    {
        preg_match('#^' . preg_quote(_DB_PREFIX_) . '(.+)_lang$#i', $tableName, $m);
        $identifier = 'id_' . $m[1];

        $fields = [];
        // We will check if the table contains a column "id_shop"
        // If yes, we will add "id_shop" as a WHERE condition in queries copying data from default language
        $shop_field_exists = $primary_key_exists = false;
        $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . $tableName . '`');
        foreach ($columns as $column) {
            $fields[] = '`' . $column['Field'] . '`';
            if ($column['Field'] == 'id_shop') {
                $shop_field_exists = true;
            }
            if ($column['Field'] == $identifier) {
                $primary_key_exists = true;
            }
        }
        $fields = implode(',', $fields);

        if (!$primary_key_exists) {
            return true;
        }

        $sql = 'INSERT IGNORE INTO `' . $tableName . '` (' . $fields . ') (SELECT ';

        // For each column, copy data from default language
        reset($columns);
        $selectQueries = [];
        foreach ($columns as $column) {
            if ($identifier != $column['Field'] && $column['Field'] != 'id_lang') {
                $selectQueries[] = '(
							SELECT `' . bqSQL($column['Field']) . '`
							FROM `' . bqSQL($tableName) . '` tl
							WHERE tl.`id_lang` = ' . (int) $shopDefaultLangId . '
							' . ($shop_field_exists ? ' AND tl.`id_shop` = ' . (int) $shopId : '') . '
							AND tl.`' . bqSQL($identifier) . '` = `' . bqSQL(str_replace('_lang', '', $tableName)) . '`.`' . bqSQL($identifier) . '`
						)';
            } else {
                $selectQueries[] = '`' . bqSQL($column['Field']) . '`';
            }
        }
        $sql .= implode(',', $selectQueries);
        $sql .= ' FROM `' . _DB_PREFIX_ . 'lang` CROSS JOIN `' . bqSQL(str_replace('_lang', '', $tableName)) . '` ';

        // prevent insert with where initial data exists
        $sql .= ' WHERE `' . bqSQL($identifier) . '` IN (SELECT `' . bqSQL($identifier) . '` FROM `' . bqSQL($tableName) . '`) )';

        return Db::getInstance()->execute($sql);
    }

    /**
     * @deprecated 1.6.1.1 Use Tools::deleteDirectory($dir) instead
     *
     * @param string $dir is the path of the directory to delete
     */
    public static function recurseDeleteDir($dir)
    {
        return Tools::deleteDirectory($dir);
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        if (!$this->hasMultishopEntries() || Shop::getContext() == Shop::CONTEXT_ALL) {
            if (empty($this->iso_code)) {
                $this->iso_code = Language::getIsoById($this->id);
            }

            // Database translations deletion
            $result = Db::getInstance()->executeS('SHOW TABLES FROM `' . _DB_NAME_ . '`');
            $tableNameKey = 'Tables_in_' . _DB_NAME_;

            foreach ($result as $row) {
                if (isset($row[$tableNameKey]) && !empty($row[$tableNameKey]) && preg_match('/_lang$/', $row[$tableNameKey])) {
                    if (!Db::getInstance()->execute('DELETE FROM `' . $row[$tableNameKey] . '` WHERE `id_lang` = ' . (int) $this->id)) {
                        return false;
                    }
                }
            }

            // Delete tags
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'tag WHERE id_lang = ' . (int) $this->id);

            // Delete search words
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'search_word WHERE id_lang = ' . (int) $this->id);

            // Files deletion
            foreach (Language::getFilesList($this->iso_code, _THEME_NAME_, false, false, false, true, true) as $key => $file) {
                if (file_exists($key)) {
                    unlink($key);
                }
            }

            $modList = scandir(_PS_MODULE_DIR_, SCANDIR_SORT_NONE);
            foreach ($modList as $mod) {
                Tools::deleteDirectory(_PS_MODULE_DIR_ . $mod . '/mails/' . $this->iso_code);
                $files = @scandir(_PS_MODULE_DIR_ . $mod . '/mails/', SCANDIR_SORT_NONE);
                if (is_array($files) && count($files) <= 2) {
                    Tools::deleteDirectory(_PS_MODULE_DIR_ . $mod . '/mails/');
                }

                if (file_exists(_PS_MODULE_DIR_ . $mod . '/' . $this->iso_code . '.php')) {
                    unlink(_PS_MODULE_DIR_ . $mod . '/' . $this->iso_code . '.php');
                    $files = @scandir(_PS_MODULE_DIR_ . $mod, SCANDIR_SORT_NONE);
                    if (count($files) <= 2) {
                        Tools::deleteDirectory(_PS_MODULE_DIR_ . $mod);
                    }
                }
            }

            if (file_exists(_PS_MAIL_DIR_ . $this->iso_code)) {
                Tools::deleteDirectory(_PS_MAIL_DIR_ . $this->iso_code);
            }
            if (file_exists(_PS_TRANSLATIONS_DIR_ . $this->iso_code)) {
                Tools::deleteDirectory(_PS_TRANSLATIONS_DIR_ . $this->iso_code);
            }

            (new LanguageImageManager())->deleteImages($this->id, $this->iso_code);
        }

        if (!parent::delete()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSelection($selection)
    {
        if (!is_array($selection)) {
            die(Tools::displayError());
        }

        $result = true;
        foreach ($selection as $id) {
            $language = new Language($id);
            $result = $result && $language->delete();
        }

        return $result;
    }

    /**
     * Returns installed languages.
     *
     * @see loadLanguages()
     *
     * @param bool $active Select only active languages
     * @param int|false $id_shop Shop ID
     * @param bool $ids_only If true, returns an array of language IDs
     *
     * @return array[] Language information
     */
    public static function getLanguages($active = true, $id_shop = false, $ids_only = false)
    {
        if (!self::$_LANGUAGES) {
            Language::loadLanguages();
        }

        $languages = [];
        foreach (self::$_LANGUAGES as $language) {
            if ($active && !$language['active'] || ($id_shop && !isset($language['shops'][(int) $id_shop])) || self::$locale_crowdin_lang === $language['locale']) {
                continue;
            }

            $languages[] = $ids_only ? $language['id_lang'] : $language;
        }

        return $languages;
    }

    /**
     * Returns an array of installed language IDs.
     *
     * @param bool $active Select only active languages
     * @param int|false $id_shop Shop ID
     *
     * @return array
     */
    public static function getIDs($active = true, $id_shop = false)
    {
        return self::getLanguages($active, $id_shop, true);
    }

    /**
     * Returns installed language information for the provided id_lang
     *
     * @param int $id_lang Language Id
     *
     * @return array|false
     */
    public static function getLanguage($id_lang)
    {
        if (!self::$_LANGUAGES) {
            Language::loadLanguages();
        }
        if (!array_key_exists((int) $id_lang, self::$_LANGUAGES)) {
            return false;
        }

        return self::$_LANGUAGES[(int) ($id_lang)];
    }

    /**
     * Return iso code from id (installed languages only).
     *
     * @param int $id_lang Language ID
     *
     * @return string 2-letter ISO code
     */
    public static function getIsoById($id_lang)
    {
        if (!self::$_LANGUAGES) {
            Language::loadLanguages();
        }
        if (isset(self::$_LANGUAGES[(int) $id_lang]['iso_code'])) {
            return self::$_LANGUAGES[(int) $id_lang]['iso_code'];
        }

        return false;
    }

    /**
     * Returns language information form the all_languages file using IETF language tag
     *
     * @param string $locale IETF language tag
     *
     * @return array|false
     *
     * @throws Exception
     */
    public static function getJsonLanguageDetails($locale)
    {
        if (self::$_cache_all_language_json === null) {
            self::$_cache_all_language_json = [];
            $allLanguages = file_get_contents(_PS_ROOT_DIR_ . self::ALL_LANGUAGES_FILE);
            $allLanguages = json_decode($allLanguages, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new Exception(sprintf('The legacy to standard locales JSON could not be decoded %s', json_last_error_msg()));
            }

            foreach ($allLanguages as $isoCode => $langDetails) {
                self::$_cache_all_language_json[$langDetails['locale']] = $langDetails;
            }
        }

        return isset(self::$_cache_all_language_json[$locale]) ? self::$_cache_all_language_json[$locale] : false;
    }

    /**
     * Returns language id from iso code.
     *
     * @param string $iso_code Iso code
     * @param bool $no_cache
     *
     * @return int|null Language id, or null if not found
     */
    public static function getIdByIso($iso_code, $no_cache = false)
    {
        if (!Validate::isLanguageIsoCode($iso_code)) {
            throw new PrestaShopException(sprintf('Invalid language ISO code: %s', $iso_code));
        }

        $key = 'Language::getIdByIso_' . $iso_code;
        if ($no_cache || !Cache::isStored($key)) {
            $id_lang = Db::getInstance()->getValue('SELECT `id_lang` FROM `' . _DB_PREFIX_ . 'lang` WHERE `iso_code` = \'' . pSQL(strtolower($iso_code)) . '\'');

            Cache::store($key, $id_lang);

            return $id_lang ?: null;
        }

        return Cache::retrieve($key);
    }

    /**
     * Returns language id from locale
     *
     * @param string $locale Locale IETF language tag
     * @param bool $noCache
     *
     * @return int|false|null
     */
    public static function getIdByLocale($locale, $noCache = false)
    {
        $key = 'Language::getIdByLocale_' . $locale;
        if ($noCache || !Cache::isStored($key)) {
            $idLang = Db::getInstance()->getValue('SELECT `id_lang` FROM `' . _DB_PREFIX_ . 'lang` WHERE `locale` = \'' . pSQL(strtolower($locale)) . '\'');

            Cache::store($key, $idLang);

            return $idLang;
        }

        return Cache::retrieve($key);
    }

    /**
     * Returns language information from the all-languages file
     *
     * @param string $iso 2-letter ISO code
     *
     * @return array|false
     *
     * @throws Exception
     */
    public static function getLangDetails($iso)
    {
        $iso = (string) $iso; // $iso often comes from xml and is a SimpleXMLElement

        $allLanguages = file_get_contents(_PS_ROOT_DIR_ . self::ALL_LANGUAGES_FILE);
        $allLanguages = json_decode($allLanguages, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(sprintf('The legacy to standard locales JSON could not be decoded %s', json_last_error_msg()));
        }

        return isset($allLanguages[$iso]) ? $allLanguages[$iso] : false;
    }

    /**
     * Returns locale with iso parameter.
     *
     * @param string $isoCode 2-letter ISO code
     *
     * @return string|false
     *
     * @throws Exception
     */
    public static function getLocaleByIso($isoCode)
    {
        if (!Validate::isLanguageIsoCode($isoCode)) {
            throw new Exception('The ISO code ' . $isoCode . ' is invalid');
        }

        if ($details = self::getLangDetails($isoCode)) {
            return $details['locale'];
        }

        return false;
    }

    /**
     * Returns iso with locale parameter.
     *
     * @param string $locale
     *
     * @return string|false
     *
     * @throws Exception
     */
    public static function getIsoByLocale($locale)
    {
        if (!Validate::isLanguageCode($locale)) {
            throw new Exception('The locale ' . $locale . ' is invalid');
        }

        if ($details = self::getJsonLanguageDetails($locale)) {
            return $details['iso_code'];
        }

        return false;
    }

    /**
     * Retrieves a language code from an installed language using a 2-letter iso code
     *
     * @param string $iso_code 2-letter iso code
     *
     * @return string|false Returns the language code, or false if it doesn't exist
     *
     * @throws PrestaShopException
     */
    public static function getLanguageCodeByIso($iso_code)
    {
        if (!Validate::isLanguageIsoCode($iso_code)) {
            throw new PrestaShopException(sprintf('Invalid language ISO code: %s', $iso_code));
        }

        return Db::getInstance()->getValue('SELECT `language_code` FROM `' . _DB_PREFIX_ . 'lang` WHERE `iso_code` = \'' . pSQL(strtolower($iso_code)) . '\'');
    }

    /**
     * Retrieves an installed language by IETF language tag
     *
     * @param string $code IETF language tag
     *
     * @return Language|false
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getLanguageByIETFCode($code)
    {
        if (!Validate::isLanguageCode($code)) {
            throw new PrestaShopException(sprintf('Invalid IETF language tag: %s', $code));
        }

        // $code is in the form of 'xx-YY' where xx is the language code
        // and 'YY' a country code identifying a variant of the language.
        $lang_country = explode('-', $code);
        // Get the language component of the code
        $lang = $lang_country[0];

        // Find the id_lang of the language.
        // We look for anything with the correct language code
        // and sort on equality with the exact IETF code wanted.
        // That way using only one query we get either the exact wanted language
        // or a close match.
        $id_lang = Db::getInstance()->getValue(
            'SELECT `id_lang`, IF(language_code = \'' . pSQL($code) . '\', 0, LENGTH(language_code)) as found
			FROM `' . _DB_PREFIX_ . 'lang`
			WHERE LEFT(`language_code`,2) = \'' . pSQL($lang) . '\'
			ORDER BY found ASC'
        );

        // Instantiate the Language object if we found it.
        if (!$id_lang) {
            return false;
        }

        return new Language($id_lang);
    }

    /**
     * Return array (id_lang, iso_code).
     *
     * @param string $iso_code Iso code
     *
     * @return array Language (id_lang, iso_code)
     */
    public static function getIsoIds($active = true)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)
            ->executeS(
                'SELECT `id_lang`, `iso_code` FROM `' . _DB_PREFIX_ . 'lang` ' . ($active ? 'WHERE active = 1' : '')
            );
    }

    /**
     * Copies translated information in *_lang tables from one language to another
     *
     * @param int $from Source language id
     * @param int $to Destination language id
     *
     * @return true
     *
     * @throws PrestaShopDatabaseException
     */
    public static function copyLanguageData($from, $to)
    {
        $result = Db::getInstance()->executeS('SHOW TABLES FROM `' . _DB_NAME_ . '`');
        foreach ($result as $row) {
            if (preg_match('/_lang/', $row['Tables_in_' . _DB_NAME_]) && $row['Tables_in_' . _DB_NAME_] != _DB_PREFIX_ . 'lang') {
                $result2 = Db::getInstance()->executeS('SELECT * FROM `' . $row['Tables_in_' . _DB_NAME_] . '` WHERE `id_lang` = ' . (int) $from);
                if (!count($result2)) {
                    continue;
                }
                Db::getInstance()->execute('DELETE FROM `' . $row['Tables_in_' . _DB_NAME_] . '` WHERE `id_lang` = ' . (int) $to);
                $query = 'INSERT INTO `' . $row['Tables_in_' . _DB_NAME_] . '` VALUES ';
                foreach ($result2 as $row2) {
                    $query .= '(';
                    $row2['id_lang'] = $to;
                    foreach ($row2 as $field) {
                        $query .= (!is_string($field) && $field == null) ? 'NULL,' : '\'' . pSQL($field, true) . '\',';
                    }
                    $query = rtrim($query, ',') . '),';
                }
                $query = rtrim($query, ',');
                Db::getInstance()->execute($query);
            }
        }

        return true;
    }

    /**
     * Load all installed languages in memory for caching.
     */
    public static function loadLanguages()
    {
        self::$_LANGUAGES = [];

        $sql = 'SELECT l.*, ls.`id_shop`
				FROM `' . _DB_PREFIX_ . 'lang` l
				LEFT JOIN `' . _DB_PREFIX_ . 'lang_shop` ls ON (l.id_lang = ls.id_lang)';

        $result = Db::getInstance()->executeS($sql);
        if (!is_array($result)) {
            // executeS method can returns false
            return;
        }

        foreach ($result as $row) {
            $idLang = (int) $row['id_lang'];

            if (!isset(self::$_LANGUAGES[$idLang])) {
                self::$_LANGUAGES[$idLang] = $row;
            }
            self::$_LANGUAGES[$idLang]['shops'][(int) $row['id_shop']] = true;
        }
    }

    public static function loadLanguagesLegacy()
    {
        self::$_LANGUAGES = [];

        $result = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'lang`');

        foreach ($result as $row) {
            $idLang = (int) $row['id_lang'];

            if (!isset(self::$_LANGUAGES[$idLang])) {
                self::$_LANGUAGES[$idLang] = $row;
            }
            self::$_LANGUAGES[$idLang]['shops'][1] = true;
        }
    }

    public static function checkAndAddLanguage($iso_code, $lang_pack = false, $only_add = false, $params_lang = null)
    {
        if (Language::getIdByIso($iso_code)) {
            return true;
        }

        // Initialize the language
        $lang = new Language();
        $lang->iso_code = Tools::strtolower($iso_code);
        $lang->language_code = $iso_code; // Rewritten afterwards if the language code is available
        $lang->active = true;

        // If the language pack has not been provided, retrieve it from prestashop.com
        if (!$lang_pack) {
            $lang_pack = self::getLangDetails($iso_code);
        }

        // If a language pack has been found or provided, prefill the language object with the value
        if ($lang_pack) {
            foreach ($lang_pack as $key => $value) {
                if ($key != 'iso_code' && isset(Language::$definition['fields'][$key])) {
                    $lang->$key = $value;
                }
            }
        }

        // Use the values given in parameters to override the data retrieved automatically
        if ($params_lang !== null && is_array($params_lang)) {
            foreach ($params_lang as $key => $value) {
                if ($key != 'iso_code' && isset(Language::$definition['fields'][$key])) {
                    $lang->$key = $value;
                }
            }
        }

        if (!$lang->name && $lang->iso_code) {
            $lang->name = $lang->iso_code;
        }

        if (!$lang->validateFields() || !$lang->validateFieldsLang() || !$lang->add(true, false, $only_add)) {
            return false;
        }

        if (isset($params_lang['allow_accented_chars_url']) && in_array($params_lang['allow_accented_chars_url'], ['1', 'true'])) {
            Configuration::updateGlobalValue('PS_ALLOW_ACCENTED_CHARS_URL', 1);
        }

        $languageManager = new LanguageImageManager();
        $languageManager->setupLanguageFlag($lang->locale, $lang->id, $lang_pack['flag'] ?? null);
        $languageManager->setupDefaultImagePlaceholder($lang->iso_code);

        self::loadLanguages();

        return true;
    }

    /**
     * @deprecated Since 1.7.7, use LanguageImageManager
     */
    protected static function _copyNoneFlag($id)
    {
        @trigger_error(
            __FUNCTION__ . 'is deprecated since version 1.7.7. Use ' . LanguageImageManager::class . ' instead.',
            E_USER_DEPRECATED
        );

        return true;
    }

    public static function isInstalled($iso_code)
    {
        if (self::$_cache_language_installation === null) {
            self::$_cache_language_installation = [];
            $result = Db::getInstance()->executeS('SELECT `id_lang`, `iso_code` FROM `' . _DB_PREFIX_ . 'lang`');
            foreach ($result as $row) {
                self::$_cache_language_installation[$row['iso_code']] = $row['id_lang'];
            }
        }

        return isset(self::$_cache_language_installation[$iso_code]) ? self::$_cache_language_installation[$iso_code] : false;
    }

    public static function isInstalledByLocale($locale)
    {
        if (self::$_cache_language_installation_by_locale === null) {
            self::$_cache_language_installation_by_locale = [];
            $result = Db::getInstance()->executeS('SELECT `id_lang`, `locale` FROM `' . _DB_PREFIX_ . 'lang`');
            foreach ($result as $row) {
                self::$_cache_language_installation_by_locale[$row['locale']] = $row['id_lang'];
            }
        }

        return isset(self::$_cache_language_installation_by_locale[$locale]);
    }

    public static function countActiveLanguages($id_shop = null)
    {
        if (isset(Context::getContext()->shop) && is_object(Context::getContext()->shop) && $id_shop === null) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        if (!isset(self::$countActiveLanguages[$id_shop])) {
            self::$countActiveLanguages[$id_shop] = Db::getInstance()->getValue('
				SELECT COUNT(DISTINCT l.id_lang) FROM `' . _DB_PREFIX_ . 'lang` l
				JOIN ' . _DB_PREFIX_ . 'lang_shop lang_shop ON (lang_shop.id_lang = l.id_lang AND lang_shop.id_shop = ' . (int) $id_shop . ')
				WHERE l.`active` = 1
			');
        }

        return self::$countActiveLanguages[$id_shop];
    }

    public static function downloadAndInstallLanguagePack($iso, $version = _PS_VERSION_, $params = null, $install = true)
    {
        if (!Validate::isLanguageIsoCode((string) $iso)) {
            return false;
        }

        $errors = [];

        if (Language::downloadLanguagePack($iso, $version, $errors)) {
            if ($install) {
                Language::installLanguagePack($iso, $params, $errors);
            } else {
                Language::updateLanguagePack($iso, $errors);
            }
        }

        return count($errors) ? $errors : true;
    }

    public static function downloadLanguagePack($iso, $version, &$errors = [])
    {
        $iso = (string) $iso; // $iso often comes from xml and is a SimpleXMLElement

        $lang_pack = self::getLangDetails($iso);
        if (!$lang_pack) {
            $errors[] = Context::getContext()->getTranslator()->trans('Sorry this language is not available', [], 'Admin.International.Notification');
        } else {
            self::downloadXLFLanguagePack($lang_pack['locale'], $errors, 'sf');
        }

        return !count($errors);
    }

    public static function downloadXLFLanguagePack($locale, &$errors = [], $type = 'sf')
    {
        $file = _PS_TRANSLATIONS_DIR_ . $type . '-' . $locale . '.zip';
        $url = ('emails' === $type) ? self::EMAILS_LANGUAGE_PACK_URL : self::SF_LANGUAGE_PACK_URL;
        $url = str_replace(
            [
                '%version%',
                '%locale%',
            ],
            [
                _PS_VERSION_,
                $locale,
            ],
            $url
        );

        if (!is_writable(dirname($file))) {
            // @todo Throw exception
            $errors[] = Context::getContext()->getTranslator()->trans('Server does not have permissions for writing.', [], 'Admin.International.Notification') . ' (' . $file . ')';
        } elseif ($content = Tools::file_get_contents($url, false, null, self::PACK_DOWNLOAD_TIMEOUT)) {
            file_put_contents($file, $content);
        } else {
            $errors[] = Context::getContext()->getTranslator()->trans('Language pack unavailable.', [], 'Admin.International.Notification') . ' ' . $url;
        }
    }

    public static function installSfLanguagePack($locale, &$errors = [])
    {
        $zipFilePath = _PS_TRANSLATIONS_DIR_ . 'sf-' . $locale . '.zip';
        if (!file_exists($zipFilePath)) {
            // @todo Throw exception
            $errors[] = Context::getContext()->getTranslator()->trans('Language pack unavailable.', [], 'Admin.International.Notification');
        } else {
            $zipArchive = new ZipArchive();
            $zipArchive->open($zipFilePath);
            $zipArchive->extractTo(_PS_ROOT_DIR_ . '/app/Resources/translations');
            $zipArchive->close();
        }
    }

    /**
     * @param array $langPack
     * @param array $errors
     * @param bool $overwriteTemplates
     */
    private static function generateEmailsLanguagePack($langPack, &$errors = [], $overwriteTemplates = false)
    {
        $locale = $langPack['locale'];
        $sfContainer = SymfonyContainer::getInstance();
        if (null === $sfContainer) {
            $errors[] = Context::getContext()->getTranslator()->trans(
                'Cannot generate emails because the Symfony container is unavailable.',
                [],
                'Admin.Notifications.Error'
            );

            return;
        }

        $mailTheme = Configuration::get('PS_MAIL_THEME', null, null, null, 'modern');
        /** @var GenerateThemeMailTemplatesCommand $generateCommand */
        $generateCommand = new GenerateThemeMailTemplatesCommand(
            $mailTheme,
            $locale,
            $overwriteTemplates
        );
        /** @var CommandBusInterface $commandBus */
        $commandBus = $sfContainer->get('prestashop.core.command_bus');
        try {
            $commandBus->handle($generateCommand);
        } catch (CoreException $e) {
            $errors[] = Context::getContext()->getTranslator()->trans(
                'Cannot generate email templates: %s.',
                [$e->getMessage()],
                'Admin.Notifications.Error'
            );
        }
    }

    /**
     * @param array $lang_pack
     * @param array $errors
     *
     * @deprecated This method is deprecated since 1.7.6.0 use GenerateThemeMailsCommand instead
     */
    public static function installEmailsLanguagePack($lang_pack, &$errors = [])
    {
        @trigger_error(
            'Language::installEmailsLanguagePack() is deprecated since version 1.7.6.0 Use GenerateThemeMailsCommand instead.',
            E_USER_DEPRECATED
        );

        self::generateEmailsLanguagePack($lang_pack, $errors, true);
    }

    public static function installLanguagePack($iso, $params, &$errors = [])
    {
        // Clear smarty modules cache
        Tools::clearCache();

        if (!Language::checkAndAddLanguage((string) $iso, false, false, $params)) {
            $errors[] = Context::getContext()->getTranslator()->trans('An error occurred while creating the language: %s', [(string) $iso], 'Admin.International.Notification');
        } else {
            // Reset cache
            Language::loadLanguages();
        }

        $lang_pack = self::getLangDetails($iso);
        self::installSfLanguagePack(self::getLocaleByIso($iso), $errors);
        self::updateMultilangTable($iso);
        self::updateCurrenciesCldr(new Language(self::getIdByIso($iso, true)));
        self::generateEmailsLanguagePack($lang_pack, $errors, true);

        return count($errors) ? $errors : true;
    }

    private static function updateCurrenciesCldr(Language $language)
    {
        /** @var Currency[] $currencies */
        $currencies = Currency::getCurrencies(true, false, false);
        $container = SymfonyContainer::getInstance();
        /** @var LocaleRepository $localeRepoCLDR */
        $localeRepoCLDR = $container->get('prestashop.core.localization.cldr.locale_repository');
        $localeCLDR = $localeRepoCLDR->getLocale($language->locale);

        foreach ($currencies as $currency) {
            $names = $currency->getLocalizedNames();
            $symbols = $currency->getLocalizedSymbols();

            $currencyCLDR = $localeCLDR->getCurrency($currency->iso_code);
            if (null === $currencyCLDR) {
                continue;
            }

            $names[$language->id] = $currencyCLDR->getDisplayName();
            $symbols[$language->id] = $currencyCLDR->getSymbol();

            $currency->setLocalizedNames($names);
            $currency->setLocalizedSymbols($symbols);

            $currency->save();
        }
    }

    public static function updateLanguagePack($iso, &$errors = [])
    {
        $lang_pack = self::getLangDetails($iso);
        if (!empty($lang_pack['locale'])) {
            //Update locale field if empty (manually created, or imported without it)
            $language = new Language(Language::getIdByIso($iso));
            if ($language->id && empty($language->locale)) {
                $language->locale = $lang_pack['locale'];
                $language->save();
            }

            self::installSfLanguagePack($lang_pack['locale'], $errors);
            Language::updateMultilangTable($iso);
            self::generateEmailsLanguagePack($lang_pack, $errors, false);
        }
    }

    /**
     * Check if more on than one language is activated.
     *
     * @since 1.5.0
     *
     * @return bool
     */
    public static function isMultiLanguageActivated($id_shop = null)
    {
        return Language::countActiveLanguages($id_shop) > 1;
    }

    public static function getLanguagePackListContent($iso, $tar)
    {
        $key = 'Language::getLanguagePackListContent_' . $iso;
        if (!Cache::isStored($key)) {
            if (!$tar instanceof \Archive_Tar) {
                return false;
            }
            $result = $tar->listContent();
            Cache::store($key, $result);

            return $result;
        }

        return Cache::retrieve($key);
    }

    /**
     * Updates multilanguage tables in all languages using DataLang
     *
     * @param array $modules_list [deprecated since 1.7.7] Not used anymore
     */
    public static function updateModulesTranslations(array $modules_list = [])
    {
        $languages = static::getLanguages(false);
        foreach ($languages as $lang) {
            static::updateMultilangTable($lang['iso_code']);
        }
    }

    /**
     * Update all table_lang from xlf & DataLang.
     *
     * @param string $iso_code 2-letter language code
     *
     * @return bool
     */
    public static function updateMultilangTable($iso_code)
    {
        $langId = Language::getIdByIso($iso_code);

        if (!empty($langId)) {
            $lang = new Language($langId);

            $rows = Db::getInstance()->executeS('SHOW TABLES LIKE \'' . str_replace('_', '\\_', _DB_PREFIX_) . '%\_lang\' ');
            if (!empty($rows)) {
                // get all values
                $tableNames = array_map('reset', $rows);
                static::updateMultilangTables($lang, $tableNames);
            }
        }

        return true;
    }

    /**
     * Translates translatable content in the requested database tables
     *
     * @param Language $language Language to translate to
     * @param string[] $tablesToUpdate Tables to update (including datbase prefix, ending in _lang)
     *
     * @throws PrestaShopException
     */
    public static function updateMultilangTables(Language $language, array $tablesToUpdate)
    {
        foreach ($tablesToUpdate as $tableName) {
            $className = ucfirst(Tools::toCamelCase(str_replace(_DB_PREFIX_, '', $tableName)));

            if (_DB_PREFIX_ . 'country_lang' === $tableName) {
                self::updateMultilangFromCldr($language);
            } else {
                self::updateMultilangFromClass($tableName, $className, $language);
            }
        }

        Hook::exec('actionUpdateLangAfter', ['lang' => $language]);
    }

    public static function updateMultilangFromCldr($lang)
    {
        $cldrLocale = $lang->getLocale();
        $cldrFile = _PS_TRANSLATIONS_DIR_ . 'cldr/datas/main/' . $cldrLocale . '/territories.json';

        if (file_exists($cldrFile)) {
            $cldrContent = json_decode(file_get_contents($cldrFile), true);

            if (!empty($cldrContent)) {
                $translatableCountries = Db::getInstance()->executeS('SELECT c.`iso_code`, cl.* FROM `' . _DB_PREFIX_ . 'country` c
                    INNER JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON c.`id_country` = cl.`id_country`
                    WHERE cl.`id_lang` = "' . (int) $lang->id . '" ', true, false);

                if (!empty($translatableCountries)) {
                    $cldrLanguages = $cldrContent['main'][$cldrLocale]['localeDisplayNames']['territories'];

                    foreach ($translatableCountries as $country) {
                        if (isset($cldrLanguages[$country['iso_code']]) &&
                            !empty($cldrLanguages[$country['iso_code']])
                        ) {
                            $sql = 'UPDATE `' . _DB_PREFIX_ . 'country_lang`
                                SET `name` = "' . pSQL(ucwords($cldrLanguages[$country['iso_code']])) . '"
                                WHERE `id_country` = "' . (int) $country['id_country'] . '" AND `id_lang` = "' . (int) $lang->id . '" LIMIT 1;';
                            Db::getInstance()->execute($sql);
                        }
                    }
                }
            }
        }
    }

    /**
     * Updates multilang tables using DataLang classes
     *
     * @param string $table
     * @param string $className
     * @param Language $lang
     *
     * @throws PrestaShopDatabaseException
     */
    public static function updateMultilangFromClass($table, $className, $lang)
    {
        if (!class_exists($className)) {
            return;
        }

        /** @var DataLangCore $classObject */
        $classObject = new $className($lang->locale);

        $keys = $classObject->getKeys();
        $fieldsToUpdate = $classObject->getFieldsToUpdate();

        if (!empty($keys) && !empty($fieldsToUpdate)) {
            $shops = Shop::getShopsCollection(false);
            foreach ($shops as $shop) {
                static::updateMultilangFromClassForShop($table, $classObject, $lang, $shop, $keys, $fieldsToUpdate);
            }
        }
    }

    /**
     * untranslate then re-translate duplicated rows in tables with pattern xxx_lang.
     *
     * @param string $tableName
     * @param DataLang $classObject
     * @param Language $lang
     * @param Shop $shop
     * @param array $keys
     * @param array $fieldsToUpdate
     *
     * @throws \PrestaShopDatabaseException
     */
    private static function updateMultilangFromClassForShop($tableName, $classObject, $lang, $shop, $keys, $fieldsToUpdate)
    {
        $shopFieldExists = false;
        $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . $tableName . '`');
        foreach ($columns as $column) {
            $fields[] = '`' . $column['Field'] . '`';
            if ($column['Field'] == 'id_shop') {
                $shopFieldExists = true;
            }
        }

        // get table data
        $tableData = Db::getInstance()->executeS(
            'SELECT * FROM `' . bqSQL($tableName) . '`
            WHERE `id_lang` = "' . (int) $lang->id . '"'
            . ($shopFieldExists ? ' AND `id_shop` = ' . (int) $shop->id : ''),
            true,
            false
        );

        if (!empty($tableData)) {
            $shopDefaultLangId = Configuration::get('PS_LANG_DEFAULT', null, $shop->id_shop_group, $shop->id);
            $shopDefaultLanguage = new Language($shopDefaultLangId);

            $translator = SymfonyContainer::getInstance()->get('translator');
            if (!$translator->isLanguageLoaded($shopDefaultLanguage->locale)) {
                (new TranslatorLanguageLoader(true))->loadLanguage($translator, $shopDefaultLanguage->locale);
            }

            foreach ($tableData as $data) {
                $updateWhere = '';
                $updateField = '';

                // Construct update where
                foreach ($keys as $key) {
                    if (!empty($updateWhere)) {
                        $updateWhere .= ' AND ';
                    }
                    $updateWhere .= '`' . bqSQL($key) . '` = "' . pSQL($data[$key]) . '"';
                }

                // Construct update field
                foreach ($fieldsToUpdate as $toUpdate) {
                    if ('url_rewrite' === $toUpdate && self::$locale_crowdin_lang === $lang->locale) {
                        continue;
                    }

                    $untranslated = $translator->getSourceString($data[$toUpdate], $classObject->getDomain());
                    $translatedField = $classObject->getFieldValue($toUpdate, $untranslated);

                    if (!empty($translatedField) && $translatedField != $data[$toUpdate]) {
                        if (!empty($updateField)) {
                            $updateField .= ' , ';
                        }
                        $updateField .= '`' . bqSQL($toUpdate) . '` = "' . pSQL($translatedField) . '"';
                    }
                }

                // Update table
                if (!empty($updateWhere) && !empty($updateField)) {
                    $sql = 'UPDATE `' . bqSQL($tableName) . '` SET ' . $updateField . '
                    WHERE ' . $updateWhere . ' AND `id_lang` = "' . (int) $lang->id . '"
                    ' . ($shopFieldExists ? ' AND `id_shop` = ' . (int) $shop->id : '') . '
                    LIMIT 1;';
                    Db::getInstance()->execute($sql);
                }
            }
        }
    }

    /**
     * Returns an RTL stylesheet processor instance.
     *
     * @return RtlStylesheetProcessor
     */
    public static function getRtlStylesheetProcessor()
    {
        if (defined('_PS_ADMIN_DIR_')) {
            $adminDir = _PS_ADMIN_DIR_;
        } else {
            $adminDir = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'admin';
            $adminDir = (is_dir($adminDir)) ? $adminDir : ($adminDir . '-dev');
        }

        $themesDir = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'themes';

        $processor = new RtlStylesheetProcessor(
            $adminDir,
            $themesDir,
            [
                _PS_MODULE_DIR_ . 'gamification',
                _PS_MODULE_DIR_ . 'welcome',
                _PS_MODULE_DIR_ . 'cronjobs',
            ]
        );

        return $processor;
    }

    /**
     * @return string return the language locale, or its code by default
     */
    public function getLocale()
    {
        return !empty($this->locale) ?
            $this->locale :
            $this->language_code;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsoCode()
    {
        return $this->iso_code;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageCode()
    {
        return $this->language_code;
    }

    /**
     * {@inheritdoc}
     */
    public function isRTL()
    {
        return $this->is_rtl;
    }
}
