<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Form;

use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionShopFilterInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriterInterface;
use PrestaShopBundle\Form\Admin\Type\NavigationTabType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * Persists extra properties submitted in a Back Office form.
 *
 * Strategy:
 * - Read submitted values from form fields (unmapped) based on definitions.
 * - Group them by module/property — scope routing happens inside the writer.
 * - Write directly via ExtraPropertyWriterInterface.
 */
class ExtraPropertiesFormDataPersister
{
    public function __construct(
        protected readonly ExtraPropertyDefinitionRepositoryInterface $repository,
        protected readonly ExtraPropertyWriterInterface $writer,
        protected readonly ShopContext $shopContext,
        protected readonly ExtraPropertyDefinitionShopFilterInterface $definitionShopFilter,
    ) {
    }

    public function persist(FormInterface $form, string $entityName, int $entityId): void
    {
        if ($entityId <= 0) {
            return;
        }

        // Same shop filter as ExtraPropertiesFormBuilderModifier: fields that were not
        // built are not read back (the writer would skip them anyway — defense in depth).
        $definitions = $this->definitionShopFilter->filterByShopConstraint(
            $this->repository->getAllDefinitions()->filterByForm($entityName),
            $this->shopContext->getShopConstraint()
        );
        if ($definitions->isEmpty()) {
            return;
        }

        $storageEntityName = $definitions->first()->getTableName();

        $valuesByModule = [];

        foreach ($definitions as $definition) {
            // getFormEntry() returns the fully-resolved placement (path is the node the field lives in)
            // — the builder used the same value, so the two cannot drift.
            $formEntry = $definition->getFormEntry($entityName);

            if (null === $formEntry || null === $formEntry['path']) {
                // Fallback placement, kept consistent with ExtraPropertiesFormBuilderModifier.
                $targetPath = ($form->has(ExtraPropertiesFormBuilderModifier::DEFAULT_FALLBACK_TAB) || $this->isNavigationTabForm($form))
                    ? ExtraPropertiesFormBuilderModifier::DEFAULT_FALLBACK_TAB . '.' . ExtraPropertiesFormBuilderModifier::FALLBACK_FORM_SECTION
                    : '';
            } else {
                $targetPath = (string) $formEntry['path'];
            }
            $formFieldName = $definition->getFieldName();

            $targetForm = $this->resolveTargetFormForExtraField($form, $targetPath, $formFieldName);
            if (null === $targetForm) {
                continue;
            }

            // No cast here: the writer itself is the single castForDb() choke point, so
            // form submissions get exactly the same typing as ObjectModel and API writes.
            $submittedValue = $targetForm->get($formFieldName)->getData();

            $valuesByModule[$definition->getNormalizedModuleKey()][$definition->getPropertyName()] = $submittedValue;
        }

        // Scope routing, storage column resolution and single-shop guards for
        // lang/shop scopes are handled by the writer.
        $this->writer->writeAll(
            $storageEntityName,
            $definitions->first()->getPrimaryKeyName(),
            $entityId,
            $valuesByModule,
            $this->shopContext->getShopConstraint()
        );
    }

    /**
     * Resolves the sub-form that holds the unmapped extra field, consistent with ExtraPropertiesFormBuilderModifier.
     *
     * $targetPath is the node the field lives in (already resolved by getFormEntry): the full container
     * path for no-mode entries, or the anchor's parent for before/after entries.
     */
    protected function resolveTargetFormForExtraField(FormInterface $rootForm, string $targetPath, string $formFieldName): ?FormInterface
    {
        $targetForm = $this->resolvePathForm($rootForm, $targetPath);
        if (null !== $targetForm && $targetForm->has($formFieldName)) {
            return $targetForm;
        }
        if ($rootForm->has($formFieldName)) {
            return $rootForm;
        }

        return null;
    }

    protected function resolvePathForm(FormInterface $rootForm, string $path): ?FormInterface
    {
        $segments = array_values(array_filter(array_map('trim', explode('.', $path)), static fn (string $s): bool => '' !== $s));
        if (empty($segments)) {
            return $rootForm;
        }

        $current = $rootForm;
        foreach ($segments as $segment) {
            if (!$current->has($segment)) {
                return null;
            }
            $current = $current->get($segment);
        }

        return $current;
    }

    protected function isNavigationTabForm(FormInterface $form): bool
    {
        // Climb to root form, then check its type and direct children (tabs live as direct children).
        $root = $form;
        while (null !== $root->getParent()) {
            $root = $root->getParent();
        }
        if ($this->hasNavigationTabTypeInHierarchy($root->getConfig()->getType())) {
            return true;
        }
        foreach ($root->all() as $child) {
            if ($this->hasNavigationTabTypeInHierarchy($child->getConfig()->getType())) {
                return true;
            }
        }

        return false;
    }

    protected function hasNavigationTabTypeInHierarchy(ResolvedFormTypeInterface $resolvedType): bool
    {
        $current = $resolvedType;
        while (null !== $current) {
            if ($current->getInnerType() instanceof NavigationTabType) {
                return true;
            }
            $current = $current->getParent();
        }

        return false;
    }
}
