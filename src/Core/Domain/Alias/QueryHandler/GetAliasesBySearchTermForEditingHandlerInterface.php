<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Alias\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Alias\Query\GetAliasesBySearchTermForEditing;
use PrestaShop\PrestaShop\Core\Domain\Alias\QueryResult\AliasForEditing;

/**
 * Interface defines contract for GetAliasesBySearchTermForEditingHandler
 */
interface GetAliasesBySearchTermForEditingHandlerInterface
{
    /**
     * @param GetAliasesBySearchTermForEditing $query
     *
     * @return AliasForEditing
     */
    public function handle(GetAliasesBySearchTermForEditing $query): AliasForEditing;
}
