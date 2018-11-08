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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Search\Filters\MetaFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MetaController is responsible for page display and all actions used in Configure -> Shop parameters ->
 * Traffic & Seo -> Seo & Urls tab.
 */
class MetaController extends FrameworkBundleAdminController
{
    /**
     * Shows index Meta page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param MetaFilters $filters
     *
     * @return Response
     */
    public function indexAction(MetaFilters $filters)
    {
        $seoUrlsGridFactory = $this->get('prestashop.core.grid.factory.meta');
        $grid = $seoUrlsGridFactory->getGrid($filters);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');
        $presentedGrid = $gridPresenter->present($grid);

        $metaForm = $this->get('prestashop.admin.meta_settings.form_handler')->getForm();

        $tools = $this->get('prestashop.adapter.tools');
        $context = $this->get('prestashop.adapter.shop.context');

        $urlFileChecker = $this->get('prestashop.core.util.url.url_file_checker');

        $hostingInformation = $this->get('prestashop.adapter.hosting_information');

        $defaultRoutesProvider = $this->get('prestashop.adapter.data_provider.default_route');

        $isShopContext = $context->isShopContext();
        $isShopFeatureActive = $this->get('prestashop.adapter.multistore_feature')->isActive();

        $helperBlockLinkProvider = $this->get('prestashop.core.helper_doc.meta_page_link_provider');
        $metaDataProvider = $this->get('prestashop.adapter.meta.data_provider');

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/TrafficSeo/Meta/index.html.twig', [
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->getAdminLink('AdminMeta', ['addmeta' => '']),
                    'desc' => $this->trans('Add a new page', 'Admin.Shopparameters.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'grid' => $presentedGrid,
            'metaForm' => $metaForm->createView(),
            'robotsForm' => $this->createFormBuilder()->getForm()->createView(),
            'routeKeywords' => $defaultRoutesProvider->getKeywords(),
            'isModRewriteActive' => $tools->isModRewriteActive(),
            'isHtaccessFileValid' => $urlFileChecker->isHtaccessFileWritable(),
            'isRobotsTextFileValid' => $urlFileChecker->isRobotsFileWritable(),
            'isShopContext' => $isShopContext,
            'isShopFeatureActive' => $isShopFeatureActive,
            'isHostMode' => $hostingInformation->isHostMode(),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminMeta'),
            'helperDocLink' => $helperBlockLinkProvider->getLink(),
            'indexPageId' => $metaDataProvider->getIdByPage('index'),
        ]);
    }

    /**
     * Used for applying filtering actions.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.meta');
        $definitionFactory = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $searchParametersForm = $gridFilterFormFactory->create($definitionFactory);
        $searchParametersForm->handleRequest($request);

        $filters = [];
        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_metas_index', ['filters' => $filters]);
    }

    /**
     * Points to the form where new record of meta list can be created.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message="You do not have permission to add this.")
     *
     * @return Response
     */
    public function createAction()
    {
        $legacyLink = $this->getAdminLink('AdminMeta', [
            'addmeta' => 1,
        ]);

        return $this->redirect($legacyLink);
    }

    /**
     * Redirects to page where list record can be edited.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param int $metaId
     *
     * @return RedirectResponse
     */
    public function editAction($metaId)
    {
        $legacyLink = $this->getAdminLink('AdminMeta', [
            'id_meta' => $metaId,
            'updatemeta' => 1,
        ]);

        return $this->redirect($legacyLink);
    }

    /**
     * Removes single element from meta list.
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="You do not have permission to delete this.")
     * @DemoRestricted(redirectRoute="admin_metas_index")
     *
     * @param int $metaId
     *
     * @return RedirectResponse
     */
    public function deleteAction($metaId)
    {
        $metaEraser = $this->get('prestashop.adapter.meta.meta_eraser');
        $errors = $metaEraser->erase([$metaId]);

        if (!empty($errors)) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_metas_index');
    }

    /**
     * Removes multiple records from meta list.
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="You do not have permission to delete this.")
     * @DemoRestricted(redirectRoute="admin_metas_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request)
    {
        $metaToDelete = $request->request->get('meta_bulk');

        $metaEraser = $this->get('prestashop.adapter.meta.meta_eraser');
        $errors = $metaEraser->erase($metaToDelete);

        if (!empty($errors)) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        }

        return $this->redirectToRoute('admin_metas_index');
    }

    /**
     * Submits settings forms.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     * @DemoRestricted(redirectRoute="admin_metas_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function saveOptionsAction(Request $request)
    {
        $formHandler = $this->get('prestashop.admin.meta_settings.form_handler');
        $configurationForm = $formHandler->getForm();

        $configurationForm->handleRequest($request);

        if ($configurationForm->isSubmitted()) {
            $errors = $formHandler->save($configurationForm->getData());

            if (!empty($errors)) {
                $this->flashErrors($errors);
            } else {
                $this->addFlash(
                    'success',
                    $this->trans('The settings have been successfully updated.', 'Admin.Notifications.Success')
                );
            }
        }

        return $this->redirectToRoute('admin_metas_index');
    }

    /**
     * Generates robots.txt file for Front Office.
     *
     * @AdminSecurity("is_granted(['create', 'update', 'delete'], request.get('_legacy_controller'))")
     * @DemoRestricted(redirectRoute="admin_metas_index")
     *
     * @return RedirectResponse
     */
    public function generateRobotsFileAction()
    {
        $robotsTextFileGenerator = $this->get('prestashop.adapter.file.robots_text_file_generator');

        $rootDir = $this->get('prestashop.adapter.legacy.configuration')->get('_PS_ROOT_DIR_');

        if (!$robotsTextFileGenerator->generateFile()) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot write into file: %filename%. Please check write permissions.',
                    'Admin.Notifications.Error',
                    [
                        '%filename%' => $rootDir . '/robots.txt',
                    ]
                )
            );

            return $this->redirectToRoute('admin_metas_index');
        }

        $this->addFlash(
            'success',
            $this->trans('Successful update.', 'Admin.Notifications.Success')
        );

        return $this->redirectToRoute('admin_metas_index');
    }
}
