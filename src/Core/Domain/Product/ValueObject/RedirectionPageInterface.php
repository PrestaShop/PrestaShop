<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

/**
 * Redirection page which points to different page or displays certain response code.
 */
interface RedirectionPageInterface
{
    /**
     * @return ResponseCode
     */
    public function getResponseCode(): ResponseCode;

    /**
     * Type of redirection page - e.g category or product.
     *
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * Unique identifier of redirection page.
     *
     * @return int|null
     */
    public function getId(): ?int;
}
