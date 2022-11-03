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

namespace PrestaShop\PrestaShop\Core\Grid;

use PrestaShop\PrestaShop\Core\Grid\Data\GridDataInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class Grid is responsible for holding final Grid data.
 */
final class Grid implements GridInterface
{
    /**
     * @var GridDefinitionInterface
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
     * @var FormInterface
     */
    private $filtersForm;

    /**
     * @param GridDefinitionInterface $definition
     * @param GridDataInterface $data
     * @param SearchCriteriaInterface $searchCriteria
     * @param FormInterface $filtersForm
     */
    public function __construct(
        GridDefinitionInterface $definition,
        GridDataInterface $data,
        SearchCriteriaInterface $searchCriteria,
        FormInterface $filtersForm
    ) {
        $this->definition = $definition;
        $this->data = $data;
        $this->searchCriteria = $searchCriteria;
        $this->filtersForm = $filtersForm;
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

    /**
     * {@inheritdoc}
     */
    public function getFilterForm()
    {
        return $this->filtersForm;
    }
}
