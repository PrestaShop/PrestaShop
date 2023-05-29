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

namespace Tests\Unit\Core\Domain;

use DomainException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\BulkFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Exception\ProductException;
use Throwable;

class AbstractBulkCommandHandlerTest extends TestCase
{
    public function testItThrowsInvalidArgumentExceptionWhenUnsupportedTypeIsProvided(): void
    {
        $handler = new TestAbstractBulkCommandHandler(
            [],
            ExampleId2::class
        );

        $this->expectException(InvalidArgumentException::class);
        $handler->handle([new ExampleId(1)], DomainException::class);
    }

    public function testItStopsOnFirstErrorWhenExceptionIsNotInstanceOfExceptionToCatch(): void
    {
        // random code to throw in test handler and later assert
        $expectedCode = 50;
        $failingId = new FailingId(2, new FeatureException('test', $expectedCode));
        $handler = new TestAbstractBulkCommandHandler([$failingId], IdInterface::class);

        try {
            $handler->handle(
                [new ExampleId(1), $failingId, new ExampleId(3)],
                ProductException::class
            );
        } catch (Throwable $e) {
            // ensure that thrown exception was the one provided in this test case
            Assert::assertInstanceOf(FeatureException::class, $e);
            Assert::assertEquals($expectedCode, $e->getCode());
        }

        // and assert that only the first id was handled and loop did not continue after second id failure
        Assert::assertSame([1], $handler->getHandledIds());
    }

    public function testItDoesNotStopLoopingAndThrowsBulkCommandExceptionWhenExceptionToCatchMatchesThrownException(): void
    {
        $expectedCode = 50;
        $failingId = new FailingId(1, new FeatureException('test', $expectedCode));
        $handler = new TestAbstractBulkCommandHandler([$failingId], IdInterface::class);

        try {
            $handler->handle(
                [$failingId, new ExampleId(2), new ExampleId(3)],
                FeatureException::class
            );
        } catch (Throwable $e) {
            // ensure that thrown exception was the one provided in this test case
            Assert::assertInstanceOf(BulkCommandExceptionInterface::class, $e);
            foreach ($e->getExceptions() as $exception) {
                // check that exception list contains expected exceptions and codes inside the bulk exception
                Assert::assertInstanceOf(FeatureException::class, $exception);
                Assert::assertEquals($expectedCode, $exception->getCode());
            }
        }

        // and assert that handler continued after first id failure
        Assert::assertSame([2, 3], $handler->getHandledIds());
    }
}

class TestAbstractBulkCommandHandler extends AbstractBulkCommandHandler
{
    /**
     * @var FailingId[]
     */
    private $failingIdsMock;

    /**
     * @var string
     */
    private $supportedIdType;

    /**
     * @var int[]
     */
    private $handledIds = [];

    public function __construct(
        array $failingIdsMock,
        string $supportedIdType
    ) {
        $this->failingIdsMock = $failingIdsMock;
        $this->supportedIdType = $supportedIdType;
    }

    /**
     * Allows test case to check which ids was looped through and which wasn't reached,
     * to make sure that loop stopped where expected when exception was thrown
     *
     * @return int[]
     */
    public function getHandledIds(): array
    {
        return $this->handledIds;
    }

    /**
     * @param IdInterface[] $ids
     * @param string $exceptionToCatch
     */
    public function handle(array $ids, string $exceptionToCatch, mixed $command = null): void
    {
        $this->handleBulkAction($ids, $exceptionToCatch, $command);
    }

    protected function buildBulkException(array $coughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkFeatureException($coughtExceptions, 'test bulk action failed');
    }

    /**
     * @param mixed $id
     */
    protected function handleSingleAction(mixed $id, $command): void
    {
        foreach ($this->failingIdsMock as $failingId) {
            if ($id->getValue() === $failingId->getValue()) {
                throw $failingId->getExceptionToThrow();
            }
        }
        $this->handledIds[] = $id->getValue();
    }

    protected function supports($id): bool
    {
        return $id instanceof $this->supportedIdType;
    }
}

interface IdInterface
{
    public function getValue(): int;
}

class ExampleId implements IdInterface
{
    private $id;

    public function __construct(
        int $id
    ) {
        $this->id = $id;
    }

    public function getValue(): int
    {
        return $this->id;
    }
}

class ExampleId2 extends ExampleId
{
}

class FailingId implements IdInterface
{
    private $id;

    private $exceptionToThrow;

    public function __construct(
        int $id,
        Throwable $exceptionToThrow
    ) {
        $this->id = $id;
        $this->exceptionToThrow = $exceptionToThrow;
    }

    public function getValue(): int
    {
        return $this->id;
    }

    public function getExceptionToThrow(): Throwable
    {
        return $this->exceptionToThrow;
    }
}
