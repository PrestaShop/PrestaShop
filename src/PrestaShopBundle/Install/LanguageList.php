<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Install;

use Symfony\Component\Finder\Finder;
use PrestashopInstallerException;

class LanguageList
{
    const DEFAULT_ISO = 'en';

    /**
     * @var array List of available languages
     */
    protected $languages;

    /**
     * @var string Current language
     */
    protected $language;

    public $locale;

    public $id;

    /**
     * @var Language Default language (english)
     */
    protected $default;

    protected static $_instance;

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        // English language is required
        if (!file_exists(_PS_INSTALL_LANGS_PATH_.'en/language.xml')) {
            throw new PrestashopInstallerException('English language is missing');
        }

        $this->languages = array(
            self::DEFAULT_ISO => new Language(self::DEFAULT_ISO),
        );

        // Load other languages
        foreach ((new Finder)->files()->name('language.xml')->in(_PS_INSTALL_LANGS_PATH_) as $langFile) {
            $this->languages[$langFile->getRelativePath()] = new Language($langFile->getRelativePath());
        }
        uasort($this->languages, function ($a, $b) {
            $aname = $a->getName();
            $bname = $b->getName();
            if ($aname == $bname) {
                return 0;
            }
            return ($aname < $bname) ? -1 : 1;
        });
    }

    /**
     * Set current language
     *
     * @param string $iso Language iso
     */
    public function setLanguage($iso)
    {
        if (!in_array($iso, $this->getIsoList())) {
            throw new PrestashopInstallerException('Language '.$iso.' not found');
        }
        $this->language = $iso;
    }

    /**
     * Get current language
     *
     * @return string
     */
    public function getLanguageIso()
    {
        return $this->language;
    }

    /**
     * Get current language
     *
     * @return Language
     */
    public function getLanguage($iso = null)
    {
        if (!$iso) {
            $iso = $this->language;
        }
        return $this->languages[$iso];
    }

    public function getIsoList()
    {
        return array_keys($this->languages);
    }

    /**
     * Get list of languages iso supported by installer
     *
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Get list of countries for current language
     *
     * @return array
     */
    public function getCountries()
    {
        static $countries = null;

        if (is_null($countries)) {
            $countries = array();
            $countries_lang = $this->getLanguage()->getCountries();
            $countries_default = $this->getLanguage(self::DEFAULT_ISO)->getCountries();
            $xml = @simplexml_load_file(_PS_INSTALL_DATA_PATH_.'xml/country.xml');
            if ($xml) {
                foreach ($xml->entities->country as $country) {
                    $iso = strtolower((string)$country['iso_code']);
                    $countries[$iso] = isset($countries_lang[$iso]) ? $countries_lang[$iso] : $countries_default[$iso];
                }
            }
            asort($countries);
        }

        return $countries;
    }

    /**
     * Parse HTTP_ACCEPT_LANGUAGE and get first data matching list of available languages
     *
     * @return bool|array
     */
    public function detectLanguage()
    {
        // This code is from a php.net comment : http://www.php.net/manual/fr/reserved.variables.server.php#94237
        $split_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        if (!is_array($split_languages)) {
            return false;
        }

        foreach ($split_languages as $lang) {
            $pattern = '/^(?P<primarytag>[a-zA-Z]{2,8})'.
                '(?:-(?P<subtag>[a-zA-Z]{2,8}))?(?:(?:;q=)'.
                '(?P<quantifier>\d\.\d))?$/';
            if (preg_match($pattern, $lang, $m)) {
                if (in_array($m['primarytag'], $this->getIsoList())) {
                    return $m;
                }
            }
        }
        return false;
    }
}
