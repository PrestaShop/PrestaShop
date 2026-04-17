<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
