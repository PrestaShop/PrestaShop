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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Advanced Parameters > Performance" page display.
 */
class PerformanceController extends FrameworkBundleAdminController
{
    const CONTROLLER_NAME = 'AdminPerformance';

    /**
     * Displays the Performance main page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param FormInterface $form
     *
     * @return Response
     */
    public function indexAction(FormInterface $form = null): Response
    {
        $toolbarButtons = [
            'clear_cache' => [
                'href' => $this->generateUrl('admin_clear_cache'),
                'desc' => $this->trans('Clear cache', 'Admin.Advparameters.Feature'),
                'icon' => 'delete',
            ],
        ];

        $form = null === $form ? $this->get('prestashop.adapter.performance.form_handler')->getForm() : $form;

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/performance.html.twig', [
            'layoutHeaderToolbarBtn' => $toolbarButtons,
            'layoutTitle' => $this->trans('Performance', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminPerformance'),
            'requireFilterStatus' => false,
            'form' => $form->createView(),
            'servers' => $this->get('prestashop.adapter.memcache_server.manager')->getServers(),
        ]);
    }

    /**
     * Process the Performance configuration form.
     *
     * @AdminSecurity("is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))", message="You do not have permission to update this.")
     * @DemoRestricted(redirectRoute="admin_performance")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processFormAction(Request $request)
    {
        $this->dispatchHook('actionAdminPerformanceControllerPostProcessBefore', ['controller' => $this]);
        $form = $this->get('prestashop.adapter.performance.form_handler')->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $saveErrors = $this->get('prestashop.adapter.performance.form_handler')->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_performance');
            }

            $this->flashErrors($saveErrors);
        }

        return $this->redirectToRoute('admin_performance');
    }

    /**
     * @AdminSecurity("is_granted(['delete'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this.",
     *     redirectRoute="admin_performance"
     * )
     *
     * @return RedirectResponse
     */
    public function clearCacheAction()
    {
        $this->get('prestashop.core.cache.clearer.cache_clearer_chain')->clear();
        $this->addFlash('success', $this->trans('All caches cleared successfully', 'Admin.Advparameters.Notification'));

        return $this->redirectToRoute('admin_performance');
    }
}
