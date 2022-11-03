<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position;

/**
 * Class PositionDefinition used to define a position relationship, see
 * PositionDefinitionInterface for more details.
 */
final class PositionDefinition implements PositionDefinitionInterface
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $idField;

    /**
     * @var string
     */
    private $positionField;

    /**
     * @var string|null
     */
    private $parentIdField;

    /**
     * @var int
     */
    private $firstPosition;

    /**
     * @param string $table
     * @param string $idField
     * @param string $positionField
     * @param string|null $parentIdField
     * @param int $firstPosition
     */
    public function __construct(
        $table,
        $idField,
        $positionField,
        $parentIdField = null,
        int $firstPosition = 0
    ) {
        $this->table = $table;
        $this->idField = $idField;
        $this->positionField = $positionField;
        $this->parentIdField = $parentIdField;
        $this->firstPosition = $firstPosition;
    }

    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * {@inheritdoc}
     */
    public function getPositionField()
    {
        return $this->positionField;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentIdField()
    {
        return $this->parentIdField;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstPosition(): int
    {
        return $this->firstPosition;
    }
}
