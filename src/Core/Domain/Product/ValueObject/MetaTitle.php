<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Product title metadata.
 */
final class MetaTitle
{
    public const MAX_SIZE = 255;

    /**
     * @var string
     */
    private $metaTitle;

    public function __construct(string $metaTitle)
    {
        $this->setMetaTitle($metaTitle);
    }

    public function getValue(): string
    {
        return $this->metaTitle;
    }

    /**
     * @param string $name
     *
     * @throws ProductConstraintException
     */
    private function setMetaTitle(string $name): void
    {
        $pattern = '/^[^<>={}]*$/u';

        if (!preg_match($pattern, $name)) {
            throw new ProductConstraintException(
                sprintf(
                    'Given product meta title "%s" did not matched pattern "%s"',
                    $name,
                    $pattern
                ),
                ProductConstraintException::INVALID_META_TITLE
            );
        }

        if (strlen($name) > self::MAX_SIZE) {
            throw new ProductConstraintException(
                sprintf(
                    'Given product meta title "%s" is longer then expected size %d',
                    $name,
                    self::MAX_SIZE
                ),
                ProductConstraintException::META_TITLE_NAME_TOO_LONG
            );
        }

        $this->metaTitle = $name;
    }
}
