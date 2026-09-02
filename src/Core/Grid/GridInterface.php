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
 * Interface GridInterface defines contract for grid.
 */
interface GridInterface
{
    /**
     * Get grid definition.
     *
     * @return GridDefinitionInterface
     */
    public function getDefinition();

    /**
     * Get grid data.
     *
     * @return GridDataInterface
     */
    public function getData();

    /**
     * Get grid data search criteria.
     *
     * @return SearchCriteriaInterface
     */
    public function getSearchCriteria();

    /**
     * Get grid filter form.
     *
     * @return FormInterface
     */
    public function getFilterForm();
}
