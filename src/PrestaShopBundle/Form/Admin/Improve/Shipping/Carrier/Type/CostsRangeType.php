<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier\Type;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\PositiveOrZero;
use PrestaShopBundle\Form\Admin\Type\MoneyWithSuffixType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CostsRangeType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('range', TextPreviewType::class, [
                'label' => $this->trans('Range', 'Admin.Shipping.Feature'),
            ])
            ->add('from', HiddenType::class, [
                'constraints' => [
                    new PositiveOrZero([
                        'message' => 'The value must be a positive number.',
                    ]),
                ],
            ])
            ->add('to', HiddenType::class, [
                'constraints' => [
                    new PositiveOrZero([
                        'message' => 'The value must be a positive number.',
                    ]),
                ],
            ])
            ->add('price', MoneyWithSuffixType::class, [
                'label' => $this->trans('Price (VAT excl.)', 'Admin.Shipping.Feature'),
                'empty_data' => '0.0', // string instead number needed for DecimalNumber.php validation
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'form_theme' => '@PrestaShop/Admin/Improve/Shipping/Carriers/FormTheme/costs-range.html.twig',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'carrier_ranges_costs_zone_range';
    }
}
