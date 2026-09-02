<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;

/**
 * Properties container for single Module translation provider.
 */
class ThemeProviderDefinition implements ProviderDefinitionInterface
{
    /**
     * @deprecated To be removed in 10.0
     */
    public const DEFAULT_THEME_NAME = _PS_DEFAULT_THEME_NAME_;

    private const FILENAME_FILTERS_REGEX = [];

    private const TRANSLATION_DOMAINS_REGEX = [];

    /**
     * @var string
     */
    private $themeName;

    /**
     * @param string|null $themeName
     */
    public function __construct(?string $themeName = null)
    {
        if (null === $themeName) {
            $themeName = Theme::getDefaultTheme();
        }

        $this->themeName = $themeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return ProviderDefinitionInterface::TYPE_THEMES;
    }

    /**
     * @return string
     */
    public function getThemeName(): string
    {
        return $this->themeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilenameFilters(): array
    {
        return self::FILENAME_FILTERS_REGEX;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains(): array
    {
        return self::TRANSLATION_DOMAINS_REGEX;
    }
}
