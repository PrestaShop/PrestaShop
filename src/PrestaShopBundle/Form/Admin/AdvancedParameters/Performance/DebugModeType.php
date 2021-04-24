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

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form class generates the "Debug mode" form in Performance page.
 */
class DebugModeType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('disable_non_native_modules', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Disable non PrestaShop modules', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Enable or disable non PrestaShop Modules.', 'Admin.Advparameters.Feature'),
            ])
            ->add('disable_overrides', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Disable all overrides', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Enable or disable all classes and controllers overrides.', 'Admin.Advparameters.Feature'),
            ])
            ->add('debug_mode', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Debug mode', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Enable or disable debug mode.', 'Admin.Advparameters.Help'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'performance_debug_mode_block';
    }
}
