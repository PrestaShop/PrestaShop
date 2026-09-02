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
class MailsBodyProviderDefinition extends AbstractCoreProviderDefinition
{
    private const FILENAME_FILTERS_REGEX = ['#EmailsBody*#'];

    private const TRANSLATION_DOMAINS_REGEX = ['EmailsBody*'];

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return ProviderDefinitionInterface::TYPE_MAILS_BODY;
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
