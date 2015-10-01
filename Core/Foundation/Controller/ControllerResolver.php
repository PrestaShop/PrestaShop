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

use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;

/**
 * This override of the Symfony Content resolver will add some elements to inject in the controller during routing.
 * We add $response object by injection before resolving arguments, and all its content dat through $response->getContentData().
 *
 * We also modify response and request objects injection to allow injection by reference directly.
 */
class ControllerResolver extends \Symfony\Component\HttpKernel\Controller\ControllerResolver
{
    /**
     * @var Response
     */
    private $response;

    /**
     * Keeps the Response object to allow injection of it into action.
     *
     * @param unknown $response
     */
    public function setResponse(Response &$response)
    {
        $this->response = $response;
        $this->addInjection($this->response);
    }

    /**
     * @var Container
     */
    private $container;

    /**
     * Keeps the Container object to allow injection of it (or another service that it provides) into action.
     * @param Container $container
     */
    public function setContainer(Container &$container)
    {
        $this->container = $container;
        $this->addInjection($this->container);
    }

    /**
     * @var AbstractRouter
     */
    private $router;

    /**
     * Keeps the Router instance to allow Controller's instantiation.
     *
     * @param AbstractRouter $router
     */
    public function setRouter(AbstractRouter &$router)
    {
        $this->router = $router;
    }

    private $additionalInjections = array();

    /**
     * Add an object to inject into the action signature.
     *
     * @param unknown $objectInstance
     */
    public function addInjection(&$objectInstance)
    {
        $this->additionalInjections[] = $objectInstance;
    }

    final private function checkAdditionalInjections(\ReflectionClass $paramClass)
    {
        $names = explode('\\', $paramClass->name);
        $names = array('\\'.$paramClass->name, $paramClass->name, $names[count($names)-1]);
        // search for Service instance in container
        foreach ($names as $name) {
            if ($this->container->knows($name)) {
                return $this->container->make($name);
            }
        }

        // then search for additional injections
        foreach ($this->additionalInjections as $injection) {
            if ($paramClass->isInstance($injection)) {
                return $injection;
            }
        }

        return false; // Not found
    }

    /* (non-PHPdoc)
     * @see \Symfony\Component\HttpKernel\Controller\ControllerResolver::doGetArguments()
     */
    protected function doGetArguments(Request $request, $controller, array $parameters)
    {
        $attributes = $request->attributes->all();
        $contentData = $this->response->getContentData();
        $arguments = array();
        $injection = array();
        foreach ($parameters as $param) {
            if (array_key_exists($param->name, $attributes)) {
                $arguments[] = $attributes[$param->name];
            } elseif (array_key_exists($param->name, $contentData)) {
                $arguments[] = $contentData[$param->name];
            } elseif (array_key_exists(lcfirst($param->name), $contentData)) {
                $arguments[] = $contentData[lcfirst($param->name)];
            } elseif (array_key_exists(ucfirst($param->name), $contentData)) {
                $arguments[] = $contentData[ucfirst($param->name)];
            } elseif ($param->getClass() && $param->getClass()->isInstance($request)) {
                $arguments[] = &$request; // by ref
            } elseif ($param->getClass() && ($injection[] = $this->checkAdditionalInjections($param->getClass()))) {
                $arguments[] = &$injection[count($injection)-1]; // by ref
            } elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            } else {
                if (is_array($controller)) {
                    $repr = sprintf('%s::%s()', get_class($controller[0]), $controller[1]);
                } elseif (is_object($controller)) {
                    $repr = get_class($controller);
                } else {
                    $repr = $controller;
                }

                throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $repr, $param->name));
            }
        }
        return $arguments;
    }
}
