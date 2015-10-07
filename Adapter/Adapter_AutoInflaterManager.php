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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Business\Context;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;

/**
 * This adapter will 'try' to inflate objects or collections in the Legacy object model.
 *
 * This adapter is called by AutoObjectInflaterTrait methods when an action is executed from
 * a controller that uses this Trait.
 */
class Adapter_AutoInflaterManager
{
    /**
     * @var Context
     */
    private $context;

    /**
     * Constructor.
     *
     * Keeps the context.
     *
     * @param Context $context The Context, given from IoC
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Try to retrieve an Object of the Legacy architecture by its class name and its ID.
     * The instantiation will be equivalent to:
     * <$className>::_construct(<$id>); or new <$className>(<$id>);
     *
     * @param string $className
     * @param integer $id
     * @return boolean|object|NULL False if conditions are not satisfied (calssName not found). Null if the ID is not in the DB.
     */
    public function inflateObject($className, $id)
    {
        $className = ucfirst($className);
        if (!class_exists('\\'.$className)) {
            $className .= 'Core';
        }
        if (!class_exists('\\'.$className)) {
            return false;
        }

        $class = new \ReflectionClass('\\'.$className);
        $constructorParameters = $class->getConstructor()->getParameters();
        $constructorParametersValues = array();

        foreach ($constructorParameters as $p) {
            if ($p->name == 'id' || $p->name == 'id_'.lcfirst($className)) {
                $constructorParametersValues[] = $id;
            }
        }

        $object = $class->newInstanceArgs($constructorParametersValues);
        if (\Validate::isLoadedObject($object)) {
            return $object;
        } else {
            return null;
        }
    }

    /**
     * Try to retrieve a collection of Objects of the Legacy architecture by its class name, a method and methods parameters.
     * The instantiation will be equivalent to:
     * <$className>::<$method>(<$parameters>); or (new <$className>())-><$method>(<$parameters>);
     *
     * @param string $className
     * @param string $method
     * @param array[string] $parameters The route attributes
     * @param array[string] $queryParameters The GETS parameters
     * @param array[string] $requestParameters The POSTs parameters
     * @return boolean|object|NULL False if conditions are not satisfied (calssName not found, mandatory params mmissing). Null if the ID is not in the DB.
     */
    public function inflateCollection($className, $method, $parameters, $queryParameters, $requestParameters)
    {
        $className = ucfirst($className);
        if (!class_exists('\\'.$className)) {
            return false;
        }

        // reflective call
        $class = new \ReflectionClass('\\'.$className);
        $method = $class->getMethod($method);

        // inject route parameters
        $methodParameters = $method->getParameters();
        $givenParameters = array();
        foreach ($methodParameters as $mp) {
            if (array_key_exists($mp->name, $parameters)) {
                $givenParameters[] = $parameters[$mp->name];
                continue;
            } else {
                if ($mp->name == 'id_lang') {
                    if (isset($this->context->language) && isset($this->context->language->id_lang)) {
                        $givenParameters[] = $this->context->language->id_lang;
                    } elseif (isset($this->context->cookie) && isset($this->context->cookie->id_lang)) {
                        $givenParameters[] = $this->context->cookie->id_lang;
                    }
                    continue;
                }
                if (in_array($mp->name, array('query', 'get'))) {
                    $givenParameters[] = $queryParameters;
                    continue;
                }
                if (in_array($mp->name, array('request', 'post'))) {
                    $givenParameters[] = $requestParameters;
                    continue;
                }
            }
            if (!$mp->isOptional()) {
                throw new DevelopmentErrorException('An optional argument is missing to inflate a collection: '.$className.'->'.$method.'(), missing argument: '.$mp->name, null, 5014);
            }
        }
        return $method->invokeArgs(($method->isStatic())?null:$method->getDeclaringClass()->newInstance(), $givenParameters);
    }
}
