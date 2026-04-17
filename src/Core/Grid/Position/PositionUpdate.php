<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position;

/**
 * Class PositionUpdate contains the modifications needed
 * to update the grid positions.
 */
final class PositionUpdate implements PositionUpdateInterface
{
    /**
     * @var PositionDefinitionInterface
     */
    private $positionDefinition;

    /**
     * @var PositionModificationCollectionInterface
     */
    private $positionModificationCollection;

    /**
     * @var string|null
     */
    private $parentId;

    /**
     * @param PositionModificationCollectionInterface $positionModificationCollection
     * @param PositionDefinitionInterface $positionDefinition
     * @param string|null $parentId
     */
    public function __construct(
        PositionModificationCollectionInterface $positionModificationCollection,
        PositionDefinitionInterface $positionDefinition,
        $parentId = null
    ) {
        $this->positionDefinition = $positionDefinition;
        $this->positionModificationCollection = $positionModificationCollection;
        $this->parentId = $parentId;
    }

    /**
     * {@inheritdoc}
     */
    public function getPositionDefinition()
    {
        return $this->positionDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function getPositionModificationCollection()
    {
        return $this->positionModificationCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentId()
    {
        return $this->parentId;
    }
}
