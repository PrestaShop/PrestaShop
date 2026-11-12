<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopUrlForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\EditableShopUrl;

interface GetShopUrlForEditingHandlerInterface
{
    public function handle(GetShopUrlForEditing $query): EditableShopUrl;
}
