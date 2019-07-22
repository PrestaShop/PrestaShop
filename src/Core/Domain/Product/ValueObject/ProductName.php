<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Holds product name.
 */
final class ProductName
{
    public const MAX_SIZE = 128;

    private $name;

    /**
     * @param string $name
     *
     * @throws ProductConstraintException
     */
    public function __construct(string $name)
    {
        $this->setName($name);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @throws ProductConstraintException
     */
    private function setName(string $name): void
    {
        $pattern = '/^[^<>;=#{}]*$/u';

        if (!preg_match($pattern, $name)) {
            throw new ProductConstraintException(
                sprintf(
                    'Given product name "%s" did not matched pattern "%s"',
                    $name,
                    $pattern
                ),
                ProductConstraintException::INVALID_NAME
            );
        }

        if (strlen($name) > self::MAX_SIZE) {
            throw new ProductConstraintException(
                sprintf(
                    'Given product name "%s" is longer then expected size %d',
                    $name,
                    self::MAX_SIZE
                ),
                ProductConstraintException::NAME_TOO_LONG
            );
        }

        $this->name = $name;
    }
}
