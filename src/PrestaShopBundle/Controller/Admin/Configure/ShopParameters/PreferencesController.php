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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Tab\Command\UpdateTabStatusByClassNameCommand;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Shop Parameters > General" page.
 */
class PreferencesController extends FrameworkBundleAdminController
{
    public const CONTROLLER_NAME = 'AdminPreferences';

    /**
     * @param Request $request
     * @param FormInterface|null $form
     *
     * @return Response
     *
     * @throws \Exception
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(Request $request, FormInterface $form = null)
    {
        $form = $this->get('prestashop.adapter.preferences.form_handler')->getForm();

        return $this->doRenderForm($request, $form);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws \LogicException
     */
    #[DemoRestricted(redirectRoute: 'admin_preferences')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_preferences')]
    public function processFormAction(Request $request)
    {
        $this->dispatchHook('actionAdminPreferencesControllerPostProcessBefore', ['controller' => $this]);

        /** @var FormInterface $form */
        $form = $this->get('prestashop.adapter.preferences.form_handler')->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $saveErrors = $this->get('prestashop.adapter.preferences.form_handler')->save($data);

            if (0 === count($saveErrors)) {
                $this->getCommandBus()->handle(
                    new UpdateTabStatusByClassNameCommand(
                        'AdminShopGroup',
                        $this->getConfiguration()->get('PS_MULTISHOP_FEATURE_ACTIVE')
                    )
                );

                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_preferences');
            }

            $this->flashErrors($saveErrors);
        }

        return $this->doRenderForm($request, $form);
    }

    private function doRenderForm(Request $request, FormInterface $form): Response
    {
        /** @var Tools $toolsAdapter */
        $toolsAdapter = $this->get(Tools::class);

        // SSL URI is used for the merchant to check if he has SSL enabled
        $sslUri = 'https://' . $toolsAdapter->getShopDomainSsl() . $request->getRequestUri();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/preferences.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Preferences', 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminPreferences'),
            'requireFilterStatus' => false,
            'generalForm' => $form->createView(),
            'isSslEnabled' => $this->getConfiguration()->get('PS_SSL_ENABLED'),
            'sslUri' => $sslUri,
        ]);
    }
}
