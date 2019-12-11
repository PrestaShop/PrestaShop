<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Exception;
use Language;
use ObjectModel;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Shop;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

abstract class AbstractDomainFeatureContext implements Context
{
    /**
     * @var Exception|null
     */
    protected $lastException;

    /**
     * @var int
     */
    protected $lastErrorCode;

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
     * @return CommandBusInterface
     *
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    protected function getCommandBus()
    {
        return CommonFeatureContext::getContainer()->get('prestashop.core.command_bus');
    }

    /**
     * @return CommandBusInterface
     *
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
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
        if (null !== $this->lastException) {
            throw new RuntimeException(sprintf(
                'An unexpected exception was thrown %s: %s',
                get_class($this->lastException),
                $this->lastException->getMessage()
            ), 0, $this->lastException);
        }
    }

    /**
     * @param string $expectedError
     * @param int|null $errorCode
     *
     * @throws RuntimeException
     */
    protected function assertLastErrorIs($expectedError, $errorCode = null)
    {
        if (!$this->lastException instanceof $expectedError) {
            throw new RuntimeException(sprintf(
                'Last error should be "%s", but got "%s"',
                $expectedError,
                $this->lastException ? get_class($this->lastException) : 'null'
            ), 0, $this->lastException);
        }
        if (null !== $errorCode && $this->lastException->getCode() !== $errorCode) {
            throw new RuntimeException(sprintf(
                'Last error should have code "%s", but has "%s"',
                $errorCode,
                $this->lastException ? $this->lastException->getCode() : 'null'
            ), 0, $this->lastException);
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
        $arrayValues = explode(';', $parsedArray);
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
     * @Given single shop context is loaded
     */
    protected function singleShopContextIsLoaded()
    {
        Shop::setContext(Shop::CONTEXT_SHOP);
    }

    /**
     * @Given multiple shop context is loaded
     */
    protected function multipleShopContextIsLoaded()
    {
        Shop::setContext(Shop::CONTEXT_ALL);
    }

    /**
     * Used in scenarios which have horizontal properties table
     *
     * @param TableNode $table
     *
     * @return array
     *
     * @throws RuntimeException
     */
    protected function extractFirstHorizontalRowFromProperties(TableNode $table): array
    {
        $hash = $table->getHash();
        if (count($hash) != 1) {
            throw new RuntimeException('Properties are invalid');
        }
        /** @var array $data */
        $data = $hash[0];

        return $data;
    }

    /**
     * @param string|int $value
     *
     * @return bool
     */
    protected function castToBool($value)
    {
        if (is_numeric($value)) {
            return (bool) $value; // 0 => false; all other true
        } else {
            $lValue = strtolower($value);

            return $lValue == 'false' ? false : true;
        }
    }
}
