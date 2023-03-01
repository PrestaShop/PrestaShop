<?php

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject;

class GroupName
{
    /** @var string */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
