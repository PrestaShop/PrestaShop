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

        if (TestResult::FAILED === $scope->getTestResult()->getResultCode() && null !== $e) {
            throw new RuntimeException(sprintf('Might be related to the last exception: %s %s', get_class($e), $e->getTraceAsString()));
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

    protected function assertLastErrorIsNull()
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
     * Parse a localized string into a localized array, the expected format can be:
     *   fr-FR:valueFr;en-EN:valueEn:{localeCode}:{localeValue}
     *   1:valueFr;2:valueEn:{langId}:{localeValue}
     * and will be converted into an array indexed by language id
     *
     * @param string $parsedArray
     *
     * @return array
     */
    protected function parseLocalizedArray(string $parsedArray): array
    {
        $arrayValues = array_map('trim', explode(';', $parsedArray));
        $localizedArray = [];
        foreach ($arrayValues as $arrayValue) {
            $data = explode(':', $arrayValue);
            $langKey = $data[0];
            $langValue = $data[1];
            if (ctype_digit($langKey)) {
                $localizedArray[$langKey] = $langValue;
            } else {
                $localizedArray[Language::getIdByLocale($langKey, true)] = $langValue;
            }
        }

        return $localizedArray;
    }

    /**
     * @return int
     */
    protected function getDefaultLangId(): int
    {
        return (int) Configuration::get('PS_LANG_DEFAULT');
    }
}
