<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * "Field definition" card: structural fields of an extra property definition.
 *
 * entity_name, property_name, type and scope are immutable once created (disabled in edit
 * mode). sql_index, size, nullable and enum_values ARE editable, but only non-destructively —
 * the registry refuses a destructive attempt server-side (ExtraPropertyRegistry::hasStorageChanges()).
 */
class ExtraPropertyDefinitionFieldDefinitionType extends TranslatorAwareType
{
    /**
     * @param list<string> $locales
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly FormChoiceProviderInterface $typeChoiceProvider,
        private readonly FormChoiceProviderInterface $scopeChoiceProvider,
        private readonly FormChoiceProviderInterface $sqlIndexChoiceProvider,
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];
        $disabledAttr = $isEdit ? ['disabled' => true] : [];

        $builder
            ->add('entity_name', TextType::class, [
                'label' => $this->trans('Entity name', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('ObjectModel table name (e.g. product, customer, order).', 'Admin.Advparameters.Help'),
                'required' => !$isEdit,
                'disabled' => $isEdit,
                'attr' => $disabledAttr,
                'constraints' => [new NotBlank()],
            ])
            ->add('property_name', TextType::class, [
                'label' => $this->trans('Property name', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Identifier used as the column name (e.g. internal_code). Only letters, digits, and underscores.', 'Admin.Advparameters.Help'),
                'required' => !$isEdit,
                'disabled' => $isEdit,
                'attr' => $disabledAttr,
                'constraints' => [new NotBlank()],
            ])
            // Carries the owning module name so the controller can detect a module-owned
            // definition from the already-built form, instead of dispatching a second query.
            ->add('module_name', HiddenType::class, [
                'required' => false,
            ])
            ->add('type', ChoiceType::class, [
                'label' => $this->trans('Field type', 'Admin.Advparameters.Feature'),
                'choices' => $this->typeChoiceProvider->getChoices(),
                'required' => !$isEdit,
                'disabled' => $isEdit,
                'attr' => $disabledAttr,
            ])
            ->add('scope', ChoiceType::class, [
                'label' => $this->trans('Scope', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('common: one value per entity. lang: one value per entity × language. shop: one value per entity × shop.', 'Admin.Advparameters.Help'),
                'choices' => $this->scopeChoiceProvider->getChoices(),
                'required' => !$isEdit,
                'disabled' => $isEdit,
                'attr' => $disabledAttr,
            ])
            ->add('sql_index', ChoiceType::class, [
                'label' => $this->trans('SQL index', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Can be changed at any time.', 'Admin.Advparameters.Help'),
                'choices' => $this->sqlIndexChoiceProvider->getChoices(),
                'required' => false,
            ])
            ->add('size', IntegerType::class, [
                'label' => $this->trans('Size', 'Admin.Advparameters.Feature'),
                'help' => $isEdit
                    ? $this->trans('VARCHAR size for string type only (defaults to 255). Can only be increased once set.', 'Admin.Advparameters.Help')
                    : $this->trans('VARCHAR size for string type only (defaults to 255). Ignored for other types.', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => ['min' => 1, 'max' => 16383],
            ])
            ->add('nullable', CheckboxType::class, [
                'label' => $this->trans('Nullable', 'Admin.Advparameters.Feature'),
                'help' => $isEdit
                    ? $this->trans('Whether the storage column allows NULL values. Can only be relaxed (not tightened) once set.', 'Admin.Advparameters.Help')
                    : $this->trans('Whether the storage column allows NULL values.', 'Admin.Advparameters.Help'),
                'required' => false,
            ])
            ->add('enum_values', TextareaType::class, [
                'label' => $this->trans('Choice values', 'Admin.Advparameters.Feature'),
                'help' => $isEdit
                    ? $this->trans('One value per line. Only used for the "Choice list" field type. Existing values cannot be removed, only new ones added.', 'Admin.Advparameters.Help')
                    : $this->trans('One value per line. Only used for the "Choice list" field type.', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('default_value', TextType::class, [
                'label' => $this->trans('Default value', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('SQL DEFAULT clause value. Leave empty for no default.', 'Admin.Advparameters.Help'),
                'required' => false,
                'disabled' => $isEdit,
                'attr' => $disabledAttr,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => $this->trans('Field definition', 'Admin.Advparameters.Feature'),
            'icon' => 'storage',
            'is_edit' => false,
        ]);
        $resolver->setAllowedTypes('is_edit', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return CardType::class;
    }
}
