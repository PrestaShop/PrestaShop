<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DiscountAmount;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShopBundle\Form\Admin\Type\CurrencyMoneyType;
use PrestaShopBundle\Form\Admin\Type\TaxInclusionChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountAmountType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', CurrencyMoneyType::class)
            ->add('type', ChoiceType::class, [
                'placeholder' => false,
                'required' => false,
                'choices' => [
                    $this->trans('Amount', 'Admin.Global') => DiscountSettings::AMOUNT,
                    $this->trans('Percentage', 'Admin.Global') => DiscountSettings::PERCENT,
                ],
            ])
            ->add('include_tax', TaxInclusionChoiceType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Discount/FormTheme/discount_value.html.twig',
            'constraints' => [
                new DiscountAmount([
                    'invalidPercentageValueMessage' => $this->trans(
                        'Reduction value "%value%" is invalid. It must be greater than 0 and maximum %max%.',
                        'Admin.Notifications.Error',
                        ['%max%' => Reduction::MAX_ALLOWED_PERCENTAGE . '%']
                    ),
                    'invalidAmountValueMessage' => $this->trans(
                        'Reduction value "%value%" is invalid. It must be greater than 0.',
                        'Admin.Notifications.Error'
                    ),
                ]),
            ],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'discount_amount';
    }
}
