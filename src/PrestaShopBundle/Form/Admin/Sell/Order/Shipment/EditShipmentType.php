<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Shipment;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\AvailableCarriersForShipmentChoiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditShipmentType extends AbstractType
{
    public function __construct(
        private readonly AvailableCarriersForShipmentChoiceProvider $availableCarriersForShipmentChoiceProvider,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $builder->getData();

        $builder
            ->add('carrier', ChoiceType::class, [
                'choices' => $this->availableCarriersForShipmentChoiceProvider->getChoices([
                    'selectedProducts' => $data['selectedProducts'],
                    'shipment_id' => $data['shipment_id'],
                    'useCurrentCarrierId' => true,
                ]),
                'placeholder' => $this->translator->trans('Select a carrier', [], 'Admin.Orderscustomers.Feature'),
                'required' => true,
            ])
            ->add('current_order_carrier_id', HiddenType::class)
            ->add('tracking_number', TextType::class, [
                'required' => false,
            ]);
    }
}
