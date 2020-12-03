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

use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Entity\Tab;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Shop Parameters > General" page.
 */
class PreferencesController extends FrameworkBundleAdminController
{
    const CONTROLLER_NAME = 'AdminPreferences';

    /**
     * @param Request $request
     * @param FormInterface|null $form
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return Response
     *
     * @throws \LogicException
     */
    public function indexAction(Request $request, FormInterface $form = null)
    {
        if (null === $form) {
            $form = $this->get('prestashop.adapter.preferences.form_handler')->getForm();
        }

        /** @var Tools $toolsAdapter */
        $toolsAdapter = $this->get('prestashop.adapter.tools');

        // SSL URI is used for the merchant to check if he has SSL enabled
        $sslUri = 'https://' . $toolsAdapter->getShopDomainSsl() . $request->getRequestUri();

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/preferences.html.twig', [
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->get('translator')->trans('Preferences', [], 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminPreferences'),
            'requireFilterStatus' => false,
            'form' => $form->createView(),
            'isSslEnabled' => $this->configuration->get('PS_SSL_ENABLED'),
            'sslUri' => $sslUri,
        ]);
    }

    /**
     * @param Request $request
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_preferences")
     *
     * @DemoRestricted(redirectRoute="admin_preferences")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \LogicException
     */
    public function processFormAction(Request $request)
    {
        $this->dispatchHook('actionAdminPreferencesControllerPostProcessBefore', ['controller' => $this]);

        /** @var FormInterface $form */
        $form = $this->get('prestashop.adapter.preferences.form_handler')->getForm();
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->redirectToRoute('admin_preferences');
        }

        $data = $form->getData();
        $saveErrors = $this->get('prestashop.adapter.preferences.form_handler')->save($data);

        if (0 === count($saveErrors)) {
            /** @var EntityManager $em */
            $em = $this->get('doctrine.orm.entity_manager');

            /** @var TabRepository $tabRepository */
            $tabRepository = $em->getRepository(Tab::class);

            $tabRepository->changeStatusByClassName(
                'AdminShopGroup',
                (bool) $this->configuration->get('PS_MULTISHOP_FEATURE_ACTIVE')
            );

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

            return $this->redirectToRoute('admin_preferences');
        }

        $this->flashErrors($saveErrors);

        return $this->redirectToRoute('admin_preferences');
    }
}
