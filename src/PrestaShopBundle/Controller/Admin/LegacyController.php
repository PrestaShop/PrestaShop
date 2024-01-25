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

namespace PrestaShopBundle\Controller\Admin;

use AdminController;
use Dispatcher;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Routing\LegacyRouterChecker;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use PrestaShopBundle\Twig\Layout\SmartyVariablesFiller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\String\u;

/**
 * This controller acts as a wrapper around a legacy controller, it executes the core logic of an AdminController
 * instance, most of the init methods are called to stay the closest possible to the original behaviour. Some methods
 * have been voluntarily stripped (meaning they will not be executed) because they mostly handle the logic of the layout
 * rendering (menu, toolbar, ...).
 *
 * All the layout logic is already handled by the Symfony layout and its internal components, so we don't need to execute
 * it twice.
 *
 * So this controller gets back the central content of an AdminController after it's been run and displayed and integrate it
 * in the legacy layout that is based on the same Symfony layout as migrated page, but it still uses the templates JS and CSS
 * from the default theme.
 *
 * There are cases where this approach may not work, mostly when the legacy controllers relies on die or exit methods (which is a
 * bad practice). So far the use cases tested work fine, even the use of the header function in legacy code still works correctly.
 * But if the legacy controller exits too soon then we can't get the content and return a Symfony response which may result in
 * unexpected side effects.
 */
class LegacyController extends PrestaShopAdminController
{
    public function __construct(
        protected readonly TabRepository $tabRepository,
        protected readonly LegacyContext $legacyContext,
        protected readonly SmartyVariablesFiller $assignSmartyVariables,
        protected readonly ConfigurationInterface $configuration,
        protected readonly MenuBuilder $menuBuilder,
    ) {
    }

    /**
     * This mimics/adapts the Dispatcher::dispatch method, detect the controller, initialize it and display it
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws CoreException
     */
    public function legacyPageAction(Request $request): Response
    {
        // These parameters have already been set as request attributes by LegacyRouterChecker
        $dispatcherHookParameters = [
            'controller_type' => Dispatcher::FC_ADMIN,
            'controller_class' => $request->attributes->get(LegacyRouterChecker::LEGACY_CONTROLLER_CLASS_ATTRIBUTE),
            'is_module' => $request->attributes->get(LegacyRouterChecker::LEGACY_CONTROLLER_IS_MODULE_ATTRIBUTE),
        ];

        $adminController = $this->initController($dispatcherHookParameters);
        // Redirect if necessary after post process
        if (!empty($adminController->getRedirectAfter())) {
            // After each request the cookie must be written to save its modified state during AdminController workflow
            // See Controller::smartyOutputContent
            $this->legacyContext->getContext()->cookie->write();
            $redirectAfter = $adminController->getRedirectAfter();

            // In case redirect url is purely relative (starts with index.php) we transform it into an absolute one
            // this avoids unexpected redirection by the BackUrlRedirectResponseListener
            if (str_starts_with($redirectAfter, 'index.php')) {
                $redirectAfter = rtrim($request->getSchemeAndHttpHost() . $request->getBasePath(), '/') . '/' . $redirectAfter;
            }

            return $this->redirect($redirectAfter);
        }

        // Force disabling cache control like it's done in initHeader usually (we use header function instead of Response attributes
        // in case the process is forced to exit early, without returning a Symfony response)
        header('Cache-Control: no-store, no-cache');

        $smarty = $this->legacyContext->getSmarty();
        $smarty->setTemplateDir(_PS_BO_ALL_THEMES_DIR_ . 'default/template/');

        $isAjaxRequest = (bool) $request->get('ajax');
        if ($isAjaxRequest) {
            $response = $this->renderAjaxController($adminController, u($request->get('action'))->camel());
        } else {
            $response = $this->renderPageContent($adminController);
        }

        // Execute hook dispatcher after
        $this->dispatchHookWithParameters('actionDispatcherAfter', $dispatcherHookParameters);

        return $response;
    }

    /**
     * This mimics/adapts the AdminController:display method, stripped from the part that are already handled by the
     * symfony layout and without direct echoes from smarty
     *
     * @param AdminController $adminController
     *
     * @return Response
     *
     * @throws \SmartyException
     */
    protected function renderPageContent(AdminController $adminController): Response
    {
        $adminController->initContent();

        $smarty = $this->legacyContext->getSmarty();
        $templateDirectories = $smarty->getTemplateDir() ?: [];
        $controllerDisplay = $adminController->getDisplay();
        if (!empty($controllerDisplay)) {
            $actionTemplate = $adminController->tpl_folder . $controllerDisplay . '.tpl';

            // Check if action template has been overridden
            foreach ($templateDirectories as $templateDirectory) {
                if (file_exists($templateDirectory . DIRECTORY_SEPARATOR . $actionTemplate) && $controllerDisplay != 'view' && $controllerDisplay != 'options') {
                    if (method_exists($this, $controllerDisplay . u($adminController->className)->camel())) {
                        $this->{$controllerDisplay . u($adminController->className)->camel()}();
                    }
                    $smarty->assign('content', $smarty->fetch($actionTemplate));

                    break;
                }
            }
        }

        $template = $adminController->createTemplate($adminController->template);
        $legacyContent = $template->fetch();

        // After each request the cookie must be written to save its modified state during AdminController workflow
        // See Controller::smartyOutputContent
        $this->legacyContext->getContext()->cookie->write();

        return $this->render('@PrestaShop/Admin/Layout/legacy_layout.html.twig', [
            'legacyContent' => $legacyContent,
            'showContentHeader' => $adminController->show_page_header_toolbar,
            // Since AdminController::initFooter is not called we render modals manually here
            'modals' => $adminController->renderModal(),
        ]);
    }

    /**
     * This part mimics how AdminController renders ajax content
     *
     * Many ajax controllers directly echo their content so in this case we prefer catching the output of the legacy controller,
     * it is then returned as a proper Symfony response.
     *
     * @param AdminController $adminController
     * @param string $action
     *
     * @return Response
     */
    protected function renderAjaxController(AdminController $adminController, string $action): Response
    {
        ob_start();
        // In this case, initContent must be executed after ob_start because it can already echo some output
        $adminController->initContent();
        if (!empty($action) && method_exists($adminController, 'displayAjax' . $action)) {
            $adminController->{'displayAjax' . $action}();
        } elseif (method_exists($adminController, 'displayAjax')) {
            $adminController->displayAjax();
        }
        $outputContent = ob_get_clean();

        // The output of the controller is either directly echoed or it can be properly appended in AdminController::content
        // it depends on the implementation of the controllers.
        if (!empty($outputContent) && !empty($adminController->content)) {
            // Sometimes they are both done and are equal, in which case we only return one content to avoid duplicates.
            if ($outputContent === $adminController->content) {
                $responseContent = $outputContent;
            } else {
                // In case both contents are present and are different we concatenate them, first the echoed output and then the controller
                // one since it is displayed last by smarty in the original workflow
                // This concatenation approach is theoretical and naive and was not tested because of the lack of use cases, so it may need
                // to be improved in the future
                $responseContent = $outputContent . $adminController->content;
            }
        } elseif (!empty($outputContent)) {
            $responseContent = $outputContent;
        } elseif (!empty($adminController->content)) {
            $responseContent = $adminController->content;
        } else {
            $responseContent = '';
        }

        return new Response($responseContent);
    }

    /**
     * This mimics the first part of AdminController::run initialize the controller and its sub contents before actually displaying it,
     * it was stripped from the part already handled by the Symfony layout
     *
     * Note: some legacy controllers may already use die at this point (to echo content and finish the process) when postProcess is called.
     *
     * @param array $dispatcherHookParameters
     *
     * @return AdminController
     */
    protected function initController(array $dispatcherHookParameters): AdminController
    {
        $controllerClass = $dispatcherHookParameters['controller_class'];

        // Loading controller
        /** @var AdminController $adminController */
        $adminController = new $controllerClass();

        // Fill default smarty variables as they can be used in partial templates rendered in init methods
        $this->assignSmartyVariables->fillDefault();

        // Execute hook dispatcher
        $this->dispatchHookWithParameters('actionDispatcher', $dispatcherHookParameters);

        // This part comes from AdminController::run method, it has been stripped from permission checks since the permission is already
        // handled by this Symfony controller
        $adminController->init();
        $adminController->setMedia(false);
        $adminController->postProcess();

        return $adminController;
    }
}
