<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
