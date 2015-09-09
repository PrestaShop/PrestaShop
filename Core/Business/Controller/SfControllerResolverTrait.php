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

use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use PrestaShop\PrestaShop\Core\Business\Context;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;
use PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerResolver;

/**
 * This Trait will add dependency injection in the controller action methods.
 * Can work in junction with AutoObjectInflaterTrait for example.
 */
trait SfControllerResolverTrait
{
    /**
     * This trait function will try to resolve controller paramters to inject them.
     * Can work in junction with AutoObjectInflaterTrait for example.
     *
     * @param Request $request
     * @param Response $response
     * @return \Closure The controller resolver closure to be executed by the Router.
     */
    public function controllerResolverSymfony(Request &$request, Response &$response, AbstractRouter &$router = null)
    {
        return function (BaseController &$controllerInstance, \ReflectionMethod &$controllerMethod) use (&$request, &$response, &$router) {
            $resolver = new ControllerResolver(); // Prestashop resolver, not sf!
            $resolver->setRouter($router); // inject router for Controller instantiation.
            $resolver->setResponse($response); // inject response for its contentData array (scanned for injections)
            $context = Context::getInstance();
            $resolver->addInjection($context);
            $callable = $resolver->getController($request);
            $arguments = $resolver->getArguments($request, $callable);
            return $controllerMethod->invokeArgs($controllerInstance, $arguments);
        };
    }
}
