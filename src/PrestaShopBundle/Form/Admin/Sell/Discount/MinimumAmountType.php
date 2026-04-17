<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShopBundle\Form\Admin\Type\CurrencyMoneyType;
use PrestaShopBundle\Form\Admin\Type\TaxInclusionChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\When;

class MinimumAmountType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', CurrencyMoneyType::class, [
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().getParent().get("children_selector").getData() === "%s"',
                            CartConditionsType::MINIMUM_AMOUNT,
                        ),
                        constraints: new Collection(
                            fields: [
                                'amount' => new GreaterThan(0),
                            ],
                            allowExtraFields: true,
                        ),
                    ),
                ],
            ])
            ->add('tax_included', TaxInclusionChoiceType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Discount/FormTheme/minimum_amount.html.twig',
            ])
        ;
    }
}
