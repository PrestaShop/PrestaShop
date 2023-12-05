<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Type\Material;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MaterialMultipleChoiceTableType
 * 
 * @link https://devdocs.prestashop-project.org/8/development/components/form/types-reference/material-multiple-choice-table/
 */
class MaterialMultipleChoiceTableType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['multiple_choices'] as $choices) {
            $builder->add($choices['name'], ChoiceType::class, [
                'label' => $choices['label'],
                'choices' => $choices['choices'],
                'expanded' => true,
                'multiple' => $choices['multiple'],
                'choice_label' => false,
                'choice_translation_domain' => false,
            ]);

            $builder->get($choices['name'])->addModelTransformer(new CallbackTransformer(
                function ($value) use ($choices) {
                    if (is_array($value) && false === $choices['multiple']) {
                        return reset($value);
                    }

                    return $value;
                },
                function ($value) {
                    return $value;
                }
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['choices'] = $options['choices'];
        $view->vars['scrollable'] = $options['scrollable'];
        $view->vars['headers_to_disable'] = $options['headers_to_disable'];
        $view->vars['headers_fixed'] = $options['headers_fixed'];
        $view->vars['table_label'] = $options['table_label'];
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $entryIndexMapping = [];

        foreach ($view->children as $childChoiceName => $childChoiceView) {
            foreach ($childChoiceView->children as $index => $childChoiceEntryView) {
                $entryIndexMapping[$childChoiceEntryView->vars['value']][$childChoiceName] = $index;
            }
        }

        $view->vars['child_choice_entry_index_mapping'] = $entryIndexMapping;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'multiple_choices',
                'choices',
                // in some cases we want to disable
                // header for columns
                'headers_to_disable',
            ])
            ->setDefaults([
                'scrollable' => true,
                'headers_to_disable' => [],
                'headers_fixed' => false,
                'table_label' => false,
            ])
        ;

        $resolver->setAllowedTypes('choices', 'array');
        $resolver->setAllowedTypes('multiple_choices', 'array');
        $resolver->setAllowedTypes('scrollable', 'bool');
        $resolver->setAllowedTypes('headers_to_disable', 'array');
        $resolver->setAllowedTypes('headers_fixed', 'bool');
        $resolver->setAllowedTypes('table_label', ['bool', 'string']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'material_multiple_choice_table';
    }
}
