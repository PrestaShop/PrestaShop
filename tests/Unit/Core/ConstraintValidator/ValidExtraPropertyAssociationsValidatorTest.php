<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ValidExtraPropertyAssociations;
use PrestaShop\PrestaShop\Core\ConstraintValidator\ValidExtraPropertyAssociationsValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ValidExtraPropertyAssociationsValidatorTest extends ConstraintValidatorTestCase
{
    public function testItDetectsIncorrectConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate('product', new NotBlank());
    }

    public function testItDetectsIncorrectValueType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(['product'], new ValidExtraPropertyAssociations(['type' => ValidExtraPropertyAssociations::TYPE_FORM]));
    }

    public function testItRejectsUnknownAssociationType(): void
    {
        $this->expectException(ConstraintDefinitionException::class);
        $this->validator->validate('product', new ValidExtraPropertyAssociations(['type' => 'unknown']));
    }

    /**
     * @dataProvider getEmptyValues
     */
    public function testEmptyValuesAreValid(?string $value): void
    {
        $this->validator->validate($value, new ValidExtraPropertyAssociations(['type' => ValidExtraPropertyAssociations::TYPE_FORM]));
        $this->assertNoViolation();
    }

    public function getEmptyValues(): iterable
    {
        yield 'null' => [null];
        yield 'empty string' => [''];
        yield 'whitespace only' => ["  \n  \n"];
    }

    /**
     * @dataProvider getValidValues
     */
    public function testValidValues(string $type, string $value): void
    {
        $this->validator->validate($value, new ValidExtraPropertyAssociations(['type' => $type]));
        $this->assertNoViolation();
    }

    public function getValidValues(): iterable
    {
        yield 'single form id' => [ValidExtraPropertyAssociations::TYPE_FORM, 'product'];
        yield 'form entries with path and mode' => [ValidExtraPropertyAssociations::TYPE_FORM, "product:options.suppliers:before\ncategory:seo"];
        yield 'unknown form id is NOT flagged (manual override is a feature)' => [ValidExtraPropertyAssociations::TYPE_FORM, 'form_nobody_has_ever_heard_of'];
        yield 'blank lines between entries' => [ValidExtraPropertyAssociations::TYPE_FORM, "product\n\ncategory"];
        yield 'entries with surrounding whitespace' => [ValidExtraPropertyAssociations::TYPE_FORM, "  product  \r\n  category  "];
        yield 'grid entries' => [ValidExtraPropertyAssociations::TYPE_GRID, "product:reference:after\ncategory"];
        yield 'api entries' => [ValidExtraPropertyAssociations::TYPE_API, "/products\n/products/{productId}:GET,PATCH"];
        yield 'api methods are case-insensitive' => [ValidExtraPropertyAssociations::TYPE_API, '/products:get,patch'];
        yield 'api duplicates are allowed (no unique-path rule in the VO)' => [ValidExtraPropertyAssociations::TYPE_API, "/products:GET\n/products:PATCH"];
    }

    /**
     * @dataProvider getInvalidValues
     */
    public function testInvalidValues(string $type, string $value, string $expectedLine, string $expectedError): void
    {
        $constraint = new ValidExtraPropertyAssociations(['type' => $type]);
        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('%line%', $expectedLine)
            ->setParameter('%error%', $expectedError)
            ->assertRaised();
    }

    public function getInvalidValues(): iterable
    {
        yield 'form entry without formId' => [
            ValidExtraPropertyAssociations::TYPE_FORM,
            ':options.suppliers',
            '1',
            'invalid associatedForms entry ":options.suppliers" — formId must not be empty.',
        ];
        yield 'invalid form entry on second line' => [
            ValidExtraPropertyAssociations::TYPE_FORM,
            "product\n:options",
            '2',
            'invalid associatedForms entry ":options" — formId must not be empty.',
        ];
        yield 'blank lines still count towards line numbers' => [
            ValidExtraPropertyAssociations::TYPE_FORM,
            "product\n\n:options",
            '3',
            'invalid associatedForms entry ":options" — formId must not be empty.',
        ];
        yield 'duplicate formId' => [
            ValidExtraPropertyAssociations::TYPE_FORM,
            "product:options\nproduct:seo",
            '2',
            'duplicate formId "product" — each form may only be referenced once.',
        ];
        yield 'grid entry without gridId' => [
            ValidExtraPropertyAssociations::TYPE_GRID,
            ':reference',
            '1',
            'invalid associatedGrids entry ":reference" — gridId must not be empty.',
        ];
        yield 'duplicate gridId' => [
            ValidExtraPropertyAssociations::TYPE_GRID,
            "product\nproduct:reference",
            '2',
            'duplicate gridId "product" — each grid may only be referenced once.',
        ];
        yield 'api entry without path' => [
            ValidExtraPropertyAssociations::TYPE_API,
            ':GET',
            '1',
            'invalid associatedApis entry ":GET" — URI path must not be empty.',
        ];
        yield 'api entry with unknown method' => [
            ValidExtraPropertyAssociations::TYPE_API,
            '/products:GET,FETCH',
            '1',
            'invalid HTTP method "FETCH" in associatedApis entry "/products:GET,FETCH" (allowed: GET, POST, PUT, PATCH, DELETE).',
        ];
    }

    public function testEveryInvalidLineIsReported(): void
    {
        $constraint = new ValidExtraPropertyAssociations(['type' => ValidExtraPropertyAssociations::TYPE_FORM]);
        $this->validator->validate(":one\nproduct\n:two", $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('%line%', '1')
            ->setParameter('%error%', 'invalid associatedForms entry ":one" — formId must not be empty.')
            ->buildNextViolation($constraint->message)
            ->setParameter('%line%', '3')
            ->setParameter('%error%', 'invalid associatedForms entry ":two" — formId must not be empty.')
            ->assertRaised();
    }

    protected function createValidator(): ValidExtraPropertyAssociationsValidator
    {
        return new ValidExtraPropertyAssociationsValidator();
    }
}
