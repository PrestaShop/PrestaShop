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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\BulkToggleModuleStatusCommand;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible of "Configure > Advanced Parameters > Performance" page display.
 */
class PerformanceController extends FrameworkBundleAdminController
{
    public const CONTROLLER_NAME = 'AdminAdvancedParametersPerformance';

    /**
     * Displays the Performance main page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $toolbarButtons = [
            'clear_cache' => [
                'href' => $this->generateUrl('admin_clear_cache'),
                'desc' => $this->trans('Clear cache', 'Admin.Advparameters.Feature'),
                'icon' => 'delete',
            ],
        ];

        $smartyForm = $this->getSmartyFormHandler()->getForm();
        $debugModeForm = $this->getDebugModeFormHandler()->getForm();
        $optionalFeaturesForm = $this->getOptionalFeaturesFormHandler()->getForm();
        $combineCompressCacheForm = $this->getCombineCompressCacheFormHandler()->getForm();
        $mediaServersForm = $this->getMediaServersFormHandler()->getForm();
        $cachingForm = $this->getCachingFormHandler()->getForm();
        $memcacheForm = $this->getMemcacheFormBuilder()->getForm();

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/performance.html.twig', [
            'layoutHeaderToolbarBtn' => $toolbarButtons,
            'layoutTitle' => $this->trans('Performance', 'Admin.Navigation.Menu'),
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
            'servers' => $this->get('prestashop.adapter.memcache_server.manager')->getServers(),
        ]);
    }

    /**
     * Process the Performance Smarty configuration form.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *     message="You do not have permission to update this."
     * )
     * @DemoRestricted(redirectRoute="admin_performance")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processSmartyFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getSmartyFormHandler(),
            'Smarty'
        );
    }

    /**
     * Process the Performance Debug Mode configuration form.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *     message="You do not have permission to update this."
     * )
     * @DemoRestricted(redirectRoute="admin_performance")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processDebugModeFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getDebugModeFormHandler(),
            'DebugMode'
        );
    }

    /**
     * Process the Performance Optional Features configuration form.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *     message="You do not have permission to update this."
     * )
     * @DemoRestricted(redirectRoute="admin_performance")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processOptionalFeaturesFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getOptionalFeaturesFormHandler(),
            'OptionalFeatures'
        );
    }

    /**
     * Process the Performance Combine Compress Cache configuration form.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *     message="You do not have permission to update this."
     * )
     * @DemoRestricted(redirectRoute="admin_performance")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processCombineCompressCacheFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getCombineCompressCacheFormHandler(),
            'CombineCompressCache'
        );
    }

    /**
     * Process the Performance Media Servers configuration form.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *     message="You do not have permission to update this."
     * )
     * @DemoRestricted(redirectRoute="admin_performance")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processMediaServersFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getMediaServersFormHandler(),
            'MediaServers'
        );
    }

    /**
     * Process the Performance Caching configuration form.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *     message="You do not have permission to update this."
     * )
     * @DemoRestricted(redirectRoute="admin_performance")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function processCachingFormAction(Request $request)
    {
        return $this->processForm(
            $request,
            $this->getCachingFormHandler(),
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
    protected function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName)
    {
        $this->dispatchHook(
            'actionAdminAdvancedParametersPerformanceControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $this->dispatchHook('actionAdminAdvancedParametersPerformanceControllerPostProcessBefore', ['controller' => $this]);

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

        return $this->redirectToRoute('admin_performance');
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="Access denied.")
     * @DemoRestricted(redirectRoute="admin_performance")
     *
     * @return RedirectResponse
     */
    public function disableNonBuiltInAction(): RedirectResponse
    {
        try {
            $bulkToggleModuleStatusCommand = new BulkToggleModuleStatusCommand(
                $this->get('prestashop.adapter.module.repository.module_repository')->getNonNativeModules(),
                false
            );

            $this->getCommandBus()->handle($bulkToggleModuleStatusCommand);

            $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_performance');
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))",
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

    /**
     * @return FormHandlerInterface
     */
    protected function getSmartyFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.adapter.performance.smarty.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getDebugModeFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.adapter.performance.debug_mode.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getOptionalFeaturesFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.adapter.performance.optional_features.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getCombineCompressCacheFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.adapter.performance.ccc.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getMediaServersFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.adapter.performance.media_servers.form_handler');
    }

    /**
     * @return FormHandlerInterface
     */
    protected function getCachingFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.adapter.performance.caching.form_handler');
    }

    /**
     * @return FormBuilderInterface
     */
    protected function getMemcacheFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.admin.advanced_parameters.performance.memcache.form_builder');
    }
}
