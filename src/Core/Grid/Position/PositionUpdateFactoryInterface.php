<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position;

use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;

/**
 * Interface PositionUpdateFactoryInterface is used to interpret the provided
 * data array and transform it in a fully filled PositionUpdate object.
 */
interface PositionUpdateFactoryInterface
{
    /**
     * Transform the provided data into a PositionUpdate.
     *
     * @param array $data
     * @param PositionDefinition $positionDefinition
     *
     * @return PositionUpdate
     *
     * @throws PositionDataException
     */
    public function buildPositionUpdate(array $data, PositionDefinition $positionDefinition);
}
