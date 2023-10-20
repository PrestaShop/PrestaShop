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

namespace PrestaShopBundle\Service\Grid;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\GridFilterFormFactoryInterface;
use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;

class ResponseBuilder
{
    /** @var AdminFilterRepository */
    private $adminFilterRepository;

    /** @var int|null */
    private $employeeId;

    /** @var GridFilterFormFactoryInterface */
    private $filterFormFactory;

    /** @var Router */
    private $router;

    /** @var int */
    private $shopId;

    /** @var Session */
    private $session;

    /**
     * @param GridFilterFormFactoryInterface $filterFormFactory
     * @param Router $router
     * @param AdminFilterRepository $adminFilterRepository
     * @param int|null $employeeId
     * @param int $shopId
     */
    public function __construct(
        GridFilterFormFactoryInterface $filterFormFactory,
        Router $router,
        AdminFilterRepository $adminFilterRepository,
        ?int $employeeId,
        int $shopId,
        Session $session
    ) {
        $this->filterFormFactory = $filterFormFactory;
        $this->router = $router;
        $this->adminFilterRepository = $adminFilterRepository;
        $this->employeeId = $employeeId;
        $this->shopId = $shopId;
        $this->session = $session;
    }

    /**
     * @param GridDefinitionFactoryInterface $definitionFactory
     * @param Request $request
     * @param string $filterId
     * @param string $redirectRoute
     * @param array $queryParamsToKeep
     *
     * @return RedirectResponse
     */
    public function buildSearchResponse(
        GridDefinitionFactoryInterface $definitionFactory,
        Request $request,
        $filterId,
        $redirectRoute,
        array $queryParamsToKeep = []
    ) {
        /** @var GridDefinitionInterface $definition */
        $definition = $definitionFactory->getDefinition();

        /** @var FormInterface $filtersForm */
        $filtersForm = $this->filterFormFactory->create($definition);
        $filtersForm->handleRequest($request);

        $redirectParams = [];
        if ($filtersForm->isSubmitted()) {
            if ($filtersForm->isValid()) {
                if ($this->checkIsFormDataEmpty($filtersForm->getData())) {
                    $this->resetPersistedFilter($filterId);
                }

                $redirectParams = [
                    $filterId => [
                        'filters' => $filtersForm->getData(),
                    ],
                ];
            } else {
                foreach ($filtersForm->getErrors(true) as $error) {
                    $fieldLabel = $error->getOrigin()->getConfig()->getOption('label') ?: $error->getOrigin()->getName();
                    $this->session->getFlashBag()->add('error', sprintf('%s: %s', $fieldLabel, $error->getMessage()));
                }
            }
        }

        foreach ($queryParamsToKeep as $paramName) {
            if ($request->query->has($paramName)) {
                $redirectParams[$paramName] = $request->query->get($paramName);
            }
            if ($request->attributes->has($paramName)) {
                $redirectParams[$paramName] = $request->attributes->get($paramName);
            }
        }

        $redirectUrl = $this->router->generate($redirectRoute, $redirectParams);

        return new RedirectResponse($redirectUrl, 302);
    }

    /**
     * @param string $filterId
     *
     * @return void
     */
    private function resetPersistedFilter(string $filterId): void
    {
        if (empty($filterId)) {
            return;
        }
        $adminFilter = $this->adminFilterRepository->findByEmployeeAndFilterId(
            $this->employeeId,
            $this->shopId,
            $filterId
        );
        if (!$adminFilter) {
            return;
        }
        $this->adminFilterRepository->unsetFilters($adminFilter);
    }

    /**
     * Return true if array is empty (null values or empty array)
     *
     * @param array $formData
     *
     * @return bool
     */
    private function checkIsFormDataEmpty(array $formData): bool
    {
        foreach ($formData as $data) {
            if ($data === null) {
                continue;
            }
            if (is_array($data) && $this->checkIsFormDataEmpty($data)) {
                continue;
            }

            return false;
        }

        return true;
    }
}
