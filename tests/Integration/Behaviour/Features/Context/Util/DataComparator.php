<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Util;

use Exception;

class DataComparator
{
    /**
     * @param array $expectedData
     * @param array $realData
     *
     * @throws Exception
     */
    public static function assertDataSetsAreIdentical(array $expectedData, array $realData)
    {
        foreach ($expectedData as $key => $expectedElement) {
            if (false === array_key_exists($key, $realData)) {
                $availableKeys = array_keys($realData);
                throw new Exception("Expected data $key but no such data in real data ; available data is " . implode(',', $availableKeys));
            }

            $realElement = $realData[$key];
            $realElementType = gettype($realElement);

            if (($realElementType === 'array') && array_key_exists('value', $realElement)) {
                $realElement = $realElement['value'];
                $realElementType = gettype($realElement);
            }

            $isADateTime = (($realElementType === 'object') && (get_class($realElement) === 'DateTime'));
            if ($isADateTime) {
                $realElementType = 'datetime';
            }

            $castedExpectedElement = PrimitiveUtils::castElementInType($expectedElement, $realElementType);

            if (false === PrimitiveUtils::isIdentical($castedExpectedElement, $realElement)) {
                if ($realElementType === 'boolean') {
                    $realAsString = ($realElement) ? 'true' : 'false';
                    $expectedAsString = ($castedExpectedElement) ? 'true' : 'false';

                    throw new Exception("Real $key is " . $realAsString . ' / expected ' . $expectedAsString);
                } elseif ($realElementType === 'array') {
                    sort($realElement);
                    sort($castedExpectedElement);

                    $realAsString = implode('; ', $realElement);
                    $expectedAsString = implode('; ', $castedExpectedElement);

                    if ('' === $realAsString) {
                        $realAsString = 'empty';
                    }

                    if ('' === $expectedAsString) {
                        $expectedAsString = 'empty';
                    }

                    throw new Exception("Real $key is $realAsString / expected $expectedAsString");
                } elseif ($realElementType === 'datetime') {
                    $realAsString = $realElement->format('Y/m/d H:i:s');
                    $expectedAsString = $castedExpectedElement->format('Y/m/d H:i:s');

                    throw new Exception("Real $key is $realAsString / expected $expectedAsString");
                } else {
                    throw new Exception("Real $key is " . $realElement . ' / expected ' . $castedExpectedElement);
                }
            }
        }
    }
}
