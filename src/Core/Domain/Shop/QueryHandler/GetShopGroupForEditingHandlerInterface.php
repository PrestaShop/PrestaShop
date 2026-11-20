<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\EditableShopGroup;

interface GetShopGroupForEditingHandlerInterface
{
    public function handle(GetShopGroupForEditing $query): EditableShopGroup;
}
