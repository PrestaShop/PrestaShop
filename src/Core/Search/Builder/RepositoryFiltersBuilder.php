<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Search\Builder;


use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShopBundle\Entity\AdminFilter;
use PrestaShopBundle\Entity\Repository\AdminFilterRepository;

class RepositoryFiltersBuilder extends AbstractFiltersBuilder
{
    /** @var AdminFilterRepository */
    private $adminFilterRepository;

    /** @var int */
    private $employeeId;

    /** @var int */
    private $shopId;

    /** @var string */
    private $controller;

    /** @var string */
    private $action;

    /**
     * @param AdminFilterRepository $adminFilterRepository
     */
    public function __construct(AdminFilterRepository $adminFilterRepository)
    {
        $this->adminFilterRepository = $adminFilterRepository;
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config)
    {
        $this->employeeId = isset($config['employee_id']) ? $config['employee_id'] : null;
        $this->shopId = isset($config['shop_id']) ? $config['shop_id'] : null;
        $this->controller = isset($config['controller']) ? $config['controller'] : '';
        $this->action = isset($config['action']) ? $config['action'] : '';

        return parent::setConfig($config);
    }

    /**
     * @inheritDoc
     */
    public function buildFilters(Filters $filters = null)
    {
        if (null === $this->employeeId || null === $this->shopId) {
            return $filters;
        }

        $parameters = $this->getParametersFromRepository();

        if (null !== $filters) {
            $filters->add($parameters);
        } else {
            $filters = new Filters($parameters, $this->filtersUuid);
        }

        return $filters;
    }

    /**
     * @return array
     */
    private function getParametersFromRepository()
    {
        if (empty($this->filtersUuid) && (empty($this->controller) || empty($this->action))) {
            return [];
        }

        if (!empty($this->filtersUuid)) {
            /** @var AdminFilter $adminFilter */
            $adminFilter = $this->adminFilterRepository->findByEmployeeAndUuid(
                $this->employeeId,
                $this->shopId,
                $this->filtersUuid
            );
        } else {
            /** @var AdminFilter $adminFilter */
            $adminFilter = $this->adminFilterRepository->findByEmployeeAndRouteParams(
                $this->employeeId,
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
