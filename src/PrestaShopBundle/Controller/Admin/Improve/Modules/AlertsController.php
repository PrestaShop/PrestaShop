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

namespace PrestaShopBundle\Controller\Admin\Improve\Modules;

use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Improve > Modules > Modules & Services > Alerts" page display.
 */
class AlertsController extends ModuleAbstractController
{
    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function indexAction()
    {
        $moduleRepository = $this->getModuleRepository();

        return $this->render(
            '@PrestaShop/Admin/Module/alerts.html.twig',
            array_merge(
                $this->getNotificationPageData($moduleRepository->getMustBeConfiguredModules()),
                ['layoutTitle' => $this->trans('Module alerts', 'Admin.Navigation.Menu')]
            )
        );
    }

    /**
     * @return JsonResponse with number of modules having at least one notification
     */
    public function notificationsCountAction()
    {
        $moduleRepository = $this->getModuleRepository();
        $toConfigure = count($moduleRepository->getMustBeConfiguredModules());
        $toUpdate = count($moduleRepository->getUpgradableModules());

        return new JsonResponse([
            self::UPDATABLE_MODULE_TYPE => $toUpdate,
            self::CONFIGURABLE_MODULE_TYPE => $toConfigure,
            self::TOTAL_MODULE_TYPE => $toConfigure + $toUpdate,
        ]);
    }
}
