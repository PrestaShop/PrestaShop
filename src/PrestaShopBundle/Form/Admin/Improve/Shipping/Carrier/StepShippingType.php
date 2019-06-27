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
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Defines form part for carrier create/edit action Shipping step
 */
class StepShippingType extends AbstractType
{
    /**
     * @var array
     */
    private $taxChoices;

    /**
     * @var array
     */
    private $outOfRangeBehaviorChoices;

    /**
     * @param array $taxChoices
     * @param array $outOfRangeBehaviorChoices
     */
    public function __construct(
        array $taxChoices,
        array $outOfRangeBehaviorChoices
    ) {
        $this->taxChoices = $taxChoices;
        $this->outOfRangeBehaviorChoices = $outOfRangeBehaviorChoices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cost_handling', SwitchType::class)
            ->add('is_free_shipping', SwitchType::class)
            ->add('billing_type', ChoiceType::class, [
                'expanded' => true,
                'choices' => [
                    'test1' => 'test1',
                    'test2' => 'test2',
                ],
            ])
            ->add('tax', ChoiceType::class, [
                'choices' => $this->taxChoices,
            ])
            ->add('out_of_range', ChoiceType::class, [
                'choices' => $this->outOfRangeBehaviorChoices,
            ])
            ->add('zone_ranges', ZoneRangeType::class)
        ;
    }
}
