<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Grid\Row;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Row\RowCollection;

class RowCollectionTest extends TestCase
{
    /**
     * @var RowCollection
     */
    private $rowCollection;

    public function setUp()
    {
        $this->rowCollection = new RowCollection([
            [
                'first_name' => 'Tom',
                'last_name' => 'Developer',
                'age' => 29,
            ],
            [
                'first_name' => 'Greg',
                'last_name' => 'Tester',
                'age' => null,
            ],
        ]);
    }

    public function testItAppliesModificationToRows()
    {
        $ageFieldModifier = function ($row) {
            $row['age'] = is_int($row['age']) ? $row['age'] : '--';

            return $row;
        };

        $this->rowCollection->applyModification($ageFieldModifier);

        $this->assertEquals([
            [
                'first_name' => 'Tom',
                'last_name' => 'Developer',
                'age' => 29,
            ],
            [
                'first_name' => 'Greg',
                'last_name' => 'Tester',
                'age' => '--',
            ],
        ], $this->rowCollection->getRows());
    }

    public function testModifierCreatesNewFieldInRow()
    {
        $fullNameModifier = function ($row) {
            $row['full_name'] = sprintf('%s %s', $row['first_name'], $row['last_name']);

            return $row;
        };

        $this->rowCollection->applyModification($fullNameModifier);

        $this->assertEquals([
            [
                'first_name' => 'Tom',
                'last_name' => 'Developer',
                'age' => 29,
                'full_name' => 'Tom Developer',
            ],
            [
                'first_name' => 'Greg',
                'last_name' => 'Tester',
                'age' => null,
                'full_name' => 'Greg Tester',
            ],
        ], $this->rowCollection->getRows());
    }
}
