<?php

namespace PrestaShop\PrestaShop\Adapter\Country\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;

class Country
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @param int $id
     * @param string $name
     */
    public function __construct(int $id, string $name)
    {
        $this->assertIsGreaterThanZero($id);
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Validates that the value is greater than zero int
     *
     * @param $value
     *
     * @throws CountryConstraintException
     */
    private function assertIsGreaterThanZero(int $value)
    {
        if (0 >= $value) {
            throw new CountryConstraintException(sprintf('Invalid country id "%s".', var_export($value, true)), CountryConstraintException::INVALID_ID);
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
