<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\GridView\Query\GetGridViewForEditing;
use PrestaShop\PrestaShop\Core\Domain\GridView\QueryResult\EditableGridView;

interface GetGridViewForEditingHandlerInterface
{
    /**
     * @param GetGridViewForEditing $query
     *
     * @return EditableGridView
     */
    public function handle(GetGridViewForEditing $query): EditableGridView;
}
