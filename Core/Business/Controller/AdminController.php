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

use Symfony\Component\Routing\RequestContext;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Core\Foundation\Log\MessageStackManager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShop\PrestaShop\Core\Business\Context;

/**
 * Base class for all Admin controllers. Others won't be accepted by AdminRouter.
 * You must extends this one, and use traits that you need.
 * For more explanations about action functions normalization, please read:
 * @see PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController
 */
class AdminController extends BaseController
{
    use AdminAuthenticationTrait;
    
    /**
     * This function should encapsulate the content to display into an HTML layout (menu, headers, footers, etc...)
     * Implements it and use $response->getContent() to retrieve the main content.
     * Once you encapsulated the content in the layout, use $response->setContent() to store the result.
     *
     * @param Response $response
     */
    protected function encapsulateLayout(Response &$response)
    {
        // Display catched WarningExceptions (not catched one will fail display and will be catched by Router->dispatch())
        $warningBlock = '';
        if ($this->getWarningIterator()) {
            $warningBlock = $response->getTemplateEngine()->view->fetch('Core/system_messages.tpl', array(
                'exceptions' => $this->getWarningIterator(),
                'color' => 'orange'
            ));
        }

        //GET LAYOUT FROM ORIGINAL CONTROLLER REQUESTED
        $orginCtrl = new \AdminLegacyLayoutController($response->getLegacyControllerName());
        $orginCtrl->run();

        $link = new \Link();
        $content = str_replace(
            array(
                '{$content}',
                'var currentIndex = \'index.php\';'),
            array(
                $response->getContent(),
                'var currentIndex = \''.$link->getAdminLink($response->getLegacyControllerName()).'\';'),
            $orginCtrl->outPutHtml
        );

        $response->setContent($warningBlock.$content);
    }

    /**
     * This function will format data in an HTML result. Most of the time, you should use a template engine to render
     * the response. The data given by the controller action is available in $response->getContentData(), and once
     * you rendered the HTML content, you should put it in $response->setContent().
     *
     * @param Response $response
     */
    protected function formatHtmlResponse(Response &$response)
    {
        $templateEngine = $response->getTemplateEngine();
        $response->setContent($templateEngine->view->fetch($response->getTemplate(), $response->getContentData()));
    }
    
    /**
     * Override this in your controller if you want to allow anonymous users to call it.
     * For example, for Login controllers, should be overriden to return false.
     *
     * @return boolean True if authenticated user is needed. False if the controller can be called by anonymous users.
     */
    protected function isAuthenticationNeeded()
    {
        return true;
    }
    
    /**
     * Generates a URL or path for a specific ADMIN route based on the given parameters.
     *
     * This is a Wrapper for the Symfony method:
     * @see \Symfony\Component\Routing\Generator\UrlGeneratorInterface::generate()
     * but also adds a legacy URL generation support for Admin interface.
     *
     * @param string      $name             The name of the route
     * @param mixed       $parameters       An array of parameters (to use in route matching, or to add as GET values if $forceLegacyUrl is True)
     * @param bool        $forceLegacyUrl   True to use alternative URL to reach legacy dispatcher.
     * @param bool|string $referenceType The type of reference to be generated (one of the constants)
     *
     * @return string The generated URL
     *
     * @throws RouteNotFoundException              If the named route doesn't exist
     * @throws MissingMandatoryParametersException When some parameters are missing that are mandatory for the route
     * @throws InvalidParameterException           When a parameter value for a placeholder is not correct because
     *                                             it does not match the requirement
     */
    final public function generateUrl($name, $parameters = array(), $forceLegacyUrl = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        if (($routeParams = $this->getRouter()->getRouteParameters($name)) &&
            ($defaultParams = $routeParams->getDefaults()) &&
            ($forceLegacyUrl == true || (isset($defaultParams['_legacy_force']) && $defaultParams['_legacy_force'] === true)) &&
            isset($defaultParams['_legacy_path']) &&
            ($link = Context::getInstance()->link)) { // For legacy case!
            $legacyPath = $defaultParams['_legacy_path'];

            $legacyContext = \Adapter_ServiceLocator::get('Adapter_LegacyContext');
            $basePath = $legacyContext->getAdminBaseUrl();

//             switch ($referenceType) {
//                 case UrlGeneratorInterface::ABSOLUTE_URL:
//                     return $basePath.$link->getAdminLink($legacyPath);
//                 case UrlGeneratorInterface::ABSOLUTE_PATH:
//                 default:
                    return $basePath.$legacyContext->getAdminLink($legacyPath, true, $parameters);
//             }
        }
        return parent::generateUrl($name, $parameters, false, $referenceType);
    }
}
