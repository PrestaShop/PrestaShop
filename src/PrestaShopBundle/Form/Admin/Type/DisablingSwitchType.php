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
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This type is used by the DisablingExtension and automatically added on form fields which have
 * the disabling_switcher option enabled.
 *
 * @todo: this type doesn't seem to work on its own (e.g. when trying $builder->add('foo', DisablingSwitchType))
 */
class DisablingSwitchType extends SwitchType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            // The set default options from parent type
            ->setDefaults([
                'target_selector' => '',
                'disable_on_match' => true,
                'show_choices' => false,
                'label' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'ps-disabling-switch',
                ],
                'switch_event' => null,
            ])
            ->setAllowedTypes('disable_on_match', 'bool')
            ->setAllowedTypes('target_selector', 'string')
            ->setAllowedTypes('switch_event', ['string', 'null'])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['attr']['data-target-selector'] = $options['target_selector'];
        $view->vars['attr']['data-matching-value'] = '0';
        $view->vars['attr']['data-disable-on-match'] = (int) $options['disable_on_match'];

        // Optional event to trigger on switch
        if (!empty($options['switch_event'])) {
            $view->vars['attr']['data-switch-event'] = $options['switch_event'];
        }
    }

    public function getParent(): string
    {
        return SwitchType::class;
    }
}
