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

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Help\Documentation;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;
use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorInterface;
use PrestaShop\PrestaShop\Core\Security\Permission;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Extends The Symfony framework bundle controller to add common functions for PrestaShop needs.
 *
 * @deprecated since 9.0 to be removed in future versions (10+ at least, when it will not be used anymore)
 */
class FrameworkBundleAdminController extends AbstractController implements ContainerAwareInterface
{
    /**
     * @deprecated since 9.0
     */
    public const PRESTASHOP_CORE_CONTROLLERS_TAG = 'prestashop.core.controllers';

    /**
     * Override to make this compatible with the ContainerAware signature, content should be the same as in the abstract.
     * Do not override this neither use this, it will be removed in next versions. This overridden method
     * along with the ContainerAwareInterface was added to skip the error sent by Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver
     * that forces the controllers extending AbstractController to be defined as service subscriber.
     *
     * This method allows us to keep controllers based on FrameworkBundleAdminController from being
     * adapted. However, the core and the modules should stop using it in favor of PrestaShopAdminController
     *
     * @internal
     */
    public function setContainer(ContainerInterface $container = null): ?ContainerInterface
    {
        $previous = $this->container;
        $this->container = $container;

        return $previous;
    }

    /**
     * @var string|null
     */
    protected $layoutTitle;

    /**
     * @return ShopConfigurationInterface
     */
    protected function getConfiguration(): ShopConfigurationInterface
    {
        return $this->container->get('prestashop.adapter.legacy.configuration');
    }

    /**
     * Returns form errors for JS implementation.
     *
     * Parse all errors mapped by id html field
     *
     * @param FormInterface $form
     *
     * @return array<array<string>> Errors
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     */
    public function getFormErrorsForJS(FormInterface $form)
    {
        $errors = [];

        if ($form->count() === 0) {
            return $errors;
        }

        $translator = $this->get('translator');

        foreach ($form->getErrors(true) as $error) {
            if ($error->getCause() && method_exists($error->getCause(), 'getPropertyPath')) {
                $formId = str_replace(
                    ['.', 'children[', ']', '_data'],
                    ['_', '', '', ''],
                    $error->getCause()->getPropertyPath()
                );
            } else {
                $formId = 'bubbling_errors';
            }

            if ($error->getMessagePluralization()) {
                $errors[$formId][] = $translator->trans(
                    $error->getMessageTemplate(),
                    array_merge(
                        $error->getMessageParameters(),
                        ['%count%' => $error->getMessagePluralization()]
                    ),
                    'validators'
                );
            } else {
                $errors[$formId][] = $translator->trans(
                    $error->getMessageTemplate(),
                    $error->getMessageParameters(),
                    'validators'
                );
            }
        }

        return $errors;
    }

    /**
     * Creates a HookEvent, sets its parameters, and dispatches it.
     *
     * Wrapper to: @see HookDispatcher::dispatchWithParameters()
     *
     * @param string $hookName The hook name
     * @param array $parameters The hook parameters
     */
    protected function dispatchHook($hookName, array $parameters)
    {
        $this->container->get('prestashop.core.hook.dispatcher')->dispatchWithParameters($hookName, $parameters);
    }

    /**
     * Creates a RenderingHookEvent, sets its parameters, and dispatches it. Returns the event with the response(s).
     *
     * Wrapper to: @see HookDispatcher::renderForParameters()
     *
     * @param string $hookName The hook name
     * @param array $parameters The hook parameters
     *
     * @return array The responses of hooks
     *
     * @throws Exception
     */
    protected function renderHook($hookName, array $parameters)
    {
        return $this->container->get('prestashop.core.hook.dispatcher')->renderForParameters($hookName, $parameters)->getContent();
    }

    /**
     * Generates a documentation link.
     *
     * @param string $section Legacy controller name
     * @param bool|string $title Help title
     *
     * @return string
     */
    protected function generateSidebarLink($section, $title = false)
    {
        $legacyContext = $this->get('prestashop.adapter.legacy.context');

        if (empty($title)) {
            $title = $this->trans('Help', 'Admin.Global');
        }

        $iso = (string) $legacyContext->getEmployeeLanguageIso();

        $url = $this->generateUrl('admin_common_sidebar', [
            'url' => $this->container->get(Documentation::class)->generateLink($section, $iso),
            'title' => $title,
        ]);

        //this line is allow to revert a new behaviour introduce in sf 5.4 which break the result we used to have
        return strtr($url, ['%2F' => '%252F']);
    }

    /**
     * Get the old but still useful context.
     *
     * @return \Context
     */
    protected function getContext()
    {
        return $this->get('prestashop.adapter.legacy.context')->getContext();
    }

    /**
     * @return string
     *
     * //@todo: is there a better way using currency iso_code?
     */
    protected function getContextCurrencyIso(): string
    {
        return $this->getContext()->currency->iso_code;
    }

    /**
     * Get the locale based on the context
     *
     * @return Locale
     */
    protected function getContextLocale(): Locale
    {
        $locale = $this->getContext()->getCurrentLocale();
        if (null !== $locale) {
            return $locale;
        }

        /** @var LocaleRepository $localeRepository */
        $localeRepository = $this->get('prestashop.core.localization.locale.repository');
        $locale = $localeRepository->getLocale(
            $this->getContext()->language->getLocale()
        );

        return $locale;
    }

    /**
     * @param string $lang
     *
     * @return mixed
     */
    protected function langToLocale($lang)
    {
        return $this->container->get('prestashop.service.translation')->langToLocale($lang);
    }

    /**
     * @return bool
     */
    protected function isDemoModeEnabled()
    {
        return $this->getConfiguration()->get('_PS_MODE_DEMO_');
    }

    /**
     * @return string
     */
    protected function getDemoModeErrorMessage()
    {
        return $this->trans('This functionality has been disabled.', 'Admin.Notifications.Error');
    }

    /**
     * Checks if the attributes are granted against the current authentication token and optionally supplied object.
     *
     * @param string $controller name of the controller that token is tested against
     *
     * @return int
     *
     * @throws \LogicException
     */
    protected function authorizationLevel($controller)
    {
        if ($this->isGranted(Permission::DELETE, $controller)) {
            return Permission::LEVEL_DELETE;
        }

        if ($this->isGranted(Permission::CREATE, $controller)) {
            return Permission::LEVEL_CREATE;
        }

        if ($this->isGranted(Permission::UPDATE, $controller)) {
            return Permission::LEVEL_UPDATE;
        }

        if ($this->isGranted(Permission::READ, $controller)) {
            return Permission::LEVEL_READ;
        }

        return 0;
    }

    /**
     * Get the translated chain from key.
     *
     * @param string $key the key to be translated
     * @param string $domain the domain to be selected
     * @param array $parameters Optional, pass parameters if needed (uncommon)
     *
     * @return string
     */
    protected function trans($key, $domain, array $parameters = [])
    {
        return $this->container->get('translator')->trans($key, $parameters, $domain);
    }

    /**
     * Return errors as flash error messages.
     *
     * @param array $errorMessages
     *
     * @throws \LogicException
     */
    protected function flashErrors(array $errorMessages)
    {
        foreach ($errorMessages as $error) {
            $message = is_array($error) ? $this->trans($error['key'], $error['domain'], $error['parameters']) : $error;
            $this->addFlash('error', $message);
        }
    }

    /**
     * Redirect employee to default page.
     *
     * @return RedirectResponse
     */
    protected function redirectToDefaultPage()
    {
        $legacyContext = $this->container->get('prestashop.adapter.legacy.context');
        $defaultTab = $legacyContext->getDefaultEmployeeTab();

        return $this->redirect($legacyContext->getAdminLink($defaultTab));
    }

    /**
     * Check if the connected user is granted to actions on a specific object.
     *
     * @param string $action
     * @param string $object
     * @param string $suffix
     *
     * @return bool
     *
     * @throws \LogicException
     */
    protected function actionIsAllowed($action, $object = '', $suffix = '')
    {
        return (
                $action === 'delete' . $suffix && $this->isGranted(Permission::DELETE, $object)
            ) || (
                ($action === 'activate' . $suffix || $action === 'deactivate' . $suffix) &&
                $this->isGranted(Permission::UPDATE, $object)
            ) || (
                ($action === 'duplicate' . $suffix) &&
                ($this->isGranted(Permission::UPDATE, $object) || $this->isGranted(Permission::CREATE, $object))
            );
    }

    /**
     * Display a message about permissions failure according to an action.
     *
     * @param string $action
     * @param string $suffix
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getForbiddenActionMessage($action, $suffix = '')
    {
        if ($action === 'delete' . $suffix) {
            return $this->trans('You do not have permission to delete this.', 'Admin.Notifications.Error');
        }

        if ($action === 'deactivate' . $suffix || $action === 'activate' . $suffix) {
            return $this->trans('You do not have permission to edit this.', 'Admin.Notifications.Error');
        }

        if ($action === 'duplicate' . $suffix) {
            return $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error');
        }

        throw new Exception(sprintf('Invalid action (%s)', $action . $suffix));
    }

    /**
     * Get fallback error message when something unexpected happens.
     *
     * @param string $type
     * @param int $code
     * @param string $message
     *
     * @return string
     */
    protected function getFallbackErrorMessage($type, $code, $message = '')
    {
        $isDebug = $this->container->get('kernel')->isDebug();
        if ($isDebug && !empty($message)) {
            return $this->trans(
                'An unexpected error occurred. [%type% code %code%]: %message%',
                'Admin.Notifications.Error',
                [
                    '%type%' => $type,
                    '%code%' => $code,
                    '%message%' => $message,
                ]
            );
        }

        return $this->trans(
            'An unexpected error occurred. [%type% code %code%]',
            'Admin.Notifications.Error',
            [
                '%type%' => $type,
                '%code%' => $code,
            ]
        );
    }

    /**
     * Get Admin URI from PrestaShop 1.6 Back Office.
     *
     * @param string $controller the old Controller name
     * @param bool $withToken whether we add token or not
     * @param array $params url parameters
     *
     * @return string the page URI (with token)
     */
    protected function getAdminLink($controller, array $params, $withToken = true)
    {
        return $this->container->get('prestashop.adapter.legacy.context')->getAdminLink($controller, $withToken, $params);
    }

    /**
     * Present provided grid.
     *
     * @param GridInterface $grid
     *
     * @return array
     */
    protected function presentGrid(GridInterface $grid)
    {
        return $this->container->get('prestashop.core.grid.presenter.grid_presenter')->present($grid);
    }

    /**
     * Get commands bus to execute commands.
     *
     * @return \PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface
     */
    protected function getCommandBus()
    {
        return $this->container->get('prestashop.core.command_bus');
    }

    /**
     * Get query bus to execute queries.
     *
     * @return \PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface
     */
    protected function getQueryBus()
    {
        return $this->container->get('prestashop.core.query_bus');
    }

    /**
     * @param array $errors
     * @param int $httpStatusCode
     *
     * @return JsonResponse
     */
    protected function returnErrorJsonResponse(array $errors, $httpStatusCode)
    {
        $response = new JsonResponse();
        $response->setStatusCode($httpStatusCode);
        $response->setData($errors);

        return $response;
    }

    /**
     * @return int
     */
    protected function getContextLangId()
    {
        return $this->getContext()->language->id;
    }

    /**
     * @return int
     */
    protected function getContextShopId()
    {
        return $this->getContext()->shop->id;
    }

    /**
     * @param FormInterface $form
     */
    protected function addFlashFormErrors(FormInterface $form)
    {
        /** @var FormError $formError */
        foreach ($form->getErrors(true) as $formError) {
            $this->addFlash('error', $formError->getMessage());
        }
    }

    /**
     * Get error by exception from given messages
     *
     * @param Exception $e
     * @param array $messages
     *
     * @return string
     */
    protected function getErrorMessageForException(Exception $e, array $messages)
    {
        if ($e instanceof ModuleErrorInterface) {
            return $e->getMessage();
        }

        $exceptionType = $e::class;
        $exceptionCode = $e->getCode();

        if (isset($messages[$exceptionType])) {
            $message = $messages[$exceptionType];

            if (is_string($message)) {
                return $message;
            }

            if (is_array($message) && isset($message[$exceptionCode])) {
                return $message[$exceptionCode];
            }
        }

        return $this->getFallbackErrorMessage(
            $exceptionType,
            $exceptionCode,
            $e->getMessage()
        );
    }
}
