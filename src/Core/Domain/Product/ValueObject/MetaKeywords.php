<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Product keywords metadata.
 */
final class MetaKeywords
{
    public const MAX_SIZE = 255;

    /**
     * @var string
     */
    private $metaKeywords;

    public function __construct(string $metaKeywords)
    {
        $this->setMetaKeywords($metaKeywords);
    }

    public function getValue(): string
    {
        return $this->metaKeywords;
    }

    /**
     * @param string $name
     *
     * @throws ProductConstraintException
     */
    private function setMetaKeywords(string $name): void
    {
        $pattern = '/^[^<>={}]*$/u';

        if (!preg_match($pattern, $name)) {
            throw new ProductConstraintException(
                sprintf(
                    'Given product meta keywords "%s" did not matched pattern "%s"',
                    $name,
                    $pattern
                ),
                ProductConstraintException::INVALID_META_KEYWORDS
            );
        }

        if (strlen($name) > self::MAX_SIZE) {
            throw new ProductConstraintException(
                sprintf(
                    'Given product meta keywords "%s" is longer then expected size %d',
                    $name,
                    self::MAX_SIZE
                ),
                ProductConstraintException::META_KEYWORDS_NAME_TOO_LONG
            );
        }

        $this->metaKeywords = $name;
    }
}
