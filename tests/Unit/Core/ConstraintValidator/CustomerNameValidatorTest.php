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
     * A name has to carry a letter. Everything here is punctuation the pattern allows inside a name but
     * which says nothing on its own, which is how an account named "-" could be created.
     *
     * @return array
     */
    public function getNamesWithoutALetter()
    {
        return [
            ['-'], ['--'], ['- -'], ["'"], ["''"], ["-'-"], ['  -  '],
        ];
    }

    /**
     * The counter-examples raised when this was discussed: one and two letter names exist, and a name
     * may legitimately carry a hyphen, an apostrophe or a non-latin script. None of these may regress.
     *
     * @return array
     */
    public function getShortAndInternationalNames()
    {
        return [
            ['E'], ['Li'], ['Xu Li'], ["O'Brien"], ['Jean-Luc'], ['Ann-Marie'],
            ['李'], ['Þór'], ['van der Berg'],
        ];
    }

    /**
     * @dataProvider getNamesWithoutALetter
     *
     * @param string $name
     */
    public function testItFailsWhenTheNameCarriesNoLetter($name)
    {
        $this->validator->validate($name, new CustomerName());

        $this->buildViolation((new CustomerName())->message)
            ->assertRaised()
        ;
    }

    /**
     * @dataProvider getShortAndInternationalNames
     *
     * @param string $name
     */
    public function testItAcceptsShortAndNonLatinNames($name)
    {
        $this->validator->validate($name, new CustomerName());

        $this->assertNoViolation();
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
