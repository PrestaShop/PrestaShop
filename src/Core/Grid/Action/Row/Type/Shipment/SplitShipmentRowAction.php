<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Shipment;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\AbstractRowAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SplitShipmentRowAction extends AbstractRowAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'split_shipment_row_action';
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(array $record): bool
    {
        $options = $this->getOptions();
        $itemsField = $options['items'];

        if ($this->shipmentIsPacked($record)) {
            return false;
        }

        // Show split action only if items > 1
        return !empty($record[$itemsField]) && (int) $record[$itemsField] > 1;
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
                'items',
            ])
            ->setDefaults([
                'total_shipments' => null,
            ])
            ->setAllowedTypes('shipment_id_field', 'string')
            ->setAllowedTypes('items', 'string')
            ->setAllowedTypes('total_shipments', ['string', 'null'])
            ->setAllowedTypes('order_id_field', 'string');
    }
}
