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
use PrestaShop\PrestaShop\Adapter\EntityTranslation\DataLangFactory;
use PrestaShop\PrestaShop\Adapter\EntityTranslation\EntityTranslatorFactory;
use PrestaShop\PrestaShop\Adapter\EntityTranslation\Exception\DataLangClassNameNotFoundException;
use PrestaShop\PrestaShop\Adapter\Language\LanguageImageManager;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\RTL\Processor as RtlStylesheetProcessor;
use PrestaShopBundle\Translation\TranslatorInterface;
use Symfony\Component\Intl\Countries;

class LanguageCore extends ObjectModel implements LanguageInterface
{
    public const ALL_LANGUAGES_FILE = '/app/Resources/all_languages.json';
    public const SF_LANGUAGE_PACK_URL = 'https://i18n.prestashop-project.org/translations/%version%/%locale%/%locale%.zip';
    public const EMAILS_LANGUAGE_PACK_URL = 'https://i18n.prestashop-project.org/mails/%version%/%locale%/%locale%.zip';
    public const PACK_TYPE_EMAILS = 'emails';
    public const PACK_TYPE_SYMFONY = 'sf';

    /**
     * Timeout for downloading a translation pack, in seconds
     */
    public const PACK_DOWNLOAD_TIMEOUT = 20;

    /**
     * Path to the local translation pack cache directory.
     * This is usually `/translations`.
     */
    private const TRANSLATION_PACK_CACHE_DIR = _PS_TRANSLATIONS_DIR_;

    /** Path to the symfony translations directory */
    private const SF_TRANSLATIONS_DIR = _PS_ROOT_DIR_ . '/translations';

    /** @var int */
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
    public $date_format_lite = 'Y‑m‑d'; // note the use of non-breaking hyphens (U+2011)

    /** @var string date format http://http://php.net/manual/en/function.date.php with hours and minutes */
    public $date_format_full = 'Y‑m‑d H:i:s'; // note the use of non-breaking hyphens (U+2011)

    /** @var bool true if this language is right to left language */
    public $is_rtl = false;

    /** @var bool Status */
    public $active = true;

    protected static $_cache_language_installation = null;

    protected static $_cache_language_installation_by_locale = null;

    /** @var array|null Contains data from all languages, indexed by locale */
    protected static $_cache_all_language_json = null;

    /** @var array|null Contains data from all languages, indexed by iso code */
    protected static $_cache_all_languages_iso;

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

    /** @var array|null Languages cache */
    protected static $_checkedLangs;
    /**
     * @var array[]|null language information, indexed by id_language
     *
     * @see loadLanguages()
     */
    protected static $_LANGUAGES;
    protected static $countActiveLanguages = [];

    protected $webserviceParameters = [
        'objectNodeName' => 'language',
        'objectsNodeName' => 'languages',
    ];

    public static function resetStaticCache()
    {
        parent::resetStaticCache();
        static::$loaded_classes = [];
        static::resetCache();
    }

    public static function resetCache()
    {
        static::$_checkedLangs = null;
        static::$_LANGUAGES = null;
        static::$countActiveLanguages = null;
        static::$_cache_language_installation = null;
        static::$_cache_language_installation_by_locale = null;
        static::$_cache_all_language_json = null;
        static::$_cache_all_languages_iso = null;
        Cache::clean('Language::*');
    }

    /**
     * Loads details for all languages
     *
     * @return array Data from all languages, indexed by iso code
     *
     * @throws RuntimeException If the details cannot be loaded for any reason
     */
    private static function loadAllLanguagesDetails(): array
    {
        if (null === static::$_cache_all_languages_iso) {
            $allLanguages = file_get_contents(_PS_ROOT_DIR_ . self::ALL_LANGUAGES_FILE);
            static::$_cache_all_languages_iso = json_decode($allLanguages, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new RuntimeException(
                    sprintf('The legacy to standard locales JSON could not be decoded %s', json_last_error_msg())
                );
            }
        }

        return static::$_cache_all_languages_iso;
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
            static::getRtlStylesheetProcessor()
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
            static::getRtlStylesheetProcessor()
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
        if (isset(static::$_checkedLangs[$iso_code]) && static::$_checkedLangs[$iso_code]) {
            return true;
        }

        foreach (array_keys(Language::getFilesList($iso_code, _THEME_NAME_, false, false, false, true)) as $key) {
            if (!file_exists($key)) {
                return false;
            }
        }
        static::$_checkedLangs[$iso_code] = true;

        return true;
    }

    /**
     * @param string $iso_from
     * @param string $theme_from
     * @param string|bool $iso_to
     * @param string|bool $theme_to
     * @param bool|string $select
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

        $lPath_to = $tPath_to = $pPath_to = $mPath_to = '';
        if ($copy) {
            $lPath_to = _PS_TRANSLATIONS_DIR_ . (string) $iso_to . '/';
            $tPath_to = _PS_ROOT_DIR_ . '/themes/' . (string) $theme_to . '/';
            $pPath_to = _PS_ROOT_DIR_ . '/themes/' . (string) $theme_to . '/pdf/';
            $mPath_to = _PS_MAIL_DIR_ . (string) $iso_to . '/';
        }

        $lFiles = ['admin.php', 'errors.php', 'pdf.php', 'tabs.php'];

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
            if (!$check || ((string) $iso_from) != 'en') {
                foreach ($lFiles as $file) {
                    $files_tr[$lPath_from . $file] = ($copy ? $lPath_to . $file : ++$number);
                }
            }
            if ($select == 'tr') {
                return $files_tr;
            }
            $files = array_merge($files, $files_tr);

            // Mail files
            if (!$check || ((string) $iso_from) != 'en') {
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
        if (!$check || ((string) $iso_from) != 'en') {
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
            $shopDefaultLangId = (int) Configuration::get('PS_LANG_DEFAULT', null, $shop->id_shop_group, $shop->id);

            foreach ($langTables as $name) {
                $return &= $this->duplicateRowsFromDefaultShopLang($name, $shopDefaultLangId, $shop->id);
            }
        }

        return $return;
    }

    /**
     * Creates new entries in _lang tables using the data from default language.
     * If data already exist in that language, they won't be overwritten.
     *
     * @param string $tableName
     * @param int $shopDefaultLangId
     * @param int $shopId
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     */
    private function duplicateRowsFromDefaultShopLang($tableName, $shopDefaultLangId, $shopId)
    {
        // We load all columns from the table
        $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . $tableName . '`');

        // We check if the table contains a column "id_shop".
        // If yes, we will add "id_shop" as a WHERE condition in queries copying data from default language.
        $idShopColumnExists = false;
        $idLangColumnExists = false;

        // We extract the column names from the table
        $columnNames = [];
        foreach ($columns as $column) {
            // Build field to our list
            $columnNames[] = $column['Field'];

            // Save info that we will need to use id_shop
            if ($column['Field'] == 'id_shop') {
                $idShopColumnExists = true;
            }

            // Check if the table actually has id_lang column, so we don't crash for some custom tables
            if ($column['Field'] == 'id_lang') {
                $idLangColumnExists = true;
            }
        }

        // If the table does not contain id_lang, nothing to do here
        if ($idLangColumnExists === false) {
            return true;
        }

        // Format insert fields, they are just normal column names without any prefix.
        $insertFields = [];
        foreach ($columnNames as $columnName) {
            $insertFields[] = '`' . $columnName . '`';
        }

        /*
         * Now let's format select fields. We will prefix every column with our table prefix, with one exception.
         *
         * For language field, we will use the unique language ID from lang table we cross join. Otherwise we would
         * be inserting the ID of default language for every row.
         */
        $selectFields = [];
        foreach ($columnNames as $columnName) {
            if ($columnName == 'id_lang') {
                $selectFields[] = 'l.`id_lang`';
            } else {
                $selectFields[] = 'tl.`' . $columnName . '`';
            }
        }

        // Format the SQL query and run it
        // Entries which already exist are ignored, only new ones are added.
        $sql = '
        INSERT IGNORE INTO `' . $tableName . '` (' . implode(', ', $insertFields) . ')
        SELECT ' . implode(', ', $selectFields) . '
        FROM `' . $tableName . '` tl
        CROSS JOIN `' . _DB_PREFIX_ . 'lang` l
        WHERE tl.id_lang = ' . (int) $shopDefaultLangId .
        ($idShopColumnExists ? ' AND tl.`id_shop` = ' . (int) $shopId : '');

        return Db::getInstance()->execute($sql);
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

            // Now let's delete all entries in _lang tables for given languages
            $result = Db::getInstance()->executeS('SHOW TABLES FROM `' . _DB_NAME_ . '`');

            // A key we will be searching for, database returns it in this weird format
            $tableNameKey = 'Tables_in_' . _DB_NAME_;

            foreach ($result as $row) {
                // If we received empty table name for some reason or the language name does not end with _lang
                if (empty($row[$tableNameKey]) || !preg_match('/_lang$/', $row[$tableNameKey])) {
                    continue;
                }

                // We check if this table contains id_lang column
                $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . $row[$tableNameKey] . '`');
                $idLangColumnExists = false;
                foreach ($columns as $column) {
                    if ($column['Field'] == 'id_lang') {
                        $idLangColumnExists = true;
                    }
                }

                // If it doesn't, nothing to delete
                if ($idLangColumnExists === false) {
                    continue;
                }

                // Delete all entries for this language ID
                Db::getInstance()->execute('DELETE FROM `' . $row[$tableNameKey] . '` WHERE `id_lang` = ' . (int) $this->id);
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
                if (!is_dir(_PS_MODULE_DIR_ . $mod)) {
                    continue;
                }
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
    public function deleteSelection(array $selection)
    {
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
     * @param int|bool $id_shop Shop ID
     * @param bool $ids_only If true, returns an array of language IDs
     *
     * @return array<int|array> Language information
     */
    public static function getLanguages($active = true, $id_shop = false, $ids_only = false)
    {
        if (!static::$_LANGUAGES) {
            Language::loadLanguages();
        }

        $languages = [];
        foreach (static::$_LANGUAGES as $language) {
            if ($active && !$language['active'] || ($id_shop && !isset($language['shops'][(int) $id_shop])) || static::$locale_crowdin_lang === $language['locale']) {
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
        return static::getLanguages($active, $id_shop, true);
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
        if (!static::$_LANGUAGES) {
            Language::loadLanguages();
        }
        if (!array_key_exists((int) $id_lang, static::$_LANGUAGES)) {
            return false;
        }

        return static::$_LANGUAGES[(int) ($id_lang)];
    }

    /**
     * Return iso code from id (installed languages only).
     *
     * @param int $id_lang Language ID
     *
     * @return string|bool 2-letter ISO code
     */
    public static function getIsoById($id_lang)
    {
        if (!static::$_LANGUAGES) {
            Language::loadLanguages();
        }
        if (isset(static::$_LANGUAGES[(int) $id_lang]['iso_code'])) {
            return static::$_LANGUAGES[(int) $id_lang]['iso_code'];
        }

        return false;
    }

    /**
     * Provides locale by language id (e.g. en-US, fr-FR, ru-RU)
     *
     * @param int $langId
     *
     * @return string|null
     */
    public static function getLocaleById(int $langId): ?string
    {
        $locale = Db::getInstance()->getValue('
            SELECT `locale` FROM `' . _DB_PREFIX_ . 'lang` WHERE `id_lang` = ' . $langId
        );

        if (!$locale) {
            return null;
        }

        return $locale;
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
        if (static::$_cache_all_language_json === null) {
            static::$_cache_all_language_json = [];
            $allLanguages = self::loadAllLanguagesDetails();

            foreach ($allLanguages as $isoCode => $langDetails) {
                static::$_cache_all_language_json[$langDetails['locale']] = $langDetails;
            }
        }

        return isset(static::$_cache_all_language_json[$locale]) ? static::$_cache_all_language_json[$locale] : false;
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
            if (empty($id_lang)) {
                return null;
            }

            Cache::store($key, $id_lang);

            return (int) $id_lang;
        }

        return (int) Cache::retrieve($key);
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
            $idLang = Db::getInstance()
                ->getValue(
                    'SELECT `id_lang` FROM `' . _DB_PREFIX_ . 'lang`
                    WHERE `locale` = \'' . pSQL(strtolower($locale)) . '\'
                    OR `language_code` = \'' . pSQL(strtolower($locale)) . '\''
                );

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
     * @return string[]|false
     *
     * @throws Exception
     */
    public static function getLangDetails($iso)
    {
        $iso = (string) $iso; // $iso often comes from xml and is a SimpleXMLElement

        $allLanguages = self::loadAllLanguagesDetails();

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

        if ($details = static::getLangDetails($isoCode)) {
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

        if ($details = static::getJsonLanguageDetails($locale)) {
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

        return new Language((int) $id_lang);
    }

    /**
     * Return array (id_lang, iso_code).
     *
     * @param bool $active Select only active languages
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
                /** @var array<string, int|string|null> $row2 */
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
        static::$_LANGUAGES = [];

        $sql = 'SELECT l.*, ls.`id_shop`
				FROM `' . _DB_PREFIX_ . 'lang` l
				LEFT JOIN `' . _DB_PREFIX_ . 'lang_shop` ls ON (l.id_lang = ls.id_lang)';

        $result = Db::getInstance()->executeS($sql);
        if (!is_array($result)) {
            // executeS method can return false
            return;
        }

        foreach ($result as $row) {
            $idLang = (int) $row['id_lang'];

            if (!isset(static::$_LANGUAGES[$idLang])) {
                static::$_LANGUAGES[$idLang] = $row;
            }
            static::$_LANGUAGES[$idLang]['shops'][(int) $row['id_shop']] = true;
        }
    }

    public static function loadLanguagesLegacy()
    {
        static::$_LANGUAGES = [];

        $result = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'lang`');

        foreach ($result as $row) {
            $idLang = (int) $row['id_lang'];

            if (!isset(static::$_LANGUAGES[$idLang])) {
                static::$_LANGUAGES[$idLang] = $row;
            }
            static::$_LANGUAGES[$idLang]['shops'][1] = true;
        }
    }

    /**
     * Adds a language
     *
     * @param string $iso_code 2-letter language ISO code
     * @param array|false $lang_pack [default=false] Pack information. By default, this is automatically retrieved from all_languages.json.
     * @param bool $only_add [default=false] If true, do not create copies of translated fields in *_lang tables
     * @param ?array $params_lang [default=null] See allow_accented_chars_url
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
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
            $lang_pack = static::getLangDetails($iso_code);
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

        static::loadLanguages();

        return true;
    }

    public static function isInstalled($iso_code)
    {
        if (static::$_cache_language_installation === null) {
            static::$_cache_language_installation = [];
            $result = Db::getInstance()->executeS('SELECT `id_lang`, `iso_code` FROM `' . _DB_PREFIX_ . 'lang`');
            foreach ($result as $row) {
                static::$_cache_language_installation[$row['iso_code']] = $row['id_lang'];
            }
        }

        return isset(static::$_cache_language_installation[$iso_code]) ? static::$_cache_language_installation[$iso_code] : false;
    }

    public static function isInstalledByLocale($locale)
    {
        if (static::$_cache_language_installation_by_locale === null) {
            static::$_cache_language_installation_by_locale = [];
            $result = Db::getInstance()->executeS('SELECT `id_lang`, `locale` FROM `' . _DB_PREFIX_ . 'lang`');
            foreach ($result as $row) {
                static::$_cache_language_installation_by_locale[$row['locale']] = $row['id_lang'];
            }
        }

        return isset(static::$_cache_language_installation_by_locale[$locale]);
    }

    public static function countActiveLanguages($id_shop = null)
    {
        if (isset(Context::getContext()->shop) && is_object(Context::getContext()->shop) && $id_shop === null) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        if (!isset(static::$countActiveLanguages[$id_shop])) {
            static::$countActiveLanguages[$id_shop] = Db::getInstance()->getValue('
				SELECT COUNT(DISTINCT l.id_lang) FROM `' . _DB_PREFIX_ . 'lang` l
				JOIN ' . _DB_PREFIX_ . 'lang_shop lang_shop ON (lang_shop.id_lang = l.id_lang AND lang_shop.id_shop = ' . (int) $id_shop . ')
				WHERE l.`active` = 1
			');
        }

        return static::$countActiveLanguages[$id_shop];
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

        $lang_pack = static::getLangDetails($iso);
        if (!$lang_pack) {
            $errors[] = Context::getContext()->getTranslator()->trans('Sorry this language is not available', [], 'Admin.International.Notification');

            return false;
        }

        return static::downloadXLFLanguagePack($lang_pack['locale'], $errors, self::PACK_TYPE_SYMFONY);
    }

    /**
     * Downloads a language pack into local cache
     *
     * @param string $locale IETF language tag
     * @param array $errors
     * @param string $type self:PACK_TYPE_SYMFONY|self::PACK_TYPE_EMAILS
     *
     * @return bool
     */
    public static function downloadXLFLanguagePack($locale, &$errors = [], $type = self::PACK_TYPE_SYMFONY)
    {
        $file = self::getPathToCachedTranslationPack($locale, $type);
        $url = (self::PACK_TYPE_EMAILS === $type) ? self::EMAILS_LANGUAGE_PACK_URL : self::SF_LANGUAGE_PACK_URL;
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

            return false;
        }

        // 3 attempts to download the language pack
        for ($i = 1; $i <= 3; ++$i) {
            $content = Tools::file_get_contents($url, false, null, static::PACK_DOWNLOAD_TIMEOUT);

            // If we managed to download the pack successfully and it's a valid zip file, we stop
            if (!empty($content) && strpos($content, "\x50\x4b\x03\x04") !== false) {
                break;
            }

            // If not, we will give it another try, unless we are on our last attempt
            if ($i == 3) {
                $errors[] = Context::getContext()->getTranslator()->trans('Language pack unavailable.', [], 'Admin.International.Notification') . ' ' . $url;

                return false;
            }
        }

        return false !== file_put_contents($file, $content);
    }

    /**
     * Extracts a local translation pack
     *
     * @param string $locale IETF language tag
     * @param array $errors
     *
     * @return bool
     */
    public static function installSfLanguagePack($locale, &$errors = [])
    {
        if (!static::translationPackIsInCache($locale)) {
            // @todo Throw exception
            $errors[] = Context::getContext()->getTranslator()->trans('Language pack unavailable.', [], 'Admin.International.Notification');

            return false;
        }

        $zipArchive = new ZipArchive();
        $zipArchive->open(self::getPathToCachedTranslationPack($locale));
        $zipArchive->extractTo(self::SF_TRANSLATIONS_DIR);
        $zipArchive->close();

        return true;
    }

    /**
     * @param array $langPack
     * @param array $errors
     * @param bool $overwriteTemplates
     *
     * @return bool
     */
    private static function generateEmailsLanguagePack($langPack, &$errors = [], $overwriteTemplates = false): bool
    {
        $locale = $langPack['locale'];
        $sfContainer = SymfonyContainer::getInstance();
        if (null === $sfContainer) {
            $errors[] = Context::getContext()->getTranslator()->trans(
                'Cannot generate emails because the Symfony container is unavailable.',
                [],
                'Admin.Notifications.Error'
            );

            return false;
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

        return true;
    }

    /**
     * Installs a language pack and updates language sensitive information
     *
     * @param string $iso Language ISO code
     * @param array $params Optional parameters for self::checkAndAddLanguage
     * @param array $errors
     *
     * @return array|true Array of errors, or true if all goes well
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function installLanguagePack($iso, $params, &$errors = [])
    {
        // Clear smarty modules cache
        Tools::clearCache();

        $lang_pack = static::getLangDetails($iso);

        if (!Language::checkAndAddLanguage((string) $iso, $lang_pack, false, $params)) {
            $errors[] = Context::getContext()->getTranslator()->trans('An error occurred while creating the language: %s', [(string) $iso], 'Admin.International.Notification');

            return $errors;
        }

        $langId = static::getIdByIso($iso, true);

        // extract language pack
        if (!static::installSfLanguagePack(static::getLocaleByIso($iso), $errors)) {
            return $errors;
        }

        // update multi language tables (*_lang tables in DB)
        static::updateMultilangTable($iso);

        // update localized information in currencies
        self::updateCurrenciesCldr(new static($langId));

        // generate mail templates in the installed language
        self::generateEmailsLanguagePack($lang_pack, $errors, true);

        return true;
    }

    /**
     * Installs the first language pack (during shop install)
     *
     * @param string $iso Language ISO code
     * @param array $params Optional parameters for self::checkAndAddLanguage
     * @param array $errors
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function installFirstLanguagePack(string $iso, array $params = [], array &$errors = []): bool
    {
        $lang_pack = static::getLangDetails($iso);

        if (!Language::checkAndAddLanguage((string) $iso, $lang_pack, false, $params)) {
            $errors[] = Context::getContext()->getTranslator()->trans('An error occurred while creating the language: %s', [(string) $iso], 'Admin.International.Notification');

            return false;
        }

        // extract language pack
        if (!static::installSfLanguagePack(static::getLocaleByIso($iso), $errors)) {
            return false;
        }

        // generate mail templates in the installed language
        self::generateEmailsLanguagePack($lang_pack, $errors, true);

        return true;
    }

    private static function updateCurrenciesCldr(self $language): void
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
        $lang_pack = static::getLangDetails($iso);
        if (!empty($lang_pack['locale'])) {
            //Update locale field if empty (manually created, or imported without it)
            $language = new Language(Language::getIdByIso($iso));
            if ($language->id && empty($language->locale)) {
                $language->locale = $lang_pack['locale'];
                $language->save();
            }

            if (!static::installSfLanguagePack($lang_pack['locale'], $errors)) {
                return false;
            }

            Language::updateMultilangTable($iso);
            self::generateEmailsLanguagePack($lang_pack, $errors, false);
        }

        return true;
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
        $langId = static::getIdByIso($iso_code);

        if (!empty($langId)) {
            $lang = new static($langId);
            /** @var Language $lang */
            $rows = Db::getInstance()->executeS('SHOW TABLES LIKE \'' . str_replace('_', '\\_', _DB_PREFIX_) . '%\_lang\' ');
            if (!empty($rows)) {
                // get all values
                $tableNames = [];
                foreach ($rows as $row) {
                    $tableNames[] = reset($row);
                }
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
        $translator = SymfonyContainer::getInstance()->get(TranslatorInterface::class);

        foreach ($tablesToUpdate as $tableName) {
            $className = (new DataLangFactory(_DB_PREFIX_, $translator))
                ->getClassNameFromTable($tableName);

            if (_DB_PREFIX_ . 'country_lang' === $tableName) {
                static::updateMultilangFromCldr($language);
            } else {
                static::updateMultilangFromClass($tableName, $className, $language);
            }
        }

        Hook::exec('actionUpdateLangAfter', ['lang' => $language]);
    }

    /**
     * @param Language $lang
     *
     * @throws PrestaShopDatabaseException
     */
    public static function updateMultilangFromCldr($lang)
    {
        // Fetch all countries from DB in specified locale
        $sql = 'SELECT c.`iso_code`, cl.* FROM `' . _DB_PREFIX_ . 'country` c
                INNER JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON c.`id_country` = cl.`id_country`
                WHERE cl.`id_lang` = "' . (int) $lang->id . '" ';
        $translatableCountries = Db::getInstance()->executeS($sql, true, false);

        if (empty($translatableCountries)) {
            return;
        }

        // Fetch all countries from Intl in specified locale
        $langCountries = (new self())->getCountries($lang->getLocale());
        foreach ($translatableCountries as $country) {
            $isoCode = strtolower($country['iso_code']);
            if (empty($langCountries[$isoCode])) {
                continue;
            }
            // Translate the country name
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'country_lang`
                    SET `name` = "' . pSQL($langCountries[$isoCode]) . '"
                    WHERE `id_country` = "' . (int) $country['id_country'] . '"
                    AND `id_lang` = "' . (int) $lang->id . '" LIMIT 1;';
            Db::getInstance()->execute($sql);
        }
    }

    /**
     * Updates multilang tables using DataLang classes
     *
     * @param string $table
     * @param string $className
     * @param LanguageCore $lang
     *
     * @throws PrestaShopDatabaseException
     */
    public static function updateMultilangFromClass($table, $className, $lang)
    {
        $translator = SymfonyContainer::getInstance()->get(TranslatorInterface::class);

        try {
            $classObject = (new DataLangFactory(_DB_PREFIX_, $translator))
                ->buildFromClassName($className, $lang->getLocale());
        } catch (DataLangClassNameNotFoundException $e) {
            return;
        }

        $keys = $classObject->getKeys();
        $fieldsToUpdate = $classObject->getFieldsToUpdate();

        if (!empty($keys) && !empty($fieldsToUpdate)) {
            $shops = Shop::getShopsCollection(false);
            /** @var array<Shop> $shops */
            foreach ($shops as $shop) {
                self::updateMultilangFromClassForShop($classObject, $lang, $shop);
            }
        }
    }

    /**
     * untranslate then re-translate duplicated rows in tables with pattern xxx_lang.
     *
     * @param DataLangCore $classObject
     * @param LanguageCore $lang
     * @param Shop $shop
     *
     * @throws \PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private static function updateMultilangFromClassForShop(DataLangCore $classObject, self $lang, Shop $shop)
    {
        $shopDefaultLangId = (int) Configuration::get('PS_LANG_DEFAULT', null, $shop->id_shop_group, $shop->id);
        $shopDefaultLanguage = new Language($shopDefaultLangId);

        $sfContainer = SymfonyContainer::getInstance();
        $translator = $sfContainer->get(TranslatorInterface::class);
        if (!$translator->isLanguageLoaded($shopDefaultLanguage->locale)) {
            $sfContainer->get('prestashop.translation.translator_language_loader')
                ->setIsAdminContext(true)
                ->loadLanguage($translator, $shopDefaultLanguage->locale);
        }

        (new EntityTranslatorFactory($translator))
            ->build($classObject)
            ->translate($lang->id, $shop->id);
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

        return new RtlStylesheetProcessor(
            $adminDir,
            $themesDir,
            []
        );
    }

    /**
     * Indicates if a given translation pack exists in cache
     *
     * @param string $type IETF language tag
     * @param string $locale self::PACK_TYPE_SYMFONY|self::PACK_TYPE_EMAILS
     *
     * @return bool
     */
    public static function translationPackIsInCache(string $locale, string $type = self::PACK_TYPE_SYMFONY): bool
    {
        return file_exists(self::getPathToCachedTranslationPack($locale, $type));
    }

    /**
     * Returns the path to the local translation pack file
     *
     * @param string $locale IETF language tag
     * @param string $type self::PACK_TYPE_SYMFONY|self::PACK_TYPE_EMAILS
     *
     * @return string Local path
     */
    private static function getPathToCachedTranslationPack(string $locale, string $type = self::PACK_TYPE_SYMFONY): string
    {
        return self::TRANSLATION_PACK_CACHE_DIR . $type . '-' . $locale . '.zip';
    }

    /**
     * @return string return the language locale, or its code by default
     */
    public function getLocale(): string
    {
        return !empty($this->locale) ?
            $this->locale :
            $this->language_code;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsoCode(): string
    {
        return $this->iso_code;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageCode(): string
    {
        return $this->language_code;
    }

    /**
     * {@inheritdoc}
     */
    public function isRTL(): bool
    {
        return $this->is_rtl;
    }

    /**
     * {@inheritdoc}
     */
    public function getDateFormat(): string
    {
        return $this->date_format_lite;
    }

    /**
     * {@inheritdoc}
     */
    public function getDateTimeFormat(): string
    {
        return $this->date_format_full;
    }

    /**
     * @param string $locale
     *
     * @return array<string, string>
     */
    private function getCountries(string $locale): array
    {
        Locale::setDefault($locale);
        $countries = Countries::getNames();
        $countries = array_change_key_case($countries, CASE_LOWER);

        return $countries;
    }
}
