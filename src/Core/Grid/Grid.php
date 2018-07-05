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

namespace PrestaShop\PrestaShop\Core\Grid;

use PrestaShop\PrestaShop\Core\Grid\DataProvider\GridDataInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\DefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class Grid is responsible for holding final Grid data.
 */
final class Grid implements GridInterface
{
    /**
     * @var DefinitionInterface
     */
    private $definition;

    /**
     * @var GridDataInterface
     */
    private $data;

    /**
     * @var SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * @param DefinitionInterface     $definition
     * @param GridDataInterface       $data
     * @param SearchCriteriaInterface $searchCriteria
     */
    public function __construct(
        DefinitionInterface $definition,
        GridDataInterface $data,
        SearchCriteriaInterface $searchCriteria = null
    ) {
        $this->definition = $definition;
        $this->data = $data;
        $this->searchCriteria = $searchCriteria;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        return $this->searchCriteria;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }
}
