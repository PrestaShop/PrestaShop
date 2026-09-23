<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Query\GetVirtualProductFileForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryResult\VirtualProductFileForEditing;

/**
 * Handles @see GetVirtualProductFileForEditing query
 */
interface GetVirtualProductFileForEditingHandlerInterface
{
    /**
     * @throws VirtualProductFileNotFoundException
     */
    public function handle(GetVirtualProductFileForEditing $query): VirtualProductFileForEditing;
}
