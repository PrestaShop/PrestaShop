<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\EnumValuesParser;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\FormOptionsValidator;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use ValueError;

/**
 * Root form type for creating and editing an extra property definition.
 *
 * This type only aggregates the 5 "card" sub-forms (one per section); it renders no field of
 * its own, so the data is nested by section (field_definition, visibility, labels, validation,
 * advanced) — see ExtraPropertyDefinitionFormDataProvider/Handler for the mapping.
 *
 * Root-level Callback constraints surface the cross-card rules enforced deeper in the stack
 * (ExtraPropertyDefinition value object, ExtraPropertyRegistry) as inline errors on the
 * relevant fields instead of failing later in the command handler:
 *  - a label wording is required as soon as the property is associated with a form or a grid;
 *  - form_type/form_options must build a working form field (the rule needs the
 *    field_definition card's type/scope/enum_values, hence root level — see FormOptionsValidator).
 */
class ExtraPropertyDefinitionType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly FormOptionsValidator $formOptionsValidator,
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('field_definition', ExtraPropertyDefinitionFieldDefinitionType::class, [
                'is_edit' => $options['is_edit'],
            ])
            ->add('visibility', ExtraPropertyDefinitionVisibilityType::class)
            ->add('labels', ExtraPropertyDefinitionLabelsType::class)
            ->add('validation', ExtraPropertyDefinitionValidationType::class)
            ->add('advanced', ExtraPropertyDefinitionAdvancedType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'is_edit' => false,
            // Applied through FormThemeExtension so every template rendering this form (create,
            // edit, read-only view) gets the card/row blocks without repeating a form_theme tag.
            'form_theme' => [
                '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit.html.twig',
                '@PrestaShop/Admin/Configure/AdvancedParameters/ExtraPropertyDefinition/FormTheme/definition_form_theme.html.twig',
            ],
            'constraints' => [
                new Callback([$this, 'validateLabelWordingRequirement']),
                new Callback([$this, 'validateFormTypeAndOptions']),
            ],
        ]);
        $resolver->setAllowedTypes('is_edit', 'bool');
    }

    /**
     * Mirrors the ExtraPropertyDefinition constructor rule: labelWording is required when
     * associatedForms or associatedGrids is set. Validated here at form level so the user gets
     * an inline error on the "Label wording" field instead of a generic flash message.
     *
     * @param array<string, mixed>|null $data the whole nested form data, keyed by card section
     */
    public function validateLabelWordingRequirement(?array $data, ExecutionContextInterface $context): void
    {
        if (null === $data) {
            return;
        }

        // associated_forms/associated_grids are row collections; abandoned rows were already
        // dropped by delete_empty, so any remaining row is a real placement.
        if ([] === ($data['advanced']['associated_forms'] ?? []) && [] === ($data['advanced']['associated_grids'] ?? [])) {
            return;
        }

        if ('' !== trim((string) ($data['labels']['label_wording'] ?? ''))) {
            return;
        }

        $context->buildViolation(
            $this->trans('A label wording is required when the property is displayed in forms or grids.', 'Admin.Advparameters.Notification')
        )
            ->atPath('[labels][label_wording]')
            ->addViolation();
    }

    /**
     * Mirrors the ExtraPropertyRegistry save-time gate: the advanced card's
     * form_type/form_options must build a working form field for the type/scope/enum
     * values declared on the field_definition card. Validated here at form level so the user
     * gets an inline error on the offending field instead of a generic flash message.
     *
     * @param array<string, mixed>|null $data the whole nested form data, keyed by card section
     */
    public function validateFormTypeAndOptions(?array $data, ExecutionContextInterface $context): void
    {
        if (null === $data) {
            return;
        }

        $advanced = $data['advanced'] ?? [];
        $formType = trim((string) ($advanced['form_type'] ?? '')) ?: null;

        $formOptions = null;
        $rawFormOptions = trim((string) ($advanced['form_options'] ?? ''));
        if ('' !== $rawFormOptions) {
            $decoded = json_decode($rawFormOptions, true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                // Invalid JSON is already flagged by the field's own Assert\Json constraint.
                return;
            }
            if (!is_array($decoded) || ($decoded !== [] && array_is_list($decoded))) {
                // Valid scalar JSON (e.g. "123") and JSON lists (e.g. "[1, 2]") are NOT flagged
                // by Assert\Json — flag them here with the same wording, they would otherwise
                // only fail later in the form data handler.
                $context->buildViolation(
                    $this->trans('The form options must be a valid JSON object.', 'Admin.Advparameters.Notification')
                )
                    ->atPath('[advanced][form_options]')
                    ->addViolation();

                return;
            }
            $formOptions = $decoded;
        }

        $fieldDefinition = $data['field_definition'] ?? [];
        try {
            $type = ExtraPropertyType::from((string) ($fieldDefinition['type'] ?? ''));
            $scope = ExtraPropertyScope::from((string) ($fieldDefinition['scope'] ?? ''));
        } catch (ValueError) {
            // Unmappable structural values are flagged by the choice fields' own validation.
            return;
        }

        $errors = $this->formOptionsValidator->validate(
            $formType,
            $type,
            EnumValuesParser::parse($fieldDefinition['enum_values'] ?? null),
            $scope,
            $formOptions
        );
        if ([] === $errors) {
            return;
        }

        // A rejected FQCN is the validator's only early-return error (options are then not
        // validated), so all errors target the same field: form_type when the declared
        // type is not a form type, form_options otherwise.
        $errorPath = null !== $formType && !is_subclass_of($formType, FormTypeInterface::class)
            ? '[advanced][form_type]'
            : '[advanced][form_options]';

        foreach ($errors as $error) {
            $context->buildViolation($error)->atPath($errorPath)->addViolation();
        }
    }
}
