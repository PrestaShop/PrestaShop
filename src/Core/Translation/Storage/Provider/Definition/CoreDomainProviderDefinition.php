<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition;

/**
 * Properties container for core translation provider filtering by a single domain name.
 */
class CoreDomainProviderDefinition extends AbstractCoreProviderDefinition
{
    private const FILENAME_FILTERS_REGEX = [
        '#^%s([A-Za-z]|\.|$)#',
    ];
    private const TRANSLATION_DOMAINS_REGEX = [
        '^%s([A-Za-z]|$)',
    ];

    /**
     * @var string
     */
    private $domainName;

    /**
     * @param string $domainName
     */
    public function __construct(string $domainName)
    {
        $this->domainName = $domainName;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return ProviderDefinitionInterface::TYPE_CORE_DOMAIN;
    }

    /**
     * @return string
     */
    public function getDomainName(): string
    {
        return $this->domainName;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilenameFilters(): array
    {
        return array_map(function (string $filenameFilter) {
            return sprintf($filenameFilter, preg_quote($this->domainName, '#'));
        }, self::FILENAME_FILTERS_REGEX);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains(): array
    {
        return array_map(function (string $translationDomain) {
            return sprintf($translationDomain, preg_quote($this->domainName, '#'));
        }, self::TRANSLATION_DOMAINS_REGEX);
    }
}
