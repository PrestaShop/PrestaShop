<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Type\Material;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $resolver->setRequired([
            'multiple_choices',
            'choices',
        ]);

        $resolver->setAllowedTypes('choices', 'array');
        $resolver->setAllowedTypes('multiple_choices', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'material_multiple_choice_table';
    }
}
