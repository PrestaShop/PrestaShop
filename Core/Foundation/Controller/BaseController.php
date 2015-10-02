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
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Core\Foundation\Exception\ErrorException;

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
     * @var boolean
     */
    private $constructorCalled = false;

    /**
     * Instantiate the Controller. Often made from the subclass.
     *
     * @param AbstractRouter $router The Router instance that instantiated the Controller.
     * @param Container $container The application container.
     */
    public function __construct(AbstractRouter $router, Container $container)
    {
        if ($this->constructorCalled) {
            throw new DevelopmentErrorException('Cannot instantiate a controller twice in the same process.');
        }

        $this->router = $router;
        $this->container = $container;

        $this->constructorCalled = true;
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::isConstructionStrategyChecked()
     */
    public function isConstructionStrategyChecked()
    {
        return $this->constructorCalled;
    }

    /**
     * Call this during construction of your controller to register a service into the
     * execution sequence (pre-actions and post-actions).
     *
     * @throws DevelopmentErrorException If the service does not implement ExecutionSequenceServiceInterface or is null.
     * @throws ErrorException the service is provided as a string, and resolution failed.
     * @param string|ExecutionSequenceServiceInterface $service
     */
    final protected function registerExecutionSequenceService($service)
    {
        if ($service === null) {
            throw new DevelopmentErrorException('Null service given.');
        }

        // try to resolve service name
        if (is_string($service)) {
            $tryPrefixes = array(
                '',
                'CoreBusiness:Controller\\ExecutionSequenceService\\',
                'CoreFoundation:Controller\\ExecutionSequenceService\\',
                'CoreBusiness:',
                'CoreFoundation:'
            );
            foreach ($tryPrefixes as $prefix) {
                $fullServiceName = $this->container->resolveClassName($prefix.$service);
                try {
                    $service = $this->container->make($fullServiceName);
                    break;
                } catch (\PrestaShop\PrestaShop\Core\Foundation\IoC\Exception $ioce) {
                    continue;
                }
            }
        }

        if (is_string($service)) {
            throw new ErrorException('The given service ('.$service.') cannot be found (or instantiated) to be registered in the execution sequence.');
        }
        if (!$service instanceof ExecutionSequenceServiceInterface) {
            throw new DevelopmentErrorException('The given service does not implements ExecutionSequenceServiceInterface.');
        }
        $this->router->registerExecutionSequenceService($service);
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
        $response->setContent(json_encode($content, $this->container->make('CoreBusiness:Context')->get('debug') ? JSON_PRETTY_PRINT : 0));
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
        $content = <<<EOT
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="robots" content="index, follow, all" />
        <title></title>
    </head>
    <body>
$content
    </body>
</html>
EOT;
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
    final public function generateUrl($name, array $parameters = array(), $forceLegacyUrl = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        return $this->getRouter()->generateUrl($name, $parameters, $forceLegacyUrl, $referenceType);
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::subcall()
     */
    final public function subcall($name, array $parameters = array(), $layoutMode = BaseController::RESPONSE_PARTIAL_VIEW, $fullResponse = false)
    {
        return $this->getRouter()->subcall($name, $parameters, $layoutMode, $fullResponse);
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::forward()
     */
    final public function forward(Request &$oldRequest, $routeName, array $routeParameters = array())
    {
        return $this->getRouter()->forward($oldRequest, $routeName, $routeParameters);
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::redirectToRoute()
     */
    final public function redirectToRoute(Request &$oldRequest, $routeName, array $routeParameters, $forceLegacyUrl = false, $permanent = false)
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
