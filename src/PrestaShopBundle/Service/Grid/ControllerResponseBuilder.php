<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

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
