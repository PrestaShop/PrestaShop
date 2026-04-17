<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Shipment;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\AbstractRowAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AdditionalShipmentRowAction extends AbstractRowAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'additional_shipment_row_action';
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
                'items' => 'items',
                'total_shipments' => 'total_shipments',
                'tracking_number' => 'tracking_number',
                'carrier' => 'carrier',
            ])
            ->setAllowedTypes('shipment_id_field', 'string')
            ->setAllowedTypes('items', 'string')
            ->setAllowedTypes('total_shipments', 'string')
            ->setAllowedTypes('order_id_field', 'string')
            ->setAllowedTypes('tracking_number', 'string')
            ->setAllowedTypes('carrier', 'string');
    }
}
