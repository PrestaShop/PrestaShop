<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Exception;

class ShopUrlConstraintException extends ShopUrlException
{
    public const INVALID_ID = 1;
    public const INVALID_DOMAIN = 2;
    public const INVALID_DOMAIN_SSL = 3;
    public const INVALID_PHYSICAL_URI = 4;
    public const INVALID_VIRTUAL_URI = 5;
    public const URL_ALREADY_USED = 6;
    public const MAIN_URL_MUST_BE_ACTIVE = 7;
    public const MAIN_URL_CANNOT_BE_UNSET = 8;

    private string $invalidVirtualUri = '';

    public static function invalidVirtualUri(string $virtualUri): self
    {
        $exception = new self(sprintf('A shop virtual URL cannot be "%s"', $virtualUri), self::INVALID_VIRTUAL_URI);
        $exception->invalidVirtualUri = $virtualUri;

        return $exception;
    }

    public function getInvalidVirtualUri(): string
    {
        return $this->invalidVirtualUri;
    }
}
