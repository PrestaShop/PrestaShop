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

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\BulkDeleteSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\DeleteSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\DeleteSearchEngineException;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineException;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Query\GetSearchEngineForEditing;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\QueryResult\SearchEngineForEditing;
use PrestaShop\PrestaShop\Core\Search\Filters\SearchEngineFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for handling "Configure > Shop Parameters > Traffic & SEO > Search Engines" page.
 */
class SearchEnginesController extends FrameworkBundleAdminController
{
    /**
     * Show search engines listing page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param SearchEngineFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, SearchEngineFilters $filters): Response
    {
        $searchEngineGridFactory = $this->get('prestashop.core.grid.factory.search_engines');
        $searchEnginesGrid = $searchEngineGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/TrafficSeo/SearchEngines/index.html.twig', [
            'searchEnginesGrid' => $this->presentGrid($searchEnginesGrid),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Shows search engine creation form page and handle its submit.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $searchEngineFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.search_engine_form_handler');
        $searchEngineFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.search_engine_form_builder');

        $searchEngineForm = $searchEngineFormBuilder->getForm();
        $searchEngineForm->handleRequest($request);

        try {
            $result = $searchEngineFormHandler->handle($searchEngineForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_search_engines_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/TrafficSeo/SearchEngines/create.html.twig', [
            'searchEngineForm' => $searchEngineForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'multistoreInfoTip' => $this->trans(
                'Note that this feature is only available in the "all stores" context. It will be added to all your stores.',
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->get('prestashop.adapter.multistore_feature')->isUsed(),
        ]);
    }

    /**
     * Show search engine edit form page and handles its submit.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $searchEngineId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(int $searchEngineId, Request $request): Response
    {
        $searchEngineFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.search_engine_form_handler');
        $searchEngineFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.search_engine_form_builder');

        try {
            $searchEngineForm = $searchEngineFormBuilder->getFormFor($searchEngineId);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_search_engines_index');
        }

        try {
            $searchEngineForm->handleRequest($request);
            $result = $searchEngineFormHandler->handleFor($searchEngineId, $searchEngineForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_search_engines_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            if ($e instanceof SearchEngineNotFoundException) {
                return $this->redirectToRoute('admin_search_engines_index');
            }
        }

        /** @var SearchEngineForEditing $editableSearchEngine */
        $editableSearchEngine = $this->getQueryBus()->handle(new GetSearchEngineForEditing($searchEngineId));

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/TrafficSeo/SearchEngines/edit.html.twig', [
            'searchEngineForm' => $searchEngineForm->createView(),
            'searchEngineServer' => $editableSearchEngine->getServer(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Deletes search engine.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_search_engines_index",
     * )
     *
     * @param int $searchEngineId
     *
     * @return RedirectResponse
     */
    public function deleteAction(int $searchEngineId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteSearchEngineCommand($searchEngineId));

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (SearchEngineException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_search_engines_index');
    }

    /**
     * Deletes search engines in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_search_engines_index",
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $searchEngineIds = $this->getBulkSearchEnginesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteSearchEngineCommand($searchEngineIds));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (SearchEngineException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_search_engines_index');
    }

    /**
     * Gets error messages for exceptions.
     *
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            SearchEngineNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                'Admin.Notifications.Error'
            ),
            DeleteSearchEngineException::class => [
                DeleteSearchEngineException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                DeleteSearchEngineException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * Get search engine IDs from request for bulk actions.
     *
     * @param Request $request
     *
     * @return int[]
     */
    private function getBulkSearchEnginesFromRequest(Request $request): array
    {
        $searchEngineIds = $request->request->get('search_engine_bulk');

        if (!is_array($searchEngineIds)) {
            return [];
        }

        return array_map('intval', $searchEngineIds);
    }
}
