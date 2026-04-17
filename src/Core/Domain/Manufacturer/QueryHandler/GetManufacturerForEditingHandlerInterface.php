<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\EditableManufacturer;

/**
 * Defines contract for GetManufacturerForEditingHandler
 */
interface GetManufacturerForEditingHandlerInterface
{
    /**
     * @param GetManufacturerForEditing $query
     *
     * @return EditableManufacturer
     */
    public function handle(GetManufacturerForEditing $query);
}
