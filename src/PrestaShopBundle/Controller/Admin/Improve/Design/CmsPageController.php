<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\BulkDeleteCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\BulkDisableCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\BulkEnableCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\DeleteCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\ToggleCmsPageStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotDeleteCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotDisableCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotEnableCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotToggleCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Query\GetCmsCategoryIdForRedirection;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\BulkDeleteCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\BulkDisableCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\BulkEnableCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\DeleteCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\ToggleCmsPageCategoryStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CannotDeleteCmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CannotToggleCmsPageCategoryStatusException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageParentCategoryIdForRedirection;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CmsPageCategoryDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CmsPageDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Search\Filters\CmsPageCategoryFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\CmsPageFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CmsPageController is responsible for handling the logic in "Improve -> Design -> pages" page.
 */
class CmsPageController extends FrameworkBundleAdminController
{
    /**
     * Responsible for displaying page content.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param CmsPageCategoryFilters $categoryFilters
     * @param CmsPageFilters $cmsFilters
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(CmsPageCategoryFilters $categoryFilters, CmsPageFilters $cmsFilters, Request $request)
    {
        $cmsCategoryParentId = (int) $categoryFilters->getFilters()['id_cms_category_parent'];
        $viewData = [];

        try {
            $viewData = $this
                ->get('prestashop.core.cms_page.data_provider.cms_page_view')
                ->getView($cmsCategoryParentId)
            ;
        } catch (CmsPageCategoryNotFoundException $exception) {
            return $this->redirectToRoute('admin_cms_pages_index');
        } catch (CmsPageCategoryException $exception) {
        }

        $cmsCategoryGridFactory = $this->get('prestashop.core.grid.factory.cms_page_category');
        $cmsCategoryGrid = $cmsCategoryGridFactory->getGrid($categoryFilters);

        $cmsGridFactory = $this->get('prestashop.core.grid.factory.cms_page');
        $cmsGrid = $cmsGridFactory->getGrid($cmsFilters);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return $this->render('@PrestaShop/Admin/Improve/Design/Cms/index.html.twig', [
            'cmsCategoryGrid' => $gridPresenter->present($cmsCategoryGrid),
            'cmsGrid' => $gridPresenter->present($cmsGrid),
            'cmsPageView' => $viewData,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $gridDefinitionFactory = 'prestashop.core.grid.definition.factory.cms_page_category';
        $filterId = CmsPageCategoryDefinitionFactory::GRID_ID;
        if ($request->request->has(CmsPageDefinitionFactory::GRID_ID)) {
            $gridDefinitionFactory = 'prestashop.core.grid.definition.factory.cms_page';
            $filterId = CmsPageDefinitionFactory::GRID_ID;
        }

        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get($gridDefinitionFactory),
            $request,
            $filterId,
            'admin_cms_pages_index',
            [
                'id_cms_category',
            ]
        );
    }

    /**
     * Displays cms category page form and handles create new cms page category logic.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"},
     *     message="You do not have permission to add this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createCmsCategoryAction(Request $request)
    {
        $cmsPageCategoryFormBuilder = $this->getCmsPageCategoryFormBuilder();
        $cmsPageCategoryForm = $cmsPageCategoryFormBuilder->getForm();

        $cmsPageCategoryForm->handleRequest($request);

        try {
            $result = $this->getCmsPageCategoryFormHandler()->handle($cmsPageCategoryForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful creation.', 'Admin.Notifications.Success')
                );

                return $this->redirectToIndexPageById($result->getIdentifiableObjectId());
            }
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->render('@PrestaShop/Admin/Improve/Design/Cms/create_category.html.twig', [
            'cmsPageCategoryForm' => $cmsPageCategoryForm->createView(),
        ]);
    }

    /**
     * Displays cms category page form and handles update cms page category logic.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $cmsCategoryId
     * @param Request $request
     *
     * @return Response
     *
     * @throws CmsPageCategoryException
     */
    public function editCmsCategoryAction($cmsCategoryId, Request $request)
    {
        $cmsPageCategoryFormBuilder = $this->getCmsPageCategoryFormBuilder();
        $cmsPageCategoryForm = $cmsPageCategoryFormBuilder->getFormFor((int) $cmsCategoryId);
        $cmsPageCategoryForm->handleRequest($request);
        $cmsCategoryParentId = null;

        try {
            $result = $this->getCmsPageCategoryFormHandler()->handleFor((int) $cmsCategoryId, $cmsPageCategoryForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToIndexPageById($result->getIdentifiableObjectId());
            }

            $cmsCategoryParentId = $this->getParentCategoryId((int) $cmsCategoryId)->getValue();
        } catch (CmsPageCategoryNotFoundException $exception) {
            $this->addFlash('error', $this->handleException($exception));

            return $this->redirectToParentIndexPage((int) $cmsCategoryId);
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->render('@PrestaShop/Admin/Improve/Design/Cms/edit_category.html.twig', [
            'cmsPageCategoryForm' => $cmsPageCategoryForm->createView(),
            'cmsCategoryParentId' => $cmsCategoryParentId,
        ]);
    }

    /**
     * Deletes cms page category and all its children categories.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"},
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"}
     * )
     *
     * @param int $cmsCategoryId
     *
     * @return RedirectResponse
     *
     * @throws CmsPageCategoryException
     */
    public function deleteCmsCategoryAction($cmsCategoryId)
    {
        $redirectResponse = $this->redirectToParentIndexPage((int) $cmsCategoryId);
        try {
            $this->getCommandBus()->handle(
                new DeleteCmsPageCategoryCommand((int) $cmsCategoryId)
            );

            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $redirectResponse;
    }

    /**
     * Deletes multiple cms page categories.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"},
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"}
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws CmsPageCategoryException
     */
    public function deleteBulkCmsCategoryAction(Request $request)
    {
        $cmsCategoriesToDelete = $request->request->get('cms_page_category_bulk');
        $redirectResponse = $this->redirectToParentIndexPageByCategoryBulkIds($cmsCategoriesToDelete);

        try {
            $cmsCategoriesToDelete = array_map(function ($item) { return (int) $item; }, $cmsCategoriesToDelete);

            $this->getCommandBus()->handle(
                new BulkDeleteCmsPageCategoryCommand($cmsCategoriesToDelete)
            );

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $redirectResponse;
    }

    /**
     * Updates cms page category position.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"},
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"}
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws CmsPageCategoryException
     */
    public function updateCmsCategoryPositionAction(Request $request)
    {
        $cmsCategoryParentId = $request->query->getInt('id_cms_category');

        //todo: position update using ajax and position search fix in another PR.
        $positionsData = [
            'positions' => $request->request->get('positions'),
            'parentId' => $cmsCategoryParentId,
        ];

        $positionDefinition = $this->get('prestashop.core.grid.cms_page_category.position_definition');

        $positionUpdateFactory = $this->get('prestashop.core.grid.position.position_update_factory');

        try {
            $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);
        } catch (PositionDataException $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);

            return $this->redirectToParentIndexPage($cmsCategoryParentId);
        }

        $updater = $this->get('prestashop.core.grid.position.doctrine_grid_position_updater');

        try {
            $updater->update($positionUpdate);
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);
        }

        return $this->redirectToParentIndexPage($cmsCategoryParentId);
    }

    /**
     * Toggles cms page category status.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *      redirectRoute="admin_cms_pages_index",
     *      redirectQueryParamsToKeep={"id_cms_category"},
     *      message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"}
     * )
     *
     * @param int $cmsCategoryId
     *
     * @return RedirectResponse
     *
     * @throws CmsPageCategoryException
     */
    public function toggleCmsCategoryAction($cmsCategoryId)
    {
        try {
            $this->getCommandBus()->handle(
                new ToggleCmsPageCategoryStatusCommand((int) $cmsCategoryId)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->redirectToParentIndexPage((int) $cmsCategoryId);
    }

    /**
     * Changes multiple cms page category statuses to enabled.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"},
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"}
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws CmsPageCategoryException
     */
    public function bulkCmsPageCategoryStatusEnableAction(Request $request)
    {
        $cmsCategoriesToEnable = $request->request->get('cms_page_category_bulk');
        $cmsCategoryParentId = null;
        try {
            $cmsCategoriesToEnable = array_map(function ($item) { return (int) $item; }, $cmsCategoriesToEnable);

            $this->getCommandBus()->handle(
                new BulkEnableCmsPageCategoryCommand($cmsCategoriesToEnable)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->redirectToParentIndexPageByCategoryBulkIds($cmsCategoriesToEnable);
    }

    /**
     * Changes multiple cms page category statuses to disabled.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"},
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"}
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws CmsPageCategoryException
     */
    public function bulkCmsPageCategoryStatusDisableAction(Request $request)
    {
        $cmsCategoriesToDisable = $request->request->get('cms_page_category_bulk');
        try {
            $cmsCategoriesToDisable = array_map(function ($item) { return (int) $item; }, $cmsCategoriesToDisable);
            $this->getCommandBus()->handle(
                new BulkDisableCmsPageCategoryCommand($cmsCategoriesToDisable)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->redirectToParentIndexPageByCategoryBulkIds($cmsCategoriesToDisable);
    }

    /**
     * Toggles cms page listing status.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"},
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"}
     * )
     *
     * @param int $cmsId
     *
     * @return RedirectResponse
     */
    public function toggleCmsAction($cmsId)
    {
        try {
            $this->getCommandBus()->handle(new ToggleCmsPageStatusCommand((int) $cmsId));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->redirectToParentIndexPageByCmsPageId($cmsId);
    }

    /**
     * Disables multiple cms pages.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"},
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"}
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDisableCmsPageStatusAction(Request $request)
    {
        $cmsPagesToDisable = $request->request->get('cms_page_bulk');

        try {
            $cmsPagesToDisable = array_map(function ($item) { return (int) $item; }, $cmsPagesToDisable);

            $this->getCommandBus()->handle(
                new BulkDisableCmsPageCommand($cmsPagesToDisable)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->redirectToParentIndexPageByBulkIds($cmsPagesToDisable);
    }

    /**
     * Enables multiple cms pages.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"},
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"}
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkEnableCmsPageStatusAction(Request $request)
    {
        $cmsPagesToDisable = $request->request->get('cms_page_bulk');

        try {
            $cmsPagesToDisable = array_map(function ($item) { return (int) $item; }, $cmsPagesToDisable);

            $this->getCommandBus()->handle(
                new BulkEnableCmsPageCommand($cmsPagesToDisable)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->redirectToParentIndexPageByBulkIds($cmsPagesToDisable);
    }

    /**
     * Deletes multiple cms pages.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"},
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"}
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteCmsPageAction(Request $request)
    {
        $cmsPagesToDisable = $request->request->get('cms_page_bulk');

        $redirectResponse = $this->redirectToParentIndexPageByBulkIds($cmsPagesToDisable);

        try {
            $cmsPagesToDisable = array_map(function ($item) { return (int) $item; }, $cmsPagesToDisable);

            $this->getCommandBus()->handle(
                new BulkDeleteCmsPageCommand($cmsPagesToDisable)
            );

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $redirectResponse;
    }

    /**
     * Deletes cms page by given id.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"},
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"id_cms_category"}
     * )
     *
     * @param int $cmsId
     *
     * @return RedirectResponse
     */
    public function deleteCmsAction($cmsId)
    {
        $redirectResponse = $this->redirectToParentIndexPageByCmsPageId($cmsId);

        try {
            $this->getCommandBus()->handle(new DeleteCmsPageCommand((int) $cmsId));

            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $redirectResponse;
    }

    public function editCmsAction()
    {
        //todo: implement
    }

    /**
     * Gets cms page category form builder.
     *
     * @return FormBuilderInterface
     */
    private function getCmsPageCategoryFormBuilder()
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.cms_page_category_form_builder');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getCmsPageCategoryFormHandler()
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.cms_page_category_form_handler');
    }

    /**
     * This function is used for redirecting to the specific cms page category page. It uses bulk action ids which
     * share the same parent cms category in all cases.
     *
     * @param array $cmsPageCategoryIds
     *
     * @return RedirectResponse
     *
     * @throws CmsPageCategoryException
     */
    private function redirectToParentIndexPageByCategoryBulkIds(array $cmsPageCategoryIds)
    {
        if (empty($cmsPageCategoryIds)) {
            return $this->redirectToRoute('admin_cms_pages_index');
        }

        return $this->redirectToParentIndexPage((int) $cmsPageCategoryIds[0]);
    }

    /**
     * This function is used for redirecting to the specific cms page category page.
     *
     * @param array $cmsPageIds
     *
     * @return RedirectResponse
     */
    private function redirectToParentIndexPageByBulkIds(array $cmsPageIds)
    {
        if (empty($cmsPageIds)) {
            return $this->redirectToRoute('admin_cms_pages_index');
        }

        return $this->redirectToParentIndexPageByCmsPageId($cmsPageIds[0]);
    }

    /**
     * This function is used for redirecting to the specific cms page category page.
     *
     * @param int $cmsPageCategoryId
     *
     * @return RedirectResponse
     *
     * @throws CmsPageCategoryException
     */
    private function redirectToParentIndexPage($cmsPageCategoryId)
    {
        $cmsPageCategoryParentId = $this->getParentCategoryId($cmsPageCategoryId);

        return $this->redirectToIndexPageById($cmsPageCategoryParentId->getValue());
    }

    /**
     * @param int $cmsPageId
     *
     * @return RedirectResponse
     */
    private function redirectToParentIndexPageByCmsPageId($cmsPageId)
    {
        try {
            $cmsCategoryId = $this->getQueryBus()->handle(new GetCmsCategoryIdForRedirection((int) $cmsPageId));
        } catch (CmsPageException $e) {
            $cmsCategoryId = CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID;
        }

        return $this->redirectToIndexPageById($cmsCategoryId->getValue());
    }

    /**
     * Redirects to index page by given id.
     *
     * @param $cmsPageCategoryId
     *
     * @return RedirectResponse
     */
    private function redirectToIndexPageById($cmsPageCategoryId)
    {
        $routeParameters = [];

        if ($cmsPageCategoryId !== CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID) {
            $routeParameters = [
                'id_cms_category' => $cmsPageCategoryId,
            ];
        }

        return $this->redirectToRoute('admin_cms_pages_index', $routeParameters);
    }

    /**
     * Gets parent id according to the given child
     *
     * @param int $cmsPageCategoryChildId
     *
     * @return CmsPageCategoryId
     *
     * @throws CmsPageCategoryException
     */
    private function getParentCategoryId($cmsPageCategoryChildId)
    {
        /** @var CmsPageCategoryId $cmsPageCategoryParentId */
        $cmsPageCategoryParentId = $this->getQueryBus()->handle(
            new GetCmsPageParentCategoryIdForRedirection($cmsPageCategoryChildId)
        );

        return $cmsPageCategoryParentId;
    }

    /**
     * Handles commands exceptions and formats to user friendly error message.
     *
     * @param DomainException $exception
     *
     * @return string
     */
    private function handleException(DomainException $exception)
    {
        $errorMessage = $this->trans('Unexpected error occurred.', 'Admin.Notifications.Error');
        $statusCode = $exception->getCode();

        if ($exception instanceof CmsPageCategoryException && 0 === $statusCode) {
            $errorMessage = $this->getCmsPageCategoryErrorByExceptionType($exception);
        }

        if ($exception instanceof CmsPageCategoryException && 0 !== $statusCode) {
            $errorMessage = $this->getCmsPageCategoryErrorByExceptionTypeAndCode($exception);
        }

        if ($exception instanceof CmsPageException && 0 === $statusCode) {
            $errorMessage = $this->getCmsPageErrorByExceptionType($exception);
        }

        return $errorMessage;
    }

    /**
     * Gets error by exception type.
     *
     * @param CmsPageCategoryException $exception
     *
     * @return string
     */
    private function getCmsPageCategoryErrorByExceptionType(CmsPageCategoryException $exception)
    {
        $exceptionTypeDictionary = [
            CmsPageCategoryNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CannotToggleCmsPageCategoryStatusException::class => $this->trans(
                'An error occurred while updating the status.',
                'Admin.Notifications.Error'
            ),
        ];

        if ($exception instanceof CannotDeleteCmsPageCategoryException) {
            return $this->trans(
                'Can\'t delete #%id%',
                'Admin.Notifications.Error',
                [
                    '%id%' => $exception->getCmsPageCategoryId(),
                ]
            );
        }

        $exceptionType = get_class($exception);
        if (isset($exceptionTypeDictionary[$exceptionType])) {
            return $exceptionTypeDictionary[$exceptionType];
        }

        return $this->getFallbackErrorMessage($exceptionType, $exception->getCode());
    }

    /**
     * Gets exception of cms page category by its type and status code.
     *
     * @param CmsPageCategoryException $exception
     *
     * @return string
     */
    private function getCmsPageCategoryErrorByExceptionTypeAndCode(CmsPageCategoryException $exception)
    {
        $exceptionTypeDictionary = [
            CmsPageCategoryConstraintException::class => [
                CmsPageCategoryConstraintException::INVALID_BULK_DATA => $this->trans(
                    'You must select at least one element to delete.',
                    'Admin.Notifications.Error'
                ),
                CmsPageCategoryConstraintException::CANNOT_MOVE_CATEGORY_TO_PARENT => $this->trans('The page Category cannot be moved here.', 'Admin.Design.Notification'),
                CmsPageCategoryConstraintException::MISSING_DEFAULT_LANGUAGE_FOR_NAME => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Name', 'Admin.Global')),
                        ]
                    ),
                CmsPageCategoryConstraintException::MISSING_DEFAULT_LANGUAGE_FOR_FRIENDLY_URL => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Friendly URL', 'Admin.Global')),
                        ]
                    ),
                CmsPageCategoryConstraintException::INVALID_CATEGORY_NAME => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Name', 'Admin.Global')),
                        ]
                    ),
                CmsPageCategoryConstraintException::INVALID_LINK_REWRITE => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Friendly URL', 'Admin.Global')),
                        ]
                    ),
                CmsPageCategoryConstraintException::INVALID_META_TITLE => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Meta title', 'Admin.Global')),
                        ]
                    ),
                CmsPageCategoryConstraintException::INVALID_DESCRIPTION => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Description', 'Admin.Global')),
                        ]
                    ),
                CmsPageCategoryConstraintException::INVALID_META_DESCRIPTION => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Meta description', 'Admin.Global')),
                        ]
                    ),
                CmsPageCategoryConstraintException::INVALID_META_KEYWORDS => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Meta keywords', 'Admin.Global')),
                        ]
                    ),
            ],
        ];

        $exceptionType = get_class($exception);
        $statusCode = $exception->getCode();

        if (isset($exceptionTypeDictionary[$exceptionType][$statusCode])) {
            return $exceptionTypeDictionary[$exceptionType][$statusCode];
        }

        return $this->getFallbackErrorMessage($exceptionType, $statusCode);
    }

    /**
     * Gets user friendly error message by exception.
     *
     * @param CmsPageException $exception
     *
     * @return string
     */
    private function getCmsPageErrorByExceptionType(CmsPageException $exception)
    {
        $exceptionTypeDictionary = [
            CannotToggleCmsPageException::class => $this->trans(
                'An error occurred while updating the status.',
                'Admin.Notifications.Error'
            ),
            CannotDisableCmsPageException::class => $this->trans(
                'An error occurred while updating the status.',
                'Admin.Notifications.Error'
            ),
            CannotEnableCmsPageException::class => $this->trans(
                'An error occurred while updating the status.',
                'Admin.Notifications.Error'
            ),
        ];

        if ($exception instanceof CannotDeleteCmsPageException) {
            return $this->trans(
                'Can\'t delete #%id%',
                'Admin.Notifications.Error',
                [
                    '%id%' => $exception->getCmsPageId(),
                ]
            );
        }

        $exceptionType = get_class($exception);

        if (isset($exceptionTypeDictionary[$exceptionType])) {
            return $exceptionTypeDictionary[$exceptionType];
        }

        return $this->getFallbackErrorMessage($exceptionType, $exception->getCode());
    }
}
