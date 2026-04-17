<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context;

use Exception;
use RuntimeException;

trait LastExceptionTrait
{
    protected function setLastException(Exception $e): void
    {
        $this->getSharedStorage()->set(CommonFeatureContext::LAST_EXCEPTION_STORAGE_KEY, $e);
    }

    /**
     * @throws RuntimeException
     */
    protected function assertLastErrorIsNull(): void
    {
        $e = $this->getExpectedExceptionFromStorage();

        if (null !== $e) {
            throw new RuntimeException(sprintf('An unexpected exception was thrown %s: %s', get_class($e), $e->getMessage()), 0, $e);
        }
    }

    /**
     * Assert the last caught exception matches the expected class and error code, then the saved
     * exception is cleaned, so you can only assert it once.
     *
     * @param string $expectedError
     * @param int|null $errorCode
     *
     * @return Exception Returns the exception in case additional assertions are needed
     */
    protected function assertLastErrorIs(string $expectedError, ?int $errorCode = null): Exception
    {
        $lastException = $this->getExpectedExceptionFromStorage();
        if (null === $lastException) {
            // Sometimes the last exception is asserted in the same step, so it is not stored as expected yet
            $lastException = $this->getLastExceptionFromStorage();
            $this->getSharedStorage()->clear(CommonFeatureContext::LAST_EXCEPTION_STORAGE_KEY);
        } else {
            // The exception has been asserted, so it is indeed an expected one, and we can clean it
            $this->getSharedStorage()->clear(CommonFeatureContext::EXPECTED_EXCEPTION_STORAGE_KEY);
        }

        if (!$lastException instanceof $expectedError) {
            throw new RuntimeException(sprintf('Last error should be "%s", but got "%s"', $expectedError, $lastException ? get_class($lastException) : 'null'), 0, $lastException);
        }

        if (null !== $errorCode && $lastException->getCode() !== $errorCode) {
            throw new RuntimeException(sprintf('Last error should have code "%s", but has "%s"', $errorCode, $lastException->getCode()), 0, $lastException);
        }

        return $lastException;
    }

    /**
     * This method is private because expected exception should only be handled inside this abstract class, if you need
     * to assert it you should use the assertLastError function which returns the exception if you need more assertions.
     *
     * @return Exception|null
     */
    private function getExpectedExceptionFromStorage(): ?Exception
    {
        if (!$this->getSharedStorage()->exists(CommonFeatureContext::EXPECTED_EXCEPTION_STORAGE_KEY)) {
            return null;
        }

        return $this->getSharedStorage()->get(CommonFeatureContext::EXPECTED_EXCEPTION_STORAGE_KEY);
    }

    /**
     * This method is private because expected exception should only be handled inside this abstract class, if you need
     * to assert it you should use the assertLastError function which returns the exception if you need more assertions.
     *
     * @return Exception|null
     */
    private function getLastExceptionFromStorage(): ?Exception
    {
        if (!$this->getSharedStorage()->exists(CommonFeatureContext::LAST_EXCEPTION_STORAGE_KEY)) {
            return null;
        }

        return $this->getSharedStorage()->get(CommonFeatureContext::LAST_EXCEPTION_STORAGE_KEY);
    }
}
