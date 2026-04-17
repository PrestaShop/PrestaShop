<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Improve\Modules;

use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Improve > Modules > Modules & Services > Updates" page display.
 */
class UpdatesController extends ModuleAbstractController
{
    /**
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(): Response
    {
        $moduleList = $this->getModuleRepository()->getUpgradableModules();
        $pageData = $this->getNotificationPageData($moduleList);

        // In update view, the only available action for module is update.
        // Can't use AdminModuleDataProvider::setActionUrls $specific_action attribute while abstract definition isn't clear.
        foreach ($pageData['modules'] as $key => $module) {
            if (isset($module['attributes']['urls']['upgrade'])) {
                $pageData['modules'][$key]['attributes']['urls'] = ['upgrade' => $module['attributes']['urls']['upgrade']];
                $pageData['modules'][$key]['attributes']['url_active'] = 'upgrade';
            }
        }

        return $this->render(
            '@PrestaShop/Admin/Module/updates.html.twig',
            array_merge(
                $pageData,
                ['layoutTitle' => $this->trans('Module updates', [], 'Admin.Navigation.Menu')]
            )
        );
    }
}
