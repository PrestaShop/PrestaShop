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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\BulkDeleteCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\DeleteCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\DeleteCatalogPriceRuleException;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CatalogPriceRuleGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\CatalogPriceRuleFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Sell > Catalog > Discounts > Catalog Price Rules page
 */
class CatalogPriceRuleController extends FrameworkBundleAdminController
{
    /**
     * Displays catalog price rule listing page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param CatalogPriceRuleFilters $catalogPriceRuleFilters
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        CatalogPriceRuleFilters $catalogPriceRuleFilters
    ) {
        $catalogPriceRuleGridFactory = $this->get('prestashop.core.grid.grid_factory.catalog_price_rule');
        $catalogPriceRuleGrid = $catalogPriceRuleGridFactory->getGrid($catalogPriceRuleFilters);

        return $this->render('@PrestaShop/Admin/Sell/Catalog/CatalogPriceRule/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'catalogPriceRuleGrid' => $this->presentGrid($catalogPriceRuleGrid),
        ]);
    }

    /**
     * Provides filters functionality.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.core.grid.definition.factory.catalog_price_rule'),
            $request,
            CatalogPriceRuleGridDefinitionFactory::GRID_ID,
            'admin_catalog_price_rules_index'
        );
    }

    /**
     * Deletes catalog price rule
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_catalog_price_rules_index")
     * @DemoRestricted(redirectRoute="admin_catalog_price_rules_index")
     *
     * @param $catalogPriceRuleId
     *
     * @return RedirectResponse
     */
    public function deleteAction($catalogPriceRuleId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteCatalogPriceRuleCommand((int) $catalogPriceRuleId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (CatalogPriceRuleException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_catalog_price_rules_index');
    }

    /**
     * Deletes catalogPriceRules on bulk action
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_catalog_price_rules_index")
     * @DemoRestricted(redirectRoute="admin_catalog_price_rules_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        $catalogPriceRuleIds = $this->getBulkCatalogPriceRulesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteCatalogPriceRuleCommand($catalogPriceRuleIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (CatalogPriceRuleException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_catalog_price_rules_index');
    }

    /**
     * Provides translated error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            DeleteCatalogPriceRuleException::class => [
                DeleteCatalogPriceRuleException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                DeleteCatalogPriceRuleException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * Provides catalog price rule ids from request of bulk action
     *
     * @param Request $request
     *
     * @return array
     */
    private function getBulkCatalogPriceRulesFromRequest(Request $request)
    {
        $catalogPriceRuleIds = $request->request->get('catalog_price_rule_bulk');

        if (!is_array($catalogPriceRuleIds)) {
            return [];
        }

        foreach ($catalogPriceRuleIds as &$catalogPriceRuleId) {
            $catalogPriceRuleId = (int) $catalogPriceRuleId;
        }

        return $catalogPriceRuleIds;
    }
}
