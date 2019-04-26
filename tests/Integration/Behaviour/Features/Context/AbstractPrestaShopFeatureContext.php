<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Context as BehatContext;

/**
 * PrestaShopFeatureContext provides behat hooks to perform necessary operations for testing:
 * - shop setup
 * - database reset between scenario
 * - cache clear between steps
 * - ...
 */
abstract class AbstractPrestaShopFeatureContext implements BehatContext
{
    const MODULES_DIRECTORY = __DIR__ . '/../../../../Resources/modules';

    protected function checkFixtureExists(array $fixtures, $fixtureName, $fixtureIndex)
    {
        if (!isset($fixtures[$fixtureIndex])) {
            $fixtureNames = array_keys($fixtures);
            $firstFixtureNames = array_splice($fixtureNames, 0, 5);
            $firstFixtureNamesStr = implode(',', $firstFixtureNames);
            throw new \Exception($fixtureName . ' named "' . $fixtureIndex . '" was not added in fixtures. First 5 added are: ' . $firstFixtureNamesStr);
        }
    }
}
