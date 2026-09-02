<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerName;
use PrestaShop\PrestaShop\Core\ConstraintValidator\CustomerNameValidator;
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
    public function getValidCharactersWithSpaces()
    {
        return [
            ['. '], ['。 '],
        ];
    }

    /**
     * @return array
     */
    public function getValidCharacters()
    {
        return [
            ['.'], ['。'],
        ];
    }

    public function testIfFailsWhenInputIsOnlyBlank()
    {
        $this->validator->validate(' ', new CustomerName());

        $this->buildViolation((new CustomerName())->message)
            ->assertRaised()
        ;
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
     * @dataProvider getValidCharactersWithSpaces
     *
     * @param string $invalidChar
     */
    public function testIfFailsWhenSpacedPointsAreFinal($invalidChar)
    {
        $input = 'AZERTY' . $invalidChar;
        $this->validator->validate($input, new CustomerName());

        $this->buildViolation((new CustomerName())->message)
            ->assertRaised()
        ;
    }

    /**
     * @dataProvider getValidCharacters
     *
     * @param string $invalidChar
     */
    public function testIfFailsWhenDoublePoints($invalidChar)
    {
        $input = 'AZE' . $invalidChar . 'RTY' . $invalidChar;
        $this->validator->validate($input, new CustomerName());

        $this->buildViolation((new CustomerName())->message)
            ->assertRaised()
        ;
    }

    public function testIfSucceedsWhenNoPoints()
    {
        $input = 'AZERTY';
        $this->validator->validate($input, new CustomerName());

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getValidCharacters
     *
     * @param string $validChar
     */
    public function testIfSucceedsWhenPointsAreFinal($validChar)
    {
        $input = 'AZERTY' . $validChar;
        $this->validator->validate($input, new CustomerName());

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getValidCharactersWithSpaces
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
