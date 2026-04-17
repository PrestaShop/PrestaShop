<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class ApiClientConstraintException extends ApiClientException
{
    public const INVALID_ID = 1;
    public const CLIENT_ID_ALREADY_USED = 2;
    public const INVALID_CLIENT_ID = 3;
    public const CLIENT_NAME_ALREADY_USED = 4;
    public const INVALID_CLIENT_NAME = 5;
    public const INVALID_ENABLED = 6;
    public const INVALID_DESCRIPTION = 7;
    public const CLIENT_ID_TOO_LARGE = 8;
    public const CLIENT_NAME_TOO_LARGE = 9;
    public const DESCRIPTION_TOO_LARGE = 10;
    public const INVALID_SCOPES = 11;
    public const NON_INSTALLED_SCOPES = 12;
    public const NOT_POSITIVE_LIFETIME = 13;
    public const INVALID_SECRET = 14;

    public static function buildFromPropertyPath(string $propertyPath, string $message, string $template): self
    {
        switch ($propertyPath) {
            case 'clientId':
                if ($template === 'This value is already used.') {
                    $errorCode = self::CLIENT_ID_ALREADY_USED;
                } elseif (preg_match('/This value is too long/', $template) > 0) {
                    $errorCode = self::CLIENT_ID_TOO_LARGE;
                } else {
                    $errorCode = self::INVALID_CLIENT_ID;
                }
                break;
            case 'clientName':
                if ($template === 'This value is already used.') {
                    $errorCode = self::CLIENT_NAME_ALREADY_USED;
                } elseif (preg_match('/This value is too long/', $template) > 0) {
                    $errorCode = self::CLIENT_NAME_TOO_LARGE;
                } else {
                    $errorCode = self::INVALID_CLIENT_NAME;
                }
                break;
            case 'enabled':
                $errorCode = self::INVALID_ENABLED;
                break;
            case 'description':
                if (preg_match('/This value is too long/', $template) > 0) {
                    $errorCode = self::DESCRIPTION_TOO_LARGE;
                } else {
                    $errorCode = self::INVALID_DESCRIPTION;
                }
                break;
            case 'scopes':
                $errorCode = self::NON_INSTALLED_SCOPES;
                break;
            case 'lifetime':
                $errorCode = self::NOT_POSITIVE_LIFETIME;
                break;
            default:
                throw new InvalidArgumentException(sprintf('Unknown property path %s', $propertyPath));
        }

        return new self($message, $errorCode);
    }
}
