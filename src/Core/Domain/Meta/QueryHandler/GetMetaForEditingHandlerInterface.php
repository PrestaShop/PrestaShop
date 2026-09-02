<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Meta\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Meta\Query\GetMetaForEditing;
use PrestaShop\PrestaShop\Core\Domain\Meta\QueryResult\EditableMeta;

/**
 * Interface GetMetaForEditingHandlerInterface defines contract for GetMetaForEditingHandler.
 */
interface GetMetaForEditingHandlerInterface
{
    /**
     * Gets data related with meta entity.
     *
     * @param GetMetaForEditing $query
     *
     * @return EditableMeta
     */
    public function handle(GetMetaForEditing $query);
}
