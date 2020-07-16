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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Behaviour\Features\Context\Util;

use Exception;
use RuntimeException;

class PrimitiveUtils
{
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_INTEGER = 'integer';
    const TYPE_DOUBLE = 'double';
    const TYPE_STRING = 'string';
    const TYPE_DATETIME = 'datetime';
    const TYPE_ARRAY = 'array';
    const TYPE_NULL = 'NULL';
    const TYPE_OBJECT = 'object';
    const TYPE_RESOURCE = 'resource';
    const TYPE_UNKNOWN = 'unknown type';

    /**
     * @param mixed $element
     * @param string $type
     *
     * @return mixed
     */
    public static function castElementInType($element, $type)
    {
        switch ($type) {
            case self::TYPE_BOOLEAN:
                return self::castStringBooleanIntoBoolean($element);
                break;

            case self::TYPE_INTEGER:
                return intval($element);
                break;

            case self::TYPE_DOUBLE:
                return floatval($element);
                break;

            case self::TYPE_STRING:
                return $element;
                break;

            case self::TYPE_DATETIME:
                $dateTime = new \DateTime($element);

                return $dateTime;
                break;

            case self::TYPE_ARRAY:

                if ('empty' === $element) {
                    return [];
                }
                if (is_array($element)) {
                    return $element;
                }

                $exploded = explode('; ', $element);

                return $exploded;
                break;

            case self::TYPE_NULL:
                if (('null' === $element) || ('Null' === $element) || ('NULL' === $element)) {
                    return;
                } else {
                    return $element;
                }

            // no break
            case self::TYPE_OBJECT:
            case self::TYPE_RESOURCE:
            case self::TYPE_UNKNOWN:
                throw new Exception("Cannot cast element into type $type");
                break;

            default:
                throw new RuntimeException("Unexpected cast type $type, function gettype is not supposed to return it");
        }
    }

    /**
     * @param mixed $element1
     * @param mixed $element2
     *
     * @return bool
     */
    public static function isIdentical($element1, $element2)
    {
        if (gettype($element1) !== gettype($element2)) {
            return false;
        }

        $type = gettype($element1);
        $isADateTime = (($type === self::TYPE_OBJECT) && (get_class($element1) === 'DateTime'));
        if ($isADateTime) {
            $type = self::TYPE_DATETIME;
        }

        switch ($type) {
            case self::TYPE_BOOLEAN:
            case self::TYPE_INTEGER:
                return $element1 === $element2;
                break;
            case self::TYPE_DOUBLE:

                // see http://php.net/manual/en/language.types.float.php#language.types.float.comparison
                $epsilon = 0.00001;

                return abs($element1 - $element2) < $epsilon;
                break;

            case self::TYPE_DATETIME:
                return $element1->format('YmdHis') === $element2->format('YmdHis');
                break;

            case self::TYPE_STRING:
                $cleanedString1 = trim($element1);
                $cleanedString2 = trim($element2);

                return $cleanedString1 === $cleanedString2;
                break;

            case self::TYPE_ARRAY:
                $castedArray1 = self::castArrayElementsIntoString($element1);
                $castedArray2 = self::castArrayElementsIntoString($element2);

                sort($castedArray1);
                sort($castedArray2);

                return $castedArray1 === $castedArray2;
                break;

            case self::TYPE_OBJECT:
            case self::TYPE_RESOURCE:
            case self::TYPE_NULL:
                if ((null === $element1) && (null === $element2)) {
                    return true;
                } else {
                    return false;
                }
                break;

            case self::TYPE_UNKNOWN:
                throw new Exception("Cannot compare elements of type $type");
                break;

            default:
                throw new RuntimeException("Unexpected type $type, function gettype is not supposed to return it");
        }
    }

    /**
     * @param string $element
     *
     * @return bool
     */
    public static function castStringBooleanIntoBoolean($element)
    {
        if ($element === 'false') {
            return false;
        }

        return boolval($element);
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public static function castArrayElementsIntoString(array $array)
    {
        $newArray = [];
        foreach ($array as $key => $element) {
            if (is_array($element)) {
                throw new Exception('Cannot cast two-level array into string');
            }

            $newArray[$key] = (string) $element;
        }

        return $newArray;
    }

    /**
     * @param string $arrayAsString
     *
     * @return array
     */
    public static function castStringArrayIntoArray($arrayAsString)
    {
        $arrayAsString = str_replace(['[', ']', ' '], ['', '', ''], $arrayAsString);

        return explode(',', $arrayAsString);
    }

    /**
     * @param string $element
     *
     * @return int
     *
     * @throws RuntimeException
     */
    public static function castStringIntegerIntoInteger($element)
    {
        if (intval($element) !== 0) {
            return intval($element);
        }

        switch ($element) {
            case 'first':
            case 'one':
                return 1;

            case 'second':
            case 'two':
                return 2;

            case 'third':
            case 'three':
                return 3;

            case 'four':
                return 4;

            case 'five':
                return 5;

            default:
                throw new RuntimeException("Unknown string integer: $element");
        }
    }
}
