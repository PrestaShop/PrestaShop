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
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for "Configure > Advanced Parameters > Performance" servers block management.
 */
class MemcacheServerController extends PrestaShopAdminController
{
    public const CONTROLLER_NAME = 'AdminPerformance';

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function listAction(
        #[Autowire(service: 'prestashop.adapter.memcache_server.manager')]
        MemcacheServerManager $memcacheServerManager,
    ): JsonResponse {
        return new JsonResponse($memcacheServerManager->getServers());
    }

    #[DemoRestricted(redirectRoute: 'admin_servers_test')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function testAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.memcache_server.manager')]
        MemcacheServerManager $memcacheServerManager,
    ): JsonResponse {
        $queryValues = $request->query;

        if ($queryValues->has('server_ip') && $queryValues->has('server_port')) {
            $isValid = $memcacheServerManager
                ->testConfiguration(
                    $queryValues->get('server_ip'),
                    $queryValues->getInt('server_port')
                );

            return new JsonResponse(['test' => $isValid]);
        }

        return new JsonResponse(['errors' => 'error'], Response::HTTP_BAD_REQUEST);
    }

    #[DemoRestricted(redirectRoute: 'admin_servers_test')]
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function addAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.memcache_server.manager')]
        MemcacheServerManager $memcacheServerManager,
    ): JsonResponse {
        if (!in_array(
            $this->getAuthorizationLevel($this::CONTROLLER_NAME),
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
                        $this->trans('You do not have permission to create this.', [], 'Admin.Notifications.Error'),
                    ],
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $postValues = $request->request;

        if ($postValues->has('server_ip')
            && $postValues->has('server_port')
            && $postValues->has('server_weight')
            && $memcacheServerManager->testConfiguration(
                $postValues->get('server_ip'),
                $postValues->getInt('server_port')
            )
        ) {
            $server = $memcacheServerManager
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
                    $this->trans('The Memcached server cannot be added.', [], 'Admin.Advparameters.Notification'),
                ],
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    #[DemoRestricted(redirectRoute: 'admin_servers_test')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function deleteAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.memcache_server.manager')]
        MemcacheServerManager $memcacheServerManager,
    ): JsonResponse {
        if (!in_array(
            $this->getAuthorizationLevel($this::CONTROLLER_NAME),
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
                        $this->trans('You do not have permission to delete this.', [], 'Admin.Notifications.Error'),
                    ],
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($request->request->has('server_id')) {
            $memcacheServerManager->deleteServer($request->request->get('server_id'));

            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(
            [
                'errors' => [
                    $this->trans(
                        'There was an error when attempting to delete the Memcached server.',
                        [],
                        'Admin.Advparameters.Notification'
                    ),
                ],
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}
