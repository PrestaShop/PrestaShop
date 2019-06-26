<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Defines form part for carrier shipping locations and costs step
 */
class CarrierStepShippingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $zones = ['zone1'];
        $builder
            ->add('cost_handling', SwitchType::class)
            ->add('is_free_shipping', SwitchType::class)
            ->add('billing_type', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'test1' => 'test1',
                    'test2' => 'test2',
                ],
            ])
            ->add('tax', ChoiceType::class, [
                'choices' => [],
            ])
            ->add('out_of_range', ChoiceType::class, [
                'choices' => [],
            ])
            ->add('zone_checks', CollectionType::class, [
                'entry_type' => ZoneCheckType::class,
                'data' => [
                    $zones,
                ],
            ])
            ->add('zone_range_inputs', CollectionType::class, [
                'entry_type' => ZoneRangeInputType::class,
                'data' => [
                    $zones,
                ],
            ])
        ;
    }
}
