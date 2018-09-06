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

use PrestaShop\PrestaShop\Core\Search\Filters\SeoUrlsFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MetaController is responsible for page display and all actions used in Configure -> Shop parameters ->
 * Traffic & Seo -> Seo & Urls tab.
 */
class MetaController extends FrameworkBundleAdminController
{
    /**
     * responsible for displaying page content
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @Template("@PrestaShop/Admin/Configure/ShopParameters/TrafficSeo/meta.html.twig")
     *
     * @param SeoUrlsFilters $filters
     *
     * @return array
     *
     * @throws \Exception
     */
    public function indexAction(SeoUrlsFilters $filters)
    {
        $seoUrlsGridFactory = $this->get('prestashop.core.grid.factory.meta');
        $grid = $seoUrlsGridFactory->getGrid($filters);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');
        $presentedGrid = $gridPresenter->present($grid);

        $seoUrlsForm = $this->get('prestashop.admin.seo_urls_settings.form_handler')->getForm();

        return [
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_meta_list_create'),
                    'desc' => $this->trans('Add a new page', 'Admin.Shopparameters.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'grid' => $presentedGrid,
            'seoUrlsForm' => $seoUrlsForm->createView()
        ];
    }

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

    public function createListAction()
    {
        $legacyContext = $this->get('prestashop.adapter.legacy.context');
        //@todo: this action should point to new add page
        $legacyLink = $legacyContext->getAdminLink(
                'AdminMeta'
            ) . '&addmeta';

        return $this->redirect($legacyLink);
    }

    public function editListAction($metaId)
    {
        $legacyContext = $this->get('prestashop.adapter.legacy.context');
        //@todo: this action should point to new add page
        $legacyLink = $legacyContext->getAdminLink(
                'AdminMeta',
                true,
                [
                    'id_meta' => $metaId,
                ]
            ) . '&updatemeta';

        return $this->redirect($legacyLink);
    }

    public function deleteSingleListItemAction()
    {
    }

    public function deleteMultipleListItemsAction()
    {
    }

    public function processSettingsFormAction()
    {
    }
}
