<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
    public function buildFilters(Filters $filters = null)
    {
        if (null === $filters || !$this->employeeProvider->getId() || !$this->shopId) {
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
