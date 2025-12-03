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

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CustomerSearchType;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\ToggleChildrenChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\When;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountCustomerEligibilityChoiceType extends TranslatorAwareType
{
    public const ALL_CUSTOMERS = 'all_customers';
    public const CUSTOMER_GROUPS = 'customer_groups';
    public const SINGLE_CUSTOMER = 'single_customer';

    private FormChoiceProviderInterface $groupByIdChoiceProvider;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $groupByIdChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->groupByIdChoiceProvider = $groupByIdChoiceProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::ALL_CUSTOMERS, HiddenType::class, [
                'label' => $this->trans('All customers', 'Admin.Catalog.Feature'),
            ])
            ->add(self::CUSTOMER_GROUPS, MaterialChoiceTableType::class, [
                'label' => $this->trans('Customer groups', 'Admin.Catalog.Feature'),
                'required' => false,
                'choices' => $this->groupByIdChoiceProvider->getChoices(),
                'display_total_items' => true,
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().get("children_selector").getData() === "%s"',
                            self::CUSTOMER_GROUPS,
                        ),
                        constraints: [
                            new Count([
                                'min' => 1,
                                'minMessage' => $this->trans(
                                    'Please select at least one group.',
                                    'Admin.Catalog.Notification'
                                ),
                            ]),
                        ],
                    ),
                ],
            ])
            ->add(self::SINGLE_CUSTOMER, CustomerSearchType::class, [
                'label' => $this->trans('Single customer', 'Admin.Catalog.Feature'),
                'layout' => EntitySearchInputType::LIST_LAYOUT,
                'required' => false,
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().get("children_selector").getData() === "%s"',
                            self::SINGLE_CUSTOMER,
                        ),
                        constraints: [
                            new NotBlank([
                                'message' => $this->trans(
                                    'You must select a customer when using the "Single customer" option.',
                                    'Admin.Catalog.Notification'
                                ),
                            ]),
                        ],
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
        ]);
    }

    public function getParent()
    {
        return ToggleChildrenChoiceType::class;
    }
}
