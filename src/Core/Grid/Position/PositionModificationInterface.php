<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position;

/**
 * Interface PositionModificationInterface contains the modification for a
 * designated row.
 */
interface PositionModificationInterface
{
    /**
     * The row id allowing to match it.
     *
     * @return string|int
     */
    public function getId();

    /**
     * The former row position.
     *
     * @return int|null
     *
     * @deprecated Since 9.0 because this field is never used and should be removed.
     * *
     */
    public function getOldPosition();

    /**
     * The new row position.
     *
     * @return int
     */
    public function getNewPosition();
}
