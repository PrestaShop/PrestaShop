<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductPreviewUrl;

/**
 * Defines contract for product preview.
 */
interface GetProductPreviewUrlHandlerInterface
{
    /**
     * @param GetProductPreviewUrl $query
     *
     * @return string
     */
    public function handle(GetProductPreviewUrl $query);
}
