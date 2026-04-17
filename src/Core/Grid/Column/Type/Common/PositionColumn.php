<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdater;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PositionColumn defines a position column used to sort elements in a grid,
 * it is associated to a special template, and works well with the PositionExtension
 * javascript extension and the GridPositionUpdater service.
 *
 * @see admin-dev/themes/new-theme/js/components/grid/extension/position-extension.js
 * @see GridPositionUpdater
 */
final class PositionColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'position';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'id_field',
                'position_field',
                'update_route',
            ])
            ->setDefaults([
                'sortable' => true,
                'update_method' => 'GET',
                'record_route_params' => [],
                'clickable' => true,
                'required_filter' => null,
                'display_offset' => 1,
            ])
            ->setAllowedTypes('id_field', 'string')
            ->setAllowedTypes('position_field', 'string')
            ->setAllowedTypes('update_route', 'string')
            ->setAllowedTypes('sortable', 'bool')
            ->setAllowedTypes('update_method', 'string')
            ->setAllowedTypes('record_route_params', ['array'])
            ->setAllowedTypes('clickable', 'bool')
            ->setAllowedTypes('required_filter', ['string', 'null'])
            ->setAllowedTypes('display_offset', ['integer'])
            ->setAllowedValues('update_method', ['GET', 'POST'])
        ;
    }
}
