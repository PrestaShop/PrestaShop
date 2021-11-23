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

namespace Tests\Unit\Core\ConstraintValidator;

use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use PrestaShop\PrestaShop\Core\String\CharacterCleaner;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class TypedRegexValidatorTest extends ConstraintValidatorTestCase
{
    public function testItSucceedsForNameTypeWhenValidCharactersGiven(): void
    {
        $value = 'goodname';
        $this->validator->validate($value, new TypedRegex(['type' => 'name']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForNameType
     *
     * @param string $invalidChar
     */
    public function testItFailsForNameTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'name']), $invalidChar);
    }

    public function testItSucceedsForCatalogNameTypeWhenValidCharactersGiven()
    {
        $value = 'catalog name';
        $this->validator->validate($value, new TypedRegex(['type' => 'catalog_name']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForCatalogNameType
     *
     * @param string $invalidChar
     */
    public function testItFailsForCatalogNameTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'catalog_name']), $invalidChar);
    }

    public function testItSucceedsForGenericNameTypeWhenValidCharactersGiven()
    {
        $value = 'good generic name /';
        $this->validator->validate($value, new TypedRegex(['type' => 'generic_name']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForGenericNameType
     *
     * @param string $invalidChar
     */
    public function testItFailsForGenericNameTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'generic_name']), $invalidChar);
    }

    public function testItSucceedsForCityNameTypeWhenValidCharactersGiven(): void
    {
        $value = 'London';
        $this->validator->validate($value, new TypedRegex(['type' => 'city_name']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForCityNameType
     *
     * @param string $invalidChar
     */
    public function testItFailsForCityNameTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'city_name']), $invalidChar);
    }

    public function testItSucceedsForAddressTypeWhenValidCharactersGiven(): void
    {
        $value = '3197 Hillview Drive';
        $this->validator->validate($value, new TypedRegex(['type' => 'address']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForAddressType
     *
     * @param string $invalidChar
     */
    public function testItFailsForAddressTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'address']), $invalidChar);
    }

    public function testItSucceedsForPostCodeTypeWhenValidCharactersGiven(): void
    {
        $value = '94103';
        $this->validator->validate($value, new TypedRegex(['type' => 'post_code']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForPostCodeType
     *
     * @param string $invalidChar
     */
    public function testItFailsForPostCodeTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'post_code']), $invalidChar);
    }

    public function testItSucceedsForPhoneNumberTypeWhenValidCharactersGiven(): void
    {
        $value = '707-216-7924';
        $this->validator->validate($value, new TypedRegex(['type' => 'phone_number']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForPhoneNumberType
     *
     * @param string $invalidChar
     */
    public function testItFailsForPhoneNumberTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'phone_number']), $invalidChar);

        $this->buildViolation((new TypedRegex(['type' => 'phone_number']))->message)
            ->setParameter('%s', '"' . $invalidChar . '"')
            ->assertRaised();
    }

    public function testItSucceedsForMessageTypeWhenValidCharactersGiven(): void
    {
        $value = 'some random message #)F@$. ';
        $this->validator->validate($value, new TypedRegex(['type' => 'message']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForMessageType
     *
     * @param string $invalidChar
     */
    public function testItFailsForMessageTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'message']), $invalidChar);
    }

    public function testItSucceedsForLanguageIsoCodeTypeWhenValidCharactersGiven(): void
    {
        $value = 'US';
        $this->validator->validate($value, new TypedRegex(['type' => 'language_iso_code']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForLanguageIsoCodeType
     *
     * @param string $invalidChar
     */
    public function testItFailsForLanguageIsoCodeTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'language_iso_code']), $invalidChar);
    }

    public function testItSucceedsForLanguageCodeTypeWhenValidCharactersGiven(): void
    {
        $value = 'lt-LT';
        $this->validator->validate($value, new TypedRegex(['type' => 'language_code']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForLanguageCodeType
     *
     * @param string $invalidChar
     */
    public function testItFailsForLanguageCodeTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'language_code']), $invalidChar);
    }

    public function testItSucceedsForUpcTypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('12345678901', new TypedRegex('upc'));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForUpc
     *
     * @param string $invalidChar
     */
    public function testItFailsForUpcTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex('upc'), $invalidChar);
    }

    public function testItSucceedsForEan13TypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('1780201379629', new TypedRegex('ean_13'));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForEan13
     *
     * @param string $invalidChar
     */
    public function testItFailsForEan13TypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex('ean_13'), $invalidChar);
    }

    public function testItSucceedsForIsbnTypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('-1780', new TypedRegex('isbn'));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForIsbn
     *
     * @param string $invalidChar
     */
    public function testItFailsForIsbnTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex('isbn'), $invalidChar);
    }

    /**
     * @dataProvider getInvalidCharactersForReference
     *
     * @param string $invalidChar
     */
    public function testItFailsForReferenceTypeWhenInvalidCharacterGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex('reference'), $invalidChar);
    }

    public function testItSucceedsForReferenceTypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('product1', new TypedRegex('reference'));

        $this->assertNoViolation();
    }

    public function testItSucceedsForUrlTypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('test.com', new TypedRegex(['type' => 'url']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForUrl
     *
     * @param string $invalidChar
     */
    public function testItFailsForUrlTypeWhenInvalidCharactersGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'url']), $invalidChar);
    }

    public function testItSucceedsForModuleNameTypeWhenValidCharactersGiven(): void
    {
        $this->validator->validate('my-name', new TypedRegex(['type' => 'module_name']));

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidCharactersForModuleName
     *
     * @param string $invalidChar
     */
    public function testItFailsForModuleNameTypeWhenInvalidCharactersGiven(string $invalidChar): void
    {
        $this->assertViolationIsRaised(new TypedRegex(['type' => 'module_name']), $invalidChar);
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForNameType(): array
    {
        return [
            ['0'], ['2'], ['<'], ['>'], ['?'], ['#'], ['%'], [','], [';'], ['+'], ['¤'], [':'], ['!'], ['='], ['#'],
            ['"'], ['$'], ['}'], ['{'], ['@'], ['|'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForCatalogNameType(): array
    {
        return [
            ['<'], ['>'], [';'], ['='], ['#'], ['{'], ['}'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForGenericNameType(): array
    {
        return [
            ['<'], ['>'], ['='], ['{'], ['}'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForCityNameType(): array
    {
        return [
            ['!'], ['>'], ['<'], [';'], ['?'], ['='], ['+'], ['@'], ['#'], ['"'], ['°'], ['{'], ['}'], ['_'],
            ['$'], ['%'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForAddressType(): array
    {
        return [
            ['!'], ['>'], ['<'], ['?'], ['='], ['+'], ['@'], ['{'], ['}'], ['_'], ['$'], ['%'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForPostCodeType(): array
    {
        return [
            ['<'], ['>'], ['?'], ['#'], ['%'], [','], [';'], ['+'], ['¤'], [':'], ['!'], ['='], ['#'],
            ['"'], ['$'], ['}'], ['{'], ['@'], ['|'], ['ž'], ['Š'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForPhoneNumberType(): array
    {
        return [
            ['<'], ['>'], ['?'], ['#'], ['%'], [','], [';'], ['¤'], [':'], ['!'], ['='], ['#'],
            ['"'], ['$'], ['}'], ['{'], ['@'], ['|'], ['ž'], ['Š'], ['r'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForMessageType(): array
    {
        return [
            ['<'], ['>'], ['{'], ['}'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForLanguageIsoCodeType(): array
    {
        return [
            ['a'], ['ž'], ['abcd'], ['2'], ['26'], ['ABCE'],
        ];
    }

    /**
     * @return string[][]
     */
    public function getInvalidCharactersForLanguageCodeType(): array
    {
        return [
            ['az-acc'], ['1'], ['12-22'], ['ži-as'],
        ];
    }

    /**
     * @return Generator
     */
    public function getInvalidCharactersForUpc(): Generator
    {
        yield ['1234567890013'];
        yield ['what'];
        yield ['!'];
        yield ['@'];
        yield ['$'];
        yield ['%s'];
        yield ['^'];
        yield ['&'];
        yield ['*'];
        yield ['('];
        yield [')'];
        yield ['-'];
        yield ['+'];
        yield ['='];
        yield ['{'];
        yield ['}'];
        yield ['['];
        yield ['['];
        yield ['<'];
        yield ['>'];
        yield ['?'];
        yield ['/'];
        yield ['\\'];
        yield ['\''];
        yield [';'];
        yield [':'];
        yield ['.'];
        yield [','];
    }

    /**
     * @return Generator
     */
    public function getInvalidCharactersForEan13(): Generator
    {
        yield ['10000000000014'];
        yield ['what'];
        yield ['!'];
        yield ['@'];
        yield ['$'];
        yield ['%s'];
        yield ['^'];
        yield ['&'];
        yield ['*'];
        yield ['('];
        yield [')'];
        yield ['-'];
        yield ['+'];
        yield ['='];
        yield ['{'];
        yield ['}'];
        yield ['['];
        yield ['['];
        yield ['<'];
        yield ['>'];
        yield ['?'];
        yield ['/'];
        yield ['\\'];
        yield ['\''];
        yield [';'];
        yield [':'];
        yield ['.'];
        yield [','];
    }

    /**
     * @return Generator
     */
    public function getInvalidCharactersForIsbn(): Generator
    {
        yield ['12345678901234567890123412345678901234-33'];
        yield ['what'];
        yield ['!'];
        yield ['@'];
        yield ['$'];
        yield ['%s'];
        yield ['^'];
        yield ['&'];
        yield ['*'];
        yield ['('];
        yield [')'];
        yield ['+'];
        yield ['='];
        yield ['{'];
        yield ['}'];
        yield ['['];
        yield ['['];
        yield ['<'];
        yield ['>'];
        yield ['?'];
        yield ['/'];
        yield ['\\'];
        yield ['\''];
        yield [';'];
        yield [':'];
        yield ['.'];
        yield [','];
    }

    /**
     * @return Generator
     */
    public function getInvalidCharactersForReference(): Generator
    {
        yield ['='];
        yield ['{'];
        yield ['}'];
        yield ['<'];
        yield ['>'];
        yield [';'];
    }

    /**
     * @return Generator
     */
    public function getInvalidCharactersForUrl(): Generator
    {
        yield ['!'];
        yield ['"'];
        yield ["'"];
        yield ['*'];
        yield ['§'];
        yield ['{'];
        yield ['['];
        yield [']'];
        yield ['}'];
        yield ['\\'];
        yield [';'];
    }

    /**
     * @return Generator
     */
    public function getInvalidCharactersForModuleName(): Generator
    {
        yield ['~'];
        yield ['ˇ'];
        yield ['"'];
        yield ['@'];
        yield ['#'];
        yield ['€'];
        yield ['$'];
        yield ['£'];
        yield ['%'];
        yield ['&'];
        yield ['§'];
        yield ['/'];
        yield ['('];
        yield [')'];
        yield ['='];
        yield ['?'];
        yield ['`'];
        yield ['\\'];
        yield ['}'];
        yield [']'];
        yield ['['];
        yield ['{'];
        yield ["'"];
        yield ['*'];
        yield ['.'];
        yield [','];
        yield [':'];
        yield [';'];
        yield ['<'];
        yield ['>'];
        yield ['|'];
    }

    /**
     * @return TypedRegexValidator
     */
    protected function createValidator(): TypedRegexValidator
    {
        return new TypedRegexValidator($this->createCharacterCleanersMock());
    }

    /**
     * @return MockObject|CharacterCleaner
     */
    private function createCharacterCleanersMock()
    {
        $toolsMock = $this->getMockBuilder(CharacterCleaner::class)
            ->disableOriginalConstructor()
            ->getMock();
        $toolsMock
            ->method('cleanNonUnicodeSupport')
            ->will($this->returnArgument(0));

        return $toolsMock;
    }

    /**
     * @param Constraint $constraint
     * @param string $invalidChar
     */
    private function assertViolationIsRaised(Constraint $constraint, string $invalidChar): void
    {
        $this->validator->validate($invalidChar, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('%s', '"' . $invalidChar . '"')
            ->assertRaised();
    }
}
