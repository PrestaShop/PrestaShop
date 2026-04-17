<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position;

/**
 * Class PositionModification contains the modification for a
 * designated row.
 */
final class PositionModification implements PositionModificationInterface
{
    /**
     * @var string|int
     */
    private $id;

    /**
     * @var int
     */
    private $oldPosition;

    /**
     * @var int
     */
    private $newPosition;

    /**
     * @param string|int $id
     * @param int|null $oldPosition
     * @param int $newPosition
     */
    public function __construct(
        $id,
        $oldPosition,
        $newPosition
    ) {
        $this->id = $id;
        $this->oldPosition = $oldPosition;
        $this->newPosition = $newPosition;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getOldPosition()
    {
        return $this->oldPosition;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewPosition()
    {
        return $this->newPosition;
    }
}
