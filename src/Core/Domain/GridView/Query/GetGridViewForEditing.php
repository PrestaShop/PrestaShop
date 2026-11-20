<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\Query;

use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewConstraintException;
use PrestaShop\PrestaShop\Core\Domain\GridView\ValueObject\GridViewId;

/**
 * Retrieves the data of a grid view owned by the current employee for the edit form.
 */
class GetGridViewForEditing
{
    private readonly GridViewId $gridViewId;

    /**
     * @param int $gridViewId
     *
     * @throws GridViewConstraintException
     */
    public function __construct(int $gridViewId)
    {
        $this->gridViewId = new GridViewId($gridViewId);
    }

    /**
     * @return GridViewId
     */
    public function getGridViewId(): GridViewId
    {
        return $this->gridViewId;
    }
}
