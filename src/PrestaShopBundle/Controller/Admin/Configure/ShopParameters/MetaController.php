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

use Exception;
use PrestaShop\PrestaShop\Core\Search\Filters\MetaFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MetaController is responsible for page display and all actions used in Configure -> Shop parameters ->
 * Traffic & Seo -> Seo & Urls tab.
 */
class MetaController extends FrameworkBundleAdminController
{
    /**
     * responsible for displaying page content.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @Template("@PrestaShop/Admin/Configure/ShopParameters/TrafficSeo/meta.html.twig")
     *
     * @param MetaFilters $filters
     *
     * @return array
     *
     * @throws \Exception
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

        $htaccessFileChecker = $this->get('prestashop.core.util.url.htaccess_file_checker');
        $robotsTextFileChecker = $this->get('prestashop.core.util.url.robots_text_file_checker');

        $hostingInformation = $this->get('prestashop.adapter.hosting_information');

        $defaultRoutesProvider = $this->get('prestashop.adapter.data_provider.default_route');
        return [
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_meta_list_create'),
                    'desc' => $this->trans('Add a new page', 'Admin.Shopparameters.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'grid' => $presentedGrid,
            'metaForm' => $metaForm->createView(),
            'robotsForm' => $this->createFormBuilder()->getForm()->createView(),
            'routeKeywords' => $defaultRoutesProvider->getKeywords(),
            'isModRewriteActive' => $tools->isModRewriteActive(),
            'isHtaccessFileValid' => $htaccessFileChecker->isValidFile(),
            'isRobotsTextFileValid' => $robotsTextFileChecker->isValidFile(),
            'isShopFeatureActive' => $context->isShopFeatureActive(),
            'isHostMode' => $hostingInformation->isHostMode(),
        ];
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

        return $this->redirectToRoute('admin_meta', ['filters' => $filters]);
    }

    /**
     * Redirects to page where new record of meta list can be created.
     *
     * @return RedirectResponse
     */
    public function createAction()
    {
        $legacyContext = $this->get('prestashop.adapter.legacy.context');
        //@todo: this action should point to new add page
        $legacyLink = $legacyContext->getAdminLink(
                'AdminMeta'
            ) . '&addmeta';

        return $this->redirect($legacyLink);
    }

    /**
     * Redirects to page where list record can be edited.
     *
     * @param int $metaId
     *
     * @return RedirectResponse
     */
    public function editAction($metaId)
    {
        //@todo: this action should point to new add page
        $legacyLink = $this->getAdminLink('AdminMeta', [
            'id_meta' => $metaId,
            'updatemeta' => 1,
        ]);

        return $this->redirect($legacyLink);
    }

    public function deleteSingleListItemAction()
    {
        // todo: implement
    }

    public function deleteMultipleListItemsAction()
    {
        // todo: implement
    }

    /**
     * Submits settings forms.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function processFormAction(Request $request)
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

        return $this->redirectToRoute('admin_meta');
    }

    /**
     * Generates robots.txt file.
     *
     * @return RedirectResponse
     */
    public function generateRobotsTextFileAction()
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
                        '%filename%' => $rootDir.'/robots.txt'
                    ]
                )
            );

            return $this->redirectToRoute('admin_meta');
        }

        $this->addFlash(
            'success',
            $this->trans('Successful update.', 'Admin.Notifications.Success')
        );

        return $this->redirectToRoute('admin_meta');
    }
}
