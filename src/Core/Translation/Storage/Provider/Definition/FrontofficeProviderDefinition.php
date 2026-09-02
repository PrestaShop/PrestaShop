<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition;

/**
 * Properties container for Backoffice translation provider.
 */
class FrontofficeProviderDefinition extends AbstractCoreProviderDefinition
{
    private const FILENAME_FILTERS_REGEX = ['#^Shop*#'];

    private const TRANSLATION_DOMAINS_REGEX = ['^Shop*'];

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return ProviderDefinitionInterface::TYPE_FRONT;
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
