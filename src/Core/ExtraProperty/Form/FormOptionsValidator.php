<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Form;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface as OptionsResolverExceptionInterface;
use Throwable;

/**
 * Validates the formType/formOptions pair of an extra property definition by building a
 * throwaway form with the EXACT type and merged options ExtraPropertiesFormBuilderModifier
 * will use at render time (see resolveFieldTypeAndOptions()), so an accepted definition is
 * guaranteed to build later and a refused one fails at save time instead of breaking the
 * target back-office form.
 *
 * Runtime-only option values injected by the modifier (label, help) are replaced by dummy
 * strings: they never influence option resolution.
 */
class FormOptionsValidator
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly ExtraPropertyFormTypeMap $formTypeMap,
    ) {
    }

    /**
     * @param string|null $formTypeFqcn Explicit form type override declared by the definition (null = mapped default)
     * @param list<string>|null $enumValues ENUM literals of a CHOICE definition (null for other types)
     * @param array<string, mixed>|null $formOptions Extra options merged into the form type options
     *
     * @return list<string> human-readable errors; empty when the form field can be built
     */
    public function validate(
        ?string $formTypeFqcn,
        ExtraPropertyType $type,
        ?array $enumValues,
        ExtraPropertyScope $scope,
        ?array $formOptions,
    ): array {
        // Nothing declared: the mapped default type with its base options is built by
        // construction (covered by ExtraPropertyFormTypeMapTest) — no need to build a form.
        if (null === $formTypeFqcn && (null === $formOptions || [] === $formOptions)) {
            return [];
        }

        // The modifier silently falls back to the mapped default when the FQCN is not a form
        // type; at save time this is refused instead — a definition must not declare a type it
        // will never render with. Options are not validated against the unintended fallback.
        if (null !== $formTypeFqcn && !is_subclass_of($formTypeFqcn, FormTypeInterface::class)) {
            return [sprintf(
                '"%s" is not a Symfony form type: it must be the fully-qualified class name of a %s implementation.',
                $formTypeFqcn,
                FormTypeInterface::class
            )];
        }

        [$effectiveType, $mergedOptions] = $this->resolveEffectiveTypeAndOptions(
            $formTypeFqcn,
            $type,
            $enumValues,
            $scope,
            $formOptions ?? []
        );

        try {
            $this->formFactory->create($effectiveType, null, $mergedOptions);
        } catch (OptionsResolverExceptionInterface $e) {
            // The resolver messages are precise and self-contained (unknown option, wrong value
            // type, disallowed value...) — surface them as-is.
            return [$e->getMessage()];
        } catch (Throwable $e) {
            return [sprintf('The form field could not be built with these options: %s', $e->getMessage())];
        }

        return [];
    }

    /**
     * Replicates ExtraPropertiesFormBuilderModifier::resolveFieldTypeAndOptions() so the
     * throwaway form is built with the same type and option merge as the real one.
     *
     * @param list<string>|null $enumValues
     * @param array<string, mixed> $extraOptions
     *
     * @return array{0: class-string<FormTypeInterface>, 1: array<string, mixed>}
     */
    private function resolveEffectiveTypeAndOptions(
        ?string $formTypeFqcn,
        ExtraPropertyType $type,
        ?array $enumValues,
        ExtraPropertyScope $scope,
        array $extraOptions,
    ): array {
        if (null !== $formTypeFqcn) {
            $baseType = $formTypeFqcn;
            $baseOptions = [];
        } else {
            [$baseType, $baseOptions] = $this->formTypeMap->getDefaultFor($type, $enumValues);
        }

        if (ExtraPropertyScope::LANG === $scope) {
            // LANG fields are wrapped in a TranslatableType whose per-language children carry
            // the merged base + extra options (see the modifier) — validate that inner merge.
            return [
                TranslatableType::class,
                [
                    'type' => $baseType,
                    'label' => 'label',
                    'help' => 'help',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [],
                    'options' => array_merge($baseOptions, $extraOptions),
                ],
            ];
        }

        return [
            $baseType,
            array_merge(
                $baseOptions,
                [
                    'mapped' => false,
                    'required' => false,
                    'label' => 'label',
                    'help' => 'help',
                    'constraints' => [],
                ],
                $extraOptions
            ),
        ];
    }
}
