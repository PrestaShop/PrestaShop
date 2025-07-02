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

namespace PrestaShopBundle\Form\Admin\Sell\Order\Shipment;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SplitShipmentType extends AbstractType
{
    private ConfigurableFormChoiceProviderInterface $carrierForOrderChoiceProvider;

    public function __construct(ConfigurableFormChoiceProviderInterface $carrierForOrderChoiceProvider)
    {
        $this->carrierForOrderChoiceProvider = $carrierForOrderChoiceProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('products', CollectionType::class, [
            'entry_type' => ProductSplitType::class,
            'entry_options' => ['label' => false],
            'allow_add' => false,
            'mapped' => false,
            'data' => $options['data']['products'],
        ]);

        $builder->add('shipment_id', HiddenType::class);

        $builder->add('carrier', ChoiceType::class, [
            'choices' => $this->carrierForOrderChoiceProvider->getChoices([
                'order_id' => $options['order_id'],
            ]),
            'autocomplete' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'products' => [],
            'order_id' => null,
        ]);
    }
}
