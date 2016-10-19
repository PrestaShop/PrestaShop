<?php
/**
 * 2007-2016 PrestaShop.
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;

/**
 * Class LanguageCore
 */
class LanguageCore extends ObjectModel
{
    const ALL_LANGUAGES_FILE = '/app/Resources/all_languages.json';
    const SF_LANGUAGE_PACK_URL = 'http://translate.prestashop.com/TEMP/TEMP/TEMP/TEMP/TEMP/%s.zip';
    const EMAILS_LANGUAGE_PACK_URL = 'http://translate.prestashop.com/TEMP/TEMP/TEMP/TEMP/emails/%s.zip';

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

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'lang',
        'primary' => 'id_lang',
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
            'iso_code' => array('type' => self::TYPE_STRING, 'validate' => 'isLanguageIsoCode', 'required' => true, 'size' => 2),
            'locale' => array('type' => self::TYPE_STRING, 'validate' => 'isLocale', 'size' => 5),
            'language_code' => array('type' => self::TYPE_STRING, 'validate' => 'isLanguageCode', 'size' => 5),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_rtl' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_format_lite' => array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat', 'required' => true, 'size' => 32),
            'date_format_full' => array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat', 'required' => true, 'size' => 32),
        ),
    );

    /** @var array Languages cache */
    protected static $_checkedLangs;
    protected static $_LANGUAGES;
    protected static $countActiveLanguages = array();

    protected $webserviceParameters = array(
        'objectNodeName' => 'language',
        'objectsNodeName' => 'languages',
    );

    protected $translationsFilesAndVars = array(
            'fields' => '_FIELDS',
            'errors' => '_ERRORS',
            'admin' => '_LANGADM',
            'pdf' => '_LANGPDF',
            'tabs' => 'tabs',
        );

    /**
     * LanguageCore constructor.
     *
     * @param int|null $id
     * @param int|null $idLang
     */
    public function __construct($id = null, $idLang = null)
    {
        parent::__construct($id);
    }

    /**
     * @see ObjectModel::getFields()
     *
     * @return array
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
     * @deprecated 1.7.0
     */
    protected function _generateFiles($newIso = null)
    {
        return $this->generateFiles($newIso);
    }

    /**
     * Generate translations files
     *
     * @since 1.7.0
     */
    protected function generateFiles($newIso = null)
    {
        $isoCode = $newIso ? $newIso : $this->iso_code;

        if (!file_exists(_PS_TRANSLATIONS_DIR_.$isoCode)) {
            if (@mkdir(_PS_TRANSLATIONS_DIR_.$isoCode)) {
                @chmod(_PS_TRANSLATIONS_DIR_.$isoCode, 0777);
            }
        }

        foreach ($this->translationsFilesAndVars as $file => $var) {
            $pathFile = _PS_TRANSLATIONS_DIR_.$isoCode.'/'.$file.'.php';
            if (!file_exists($pathFile)) {
                if ($file != 'tabs') {
                    @file_put_contents($pathFile, '<?php
	global $'.$var.';
	$'.$var.' = array();
?>');
                } else {
                    @file_put_contents($pathFile, '<?php
	$'.$var.' = array();
	return $'.$var.';
?>');
                }
            }

            @chmod($pathFile, 0777);
        }
    }

    /**
     * Move translations files after editing language iso code.
     */
    public function moveToIso($newIso)
    {
        if ($newIso == $this->iso_code) {
            return true;
        }

        if (file_exists(_PS_TRANSLATIONS_DIR_.$this->iso_code)) {
            rename(_PS_TRANSLATIONS_DIR_.$this->iso_code, _PS_TRANSLATIONS_DIR_.$newIso);
        }

        if (file_exists(_PS_MAIL_DIR_.$this->iso_code)) {
            rename(_PS_MAIL_DIR_.$this->iso_code, _PS_MAIL_DIR_.$newIso);
        }

        $modulesList = Module::getModulesDirOnDisk();
        foreach ($modulesList as $moduleDir) {
            if (file_exists(_PS_MODULE_DIR_.$moduleDir.'/mails/'.$this->iso_code)) {
                rename(_PS_MODULE_DIR_.$moduleDir.'/mails/'.$this->iso_code, _PS_MODULE_DIR_.$moduleDir.'/mails/'.$newIso);
            }

            if (file_exists(_PS_MODULE_DIR_.$moduleDir.'/'.$this->iso_code.'.php')) {
                rename(_PS_MODULE_DIR_.$moduleDir.'/'.$this->iso_code.'.php', _PS_MODULE_DIR_.$moduleDir.'/'.$newIso.'.php');
            }
        }

        $themes = (new ThemeManagerBuilder($this->context, Db::getInstance()))
                        ->buildRepository()
                        ->getList();
        foreach ($themes as $theme) {
            /* @var Theme $theme */
            $themeDir = $theme->getDirectory();
            if (file_exists(_PS_ALL_THEMES_DIR_.$themeDir.'/lang/'.$this->iso_code.'.php')) {
                rename(_PS_ALL_THEMES_DIR_.$themeDir.'/lang/'.$this->iso_code.'.php', _PS_ALL_THEMES_DIR_.$themeDir.'/lang/'.$newIso.'.php');
            }

            if (file_exists(_PS_ALL_THEMES_DIR_.$themeDir.'/mails/'.$this->iso_code)) {
                rename(_PS_ALL_THEMES_DIR_.$themeDir.'/mails/'.$this->iso_code, _PS_ALL_THEMES_DIR_.$themeDir.'/mails/'.$newIso);
            }

            foreach ($modulesList as $module) {
                if (file_exists(_PS_ALL_THEMES_DIR_.$themeDir.'/modules/'.$module.'/'.$this->iso_code.'.php')) {
                    rename(_PS_ALL_THEMES_DIR_.$themeDir.'/modules/'.$module.'/'.$this->iso_code.'.php', _PS_ALL_THEMES_DIR_.$themeDir.'/modules/'.$module.'/'.$newIso.'.php');
                }
            }
        }
    }

    /**
     * Adds current Language as a new Object to the database
     *
     * @param bool $autoDate   Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @param bool $onlyAdd Only add
     *                      Do not generate files for this Language
     *
     * @return bool Indicates whether the Language has been successfully added
     */
    public function add($autoDate = true, $nullValues = false, $onlyAdd = false)
    {
        if (!parent::add($autoDate, $nullValues)) {
            return false;
        }

        if ($onlyAdd) {
            return true;
        }

        // create empty files if they not exists
        $this->generateFiles();

        // @todo Since a lot of modules are not in right format with their primary keys name, just get true ...
        $this->loadUpdateSQL();

        return true;
    }

    /**
     * Cehk language files
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
     * @param mixed $isoCode
     *
     *@return `true` if all files exists
     */
    public static function checkFilesWithIsoCode($isoCode)
    {
        if (isset(self::$_checkedLangs[$isoCode]) && self::$_checkedLangs[$isoCode]) {
            return true;
        }

        foreach (array_keys(Language::getFilesList($isoCode, _THEME_NAME_, false, false, false, true)) as $key) {
            if (!file_exists($key)) {
                return false;
            }
        }
        self::$_checkedLangs[$isoCode] = true;

        return true;
    }

    /**
     * Get files list
     *
     * @param string $isoFrom
     * @param string $themeFrom
     * @param bool   $isoTo
     * @param bool   $themeTo
     * @param bool   $select
     * @param bool   $check
     * @param bool   $modules
     *
     * @return array
     */
    public static function getFilesList($isoFrom, $themeFrom, $isoTo = false, $themeTo = false, $select = false, $check = false, $modules = false)
    {
        if (empty($isoFrom)) {
            die(Tools::displayError());
        }

        $copy = ($isoTo && $themeTo) ? true : false;

        $lPathFrom = _PS_TRANSLATIONS_DIR_.(string) $isoFrom.'/';
        $tPathFrom = _PS_ROOT_DIR_.'/themes/'.(string) $themeFrom.'/';
        $pPathFrom = _PS_ROOT_DIR_.'/themes/'.(string) $themeFrom.'/pdf/';
        $mPathFrom = _PS_MAIL_DIR_.(string) $isoFrom.'/';

        if ($copy) {
            $lPathTo = _PS_TRANSLATIONS_DIR_.(string) $isoTo.'/';
            $tPathTo = _PS_ROOT_DIR_.'/themes/'.(string) $themeTo.'/';
            $pPathTo = _PS_ROOT_DIR_.'/themes/'.(string) $themeTo.'/pdf/';
            $mPathTo = _PS_MAIL_DIR_.(string) $isoTo.'/';
        }

        $lFiles = array('admin.php', 'errors.php', 'fields.php', 'pdf.php', 'tabs.php');

        // Added natives mails files
        $mFiles = array(
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
            'in_transit.html', 'in_transit.txt',
            'log_alert.html', 'log_alert.txt',
            'newsletter.html', 'newsletter.txt',
            'order_canceled.html', 'order_canceled.txt',
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
            'order_changed.html', 'order_changed.txt',
        );

        $number = -1;

        $files = array();
        $filesTr = array();
        $filesTheme = array();
        $filesMail = array();
        $filesModules = array();

        // When a copy is made from a theme in specific language
        // to an other theme for the same language,
        // it's avoid to copy Translations, Mails files
        // and modules files which are not override by theme.
        if (!$copy || $isoFrom != $isoTo) {
            // Translations files
            if (!$check || ($check && (string) $isoFrom != 'en')) {
                foreach ($lFiles as $file) {
                    $filesTr[$lPathFrom.$file] = ($copy ? $lPathTo.$file : ++$number);
                }
            }
            if ($select == 'tr') {
                return $filesTr;
            }
            $files = array_merge($files, $filesTr);

            // Mail files
            if (!$check || ($check && (string) $isoFrom != 'en')) {
                $filesMail[$mPathFrom.'lang.php'] = ($copy ? $mPathTo.'lang.php' : ++$number);
            }
            foreach ($mFiles as $file) {
                $filesMail[$mPathFrom.$file] = ($copy ? $mPathTo.$file : ++$number);
            }
            if ($select == 'mail') {
                return $filesMail;
            }
            $files = array_merge($files, $filesMail);

            // Modules
            if ($modules) {
                $modList = Module::getModulesDirOnDisk();
                foreach ($modList as $mod) {
                    $modDir = _PS_MODULE_DIR_.$mod;
                    // Lang file
                    if (file_exists($modDir.'/translations/'.(string) $isoFrom.'.php')) {
                        $filesModules[$modDir.'/translations/'.(string) $isoFrom.'.php'] = ($copy ? $modDir.'/translations/'.(string) $isoTo.'.php' : ++$number);
                    } elseif (file_exists($modDir.'/'.(string) $isoFrom.'.php')) {
                        $filesModules[$modDir.'/'.(string) $isoFrom.'.php'] = ($copy ? $modDir.'/'.(string) $isoTo.'.php' : ++$number);
                    }
                    // Mails files
                    $modMailDirFrom = $modDir.'/mails/'.(string) $isoFrom;
                    $modMailDirTo = $modDir.'/mails/'.(string) $isoTo;
                    if (file_exists($modMailDirFrom)) {
                        $dirFiles = scandir($modMailDirFrom);
                        foreach ($dirFiles as $file) {
                            if (file_exists($modMailDirFrom.'/'.$file) && $file != '.' && $file != '..' && $file != '.svn') {
                                $filesModules[$modMailDirFrom.'/'.$file] = ($copy ? $modMailDirTo.'/'.$file : ++$number);
                            }
                        }
                    }
                }
                if ($select == 'modules') {
                    return $filesModules;
                }
                $files = array_merge($files, $filesModules);
            }
        } elseif ($select == 'mail' || $select == 'tr') {
            return $files;
        }

        // Theme files
        if (!$check || ($check && (string) $isoFrom != 'en')) {
            $filesTheme[$tPathFrom.'lang/'.(string) $isoFrom.'.php'] = ($copy ? $tPathTo.'lang/'.(string) $isoTo.'.php' : ++$number);

            // Override for pdf files in the theme
            if (file_exists($pPathFrom.'lang/'.(string) $isoFrom.'.php')) {
                $filesTheme[$pPathFrom.'lang/'.(string) $isoFrom.'.php'] = ($copy ? $pPathTo.'lang/'.(string) $isoTo.'.php' : ++$number);
            }

            $moduleThemeFiles = (file_exists($tPathFrom.'modules/') ? scandir($tPathFrom.'modules/') : array());
            foreach ($moduleThemeFiles as $module) {
                if ($module !== '.' && $module != '..' && $module !== '.svn' && file_exists($tPathFrom.'modules/'.$module.'/translations/'.(string) $isoFrom.'.php')) {
                    $filesTheme[$tPathFrom.'modules/'.$module.'/translations/'.(string) $isoFrom.'.php'] = ($copy ? $tPathTo.'modules/'.$module.'/translations/'.(string) $isoTo.'.php' : ++$number);
                }
            }
        }
        if ($select == 'theme') {
            return $filesTheme;
        }
        $files = array_merge($files, $filesTheme);

        // Return
        return $files;
    }

    /**
     * loadUpdateSQL will create default lang values when you create a new lang, based on default id lang.
     *
     * @return bool true if succeed
     */
    public function loadUpdateSQL()
    {
        $tables = Db::getInstance()->executeS('SHOW TABLES LIKE \''.str_replace('_', '\\_', _DB_PREFIX_).'%\_lang\' ');
        $langTables = array();

        foreach ($tables as $table) {
            foreach ($table as $t) {
                if ($t != _DB_PREFIX_.'configuration_lang') {
                    $langTables[] = $t;
                }
            }
        }

        $return = true;

        $shops = Shop::getShopsCollection(false);
        foreach ($shops as $shop) {
            /* @var Shop $shop */
            $idLangDefault = Configuration::get('PS_LANG_DEFAULT', null, $shop->id_shop_group, $shop->id);

            foreach ($langTables as $name) {
                preg_match('#^'.preg_quote(_DB_PREFIX_).'(.+)_lang$#i', $name, $m);
                $identifier = 'id_'.$m[1];

                $fields = '';
                // We will check if the table contains a column "id_shop"
                // If yes, we will add "id_shop" as a WHERE condition in queries copying data from default language
                $shopFieldExists = $primaryKeyExists = false;
                $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `'.$name.'`');
                foreach ($columns as $column) {
                    $fields .= '`'.$column['Field'].'`, ';
                    if ($column['Field'] == 'id_shop') {
                        $shopFieldExists = true;
                    }
                    if ($column['Field'] == $identifier) {
                        $primaryKeyExists = true;
                    }
                }
                $fields = rtrim($fields, ', ');

                if (!$primaryKeyExists) {
                    continue;
                }

                $sql = 'INSERT IGNORE INTO `'.$name.'` ('.$fields.') (SELECT ';

                // For each column, copy data from default language
                reset($columns);
                foreach ($columns as $column) {
                    if ($identifier != $column['Field'] && $column['Field'] != 'id_lang') {
                        $sql .= '(
							SELECT `'.bqSQL($column['Field']).'`
							FROM `'.bqSQL($name).'` tl
							WHERE tl.`id_lang` = '.(int) $idLangDefault.'
							'.($shopFieldExists ? ' AND tl.`id_shop` = '.(int) $shop->id : '').'
							AND tl.`'.bqSQL($identifier).'` = `'.bqSQL(str_replace('_lang', '', $name)).'`.`'.bqSQL($identifier).'`
						),';
                    } else {
                        $sql .= '`'.bqSQL($column['Field']).'`,';
                    }
                }
                $sql = rtrim($sql, ', ');
                $sql .= ' FROM `'._DB_PREFIX_.'lang` CROSS JOIN `'.bqSQL(str_replace('_lang', '', $name)).'`)';
                $return &= Db::getInstance()->execute($sql);
            }
        }

        return $return;
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
     * Deletes current Language from the database
     *
     * @return bool `true` if delete was successful
     * @throws PrestaShopException
     */
    public function delete()
    {
        if (!$this->hasMultishopEntries() || Shop::getContext() == Shop::CONTEXT_ALL) {
            if (empty($this->iso_code)) {
                $this->iso_code = Language::getIsoById($this->id);
            }

            // Database translations deletion
            $result = Db::getInstance()->executeS('SHOW TABLES FROM `'._DB_NAME_.'`');
            foreach ($result as $row) {
                if (isset($row['Tables_in_'._DB_NAME_]) && !empty($row['Tables_in_'._DB_NAME_]) && preg_match('/'.preg_quote(_DB_PREFIX_).'_lang/', $row['Tables_in_'._DB_NAME_])) {
                    if (!Db::getInstance()->execute('DELETE FROM `'.$row['Tables_in_'._DB_NAME_].'` WHERE `id_lang` = '.(int) $this->id)) {
                        return false;
                    }
                }
            }

            // Delete tags
            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'tag WHERE id_lang = '.(int) $this->id);

            // Delete search words
            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'search_word WHERE id_lang = '.(int) $this->id);

            // Files deletion
            foreach (Language::getFilesList($this->iso_code, _THEME_NAME_, false, false, false, true, true) as $key => $file) {
                if (file_exists($key)) {
                    unlink($key);
                }
            }

            $modList = scandir(_PS_MODULE_DIR_);
            foreach ($modList as $mod) {
                Tools::deleteDirectory(_PS_MODULE_DIR_.$mod.'/mails/'.$this->iso_code);
                $files = @scandir(_PS_MODULE_DIR_.$mod.'/mails/');
                if (count($files) <= 2) {
                    Tools::deleteDirectory(_PS_MODULE_DIR_.$mod.'/mails/');
                }

                if (file_exists(_PS_MODULE_DIR_.$mod.'/'.$this->iso_code.'.php')) {
                    unlink(_PS_MODULE_DIR_.$mod.'/'.$this->iso_code.'.php');
                    $files = @scandir(_PS_MODULE_DIR_.$mod);
                    if (count($files) <= 2) {
                        Tools::deleteDirectory(_PS_MODULE_DIR_.$mod);
                    }
                }
            }

            if (file_exists(_PS_MAIL_DIR_.$this->iso_code)) {
                Tools::deleteDirectory(_PS_MAIL_DIR_.$this->iso_code);
            }
            if (file_exists(_PS_TRANSLATIONS_DIR_.$this->iso_code)) {
                Tools::deleteDirectory(_PS_TRANSLATIONS_DIR_.$this->iso_code);
            }

            $images = array(
                '.jpg',
                '-default-'.ImageType::getFormattedName('thickbox').'.jpg',
                '-default-'.ImageType::getFormattedName('home').'.jpg',
                '-default-'.ImageType::getFormattedName('large').'.jpg',
                '-default-'.ImageType::getFormattedName('medium').'.jpg',
                '-default-'.ImageType::getFormattedName('small').'.jpg',
            );
            $imagesDirectories = array(_PS_CAT_IMG_DIR_, _PS_MANU_IMG_DIR_, _PS_PROD_IMG_DIR_, _PS_SUPP_IMG_DIR_);
            foreach ($imagesDirectories as $imageDirectory) {
                foreach ($images as $image) {
                    if (file_exists($imageDirectory.$this->iso_code.$image)) {
                        unlink($imageDirectory.$this->iso_code.$image);
                    }
                    if (file_exists(_PS_ROOT_DIR_.'/img/l/'.$this->id.'.jpg')) {
                        unlink(_PS_ROOT_DIR_.'/img/l/'.$this->id.'.jpg');
                    }
                }
            }
        }

        if (!parent::delete()) {
            return false;
        }

        return true;
    }

    /**
     * Delete selected languages
     *
     * @param array $selection
     *
     * @return bool
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
     * Returns available languages.
     *
     * @param bool     $active  Select only active languages
     * @param int|bool $idShop  Shop ID
     * @param bool     $idsOnly If true, returns an array of language IDs
     *
     * @return array Languages
     */
    public static function getLanguages($active = true, $idShop = false, $idsOnly = false)
    {
        if (!self::$_LANGUAGES) {
            Language::loadLanguages();
        }

        $languages = array();
        foreach (self::$_LANGUAGES as $language) {
            if ($active && !$language['active'] || ($idShop && !isset($language['shops'][(int) $idShop]))) {
                continue;
            }

            $languages[] = $idsOnly ? $language['id_lang'] : $language;
        }

        return $languages;
    }

    /**
     * Returns an array of language IDs.
     *
     * @param bool     $active Select only active languages
     * @param int|bool $idShop Shop ID
     *
     * @return array
     */
    public static function getIDs($active = true, $idShop = false)
    {
        return self::getLanguages($active, $idShop, true);
    }

    /**
     * Get Language instance
     *
     * @param int $idLang Language ID
     *
     * @return bool|Language|LanguageCore
     */
    public static function getLanguage($idLang)
    {
        if (!self::$_LANGUAGES) {
            Language::loadLanguages();
        }
        if (!array_key_exists((int) $idLang, self::$_LANGUAGES)) {
            return false;
        }

        return self::$_LANGUAGES[(int) ($idLang)];
    }

    /**
     * Return iso code from id.
     *
     * @param int $idLang Language ID
     *
     * @return string Iso code
     */
    public static function getIsoById($idLang)
    {
        if (!self::$_LANGUAGES) {
            Language::loadLanguages();
        }
        if (isset(self::$_LANGUAGES[(int) $idLang]['iso_code'])) {
            return self::$_LANGUAGES[(int) $idLang]['iso_code'];
        }

        return false;
    }

    /**
     * Return id from iso code.
     *
     * @param string $isoCode Iso code
     * @param bool   $noCache
     *
     * @return false|null|string
     */
    public static function getIdByIso($isoCode, $noCache = false)
    {
        if (!Validate::isLanguageIsoCode($isoCode)) {
            die(Tools::displayError('Fatal error: ISO code is not correct').' '.Tools::safeOutput($isoCode));
        }

        $key = 'Language::getIdByIso_'.$isoCode;
        if ($noCache || !Cache::isStored($key)) {
            $idLang = Db::getInstance()->getValue('SELECT `id_lang` FROM `'._DB_PREFIX_.'lang` WHERE `iso_code` = \''.pSQL(strtolower($isoCode)).'\'');

            Cache::store($key, $idLang);

            return $idLang;
        }

        return Cache::retrieve($key);
    }

    /**
     * Get Language details
     *
     * @param string $iso
     *
     * @return bool
     * @throws Exception
     */
    public static function getLangDetails($iso)
    {
        $iso = (string) $iso; // $iso often comes from xml and is a SimpleXMLElement

        $allLanguages = file_get_contents(_PS_ROOT_DIR_.self::ALL_LANGUAGES_FILE);
        $allLanguages = json_decode($allLanguages, true);

        $jsonLastErrorCode = json_last_error();
        if (JSON_ERROR_NONE !== $jsonLastErrorCode) {
            throw new \Exception('The legacy to standard locales JSON could not be decoded', $jsonLastErrorCode);
        }

        return $allLanguages[$iso] ?: false;
    }

    /**
     * @param string $isoCode
     *
     * @return string|false|null
     *
     * @throws Exception
     */
    public static function getLocaleByIso($isoCode)
    {
        if (!Validate::isLanguageIsoCode($isoCode)) {
            throw new Exception(sprintf('The ISO code %s is invalid'));
        }

        if ($details = self::getLangDetails($isoCode)) {
            return $details['locale'];
        } else {
            return false;
        }
    }

    /**
     * Get Language IETF code by Language ISO code
     *
     * @param string $isoCode
     *
     * @return false|null|string
     */
    public static function getLanguageCodeByIso($isoCode)
    {
        if (!Validate::isLanguageIsoCode($isoCode)) {
            die(Tools::displayError('Fatal error: ISO code is not correct').' '.Tools::safeOutput($isoCode));
        }

        return Db::getInstance()->getValue('SELECT `language_code` FROM `'._DB_PREFIX_.'lang` WHERE `iso_code` = \''.pSQL(strtolower($isoCode)).'\'');
    }

    /**
     * Get Language instance by IETF code
     *
     * @param string $code IETF code
     *
     * @return bool|Language
     */
    public static function getLanguageByIETFCode($code)
    {
        if (!Validate::isLanguageCode($code)) {
            die(sprintf(Tools::displayError('Fatal error: IETF code %s is not correct'), Tools::safeOutput($code)));
        }

        // $code is in the form of 'xx-YY' where xx is the language code
        // and 'YY' a country code identifying a variant of the language.
        $langCountry = explode('-', $code);
        // Get the language component of the code
        $lang = $langCountry[0];

        // Find the id_lang of the language.
        // We look for anything with the correct language code
        // and sort on equality with the exact IETF code wanted.
        // That way using only one query we get either the exact wanted language
        // or a close match.
        $idLang = Db::getInstance()->getValue(
            'SELECT `id_lang`, IF(language_code = \''.pSQL($code).'\', 0, LENGTH(language_code)) as found
			FROM `'._DB_PREFIX_.'lang`
			WHERE LEFT(`language_code`,2) = \''.pSQL($lang).'\'
			ORDER BY found ASC'
        );

        // Instantiate the Language object if we found it.
        if ($idLang) {
            return new Language($idLang);
        } else {
            return false;
        }
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
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_lang`, `iso_code` FROM `'._DB_PREFIX_.'lang` '.($active ? 'WHERE active = 1' : ''));
    }

    public static function copyLanguageData($from, $to)
    {
        $result = Db::getInstance()->executeS('SHOW TABLES FROM `'._DB_NAME_.'`');
        foreach ($result as $row) {
            if (preg_match('/_lang/', $row['Tables_in_'._DB_NAME_]) && $row['Tables_in_'._DB_NAME_] != _DB_PREFIX_.'lang') {
                $result2 = Db::getInstance()->executeS('SELECT * FROM `'.$row['Tables_in_'._DB_NAME_].'` WHERE `id_lang` = '.(int) $from);
                if (!count($result2)) {
                    continue;
                }
                Db::getInstance()->execute('DELETE FROM `'.$row['Tables_in_'._DB_NAME_].'` WHERE `id_lang` = '.(int) $to);
                $query = 'INSERT INTO `'.$row['Tables_in_'._DB_NAME_].'` VALUES ';
                foreach ($result2 as $row2) {
                    $query .= '(';
                    $row2['id_lang'] = $to;
                    foreach ($row2 as $field) {
                        $query .= (!is_string($field) && $field == null) ? 'NULL,' : '\''.pSQL($field, true).'\',';
                    }
                    $query = rtrim($query, ',').'),';
                }
                $query = rtrim($query, ',');
                Db::getInstance()->execute($query);
            }
        }

        return true;
    }

    /**
     * Load all languages in memory for caching.
     */
    public static function loadLanguages()
    {
        self::$_LANGUAGES = array();

        $sql = 'SELECT l.*, ls.`id_shop`
				FROM `'._DB_PREFIX_.'lang` l
				LEFT JOIN `'._DB_PREFIX_.'lang_shop` ls ON (l.id_lang = ls.id_lang)';

        $result = Db::getInstance()->executeS($sql);
        foreach ($result as $row) {
            if (!isset(self::$_LANGUAGES[(int) $row['id_lang']])) {
                self::$_LANGUAGES[(int) $row['id_lang']] = $row;
            }
            self::$_LANGUAGES[(int) $row['id_lang']]['shops'][(int) $row['id_shop']] = true;
        }
    }

    /**
     * Check and add Language
     *
     * @param string $isoCode
     * @param array  $langPack
     * @param bool   $onlyAdd
     * @param null   $paramsLang
     *
     * @return bool
     */
    public static function checkAndAddLanguage($isoCode, $langPack = false, $onlyAdd = false, $paramsLang = null)
    {
        if (Language::getIdByIso($isoCode)) {
            return true;
        }

        // Initialize the language
        $lang = new Language();
        $lang->iso_code = Tools::strtolower($isoCode);
        $lang->language_code = $isoCode; // Rewritten afterwards if the language code is available
        $lang->active = true;

        // If the language pack has not been provided, retrieve it from prestashop.com
        if (!$langPack) {
            $langPack = self::getLangDetails($isoCode);
        }

        // If a language pack has been found or provided, prefill the language object with the value
        if ($langPack) {
            foreach ($langPack as $key => $value) {
                if ($key != 'iso_code' && isset(Language::$definition['fields'][$key])) {
                    $lang->$key = $value;
                }
            }
        }

        // Use the values given in parameters to override the data retrieved automatically
        if ($paramsLang !== null && is_array($paramsLang)) {
            foreach ($paramsLang as $key => $value) {
                if ($key != 'iso_code' && isset(Language::$definition['fields'][$key])) {
                    $lang->$key = $value;
                }
            }
        }

        if (!$lang->name && $lang->iso_code) {
            $lang->name = $lang->iso_code;
        }

        if (!$lang->validateFields() || !$lang->validateFieldsLang() || !$lang->add(true, false, $onlyAdd)) {
            return false;
        }

        if (isset($paramsLang['allow_accented_chars_url']) && in_array($paramsLang['allow_accented_chars_url'], array('1', 'true'))) {
            Configuration::updateGlobalValue('PS_ALLOW_ACCENTED_CHARS_URL', 1);
        }

        $flag = Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/flags/jpeg/'.$isoCode.'.jpg');
        if ($flag != null && !preg_match('/<body>/', $flag)) {
            $file = fopen(_PS_ROOT_DIR_.'/img/l/'.(int) $lang->id.'.jpg', 'w');
            if ($file) {
                fwrite($file, $flag);
                fclose($file);
            } else {
                Language::copyNoneFlag((int) $lang->id);
            }
        } else {
            Language::copyNoneFlag((int) $lang->id);
        }

        $filesCopy = array('/en.jpg');
        foreach (ImageType::getAll() as $alias => $config) {
            $filesCopy[] = '/en-default-'.ImageType::getFormattedName($alias).'.jpg';
        }

        foreach (array(_PS_CAT_IMG_DIR_, _PS_MANU_IMG_DIR_, _PS_PROD_IMG_DIR_, _PS_SUPP_IMG_DIR_) as $to) {
            foreach ($filesCopy as $file) {
                @copy(_PS_ROOT_DIR_.'/img/l'.$file, $to.str_replace('/en', '/'.$isoCode, $file));
            }
        }

        return true;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    protected static function copyNoneFlag($id)
    {
        return copy(_PS_ROOT_DIR_.'/img/l/none.jpg', _PS_ROOT_DIR_.'/img/l/'.$id.'.jpg');
    }

    /**
     * Is the Language installed?
     *
     * @param string $isoCode Language ISO code
     *
     * @return bool|mixed
     */
    public static function isInstalled($isoCode)
    {
        if (self::$_cache_language_installation === null) {
            self::$_cache_language_installation = array();
            $result = Db::getInstance()->executeS('SELECT `id_lang`, `iso_code` FROM `'._DB_PREFIX_.'lang`');
            foreach ($result as $row) {
                self::$_cache_language_installation[$row['iso_code']] = $row['id_lang'];
            }
        }

        return isset(self::$_cache_language_installation[$isoCode]) ? self::$_cache_language_installation[$isoCode] : false;
    }

    /**
     * Count active Languages
     *
     * @param int|null $idShop Shop ID
     *
     * @return mixed
     */
    public static function countActiveLanguages($idShop = null)
    {
        if (isset(Context::getContext()->shop) && is_object(Context::getContext()->shop) && $idShop === null) {
            $idShop = (int) Context::getContext()->shop->id;
        }

        if (!isset(self::$countActiveLanguages[$idShop])) {
            self::$countActiveLanguages[$idShop] = Db::getInstance()->getValue('
				SELECT COUNT(DISTINCT l.id_lang) FROM `'._DB_PREFIX_.'lang` l
				JOIN '._DB_PREFIX_.'lang_shop lang_shop ON (lang_shop.id_lang = l.id_lang AND lang_shop.id_shop = '.(int) $idShop.')
				WHERE l.`active` = 1
			');
        }

        return self::$countActiveLanguages[$idShop];
    }

    /**
     * Download and install Language pack
     *
     * @param string $iso
     * @param string $version
     * @param null   $params
     * @param bool   $install
     *
     * @return array|bool
     */
    public static function downloadAndInstallLanguagePack($iso, $version = _PS_VERSION_, $params = null, $install = true)
    {
        if (!Validate::isLanguageIsoCode((string) $iso)) {
            return false;
        }

        $errors = array();

        Language::downloadLanguagePack($iso, $version, $errors);

        if ($install) {
            Language::installLanguagePack($iso, $params, $errors);
        } else {
            $langPack = self::getLangDetails($iso);
            self::installSfLanguagePack($langPack['locale'], $errors);
            self::installEmailsLanguagePack($langPack, $errors);
        }

        return count($errors) ? $errors : true;
    }

    /**
     * Download Language pack
     *
     * @param string $iso     Language ISO code
     * @param string $version PrestaShop version
     * @param array  $errors
     *
     * @return bool
     */
    public static function downloadLanguagePack($iso, $version, &$errors = array())
    {
        $iso = (string) $iso; // $iso often comes from xml and is a SimpleXMLElement

        $langPack = self::getLangDetails($iso);
        if (!$langPack) {
            $errors[] = Tools::displayError('Sorry this language is not available');
        }

<<<<<<< 281f9f01aa96dd7f1dd007bf461b32823d0862ee
        self::downloadXLFLanguagePack($lang_pack['locale'], $errors, 'sf');
        self::downloadXLFLanguagePack($lang_pack['locale'], $errors, 'emails');
=======
        $content = Tools::file_get_contents(
            sprintf(self::LANGUAGE_GZIP_URL, $version, Tools::strtolower($langPack['iso_code']))
        );

        if (!@file_put_contents($file, $content)) {
            if (is_writable(dirname($file))) {
                @unlink($file);
                @file_put_contents($file, $content);
            } elseif (!is_writable($file)) {
                $errors[] = Tools::displayError('Server does not have permissions for writing.').' ('.$file.')';
            }
        }

        self::downloadSfLanguagePack($langPack['locale'], $errors);
>>>>>>> CO: Doc + cleanup Language class

        return !count($errors);
    }

<<<<<<< 281f9f01aa96dd7f1dd007bf461b32823d0862ee
    public static function downloadXLFLanguagePack($locale, &$errors = array(), $type = 'sf')
=======
    /**
     * Download Symfony Language pack
     *
     * @param string $locale
     * @param array  $errors
     */
    public static function downloadSfLanguagePack($locale, &$errors = array())
>>>>>>> CO: Doc + cleanup Language class
    {
        $file = _PS_TRANSLATIONS_DIR_.$type.'-'.$locale.'.zip';
        $url = ('emails' === $type) ? self::EMAILS_LANGUAGE_PACK_URL : self::SF_LANGUAGE_PACK_URL;
        $content = Tools::file_get_contents(sprintf($url, $locale));

        if (!is_writable(dirname($file))) {
            // @todo Throw exception
            $errors[] = Tools::displayError('Server does not have permissions for writing.').' ('.$file.')';
        } else {
            @file_put_contents($file, $content);
        }
    }

    /**
     * Install Symfony Language pack
     *
     * @param string $locale
     * @param array  $errors
     */
    public static function installSfLanguagePack($locale, &$errors = array())
    {
        if (!file_exists(_PS_TRANSLATIONS_DIR_.'sf-'.$locale.'.zip')) {
            // @todo Throw exception
            $errors[] = Tools::displayError('Language pack unavailable.');
        } else {
            $zipArchive = new ZipArchive();
            $zipArchive->open(_PS_TRANSLATIONS_DIR_.'sf-'.$locale.'.zip');
            $zipArchive->extractTo(_PS_ROOT_DIR_.'/app/Resources/translations');
        }
    }

<<<<<<< 281f9f01aa96dd7f1dd007bf461b32823d0862ee
    public static function installEmailsLanguagePack($lang_pack, &$errors = array())
=======
    /**
     * Install Language pack
     *
     * @param string $iso
     * @param array  $params
     * @param array  $errors
     *
     * @return array|bool
     */
    public static function installLanguagePack($iso, $params, &$errors = array())
>>>>>>> CO: Doc + cleanup Language class
    {
        $folder = _PS_TRANSLATIONS_DIR_.'emails-'.$lang_pack['locale'];
        $fileSystem = new \Symfony\Component\Filesystem\Filesystem();
        $finder = new \Symfony\Component\Finder\Finder();

<<<<<<< 281f9f01aa96dd7f1dd007bf461b32823d0862ee
        if (!file_exists($folder.'.zip')) {
            // @todo Throw exception
            $errors[] = Tools::displayError('Language pack unavailable.');
        } else {
            $zipArchive = new ZipArchive();
            $zipArchive->open($folder.'.zip');
            $zipArchive->extractTo($folder);

            $coreDestPath = _PS_ROOT_DIR_.'/mails/'.$lang_pack['iso_code'];
            $fileSystem->mkdir($coreDestPath, 0755);

            if ($fileSystem->exists($folder.'/core')) {
                foreach ($finder->files()->in($folder.'/core') as $coreEmail) {
                    $fileSystem->rename(
                        $coreEmail->getRealpath(),
                        $coreDestPath.'/'.$coreEmail->getFileName(),
                        true
                    );
                }
=======
        $gz = new \Archive_Tar($file, true);
        $filesList = AdminTranslationsController::filterTranslationFiles(Language::getLanguagePackListContent((string) $iso, $gz));
        $filesPaths = AdminTranslationsController::filesListToPaths($filesList);

        $tmpArray = array();

        foreach ($filesPaths as $filesPath) {
            $path = dirname($filesPath);

            if (is_dir(_PS_TRANSLATIONS_DIR_.'../'.$path) && !is_writable(_PS_TRANSLATIONS_DIR_.'../'.$path) && !in_array($path, $tmpArray)) {
                $error = Tools::displayError('The server does not have permissions for writing.').' '.sprintf(Tools::displayError('Please check permissions for %s'), $path);
                $errors[] = (count($tmpArray) == 0) ? Tools::displayError('The archive cannot be extracted.').' '.$error : $error;
                $tmpArray[] = $path;
>>>>>>> CO: Doc + cleanup Language class
            }

<<<<<<< 281f9f01aa96dd7f1dd007bf461b32823d0862ee
            if ($fileSystem->exists($folder.'/modules')) {
                foreach ($finder->directories()->in($folder.'/modules') as $moduleDirectory) {
                    $moduleDestPath = _PS_ROOT_DIR_.'/modules/'.$moduleDirectory->getFileName().'/mails/'.$lang_pack['iso_code'];
                    $fileSystem->mkdir($moduleDestPath, 0755);

                    $findEmails = new \Symfony\Component\Finder\Finder();
                    foreach ($findEmails->files()->in($moduleDirectory->getRealPath()) as $moduleEmail) {
                        $fileSystem->rename(
                            $moduleEmail->getRealpath(),
                            $moduleDestPath.'/'.$moduleEmail->getFileName(),
                            true
                        );
                    }
                }
            }

            Tools::deleteDirectory($folder);
=======
        if (defined('_PS_HOST_MODE_')) {
            $mailsFiles = array();

            foreach ($filesList as $key => $data) {
                if (substr($data['filename'], 0, 5) == 'mails') {
                    $mailsFiles[] = $data;
                }
            }

            $filesList = array_diff($filesList, $mailsFiles);
        }

        if (!$gz->extractList(AdminTranslationsController::filesListToPaths($filesList), _PS_TRANSLATIONS_DIR_.'../')) {
            $errors[] = sprintf(Tools::displayError('Cannot decompress the translation file for the following language: %s'), (string) $iso);
>>>>>>> CO: Doc + cleanup Language class
        }
    }

    public static function installLanguagePack($iso, $params, &$errors = array())
    {
        // Clear smarty modules cache
        Tools::clearCache();

        if (!Language::checkAndAddLanguage((string) $iso, false, false, $params)) {
            $errors[] = sprintf(Tools::displayError('An error occurred while creating the language: %s'), (string) $iso);
        } else {
            // Reset cache
            Language::loadLanguages();
<<<<<<< 281f9f01aa96dd7f1dd007bf461b32823d0862ee
=======
            AdminTranslationsController::checkAndAddMailsFiles((string) $iso, $filesList);
            AdminTranslationsController::addNewTabs((string) $iso, $filesList);
>>>>>>> CO: Doc + cleanup Language class
        }

        $lang_pack = self::getLangDetails($iso);
        self::installSfLanguagePack(self::getLocaleByIso($iso), $errors);
        self::installEmailsLanguagePack($lang_pack, $errors);

        return count($errors) ? $errors : true;
    }

    /**
     * Check if more on than one language is activated.
     *
     * @param null $idShop
     *
     * @return bool
     *
     * @since 1.5.0
     */
    public static function isMultiLanguageActivated($idShop = null)
    {
        return Language::countActiveLanguages($idShop) > 1;
    }

    /**
     * @param string       $iso
     * @param \Archive_Tar $tar
     *
     * @return array|bool|int|null
     */
    public static function getLanguagePackListContent($iso, $tar)
    {
        $key = 'Language::getLanguagePackListContent_'.$iso;
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
     * Update module translations
     *
     * @param array $modulesList
     */
    public static function updateModulesTranslations(array $modulesList)
    {
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $filesListing = array();
            $filegz = _PS_TRANSLATIONS_DIR_.$lang['iso_code'].'.gzip';

            clearstatcache();
            if (@filemtime($filegz) < (time() - (24 * 3600))) {
                if (Language::downloadAndInstallLanguagePack($lang['iso_code'], null, null, false) !== true) {
                    break;
                }
            }

            $gz = new \Archive_Tar($filegz, true);
            if (!$gz) {
                continue;
            }
            $filesList = Language::getLanguagePackListContent($lang['iso_code'], $gz);
            foreach ($modulesList as $moduleName) {
                foreach ($filesList as $i => $file) {
                    if (strpos($file['filename'], 'modules/'.$moduleName.'/') !== 0) {
                        unset($filesList[$i]);
                    }
                }
            }
            foreach ($filesList as $file) {
                if (isset($file['filename']) && is_string($file['filename'])) {
                    $filesListing[] = $file['filename'];
                }
            }
            $gz->extractList($filesListing, _PS_TRANSLATIONS_DIR_.'../', '');
        }
    }
}
