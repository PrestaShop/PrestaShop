<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\CustomerGroup;

use PrestaShopBundle\Form\Admin\Type\CategoryChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerGroupType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly bool $isShopFeatureEnabled,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'help' => $this->trans('Forbidden characters:', 'Admin.Notifications.Info') . ' 0-9!<>,;?=+()@#"{}_$%:',
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Name', 'Admin.Global'))]
                        ),
                    ]),
                ],
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 32,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                ['%limit%' => 32]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('reduction', NumberType::class, [
                'label' => $this->trans('Discount', 'Admin.Global'),
                'required' => false,
                'scale' => 2,
                'attr' => [
                    'placeholder' => '0',
                    'suffix' => '%',
                ],
                'help' => $this->trans(
                    'Automatically apply this value as a discount on all products for members of this customer group.',
                    'Admin.Shopparameters.Help'
                ),
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'notInRangeMessage' => $this->trans(
                            'The discount value is incorrect (must be a percentage).',
                            'Admin.Shopparameters.Notification'
                        ),
                    ]),
                ],
            ])
            ->add('price_display_method', ChoiceType::class, [
                'label' => $this->trans('Price display method', 'Admin.Shopparameters.Feature'),
                'required' => true,
                'choices' => [
                    $this->trans('Tax included', 'Admin.Global') => 0,
                    $this->trans('Tax excluded', 'Admin.Global') => 1,
                ],
                'help' => $this->trans(
                    'How prices are displayed in the order summary for this customer group.',
                    'Admin.Shopparameters.Help'
                ),
            ])
            ->add('show_prices', SwitchType::class, [
                'label' => $this->trans('Show prices', 'Admin.Shopparameters.Feature'),
                'required' => false,
                'help' => $this->trans('Customers in this group can view prices.', 'Admin.Shopparameters.Help'),
            ])
            ->add('category_picker', CategoryChoiceTreeType::class, [
                'label' => $this->trans('Select a category', 'Admin.Shopparameters.Feature'),
                'required' => false,
                'mapped' => false,
            ])
            ->add('category_reductions', CollectionType::class, [
                'label' => $this->trans('Category discount', 'Admin.Shopparameters.Feature'),
                'entry_type' => CategoryReductionEntryType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'attr' => ['class' => 'js-category-reductions-collection'],
            ])
            ->add('module_restrictions', CollectionType::class, [
                'label' => $this->trans('Authorized modules', 'Admin.Shopparameters.Feature'),
                'entry_type' => ModuleRestrictionEntryType::class,
                'allow_add' => false,
                'allow_delete' => false,
                'required' => false,
                'attr' => ['class' => 'js-module-restrictions-collection'],
            ])
        ;

        if ($this->isShopFeatureEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Store association', 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Store association', 'Admin.Global'))]
                        ),
                    ]),
                ],
            ]);
        }
    }
}
