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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Shortcut;

use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This base type for shortcut type is used to override the block prefix (which is useful
 * for the form theme rendering) and offer additional options used for rendering as well.
 */
class ShortcutType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        if (empty($options['target_tab']) || empty($options['target_tab_name'])) {
            return;
        }

        $builder->add('tab_button', IconButtonType::class, [
            'label' => $options['target_tab_name'],
            'icon' => 'open_in_new',
            'attr' => [
                'class' => 'btn btn-link px-0',
                'href' => '#' . $options['target_tab'],
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'target_tab' => null,
            'target_tab_name' => null,
        ]);
        $resolver->setAllowedTypes('target_tab', ['string', 'null']);
        $resolver->setAllowedTypes('target_tab_name', ['string', 'null']);
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['target_tab'] = $options['target_tab'];
        $view->vars['target_tab_name'] = $options['target_tab_name'];
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'shortcut';
    }
}
