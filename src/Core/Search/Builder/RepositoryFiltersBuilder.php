<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Builder;

use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * This builder is able to get the employee saved filter:
 *  - thanks to filterId if one has been specified (either in the config or by the Filters sub class)
 *  - thanks to controller/action matching from the request
 */
final class RepositoryFiltersBuilder extends AbstractRepositoryFiltersBuilder
{
    /**
     * {@inheritdoc}
     */
    public function buildFilters(?Filters $filters = null)
    {
        if (!$this->employeeProvider->getId() || !$this->shopId) {
            return $filters;
        }

        if (null !== $filters && !$filters->needsToBePersisted()) {
            return $filters;
        }

        $filterId = $this->getFilterId($filters);
        $parameters = $this->getParametersFromRepository($filterId);

        if (null !== $filters) {
            $filters->add($parameters);
        } else {
            $filters = new Filters($parameters, $filterId);
        }

        return $filters;
    }

    /**
     * @param string $filterId
     *
     * @return array
     */
    private function getParametersFromRepository($filterId)
    {
        if (empty($filterId) && (empty($this->controller) || empty($this->action))) {
            return [];
        }

        if (!empty($filterId)) {
            $adminFilter = $this->adminFilterRepository->findByEmployeeAndFilterId(
                $this->employeeProvider->getId(),
                $this->shopId,
                $filterId
            );
        } else {
            $adminFilter = $this->adminFilterRepository->findByEmployeeAndRouteParams(
                $this->employeeProvider->getId(),
                $this->shopId,
                $this->controller,
                $this->action
            );
        }

        if (!$adminFilter) {
            return [];
        }

        return json_decode($adminFilter->getFilter(), true);
    }
}
