<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Meta\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Meta\Query\GetPagesForLayoutCustomization;

/**
 * Interface GetMetaPagesHandlerInterface.
 */
interface GetPagesForLayoutCustomizationHandlerInterface
{
    /**
     * @param GetPagesForLayoutCustomization $query
     */
    public function handle(GetPagesForLayoutCustomization $query);
}
