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

namespace PrestaShopBundle\Controller\Admin\Sell\CustomerService;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\MerchandiseReturnGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\MerchandiseReturnFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MerchandiseReturnController responsible for "Sell > Customer Service > Merchandise Returns" page
 */
class MerchandiseReturnController extends FrameworkBundleAdminController
{
    /**
     * Render merchandise returns grid and options.
     *
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_merchandise_return_index"
     * )
     *
     * @param MerchandiseReturnFilters $filters
     *
     * @return Response
     */
    public function indexAction(MerchandiseReturnFilters $filters): Response
    {
        $gridFactory = $this->get('prestashop.core.grid.factory.merchandise_return');
        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return $this->render('@PrestaShop/Admin/Sell/CustomerService/MerchandiseReturn/index.html.twig', [
            'merchandiseReturnsGrid' => $gridPresenter->present($gridFactory->getGrid($filters)),
        ]);
    }

    /**
     * Prepares filtering response
     *
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_merchandise_return_index"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function filterAction(Request $request): RedirectResponse
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');
        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.core.grid.definition.factory.merchandise_return'),
            $request,
            MerchandiseReturnGridDefinitionFactory::GRID_ID,
            'admin_merchandise_returns_index'
        );
    }
}
