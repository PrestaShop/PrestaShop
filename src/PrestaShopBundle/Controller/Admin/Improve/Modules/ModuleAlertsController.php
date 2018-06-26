<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShopBundle\Controller\Admin\Improve\Modules;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ModuleAlertsController extends AbstractController
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $modulePresenter = $this->get('prestashop.adapter.presenter.module');
        $modulesPresenterCallback = function (array &$modules) use ($modulePresenter) {
            return $modulePresenter->presentCollection($modules);
        };

        $moduleManager = $this->get('prestashop.module.manager');
        $modules = $moduleManager->getModulesWithNotifications($modulesPresenterCallback);
        $layoutTitle = $this->trans('Module notifications', 'Admin.Modules.Feature');

        $errorMessage = $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error');

//        dump($modules);die;

        return $this->render('PrestaShopBundle:Admin/Module:alerts.html.twig', array(
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'layoutTitle' => $layoutTitle,
            'help_link' => $this->generateSidebarLink('AdminModules'),
            'modules' => $modules->to_configure,
            'requireAddonsSearch' => false,
            'requireBulkActions' => false,
            'requireFilterStatus' => false,
            'level' => $this->authorizationLevel($this::CONTROLLER_NAME),
            'errorMessage' => $errorMessage,
        ));
    }

    /**
     * @return JsonResponse with number of modules having at least one notification
     */
    public function notificationsCountAction()
    {
        $moduleManager = $this->container->get('prestashop.module.manager');

        $modulesWithNotif = $moduleManager->groupModulesByInstallationProgress();
        return new JsonResponse(array(
            'count' => $moduleManager->countModulesWithNotifications(),
            'alerts' => count($modulesWithNotif->to_configure),
            'updates' => count($modulesWithNotif->to_update),
        ));
    }
}
