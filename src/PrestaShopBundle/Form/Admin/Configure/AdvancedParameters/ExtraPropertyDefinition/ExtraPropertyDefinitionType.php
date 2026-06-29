<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Root form type for creating and editing an extra property definition.
 *
 * This type only aggregates the 5 "card" sub-forms (one per section); it renders no field of
 * its own, so the data is nested by section (field_definition, visibility, labels, validation,
 * advanced) — see ExtraPropertyDefinitionFormDataProvider/Handler for the mapping.
 */
class ExtraPropertyDefinitionType extends TranslatorAwareType
{
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
        ]);
        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}
