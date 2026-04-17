<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\EditableSqlRequest;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestForEditing;

interface GetSqlRequestForEditingHandlerInterface
{
    /**
     * @param GetSqlRequestForEditing $query
     *
     * @return EditableSqlRequest
     */
    public function handle(GetSqlRequestForEditing $query);
}
