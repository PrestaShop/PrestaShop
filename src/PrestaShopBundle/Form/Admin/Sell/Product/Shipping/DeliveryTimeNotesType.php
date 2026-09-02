<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Shipping;

use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryTimeNotesType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('in_stock', TranslatableType::class, [
                'label' => $this->trans('Delivery time of in-stock products:', 'Admin.Catalog.Feature'),
                'type' => TextType::class,
                'required' => false,
                'options' => [
                    'attr' => [
                        'placeholder' => $this->trans('Delivered within 3-4 days', 'Admin.Catalog.Feature'),
                    ],
                ],
                'modify_all_shops' => true,
            ])
            ->add('out_of_stock', TranslatableType::class, [
                'locales' => $this->locales,
                'required' => false,
                'label' => $this->trans(
                    'Delivery time of out-of-stock products with allowed orders:',
                    'Admin.Catalog.Feature'
                ),
                'options' => [
                    'attr' => [
                        'placeholder' => $this->trans('Delivered within 5-7 days', 'Admin.Catalog.Feature'),
                    ],
                ],
                'modify_all_shops' => true,
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => false,
            'columns_number' => 2,
        ]);
    }
}
