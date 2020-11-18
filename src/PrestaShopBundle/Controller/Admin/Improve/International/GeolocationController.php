<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GeolocationController is responsible for "Improve > International > Localization > Geolocation" page.
 */
class GeolocationController extends FrameworkBundleAdminController
{
    /**
     * Show geolocation page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $geolocationForm = $this->getGeolocationFormHandler()->getForm();
        $geoLiteCityChecker = $this->get('prestashop.core.geolocation.geo_lite_city.checker');

        return $this->render('@PrestaShop/Admin/Improve/International/Geolocation/index.html.twig', [
            'layoutTitle' => $this->trans('Geolocation', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'geolocationForm' => $geolocationForm->createView(),
            'geolocationDatabaseAvailable' => $geoLiteCityChecker->isAvailable(),
        ]);
    }

    /**
     * Process geolocation configuration form.
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this.",
     *     redirectRoute="admin_geolocation"
     * )
     * @DemoRestricted(redirectRoute="admin_geolocation_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function saveOptionsAction(Request $request)
    {
        $geolocationFormHandler = $this->getGeolocationFormHandler();

        $geolocationForm = $geolocationFormHandler->getForm();
        $geolocationForm->handleRequest($request);

        if ($geolocationForm->isSubmitted()) {
            $errors = $geolocationFormHandler->save($geolocationForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_geolocation_index');
            }

            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('admin_geolocation_index');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getGeolocationFormHandler()
    {
        return $this->get('prestashop.admin.geolocation.form_handler');
    }
}
