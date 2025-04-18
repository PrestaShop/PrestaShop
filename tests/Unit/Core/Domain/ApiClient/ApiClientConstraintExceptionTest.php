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

namespace Tests\Unit\Core\Domain\ApiClient;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientConstraintException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class ApiClientConstraintExceptionTest extends TestCase
{
    /** @dataProvider exceptionParametersProvider */
    public function testBuildFromPropertyPath(string $propertyPath, string $message, string $template, ApiClientConstraintException $expectedException): void
    {
        $exception = ApiClientConstraintException::buildFromPropertyPath($propertyPath, $message, $template);
        static::assertEquals($expectedException, $exception);
    }

    public function testBuildFromPropertyPathWithUnknownPropertyPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ApiClientConstraintException::buildFromPropertyPath('unknownProperty', 'some message', 'some template');
    }

    public function exceptionParametersProvider(): array
    {
        return [
            [
                'clientId',
                'This value is already used.',
                'This value is already used.',
                new ApiClientConstraintException('This value is already used.', ApiClientConstraintException::CLIENT_ID_ALREADY_USED),
            ],
            [
                'clientId',
                'Test exception message',
                'Test exception message',
                new ApiClientConstraintException('Test exception message', ApiClientConstraintException::INVALID_CLIENT_ID),
            ],
            [
                'clientName',
                'This value is already used.',
                'This value is already used.',
                new ApiClientConstraintException('This value is already used.', ApiClientConstraintException::CLIENT_NAME_ALREADY_USED),
            ],
            [
                'clientName',
                'Test exception message',
                'Test exception message',
                new ApiClientConstraintException('Test exception message', ApiClientConstraintException::INVALID_CLIENT_NAME),
            ],
            [
                'enabled',
                'Test exception message',
                'Test exception message',
                new ApiClientConstraintException('Test exception message', ApiClientConstraintException::INVALID_ENABLED),
            ],
            [
                'description',
                'Test exception message',
                'Test exception message',
                new ApiClientConstraintException('Test exception message', ApiClientConstraintException::INVALID_DESCRIPTION),
            ],
            [
                'clientId',
                'This value is too long.',
                'This value is too long',
                new ApiClientConstraintException('This value is too long.', ApiClientConstraintException::CLIENT_ID_TOO_LARGE),
            ],
            [
                'clientName',
                'This value is too long.',
                'This value is too long',
                new ApiClientConstraintException('This value is too long.', ApiClientConstraintException::CLIENT_NAME_TOO_LARGE),
            ],
            [
                'description',
                'This value is too long.',
                'This value is too long',
                new ApiClientConstraintException('This value is too long.', ApiClientConstraintException::DESCRIPTION_TOO_LARGE),
            ],
        ];
    }
}
