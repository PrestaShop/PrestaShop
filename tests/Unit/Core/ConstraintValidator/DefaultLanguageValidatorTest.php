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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\DefaultLanguageValidator;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * Class DefaultLanguageValidatorTest
 */
class DefaultLanguageValidatorTest extends ConstraintValidatorTestCase
{
    private const DEFAULT_LANG_ID = 1;
    private const DEFAULT_LANG_LOCALE = 'en-US';

    private LanguageContext $defaultLanguageContext;

    public function setUp(): void
    {
        $this->defaultLanguageContext = new LanguageContext(
            self::DEFAULT_LANG_ID,
            'English',
            'en',
            self::DEFAULT_LANG_LOCALE,
            'en-us',
            false,
            'm/d/Y',
            'm/d/Y H:i:s',
            $this->createMock(LocaleInterface::class)
        );
        parent::setUp();
    }

    public function testItDetectsIncorrectConstraintType()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate([], new NotBlank());
    }

    /**
     * @dataProvider getIncorrectTypes
     */
    public function testItDetectsIncorrectValueType($incorrectType, bool $expectViolation = false)
    {
        $constraint = new DefaultLanguage();

        if ($expectViolation) {
            // For null values, we expect a violation instead of an exception
            $this->validator->validate($incorrectType, $constraint);
            $this->buildViolation($constraint->message)
                ->setParameter('%field_name%', '')
                ->assertRaised()
            ;
        } else {
            // For other incorrect types (string, boolean), we expect an exception
            $this->expectException(UnexpectedTypeException::class);
            $this->validator->validate($incorrectType, $constraint);
        }
    }

    public static function getIncorrectTypes(): iterable
    {
        yield 'string value' => [
            '',
            false,
        ];

        yield 'boolean value' => [
            false,
            false,
        ];

        // Not allowed unless allowNull is set (see in getValidValues)
        // Null values now raise a violation instead of throwing an exception
        yield 'null value' => [
            null,
            true,
        ];
    }

    /**
     * @dataProvider getValidValues
     */
    public function testValidValues(?array $validValue, DefaultLanguage $constraint): void
    {
        $this->validator->validate($validValue, $constraint);
        $this->assertNoViolation();
    }

    public static function getValidValues(): iterable
    {
        $constraintNullNotAllowed = new DefaultLanguage();
        yield 'multilang array but with default language by ID, null not allowed' => [
            [
                self::DEFAULT_LANG_ID => 'some kind of value',
            ],
            $constraintNullNotAllowed,
        ];

        yield 'multilang array but with default language by ID and other language empty, null not allowed' => [
            [
                self::DEFAULT_LANG_ID => 'some kind of value',
                2 => '',
            ],
            $constraintNullNotAllowed,
        ];

        yield 'multilang array but with default language by locale, null not allowed' => [
            [
                self::DEFAULT_LANG_LOCALE => 'some kind of value',
            ],
            $constraintNullNotAllowed,
        ];

        yield 'multilang array but with default language by locale and other language empty, null not allowed' => [
            [
                self::DEFAULT_LANG_LOCALE => 'some kind of value',
                2 => '',
            ],
            $constraintNullNotAllowed,
        ];

        $constraintAllowNull = new DefaultLanguage(allowNull: true);
        yield 'constraint allows null, default language is not in the array, acceptable' => [
            [
                2 => 'test3',
            ],
            $constraintAllowNull,
        ];

        yield 'constraint allows null, value is null, acceptable' => [
            null,
            $constraintAllowNull,
        ];

        yield 'constraint allows null, default language by ID, is explicitly set as null' => [
            [
                self::DEFAULT_LANG_ID => null,
            ],
            $constraintAllowNull,
        ];

        yield 'constraint allows null, default language by locale, is explicitly set as null' => [
            [
                self::DEFAULT_LANG_LOCALE => null,
            ],
            $constraintAllowNull,
        ];
    }

    /**
     * @dataProvider getIncorrectValues
     */
    public function testItRaisesViolationWhenDefaultLanguageIsNotPreserved(array $valueWithMissingDefaultLanguage, DefaultLanguage $constraint): void
    {
        $this->validator->validate($valueWithMissingDefaultLanguage, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('%field_name%', '')
            ->assertRaised()
        ;
    }

    public static function getIncorrectValues(): iterable
    {
        $constraintNullNotAllowed = new DefaultLanguage();
        yield 'multilang array but without the default language, null not allowed' => [
            [
                0 => 'test1',
                2 => 'test1',
            ],
            $constraintNullNotAllowed,
        ];

        yield 'multilang array but with default language by ID null, null not allowed' => [
            [
                0 => 'test2',
                self::DEFAULT_LANG_ID => null,
            ],
            $constraintNullNotAllowed,
        ];

        yield 'multilang array but with default language by ID empty string, null not allowed' => [
            [
                0 => 'test3',
                self::DEFAULT_LANG_ID => '',
            ],
            $constraintNullNotAllowed,
        ];

        yield 'multilang array but with default language by locale null, null not allowed' => [
            [
                0 => 'test2',
                self::DEFAULT_LANG_LOCALE => null,
            ],
            $constraintNullNotAllowed,
        ];

        yield 'multilang array but with default language by locale empty string, null not allowed' => [
            [
                0 => 'test3',
                self::DEFAULT_LANG_LOCALE => '',
            ],
            $constraintNullNotAllowed,
        ];

        $constraintAllowNull = new DefaultLanguage(allowNull: true);
        yield 'constraint allows null, default language by ID, is explicitly set as empty string' => [
            [
                self::DEFAULT_LANG_ID => '',
            ],
            $constraintAllowNull,
        ];

        yield 'constraint allows null, default language by locale, is explicitly set as empty string' => [
            [
                self::DEFAULT_LANG_LOCALE => '',
            ],
            $constraintAllowNull,
        ];
    }

    protected function createValidator()
    {
        return new DefaultLanguageValidator($this->defaultLanguageContext);
    }
}
