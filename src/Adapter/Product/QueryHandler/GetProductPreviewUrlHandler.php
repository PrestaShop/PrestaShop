<?php

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\AdminProductWrapper;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductPreviewUrl;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetProductPreviewUrlHandlerInterface;
use Product;

/**
 * Gets product preview url.
 *
 * @internal
 */
final class GetProductPreviewUrlHandler implements GetProductPreviewUrlHandlerInterface
{
    /**
     * @var AdminProductWrapper
     */
    private $adminProductWrapper;

    /**
     * @param AdminProductWrapper $adminProductWrapper
     */
    public function __construct(AdminProductWrapper $adminProductWrapper)
    {
        $this->adminProductWrapper = $adminProductWrapper;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetProductPreviewUrl $query)
    {
        $entity = new Product($query->getProductId()->getValue());

        return $this->adminProductWrapper->getPreviewUrl($entity);
    }
}
