<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Shipment;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class ProductSplitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('selected', CheckboxType::class, [
                'required' => false,
                'label' => '',
            ])
            ->add('product_image_path', HiddenType::class)
            ->add('order_detail_id', HiddenType::class)
            ->add('product_name', HiddenType::class)
            ->add('product_reference', HiddenType::class)
            ->add('quantity', HiddenType::class);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            $default = array_key_exists('selected_quantity', $data)
                ? (int) $data['selected_quantity']
                : 1;

            $form->add('selected_quantity', IntegerType::class, [
                'empty_data' => 1,
                'data' => $default,
                'attr' => [
                    'min' => 1,
                    'max' => $data['quantity'],
                ],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => $data['quantity'],
                    ]),
                ],
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
