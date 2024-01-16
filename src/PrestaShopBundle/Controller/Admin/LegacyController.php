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
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use PrestaShopBundle\Twig\Layout\SmartyVariablesFiller;
use Smarty;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Tools;

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
        $dispatcherHookParameters = $this->getDispatcherParameters($request);

        $adminController = $this->initController($dispatcherHookParameters);
        // Redirect if necessary after post process
        if (!empty($adminController->getRedirectAfter())) {
            // After each request the cookie must be written to save its modified state during AdminController workflow
            // See Controller::smartyOutputContent
            $this->legacyContext->getContext()->cookie->write();

            return $this->redirect($adminController->getRedirectAfter());
        }

        // Init content only when no redirection needed
        $adminController->initContent();

        $smarty = $this->legacyContext->getSmarty();
        $smarty->setTemplateDir(_PS_BO_ALL_THEMES_DIR_ . 'default/template/');

        $isAjaxRequest = (bool) $request->get('ajax');
        if ($isAjaxRequest) {
            $response = $this->renderAjaxController($adminController, Tools::toCamelCase($request->get('action')));
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
        $smarty = $this->legacyContext->getSmarty();

        $templateDirectories = $smarty->getTemplateDir() ?: [];
        $controllerDisplay = $adminController->getDisplay();
        if (!empty($controllerDisplay)) {
            $actionTemplate = $adminController->tpl_folder . $controllerDisplay . '.tpl';

            // Check if action template has been overridden
            foreach ($templateDirectories as $templateDirectory) {
                if (file_exists($templateDirectory . DIRECTORY_SEPARATOR . $actionTemplate) && $controllerDisplay != 'view' && $controllerDisplay != 'options') {
                    if (method_exists($this, $controllerDisplay . Tools::toCamelCase($adminController->className))) {
                        $this->{$controllerDisplay . Tools::toCamelCase($adminController->className)}();
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
     * @param AdminController $adminController
     * @param string $action
     *
     * @return Response
     */
    protected function renderAjaxController(AdminController $adminController, string $action): Response
    {
        if (!empty($action) && method_exists($adminController, 'displayAjax' . $action)) {
            $adminController->{'displayAjax' . $action}();
        } elseif (method_exists($adminController, 'displayAjax')) {
            $adminController->displayAjax();
        }

        return new Response($adminController->content);
    }

    /**
     * This mimics the first part of AdminController::run initialize the controller and its sub contents before actually displaying it,
     * it was stripped from the part already handled by the Symfony layout
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

    protected function getDispatcherParameters(Request $request): array
    {
        $queryController = $request->query->get('controller');
        $this->dispatchHookWithParameters('actionDispatcherBefore', ['controller_type' => Dispatcher::FC_ADMIN]);

        $tab = $this->tabRepository->findOneByClassName($queryController);
        if (!$tab) {
            throw new CoreException(sprintf('Could not find tab for controller %s', $queryController));
        }

        if (!empty($tab->getModule())) {
            $moduleName = $tab->getModule();
            $controllers = Dispatcher::getControllers(_PS_MODULE_DIR_ . $moduleName . '/controllers/admin/');
            if (!isset($controllers[strtolower($queryController)])) {
                throw new RouteNotFoundException(sprintf('Unknown controller %s', $queryController));
            } else {
                $controllerName = $controllers[strtolower($queryController)];
                // Controllers in modules can be named AdminXXX.php or AdminXXXController.php
                include_once _PS_MODULE_DIR_ . "{$moduleName}/controllers/admin/$controllerName.php";
                if (file_exists(
                    _PS_OVERRIDE_DIR_ . "modules/{$moduleName}/controllers/admin/$controllerName.php"
                )) {
                    include_once _PS_OVERRIDE_DIR_ . "modules/{$moduleName}/controllers/admin/$controllerName.php";
                    $controllerClass = $controllerName . (
                        strpos($controllerName, 'Controller') ? 'Override' : 'ControllerOverride'
                        );
                } else {
                    $controllerClass = $controllerName . (
                        strpos($controllerName, 'Controller') ? '' : 'Controller'
                        );
                }
            }
        } else {
            $controllers = Dispatcher::getControllers(
                [
                    _PS_ADMIN_CONTROLLER_DIR_,
                    _PS_OVERRIDE_DIR_ . 'controllers/admin/',
                ]
            );

            // Controller not found, previously the legacy Dispatcher rendered the first child if present which doesn't make sense.
            // It's clearer to actually return a not found exception
            if (!isset($controllers[strtolower($queryController)])) {
                throw new RouteNotFoundException(sprintf('Unknown controller %s', $queryController));
            }

            $controllerClass = $controllers[strtolower($queryController)];
        }

        return [
            'controller_type' => Dispatcher::FC_ADMIN,
            'controller_class' => $controllerClass,
            'is_module' => !empty($tab->getModule()),
        ];
    }
}
