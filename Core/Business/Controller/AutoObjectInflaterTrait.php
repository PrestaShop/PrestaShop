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

use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Business\Context;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;

/**
 * This Trait will add convenience hooks before controller action execution,
 * and will try to inflate data from DB if the route contains parameters (if their name follow a specific norm)
 * and will insert them in $response->getContentData() set.
 *
 * The route.yml should indicate an object id and its class to be detected by this Trait like this:
 * 'id_<RepositoryName>'
 * example:
 * my_route:
 *   path:     /my_path/{id_order}/{id_product}/{id_customer}/view
 *   defaults: { _controller: 'Admin\TestController::bAction', id_order: 1 }
 *
 */
trait AutoObjectInflaterTrait
{
    /**
     * This trait helper will try to identify some elements in the uri parameters (if well set in the routes*.yml files,
     * and in the right syntax), to query them in the database, and to complete $response->getContentData with the
     * found object.
     *
     * Route setting example:
     * path:     /path/to/route/{id_order}/rest/of/the/path
     * This will try to Inflate a Order object by instantiation with the value of the route parameter as the unique constructor parameter.
     *
     * @param Request $request
     * @param Response $response
     * @return boolean True if success; False to forbid action execution
     */
    public function beforeActionInflateRequestedObjects(Request &$request, Response &$response)
    {
        foreach ($request->attributes->all() as $key => $value) {

            // Find parameters that begins with id_ to try to inflate corresponding object
            if (strpos($key, 'id_') === 0) {
                $className = ucfirst(substr($key, 3));
                if (!class_exists($className)) { // FIXME: when namespaces will be added on the class, should adapt here.
                    continue;
                }
                $class = new \ReflectionClass($className);
                $constructorParameters = $class->getConstructor()->getParameters();
                $constructorParametersValues = array();
                
                foreach ($constructorParameters as $p) {
                    /* @var $p \ReflectionParameter */
                    if ($p->name == 'id') {
                        $constructorParametersValues[] = $value;
                    }
                }

                $object = $class->newInstanceArgs($constructorParametersValues);
                if (\Validate::isLoadedObject($object)) { // FIXME: this test should be in the new Archi, or in an Adapter.
                    $response->addContentData($className, $object);
                } else {
                    // To indicate we tried, but not found.
                    EventDispatcher::getInstance('log')->dispatch('AutoObjectInflaterTrait', new BaseEvent('Cannot load required object.'));
                    $response->addContentData($className, null);
                }
            }
        }
        return true;
    }

    /**
     * This trait helper will try to identify some elements in the uri parameters (if well set in the routes*.yml files,
     * and in the right syntax), to query them in the database, and to complete $response->getContentData with the
     * found collections.
     *
     * Route setting example:
     * path:     /path/to/route/{ls_mykey_limit}/{ls_mykey_start}/{ls_mykey_order_by}/{ls_mykey_order_way}
     * defaults:
     *     ls_mykey_class: Product
     *     ls_mykey_method: getProducts
     *     ls_mykey_limit: 10
     *     ls_mykey_start: 0
     *     ls_mykey_order_by: 'id'
     *     ls_mykey_order_way: 'ASC'
     *
     * TODO: filter system!
     *
     * @param Request $request
     * @param Response $response
     * @return boolean True if success; False to forbid action execution
     */
    public function beforeActionInflateRequestedCollection(Request &$request, Response &$response)
    {
        $context = Context::getInstance();

        $collectionParameters = array();
        foreach ($request->attributes->all() as $key => $value) {
            $subKeys = explode('_', $key, 3);
            if (count($subKeys) < 3) {
                continue;
            }
            if ($subKeys[0] != 'ls') {
                continue;
            }

            $collectionParameters[$subKeys[1]][$subKeys[2]] = $value;

            if ($subKeys[2] == 'class') {
                if (!class_exists($subKeys[2])) { // FIXME: when namespaces will be added on the class, should adapt here.
                    continue;
                }
            }
        }

        foreach ($collectionParameters as $key => $parameters) {
            try {
                $method = $parameters['method'];
                $class = $parameters['class'];

                $class = new \ReflectionClass($class);
                $method = $class->getMethod($method);

                $methodParameters = $method->getParameters();
                $givenParameters = array();
                foreach ($methodParameters as $mp) {
                    if (array_key_exists($mp->name, $parameters)) {
                        $givenParameters[] = $parameters[$mp->name];
                        continue;
                    } else {
                        if ($mp->name == 'id_lang') {
                            // FIXME : legacy code !
                            if (isset($context->language) && isset($context->language->id_lang)) {
                                $givenParameters[] = $context->language->id_lang;
                            } elseif (isset($context->cookie) && isset($context->cookie->id_lang)) {
                                $givenParameters[] = $context->cookie->id_lang;
                            }
                            continue;
                        }
                    }
                    if (!$mp->isOptional()) {
                        // mandatory argument missing, cannot fetch Collection at all.
                        EventDispatcher::getInstance('log')->dispatch('AutoObjectInflaterTrait', new BaseEvent('Cannot load required object list: mandatory argument missing.'));
                        continue 2;
                    }
                }

                $collection = $method->invokeArgs(($method->isStatic())?null:$method->getDeclaringClass()->newInstance(), $givenParameters);
                $response->addContentData($key, $collection);
            } catch (\Exception $e) {
                // To indicate we tried, but failed.
                EventDispatcher::getInstance('log')->dispatch('AutoObjectInflaterTrait', new BaseEvent('Cannot load required object list.'));
                $response->addContentData($key, null);
            }
        }

        return true;
    }
}
