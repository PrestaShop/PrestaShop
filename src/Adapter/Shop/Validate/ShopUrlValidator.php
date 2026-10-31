<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\Validate;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopUrlConstraintException;
use ShopUrl;

final class ShopUrlValidator extends AbstractObjectModelValidator
{
    private const RESERVED_VIRTUAL_URIS = ['c', 'img'];

    public function validate(ShopUrl $shopUrl): void
    {
        $this->validateObjectModelProperty($shopUrl, 'domain', ShopUrlConstraintException::class, ShopUrlConstraintException::INVALID_DOMAIN);
        $this->validateObjectModelProperty($shopUrl, 'domain_ssl', ShopUrlConstraintException::class, ShopUrlConstraintException::INVALID_DOMAIN_SSL);
        $this->validateObjectModelProperty($shopUrl, 'physical_uri', ShopUrlConstraintException::class, ShopUrlConstraintException::INVALID_PHYSICAL_URI);

        $virtualUri = str_replace('/', '', (string) $shopUrl->virtual_uri);
        if (in_array($virtualUri, self::RESERVED_VIRTUAL_URIS, true) || is_numeric($virtualUri) || !preg_match('/^[a-z\d\-_]*$/i', $virtualUri)) {
            throw ShopUrlConstraintException::invalidVirtualUri($virtualUri);
        }
        $this->validateObjectModelProperty($shopUrl, 'virtual_uri', ShopUrlConstraintException::class, ShopUrlConstraintException::INVALID_VIRTUAL_URI);

        if ($shopUrl->main && !$shopUrl->active) {
            throw new ShopUrlConstraintException('The main URL of a shop cannot be disabled', ShopUrlConstraintException::MAIN_URL_MUST_BE_ACTIVE);
        }

        if ($shopUrl->canAddThisUrl($shopUrl->domain, $shopUrl->domain_ssl, (string) $shopUrl->physical_uri, (string) $shopUrl->virtual_uri)) {
            throw new ShopUrlConstraintException(
                sprintf('A shop url using domain "%s" already exists', $shopUrl->domain),
                ShopUrlConstraintException::URL_ALREADY_USED
            );
        }
    }
}
