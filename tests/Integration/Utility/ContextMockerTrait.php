<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Utility;

use Context;
use RuntimeException;

/**
 * This trait can be assigned to a test class when it is partially changing the context content or
 * its instance completely using @see Context::setInstanceForTesting
 *
 * When this trait is used a backup of the context is automatically made before class, and after class
 * it is all reset correctly.
 *
 * This trait can also be used to access a context mocker instance and mock it if needed.
 */
trait ContextMockerTrait
{
    /**
     * @var ContextMocker|null
     */
    protected static $contextMocker;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::backupContext();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::resetContext();
    }

    /**
     * Whenever the trait is used the context is ALWAYS saved at the beginning of the test class, thus allowing to
     * restore its state at the end of the test class. This is handled automatically by setUpBeforeClass and
     * tearDownAfterClass
     */
    protected static function backupContext(): void
    {
        if (!static::$contextMocker) {
            static::$contextMocker = new ContextMocker();
        }
        static::$contextMocker->backupContext();
    }

    /**
     * Optionally you can ask for the context to be mocked, it will then be replaced with mock values and filled with
     * most of the required context's values.
     */
    protected static function mockContext(): void
    {
        if (!static::$contextMocker) {
            static::$contextMocker = new ContextMocker();
        }
        static::$contextMocker->mockContext();
    }

    protected static function getContext(): Context
    {
        if (!static::$contextMocker) {
            throw new RuntimeException('No context mocker set, you cannot get a context that was never mocked or saved.');
        }

        return static::$contextMocker->getContext();
    }

    protected static function getMockedContext(): Context
    {
        if (!static::$contextMocker) {
            throw new RuntimeException('No context mocker set, you cannot get a mocked context that was never mocked.');
        }
        if (null === static::$contextMocker->getMockedContext()) {
            throw new RuntimeException('No context was mocked, to get a mocked context you need to first use ContextMockerTrait::mockContext method.');
        }

        return static::$contextMocker->getMockedContext();
    }

    protected static function resetContext(): void
    {
        if (!static::$contextMocker) {
            throw new RuntimeException('No context mocker set, you cannot reset a context that was never mocked or saved.');
        }
        static::$contextMocker->resetContext();
    }
}
