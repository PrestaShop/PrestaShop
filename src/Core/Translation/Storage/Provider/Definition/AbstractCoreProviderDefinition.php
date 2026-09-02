<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition;

/**
 * Properties container for core translation providers.
 */
abstract class AbstractCoreProviderDefinition implements ProviderDefinitionInterface
{
    /**
     * @return string
     */
    abstract public function getType(): string;

    /**
     * Returns a list of patterns to filter catalogue files.
     * Depends on the translation type.
     *
     * @return array<int, string>
     */
    abstract public function getFilenameFilters(): array;

    /**
     * Returns a list of patterns to filter translation domains.
     * Depends on the translation type.
     *
     * @return array<int, string>
     */
    abstract public function getTranslationDomains(): array;
}
