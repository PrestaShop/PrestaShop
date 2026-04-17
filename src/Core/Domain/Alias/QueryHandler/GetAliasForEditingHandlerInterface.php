<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Alias\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Alias\Query\GetAliasForEditing;
use PrestaShop\PrestaShop\Core\Domain\Alias\QueryResult\AliasForEditing;

/**
 * Interface GetAliasForEditingHandlerInterface defines contract for GetAliasForEditingHandler
 */
interface GetAliasForEditingHandlerInterface
{
    /**
     * @param GetAliasForEditing $query
     *
     * @return AliasForEditing
     */
    public function handle(GetAliasForEditing $query): AliasForEditing;
}
