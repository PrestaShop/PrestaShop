<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
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

        $geolocationByIpAddressForm = $this->getGeolocationByIpAddressFormHandler()->getForm();
        $geolocationIpAddressWhitelistForm = $this->getGeolocationWhitelistFormHandler()->getForm();
        $geolocationOptionsForm = $this->getGeolocationOptionsFormHandler()->getForm();
        $geoLiteCityChecker = $this->get('prestashop.core.geolocation.geo_lite_city.checker');

        return $this->render('@PrestaShop/Admin/Improve/International/Geolocation/index.html.twig', [
            'layoutTitle' => $this->trans('Geolocation', 'Admin.Navigation.Menu'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'geolocationByIpAddressForm' => $geolocationByIpAddressForm->createView(),
            'geolocationIpAddressWhitelistForm' => $geolocationIpAddressWhitelistForm->createView(),
            'geolocationOptionsForm' => $geolocationOptionsForm->createView(),
            'geolocationDatabaseAvailable' => $geoLiteCityChecker->isAvailable(),
        ]);
    }

    /**
     * Process the Geolocation ByIpAddress configuration form.
     *
     * @AdminSecurity(
     *     "is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this.",
     *     redirectRoute="admin_geolocation"
     * )
     * @DemoRestricted(redirectRoute="admin_geolocation_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processByIpAddressFormAction(Request $request)
    {
        $this->dispatchHook(
            'actionAdminPerformanceControllerPostProcessSmartyBefore',
            ['controller' => $this]
        );

        return $this->processForm(
            $request,
            $this->getGeolocationByIpAddressFormHandler()
        );
    }

    /**
     * Process the Geolocation Whitelist configuration form.
     *
     * @AdminSecurity(
     *     "is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this.",
     *     redirectRoute="admin_geolocation"
     * )
     * @DemoRestricted(redirectRoute="admin_geolocation_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processWhitelistFormAction(Request $request)
    {
        $this->dispatchHook(
            'actionAdminPerformanceControllerPostProcessSmartyBefore',
            ['controller' => $this]
        );

        return $this->processForm(
            $request,
            $this->getGeolocationWhitelistFormHandler()
        );
    }

    /**
     * Process the Geolocation Options configuration form.
     *
     * @AdminSecurity(
     *     "is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this.",
     *     redirectRoute="admin_geolocation"
     * )
     * @DemoRestricted(redirectRoute="admin_geolocation_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processOptionsFormAction(Request $request)
    {
        $this->dispatchHook(
            'actionAdminPerformanceControllerPostProcessSmartyBefore',
            ['controller' => $this]
        );

        return $this->processForm(
            $request,
            $this->getGeolocationOptionsFormHandler()
        );
    }

    /**
     * Process the Performance configuration form.
     *
     * @param Request $request
     * @param FormHandlerInterface $formHandler
     *
     * @return RedirectResponse
     */
    protected function processForm(Request $request, FormHandlerInterface $formHandler)
    {
        $this->dispatchHook('actionAdminGeolocationControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            } else {
                $this->flashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_geolocation_index');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getGeolocationByIpAddressFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.geolocation.by_ip_address.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getGeolocationWhitelistFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.geolocation.whitelist.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getGeolocationOptionsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.geolocation.options.form_handler');
    }
}
