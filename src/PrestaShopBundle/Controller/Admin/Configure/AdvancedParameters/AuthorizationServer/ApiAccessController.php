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

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Exception\NotImplementedException;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages the "Configure > Advanced Parameters > Authorization Server > Api Access" page.
 *
 * @experimental
 */
class ApiAccessController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('delete', request.get('_legacy_controller')) || is_granted('read', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/AuthorizationServer/index.html.twig', [
            'help_link' => $this->generateSidebarLink('AdminAuthorizationServer'),
            'layoutTitle' => $this->trans('Authorization Server Management', 'Admin.Navigation.Menu'),
            'showContentHeader' => true,
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getApiAccessesToolbarButtons(),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     */
    public function createAction(): void
    {
        // TODO: Implement createAction() method in create PR.
        throw new NotImplementedException();
    }

    /**
     * @return array
     */
    private function getApiAccessesToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['addApiAccess'] = [
            'href' => $this->generateUrl('admin_api_accesses_create'),
            'desc' => $this->trans('Add new API access', 'Admin.Actions'),
            'icon' => 'add_circle_outline',
            'class' => 'btn-primary',
        ];

        return $toolbarButtons;
    }
}
