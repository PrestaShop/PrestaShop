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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Core\Form\FormHandler;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GeolocationController is responsible for "Improve > International > Localization > Geolocation" page
 */
class GeolocationController extends FrameworkBundleAdminController
{
    /**
     * Show geolocation page
     *
     * @Template("@PrestaShop/Admin/Improve/International/Geolocation/geolocation.html.twig")
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param Request $request
     *
     * @return array
     */
    public function showGeolocationAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $geolocationForm = $this->getGeolocationFormHandler()->getForm();
        $geoLiteCityChecker = $this->get('prestashop.core.geolocation.geo_lite_city.checker');

        return [
            'layoutTitle' => $this->trans('Geolocation', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'geolocationForm' => $geolocationForm->createView(),
            'geolocationDatabaseAvailable' => $geoLiteCityChecker->isAvailable(),
        ];
    }

    /**
     * Process geolocation configuration form
     *
     * @AdminSecurity(
     *     "is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to edit this.",
     *     redirectRoute="admin_geolocation_show"
     * )
     * @DemoRestricted(redirectRoute="admin_geolocation_show")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processFormAction(Request $request)
    {
        $geolocationFormHandler = $this->getGeolocationFormHandler();

        $geolocationForm = $geolocationFormHandler->getForm();
        $geolocationForm->handleRequest($request);

        if ($geolocationForm->isSubmitted()) {
            $errors = $geolocationFormHandler->save($geolocationForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_geolocation_show');
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }

        return $this->redirectToRoute('admin_geolocation_show');
    }

    /**
     * @return FormHandler
     */
    protected function getGeolocationFormHandler()
    {
        return $this->get('prestashop.admin.geolocation.form_handler');
    }
}
