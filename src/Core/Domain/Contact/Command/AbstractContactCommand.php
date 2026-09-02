<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Contact\Command;

/**
 * Class AbstractContactCommand is responsible for providing common behavior for AddContactCommand and EditContactCommand.
 */
abstract class AbstractContactCommand
{
    /**
     * @param array $values
     *
     * @return bool
     */
    protected function assertIsNotEmptyAndContainsAllNonEmptyStringValues(array $values)
    {
        $filterNonEmptyStrings = function ($value) {
            return is_string($value) && $value;
        };

        return !empty($values) && count($values) === count(array_filter($values, $filterNonEmptyStrings));
    }

    /**
     * @param string $value
     *
     * @return false|int
     */
    protected function assertIsGenericName($value)
    {
        return preg_match('/^[^<>{}]*$/u', $value);
    }

    /**
     * @param array $values
     *
     * @return bool
     */
    protected function assertArrayContainsAllIntegerValues(array $values)
    {
        $filterAllIntegers = function ($value) {
            return is_int($value);
        };

        return !empty($values) && count($values) === count(array_filter($values, $filterAllIntegers));
    }
}
