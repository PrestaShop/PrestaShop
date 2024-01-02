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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
