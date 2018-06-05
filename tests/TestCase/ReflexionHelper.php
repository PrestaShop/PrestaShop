<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\TestCase;

use ReflectionClass;
use PHPUnit\Framework\TestCase;

/**
 * Class ReflexionHelper
 * @package Tests\TestCase
 *
 * Provides utilities to access private or protected properties inside classes.
 *
 * Please be careful with these feature, testing private fields or methods is often a bad smell :
 *      - you may be testing your implementation details rather than the behaviour of your code.
 *      - if this is because the class under test is too long and complicated, you should defintely consider breaking this class into several smaller ones.
 *
 * In the end, this kind of features is here just for convenience to be able to test quickly dirty legacy code.
 */
class ReflexionHelper extends TestCase
{
    public static function invoke($object, $method)
    {
        $params = array_slice(func_get_args(), 2);

        $reflexion = new ReflectionClass(self::getClass($object));
        $reflexion_method = $reflexion->getMethod($method);
        $reflexion_method->setAccessible(true);

        return $reflexion_method->invokeArgs($object, $params);
    }

    public static function getProperty($object, $property)
    {
        $reflexion = new ReflectionClass(self::getClass($object));
        $reflexion_property = $reflexion->getProperty($property);
        $reflexion_property->setAccessible(true);

        return $reflexion_property->getValue($object);
    }

    public static function setProperty($object, $property, $value)
    {
        $reflexion = new ReflectionClass(self::getClass($object));
        $reflexion_property = $reflexion->getProperty($property);
        $reflexion_property->setAccessible(true);

        $reflexion_property->setValue($object, $value);
    }

    public static function getClass($object)
    {
        $namespace = explode('\\', get_class($object));
        return preg_replace('/(.*)(?:Core)?Test$/', '$1', end($namespace));
    }
}
