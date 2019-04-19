<?php
/**
 * Created by PhpStorm.
 * User: mFerment
 * Date: 19/04/2019
 * Time: 12:53
 */

namespace Tests\Integration\Behaviour\Features\Context\Util;

use Exception;
use RuntimeException;

class PrimitiveUtils
{
    /**
     * @param mixed $element
     * @param string $type
     *
     * @return mixed
     */
    public static function castElementInType($element, $type)
    {
        switch ($type) {
            case 'boolean':
                return self::castStringBooleanIntoBoolean($element);
                break;

            case 'integer':
                return intval($element);
                break;

            case 'double':
                return floatval($element);
                break;

            case 'string':
                return $element;
                break;

            case 'datetime':
                $dateTime = new \DateTime($element);

                return $dateTime;
                break;

            case 'array':

                if ('empty' === $element) {
                    return [];
                }
                if (is_array($element)) {
                    return $element;
                }

                $exploded = explode('; ', $element);

                return $exploded;
                break;

            case 'NULL':
                if (('null' === $element) || ('Null' === $element) || ('NULL' === $element)) {
                    return;
                } else {
                    return $element;
                }

                // no break
            case 'object':
            case 'resource':
            case 'unknown type':
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
        $isADateTime = (($type === 'object') && (get_class($element1) === 'DateTime'));
        if ($isADateTime) {
            $type = 'datetime';
        }

        switch ($type) {
            case 'boolean':
            case 'integer':
                return $element1 === $element2;
                break;
            case 'double':

                    // see http://php.net/manual/en/language.types.float.php#language.types.float.comparison
                    $epsilon = 0.00001;

                    return abs($element1 - $element2) < $epsilon;
                    break;

            case 'datetime':
                return $element1->format('YmdHis') === $element2->format('YmdHis');
                break;

            case 'string':
                $cleanedString1 = trim($element1);
                $cleanedString2 = trim($element2);

                return $cleanedString1 === $cleanedString2;
                break;

            case 'array':
                $castedArray1 = self::castArrayElementsIntoString($element1);
                $castedArray2 = self::castArrayElementsIntoString($element2);

                sort($castedArray1);
                sort($castedArray2);

                return $castedArray1 === $castedArray2;
                break;

            case 'object':
            case 'resource':
            case 'NULL':
                if ((null === $element1) && (null === $element2)) {
                    return true;
                } else {
                    return false;
                }
                break;

            case 'unknown type':
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
