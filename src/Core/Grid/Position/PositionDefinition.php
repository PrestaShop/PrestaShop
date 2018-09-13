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
 * Class PositionDefinition.
 * @package PrestaShop\PrestaShop\Core\Grid\Position
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
     * PositionDefinition constructor.
     * @param string $table
     * @param string $idField
     * @param string $positionField
     * @param string|null $parentIdField
     */
    public function __construct(
        $table,
        $idField,
        $positionField,
        $parentIdField = null
    ) {
        $this->table = $table;
        $this->idField = $idField;
        $this->positionField = $positionField;
        $this->parentIdField = $parentIdField;
    }

    /**
     * @inheritDoc
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @inheritDoc
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * @inheritDoc
     */
    public function getPositionField()
    {
        return $this->positionField;
    }

    /**
     * @inheritDoc
     */
    public function getParentIdField()
    {
        return $this->parentIdField;
    }
}
