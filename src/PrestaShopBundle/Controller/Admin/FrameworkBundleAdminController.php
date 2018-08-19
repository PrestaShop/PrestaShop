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
namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShopBundle\Security\Voter\PageVoter;

/**
 * Extends The Symfony framework bundle controller to add common functions for PrestaShop needs.
 */
class FrameworkBundleAdminController extends Controller
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var string|null
     */
    protected $layoutTitle;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->configuration = new Configuration();
    }

    /**
     * @Template
     *
     * @return array Template vars
     */
    public function overviewAction()
    {
        return [
            'is_shop_context' => (new Context())->isShopContext(),
            'layoutTitle' => empty($this->layoutTitle) ? '' : $this->trans($this->layoutTitle, 'Admin.Navigation.Menu'),
        ];
    }

    public function hashUpdateJsAction($hash)
    {
        $contents = file_get_contents('http://localhost:8080/' . $hash . '.hot-update.js');

        return new Response($contents);
    }

    public function hashUpdateJsonAction($hash)
    {
        $contents = file_get_contents('http://localhost:8080/' . $hash . '.hot-update.json');

        return new Response($contents);
    }

    /**
     * Returns form errors for JS implementation.
     *
     * Parse all errors mapped by id html field
     *
     * @param Form $form The form
     *
     * @return array[array[string]] Errors
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     */
    public function getFormErrorsForJS(Form $form)
    {
        $errors = [];

        if (empty($form)) {
            return $errors;
        }

        $translator = $this->get('translator');

        foreach ($form->getErrors(true) as $error) {
            if (!$error->getCause()) {
                $formId = 'bubbling_errors';
            } else {
                $formId = str_replace(
                    ['.', 'children[', ']', '_data'],
                    ['_', '', '', ''],
                    $error->getCause()->getPropertyPath()
                );
            }

            if ($error->getMessagePluralization()) {
                $errors[$formId][] = $translator->transChoice(
                    $error->getMessageTemplate(),
                    $error->getMessagePluralization(),
                    $error->getMessageParameters(),
                    'form_error'
                );
            } else {
                $errors[$formId][] = $translator->trans(
                    $error->getMessageTemplate(),
                    $error->getMessageParameters(),
                    'form_error'
                );
            }
        }

        return $errors;
    }

    /**
     * Creates a HookEvent, sets its parameters, and dispatches it.
     *
     * Wrapper to: @see HookDispatcher::dispatchForParameters()
     *
     * @param string $hookName   The hook name
     * @param array  $parameters The hook parameters
     */
    protected function dispatchHook($hookName, array $parameters)
    {
        $this->get('prestashop.hook.dispatcher')->dispatchForParameters($hookName, $parameters);
    }

    /**
     * Creates a RenderingHookEvent, sets its parameters, and dispatches it. Returns the event with the response(s).
     *
     * Wrapper to: @see HookDispatcher::renderForParameters()
     *
     * @param string $hookName   The hook name
     * @param array  $parameters The hook parameters
     *
     * @return array The responses of hooks
     * @throws \Exception
     */
    protected function renderHook($hookName, array $parameters)
    {
        return $this->get('prestashop.hook.dispatcher')->renderForParameters($hookName, $parameters)->getContent();
    }

    /**
     * Generates a documentation link
     *
     * @param string      $section Legacy controller name
     * @param bool|string $title   Help title
     *
     * @return string
     */
    protected function generateSidebarLink($section, $title = false)
    {
        $version = $this->get('prestashop.core.foundation.version')->getVersion();
        $legacyContext = $this->get('prestashop.adapter.legacy.context');

        if (empty($title)) {
            $title = $this->trans('Help', 'Admin.Global');
        }

        $docLink = urlencode('https://help.prestashop.com/'.$legacyContext->getEmployeeLanguageIso().'/doc/'
            .$section.'?version='.$version.'&country='.$legacyContext->getEmployeeLanguageIso());

        return $this->generateUrl('admin_common_sidebar', [
            'url' => $docLink,
            'title' => $title,
        ]);
    }

    /**
     * Get the old but still useful context
     *
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    protected function getContext()
    {
        return $this->get('prestashop.adapter.legacy.context')->getContext();
    }

    /**
     * @param $lang
     * @return mixed
     */
    protected function langToLocale($lang)
    {
        return $this->get('prestashop.service.translation')->langToLocale($lang);
    }

    /**
     * @return bool
     */
    protected function isDemoModeEnabled()
    {
        return $this->get('prestashop.adapter.legacy.configuration')->get('_PS_MODE_DEMO_');
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
     * @param string $controller name of the controller to valide access
     *
     * @return int
     * @throws \LogicException
     */
    protected function authorizationLevel($controller)
    {
        if ($this->isGranted(PageVoter::DELETE, $controller.'_')) {
            return PageVoter::LEVEL_DELETE;
        }

        if ($this->isGranted(PageVoter::CREATE, $controller.'_')) {
            return PageVoter::LEVEL_CREATE;
        }

        if ($this->isGranted(PageVoter::UPDATE, $controller.'_')) {
            return PageVoter::LEVEL_UPDATE;
        }

        if ($this->isGranted(PageVoter::READ, $controller.'_')) {
            return PageVoter::LEVEL_READ;
        }

        return 0;
    }

    /**
     * Get the translated chain from key
     *
     * @param string $key the key to be translated
     * @param string $domain the domain to be selected
     * @param array $parameters Optional, pass parameters if needed (uncommon)
     *
     * @return string
     */
    protected function trans($key, $domain, array $parameters = [])
    {
        return $this->get('translator')->trans($key, $parameters, $domain);
    }

    /**
     * Return errors as flash error messages
     *
     * @param array $errorMessages
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
     * Redirect employee to default page
     *
     * @return RedirectResponse
     */
    protected function redirectToDefaultPage()
    {
        $legacyContext = $this->get('prestashop.adapter.legacy.context');
        $defaultTab = $legacyContext->getDefaultEmployeeTab();

        return $this->redirect($legacyContext->getAdminLink($defaultTab));
    }

    /**
     * Check if the connected user is granted to actions on a specific object.
     * @param $action
     * @param $object
     * @param string $suffix
     * @return bool
     * @throws \LogicException
     */
    protected function actionIsAllowed($action, $object = '', $suffix = '')
    {
        return (
                $action === 'delete' . $suffix && $this->isGranted(PageVoter::DELETE, $object)
            ) || (
                ($action === 'activate' . $suffix || $action === 'deactivate' . $suffix) &&
                $this->isGranted(PageVoter::UPDATE, $object)
            ) || (
                ($action === 'duplicate' . $suffix) &&
                ($this->isGranted(PageVoter::UPDATE, $object) || $this->isGranted(PageVoter::CREATE, $object))
            )
        ;
    }

    /**
     * Display a message about permissions failure according to an action.
     * @param $action
     * @param string $suffix
     * @return string
     * @throws \Exception
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

        throw new \Exception(sprintf('Invalid action (%s)', $action . $suffix));
    }

    /**
     * Get Admin URI from PrestaShop 1.6 Back Office.
     * @param string $controller the old Controller name
     * @param bool $withToken whether we add token or not
     * @param array $params url parameters
     *
     * @return string the page URI (with token)
     */
    protected function getAdminLink($controller, array $params, $withToken = true)
    {
        return $this->get('prestashop.adapter.legacy.context')->getAdminLink($controller, $withToken, $params);
    }

    /**
     * Get commands bus to execute commands
     *
     * @return \League\Tactician\CommandBus
     */
    protected function getCommandBus()
    {
        return $this->get('tactician.commandbus');
    }

    /**
     * Get query bus to execute queries
     *
     * @return \League\Tactician\CommandBus
     */
    protected function getQueryBus()
    {
        return $this->get('tactician.commandbus');
    }
}
