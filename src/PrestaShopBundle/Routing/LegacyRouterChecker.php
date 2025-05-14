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

declare(strict_types=1);

namespace PrestaShopBundle\Routing;

use AdminController;
use Dispatcher;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Security\Permission;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Routing\Converter\LegacyParametersConverter;
use PrestaShopBundle\Security\Admin\RequestAttributes;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Routing\Attribute\AsRoutingConditionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This checker is bound to the LegacyController that wraps legacy controller, its matching condition are based on the query parameters.
 * Its priority is set low on purpose because it should be the last route to match in favor of all the other "real" Symfony route.
 */
#[AsRoutingConditionService(priority: -1)]
class LegacyRouterChecker
{
    public function __construct(
        protected readonly TabRepository $tabRepository,
        protected readonly HookDispatcherInterface $hookDispatcher,
        protected readonly LegacyParametersConverter $legacyParametersConverter,
        protected readonly LegacyContext $legacyContext,
    ) {
    }

    public function check(Request $request): bool
    {
        if (!$request->query->has('controller') && !$request->request->has('controller')) {
            return false;
        }

        $controller = $request->get('controller');
        if (empty($controller)) {
            return false;
        }

        // Now we are in a condition to display a legacy controller, so we check that the controller class exists
        $queryController = $request->get('controller');
        $this->hookDispatcher->dispatchWithParameters('actionDispatcherBefore', ['controller_type' => Dispatcher::FC_ADMIN]);
        $tab = $this->tabRepository->findOneByClassName($queryController);
        $isModule = $tab && !empty($tab->getModule());

        if ($isModule) {
            $moduleName = $tab->getModule();
            $controllers = Dispatcher::getControllers(_PS_MODULE_DIR_ . $moduleName . '/controllers/admin/');
            if (!isset($controllers[strtolower($queryController)])) {
                throw new NotFoundHttpException(sprintf(
                    'Controller %s was not found. It belonged to module %s, make sure it is still installed.',
                    $queryController,
                    $moduleName
                ));
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
            // It's clearer to actually return a not found exception, for now the dispatcher is still used as fallback in index.php
            // but when it's cleared and only Symfony handles the whole routing then we can display a proper not found Symfony page
            if (!isset($controllers[strtolower($queryController)])) {
                $controllerClass = 'AdminNotFoundController';
            } else {
                $controllerClass = $controllers[strtolower($queryController)];
            }
            $controllerName = $queryController;
        }

        // Init shop context from legacy cookie because the ShopContextSubscriber cannot rely on the session attribute yet
        // since the router listener is executed before the FirewallListener This way we ensure retro compatibility and legacy
        // controllers that are executed early and have multi-shop logic in their init, initProcess, preProcess, ... methods are
        // up-to-date
        $this->initLegacyShopContextFromCookie();

        // We load the controller early in the process (during router matching actually), because the controller
        // configuration has many impacts on the contexts, the security listeners, ... And the relevant data can
        // only be retrieved once the legacy class is instantiated to access its public configuration
        // But for performance issues we only instantiate (and init) the controller here once and then store it (along
        // with other related attributes) in the request attributes so they can be retrieved easily by the code depending on them
        /** @var AdminController $adminController */
        $adminController = new $controllerClass();
        $adminController->init();

        $request->attributes->set(LegacyControllerConstants::INSTANCE_ATTRIBUTE, $adminController);
        $request->attributes->set(RequestAttributes::ANONYMOUS_CONTROLLER_ATTRIBUTE, $adminController->isAnonymousAllowed());
        $request->attributes->set(LegacyControllerConstants::MULTISHOP_CONTEXT_ATTRIBUTE, $adminController->multishop_context);
        $request->attributes->set(LegacyControllerConstants::CONTROLLER_CLASS_ATTRIBUTE, $controllerClass);
        $request->attributes->set(LegacyControllerConstants::IS_MODULE_ATTRIBUTE, $isModule);

        // Strip the ending Controller part
        if (str_ends_with($controllerName, 'Controller')) {
            $controllerName = substr($controllerName, 0, -strlen('Controller'));
        }
        $request->attributes->set(LegacyControllerConstants::CONTROLLER_NAME_ATTRIBUTE, $controllerName);
        $request->attributes->set(LegacyControllerConstants::CONTROLLER_ACTION_ATTRIBUTE, $this->getPermission($request, $controller->table ?? ''));

        return true;
    }

    private function getPermission(Request $request, string $table): string
    {
        $action = $this->getLegacyAction($request, $table);

        switch (true) {
            case str_starts_with($action, 'add'):
            case str_starts_with($action, 'generator'):
            case str_starts_with($action, 'new'):
                return Permission::CREATE;
            case str_starts_with($action, 'edit'):
            case str_starts_with($action, 'update'):
            case str_starts_with($action, 'options'):
            case str_starts_with($action, 'status'): // This is the legacy toggle
                return Permission::UPDATE;
            case str_starts_with($action, 'delete'):
                return Permission::DELETE;
            case $action === '': // In legacy empty action is usually the listing which a specific case for the view
            case str_starts_with($action, 'export'):
            case str_starts_with($action, 'details'):
            case str_starts_with($action, 'view'):
            case str_starts_with($action, 'list'):
            default:
                return Permission::READ;
        }
    }

    private function getLegacyAction(Request $request, string $table): string
    {
        // Get action from legacy parameters set on the route when we are in a Symfony page
        $legacyParameters = $this->legacyParametersConverter->getParameters(
            $request->attributes->all(),
            $request->query->all()
        );

        return $legacyParameters['action'] ?? $this->getLegacyActionFromQuery($request, $table);
    }

    private function getLegacyActionFromQuery(Request $request, string $controllerTable): string
    {
        return match (true) {
            $request->query->has('deleteImage') || $request->query->has('delete' . $controllerTable) => 'delete',
            // This is the legacy toggle
            $request->query->has('status' . $controllerTable) || $request->query->has('status') => 'edit',
            $request->query->has('duplicate' . $controllerTable) => 'duplicate',
            $request->query->has('position') => 'edit',
            $request->query->has('add' . $controllerTable) => 'add',
            $request->query->has('update' . $controllerTable) || $request->query->has('edit' . $controllerTable) => 'edit',
            $request->query->has('view' . $controllerTable) || $request->query->has('list' . $controllerTable) => 'view',
            $request->query->has('details' . $controllerTable) || $request->query->has('export' . $controllerTable) => 'view',
            $request->query->has('action') && !empty($request->query->get('action')) => $request->query->get('action'),
            default => 'view',
        };
    }

    private function initLegacyShopContextFromCookie(): void
    {
        $context = $this->legacyContext->getContext();
        $cookie = $context->cookie;
        if ($cookie->shopContext && $context->employee->isLoggedBack()) {
            $split = explode('-', $cookie->shopContext);
            if (count($split) == 2) {
                if ($split[0] == 'g') {
                    if ($context->employee->hasAuthOnShopGroup((int) $split[1])) {
                        LegacyShop::setContext(LegacyShop::CONTEXT_GROUP, (int) $split[1]);
                    } else {
                        $shop_id = (int) $context->employee->getDefaultShopID();
                        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, $shop_id);
                    }
                } elseif (LegacyShop::getShop((int) $split[1]) && $context->employee->hasAuthOnShop((int) $split[1])) {
                    $shop_id = (int) $split[1];
                    LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, $shop_id);
                } else {
                    $shop_id = (int) $context->employee->getDefaultShopID();
                    LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, $shop_id);
                }
            }
        }
    }
}
