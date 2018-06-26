<?php
/**
 * 2007-2018 PrestaShop
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

use function foo\func;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialMultipleChoiceTableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['multiple_choices'] as $choices) {
            $builder->add($choices['id'], ChoiceType::class, [
                'label' => $choices['name'],
                'choices' => $choices['choices'],
                'expanded' => true,
                'multiple' => $choices['allow_multiple'],
                'choice_label' => false,
            ]);

            $builder->get($choices['id'])->addModelTransformer(new CallbackTransformer(
                function ($value) use ($choices) {
                    if (is_array($value) && false === $choices['allow_multiple']) {
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

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $mergedChoices = [];

        foreach ($options['multiple_choices'] as $choices) {
            $mergedChoices = array_merge($mergedChoices, $choices['choices']);

            $view->vars['choices_for'][$choices['id']] = $choices['name'];
        }

        $view->vars['choice_names'] = array_keys($mergedChoices);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'multiple_choices',
        ]);

        $resolver->setAllowedTypes('multiple_choices', 'array');
    }

    public function getBlockPrefix()
    {
        return 'material_multiple_choice_table';
    }
}
