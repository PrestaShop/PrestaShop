<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Business\Controller;

use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Core\Business\Routing\AdminRouter;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Adapter\Translator;
use Symfony\Component\Translation\Translator as SfTranslator;
use Symfony\Component\Form\Form;
use PrestaShop\PrestaShop\Core\Foundation\Twig\Extension\TranslationExtension as TwigTranslationExtension;

/**
 * Base class for all Admin controllers.
 *
 * Others won't be accepted by AdminRouter.
 * You must extends this one, and use traits that you need.
 * For more explanations about action functions normalization, please read:
 * @see PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController
 */
class AdminController extends BaseController
{
    /**
     * @var boolean
     */
    private $constructorCalled = false;

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController::__construct()
     */
    public function __construct(AdminRouter $router, Container $container)
    {
        parent::__construct($router, $container);
        $this->registerExecutionSequenceService($container->make('CoreBusiness:Controller\\ExecutionSequenceService\\AuthenticationMiddleware'));
        $this->constructorCalled = true;
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::isConstructionStrategyChecked()
     */
    final public function isConstructionStrategyChecked()
    {
        return parent::isConstructionStrategyChecked() && $this->constructorCalled;
    }

    /**
     * This function should encapsulate the content to display into an HTML layout (menu, headers, footers, etc...).
     *
     * {@inheritdoc}
     *
     * @param Response $response
     */
    protected function encapsulateLayout(Response &$response)
    {
        // Display catched WarningExceptions (not catched one will fail display and will be catched by Router->dispatch())
        $warningBlock = '';
        if (count($warnings = $this->dequeueAllWarnings()) > 0) {
            $warningBlock = $response->getTemplateEngine($this->container)->view->fetch('Core/system_messages.tpl', array(
                'exceptions' => $warnings,
                'color' => 'orange'
            ));
        }
        // Display notices and success messages
        $noticeBlock = '';
        if (count($infos = $this->dequeueAllInfos()) > 0) {
            $noticeBlock = $response->getTemplateEngine($this->container)->view->fetch('Core/user_messages.tpl', array(
                'messages' => $infos,
                'color' => 'blue'
            ));
        }
        if (count($successes = $this->dequeueAllSuccesses()) > 0) {
            $noticeBlock .= $response->getTemplateEngine($this->container)->view->fetch('Core/user_messages.tpl', array(
                'messages' => $successes,
                'color' => 'green'
            ));
        }

        //GET LAYOUT FROM ORIGINAL CONTROLLER REQUESTED
        $originCtrl = new \AdminLegacyLayoutControllerCore($response->getLegacyControllerName(), $response->getTitle(), $response->getHeaderToolbarBtn(), $response->getDisplayType(), $response->getJs(), $response->getCss());
        $originCtrl->run();

        $link = new \Link();
        $content = str_replace(
            array(
                '{$content}',
                'var currentIndex = \'index.php\';'),
            array(
                $warningBlock.$noticeBlock.$response->getContent(),
                'var currentIndex = \''.$link->getAdminLink($response->getLegacyControllerName()).'\';'),
            $originCtrl->outPutHtml
        );

        $response->setContent($content);
    }

    /**
     * This function will format data in an HTML result.
     *
     * {@inheritdoc}
     *
     * @param Response $response
     */
    protected function formatHtmlResponse(Response &$response)
    {
        $templateEngine = $response->getTemplateEngine($this->container);
        $response->setContent($templateEngine->view->fetch($response->getTemplate(), $response->getContentData()));
    }

    /**
     * This will allow you to retrieve an HTML code from the navigatorAction with a ready and linked navigator.
     *
     * To be able to use this navigator, the current route must have these standard parameters:
     * - offset
     * - limit
     * Both will be automatically manipulated by the navigator.
     * The navigator links (previous/next page...) will never tranfer POST and/or GET parameters
     * (only route parameters that are in the URL).
     *
     * The navigator will add a javascript dependency, and will add a $navigator variable in the response Data array.
     * So you just have to call this method and then use {$navigator} in your template.
     *
     * @param Request $request The original request to retrieve route parameters (to generate links)
     * @param Response $response The original response, to let the function add Javascript dependencies and the resulting navigator HTML part.
     * @param integer $totalCount The total count of elements to paginate (not the count of one page).
     */
    final protected function addNavigatorToResponse(Request &$request, Response &$response, $totalCount)
    {
        $navigatorParams = array_merge(
            $request->attributes->all(),
            array(
                '_total' => $totalCount,
            )
        );
        $navigator = $this->subcall('admin_tools_navigator', $navigatorParams, BaseController::RESPONSE_PARTIAL_VIEW);
        $response->addContentData('navigator', $navigator);
        $response->addJs(_PS_JS_DIR_.'Core/Admin/Navigator.js');

        return true; // success.
    }

    /**
     * This function returns form errors for JS implementation
     *
     * Parse all errors mapped by id html field
     *
     * @param Form $form The form
     * @return array[array[string]] Errors
     */
    final protected function getFormErrorsForJS(Form $form)
    {
        $errors = [];

        if (empty($form)) {
            return $errors;
        }

        $translator = new TwigTranslationExtension(new SfTranslator(''), $this->container);

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
}
