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
