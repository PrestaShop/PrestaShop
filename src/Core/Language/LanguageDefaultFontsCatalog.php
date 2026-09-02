<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Language;

/**
 * Class LanguageDefaultFontsCatalog is used for languages that need a specific font to display their
 * characters. This class is a catalog referencing which languages need special fonts and associate
 * the appropriate font to each language.
 */
class LanguageDefaultFontsCatalog
{
    /**
     * This is a non exhaustive list of language which need a specific font
     * so that their characters are correctly displayed.
     *
     * @var array
     */
    private $languageDefaultFonts = [
        'fa' => 'Tahoma',
        'ar' => 'Tahoma',
    ];

    /**
     * @param array|null $languageDefaultFonts
     */
    public function __construct(?array $languageDefaultFonts = null)
    {
        if (null !== $languageDefaultFonts) {
            $this->languageDefaultFonts = $languageDefaultFonts;
        }
    }

    /**
     * @param LanguageInterface $language
     *
     * @return string
     */
    public function getDefaultFontByLanguage(LanguageInterface $language)
    {
        $isoCode = $language->getIsoCode();
        if (isset($this->languageDefaultFonts[$isoCode])) {
            return $this->languageDefaultFonts[$isoCode];
        }

        return '';
    }
}
