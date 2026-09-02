<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;

/**
 * Class DisableCategoriesCommand disables provided categories.
 */
class BulkDisableCategoriesCommand extends BulkUpdateCategoriesStatusCommand
{
    /**
     * @param int[] $categoryIds
     *
     * @throws CategoryConstraintException
     */
    public function __construct(array $categoryIds)
    {
        parent::__construct($categoryIds, false);
    }
}
