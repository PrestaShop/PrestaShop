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
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Search\Filters\CmsPageCategoryFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @Template("@PrestaShop/Admin/Improve/Design/Cms/index.html.twig")
     *
     * @param int $cmsCategoryParentId
     * @param CmsPageCategoryFilters $filters
     *
     * @return array
     */
    public function indexAction($cmsCategoryParentId, CmsPageCategoryFilters $filters)
    {
        $cmsCategoryGridFactory = $this->get('prestashop.core.grid.factory.cms_page_category');
        $cmsCategoryGrid = $cmsCategoryGridFactory->getGrid($filters);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        $viewData = $this->get('prestashop.core.cms_page.data_provider.cms_page_view')->getView($cmsCategoryParentId);

        return [
            'cmsCategoryGrid' => $gridPresenter->present($cmsCategoryGrid),
            'cmsPageView' => $viewData,
        ];
    }

    /**
     * Implements filtering for the cms page category list.
     *
     * @param int $cmsCategoryParentId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchCategoryAction($cmsCategoryParentId, Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.cms_page_category');
        $definitionFactory = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $searchParametersForm = $gridFilterFormFactory->create($definitionFactory);
        $searchParametersForm->handleRequest($request);

        $filters = [];
        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_cms_pages_index', [
            'cmsCategoryParentId' => $cmsCategoryParentId,
            'filters' => $filters,
        ]);
    }

    /**
     * Displays cms category page form and handles create new cms page category logic.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"cmsCategoryParentId"},
     *     message="You do not have permission to add this."
     * )
     *
     * @param int $cmsCategoryParentId
     *
     * @return RedirectResponse
     */
    public function createCmsCategoryAction($cmsCategoryParentId)
    {
//        todo: remove legacy parts once form is ready and demoRestricted on post action
        //todo: demo restricted on post action
        $legacyLink = $this->getAdminLink('AdminCmsContent', [
            'addcms_category' => 1,
        ]);

        return $this->redirect($legacyLink);
    }

    /**
     * Displays cms category page form and handles update cms page category logic.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"cmsCategoryParentId"},
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $cmsCategoryParentId
     * @param int $cmsCategoryId
     *
     * @return RedirectResponse
     */
    public function editCmsCategoryAction($cmsCategoryParentId, $cmsCategoryId)
    {
//        todo: remove legacy parts once form is ready and demoRestricted on post action
        //todo: demo restricted on post action
        $legacyLink = $this->getAdminLink('AdminCmsContent', [
            'id_cms_category' => $cmsCategoryId,
            'updatecms_category' => 1,
        ]);

        return $this->redirect($legacyLink);
    }

    /**
     * Deletes cms page category and all its children categories.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"cmsCategoryParentId"},
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"cmsCategoryParentId"}
     * )
     *
     * @param int $cmsCategoryParentId
     * @param int $cmsCategoryId
     *
     * @return RedirectResponse
     */
    public function deleteCmsCategoryAction($cmsCategoryParentId, $cmsCategoryId)
    {
        try {
            $cmsPageCategoryId = new CmsPageCategoryId($cmsCategoryId);
            $this->getCommandBus()->handle(new DeleteCmsPageCategoryCommand($cmsPageCategoryId));

            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->redirectToRoute('admin_cms_pages_index', [
            'cmsCategoryParentId' => $cmsCategoryParentId,
        ]);
    }

    /**
     * Deletes multiple cms page categories.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"cmsCategoryParentId"},
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"cmsCategoryParentId"}
     * )
     *
     * @param int $cmsCategoryParentId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkCmsCategoryAction($cmsCategoryParentId, Request $request)
    {
        $cmsCategoriesToDelete = $request->request->get('cms_page_category_bulk');

        try {
            $this->getCommandBus()->handle(new BulkDeleteCmsPageCategoryCommand($cmsCategoriesToDelete));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->redirectToRoute('admin_cms_pages_index', [
            'cmsCategoryParentId' => $cmsCategoryParentId,
        ]);
    }

    /**
     * Updates cms page category position.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"cmsCategoryParentId"},
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"cmsCategoryParentId"}
     * )
     *
     * @param Request $request
     * @param int $cmsCategoryParentId
     *
     * @return RedirectResponse
     */
    public function updateCmsCategoryPositionAction(Request $request, $cmsCategoryParentId)
    {
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

            return $this->redirectToRoute('admin_cms_pages_index', [
                'cmsCategoryParentId' => $cmsCategoryParentId,
            ]);
        }

        $updater = $this->get('prestashop.core.grid.position.doctrine_grid_position_updater');

        try {
            $updater->update($positionUpdate);
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('admin_cms_pages_index', [
            'cmsCategoryParentId' => $cmsCategoryParentId,
        ]);
    }

    /**
     * Toggles cms page category status.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *      redirectRoute="admin_cms_pages_index",
     *      redirectQueryParamsToKeep={"cmsCategoryParentId"},
     *      message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"cmsCategoryParentId"}
     * )
     *
     * @param int $cmsCategoryParentId
     * @param int $cmsCategoryId
     *
     * @return RedirectResponse
     */
    public function toggleCmsCategoryAction($cmsCategoryParentId, $cmsCategoryId)
    {
        try {
            $cmsPageCategoryId = new CmsPageCategoryId($cmsCategoryId);
            $this->getCommandBus()->handle(new ToggleCmsPageCategoryStatusCommand($cmsPageCategoryId));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->redirectToRoute('admin_cms_pages_index', [
            'cmsCategoryParentId' => $cmsCategoryParentId,
        ]);
    }

    /**
     * Changes multiple cms page category statuses to enabled.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"cmsCategoryParentId"},
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"cmsCategoryParentId"}
     * )
     *
     * @param int $cmsCategoryParentId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkCmsPageStatusEnableAction($cmsCategoryParentId, Request $request)
    {
        $cmsCategoriesToEnable = $request->request->get('cms_page_category_bulk');

        try {
            $this->getCommandBus()->handle(new BulkEnableCmsPageCategoryCommand($cmsCategoriesToEnable));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->redirectToRoute('admin_cms_pages_index', [
            'cmsCategoryParentId' => $cmsCategoryParentId,
        ]);
    }

    /**
     * Changes multiple cms page category statuses to disabled.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"cmsCategoryParentId"},
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(
     *     redirectRoute="admin_cms_pages_index",
     *     redirectQueryParamsToKeep={"cmsCategoryParentId"}
     * )
     *
     * @param int $cmsCategoryParentId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkCmsPageStatusDisableAction($cmsCategoryParentId, Request $request)
    {
        $cmsCategoriesToDisable = $request->request->get('cms_page_category_bulk');

        try {
            $this->getCommandBus()->handle(new BulkDisableCmsPageCategoryCommand($cmsCategoriesToDisable));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->redirectToRoute('admin_cms_pages_index', [
            'cmsCategoryParentId' => $cmsCategoryParentId,
        ]);
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
                    '%id%' => $exception->getCmsPageCategoryId()->getValue(),
                ]
            );
        }

        $exceptionType = get_class($exception);
        if (isset($exceptionTypeDictionary[$exceptionType])) {
            return $exceptionTypeDictionary[$exceptionType];
        }

        return $this->trans('Unexpected error occurred.', 'Admin.Notifications.Error');
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
                CmsPageCategoryConstraintException::MISSING_BULK_DATA => $this->trans(
                    'You must select at least one element to delete.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];

        $exceptionType = get_class($exception);
        $statusCode = $exception->getCode();

        if (isset($exceptionTypeDictionary[$exceptionType][$statusCode])) {
            return $exceptionTypeDictionary[$exceptionType][$statusCode];
        }

        return $this->trans('Unexpected error occurred.', 'Admin.Notifications.Error');
    }
}
