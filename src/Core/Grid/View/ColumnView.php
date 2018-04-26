<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Core\Grid\View;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;

/**
 * Class ColumnView holds final data for single column that is passed to template for rendering
 */
final class ColumnView
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isSortable;

    /**
     * @var bool
     */
    private $isFilterable;

    /**
     * @param string $identifier
     * @param string $name
     * @param bool   $isSortable
     * @param bool   $isFilterable
     */
    public function __construct($identifier, $name, $isSortable, $isFilterable)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->isSortable = $isSortable;
        $this->isFilterable = $isFilterable;
    }

    /**
     * Create column view instance from Column
     *
     * @param ColumnInterface $column
     *
     * @return ColumnView
     */
    public static function fromColumn(ColumnInterface $column)
    {
        $view = new self(
            $column->getIdentifier(),
            $column->getName(),
            $column->isSortable(),
            $column->isFilterable()
        );

        return $view;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->isSortable;
    }

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return $this->isFilterable;
    }
}
