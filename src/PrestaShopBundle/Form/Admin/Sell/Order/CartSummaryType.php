<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form type for cart summary block of order create page
 */
class CartSummaryType extends AbstractType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $orderStatesChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $paymentModulesChoiceProvider;

    /**
     * @param FormChoiceProviderInterface $orderStatesChoiceProvider
     * @param FormChoiceProviderInterface $paymentModulesChoiceProvider
     */
    public function __construct(
        FormChoiceProviderInterface $orderStatesChoiceProvider,
        FormChoiceProviderInterface $paymentModulesChoiceProvider
    ) {
        $this->orderStatesChoiceProvider = $orderStatesChoiceProvider;
        $this->paymentModulesChoiceProvider = $paymentModulesChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cart_id', HiddenType::class, [
                'required' => false,
            ])
            ->add('order_message', TextareaType::class, [
                'required' => false,
            ])
            ->add('payment_module', ChoiceType::class, [
                'choices' => $this->getPaymentModuleChoices(),
                'required' => false,
                'placeholder' => false,
            ])
            ->add('order_state', ChoiceType::class, [
                'choices' => $this->orderStatesChoiceProvider->getChoices(),
                'required' => false,
                'placeholder' => false,
            ]);
    }

    /**
     * Gets payment module choices
     *
     * @return array
     */
    private function getPaymentModuleChoices(): array
    {
        $choices = [];

        foreach ($this->paymentModulesChoiceProvider->getChoices() as $name => $displayName) {
            $choices[$displayName] = $name;
        }

        return $choices;
    }
}
