<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

/**
 * Defines where product should be visible.
 */
final class Visibility
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

    public  function __construct(string $visibility)
    {
    }
}
