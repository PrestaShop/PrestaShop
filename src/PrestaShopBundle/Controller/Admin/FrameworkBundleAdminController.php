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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShopBundle\Security\Voter\PageVoter;

/**
 * Extends The Symfony framework bundle controller to add common functions for PrestaShop needs.
 */
class FrameworkBundleAdminController extends Controller
{
    protected $configuration;

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
        return array(
            'is_shop_context' => (new Context())->isShopContext(),
            'layoutTitle' => empty($this->layoutTitle) ? '' : $this->trans($this->layoutTitle, 'Admin.Navigation.Menu'),
        );
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
     * @return array[array[string]] Errors
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
                $form_id = 'bubbling_errors';
            } else {
                $form_id = str_replace(
                    ['.', 'children[', ']', '_data'],
                    ['_', '', '', ''],
                    $error->getCause()->getPropertyPath()
                );
            }

            if ($error->getMessagePluralization()) {
                $errors[$form_id][] = $translator->transchoice(
                    $error->getMessageTemplate(),
                    $error->getMessagePluralization(),
                    $error->getMessageParameters(),
                    'form_error'
                );
            } else {
                $errors[$form_id][] = $translator->trans(
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
     * @param $hookName The hook name
     * @param $parameters The hook parameters
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
     * @param $hookName The hook name
     * @param $parameters The hook parameters
     * @return array The responses of hooks
     */
    protected function renderHook($hookName, array $parameters)
    {
        return $this->get('prestashop.hook.dispatcher')->renderForParameters($hookName, $parameters)->getContent();
    }

    /**
     * Generates a documentation link
     */
    protected function generateSidebarLink($section, $title = false)
    {
        $legacyContext = $this->get('prestashop.adapter.legacy.context');

        if (empty($title)) {
            $title = $this->trans('Help', 'Admin.Global');
        }

        $docLink = urlencode('https://help.prestashop.com/'.$legacyContext->getEmployeeLanguageIso().'/doc/'
            .$section.'?version='._PS_VERSION_.'&country='.$legacyContext->getEmployeeLanguageIso());

        return $this->generateUrl('admin_common_sidebar', [
            'url' => $docLink,
            'title' => $title,
        ]);
    }

    /**
     * Get the old but still useful context
     *
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
     * @return mixed
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
     * @param mixed $controller name of the controller to valide access
     *
     * @return int
     */
    protected function authorizationLevel($controller)
    {
        if (
            $this->isGranted(PageVoter::DELETE, $controller.'_')) {
            return PageVoter::LEVEL_DELETE;
        } elseif ($this->isGranted(PageVoter::CREATE, $controller.'_')) {
            return PageVoter::LEVEL_CREATE;
        } elseif ($this->isGranted(PageVoter::UPDATE, $controller.'_')) {
            return PageVoter::LEVEL_UPDATE;
        } elseif ($this->isGranted(PageVoter::READ, $controller.'_')) {
            return PageVoter::LEVEL_READ;
        } else {
            return 0;
        }
    }

    /**
     * Get the translated chain from key
     *
     * @param $key the key to be translated
     * @param $domain the domain to be selected
     * @param array $parameters Optional, pass parameters if needed (uncommon)
     *
     * @returns string
     */
    protected function trans($key, $domain, $parameters = array())
    {
        return $this->get('translator')->trans($key, $parameters, $domain);
    }

    /**
     * Return errors as flash error messages
     *
     * @param array $errorMessages
     */
    protected function flashErrors(array $errorMessages)
    {
        foreach ($errorMessages as $error) {
            $this->addFlash('error', $this->trans($error['key'], $error['domain'], $error['parameters']));
        }
    }

    /**
     * Redirect employee to default page
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
     */
    protected function shouldDenyAction($action, $object = '', $suffix = '')
    {
        return (
                $action === 'delete' . $suffix && !$this->isGranted(PageVoter::DELETE, $object)
            ) || (
                ($action === 'activate' . $suffix || $action === 'deactivate' . $suffix) &&
                !$this->isGranted(PageVoter::UPDATE, $object)
            ) || (
                ($action === 'duplicate' . $suffix) &&
                (!$this->isGranted(PageVoter::UPDATE, $object) || !$this->isGranted(PageVoter::CREATE, $object))
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
}
