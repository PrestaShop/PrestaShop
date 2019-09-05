<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
    private $languageDefaultFonts = array(
        'fa' => 'Tahoma',
        'ar' => 'Tahoma',
    );

    /**
     * @param array|null $languageDefaultFonts
     */
    public function __construct(array $languageDefaultFonts = null)
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
