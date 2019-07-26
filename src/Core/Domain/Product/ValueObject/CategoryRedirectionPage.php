<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Category redirection page
 */
final class CategoryRedirectionPage implements TypedRedirectionPageInterface
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
     * @param int $categoryId
     *
     * @throws ProductConstraintException
     * @throws CategoryException
     */
    public function __construct(int $responseCode, int $categoryId)
    {
        $this->responseCode = new ResponseCode($responseCode);
        $this->categoryId = new CategoryId($categoryId);
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
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->categoryId->getValue();
    }
}
