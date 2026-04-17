<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShopBundle\Form\Admin\Type\ToggleChildrenChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\When;

class CartConditionsType extends TranslatorAwareType
{
    public const NONE = 'none';
    public const MINIMUM_AMOUNT = 'minimum_amount';
    public const MINIMUM_PRODUCT_QUANTITY = 'minimum_product_quantity';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add(self::NONE, HiddenType::class, [
                'label' => $this->trans('None', 'Admin.Catalog.Feature'),
            ])
            ->add(self::MINIMUM_AMOUNT, MinimumAmountType::class, [
                'label' => $this->trans('Minimum purchase amount', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add(self::MINIMUM_PRODUCT_QUANTITY, IntegerType::class, [
                'label' => $this->trans('Minimum product quantity', 'Admin.Catalog.Feature'),
                'required' => false,
                'default_empty_data' => 0,
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().get("children_selector").getData() === "%s"',
                            self::MINIMUM_PRODUCT_QUANTITY
                        ),
                        constraints: [
                            new GreaterThan(0),
                        ],
                    ),
                ],
            ])
        ;
    }

    public function getParent()
    {
        return ToggleChildrenChoiceType::class;
    }
}
