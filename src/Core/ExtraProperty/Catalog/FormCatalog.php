<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Throwable;

/**
 * Enumerates the identifiable-object back-office forms from the
 * prestashop.core.form.identifiable_object.form_types parameter (built by
 * IdentifiableObjectFormTypesCollectorPass in every environment).
 *
 * The form id is the type's block prefix — the exact id a form builder reports to
 * ExtraPropertiesFormBuilderModifier at runtime. Since block prefixes have no display
 * name of their own, the label reuses the translated name of the grid sharing the same
 * id when one exists (e.g. "product", "customer"), and falls back to a humanized block
 * prefix otherwise. Unresolvable form types are logged and skipped.
 *
 * The scan is memoized per instance; a cache decorator can later wrap this service for
 * cross-request caching (see the prestashop.extra_property.catalog.filesystem_cache pool).
 */
final class FormCatalog implements FormCatalogInterface
{
    /**
     * @var array<string, FormCatalogEntry>|null indexed by form id, sorted by label
     */
    private ?array $entries = null;

    /**
     * @param list<string> $identifiableObjectFormTypes form type FQCNs, from the
     *                                                  prestashop.core.form.identifiable_object.form_types parameter
     */
    public function __construct(
        private readonly array $identifiableObjectFormTypes,
        private readonly FormRegistryInterface $formRegistry,
        private readonly GridCatalogInterface $gridCatalog,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getAll(): array
    {
        return array_values($this->getEntries());
    }

    public function has(string $formId): bool
    {
        return isset($this->getEntries()[$formId]);
    }

    public function getFormTypeClass(string $formId): ?string
    {
        $entry = $this->getEntries()[$formId] ?? null;

        return $entry?->formTypeClass;
    }

    /**
     * @return array<string, FormCatalogEntry>
     */
    private function getEntries(): array
    {
        if (null !== $this->entries) {
            return $this->entries;
        }

        $entries = [];
        foreach ($this->identifiableObjectFormTypes as $formTypeClass) {
            try {
                $formId = $this->formRegistry->getType($formTypeClass)->getBlockPrefix();
            } catch (Throwable $e) {
                $this->logger->warning(
                    sprintf('Extra property form catalog: skipped form type "%s": %s', $formTypeClass, $e->getMessage()),
                    ['exception' => $e],
                );
                continue;
            }

            if ('' === $formId || isset($entries[$formId])) {
                continue;
            }

            $entries[$formId] = new FormCatalogEntry($formId, $this->resolveLabel($formId), $formTypeClass);
        }

        uasort($entries, static fn (FormCatalogEntry $a, FormCatalogEntry $b): int => strcasecmp($a->label, $b->label));

        return $this->entries = $entries;
    }

    private function resolveLabel(string $formId): string
    {
        $gridEntry = $this->gridCatalog->get($formId);
        if (null !== $gridEntry && '' !== $gridEntry->label) {
            return $gridEntry->label;
        }

        return ucfirst(str_replace('_', ' ', $formId));
    }
}
