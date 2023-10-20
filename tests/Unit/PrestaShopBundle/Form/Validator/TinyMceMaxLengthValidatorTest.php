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

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Validator;

use Exception;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShopBundle\Form\Validator\Constraints\TinyMceMaxLength;
use PrestaShopBundle\Form\Validator\Constraints\TinyMceMaxLengthValidator;
use stdClass;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class TinyMceMaxLengthValidatorTest extends ConstraintValidatorTestCase
{
    private const MAX_LENGTH = 42;

    protected function createValidator(): TinyMceMaxLengthValidator
    {
        return new TinyMceMaxLengthValidator(
            new Validate(),
            $this->createMock(TranslatorInterface::class)
        );
    }

    /**
     * @param mixed $max
     * @param string|null $message
     *
     * @return TinyMceMaxLength
     */
    private function getConstraintInstance($max, ?string $message = null): TinyMceMaxLength
    {
        return new TinyMceMaxLength([
            'max' => $max,
            'message' => $message,
        ]);
    }

    /**
     * @param Exception $expectedException
     * @param mixed $tinyMceMaxLength
     *
     * @dataProvider exceptionsInvalidTinyMceMaxLengthProvider
     */
    public function testExceptionsInvalidTinyMceMaxLength(Exception $expectedException, $tinyMceMaxLength): void
    {
        $expectedExceptionClassName = get_class($expectedException);
        $this->expectException($expectedExceptionClassName);
        $this->validator->validate(
            'fakeTinyMceText',
            $this->getConstraintInstance($tinyMceMaxLength)
        );
    }

    public function exceptionsInvalidTinyMceMaxLengthProvider(): array
    {
        return [
            [new InvalidArgumentException(), new stdClass()],
            [new InvalidArgumentException(), 'INVALID_MAX_LENGTH'],
            [new InvalidArgumentException(), false],
            [new InvalidArgumentException(), true],
            [new InvalidArgumentException(), -6666],
            [new InvalidArgumentException(), ''],
            [new InvalidArgumentException(), ' '],
            [new MissingOptionsException('fakeExceptionMessage', []), null],
        ];
    }

    /**
     * @param string $tinyMceText
     * @param int $tinyMceMaxLength
     *
     * @dataProvider validTinyMceMaxLengthProvider
     */
    public function testValidTinyMceMaxLength(string $tinyMceText, int $tinyMceMaxLength): void
    {
        $constraint = $this->getConstraintInstance($tinyMceMaxLength, 'fakeMessage');

        $this->validator->validate($tinyMceText, $constraint);

        $this->assertNoViolation();
    }

    public function validTinyMceMaxLengthProvider(): array
    {
        return [
            [$this->generateRandomTinyMceText(5), 10],
            [$this->generateRandomTinyMceText(10), 10],
            [$this->generateRandomTinyMceText(50), 51],
            [$this->generateRandomTinyMceText(0), 0],
            [$this->generateRandomTinyMceText(154), 200],
            ['Valid text', self::MAX_LENGTH],
            ['Valid text too long only because of HTML', self::MAX_LENGTH],
            ['<p>Valid text too long only because of HTML</p>', self::MAX_LENGTH],
            [
                '<p>White Hot Ceramic Mug</p>
<p>White Hot Ceramic Mug</p>',
                self::MAX_LENGTH,
            ],
            [
                '<p>White Hot Ceramic Mug</p>
<p></p>
<p>White Hot Ceramic Mug</p>
<p></p>',
                self::MAX_LENGTH,
            ],
            [
                'White Hot Ceramic Mug

White Hot Ceramic Mug',
                self::MAX_LENGTH,
            ],
            [
                '<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>
<p>testtesttesttesttesttesttesttesttest</p>
<p></p>',
                800,
            ],
        ];
    }

    /**
     * @param string $code error code
     * @param string $tinyMceText
     * @param int $tinyMceMaxLength
     *
     * @dataProvider invalidTinyMceMaxLengthProvider
     */
    public function testInvalidTinyMceMaxLength(string $code, string $tinyMceText, int $tinyMceMaxLength): void
    {
        $fakeTestMessage = 'fakeMessage';
        $constraint = $this->getConstraintInstance($tinyMceMaxLength, $fakeTestMessage);

        $this->validator->validate($tinyMceText, $constraint);

        $this->buildViolation($fakeTestMessage)
            ->setParameter('{{ value }}', '"' . $tinyMceText . '"')
            ->setCode($code)
            ->assertRaised()
        ;
    }

    public function invalidTinyMceMaxLengthProvider(): array
    {
        return [
            [TinyMceMaxLength::TOO_LONG_ERROR_CODE, $this->generateRandomTinyMceText(10), 5],
            [TinyMceMaxLength::TOO_LONG_ERROR_CODE, $this->generateRandomTinyMceText(51), 50],
            [TinyMceMaxLength::TOO_LONG_ERROR_CODE, $this->generateRandomTinyMceText(200), 154],
            [TinyMceMaxLength::TOO_LONG_ERROR_CODE, $this->generateRandomTinyMceText(1), 0],
            [TinyMceMaxLength::TOO_LONG_ERROR_CODE, $this->generateRandomTinyMceText(50), 43],
            [TinyMceMaxLength::TOO_LONG_ERROR_CODE, 'Invalid text that is a too long even without HTML', self::MAX_LENGTH],
            [TinyMceMaxLength::TOO_LONG_ERROR_CODE, '<p>Invalid text that is a too long even without HTML</p>', self::MAX_LENGTH],
            [
                TinyMceMaxLength::TOO_LONG_ERROR_CODE,
                '<p>White Ceramic Mug. 325ml</p>
<p>White Ceramic Mug. 325ml</p>',
                self::MAX_LENGTH,
            ],
        ];
    }

    /**
     * @param int|null $length
     *
     * @return string
     */
    protected function generateRandomTinyMceText(?int $length = null): string
    {
        $fakeTextFull = 'Contrary to popular belief, Lorem Ipsum is not simply random text.
            It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock,
            a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, :
            from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source.
            Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero,
            written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum,
            "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.

            The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested.
            Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form,
            accompanied by English versions from the 1914 translation by H. Rackham.
        ';

        if (null === $length) {
            return $fakeTextFull;
        }

        return substr($fakeTextFull, 0, $length);
    }
}
