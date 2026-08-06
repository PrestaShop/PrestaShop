<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\View;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Grid\Exception\GridViewException;
use PrestaShopBundle\Entity\AdminGridConfiguration;
use PrestaShopBundle\Entity\AdminGridView;
use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use PrestaShopBundle\Entity\Repository\AdminGridConfigurationRepository;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Routing\RouterInterface;

class GridViewHandler
{
    public const MAX_VIEWS_PER_CONFIGURATION = 30;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AdminGridConfigurationRepository $configurationRepository
     * @param AdminFilterRepository $adminFilterRepository
     * @param EmployeeContext $employeeContext
     * @param ShopContext $shopContext
     * @param RouterInterface $router
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminGridConfigurationRepository $configurationRepository,
        private readonly AdminFilterRepository $adminFilterRepository,
        private readonly EmployeeContext $employeeContext,
        private readonly ShopContext $shopContext,
        private readonly RouterInterface $router,
        private readonly CacheItemPoolInterface $cache,
    ) {
    }

    /**
     * @param array{name: string, shared: bool|null, controller_route: string, filter_id: string, grid_state?: string|null, dynamic_date_rules?: array|null} $data
     *
     * @throws GridViewException
     */
    public function createFromPersistedFilters(string $gridId, array $data): AdminGridView
    {
        $employeeId = $this->getEmployeeId();
        $shopId = $this->shopContext->getId();
        $this->assertRouteExists($data['controller_route']);
        $filterId = $this->assertValidFilterId($gridId, $data['filter_id']);

        $adminFilter = $this->adminFilterRepository->findByEmployeeAndFilterId($employeeId, $shopId, $filterId);
        $searchCriteria = null !== $adminFilter ? (json_decode($adminFilter->getFilter(), true) ?: []) : [];
        unset($searchCriteria['offset']);

        $configuration = $this->configurationRepository->findOrCreateForEmployee(
            $employeeId,
            $shopId,
            $gridId,
            $filterId,
            $data['controller_route']
        );
        $this->assertViewLimitIsNotReached($configuration);

        $gridView = new AdminGridView();
        $gridView
            ->setName($data['name'])
            ->setFilterId($filterId)
            ->setShared((bool) ($data['shared'] ?? false))
            ->setFilters((string) json_encode($searchCriteria))
            ->setDynamicDateRules($this->sanitizeDateRules($data['dynamic_date_rules'] ?? [], $searchCriteria))
            ->setGridState($this->sanitizeGridState($data['grid_state'] ?? null))
        ;
        $configuration->addView($gridView);

        $this->entityManager->persist($gridView);
        $this->entityManager->flush();

        return $gridView;
    }

    /**
     * @param array{name: string, shared: bool|null, dynamic_date_rules?: array|null} $data
     */
    public function update(AdminGridView $gridView, array $data): void
    {
        $searchCriteria = json_decode($gridView->getFilters(), true) ?: [];

        $gridView
            ->setName($data['name'])
            ->setShared((bool) ($data['shared'] ?? false))
            ->setDynamicDateRules($this->sanitizeDateRules($data['dynamic_date_rules'] ?? [], $searchCriteria))
        ;

        $this->entityManager->flush();
        $this->invalidateCountCache($gridView);
    }

    /**
     * @throws GridViewException
     */
    public function duplicate(AdminGridView $source, string $copyName): AdminGridView
    {
        $sourceConfiguration = $source->getGridConfiguration();
        $configuration = $this->configurationRepository->findOrCreateForEmployee(
            $this->getEmployeeId(),
            $sourceConfiguration->getShopId(),
            $sourceConfiguration->getGridId(),
            $sourceConfiguration->getFilterId(),
            $sourceConfiguration->getControllerRoute()
        );
        $this->assertViewLimitIsNotReached($configuration);

        $copy = new AdminGridView();
        $copy
            ->setName(mb_substr($copyName, 0, 255))
            ->setFilterId($source->getFilterId())
            ->setShared(false)
            ->setFilters($source->getFilters())
            ->setDynamicDateRules($source->getDynamicDateRules())
            ->setGridState($source->getGridState())
        ;
        $configuration->addView($copy);

        $this->entityManager->persist($copy);
        $this->entityManager->flush();

        return $copy;
    }

    /**
     * @param AdminGridView $gridView
     *
     * @return void
     */
    public function delete(AdminGridView $gridView): void
    {
        $gridViewId = $gridView->getId();
        $this->entityManager->remove($gridView);
        $this->entityManager->flush();
        $this->cache->deleteItem(GridViewCounter::CACHE_KEY_PREFIX . $gridViewId);
    }

    /**
     * @param AdminGridView $gridView
     *
     * @return void
     */
    private function invalidateCountCache(AdminGridView $gridView): void
    {
        $this->cache->deleteItem(GridViewCounter::CACHE_KEY_PREFIX . $gridView->getId());
    }

    /**
     * @param string $gridId
     * @param string $filterId
     *
     * @return string
     *
     * @throws GridViewException
     */
    private function assertValidFilterId(string $gridId, string $filterId): string
    {
        if ($filterId !== $gridId && !str_starts_with($filterId, $gridId . '_')) {
            throw new GridViewException(
                sprintf('Filter id "%s" does not belong to grid "%s"', $filterId, $gridId),
                GridViewException::INVALID_FILTER_ID
            );
        }

        return $filterId;
    }

    /**
     * @param AdminGridConfiguration $configuration
     *
     * @return void
     *
     * @throws GridViewException
     */
    private function assertViewLimitIsNotReached(AdminGridConfiguration $configuration): void
    {
        if ($configuration->getViews()->count() >= self::MAX_VIEWS_PER_CONFIGURATION) {
            throw new GridViewException(
                sprintf('A grid configuration cannot hold more than %d views', self::MAX_VIEWS_PER_CONFIGURATION),
                GridViewException::VIEW_LIMIT_REACHED
            );
        }
    }

    /**
     * @param array{display_shared_filters: bool|null, display_totals: bool|null, controller_route: string, filter_id: string} $data
     *
     * @throws GridViewException
     */
    public function saveConfiguration(string $gridId, array $data): AdminGridConfiguration
    {
        $this->assertRouteExists($data['controller_route']);

        $configuration = $this->configurationRepository->findOrCreateForEmployee(
            $this->getEmployeeId(),
            $this->shopContext->getId(),
            $gridId,
            $data['filter_id'],
            $data['controller_route']
        );

        $configuration
            ->setDisplaySharedFilters((bool) ($data['display_shared_filters'] ?? true))
            ->setDisplayTotals((bool) ($data['display_totals'] ?? true))
        ;

        $this->entityManager->flush();

        return $configuration;
    }

    /**
     * @throws GridViewException
     */
    private function getEmployeeId(): int
    {
        $employee = $this->employeeContext->getEmployee();

        if (null === $employee) {
            throw new GridViewException('No employee in context', GridViewException::MISSING_EMPLOYEE);
        }

        return $employee->getId();
    }

    /**
     * @throws GridViewException
     */
    private function assertRouteExists(string $routeName): void
    {
        if (null === $this->router->getRouteCollection()->get($routeName)) {
            throw new GridViewException(sprintf('Unknown route "%s"', $routeName), GridViewException::UNKNOWN_ROUTE);
        }
    }

    /**
     * @param array $dateRules
     * @param array $searchCriteria
     *
     * @return array|null
     */
    private function sanitizeDateRules(array $dateRules, array $searchCriteria): ?array
    {
        $sanitizedRules = [];

        foreach ($dateRules as $field => $ruleConfig) {
            $filterValue = $searchCriteria['filters'][$field] ?? null;
            if (!is_array($filterValue) || (!isset($filterValue['from']) && !isset($filterValue['to']))) {
                continue;
            }

            $rule = DynamicDateRule::tryFrom((string) ($ruleConfig['date_rule'] ?? ''));
            if (null === $rule || DynamicDateRule::KEEP_AS_IS === $rule) {
                continue;
            }

            $customDays = isset($ruleConfig['custom_days']) && is_numeric($ruleConfig['custom_days'])
                ? (int) $ruleConfig['custom_days']
                : null;

            if (DynamicDateRule::LAST_DAYS === $rule && (null === $customDays || $customDays < 1)) {
                continue;
            }

            $sanitizedRules[$field] = [
                'date_rule' => $rule->value,
                'custom_days' => DynamicDateRule::LAST_DAYS === $rule ? $customDays : null,
            ];
        }

        return [] !== $sanitizedRules ? $sanitizedRules : null;
    }

    /**
     * @param string|null $gridStateJson
     *
     * @return array|null
     */
    private function sanitizeGridState(?string $gridStateJson): ?array
    {
        if (empty($gridStateJson)) {
            return null;
        }

        $decodedState = json_decode($gridStateJson, true);
        if (!is_array($decodedState)) {
            return null;
        }

        return GridState::fromArray($decodedState)->toArray();
    }
}
