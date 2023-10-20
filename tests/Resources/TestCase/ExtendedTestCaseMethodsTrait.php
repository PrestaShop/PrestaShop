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

namespace Tests\Resources\TestCase;

use PHPUnit\Framework\Assert;

trait ExtendedTestCaseMethodsTrait
{
    private function compareWithEpsilon($expected, $actual, $message)
    {
        $success = false;

        // see https://github.com/sebastianbergmann/phpunit/issues/4966#issuecomment-1367081755
        if (abs($expected - $actual) < 10 ** -ini_get('precision')) {
            $success = true;
        }

        Assert::assertTrue($success, $message);
    }

    public function assertEqualsWithEpsilon($expected, $actual, $message = '')
    {
        if (!is_array($expected)) {
            $expectedArray[]['a'] = $expected; // we recreate the structure of the array as in Tools::spreadAmount()
        } else {
            $expectedArray = $expected;
        }

        if (!is_array($actual)) {
            $actualArray[]['a'] = $actual; // we recreate the structure of the array as in Tools::spreadAmount()
        } else {
            $actualArray = $actual;
        }

        foreach ($expectedArray as $key => $item) {
            $this->compareWithEpsilon($item['a'], $actualArray[$key]['a'], $message);
        }
    }
}
