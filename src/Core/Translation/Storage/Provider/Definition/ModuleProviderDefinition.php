<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition;

use PrestaShop\TranslationToolsBundle\Translation\Helper\DomainHelper;

/**
 * Properties container for single Module translation provider.
 */
class ModuleProviderDefinition extends AbstractCoreProviderDefinition
{
    private const FILENAME_FILTERS_REGEX = ['#^%s([A-Z]|\.|$)#'];

    private const TRANSLATION_DOMAINS_REGEX = ['^%s([A-Z]|$)'];

    /**
     * @var string
     */
    private $moduleName;

    public function __construct(string $moduleName)
    {
        $this->moduleName = $moduleName;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return ProviderDefinitionInterface::TYPE_MODULES;
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilenameFilters(): array
    {
        return array_map(function (string $filenameFilter) {
            return sprintf($filenameFilter, preg_quote(DomainHelper::buildModuleBaseDomain($this->moduleName)));
        }, self::FILENAME_FILTERS_REGEX);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains(): array
    {
        return array_map(function (string $translationDomain) {
            return sprintf($translationDomain, preg_quote(DomainHelper::buildModuleBaseDomain($this->moduleName)));
        }, self::TRANSLATION_DOMAINS_REGEX);
    }
}
