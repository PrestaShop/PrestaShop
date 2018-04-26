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

namespace PrestaShop\PrestaShop\Core\Grid;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Exception\ColumnsNotDefinedException;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchParametersInterface;

/**
 * Class Grid is responsible for holding grid's data
 */
final class Grid
{
    /**
     * @var GridDefinitionInterface
     */
    private $definition;

    /**
     * @var GridData
     */
    private $data;

    /**
     * @var SearchParametersInterface
     */
    private $searchParameters;

    /**
     * @param GridDefinitionInterface $definition
     * @param GridData $data
     * @param SearchParametersInterface $searchParameters
     *
     * @throws ColumnsNotDefinedException When definition does not define any columns for grid
     */
    public function __construct(
        GridDefinitionInterface $definition,
        GridData $data,
        SearchParametersInterface $searchParameters
    ) {
        if (0 == count($definition->getColumns())) {
            throw new ColumnsNotDefinedException(
                sprintf('Grid "%s" definition does not contain any columns', $definition->getIdentifier())
            );
        }

        $this->definition = $definition;
        $this->searchParameters = $searchParameters;
        $this->data = $data;
    }

    /**
     * @return GridDefinitionInterface
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return SearchParametersInterface
     */
    public function getSearchParameters()
    {
        return $this->searchParameters;
    }

    /**
     * @return GridData
     */
    public function getData()
    {
        return $this->data;
    }
}
