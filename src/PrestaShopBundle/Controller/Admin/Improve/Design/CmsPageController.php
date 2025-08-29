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

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use Exception;
use PrestaShop\PrestaShop\Adapter\Shop\Url\CmsProvider;
use PrestaShop\PrestaShop\Core\CMS\CmsPageViewDataProviderInterface;
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
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Query\GetCmsCategoryIdForRedirection;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Query\GetCmsPageForEditing;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\QueryResult\EditableCmsPage;
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
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CmsPageCategoryDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CmsPageDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Search\Filters\CmsPageCategoryFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\CmsPageFilters;
use PrestaShop\PrestaShop\Core\Util\HelperCard\DocumentationLinkProviderInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CmsPageController is responsible for handling the logic in "Improve -> Design -> pages" page.
 */
class CmsPageController extends PrestaShopAdminController
{
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            CmsPageCategoryDefinitionFactory::GRID_ID => CmsPageCategoryDefinitionFactory::class,
            CmsPageDefinitionFactory::GRID_ID => CmsPageDefinitionFactory::class,
        ];
    }

    /**
     * Responsible for displaying page content.
     *
     * @param CmsPageCategoryFilters $categoryFilters
     * @param CmsPageFilters $cmsFilters
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        CmsPageCategoryFilters $categoryFilters,
        CmsPageFilters $cmsFilters,
        Request $request,
        DocumentationLinkProviderInterface $helperBlockLinkProvider,
        #[Autowire(service: 'prestashop.core.cms_page.data_provider.cms_page_view')]
        CmsPageViewDataProviderInterface $cmsPageViewDataProvider,
        #[Autowire(service: 'prestashop.core.grid.factory.cms_page_category')]
        GridFactoryInterface $cmsCategoryGridFactory,
        #[Autowire(service: 'prestashop.core.grid.factory.cms_page')]
        GridFactoryInterface $cmsGridFactory,
    ): Response {
        $cmsCategoryParentId = (int) $categoryFilters->getFilters()['id_cms_category_parent'];
        $viewData = $cmsPageViewDataProvider->getView($cmsCategoryParentId);
        $cmsCategoryGrid = $cmsCategoryGridFactory->getGrid($categoryFilters);

        $cmsGrid = $cmsGridFactory->getGrid($cmsFilters);

        $showcaseCardIsClosed = $this->dispatchQuery(
            new GetShowcaseCardIsClosed(
                (int) $this->getEmployeeContext()->getEmployee()->getId(),
                ShowcaseCard::CMS_PAGES_CARD
            )
        );

        return $this->render(
            '@PrestaShop/Admin/Improve/Design/Cms/index.html.twig',
            [
                'cmsCategoryGrid' => $this->presentGrid($cmsCategoryGrid),
                'cmsGrid' => $this->presentGrid($cmsGrid),
                'cmsPageView' => $viewData,
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'helperDocLink' => $helperBlockLinkProvider->getLink('cms_pages'),
                'cmsPageShowcaseCardName' => ShowcaseCard::CMS_PAGES_CARD,
                'layoutHeaderToolbarBtn' => $this->getCmsPageIndexToolbarButtons($cmsCategoryParentId),
                'showcaseCardIsClosed' => $showcaseCardIsClosed,
            ]
        );
    }

    /**
     * @deprecated since 1.7.8 and will be removed in next major. Use CommonController:searchGridAction instead
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function searchAction(Request $request): RedirectResponse
    {
        $filterId = CmsPageCategoryDefinitionFactory::GRID_ID;
        if ($request->request->has(CmsPageDefinitionFactory::GRID_ID)) {
            $filterId = CmsPageDefinitionFactory::GRID_ID;
        }

        return $this->buildSearchResponse(
            $this->container->get($filterId),
            $request,
            $filterId,
            'admin_cms_pages_index',
            [
                'id_cms_category',
            ]
        );
    }

    /**
     * Creates cms page
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to add this.')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.shop.url.cms_provider')]
        CmsProvider $urlProvider,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.cms_page_form_builder')]
        FormBuilderInterface $cmsPageFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.cms_page_form_handler')]
        FormHandlerInterface $cmsPageFormHandler,
    ): Response {
        $categoryParentId = $request->query->get('id_cms_category');
        $formData = [];
        if ($categoryParentId) {
            $formData['page_category_id'] = $categoryParentId;
        }
        $form = $cmsPageFormBuilder->getForm($formData, [
            'cms_preview_url' => $urlProvider->getUrl(0, '{friendly-url}'),
        ]);
        $form->handleRequest($request);

        try {
            $result = $cmsPageFormHandler->handle($form);
            $cmsPageId = $result->getIdentifiableObjectId();

            if (null !== $cmsPageId) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful creation', [], 'Admin.Notifications.Success')
                );
                if (!$request->request->has('save-and-preview')) {
                    return $this->redirectToParentIndexPageByCmsPageId($cmsPageId);
                }

                return $this->redirectToRoute('admin_cms_pages_edit', [
                    'cmsPageId' => $cmsPageId,
                    'open_preview' => 1,
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($e, $this->getErrorMessages())
            );
        }

        return $this->render(
            '@PrestaShop/Admin/Improve/Design/Cms/add.html.twig',
            [
                'cmsPageForm' => $form->createView(),
                'cmsCategoryParentId' => $categoryParentId,
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'layoutTitle' => $this->trans('New page', [], 'Admin.Navigation.Menu'),
            ]
        );
    }

    /**
     * @param Request $request
     * @param int $cmsPageId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to edit this.')]
    public function editAction(
        Request $request,
        int $cmsPageId,
        #[Autowire(service: 'prestashop.adapter.shop.url.cms_provider')]
        CmsProvider $urlProvider,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.cms_page_form_builder')]
        FormBuilderInterface $cmsPageFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.cms_page_form_handler')]
        FormHandlerInterface $cmsPageFormHandler,
    ): Response {
        $cmsPageId = (int) $cmsPageId;

        try {
            /** @var EditableCmsPage $editableCmsPage */
            $editableCmsPage = $this->dispatchQuery(new GetCmsPageForEditing($cmsPageId));
            $previewUrl = $editableCmsPage->getPreviewUrl();

            $form = $cmsPageFormBuilder->getFormFor($cmsPageId, [], [
                'action' => $this->generateUrl('admin_cms_pages_edit', [
                    'cmsPageId' => $cmsPageId,
                ]),
                'cms_preview_url' => $urlProvider->getUrl($cmsPageId, '{friendly-url}'),
            ]);
            $form->handleRequest($request);
        } catch (Exception $e) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($e, $this->getErrorMessages())
            );

            return $this->redirectToRoute('admin_cms_pages_index');
        }

        try {
            $result = $cmsPageFormHandler->handleFor($cmsPageId, $form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful update', [], 'Admin.Notifications.Success')
                );

                if ($request->request->has('save-and-preview')) {
                    return $this->redirectToRoute('admin_cms_pages_edit', [
                        'cmsPageId' => $cmsPageId,
                        'open_preview' => 1,
                    ]);
                }

                return $this->redirectToParentIndexPageByCmsPageId($cmsPageId);
            }
        } catch (Exception $e) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($e, $this->getErrorMessages())
            );
        }

        return $this->render(
            '@PrestaShop/Admin/Improve/Design/Cms/edit.html.twig',
            [
                'cmsPageId' => $cmsPageId,
                'cmsPageForm' => $form->createView(),
                'cmsCategoryParentId' => $request->get('id_cms_category'),
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'previewUrl' => $previewUrl,
                'layoutTitle' => $this->trans(
                    'Editing page %name%',
                    [
                        '%name%' => $editableCmsPage->getLocalizedTitle()[$this->getLanguageContext()->getId()],
                    ],
                    'Admin.Navigation.Menu'
                ),
            ]
        );
    }

    /**
     * Displays cms category page form and handles create new cms page category logic.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to add this.')]
    public function createCmsCategoryAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.cms_page_category_form_builder')]
        FormBuilderInterface $cmsPageCategoryFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.cms_page_category_form_handler')]
        FormHandlerInterface $cmsPageCategoryFormHandler,
    ): Response {
        $cmsPageCategoryForm = $cmsPageCategoryFormBuilder->getForm();

        $cmsPageCategoryForm->handleRequest($request);

        try {
            $result = $cmsPageCategoryFormHandler->handle($cmsPageCategoryForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful creation', [], 'Admin.Notifications.Success')
                );

                return $this->redirectToIndexPageById($result->getIdentifiableObjectId());
            }
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        return $this->render(
            '@PrestaShop/Admin/Improve/Design/Cms/create_category.html.twig',
            [
                'cmsPageCategoryForm' => $cmsPageCategoryForm->createView(),
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'layoutTitle' => $this->trans('New category', [], 'Admin.Navigation.Menu'),
            ]
        );
    }

    /**
     * Displays cms category page form and handles update cms page category logic.
     *
     * @param int $cmsCategoryId
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to edit this.')]
    public function editCmsCategoryAction(
        int $cmsCategoryId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.cms_page_category_form_builder')]
        FormBuilderInterface $cmsPageCategoryFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.cms_page_category_form_handler')]
        FormHandlerInterface $cmsPageCategoryFormHandler,
    ): Response {
        try {
            $cmsPageCategoryForm = $cmsPageCategoryFormBuilder->getFormFor((int) $cmsCategoryId);
            $cmsCategoryParentId = $this->getParentCategoryId((int) $cmsCategoryId)->getValue();
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );

            return $this->redirectToRoute('admin_cms_pages_index');
        }

        try {
            $cmsPageCategoryForm->handleRequest($request);
            $result = $cmsPageCategoryFormHandler->handleFor((int) $cmsCategoryId, $cmsPageCategoryForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful update', [], 'Admin.Notifications.Success')
                );

                return $this->redirectToIndexPageById($result->getIdentifiableObjectId());
            }
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );

            if ($exception instanceof CmsPageCategoryNotFoundException) {
                return $this->redirectToParentIndexPage((int) $cmsCategoryId);
            }
        }

        return $this->render(
            '@PrestaShop/Admin/Improve/Design/Cms/edit_category.html.twig',
            [
                'cmsPageCategoryForm' => $cmsPageCategoryForm->createView(),
                'cmsCategoryParentId' => $cmsCategoryParentId,
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'layoutTitle' => $this->trans(
                    'Editing category %name%',
                    [
                        '%name%' => $cmsPageCategoryForm->getData()['name'][$this->getLanguageContext()->getId()],
                    ],
                    'Admin.Navigation.Menu'
                ),
            ]
        );
    }

    /**
     * Deletes cms page category and all its children categories.
     *
     * @param int $cmsCategoryId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'])]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to delete this.')]
    public function deleteCmsCategoryAction(int $cmsCategoryId): RedirectResponse
    {
        try {
            $this->dispatchCommand(
                new DeleteCmsPageCategoryCommand((int) $cmsCategoryId)
            );

            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        return $this->redirectToParentIndexPage((int) $cmsCategoryId);
    }

    /**
     * Deletes multiple cms page categories.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'])]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to delete this.')]
    public function deleteBulkCmsCategoryAction(Request $request): RedirectResponse
    {
        $cmsCategoriesToDelete = $request->request->all('cms_page_category_bulk');

        try {
            $cmsCategoriesToDelete = array_map(function ($item) { return (int) $item; }, $cmsCategoriesToDelete);

            $this->dispatchCommand(
                new BulkDeleteCmsPageCategoryCommand($cmsCategoriesToDelete)
            );

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        return $this->redirectToParentIndexPageByCategoryBulkIds($cmsCategoriesToDelete);
    }

    /**
     * Updates cms page category position.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'])]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to edit this.')]
    public function updateCmsCategoryPositionAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.cms_page_category.position_definition')]
        PositionDefinition $positionDefinition
    ): RedirectResponse {
        $cmsCategoryParentId = $request->query->getInt('id_cms_category') ?:
            CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID
        ;

        $positionsData = [
            'positions' => $request->request->all('positions'),
            'parentId' => $cmsCategoryParentId,
        ];

        try {
            $this->updateGridPosition($positionDefinition, $positionsData);
            $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
        } catch (PositionDataException $e) {
            $errors = [$e->toArray()];
            $this->addFlashErrors($errors);

            return $this->redirectToIndexPageById($cmsCategoryParentId);
        } catch (PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->addFlashErrors($errors);
        }

        return $this->redirectToIndexPageById($cmsCategoryParentId);
    }

    /**
     * Updates cms page listing position.
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'])]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to edit this.')]
    public function updateCmsPositionAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.cms_page.position_definition')]
        PositionDefinition $positionDefinition
    ): RedirectResponse {
        $cmsCategoryParentId = $request->query->getInt('id_cms_category') ?:
            CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID
        ;

        $positionsData = [
            'positions' => $request->request->all('positions'),
            'parentId' => $cmsCategoryParentId,
        ];

        try {
            $this->updateGridPosition($positionDefinition, $positionsData);
            $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
        } catch (PositionDataException $e) {
            $errors = [$e->toArray()];
            $this->addFlashErrors($errors);

            return $this->redirectToParentIndexPage($cmsCategoryParentId);
        } catch (PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->addFlashErrors($errors);
        }

        return $this->redirectToIndexPageById($cmsCategoryParentId);
    }

    /**
     * Toggles cms page category status.
     *
     * @param int $cmsCategoryId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'])]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to edit this.')]
    public function toggleCmsCategoryAction($cmsCategoryId): RedirectResponse
    {
        try {
            $this->dispatchCommand(
                new ToggleCmsPageCategoryStatusCommand((int) $cmsCategoryId)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        return $this->redirectToParentIndexPage((int) $cmsCategoryId);
    }

    /**
     * Changes multiple cms page category statuses to enabled.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'])]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to edit this.')]
    public function bulkCmsPageCategoryStatusEnableAction(Request $request): RedirectResponse
    {
        $cmsCategoriesToEnable = $request->request->all('cms_page_category_bulk');
        try {
            $cmsCategoriesToEnable = array_map(function ($item) { return (int) $item; }, $cmsCategoriesToEnable);

            $this->dispatchCommand(
                new BulkEnableCmsPageCategoryCommand($cmsCategoriesToEnable)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        return $this->redirectToParentIndexPageByCategoryBulkIds($cmsCategoriesToEnable);
    }

    /**
     * Changes multiple cms page category statuses to disabled.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'])]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to edit this.')]
    public function bulkCmsPageCategoryStatusDisableAction(Request $request): RedirectResponse
    {
        $cmsCategoriesToDisable = $request->request->all('cms_page_category_bulk');
        try {
            $cmsCategoriesToDisable = array_map(
                function ($item) {
                    return (int) $item;
                },
                $cmsCategoriesToDisable
            );
            $this->dispatchCommand(
                new BulkDisableCmsPageCategoryCommand($cmsCategoriesToDisable)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        return $this->redirectToParentIndexPageByCategoryBulkIds($cmsCategoriesToDisable);
    }

    /**
     * Toggles cms page listing status.
     *
     * @param int $cmsId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'])]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to edit this.')]
    public function toggleCmsAction($cmsId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new ToggleCmsPageStatusCommand((int) $cmsId));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        return $this->redirectToParentIndexPageByCmsPageId($cmsId);
    }

    /**
     * Disables multiple cms pages.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'])]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to edit this.')]
    public function bulkDisableCmsPageStatusAction(Request $request): RedirectResponse
    {
        $cmsPagesToDisable = $request->request->all('cms_page_bulk');

        try {
            $cmsPagesToDisable = array_map(function ($item) { return (int) $item; }, $cmsPagesToDisable);

            $this->dispatchCommand(
                new BulkDisableCmsPageCommand($cmsPagesToDisable)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        return $this->redirectToParentIndexPageByBulkIds($cmsPagesToDisable);
    }

    /**
     * Enables multiple cms pages.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'])]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to edit this.')]
    public function bulkEnableCmsPageStatusAction(Request $request): RedirectResponse
    {
        $cmsPagesToDisable = $request->request->all('cms_page_bulk');

        try {
            $cmsPagesToDisable = array_map(
                function ($item) {
                    return (int) $item;
                },
                $cmsPagesToDisable
            );

            $this->dispatchCommand(
                new BulkEnableCmsPageCommand($cmsPagesToDisable)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        return $this->redirectToParentIndexPageByBulkIds($cmsPagesToDisable);
    }

    /**
     * Deletes multiple cms pages.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'])]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to delete this.')]
    public function bulkDeleteCmsPageAction(Request $request): RedirectResponse
    {
        $cmsPagesToDisable = $request->request->all('cms_page_bulk');

        $redirectResponse = $this->redirectToParentIndexPageByBulkIds($cmsPagesToDisable);

        try {
            $cmsPagesToDisable = array_map(
                function ($item) {
                    return (int) $item;
                },
                $cmsPagesToDisable
            );

            $this->dispatchCommand(
                new BulkDeleteCmsPageCommand($cmsPagesToDisable)
            );

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        return $redirectResponse;
    }

    /**
     * Deletes cms page by given id.
     *
     * @param int $cmsId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'])]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_cms_pages_index', redirectQueryParamsToKeep: ['id_cms_category'], message: 'You do not have permission to delete this.')]
    public function deleteCmsAction($cmsId): RedirectResponse
    {
        $redirectResponse = $this->redirectToParentIndexPageByCmsPageId($cmsId);

        try {
            $this->dispatchCommand(new DeleteCmsPageCommand((int) $cmsId));

            $this->addFlash(
                'success',
                $this->trans('Successful deletion', [], 'Admin.Notifications.Success')
            );
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        return $redirectResponse;
    }

    /**
     * The redirection URL is generation thanks to the ProductPreviewProvider however it can't be used in the grid
     * since the LinkRowAction expects a symfony route, so this action is merely used as a proxy for symfony routing
     * and redirects to the appropriate product preview url.
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function previewAction(
        int $cmsPageId,
        #[Autowire(service: 'prestashop.adapter.shop.url.cms_provider')]
        CmsProvider $previewUrlProvider,
    ): RedirectResponse {
        $previewUrl = $previewUrlProvider->getUrl($cmsPageId);

        return $this->redirect($previewUrl);
    }

    /**
     * This function is used for redirecting to the specific cms page category page. It uses bulk action ids which
     * share the same parent cms category in all cases.
     *
     * @param array $cmsPageCategoryIds
     *
     * @return RedirectResponse
     */
    private function redirectToParentIndexPageByCategoryBulkIds(array $cmsPageCategoryIds): RedirectResponse
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
    private function redirectToParentIndexPageByBulkIds(array $cmsPageIds): RedirectResponse
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
     */
    private function redirectToParentIndexPage($cmsPageCategoryId): RedirectResponse
    {
        try {
            $cmsPageCategoryParentId = $this->getParentCategoryId($cmsPageCategoryId)->getValue();
        } catch (CmsPageCategoryException) {
            $cmsPageCategoryParentId = CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID;
        }

        return $this->redirectToIndexPageById($cmsPageCategoryParentId);
    }

    /**
     * @param int $cmsPageId
     *
     * @return RedirectResponse
     */
    private function redirectToParentIndexPageByCmsPageId($cmsPageId): RedirectResponse
    {
        try {
            $cmsCategoryId = $this->dispatchQuery(new GetCmsCategoryIdForRedirection((int) $cmsPageId))->getValue();
        } catch (CmsPageException) {
            $cmsCategoryId = CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID;
        }

        return $this->redirectToIndexPageById($cmsCategoryId);
    }

    /**
     * Redirects to index page by given id.
     *
     * @param int $cmsPageCategoryId
     *
     * @return RedirectResponse
     */
    private function redirectToIndexPageById($cmsPageCategoryId): RedirectResponse
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
    private function getParentCategoryId($cmsPageCategoryChildId): CmsPageCategoryId
    {
        /** @var CmsPageCategoryId $cmsPageCategoryParentId */
        $cmsPageCategoryParentId = $this->dispatchQuery(
            new GetCmsPageParentCategoryIdForRedirection($cmsPageCategoryChildId)
        );

        return $cmsPageCategoryParentId;
    }

    /**
     * Provides translatable error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            CmsPageNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            CannotToggleCmsPageException::class => $this->trans(
                'An error occurred while updating the status.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotDisableCmsPageException::class => $this->trans(
                'An error occurred while updating the status.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotEnableCmsPageException::class => $this->trans(
                'An error occurred while updating the status.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotDeleteCmsPageException::class => [
                CannotDeleteCmsPageException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    [],
                    'Admin.Notifications.Error'
                ),
                CannotDeleteCmsPageException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            CmsPageCategoryConstraintException::class => [
                CmsPageCategoryConstraintException::INVALID_BULK_DATA => $this->trans(
                    'You must select at least one element to delete.',
                    [],
                    'Admin.Notifications.Error'
                ),
                CmsPageCategoryConstraintException::CANNOT_MOVE_CATEGORY_TO_PARENT => $this->trans('The page Category cannot be moved here.', [], 'Admin.Design.Notification'),
                CmsPageCategoryConstraintException::MISSING_DEFAULT_LANGUAGE_FOR_NAME => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf('"%s"', $this->trans('Name', [], 'Admin.Global')),
                    ],
                    'Admin.Notifications.Error'
                ),
                CmsPageCategoryConstraintException::MISSING_DEFAULT_LANGUAGE_FOR_FRIENDLY_URL => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf('"%s"', $this->trans('Friendly URL', [], 'Admin.Global')),
                    ],
                    'Admin.Notifications.Error'
                ),
                CmsPageCategoryConstraintException::INVALID_CATEGORY_NAME => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf('"%s"', $this->trans('Name', [], 'Admin.Global')),
                    ],
                    'Admin.Notifications.Error'
                ),
                CmsPageCategoryConstraintException::INVALID_LINK_REWRITE => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf('"%s"', $this->trans('Friendly URL', [], 'Admin.Global')),
                    ],
                    'Admin.Notifications.Error'
                ),
                CmsPageCategoryConstraintException::INVALID_META_TITLE => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf('"%s"', $this->trans('Meta title', [], 'Admin.Global')),
                    ],
                    'Admin.Notifications.Error'
                ),
                CmsPageCategoryConstraintException::INVALID_DESCRIPTION => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf('"%s"', $this->trans('Description', [], 'Admin.Global')),
                    ],
                    'Admin.Notifications.Error'
                ),
                CmsPageCategoryConstraintException::INVALID_META_DESCRIPTION => $this->trans(
                    'The %s field is not valid',
                    [
                        sprintf('"%s"', $this->trans('Meta description', [], 'Admin.Global')),
                    ],
                    'Admin.Notifications.Error'
                ),
            ],
            CmsPageCategoryNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            CannotToggleCmsPageCategoryStatusException::class => $this->trans(
                'An error occurred while updating the status.',
                [],
                'Admin.Notifications.Error'
            ),
            CannotDeleteCmsPageCategoryException::class => [
                CannotDeleteCmsPageCategoryException::FAILED_BULK_DELETE => $this->trans(
                    'An error occurred while deleting this selection.',
                    [],
                    'Admin.Notifications.Error'
                ),
                CannotDeleteCmsPageCategoryException::FAILED_DELETE => $this->trans(
                    'An error occurred while deleting the object.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * @param int $cmsCategoryId
     *
     * @return array
     */
    private function getCmsPageIndexToolbarButtons($cmsCategoryId): array
    {
        $toolbarButtons = [];

        if ($cmsCategoryId !== CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID) {
            $toolbarButtons['edit_cms_category'] = [
                'href' => $this->generateUrl('admin_cms_pages_category_edit', ['cmsCategoryId' => $cmsCategoryId]),
                'desc' => $this->trans('Edit page category', [], 'Admin.Design.Help'),
                'icon' => 'mode_edit',
            ];
        }

        $toolbarButtons['add_cms_category'] = [
            'href' => $this->generateUrl('admin_cms_pages_category_create', ['id_cms_category' => $cmsCategoryId]),
            'desc' => $this->trans('Add new page category', [], 'Admin.Design.Help'),
            'icon' => 'add_circle_outline',
        ];

        $toolbarButtons['add_cms_page'] = [
            'href' => $this->generateUrl('admin_cms_pages_create', ['id_cms_category' => $cmsCategoryId]),
            'desc' => $this->trans('Add new page', [], 'Admin.Design.Help'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }
}
