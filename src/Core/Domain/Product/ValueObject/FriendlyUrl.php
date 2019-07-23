<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * An url which appears in the browser for product.
 */
final class FriendlyUrl
{
    public const MAX_SIZE = 128;

    /**
     * @param string $friendlyUrl
     *
     * @throws ProductConstraintException
     */
    public function __construct(string $friendlyUrl)
    {
        $this->setFriendlyUrl($friendlyUrl);
    }

    /**
     * @param string $friendlyUrl
     *
     * @throws ProductConstraintException
     */
    private function setFriendlyUrl(string $friendlyUrl)
    {
        if (strlen($friendlyUrl) > self::MAX_SIZE) {
            throw new ProductConstraintException(
                sprintf(
                    'Friendly url "%s" has breached max size which is %d',
                    $friendlyUrl,
                    self::MAX_SIZE
                ),
                ProductConstraintException::FRIENDLY_URL_TOO_LONG
            );
        }
    }
}
