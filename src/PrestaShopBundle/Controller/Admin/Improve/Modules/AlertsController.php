<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Improve\Modules;

use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Improve > Modules > Modules & Services > Alerts" page display.
 */
class AlertsController extends ModuleAbstractController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(): Response
    {
        $moduleRepository = $this->getModuleRepository();

        return $this->render(
            '@PrestaShop/Admin/Module/alerts.html.twig',
            array_merge(
                $this->getNotificationPageData($moduleRepository->getMustBeConfiguredModules()),
                ['layoutTitle' => $this->trans('Module alerts', [], 'Admin.Navigation.Menu')]
            )
        );
    }

    /**
     * @return JsonResponse with number of modules having at least one notification
     */
    public function notificationsCountAction(): JsonResponse
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
