<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Core\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\ExceptionBuilder;
use Throwable;

class ExceptionBuilderTest extends TestCase
{
    private const OBJECT_ID = 42;
    private const EXCEPTION_CODE = 51;

    /**
     * @dataProvider getExceptionValues
     *
     * @param Throwable $expectedException
     * @param string $exceptionClass
     * @param string $message
     * @param int $errorCode
     * @param int|null $objectModelId
     */
    public function testBuild(Throwable $expectedException, string $exceptionClass, string $message, int $errorCode = 0, ?Throwable $previousException = null, ?int $objectModelId = null): void
    {
        $builtException = ExceptionBuilder::buildException($exceptionClass, $message, $errorCode, $previousException, $objectModelId);
        $this->assertEquals($expectedException, $builtException);
    }

    public function getExceptionValues(): iterable
    {
        $previousException = new Exception('test');

        yield 'product not found exception without id, code or previous exception' => [
            new ProductNotFoundException('product not found'),
            ProductNotFoundException::class,
            'product not found',
        ];

        yield 'product not found exception with id, code and previous exception' => [
            new ProductNotFoundException('product not found', self::EXCEPTION_CODE, $previousException),
            ProductNotFoundException::class,
            'product not found',
            self::EXCEPTION_CODE,
            $previousException,
            self::OBJECT_ID,
        ];

        yield 'employee not found exception based on an EmployeeId value object, no code, no previous' => [
            new EmployeeNotFoundException(new EmployeeId(self::OBJECT_ID), 'employee not found'),
            EmployeeNotFoundException::class,
            'employee not found',
            0,
            null,
            self::OBJECT_ID,
        ];

        yield 'employee not found exception, with code, with previous that has no type in constructor' => [
            new EmployeeNotFoundException(new EmployeeId(self::OBJECT_ID), 'employee not found', self::EXCEPTION_CODE, $previousException),
            EmployeeNotFoundException::class,
            'employee not found',
            self::EXCEPTION_CODE,
            $previousException,
            self::OBJECT_ID,
        ];
    }
}
