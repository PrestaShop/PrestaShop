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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters\AuthorizationServer;

use PrestaShop\PrestaShop\Core\Search\Filters\AuthorizedApplicationsFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages the "Configure > Advanced Parameters > Authorization Server" page.
 */
class ApplicationController extends FrameworkBundleAdminController
{
    /**
     * @param AuthorizedApplicationsFilters $filters the list of filters from the request
     *
     * @return Response
     */
    public function indexAction(AuthorizedApplicationsFilters $filters): Response
    {
        $gridAuthorizedApplicationFactory = $this->get('prestashop.core.grid.factory.authorized_application');
        $grid = $gridAuthorizedApplicationFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/AuthorizationServer/index.html.twig', [
            'help_link' => $this->generateSidebarLink('AdminAuthorizationServer'),
            'layoutTitle' => $this->trans('Authorization Server Management', 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getApplicationToolbarButtons(),
            'grid' => $this->presentGrid($grid),
        ]);
    }

    /**
     * @return array
     */
    private function getApplicationToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['addApplication'] = [
            'href' => $this->generateUrl('admin_authorized_applications_create'),
            'desc' => $this->trans('Add new authorized app', 'Admin.Actions'),
            'icon' => 'add_circle_outline',
            'class' => 'btn-primary',
        ];

        $toolbarButtons['addApiAccess'] = [
            'href' => $this->generateUrl('admin_api_accesses_create'),
            'desc' => $this->trans('Add new Api access', 'Admin.Actions'),
            'icon' => 'add_circle_outline',
            'class' => 'btn-primary',
        ];

        return $toolbarButtons;
    }
}
