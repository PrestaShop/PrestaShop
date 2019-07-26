<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

/**
 * Redirection page which displays certain response code and redirects to the page provided with type.
 */
interface TypedRedirectionPageInterface extends RedirectionPageInterface
{
    /**
     * Type of redirection page - e.g category or product.
     *
     * @return string|null
     */
    public function getType(): string;

    /**
     * Unique identifier of redirection page.
     *
     * @return int|null
     */
    public function getId(): int;
}
