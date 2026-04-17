<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Language\Copier;

/**
 * Class LanguageCopierConfig provides configuration for language copier.
 */
final class LanguageCopierConfig implements LanguageCopierConfigInterface
{
    /**
     * @var string the theme name from which the language will be copied
     */
    private $themeFrom;

    /**
     * @var string the language iso code, which will be copied from
     */
    private $languageFrom;

    /**
     * @var string the theme name to which the language will be copied
     */
    private $themeTo;

    /**
     * @var string the language iso code, which will be copied to
     */
    private $languageTo;

    /**
     * @param string $themeFrom
     * @param string $languageFrom
     * @param string $themeTo
     * @param string $languageTo
     */
    public function __construct($themeFrom, $languageFrom, $themeTo, $languageTo)
    {
        $this->themeFrom = $themeFrom;
        $this->languageFrom = $languageFrom;
        $this->themeTo = $themeTo;
        $this->languageTo = $languageTo;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeFrom()
    {
        return $this->themeFrom;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageFrom()
    {
        return $this->languageFrom;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeTo()
    {
        return $this->themeTo;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageTo()
    {
        return $this->languageTo;
    }
}
