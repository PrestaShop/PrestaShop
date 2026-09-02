<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ApiClient\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\ApiClient\Query\GetApiClientForEditing;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\QueryResult\EditableApiClient;

interface GetApiClientForEditingHandlerInterface
{
    public function handle(GetApiClientForEditing $query): EditableApiClient;
}
