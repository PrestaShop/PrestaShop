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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Displays a switch (ON / OFF by default) for feature flags.
 */
class FeatureFlagSwitchType extends AbstractType
{
    public const TRANS_DOMAIN = 'Admin.Global';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'block_prefix' => 'feature_flag',
            'form_theme' => '@PrestaShop/Admin/Configure/AdvancedParameters/FeatureFlag/FormTheme/feature_flag_form.html.twig',
            'types' => [],
            'used_type' => null,
            'forced_by_env' => false,
        ]);
        $resolver->setAllowedTypes('types', 'array');
        $resolver->setAllowedTypes('used_type', ['null', 'string']);
        $resolver->setAllowedTypes('forced_by_env', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['types'] = $options['types'];
        $view->vars['used_type'] = $options['used_type'];
        $view->vars['forced_by_env'] = $options['forced_by_env'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return SwitchType::class;
    }
}
