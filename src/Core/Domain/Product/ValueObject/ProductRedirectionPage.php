<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Holds data which points to product redirection page.
 */
final class ProductRedirectionPage implements TypedRedirectionPageInterface
{
    /**
     * @var ResponseCode
     */
    private $responseCode;

    /**
     * @var CategoryId
     */
    private $categoryId;

    /**
     * @param int $responseCode
     * @param int $productId
     *
     * @throws ProductConstraintException
     */
    public function __construct(int $responseCode, int $productId)
    {
        $this->responseCode = new ResponseCode($responseCode);
        $this->categoryId = new ProductId($productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseCode(): ResponseCode
    {
        return $this->responseCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->categoryId->getValue();
    }
}
