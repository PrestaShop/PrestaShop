<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Defines where product should be visible.
 */
class Visibility
{
    /**
     * @var string defines that product is visible everywhere - e.g category, product, search pages
     */
    public const EVERYWHERE = 'both';

    /**
     * @var string - defines that the product is visible only in catalog page.
     */
    public const CATALOG = 'catalog';

    /**
     * @var string - defines that the product is visible only in search results.
     */
    public const SEARCH = 'search';

    /**
     * @var string - defines that product should not appear anywhere.
     */
    public const NOWHERE = 'none';

    public const AVAILABLE_VISIBILITY = [
        self::EVERYWHERE,
        self::CATALOG,
        self::SEARCH,
        self::NOWHERE,
    ];

    /**
     * @var string
     */
    private $visibility;

    /**
     * @var bool
     */
    private $isAvailableForOrder;

    /**
     * @var bool
     */
    private $isWebOnly;

    /**
     * @param string $visibility
     *
     * @param bool $isAvailableForOrder
     * @param bool $isWebOnly - only sold in retail store
     * @throws ProductConstraintException
     */
    public  function __construct(string $visibility, bool $isAvailableForOrder, bool $isWebOnly)
    {
        $this->assertIsValidVisibility($visibility);

        $this->visibility = $visibility;
        $this->isAvailableForOrder = $isAvailableForOrder;
        $this->isWebOnly = $isWebOnly;
    }

    /**
     * @return string
     */
    public function getVisibility(): string
    {
        return $this->visibility;
    }

    /**
     * @return bool
     */
    public function isAvailableForOrder(): bool
    {
        return $this->isAvailableForOrder;
    }

    /**
     * @return bool
     */
    public function isWebOnly(): bool
    {
        return $this->isWebOnly;
    }

    /**
     * @param string $visibility
     *
     * @throws ProductConstraintException
     */
    private function assertIsValidVisibility(string $visibility): void
    {
        if (!in_array($visibility, self::AVAILABLE_VISIBILITY, true)) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid product visibility "%s" detected. Available values are "%s"',
                    $visibility,
                    implode(',', self::AVAILABLE_VISIBILITY)
                ),
                ProductConstraintException::INVALID_VISIBILITY_TYPE
            );
        }
    }
}
