<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Shipment;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\AbstractRowAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MergeShipmentRowAction extends AbstractRowAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'merge_shipment_row_action';
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(array $record): bool
    {
        if ($this->shipmentIsPacked($record)) {
            return false;
        }

        if (!$this->orderHasMultipleUnfulfilledShipments($record['total_unfulfilled_shipments'] ?? 0)) {
            return false;
        }

        return true;
    }

    private function orderHasMultipleUnfulfilledShipments(int $unfulfilledShipments): bool
    {
        return $unfulfilledShipments > 1;
    }

    private function shipmentIsPacked(array $record): bool
    {
        return !empty($record['tracking_number']) && !empty($record['packed_at']);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired([
                'shipment_id_field',
                'order_id_field',
                'total_shipments',
            ])
            ->setDefaults([
                'items' => null,
            ])
            ->setAllowedTypes('shipment_id_field', 'string')
            ->setAllowedTypes('items', ['string', 'null'])
            ->setAllowedTypes('total_shipments', 'string')
            ->setAllowedTypes('order_id_field', 'string');
    }
}
