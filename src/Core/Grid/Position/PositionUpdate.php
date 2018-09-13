<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position;

/**
 * Class PositionUpdate.
 * @package PrestaShop\PrestaShop\Core\Grid\Position
 */
final class PositionUpdate implements PositionUpdateInterface
{
    /**
     * @var mixed
     */
    private $parentId;

    /**
     * @var PositionDefinitionInterface
     */
    private $positionDefinition;

    /**
     * @var RowUpdateCollectionInterface
     */
    private $rowUpdateCollection;

    /**
     * PositionUpdate constructor.
     * @param mixed $parentId
     * @param RowUpdateCollectionInterface $rowUpdateCollection
     * @param PositionDefinitionInterface $positionDefinition
     */
    public function __construct(
        $parentId,
        RowUpdateCollectionInterface $rowUpdateCollection,
        PositionDefinitionInterface $positionDefinition
    ) {
        $this->parentId = $parentId;
        $this->positionDefinition = $positionDefinition;
        $this->rowUpdateCollection = $rowUpdateCollection;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @inheritDoc
     */
    public function getPositionDefinition()
    {
        return $this->positionDefinition;
    }

    /**
     * @inheritDoc
     */
    public function getRowUpdateCollection()
    {
        return $this->rowUpdateCollection;
    }
}
