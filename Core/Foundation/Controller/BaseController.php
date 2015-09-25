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
namespace PrestaShop\PrestaShop\Core\Foundation\Controller;

use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Foundation\Log\MessageStackManager;
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;

/**
 * First layer of common implementation for controllers.
 * The router and the container is linked during instantiation (for wrapper methods).
 * Standard formats and encapsulations are managed, but some of them requires
 * abstract methods to be implemented in subclasses.
 */
abstract class BaseController implements ControllerInterface
{
    /**
     * @var AbstractRouter
     */
    protected $router;

    
    /**
     * @var Container
     */
    protected $container;

    /**
     * Instantiate the Controller. Often made from a Router.
     *
     * @param AbstractRouter $router The Router instance that instantiated the Controller.
     * @param Container $container The application container.
     */
    public function __construct(AbstractRouter &$router, Container &$container)
    {
        $this->router = $router;
        $this->container = $container;
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::formatResponse()
     */
    public function formatResponse($format, Response &$response)
    {
        switch ($format) {
            case 'html':
                $this->formatHtmlResponse($response);
                break;
            case 'json':
                $this->formatJsonResponse($response);
                break;
            case 'xml':
                throw new DevelopmentErrorException('Not yet supported!');
            case 'raw':
                return;
            case 'none':
                $this->router->exitNow(); // Break PHP process! Controller action should have already sent its result by itself (file, binary, etc...)
            default:
                throw new DevelopmentErrorException('Unknown format.');
        }
    }

    /**
     * This function will format data in an HTML result.
     *
     * Most of the time, you should use a template engine to render
     * the response. The data given by the controller action is available in $response->getContentData(), and once
     * you rendered the HTML content, you should put it in $response->setContent().
     *
     * @param Response $response
     */
    abstract protected function formatHtmlResponse(Response &$response);

    /**
     * This will format data from $response->getContentData() into JSON format.
     *
     * @param Response $response
     */
    final protected function formatJsonResponse(Response &$response)
    {
        $content = $response->getContentData();
        $configuration = $this->container->make('Core_Business_ConfigurationInterface');
        $response->setContent(json_encode($content, $configuration->get('_PS_MODE_DEV_') ? JSON_PRETTY_PRINT : 0));
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::encapsulateResponse()
     */
    public function encapsulateResponse($encapsulation, Response &$response)
    {
        switch ($encapsulation) {
            case 'layout':
                $this->encapsulateLayout($response);
                break;
            case 'nude':
                $this->encapsulateNudeHtml($response);
                break;
            case 'none':
                return;
            default:
                throw new DevelopmentErrorException('Unknown encapsulation.');
        }
    }

    /**
     * This function will encapsulate an HTML content.
     *
     * ...into a very smart HTML layout, with the minimum required to be valid HTML document.
     * If you need more HTML stuff in this mode, override this function in your extended class.
     *
     * @param Response $response
     */
    protected function encapsulateNudeHtml(Response &$response)
    {
        $content = $response->getContent();
        $content = '<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="robots" content="index, follow, all" />
        <title>Carrément a voir !</title>
    </head>
    <body>
'.$content.'
    </body>
</html>';
        $response->setContent($content);
    }

    /**
     * This function should encapsulate the content to display into an HTML layout (menu, headers, footers, etc...).
     *
     * Implements it and use $response->getContent() to retrieve the main content.
     * Once you encapsulated the content in the layout, use $response->setContent() to store the result.
     *
     * @param Response $response
     */
    abstract protected function encapsulateLayout(Response &$response);

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::getErrorIterator()
     */
    final public function getErrorIterator()
    {
        return $this->container->make('MessageStack')->getErrorIterator();
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::getWarningIterator()
     */
    final public function getWarningIterator()
    {
        return $this->container->make('MessageStack')->getWarningIterator();
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::getInfoIterator()
     */
    final public function getInfoIterator()
    {
        return $this->container->make('MessageStack')->getInfoIterator();
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::getSuccessIterator()
     */
    final public function getSuccessIterator()
    {
        return $this->container->make('MessageStack')->getSuccessIterator();
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::getRouter()
     */
    final public function getRouter()
    {
        return $this->router;
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::generateUrl()
     */
    final public function generateUrl($name, $parameters = array(), $forceLegacyUrl = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        return $this->getRouter()->generateUrl($name, $parameters, $forceLegacyUrl, $referenceType);
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::subcall()
     */
    final public function subcall($name, $parameters = array(), $layoutMode = BaseController::RESPONSE_PARTIAL_VIEW, $fullResponse = false)
    {
        return $this->getRouter()->subcall($name, $parameters, $layoutMode, $fullResponse);
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::forward()
     */
    final public function forward(Request &$oldRequest, $routeName, $routeParameters = array())
    {
        return $this->getRouter()->forward($oldRequest, $routeName, $routeParameters);
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::redirectToRoute()
     */
    final public function redirectToRoute(Request &$oldRequest, $routeName, $routeParameters, $forceLegacyUrl = false, $permanent = false)
    {
        $this->getRouter()->redirectToRoute($oldRequest, $routeName, $routeParameters, $forceLegacyUrl, $permanent);
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::redirect()
     */
    final public function redirect($to, $permanent = false)
    {
        $this->getRouter()->redirect($to, $permanent);
    }
}
