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

use PrestaShop\PrestaShop\Core\Search\Filters\TitleFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible of "Configure > Shop Parameters > Customer Settings > Titles" page.
 */
class TitleController extends FrameworkBundleAdminController
{
    /**
     * Show customer titles page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request, TitleFilters $filters): Response
    {
        $titleGridFactory = $this->get('prestashop.core.grid.factory.title');
        $titleGrid = $titleGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/CustomerSettings/Title/index.html.twig', [
            'titleGrid' => $this->presentGrid($titleGrid),
            'layoutTitle' => $this->trans('Titles', 'Admin.Navigation.Menu'),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    /**
     * Displays and handles currency form.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_title_index",
     *     message="You need permission to create this."
     * )
     *
     * @return Response
     */
    public function createAction(): Response
    {
        return $this->redirect(
            $this->getContext()->link->getAdminLink(
                'AdminGenders',
                true,
                [],
                [
                    'addgender' => '',
                ]
            )
        );
    }

    /**
     * Displays title form.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_title_index",
     *     message="You need permission to edit this."
     * )
     *
     * @return Response
     */
    public function editAction(int $titleId): Response
    {
        return $this->redirect(
            $this->getContext()->link->getAdminLink(
                'AdminGenders',
                true,
                [],
                [
                    'updategender' => '',
                    'id_gender' => $titleId,
                ]
            )
        );
    }

    /**
     * Deletes title.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_title_index",
     *     message="You need permission to delete this."
     * )
     * @DemoRestricted(redirectRoute="admin_title_index")
     *
     * @return RedirectResponse
     */
    public function deleteAction(int $titleId): RedirectResponse
    {
        return $this->redirect(
            $this->getContext()->link->getAdminLink(
                'AdminGenders',
                true,
                [],
                [
                    'deletegender' => '',
                    'id_gender' => $titleId,
                ]
            )
        );
    }

    /**
     * Deletes titles in bulk action
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_title_index")
     * @DemoRestricted(redirectRoute="admin_title_index")
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(): RedirectResponse
    {
        return $this->redirectToRoute('admin_title_index');
    }
}
