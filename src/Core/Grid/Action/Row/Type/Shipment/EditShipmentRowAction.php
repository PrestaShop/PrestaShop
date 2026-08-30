<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Shipment;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\AbstractRowAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditShipmentRowAction extends AbstractRowAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'edit_shipment_row_action';
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(array $record): bool
    {
        if ($this->shipmentIsPacked($record)) {
            return false;
        }

        return true;
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
                'tracking_number',
                'carrier',
            ])
            ->setAllowedTypes('shipment_id_field', 'string')
            ->setAllowedTypes('order_id_field', 'string')
            ->setAllowedTypes('tracking_number', 'string')
            ->setAllowedTypes('carrier', 'string');
    }
}
