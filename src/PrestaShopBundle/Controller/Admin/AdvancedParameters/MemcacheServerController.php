<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\AdvancedParameters;

use PrestaShop\PrestaShop\Adapter\Cache\MemcacheServerManager;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible of "Configure > Advanced Parameters > Performance" servers block management
 */
class MemcacheServerController extends FrameworkBundleAdminController
{
    const CONTROLLER_NAME = 'AdminPerformance';

    public function listAction()
    {
        return new JsonResponse($this->getMemcacheManager()->getServers());
    }

    public function testAction(Request $request)
    {
        if ($this->isDemoModeEnabled()) {
            $this->addFlash('error', $this->getDemoModeErrorMessage());

            return $this->redirectToRoute('admin_servers_test');
        }

        $queryValues = $request->query;

        if ($queryValues->has('server_ip') && $queryValues->has('server_port')) {
            $isValid = $this->getMemcacheManager()
                ->testConfiguration(
                    $queryValues->get('server_ip'),
                    $queryValues->getInt('server_port')
                );

            return new JsonResponse(array('test' => $isValid));
        }

        return new JsonResponse(array('errors' => 'error'), 400);
    }

    public function addAction(Request $request)
    {
        if ($this->isDemoModeEnabled()) {
            $this->addFlash('error', $this->getDemoModeErrorMessage());

            return $this->redirectToRoute('admin_servers_test');
        }

        if (!in_array(
            $this->authorizationLevel($this::CONTROLLER_NAME),
            array(
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
            )
        )) {
            return new JsonResponse(array(
                'errors' => array($this->trans('You do not have permission to create this.', 'Admin.Notifications.Error')
                )
            ), 400);
        }

        $postValues = $request->request;

        if (
            $postValues->has('server_ip')
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
                    $postValues->get('server_port'),
                    $postValues->get('server_weight')
                )
            ;

            return new JsonResponse($server, 201);
        }

        return new JsonResponse(array('errors' => array(
            $this->trans('The Memcached server cannot be added.', 'Admin.Advparameters.Notification')
        )), 400);

    }

    public function deleteAction(Request $request)
    {
        if ($this->isDemoModeEnabled()) {
            $this->addFlash('error', $this->getDemoModeErrorMessage());

            return $this->redirectToRoute('admin_servers_test');
        }

        if (!in_array(
            $this->authorizationLevel($this::CONTROLLER_NAME),
            array(
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
            )
        )) {
            return new JsonResponse(array(
                'errors' => array($this->trans('You do not have permission to delete this.', 'Admin.Notifications.Error')
                )
            ), 400);
        }

        if ($request->request->has('server_id')) {
            $this->getMemcacheManager()->deleteServer($request->request->get('server_id'));

            return new JsonResponse(array(), 204);
        }

        return new JsonResponse(array('errors' => array(
            $this->trans('There was an error when attempting to delete the Memcached server.', 'Admin.Advparameters.Notification')
        )), 400);
    }

    /**
     * @var MemcacheServerManager
     */
    private function getMemcacheManager()
    {
        return $this->get('prestashop.adapter.memcache_server.manager');
    }
}
