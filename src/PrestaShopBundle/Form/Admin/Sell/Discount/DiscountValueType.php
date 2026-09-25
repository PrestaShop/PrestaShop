<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;

class DiscountValueType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reduction', DiscountAmountType::class, [
                'label' => $this->trans('Discount value', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('You can choose a minimum amount for the cart either with or without the taxes.', 'Admin.Catalog.Help'),
                'help' => $this->trans('Shipping fees are excluded.', 'Admin.Catalog.Help'),
                'required' => false,
                'row_attr' => [
                    'class' => 'discount-container',
                ],
            ])
            // Only an amount discount can leave a remainder, so this has no effect on a percentage
            // one - the same as in the previous discount form, which also stored it either way.
            ->add('allow_partial_use', SwitchType::class, [
                'label' => $this->trans('Partial use', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('If you do not allow partial use, the voucher value will be lowered to the total order amount. If you allow partial use, however, a new voucher will be created with the remainder.', 'Admin.Catalog.Help'),
                'required' => false,
            ])
        ;
    }

    public function getParent()
    {
        return CardType::class;
    }
}
