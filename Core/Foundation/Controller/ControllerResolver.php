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
use PrestaShop\PrestaShop\Core\Business\Context;
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;

/**
 * This override of the Symfony Content resolver will add some elements to inject in the controller during routing.
 * We add $response object by injection before resolving arguments, and all its content dat through $response->getContentData().
 *
 * We also modify response and request objects injection to allow injection by reference directly.
 */
class ControllerResolver extends \Symfony\Component\HttpKernel\Controller\ControllerResolver
{
    private $response;

    public function setResponse(Response &$response)
    {
        $this->response = $response;
    }

    private $router;

    public function setRouter(AbstractRouter &$router)
    {
        $this->router = $router;
    }

    protected function doGetArguments(Request $request, $controller, array $parameters)
    {
        $attributes = $request->attributes->all();
        $contentData = $this->response->getContentData();
        $context = Context::getInstance();
        $arguments = array();
        foreach ($parameters as $param) {
            if (array_key_exists($param->name, $attributes)) {
                $arguments[] = $attributes[$param->name];
            } elseif (array_key_exists($param->name, $contentData)) {
                $arguments[] = $contentData[$param->name];
            } elseif (array_key_exists(lcfirst($param->name), $contentData)) {
                $arguments[] = $contentData[lcfirst($param->name)];
            } elseif (array_key_exists(ucfirst($param->name), $contentData)) {
                $arguments[] = $contentData[ucfirst($param->name)];
            } elseif ($param->getClass() && $param->getClass()->isInstance($this->response)) {
                $arguments[] = &$this->response; // by ref
            } elseif ($param->getClass() && $param->getClass()->isInstance($request)) {
                $arguments[] = &$request; // by ref
            } elseif ($param->getClass() && $param->getClass()->isInstance($context)) {
                $arguments[] = &$context; // by ref
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

    /**
     * Returns an instantiated controller
     *
     * @param string $class A class name
     *
     * @return object
     */
    protected function instantiateController($class)
    {
        return new $class($this->router);
    }
}
