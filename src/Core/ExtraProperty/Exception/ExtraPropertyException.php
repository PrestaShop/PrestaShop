<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Throwable;

/**
 * Base exception class for all ExtraProperty-related exceptions.
 *
 * Extend this class to create more fine-grained exception types within the
 * ExtraProperty namespace so callers can catch them individually or as a group.
 */
class ExtraPropertyException extends CoreException
{
    /**
     * Set when the exception was built through prefixed(); null means the message carries no
     * locating prefix.
     */
    private ?string $bareMessage = null;

    /**
     * Builds an exception whose message is "$prefix$bareMessage" while keeping the bare message
     * retrievable — use this instead of baking a locating prefix ("Line %d: ",
     * "ExtraPropertyDefinition: "…) into the message string, so a consumer that shows the error
     * IN PLACE (e.g. on the offending form row) does not have to strip the prefix back off.
     */
    public static function prefixed(string $prefix, string $bareMessage, ?Throwable $previous = null): static
    {
        $exception = new static($prefix . $bareMessage, 0, $previous);
        $exception->bareMessage = $bareMessage;

        return $exception;
    }

    /**
     * The message without the locating prefix prefixed() added — the full message when the
     * exception was not built through prefixed().
     */
    public function getBareMessage(): string
    {
        return $this->bareMessage ?? $this->getMessage();
    }
}
