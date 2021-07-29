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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition;

/**
 * Properties container for single Module translation provider.
 */
class ThemeProviderDefinition implements ProviderDefinitionInterface
{
    public const DEFAULT_THEME_NAME = 'classic';

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
            $themeName = static::DEFAULT_THEME_NAME;
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
