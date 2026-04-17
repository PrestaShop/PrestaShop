<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Zone\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Zone\Query\GetZoneForEditing;
use PrestaShop\PrestaShop\Core\Domain\Zone\QueryResult\EditableZone;

/**
 * Defines contract for GetZoneForEditingHandler
 */
interface GetZoneForEditingHandlerInterface
{
    /**
     * @param GetZoneForEditing $query
     *
     * @return EditableZone
     */
    public function handle(GetZoneForEditing $query): EditableZone;
}
