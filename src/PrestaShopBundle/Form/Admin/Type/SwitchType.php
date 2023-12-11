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

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Displays a switch (ON / OFF by default).
 * 
 * @link https://devdocs.prestashop-project.org/8/development/components/form/types-reference/switch/
 */
class SwitchType extends AbstractType
{
    public const TRANS_DOMAIN = 'Admin.Global';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'No' => false,
                'Yes' => true,
            ],
            'show_choices' => true,
            // Force label and switch to be displayed on the same line (mainly useful for base ui kit)
            'inline_switch' => false,
            'multiple' => false,
            'expanded' => false,
            'disabled' => false,
            'choice_translation_domain' => self::TRANS_DOMAIN,
        ]);
        $resolver->setAllowedTypes('disabled', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (true === $options['disabled']) {
            $view->vars['disabled'] = true;
        }
        $view->vars['attr']['class'] = 'ps-switch';
        if (isset($options['attr']['class'])) {
            $view->vars['attr']['class'] .= ' ' . $options['attr']['class'];
        }
        $view->vars['show_choices'] = $options['show_choices'];

        // Add a class when inline mode is enabled
        if ($options['inline_switch']) {
            $rowAttributes = $options['row_attr'] ?? [];
            if (!empty($rowAttributes['class'])) {
                $rowAttributes['class'] .= ' inline-switch-widget';
            } else {
                $rowAttributes['class'] = 'inline-switch-widget';
            }
            $view->vars['row_attr'] = $rowAttributes;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
