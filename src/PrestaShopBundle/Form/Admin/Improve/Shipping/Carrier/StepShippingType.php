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
use Symfony\Component\Translation\TranslatorInterface;

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
    private $shippingMethodChoices;

    /**
     * @var array
     */
    private $outOfRangeBehaviorChoices;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param array $taxChoices
     * @param array $shippingMethodChoices
     * @param array $outOfRangeBehaviorChoices
     * @param TranslatorInterface $translator
     */
    public function __construct(
        array $taxChoices,
        array $shippingMethodChoices,
        array $outOfRangeBehaviorChoices,
        TranslatorInterface $translator
    ) {
        $this->taxChoices = $taxChoices;
        $this->outOfRangeBehaviorChoices = $outOfRangeBehaviorChoices;
        $this->shippingMethodChoices = $shippingMethodChoices;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cost_handling', SwitchType::class, [
                'required' => false,
            ])
            ->add('is_free_shipping', SwitchType::class, [
                'required' => false,
            ])
            ->add('shipping_method', ChoiceType::class, [
                'placeholder' => false,
                'required' => false,
                'expanded' => true,
                'choices' => $this->shippingMethodChoices,
            ])
            ->add('tax', ChoiceType::class, [
                'placeholder' => false,
                'required' => false,
                'choices' => $this->getTaxChoices(),
            ])
            ->add('out_of_range', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => $this->outOfRangeBehaviorChoices,
            ])
            ->add('zone_ranges', ZoneRangeType::class)
        ;
    }

    /**
     * @return array
     */
    private function getTaxChoices(): array
    {
        $choices[$this->translator->trans('No tax', [], 'Admin.International.Help')] = 0;

        return array_merge($choices, $this->taxChoices);
    }
}
