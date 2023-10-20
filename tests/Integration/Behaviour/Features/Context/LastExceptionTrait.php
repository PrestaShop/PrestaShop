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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
