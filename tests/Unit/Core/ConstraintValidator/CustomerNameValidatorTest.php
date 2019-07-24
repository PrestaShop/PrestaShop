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

namespace Tests\Unit\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\CustomerNameValidator;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerName;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CustomerNameValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @return array
     */
    public function getInvalidCharacters()
    {
        return [
            ['0'], ['1'], ['2'], ['3'], ['4'],
            ['5'], ['6'], ['7'], ['8'], ['9'],
            ['!'], ['<'], ['>'], [','], [';'],
            ['?'], ['='], ['+'], ['('], [')'],
            ['/'], ['\\'], ['@'], ['#'], ['"'],
            ['°'], ['*'], ['`'], ['{'], ['}'],
            ['_'], ['^'], ['$'], ['%'], [':'],
            ['¤'], ['['], [']'], ['|'], ['.'],
            ['。'], ['.  '], ['。  '],
        ];
    }

    /**
     * @return array
     */
    public function getValidCharacters()
    {
        return [
            ['. '], ['。 '],
        ];
    }

    /**
     * @dataProvider getInvalidCharacters
     *
     * @param string $invalidChar
     */
    public function testIfFailsWhenBadCharactersAreGiven($invalidChar)
    {
        $input = 'AZE' . $invalidChar . 'RTY';
        $this->validator->validate($input, new CustomerName());

        $this->buildViolation((new CustomerName())->message)
            ->assertRaised()
        ;
    }

    /**
     * @dataProvider getValidCharacters
     *
     * @param string $validChar
     */
    public function testIfSucceedsWhenPointsWithSpacesAreGiven($validChar)
    {
        $input = 'AZE' . $validChar . 'RTY';
        $this->validator->validate($input, new CustomerName());

        $this->assertNoViolation();
    }

    protected function createValidator()
    {
        return new CustomerNameValidator();
    }
}
