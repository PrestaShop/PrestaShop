<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Install;

use Locale;
use Symfony\Component\Intl\Countries;

class Language
{
    public $id;
    public $name;
    public $locale;
    public $iso_code;
    public $language_code;
    public $is_rtl;
    public $date_format_lite;
    public $date_format_full;
    public $countries;
    public $allow_accented_chars_url;

    public function __construct($iso)
    {
        $this->iso_code = strtolower($iso);
        $xmlPath = _PS_INSTALL_LANGS_PATH_ . $iso . '/';
        $this->setPropertiesFromXml($xmlPath);
        $this->is_rtl = ($this->is_rtl === 'true') ? true : false;
    }

    public function setPropertiesFromXml($xmlPath)
    {
        $xml = @simplexml_load_file($xmlPath . '/language.xml');
        if ($xml) {
            foreach ($xml->children() as $node) {
                $this->{$node->getName()} = (string) $node;
            }
        }
    }

    /**
     * Get name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get locale.
     *
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Get language_code.
     *
     * @return mixed
     */
    public function getLanguageCode()
    {
        return $this->language_code;
    }

    /**
     * Get is_rtl.
     *
     * @return mixed
     */
    public function isRtl()
    {
        return $this->is_rtl;
    }

    /**
     * Get date_format_lite.
     *
     * @return mixed
     */
    public function getDateFormatLite()
    {
        return $this->date_format_lite;
    }

    /**
     * Get date_format_full.
     *
     * @return mixed
     */
    public function getDateFormatFull()
    {
        return $this->date_format_full;
    }

    public function getCountries()
    {
        if (!is_array($this->countries)) {
            Locale::setDefault($this->getLocale());
            $this->countries = Countries::getNames();
            $this->countries = array_change_key_case($this->countries, CASE_LOWER);
        }

        return $this->countries;
    }
}
