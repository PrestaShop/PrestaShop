<?php

namespace PrestaShopBundle\Utils;

/**
 * Converts strings into floats
 */
class FloatParser
{
    /**
     * Constructs a float value from an arbitrarily-formatted string.
     *
     * This method supports any thousand and decimal separator.
     * If the string is ambiguous (e.g. 123,456) the interpreter will interpret the last group of numbers
     * as the decimal part.
     *
     * In order to prevent unexpected behavior, make sure that your value has a decimal part.
     *
     * Examples:
     * - '123,456' --> 123.456
     * - '123,456,00' --> 123456.00
     * - '12,345,678 --> 12345.678
     *
     * @param string $value
     *
     * @throws \InvalidArgumentException If the provided value is not a string
     * or if it cannot be interpreted as a number.
     *
     * @return float
     */
    public function fromString($value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException(sprintf('Invalid argument: string expected, got %s', gettype($value)));
        }

        $value = trim($value);
        if ('' === $value) {
            return 0.0;
        }

        // remove all non-digit characters
        $split = preg_split('/[^\dE-]+/', $value);

        if (1 === count($split)) {
            // there's no decimal part
            return (float) $value;
        }

        foreach ($split as $part) {
            if ('' === $part) {
                throw new \InvalidArgumentException(
                    sprintf('Invalid argument: "%s" cannot be interpreted as a number', $value)
                );
            }
        }

        // use the last part as decimal
        $decimal = array_pop($split);

        // reconstruct the number using dot as decimal separator
        $value = implode('', $split) . '.' . $decimal;

        return (float) $value;
    }
}
