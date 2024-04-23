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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Adapter\Cache\MemcacheServerManager;
use PrestaShop\PrestaShop\Core\Security\Permission;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Advanced Parameters > Performance" servers block management.
 */
class MemcacheServerController extends FrameworkBundleAdminController
{
    public const CONTROLLER_NAME = 'AdminPerformance';

    /**
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function listAction()
    {
        return new JsonResponse($this->getMemcacheManager()->getServers());
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_servers_test')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function testAction(Request $request)
    {
        $queryValues = $request->query;

        if ($queryValues->has('server_ip') && $queryValues->has('server_port')) {
            $isValid = $this->getMemcacheManager()
                ->testConfiguration(
                    $queryValues->get('server_ip'),
                    $queryValues->getInt('server_port')
                );

            return new JsonResponse(['test' => $isValid]);
        }

        return new JsonResponse(['errors' => 'error'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_servers_test')]
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function addAction(Request $request)
    {
        if (!in_array(
            $this->authorizationLevel($this::CONTROLLER_NAME),
            [
                Permission::LEVEL_READ,
                Permission::LEVEL_UPDATE,
                Permission::LEVEL_CREATE,
                Permission::LEVEL_DELETE,
            ]
        )) {
            return new JsonResponse(
                [
                    'errors' => [
                        $this->trans('You do not have permission to create this.', 'Admin.Notifications.Error'),
                    ],
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $postValues = $request->request;

        if ($postValues->has('server_ip')
            && $postValues->has('server_port')
            && $postValues->has('server_weight')
            && $this->getMemcacheManager()->testConfiguration(
                $postValues->get('server_ip'),
                $postValues->getInt('server_port')
            )
        ) {
            $server = $this->getMemcacheManager()
                ->addServer(
                    $postValues->get('server_ip'),
                    $postValues->getInt('server_port'),
                    $postValues->get('server_weight')
                );

            return new JsonResponse($server, 201);
        }

        return new JsonResponse(
            [
                'errors' => [
                    $this->trans('The Memcached server cannot be added.', 'Admin.Advparameters.Notification'),
                ],
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_servers_test')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function deleteAction(Request $request)
    {
        if (!in_array(
            $this->authorizationLevel($this::CONTROLLER_NAME),
            [
                Permission::LEVEL_READ,
                Permission::LEVEL_UPDATE,
                Permission::LEVEL_CREATE,
                Permission::LEVEL_DELETE,
            ]
        )) {
            return new JsonResponse(
                [
                    'errors' => [
                        $this->trans('You do not have permission to delete this.', 'Admin.Notifications.Error'),
                    ],
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($request->request->has('server_id')) {
            $this->getMemcacheManager()->deleteServer($request->request->get('server_id'));

            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(
            [
                'errors' => [
                    $this->trans(
                        'There was an error when attempting to delete the Memcached server.',
                        'Admin.Advparameters.Notification'
                    ),
                ],
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @return MemcacheServerManager
     */
    private function getMemcacheManager()
    {
        return $this->get('prestashop.adapter.memcache_server.manager');
    }
}
