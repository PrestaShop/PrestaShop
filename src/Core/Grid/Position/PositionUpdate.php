<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
