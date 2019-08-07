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
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use ObjectModel;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

abstract class AbstractDomainFeatureContext implements Context
{
    /**
     * @var \Exception|null
     */
    protected $lastException;

    /**
     * @BeforeSuite
     */
    public static function prepare(BeforeSuiteScope $scope)
    {
        // Disable legacy object model cache to prevent conflicts between scenarios.
        ObjectModel::disableCache();
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

    /**
     * @param string $expectedError
     * @param int|null $errorCode
     */
    protected function assertLastErrorIs($expectedError, $errorCode = null)
    {
        if (!$this->lastException instanceof $expectedError) {
            throw new RuntimeException(sprintf(
                'Last error should be "%s", but got "%s"',
                $expectedError,
                $this->lastException ? get_class($this->lastException) : 'null'
            ));
        }
        if (null !== $errorCode && $this->lastException->getCode() !== $errorCode) {
            throw new RuntimeException(sprintf(
                'Last error should have code "%s", but has "%s"',
                $errorCode,
                $this->lastException ? $this->lastException->getCode() : 'null'
            ));
        }
    }
}
