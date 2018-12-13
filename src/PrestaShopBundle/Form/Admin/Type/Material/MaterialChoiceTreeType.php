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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialChoiceTreeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $selectedData = [];
        if (null !== $form->getData()) {
            $selectedData = is_array($form->getData()) ? $form->getData() : [$form->getData()];
        }

        $view->vars['multiple'] = $options['multiple'];
        $view->vars['choices_tree'] = $options['choices_tree'];
        $view->vars['choice_label'] = $options['choice_label'];
        $view->vars['choice_value'] = $options['choice_value'];
        $view->vars['choice_children'] = $options['choice_children'];
        $view->vars['disabled_values'] = $options['disabled_values'];
        $view->vars['selected_values'] = $selectedData;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices_tree' => [],
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choice_children' => 'children',
                'disabled_values' => [],
                'multiple' => false,
                'compound' => false,
            ])
            ->setAllowedTypes('choices_tree', 'array')
            ->setAllowedTypes('multiple', 'bool')
            ->setAllowedTypes('choice_value', 'string')
            ->setAllowedTypes('choice_label', 'string')
            ->setAllowedTypes('choice_children', 'string')
            ->setAllowedTypes('disabled_values', 'array')
            ->addAllowedValues('compound', false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'material_choice_tree';
    }
}
