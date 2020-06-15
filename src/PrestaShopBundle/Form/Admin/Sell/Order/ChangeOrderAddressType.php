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

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class ChangeOrderAddressType extends AbstractType
{
    const SHIPPING_TYPE = 'shipping';
    const INVOICE_TYPE = 'invoice';

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $customerAddressProvider;

    /**
     * @param ConfigurableFormChoiceProviderInterface $customerAddressProvider
     */
    public function __construct(ConfigurableFormChoiceProviderInterface $customerAddressProvider)
    {
        $this->customerAddressProvider = $customerAddressProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('new_address_id', ChoiceType::class, [
                'choices' => $this->customerAddressProvider->getChoices($options),
            ])
            ->add('address_type', HiddenType::class, [
                    'constraints' => [
                        new Choice($this->getAvailableAddressTypes()),
                    ],
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired([
                'customer_id',
            ])
            ->setAllowedTypes('customer_id', 'int');
    }

    /**
     * @return array
     */
    public function getAvailableAddressTypes()
    {
        return [
            self::SHIPPING_TYPE,
            self::INVOICE_TYPE,
        ];
    }
}
