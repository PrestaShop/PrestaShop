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
 * This form class generates the "Combine Compress Cache" form in Performance page.
 */
class CombineCompressCacheType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('smart_cache_css', SwitchType::class, [
                'label' => $this->trans('Smart cache for CSS', 'Admin.Advparameters.Feature'),
            ])
            ->add('smart_cache_js', SwitchType::class, [
                'label' => $this->trans('Smart cache for JavaScript', 'Admin.Advparameters.Feature'),
            ])
            ->add('apache_optimization', SwitchType::class, [
                'label' => $this->trans('Apache optimization', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('This will add directives to your .htaccess file, which should improve caching and compression.', 'Admin.Advparameters.Help'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'performance_ccc_block';
    }
}
