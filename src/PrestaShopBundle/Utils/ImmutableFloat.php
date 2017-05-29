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
            throw new \InvalidArgumentException('Invalid argument: "%s" is not a valid float', $value);
        }

        $this->value = $value;
    }

    /**
     * Constructs a new instance from a string value.
     * It supports dot and comma-separated values.
     *
     * @param string $value
     *
     * @return ImmutableFloat
     */
    public static function fromString($value)
    {
        $value = (string) $value;
        if ($value === '') {
            return new self(0.0);
        } else {
            // ensure decimals are dot-separated instead of comma-separated
            $value = str_replace(',', '.', $value);
            return new self((float) $value);
        }
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
