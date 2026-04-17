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
class OthersProviderDefinition extends AbstractCoreProviderDefinition
{
    public const OTHERS_DOMAIN_NAME = 'messages';

    private const FILENAME_FILTERS_REGEX = ['#^' . self::OTHERS_DOMAIN_NAME . '*#'];

    private const TRANSLATION_DOMAINS_REGEX = ['^' . self::OTHERS_DOMAIN_NAME . '*'];

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return ProviderDefinitionInterface::TYPE_OTHERS;
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
