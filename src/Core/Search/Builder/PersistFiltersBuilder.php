<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Builder;

use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * This builder does not modify the filters but instead saves them in database for
 * each Employee, thus it can then be found by the RepositoryFiltersBuilder.
 */
final class PersistFiltersBuilder extends AbstractRepositoryFiltersBuilder
{
    /**
     * @param Filters|null $filters
     *
     * @return Filters
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function buildFilters(?Filters $filters = null)
    {
        if (null === $filters || !$this->employeeProvider->getId() || !$this->shopId || !$filters->needsToBePersisted()) {
            return $filters;
        }

        $filterId = $this->getFilterId($filters);
        if (empty($filterId) && (empty($this->controller) || empty($this->action))) {
            return $filters;
        }

        $filtersToSave = $filters->all();
        unset($filtersToSave['offset']);

        if (!empty($filterId)) {
            $this->adminFilterRepository->createOrUpdateByEmployeeAndFilterId(
                $this->employeeProvider->getId(),
                $this->shopId,
                $filtersToSave,
                $filterId
            );
        } else {
            $this->adminFilterRepository->createOrUpdateByEmployeeAndRouteParams(
                $this->employeeProvider->getId(),
                $this->shopId,
                $filtersToSave,
                $this->controller,
                $this->action
            );
        }

        return $filters;
    }
}
