<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

    public function testItDrillThroughParameterCorrectTypeOfCommand(): void
    {
        $handler = new TestAbstractBulkCommandHandler(
            [],
            ExampleId::class
        );
        $command = new TestCommand(
            [new ExampleId(1)],
            true
        );

        $handler->handle($command->ids, DomainException::class, $command);

        $this->assertInstanceOf(TestCommand::class, $handler->getCommand());
    }

    /**
     * Handlers are services, so one instance serves every dispatch in a long running process (Behat,
     * a CLI command, a Messenger consumer). What one call collects must not still be there for the next.
     */
    public function testEachCallReportsOnlyItsOwnFailures(): void
    {
        $firstFailure = new FailingId(1, new FeatureException('first'));
        $secondFailure = new FailingId(2, new FeatureException('second'));
        $handler = new TestAbstractBulkCommandHandler([$firstFailure, $secondFailure], IdInterface::class);

        try {
            $handler->handle([$firstFailure, new ExampleId(10)], FeatureException::class);
            Assert::fail('the failing id should have produced a bulk exception');
        } catch (BulkCommandExceptionInterface $e) {
            Assert::assertCount(1, $e->getExceptions());
        }

        try {
            $handler->handle([$secondFailure, new ExampleId(11)], FeatureException::class);
            Assert::fail('the failing id should have produced a bulk exception');
        } catch (BulkCommandExceptionInterface $e) {
            Assert::assertCount(
                1,
                $e->getExceptions(),
                'the second call reported the first call failures as well'
            );
        }
    }

    public function testACallThatFailsOnNothingDoesNotThrow(): void
    {
        $failingId = new FailingId(1, new FeatureException('first'));
        $handler = new TestAbstractBulkCommandHandler([$failingId], IdInterface::class);

        try {
            $handler->handle([$failingId], FeatureException::class);
        } catch (BulkCommandExceptionInterface $e) {
            // expected; this call is only here to leave a failure behind
        }

        // Every id below succeeds, so reaching the end without an exception is the assertion.
        $handler->handle([new ExampleId(2), new ExampleId(3)], FeatureException::class);
        $this->addToAssertionCount(1);
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

    /** @var mixed */
    private $command = null;

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
     * Allows test case to check the commands that is drilled through method parameters.
     *
     * @return mixed
     */
    public function getCommand(): mixed
    {
        return $this->command;
    }

    /**
     * @param IdInterface[] $ids
     * @param string $exceptionToCatch
     */
    public function handle(array $ids, string $exceptionToCatch, mixed $command = null): void
    {
        $this->handleBulkAction($ids, $exceptionToCatch, $command);
    }

    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkFeatureException($caughtExceptions, 'test bulk action failed');
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
        $this->command = $command;
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

class TestCommand
{
    public function __construct(public readonly array $ids, public readonly bool $enabled)
    {
    }
}
