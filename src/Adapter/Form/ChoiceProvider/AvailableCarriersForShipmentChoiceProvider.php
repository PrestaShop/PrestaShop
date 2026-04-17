<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetAvailableCarriers;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\GetCarriersResult;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductQuantity;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AvailableCarriersForShipmentChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly ShipmentRepository $shipmentRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices(array $options): array
    {
        $options = $this->resolveOptions($options);
        $shipmentId = $options['shipment_id'];
        $useCurrentCarrierId = $options['useCurrentCarrierId'];
        $shipment = $this->shipmentRepository->findById($shipmentId);

        if ($shipment === null) {
            throw new ShipmentNotFoundException(
                $this->translator->trans(
                    'Could not find shipment with id "%id%".',
                    ['%id%' => $shipmentId],
                    'Admin.Shipment.Error'
                )
            );
        }

        $productQuantities = [];
        foreach ($options['selectedProducts'] as $productId => $quantity) {
            $productQuantities[] = new ProductQuantity(
                new ProductId($productId),
                (int) $quantity
            );
        }

        /** @var GetCarriersResult $carriers */
        $carriers = $this->commandBus->handle(new GetAvailableCarriers(
            $productQuantities,
            new AddressId($shipment->getAddressId()),
            $useCurrentCarrierId === true ? $shipment->getCarrierId() : null
        ));

        return FormChoiceFormatter::formatFormChoices(
            $carriers->getAvailableCarriersToArray(),
            'id',
            'name'
        );
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired([
            'shipment_id',
            'selectedProducts',
            'useCurrentCarrierId',
        ]);
        $resolver->setAllowedTypes('selectedProducts', 'array');

        return $resolver->resolve($options);
    }
}
