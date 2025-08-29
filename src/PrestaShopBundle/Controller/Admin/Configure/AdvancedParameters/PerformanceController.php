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

use Exception;
use PrestaShop\PrestaShop\Adapter\Cache\MemcacheServerManager;
use PrestaShop\PrestaShop\Adapter\Module\Repository\ModuleRepository;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\BulkToggleModuleStatusCommand;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for "Configure > Advanced Parameters > Performance" page display.
 */
class PerformanceController extends PrestaShopAdminController
{
    /**
     * Displays the Performance main page.
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        #[Autowire(service: 'prestashop.adapter.memcache_server.manager')]
        MemcacheServerManager $memcacheServerManager,
        #[Autowire(service: 'prestashop.adapter.performance.smarty.form_handler')]
        FormHandlerInterface $smartyFormHandler,
        #[Autowire(service: 'prestashop.adapter.performance.debug_mode.form_handler')]
        FormHandlerInterface $debugModeFormHandler,
        #[Autowire(service: 'prestashop.adapter.performance.optional_features.form_handler')]
        FormHandlerInterface $optionalFeaturesFormHandler,
        #[Autowire(service: 'prestashop.adapter.performance.ccc.form_handler')]
        FormHandlerInterface $combineCompressCacheFormHandler,
        #[Autowire(service: 'prestashop.adapter.performance.media_servers.form_handler')]
        FormHandlerInterface $mediaServersFormHandler,
        #[Autowire(service: 'prestashop.adapter.performance.caching.form_handler')]
        FormHandlerInterface $cachingFormHandler,
        #[Autowire(service: 'prestashop.admin.advanced_parameters.performance.memcache.form_builder')]
        FormBuilderInterface $memcacheFormBuilder,
    ): Response {
        $toolbarButtons = [
            'clear_cache' => [
                'href' => $this->generateUrl('admin_clear_cache'),
                'desc' => $this->trans('Clear cache', [], 'Admin.Advparameters.Feature'),
                'icon' => 'delete',
            ],
        ];

        $smartyForm = $smartyFormHandler->getForm();
        $debugModeForm = $debugModeFormHandler->getForm();
        $optionalFeaturesForm = $optionalFeaturesFormHandler->getForm();
        $combineCompressCacheForm = $combineCompressCacheFormHandler->getForm();
        $mediaServersForm = $mediaServersFormHandler->getForm();
        $cachingForm = $cachingFormHandler->getForm();
        $memcacheForm = $memcacheFormBuilder->getForm();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/performance.html.twig', [
            'layoutHeaderToolbarBtn' => $toolbarButtons,
            'layoutTitle' => $this->trans('Performance', [], 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminAdvancedParametersPerformance'),
            'requireFilterStatus' => false,
            'smartyForm' => $smartyForm->createView(),
            'debugModeForm' => $debugModeForm->createView(),
            'optionalFeaturesForm' => $optionalFeaturesForm->createView(),
            'combineCompressCacheForm' => $combineCompressCacheForm->createView(),
            'mediaServersForm' => $mediaServersForm->createView(),
            'cachingForm' => $cachingForm->createView(),
            'memcacheForm' => $memcacheForm->createView(),
            'servers' => $memcacheServerManager->getServers(),
        ]);
    }

    /**
     * Process the Performance Smarty configuration form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_performance')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_performance')]
    public function processSmartyFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.performance.smarty.form_handler')]
        FormHandlerInterface $smartyFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $smartyFormHandler,
            'Smarty'
        );
    }

    /**
     * Process the Performance Debug Mode configuration form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_performance')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_performance')]
    public function processDebugModeFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.performance.debug_mode.form_handler')]
        FormHandlerInterface $debugModeFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $debugModeFormHandler,
            'DebugMode'
        );
    }

    /**
     * Process the Performance Optional Features configuration form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_performance')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_performance')]
    public function processOptionalFeaturesFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.performance.optional_features.form_handler')]
        FormHandlerInterface $optionalFeaturesFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $optionalFeaturesFormHandler,
            'OptionalFeatures'
        );
    }

    /**
     * Process the Performance Combine Compress Cache configuration form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_performance')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_performance')]
    public function processCombineCompressCacheFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.performance.ccc.form_handler')]
        FormHandlerInterface $combineCompressCacheFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $combineCompressCacheFormHandler,
            'CombineCompressCache'
        );
    }

    /**
     * Process the Performance Media Servers configuration form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_performance')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_performance')]
    public function processMediaServersFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.performance.media_servers.form_handler')]
        FormHandlerInterface $mediaServersFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $mediaServersFormHandler,
            'MediaServers'
        );
    }

    /**
     * Process the Performance Caching configuration form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_performance')]
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_performance')]
    public function processCachingFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.performance.caching.form_handler')]
        FormHandlerInterface $cachingFormHandler,
    ): RedirectResponse {
        return $this->processForm(
            $request,
            $cachingFormHandler,
            'Caching'
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
            'actionAdminAdvancedParametersPerformanceControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHookWithParameters('actionAdminAdvancedParametersPerformanceControllerPostProcessBefore', ['controller' => $this]);

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

        return $this->redirectToRoute('admin_performance');
    }

    #[DemoRestricted(redirectRoute: 'admin_performance')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'Access denied.', redirectRoute: 'admin_performance')]
    public function disableNonBuiltInAction(ModuleRepository $moduleRepository): RedirectResponse
    {
        try {
            $bulkToggleModuleStatusCommand = new BulkToggleModuleStatusCommand(
                $moduleRepository->getNonNativeModules(),
                false
            );

            $this->dispatchCommand($bulkToggleModuleStatusCommand);

            $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_performance');
    }

    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_performance')]
    public function clearCacheAction(
        #[Autowire(service: 'prestashop.core.cache.clearer.cache_clearer_chain')]
        CacheClearerInterface $cacheClearer
    ): RedirectResponse {
        $cacheClearer->clear();
        $this->addFlash('success', $this->trans('All caches cleared successfully', [], 'Admin.Advparameters.Notification'));

        return $this->redirectToRoute('admin_performance');
    }
}
