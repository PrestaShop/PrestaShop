<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Shipment;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class MergeShipmentType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $datas = $builder->getData();

        foreach ($datas['products'] as $product) {
            $builder->add('product_' . $product['order_detail_id'], CheckboxType::class, [
                'label' => $product['order_detail_id'],
                'required' => false,
                'mapped' => false,
            ]);

            $builder->add('quantity_' . $product['order_detail_id'], IntegerType::class, [
                'label' => false,
                'data' => $product['quantity'],
                'attr' => [
                    'min' => 1,
                    'max' => $product['quantity'],
                ],
                'mapped' => false,
            ]);
        }

        $builder->add('merge_to_shipment', ChoiceType::class, [
            'label' => $this->translator->trans('Select shipment to merge to', [], 'Admin.Orderscustomers.Feature'),
            'choices' => $datas['shipments'],
            'choice_label' => function ($shipment) {
                return $this->translator->trans(
                    'Shipment %shipment_id% - carrier %carrier_name%',
                    [
                        '%shipment_id%' => $shipment->getId(),
                        '%carrier_name%' => $shipment->getCarrierSummary()->getName(),
                    ],
                    'Admin.Orderscustomers.Feature'
                );
            },
            'choice_attr' => function ($shipment) {
                if (!empty($shipment->getTrackingNumber())) {
                    return ['disabled' => 'disabled'];
                }

                return [];
            },
            'choice_value' => fn ($shipment) => $shipment ? (string) $shipment->getId() : '',
            'placeholder' => $this->translator->trans('Select shipment', [], 'Admin.Orderscustomers.Feature'),
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'products' => [],
            'shipments' => [],
        ]);
    }
}
