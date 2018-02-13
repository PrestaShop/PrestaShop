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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Advanced Parameters > Performance" page display
 */
class PerformanceController extends FrameworkBundleAdminController
{
    const CONTROLLER_NAME = 'AdminPerformance';

    /**
     * @var FormInterface
     * @return Response
     */
    public function indexAction(FormInterface $form = null)
    {
        $toolbarButtons['clear_cache'] = array(
            'href' => $this->generateUrl('admin_clear_cache'),
            'desc' => $this->trans('Clear cache', 'Admin.Advparameters.Feature'),
            'icon' => 'delete',
        );

        $form = is_null($form) ? $this->get('prestashop.adapter.performance.form_handler')->getForm() : $form;

        $twigValues = array(
            'layoutHeaderToolbarBtn' => $toolbarButtons,
            'layoutTitle' => $this->get('translator')->trans('Performance', array(), 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminPerformance'),
            'requireFilterStatus' => false,
            'form' => $form->createView(),
            'servers' => $this->get('prestashop.adapter.memcache_server.manager')->getServers(),
        );

        return $this->render('@AdvancedParameters/performance.html.twig', $twigValues);
    }

    /**
     * @return RedirectResponse
     */
    public function processFormAction(Request $request)
    {
        if ($this->isDemoModeEnabled()) {
            $this->addFlash('error', $this->getDemoModeErrorMessage());

            return $this->redirectToRoute('admin_performance');
        }

        if (!in_array(
            $this->authorizationLevel($this::CONTROLLER_NAME),
            array(
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
            )
        )) {
            $this->addFlash('error', $this->trans('You do not have permission to update this.', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_performance');
        }

        $this->dispatchHook('actionAdminPerformanceControllerPostProcessBefore', array('controller' => $this));
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
     * @return RedirectResponse
     */
    public function clearCacheAction()
    {
        $this->get('prestashop.adapter.cache_clearer')->clearAllCaches();
        $this->addFlash('success', $this->trans('All caches cleared successfully', 'Admin.Advparameters.Notification'));

        return $this->redirectToRoute('admin_performance');
    }
}
