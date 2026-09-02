<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\EditableCarrier;

/**
 * Describes get carrier handler.
 */
interface GetCarrierForEditingHandlerInterface
{
    /**
     * @param GetCarrierForEditing $query
     *
     * @return EditableCarrier
     */
    public function handle(GetCarrierForEditing $query): EditableCarrier;
}
