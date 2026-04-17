<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Delivery;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form class generates the "Options" form in Delivery slips page.
 */
class SlipOptionsType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'prefix',
                TranslatableType::class,
                [
                    'type' => TextType::class,
                    'label' => $this->trans('Delivery prefix', 'Admin.Orderscustomers.Feature'),
                    'label_help_box' => $this->trans('Prefix used for delivery slips.', 'Admin.Orderscustomers.Help'),
                ]
            )
            ->add(
                'number',
                NumberType::class,
                [
                    'label' => $this->trans('Delivery number', 'Admin.Orderscustomers.Feature'),
                    'label_help_box' => $this->trans(
                        'The next delivery slip will begin with this number and then increase with each additional slip.',
                        'Admin.Orderscustomers.Help'
                    ),
                ]
            )
            ->add(
                'enable_product_image',
                SwitchType::class,
                [
                    'label' => $this->trans('Enable product image', 'Admin.Orderscustomers.Feature'),
                    'label_help_box' => $this->trans(
                        'Add an image before the product name on delivery slips.',
                        'Admin.Orderscustomers.Help'
                    ),
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'order_delivery_slip_options';
    }
}
