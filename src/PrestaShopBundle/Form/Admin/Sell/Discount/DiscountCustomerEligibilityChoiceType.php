<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
                'disabling_switch' => false,
                'exclude_guests' => true,
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
