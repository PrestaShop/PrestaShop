<?php
/**
 * 2007-2019 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\Command\BulkDeleteFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\Command\DeleteFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\Exception\CannotDeleteFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\FeatureGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters\FeatureFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for "Sell > Catalog > Attributes & Features > Features" page
 */
class FeatureController extends FrameworkBundleAdminController
{
    /**
     * Renders features list
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param FeatureFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, FeatureFilters $filters): Response
    {
        $gridFactory = $this->get('prestashop.core.grid.factory.feature');
        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Features/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'featuresGrid' => $gridPresenter->present($gridFactory->getGrid($filters)),
        ]);
    }

    /**
     * Prepares filtering response
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
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
            $this->get('prestashop.core.grid.definition.factory.feature'),
            $request,
            FeatureGridDefinitionFactory::GRID_ID,
            'admin_features_index'
        );
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_features_index")
     *
     * @param int $featureId
     *
     * @return RedirectResponse
     */
    public function deleteAction(int $featureId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteFeatureCommand($featureId));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_features_index');
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_features_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $featureIds = $this->getBulkFeaturesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteFeatureCommand($featureIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_features_index');
    }

    /**
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_features_index"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function updatePositionAction(Request $request)
    {
        $positionsData = [
            'positions' => $request->request->get('positions'),
        ];
        $positionDefinition = $this->get('prestashop.core.grid.feature.position_definition');
        $positionUpdateFactory = $this->get('prestashop.core.grid.position.position_update_factory');
        try {
            $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);
            $updater = $this->get('prestashop.core.grid.position.doctrine_grid_position_updater');
            $updater->update($positionUpdate);
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('admin_features_index');
    }

    private function getErrorMessages()
    {
        return [
            FeatureNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CannotDeleteFeatureException::class => [
                CannotDeleteFeatureException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                CannotDeleteFeatureException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkFeaturesFromRequest(Request $request): array
    {
        $featureIds = $request->request->get('feature_bulk');

        if (!is_array($featureIds)) {
            return [];
        }

        foreach ($featureIds as $i => $featureId) {
            $featureIds[$i] = (int) $featureId;
        }

        return $featureIds;
    }
}
