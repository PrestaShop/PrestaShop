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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Generic form type for filter link groups.
 * This creates a hidden field that can be controlled by a FilterLinkGroup component.
 */
class FilterLinkFilterType extends AbstractType
{
    public function getParent(): string
    {
        return HiddenType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
        $view->vars['filter_options'] = $options['filter_options'] ?? [];
        $view->vars['filter_field_selector'] = $options['filter_field_selector'] ?? null;
        $view->vars['default_value'] = $options['default_value'] ?? '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'required' => false,
            'filter_options' => [],
            'filter_field_selector' => null,
            'default_value' => '',
            'attr' => [
                'class' => 'js-filter-link-field',
            ],
            'row_attr' => [
                'class' => 'd-none',
            ],
        ]);

        $resolver->setDefined([
            'filter_field_name',
        ]);

        $resolver->setAllowedTypes('filter_field_name', 'string');
        $resolver->setAllowedTypes('filter_options', 'array');
        $resolver->setAllowedTypes('filter_field_selector', ['string', 'null']);
        $resolver->setAllowedTypes('default_value', 'string');
    }

    public function getBlockPrefix(): string
    {
        return 'filter_link';
    }
}
