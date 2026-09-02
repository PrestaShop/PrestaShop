<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Util;

use Exception;
use ReflectionMethod;
use ReflectionObject;

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
     * @throws Exception
     */
    public static function transferAttributesFromSubObjectToObject(
        $subObject,
        $object,
        $throwException = false,
        $blackList = [])
    {
        $reflectionObject = new ReflectionObject($subObject);
        $reflectionMethods = $reflectionObject->getMethods(ReflectionMethod::IS_PUBLIC);

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
                throw new Exception("No such setter : $setter");
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
                throw new Exception("No such setter : $setter");
            }
        }

        return $object;
    }

    /**
     * Code inspired by \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer::isGetMethod()
     */
    private static function isGetMethod(ReflectionMethod $method)
    {
        return
            0 === strpos($method->name, 'get')
            && 3 < strlen($method->name)
            && 0 === $method->getNumberOfRequiredParameters()
        ;
    }
}
