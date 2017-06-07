<?php

namespace PrestaShopBundle\Utils;

/**
 * Immutable value-object that contains a float value.
 */
class ImmutableFloat
{
    /**
     * @var float
     */
    private $value;

    /**
     * @param float $value
     *
     * @throws \InvalidArgumentException if the passed value is not a valid float
     */
    public function __construct($value)
    {
        if (!is_float($value)) {
            throw new \InvalidArgumentException(sprintf('Invalid argument: "%s" is not a valid float', $value));
        }

        $this->value = $value;
    }

    /**
     * Constructs a new instance from a string value.
     *
     * Note: This method supports arbitrary thousands and decimal separators.
     *
     * If the string is ambiguous (e.g. 123,456) the interpreter will take the last group of numbers
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
     * @return ImmutableFloat
     */
    public static function fromString($value)
    {
        $value = trim((string) $value);
        if ('' === $value) {
            return new static(0.0);
        }

        // remove all non-digit characters
        $split = preg_split('/[^\dE-]/', $value);

        if (1 === count($split)) {
            // there's no decimal part
            return new static((float) $value);
        }

        // use the last part as decimal
        $decimal = array_pop($split);

        // reconstruct the number using dot as decimal separator
        $value = implode('', $split) . '.' . $decimal;

        return new static((float) $value);
    }

    /**
     * Returns the value as a float primitive.
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }
}
