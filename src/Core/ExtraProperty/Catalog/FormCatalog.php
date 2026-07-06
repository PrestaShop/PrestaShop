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
 * Enumerates the identifiable-object back-office forms an extra property definition can be
 * associated with, from the prestashop.core.form.identifiable_object.form_types parameter
 * (built by IdentifiableObjectFormTypesCollectorPass in every environment).
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
class FormCatalog
{
    /**
     * The form type class is kept internally (for getFormTypeClass()) but never exposed by
     * getAll(): it is a server-side implementation detail the client payload must not leak.
     *
     * @var array<string, array{id: string, label: string, formTypeClass: class-string}>|null indexed by form id, sorted by label
     */
    private ?array $entries = null;

    /**
     * @param list<string> $identifiableObjectFormTypes form type FQCNs, from the
     *                                                  prestashop.core.form.identifiable_object.form_types parameter
     */
    public function __construct(
        private readonly array $identifiableObjectFormTypes,
        private readonly FormRegistryInterface $formRegistry,
        private readonly GridCatalog $gridCatalog,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @return list<array{id: string, label: string}> sorted by label
     */
    public function getAll(): array
    {
        return array_values(array_map(
            static fn (array $entry): array => ['id' => $entry['id'], 'label' => $entry['label']],
            $this->getEntries()
        ));
    }

    public function has(string $formId): bool
    {
        return isset($this->getEntries()[$formId]);
    }

    /**
     * @return class-string|null the form type FQCN behind the given form id
     */
    public function getFormTypeClass(string $formId): ?string
    {
        return $this->getEntries()[$formId]['formTypeClass'] ?? null;
    }

    /**
     * @return array<string, array{id: string, label: string, formTypeClass: class-string}>
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

            $entries[$formId] = [
                'id' => $formId,
                'label' => $this->resolveLabel($formId),
                'formTypeClass' => $formTypeClass,
            ];
        }

        uasort($entries, static fn (array $a, array $b): int => strcasecmp($a['label'], $b['label']));

        return $this->entries = $entries;
    }

    private function resolveLabel(string $formId): string
    {
        $gridLabel = $this->gridCatalog->get($formId)['label'] ?? '';
        if ('' !== $gridLabel) {
            return $gridLabel;
        }

        return ucfirst(str_replace('_', ' ', $formId));
    }
}
