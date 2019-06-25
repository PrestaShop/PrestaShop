<?php

namespace PrestaShop\PrestaShop\Adapter\Product;

use Link;
use PrestaShop\PrestaShop\Core\Shop\Url\UrlProviderInterface;

/**
 * Gets product link for front-end.
 */
final class ProductUrlProvider implements UrlProviderInterface
{
    /**
     * @var Link
     */
    private $link;

    /**
     * @param Link $link
     */
    public function __construct(Link $link)
    {
        $this->link = $link;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($productId = null)
    {
        return $this->link->getProductLink($productId);
    }
}
