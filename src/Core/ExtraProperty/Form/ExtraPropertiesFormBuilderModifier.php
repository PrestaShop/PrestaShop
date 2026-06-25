<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Form;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyReaderInterface;
use PrestaShopBundle\Form\Admin\Type\NavigationTabType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\FormBuilderModifier;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Adds extra properties fields into an identifiable object form builder.
 *
 * Placement (associatedForms entry from the definition registry, format "formId[:path[:before|after]]"):
 * - null/empty        => dedicated 'extra_fields.extra_properties' section (created if missing); on simple
 *                        forms without tabs, fields are placed at root level instead.
 * - path, no mode     => the path is a CONTAINER: navigate it (every segment must exist, throws
 *                        InvalidArgumentException otherwise) and append the field inside it.
 * - path:before       => the last path segment is an ANCHOR: navigate to the parent builder and insert the
 *                        field BEFORE the anchor (anchor must exist, throws otherwise).
 * - path:after        => same, inserting the field AFTER the anchor.
 *
 * The container vs anchor split is resolved once in ExtraPropertyDefinition::getFormEntry() (which returns
 * the resolved 'path' + 'anchor') and shared with ExtraPropertiesFormDataPersister so placement and value
 * retrieval cannot drift.
 *
 * Data mapping:
 * - fields are added as unmapped; persistence reads submitted values from the FormInterface.
 */
class ExtraPropertiesFormBuilderModifier
{
    public const FALLBACK_FORM_SECTION = 'extra_properties';
    public const DEFAULT_FALLBACK_TAB = 'extra_fields';

    public function __construct(
        protected readonly ExtraPropertyDefinitionRepositoryInterface $repository,
        protected readonly ExtraPropertyReaderInterface $reader,
        protected readonly TranslatorInterface $translator,
        protected readonly ShopContext $shopContext,
        protected readonly FormBuilderModifier $formBuilderModifier,
    ) {
    }

    /**
     * @param string $formId Form identifier (equals form block_prefix, e.g. 'product', 'category')
     * @param int|null $entityId When null, no prefill is attempted (create form)
     */
    public function apply(FormBuilderInterface $formBuilder, string $formId, ?int $entityId): void
    {
        $formDefinitions = $this->repository->getAllDefinitions()->filterByForm($formId);
        if ($formDefinitions->isEmpty()) {
            return;
        }

        $existingValues = null;
        if (null !== $entityId && $entityId > 0) {
            $existingValues = $this->reader->getExtraProperties(
                $formDefinitions->first()->getEntityName(),
                $formDefinitions->first()->getPrimaryKeyName(),
                $entityId,
                null,
                $this->shopContext->getShopConstraint(),
                true,
                $formDefinitions
            );
        }

        foreach ($formDefinitions as $definition) {
            $formEntry = $definition->getFormEntry($formId);

            $formFieldName = $definition->getFieldName();

            [$type, $typeOptions] = $this->resolveFieldTypeAndOptions($definition);

            if (null !== $existingValues) {
                // The reader returns typed values (ExtraPropertyValueCaster applied on read).
                $typeOptions['data'] = $this->resolveExistingValue($existingValues, $definition);
            }

            if (null === $formEntry || null === $formEntry['path']) {
                $targetBuilder = $this->resolveOrCreateFallbackPath($formBuilder);
                if (!$targetBuilder->has($formFieldName)) {
                    $targetBuilder->add($formFieldName, $type, $typeOptions);
                }
            } else {
                $this->addAtPosition($formBuilder, $formEntry, $formFieldName, $type, $typeOptions);
            }
        }
    }

    /**
     * @return array{0: class-string<FormTypeInterface>, 1: array<string, mixed>}
     */
    protected function resolveFieldTypeAndOptions(ExtraPropertyDefinition $definition): array
    {
        $declaredType = $definition->getFormFieldType();
        $extraOptions = $definition->getFormOptions() ?? [];

        $baseType = (null !== $declaredType && class_exists($declaredType)) ? $declaredType : TextType::class;

        // Validation is fully constraint-based: attach the definition's Symfony constraints to the field so the
        // form's own validation runs them and surfaces one error per failing constraint. Requiredness is the
        // module's responsibility — it passes Assert\NotBlank when a value must be provided (no auto NotBlank).
        $constraints = $definition->getConstraints() ?? [];

        $label = $this->translateLabel($definition->getLabelWording(), $definition->getLabelDomain());
        $help = $this->translateLabel($definition->getDescriptionWording(), $definition->getDescriptionDomain());

        if (ExtraPropertyScope::LANG === $definition->getScope()) {
            // In BO, use TranslatableType (data keyed by id_lang). Constraints are attached to the OUTER field so
            // they validate the whole [id_lang => value] array — consistent with ExtraPropertyValidator: whole-array
            // constraints (e.g. DefaultLanguage) see the array, per-language rules use Assert\All. The per-language
            // children carry only $extraOptions (no constraints of their own).
            return [
                TranslatableType::class,
                [
                    'type' => $baseType,
                    'label' => $label,
                    'help' => $help,
                    'mapped' => false,
                    'required' => $definition->isRequired(),
                    'constraints' => $constraints,
                    'options' => $extraOptions,
                ],
            ];
        }

        return [
            $baseType,
            array_merge(
                [
                    'mapped' => false,
                    'required' => $definition->isRequired(),
                    'label' => $label,
                    'help' => $help,
                    'constraints' => $constraints,
                ],
                $extraOptions
            ),
        ];
    }

    /**
     * @param array<string, array<string, mixed>> $existingValues
     *
     * @return mixed
     */
    protected function resolveExistingValue(array $existingValues, ExtraPropertyDefinition $definition): mixed
    {
        $value = $existingValues[$definition->getNormalizedModuleKey()][$definition->getPropertyName()] ?? null;

        if (ExtraPropertyScope::LANG === $definition->getScope()) {
            return is_array($value) ? $value : [];
        }

        return $value;
    }

    /**
     * Adds a field at the position resolved by ExtraPropertyDefinition::getFormEntry().
     *
     * $formEntry already carries the resolved placement: path is the node the field belongs to and anchor
     * is the optional sibling field (null for container placement).
     * - anchor null => path is a container; append the field inside it.
     * - anchor set  => insert the field before/after the anchor (per $formEntry['mode']) in path.
     *
     * @param array{mode: 'before'|'after'|null, path: string|null, anchor: string|null} $formEntry
     * @param class-string<FormTypeInterface> $type
     * @param array<string, mixed> $typeOptions
     *
     * @throws InvalidArgumentException when a path segment (container or anchor parent) does not exist
     */
    protected function addAtPosition(
        FormBuilderInterface $rootBuilder,
        array $formEntry,
        string $formFieldName,
        string $type,
        array $typeOptions,
    ): void {
        $targetBuilder = $this->resolvePath($rootBuilder, (string) $formEntry['path']);

        if ($targetBuilder->has($formFieldName)) {
            return;
        }

        // Container placement (no mode): append the field directly inside the resolved node.
        if (null === $formEntry['anchor']) {
            $targetBuilder->add($formFieldName, $type, $typeOptions);

            return;
        }

        // Anchor placement: insert relative to the anchor field inside the parent builder.
        // addBefore/addAfter throw InvalidArgumentException when the anchor does not exist.
        if ('before' === $formEntry['mode']) {
            $this->formBuilderModifier->addBefore($targetBuilder, $formEntry['anchor'], $formFieldName, $type, $typeOptions);
        } else {
            $this->formBuilderModifier->addAfter($targetBuilder, $formEntry['anchor'], $formFieldName, $type, $typeOptions);
        }
    }

    /**
     * Resolves (and creates if missing) the designated fallback area for extra fields.
     *
     * - Forms with tabs: resolves/creates 'extra_fields.extra_properties'.
     * - Simple forms (no tabs): returns the root builder (fields injected at root level).
     */
    protected function resolveOrCreateFallbackPath(FormBuilderInterface $rootBuilder): FormBuilderInterface
    {
        if (!$rootBuilder->has(self::DEFAULT_FALLBACK_TAB) && !$this->isNavigationTabForm($rootBuilder)) {
            return $rootBuilder;
        }

        if (!$rootBuilder->has(self::DEFAULT_FALLBACK_TAB)) {
            $rootBuilder->add(self::DEFAULT_FALLBACK_TAB, FormType::class, [
                'mapped' => false,
                'required' => false,
                'label' => $this->translator->trans('Extra fields', domain: 'Admin.Global'),
                'row_attr' => [],
            ]);
        }

        /** @var FormBuilderInterface $tabBuilder */
        $tabBuilder = $rootBuilder->get(self::DEFAULT_FALLBACK_TAB);

        if (!$tabBuilder->has(self::FALLBACK_FORM_SECTION)) {
            $tabBuilder->add(self::FALLBACK_FORM_SECTION, FormType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
                'row_attr' => [],
            ]);
        }

        /** @var FormBuilderInterface $sectionBuilder */
        $sectionBuilder = $tabBuilder->get(self::FALLBACK_FORM_SECTION);

        return $sectionBuilder;
    }

    /**
     * Strictly resolves a dot-separated path inside an existing form builder.
     *
     * An empty path resolves to the root builder itself.
     *
     * @throws InvalidArgumentException when any segment of the path does not exist
     */
    protected function resolvePath(FormBuilderInterface $rootBuilder, string $path): FormBuilderInterface
    {
        $segments = array_values(array_filter(array_map('trim', explode('.', $path)), static fn (string $s): bool => '' !== $s));
        if (empty($segments)) {
            return $rootBuilder;
        }

        $builder = $rootBuilder;
        foreach ($segments as $segment) {
            if (!$builder->has($segment)) {
                throw new InvalidArgumentException(sprintf(
                    'Extra property associated_forms path "%s": segment "%s" does not exist in the form.',
                    $path,
                    $segment
                ));
            }
            /** @var FormBuilderInterface $builder */
            $builder = $builder->get($segment);
        }

        return $builder;
    }

    protected function isNavigationTabForm(FormBuilderInterface $formBuilder): bool
    {
        if ($this->hasNavigationTabTypeInHierarchy($formBuilder->getType())) {
            return true;
        }
        // Also check direct children: the root form is often a compound form
        // whose tabs are NavigationTabType children, not its own type.
        foreach ($formBuilder->all() as $childBuilder) {
            if ($this->hasNavigationTabTypeInHierarchy($childBuilder->getType())) {
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

    /**
     * Translates a wording/domain pair from a definition, falling back to $default.
     */
    protected function translateLabel(?string $wording, ?string $domain, ?string $default = null): ?string
    {
        if (null === $wording || '' === trim($wording)) {
            return $default;
        }

        return $this->translator->trans($wording, [], $domain ?? 'Admin.Global');
    }
}
