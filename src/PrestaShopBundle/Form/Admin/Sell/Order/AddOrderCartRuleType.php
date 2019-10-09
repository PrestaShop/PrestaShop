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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddOrderCartRuleType extends AbstractType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $orderDiscountTypeChoiceProvider;

    /**
     * @param FormChoiceProviderInterface $orderDiscountTypeChoiceProvider
     */
    public function __construct(FormChoiceProviderInterface $orderDiscountTypeChoiceProvider)
    {
        $this->orderDiscountTypeChoiceProvider = $orderDiscountTypeChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $optional): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('type', ChoiceType::class, [
                'choices' => $this->orderDiscountTypeChoiceProvider->getChoices(),
            ])
            ->add('value', TextType::class)
        ;
    }
}
