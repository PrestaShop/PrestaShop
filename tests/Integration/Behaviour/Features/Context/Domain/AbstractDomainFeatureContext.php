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
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Testwork\Tester\Result\TestResult;
use Configuration;
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
    const LAST_EXCEPTION_STORAGE_KEY = 'LAST_EXCEPTION';

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
     * @AfterScenario
     */
    public function checkLastException(AfterScenarioScope $scope)
    {
        $e = $this->getLastException();
        $this->cleanLastException();

        if (TestResult::FAILED === $scope->getTestResult()->getResultCode() && null !== $e) {
            throw new RuntimeException(sprintf('Might be related to the last exception: %s: %s Use -vvv for additional stack trace info', get_class($e), $e->getMessage()), 0, $e);
        }
    }

    /**
     * @BeforeScenario
     */
    public function cleanLastException()
    {
        $this->getSharedStorage()->set(self::LAST_EXCEPTION_STORAGE_KEY, null);
    }

    protected function setLastException(Exception $e): void
    {
        $this->getSharedStorage()->set(self::LAST_EXCEPTION_STORAGE_KEY, $e);
    }

    protected function getLastException(): ?Exception
    {
        if (!$this->getSharedStorage()->exists(self::LAST_EXCEPTION_STORAGE_KEY)) {
            return null;
        }

        if (!$e = $this->getSharedStorage()->get(self::LAST_EXCEPTION_STORAGE_KEY)) {
            return null;
        }

        return $e;
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
        $e = $this->getLastException();

        if (null !== $e) {
            throw new RuntimeException(sprintf('An unexpected exception was thrown %s: %s', get_class($e), $e->getMessage()), 0, $e);
        }
    }

    /**
     * @param string $expectedError
     * @param int|null $errorCode
     */
    protected function assertLastErrorIs($expectedError, $errorCode = null)
    {
        $e = $this->getLastException();

        if (!$e instanceof $expectedError) {
            throw new RuntimeException(sprintf('Last error should be "%s", but got "%s"', $expectedError, $e ? get_class($e) : 'null'), 0, $e);
        }
        if (null !== $errorCode && $e->getCode() !== $errorCode) {
            throw new RuntimeException(sprintf('Last error should have code "%s", but has "%s"', $errorCode, $e ? $e->getCode() : 'null'), 0, $e);
        }
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
