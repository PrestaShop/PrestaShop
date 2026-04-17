<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Shipment;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\AvailableCarriersForShipmentChoiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class SplitShipmentType extends AbstractType
{
    public function __construct(
        private readonly AvailableCarriersForShipmentChoiceProvider $availableCarriersForShipmentChoiceProvider,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('products', CollectionType::class, [
            'entry_type' => ProductSplitType::class,
            'entry_options' => ['label' => false],
            'allow_add' => false,
            'mapped' => false,
            'data' => $options['data']['products'],
        ]);

        $builder->add('shipment_id', HiddenType::class);

        $selectedProducts = array_filter(
            $options['data']['products'],
            function ($product) {
                return !empty($product['selected']);
            }
        );

        $selectedProductsId = array_column($selectedProducts, 'quantity', 'product_id');

        $builder->add('carrier', ChoiceType::class, [
            'choices' => $this->getCarrierChoices($options, $selectedProductsId),
            'placeholder' => $this->translator->trans('Select a carrier', [], 'Admin.Orderscustomers.Feature'),
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'products' => [],
            'carrier' => 0,
        ]);
    }

    /**
     * @param array<int, string> $selectedProductsId
     */
    private function getCarrierChoices(array $options, array $selectedProductsId): array
    {
        if (count($selectedProductsId) > 0) {
            return $this->availableCarriersForShipmentChoiceProvider->getChoices([
                'shipment_id' => $options['data']['shipment_id'],
                'selectedProducts' => $selectedProductsId,
                'useCurrentCarrierId' => false,
            ]);
        }

        return [];
    }
}
