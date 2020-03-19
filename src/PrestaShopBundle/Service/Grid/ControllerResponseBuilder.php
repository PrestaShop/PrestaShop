<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Service\Grid;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\GridFilterFormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

/**
 * Before we introduced the filterId notion to store/clean filters on grids the solution
 * used controller and action as a key to retrieve and filter. The filters were not scoped
 * in the request either (which prevented having two grids on the same page).
 * This class is responsible for building the controller response just like it worked before
 * filterId, this allows us to keep a backward compatibility in CommonController::searchAction.
 */
class ControllerResponseBuilder
{
    /** @var GridFilterFormFactoryInterface */
    private $filterFormFactory;

    /** @var Router */
    private $router;

    /**
     * @param GridFilterFormFactoryInterface $filterFormFactory
     * @param Router $router
     */
    public function __construct(
        GridFilterFormFactoryInterface $filterFormFactory,
        Router $router
    ) {
        $this->filterFormFactory = $filterFormFactory;
        $this->router = $router;
    }

    /**
     * @param GridDefinitionFactoryInterface $definitionFactory
     * @param Request $request
     * @param string $redirectRoute
     * @param array $queryParamsToKeep
     *
     * @return RedirectResponse
     */
    public function buildSearchResponse(
        GridDefinitionFactoryInterface $definitionFactory,
        Request $request,
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
            $redirectParams = [
                'filters' => $filtersForm->getData(),
            ];
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
}
