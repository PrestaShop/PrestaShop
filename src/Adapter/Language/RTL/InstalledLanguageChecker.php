<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Language\RTL;

use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Core\Language\RTL\InstalledLanguageCheckerInterface;

/**
 * Class InstalledLanguageChecker
 */
final class InstalledLanguageChecker implements InstalledLanguageCheckerInterface
{
    /**
     * @var LanguageDataProvider
     */
    private $languageDataProvider;

    /**
     * @param LanguageDataProvider $languageDataProvider
     */
    public function __construct(LanguageDataProvider $languageDataProvider)
    {
        $this->languageDataProvider = $languageDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function isInstalledRtlLanguage()
    {
        $languages = $this->languageDataProvider->getLanguages(false);

        return in_array('1', array_column($languages, 'is_rtl'));
    }
}
