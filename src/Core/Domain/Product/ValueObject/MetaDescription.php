<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Product meta description metadata.
 */
final class MetaDescription
{
    public const MAX_SIZE = 512;

    /**
     * @var string
     */
    private $metaDescription;

    public function __construct(string $metaDescription)
    {
        $this->setMetaDescription($metaDescription);
    }

    public function getValue(): string
    {
        return $this->metaDescription;
    }

    /**
     * @param string $name
     *
     * @throws ProductConstraintException
     */
    private function setMetaDescription(string $name): void
    {
        $pattern = '/^[^<>={}]*$/u';

        if (!preg_match($pattern, $name)) {
            throw new ProductConstraintException(
                sprintf(
                    'Given product meta keywords "%s" did not matched pattern "%s"',
                    $name,
                    $pattern
                ),
                ProductConstraintException::INVALID_META_DESCRIPTION
            );
        }

        if (strlen($name) > self::MAX_SIZE) {
            throw new ProductConstraintException(
                sprintf(
                    'Given product meta keywords "%s" is longer then expected size %d',
                    $name,
                    self::MAX_SIZE
                ),
                ProductConstraintException::META_DESCRIPTION_NAME_TOO_LONG
            );
        }

        $this->metaDescription = $name;
    }
}
