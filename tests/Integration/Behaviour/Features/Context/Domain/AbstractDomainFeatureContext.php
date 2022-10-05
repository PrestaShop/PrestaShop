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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\StepScope;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\StepNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Configuration;
use Currency;
use Exception;
use Language;
use ObjectModel;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

abstract class AbstractDomainFeatureContext implements Context
{
    /**
     * Shared storage key for last thrown exception
     */
    private const LAST_EXCEPTION_STORAGE_KEY = 'LAST_EXCEPTION';

    /**
     * Shared storage key for expected thrown exception
     */
    private const EXPECTED_EXCEPTION_STORAGE_KEY = 'EXPECTED_EXCEPTION';

    /**
     * Shared storage key for the step where the expected exception was raised
     */
    private const EXPECTED_EXCEPTION_STEP_STORAGE_KEY = 'EXPECTED_EXCEPTION_STEP';

    /**
     * @BeforeSuite
     *
     * @param BeforeSuiteScope $scope
     */
    public static function prepare(BeforeSuiteScope $scope)
    {
        // Disable legacy object model cache to prevent conflicts between scenarios.
        ObjectModel::disableCache();
    }

    /**
     * @AfterStep
     */
    public function checkLastExceptionAfterStep(AfterStepScope $scope): void
    {
        // If no exception nothing to do, if there is already an exception to handle we don't override it
        if (null === $this->getLastException() || null !== $this->getExpectedException()) {
            return;
        }

        $e = $this->getLastException();
        // We clean the last exception so that it doesn't pollute the following steps or scenarios, besides multiple
        // contexts could have this hook, and we only need to handle it once
        $this->cleanLastException();

        // When the last step ends with an exception we throw it because there is no next step to assert it
        $lastStep = $this->getLastStepFromScope($scope);
        if ($lastStep === $scope->getStep()) {
            throw $e;
        }

        // If there are steps left the exception must be checked in the next step, it is stored as the expected exception
        $this->setExpectedException($e, $scope->getStep());
    }

    /**
     * @AfterStep
     */
    public function checkExpectedExceptionAfterStep(AfterStepScope $scope): void
    {
        if (null === $this->getExpectedException() || $scope->getStep() === $this->getExpectedExceptionStep()) {
            return;
        }

        // When an expected exception is stored from another step it means it was not checked, so it is unexpected
        $unexpectedException = $this->getExpectedException();
        $exceptionStep = $this->getExpectedExceptionStep();

        // We clean the expected exception so that it doesn't pollute the following scenarios
        $this->cleanExpectedException();

        throw new RuntimeException(implode(PHP_EOL, [
            'An unexpected exception was raised in previous step:',
            sprintf('Line %d: %s', $exceptionStep->getLine(), $exceptionStep->getText()),
            sprintf('%s: %s', get_class($unexpectedException), $unexpectedException->getMessage()),
            'Either it was unexpected and an error occurred or you forgot to add an intermediate step to assert that exception using assertLastErrorIs',
        ]), 0, $unexpectedException);
    }

    /**
     * This method shouldn't be public, but it is mandatory to be a behat hook. But you shouldn't call it manually.
     *
     * @BeforeScenario
     */
    public function cleanStoredExceptionsBeforeScenario(): void
    {
        $this->cleanLastException();
        $this->cleanExpectedException();
    }

    protected function setLastException(Exception $e): void
    {
        $this->getSharedStorage()->set(self::LAST_EXCEPTION_STORAGE_KEY, $e);
    }

    protected function getLastStepFromScope(StepScope $scope): StepNode
    {
        $scenario = $this->getScenarioFromScope($scope);
        if (null !== $scenario) {
            $steps = $scenario->getSteps();
        } else {
            foreach ($scope->getFeature()->getBackground()->getSteps() as $step) {
                if ($step === $scope->getStep()) {
                    $steps = $scope->getFeature()->getBackground()->getSteps();
                    break;
                }
            }
        }

        // The step was not found in any scenario nor the background
        if (!isset($steps)) {
            throw new RuntimeException('Could not find step in the feature');
        }

        return $steps[count($steps) - 1];
    }

    protected function getScenarioFromScope(StepScope $scope): ?ScenarioInterface
    {
        foreach ($scope->getFeature()->getScenarios() as $scenario) {
            foreach ($scenario->getSteps() as $step) {
                if ($step === $scope->getStep()) {
                    return $scenario;
                }
            }
        }

        return null;
    }

    /**
     * @return CommandBusInterface
     */
    protected function getCommandBus()
    {
        return CommonFeatureContext::getContainer()->get('prestashop.core.command_bus');
    }

    /**
     * @return CommandBusInterface
     */
    protected function getQueryBus()
    {
        return CommonFeatureContext::getContainer()->get('prestashop.core.query_bus');
    }

    /**
     * @return SharedStorage
     */
    protected function getSharedStorage()
    {
        return SharedStorage::getStorage();
    }

    protected function getContainer(): ContainerInterface
    {
        return CommonFeatureContext::getContainer();
    }

    /**
     * @throws RuntimeException
     */
    protected function assertLastErrorIsNull(): void
    {
        $e = $this->getExpectedException();

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
        $lastException = $this->getExpectedException();
        if (null === $lastException) {
            // Sometimes the last exception is asserted in the same step, so it is not stored as expected yet
            $lastException = $this->getLastException();
            $this->cleanLastException();
        } else {
            // The exception has been asserted, so it is indeed an expected one, and we can clean it
            $this->cleanExpectedException();
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
     * @param TableNode $tableNode
     *
     * @return array
     */
    protected function localizeByRows(TableNode $tableNode): array
    {
        return $this->parseLocalizedRow($tableNode->getRowsHash());
    }

    /**
     * @param TableNode $table
     *
     * @return array
     */
    protected function localizeByColumns(TableNode $table): array
    {
        $rows = [];
        foreach ($table->getColumnsHash() as $key => $column) {
            $row = [];
            foreach ($column as $columnName => $value) {
                $row[$columnName] = $value;
            }

            $rows[] = $this->parseLocalizedRow($row);
        }

        return $rows;
    }

    /**
     * @param string $localizedValue
     *
     * @return array
     */
    protected function localizeByCell(string $localizedValue): array
    {
        $localizedValues = [];
        $valuesByLang = explode(';', $localizedValue);
        foreach ($valuesByLang as $valueByLang) {
            $value = explode(':', $valueByLang);
            $langId = (int) Language::getIdByLocale($value[0], true);
            $localizedValues[$langId] = $value[1];
        }

        return $localizedValues;
    }

    /**
     * @return int
     */
    protected function getDefaultLangId(): int
    {
        return (int) Configuration::get('PS_LANG_DEFAULT');
    }

    protected function getDefaultCurrencyId(): int
    {
        return (int) Configuration::get('PS_CURRENCY_DEFAULT');
    }

    protected function getDefaultCurrencyIsoCode(): string
    {
        return Currency::getIsoCodeById($this->getDefaultCurrencyId());
    }

    /**
     * @return int
     */
    protected function getDefaultShopId(): int
    {
        return (int) Configuration::get('PS_SHOP_DEFAULT');
    }

    /**
     * This method is private because last exception should only be handled inside this abstract class, you can only
     * use setLastException from inherited classes.
     */
    private function cleanLastException(): void
    {
        $this->getSharedStorage()->clear(self::LAST_EXCEPTION_STORAGE_KEY);
    }

    /**
     * This method is private because last exception should only be accessed inside this abstract class, you can only
     * use setLastException from inherited classes.
     *
     * @return Exception|null
     */
    private function getLastException(): ?Exception
    {
        if (!$this->getSharedStorage()->exists(self::LAST_EXCEPTION_STORAGE_KEY)) {
            return null;
        }

        return $this->getSharedStorage()->get(self::LAST_EXCEPTION_STORAGE_KEY);
    }

    /**
     * This method is private because expected exception should only be handled inside this abstract class, to clean it
     * you need to assert it using the assertLastError function, this will automatically clean the stored exception.
     */
    private function cleanExpectedException(): void
    {
        $this->getSharedStorage()->clear(self::EXPECTED_EXCEPTION_STORAGE_KEY);
        $this->getSharedStorage()->clear(self::EXPECTED_EXCEPTION_STEP_STORAGE_KEY);
    }

    /**
     * This method is private because expected exception should only be handled inside this abstract class, the expected
     * exception is automatically stored after each step.
     *
     * @param Exception $e
     * @param StepNode $step
     */
    private function setExpectedException(Exception $e, StepNode $step): void
    {
        $this->getSharedStorage()->set(self::EXPECTED_EXCEPTION_STORAGE_KEY, $e);
        $this->getSharedStorage()->set(self::EXPECTED_EXCEPTION_STEP_STORAGE_KEY, $step);
    }

    /**
     * This method is private because expected exception should only be handled inside this abstract class, if you need
     * to assert it you should use the assertLastError function which returns the exception if you need more assertions.
     *
     * @return Exception|null
     */
    private function getExpectedException(): ?Exception
    {
        if (!$this->getSharedStorage()->exists(self::EXPECTED_EXCEPTION_STORAGE_KEY)) {
            return null;
        }

        return $this->getSharedStorage()->get(self::EXPECTED_EXCEPTION_STORAGE_KEY);
    }

    /**
     * This method is private because expected exception step should only be handled inside this abstract class, it is
     * only necessary to throw the unexpected exception in the next step only.
     *
     * @return StepNode|null
     */
    private function getExpectedExceptionStep(): ?StepNode
    {
        if (!$this->getSharedStorage()->exists(self::EXPECTED_EXCEPTION_STEP_STORAGE_KEY)) {
            return null;
        }

        return $this->getSharedStorage()->get(self::EXPECTED_EXCEPTION_STEP_STORAGE_KEY);
    }

    /**
     * @param array $row
     *
     * @return array
     */
    private function parseLocalizedRow(array $row): array
    {
        $parsedRow = [];
        foreach ($row as $key => $value) {
            $localeMatch = preg_match('/\[.*?\]/', $key, $matches) ? reset($matches) : null;

            if (!$localeMatch) {
                $parsedRow[$key] = $value;
                continue;
            }

            $propertyName = str_replace($localeMatch, '', $key);
            $locale = str_replace(['[', ']'], '', $localeMatch);

            $langId = (int) Language::getIdByLocale($locale, true);

            if (!$langId) {
                throw new RuntimeException(sprintf('Language by locale "%s" was not found', $locale));
            }

            $parsedRow[$propertyName][$langId] = $value;
        }

        return $parsedRow;
    }
}
