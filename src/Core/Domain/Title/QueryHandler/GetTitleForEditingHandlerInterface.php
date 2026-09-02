<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Title\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Title\Query\GetTitleForEditing;
use PrestaShop\PrestaShop\Core\Domain\Title\QueryResult\EditableTitle;

/**
 * Defines contract for GetTitleForEditingHandler
 */
interface GetTitleForEditingHandlerInterface
{
    /**
     * @param GetTitleForEditing $query
     *
     * @return EditableTitle
     */
    public function handle(GetTitleForEditing $query): EditableTitle;
}
