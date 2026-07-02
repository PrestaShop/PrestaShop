<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

/**
 * Enumerates the identifiable-object back-office forms an extra property definition can be
 * associated with. Form ids are the form type block prefixes (the same ids used by
 * ExtraPropertyDefinition::getAssociatedForms()).
 */
interface FormCatalogInterface
{
    /**
     * @return list<FormCatalogEntry> sorted by label
     */
    public function getAll(): array;

    public function has(string $formId): bool;

    /**
     * @return class-string|null the form type FQCN behind the given form id
     */
    public function getFormTypeClass(string $formId): ?string;
}
