<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Builder;

use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use PrestaShop\PrestaShop\Core\Search\ControllerAction;
use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Basic abstract class for filters related to the database, whether they need to persist
 * or search for filters. It is created with all the necessary services and configuration
 * including the context (employee + shop), and it can extract the filters matching from
 * the config or the request (either via filter_id or via controller/action matching).
 */
abstract class AbstractRepositoryFiltersBuilder extends AbstractFiltersBuilder
{
    /** @var AdminFilterRepository */
    protected $adminFilterRepository;

    /** @var ContextEmployeeProviderInterface */
    protected $employeeProvider;

    /** @var int */
    protected $shopId;

    /** @var string */
    protected $controller;

    /** @var string */
    protected $action;

    /**
     * @param AdminFilterRepository $adminFilterRepository
     * @param ContextEmployeeProviderInterface $employeeProvider
     * @param int $shopId
     */
    public function __construct(
        AdminFilterRepository $adminFilterRepository,
        ContextEmployeeProviderInterface $employeeProvider,
        $shopId
    ) {
        $this->adminFilterRepository = $adminFilterRepository;
        $this->employeeProvider = $employeeProvider;
        $this->shopId = $shopId;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config)
    {
        $defaultController = $defaultAction = '';
        if (isset($config['request']) && $config['request'] instanceof Request) {
            $request = $config['request'];
            [$defaultController, $defaultAction] = ControllerAction::fromString($request->get('_controller'));
        }

        $this->controller = isset($config['controller']) ? $config['controller'] : $defaultController;
        $this->action = isset($config['action']) ? $config['action'] : $defaultAction;

        return parent::setConfig($config);
    }
}
