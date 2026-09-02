<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Tag\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Tag\Query\GetTagForEditing;
use PrestaShop\PrestaShop\Core\Domain\Tag\QueryResult\EditableTag;

interface GetTagForEditingHandlerInterface
{
    public function handle(GetTagForEditing $query): EditableTag;
}
