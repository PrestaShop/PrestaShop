<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
