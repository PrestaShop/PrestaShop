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

class Adapter_AutoInflaterManager
{
    public function inflateObject($className, $id)
    {
        $className = ucfirst($className);
        if (!class_exists('\\'.$className)) {
            return false;
        }
        $class = new \ReflectionClass('\\'.$className);
        $constructorParameters = $class->getConstructor()->getParameters();
        $constructorParametersValues = array();

        foreach ($constructorParameters as $p) {
            if ($p->name == 'id') {
                $constructorParametersValues[] = $value;
            }
        }

        $object = $class->newInstanceArgs($constructorParametersValues);
        if (\Validate::isLoadedObject($object)) {
            return $object;
        } else {
            return null;
        }
    }
    
    public function inflateCollection($className, $method, $parameters)
    {
        $context = Context::getInstance();

        $className = ucfirst($className);
        if (!class_exists('\\'.$className)) {
            return false;
        }

        $class = new \ReflectionClass('\\'.$className);
        $method = $class->getMethod($method);

        $methodParameters = $method->getParameters();
        $givenParameters = array();
        foreach ($methodParameters as $mp) {
            if (array_key_exists($mp->name, $parameters)) {
                $givenParameters[] = $parameters[$mp->name];
                continue;
            } else {
                if ($mp->name == 'id_lang') {
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
                return false;
            }
        }

        return $method->invokeArgs(($method->isStatic())?null:$method->getDeclaringClass()->newInstance(), $givenParameters);
    }
}
