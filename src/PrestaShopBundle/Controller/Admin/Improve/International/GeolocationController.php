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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Geolocation\GeoLite\GeoLiteCityCheckerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GeolocationController is responsible for "Improve > International > Localization > Geolocation" page.
 */
class GeolocationController extends PrestaShopAdminController
{
    /**
     * Show geolocation page.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.geolocation.by_ip_address.form_handler')]
        FormHandlerInterface $geolocationByIpAddressFormHandler,
        #[Autowire(service: 'prestashop.admin.geolocation.whitelist.form_handler')]
        FormHandlerInterface $geolocationWhitelistFormHandler,
        #[Autowire(service: 'prestashop.admin.geolocation.options.form_handler')]
        FormHandlerInterface $geolocationOptionsFormHandler,
        #[Autowire(service: 'prestashop.core.geolocation.geo_lite_city.checker')]
        GeoLiteCityCheckerInterface $geoLiteCityChecker
    ): Response {
        $legacyController = $request->attributes->get('_legacy_controller');

        $geolocationByIpAddressForm = $geolocationByIpAddressFormHandler->getForm();
        $geolocationIpAddressWhitelistForm = $geolocationWhitelistFormHandler->getForm();
        $geolocationOptionsForm = $geolocationOptionsFormHandler->getForm();

        return $this->render('@PrestaShop/Admin/Improve/International/Geolocation/index.html.twig', [
            'layoutTitle' => $this->trans('Geolocation', [], 'Admin.Navigation.Menu'),
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
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_geolocation_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_geolocation_index')]
    public function processByIpAddressFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.geolocation.by_ip_address.form_handler')]
        FormHandlerInterface $geolocationByIpAddressFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $geolocationByIpAddressFormHandler,
            'ByIpAddress'
        );
    }

    /**
     * Process the Geolocation Whitelist configuration form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_geolocation_index')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_geolocation_index')]
    public function processWhitelistFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.geolocation.whitelist.form_handler')]
        FormHandlerInterface $geolocationWhitelistFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $geolocationWhitelistFormHandler,
            'Whitelist'
        );
    }

    /**
     * Process the Geolocation Options configuration form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_geolocation_index')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.', redirectRoute: 'admin_geolocation_index')]
    public function processOptionsFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.geolocation.options.form_handler')]
        FormHandlerInterface $geolocationOptionsFormHandler
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $geolocationOptionsFormHandler,
            'Options'
        );
    }

    /**
     * Process the Performance configuration form.
     *
     * @param Request $request
     * @param FormHandlerInterface $formHandler
     * @param string $hookName
     *
     * @return RedirectResponse
     */
    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName): RedirectResponse
    {
        $this->dispatchHookWithParameters(
            'actionAdminInternationalGeolocationControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHookWithParameters('actionAdminInternationalGeolocationControllerPostProcessBefore', ['controller' => $this]);

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_geolocation_index');
    }
}
