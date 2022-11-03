<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Behaviour\Features\Context\Util;

class DataTransfer
{
    /**
     * Code inspired by \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer::normalize() and denormalize()
     *
     * @param object $subObject
     * @param object $object
     * @param bool $throwException false: skip bad data / true: throws Exceptions
     * @param array $blackList properties to ignore
     *
     * @return object $object
     *
     * @throws \Exception
     */
    public static function transferAttributesFromSubObjectToObject(
        $subObject,
        $object,
        $throwException = false,
        $blackList = [])
    {
        $reflectionObject = new \ReflectionObject($subObject);
        $reflectionMethods = $reflectionObject->getMethods(\ReflectionMethod::IS_PUBLIC);

        $attributes = [];
        foreach ($reflectionMethods as $method) {
            if ($method->isStatic()) {
                continue;
            }

            if (self::isGetMethod($method)) {
                $attributeName = lcfirst(substr($method->name, 3));

                $attributeValue = $method->invoke($subObject);
                $attributes[$attributeName] = $attributeValue;
            }
        }

        foreach ($attributes as $attribute => $value) {
            $setter = 'set' . $attribute;

            if (in_array($attribute, $blackList)) {
                continue;
            }

            if (method_exists($object, $setter)) {
                $object->$setter($value);
            } elseif ($throwException) {
                throw new \Exception("No such setter : $setter");
            }
        }

        return $object;
    }

    /**
     * @param array $array
     * @param object $object
     * @param bool $throwException false: skip bad data / true: throws Exceptions
     *
     * @return object $object
     */
    public static function transferAttributesFromArrayToObject(array $array, $object, $throwException = false)
    {
        foreach ($array as $attribute => $value) {
            $setter = 'set' . $attribute;

            if (method_exists($object, $setter)) {
                $object->$setter($value);
            } elseif ($throwException) {
                throw new \Exception("No such setter : $setter");
            }
        }

        return $object;
    }

    /**
     * Code inspired by \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer::isGetMethod()
     */
    private static function isGetMethod(\ReflectionMethod $method)
    {
        return
            0 === strpos($method->name, 'get') &&
            3 < strlen($method->name) &&
            0 === $method->getNumberOfRequiredParameters()
        ;
    }
}
