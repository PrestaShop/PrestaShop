<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ApiAccess\Exception;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class ApiAccessConstraintException extends ApiAccessException
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
            default:
                throw new InvalidArgumentException(sprintf('Unknown property path %s', $propertyPath));
        }

        return new self($message, $errorCode);
    }
}
