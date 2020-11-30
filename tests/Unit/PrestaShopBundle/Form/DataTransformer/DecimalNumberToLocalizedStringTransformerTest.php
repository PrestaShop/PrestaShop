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

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShopBundle\Form\DataTransformer\DecimalNumberToLocalizedStringTransformer;

class DecimalNumberToLocalizedStringTransformerTest extends TestCase
{
    /**
     * @var string
     */
    private static $defaultLocale;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$defaultLocale = \Locale::getDefault();
        \Locale::setDefault('en');
    }

    /**
     * {@inheritdoc}
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        \Locale::setDefault(static::$defaultLocale);
    }

    /**
     * @dataProvider getTransformations
     *
     * @param int|null $scale
     * @param string $emptyData
     * @param int $roundingMode
     * @param DecimalNumber|null $number
     * @param string $expectedOutput
     */
    public function testTransform(?int $scale, string $emptyData, int $roundingMode, ?DecimalNumber $number, string $expectedOutput)
    {
        $transformer = new DecimalNumberToLocalizedStringTransformer($scale, false, $roundingMode, $emptyData);
        $transformedOutput = $transformer->transform($number);
        $this->assertSame($expectedOutput, $transformedOutput);
    }

    public function getTransformations()
    {
        yield [
            2,
            '',
            DecimalNumberToLocalizedStringTransformer::ROUND_HALF_UP,
            new DecimalNumber('0.0'),
            '0,00',
        ];

        yield [
            3,
            '1',
            DecimalNumberToLocalizedStringTransformer::ROUND_HALF_UP,
            new DecimalNumber('0.0'),
            '0,000',
        ];

        yield [
            3,
            '1',
            DecimalNumberToLocalizedStringTransformer::ROUND_HALF_UP,
            null,
            '1,000',
        ];

        yield [
            3,
            '1.12369',
            DecimalNumberToLocalizedStringTransformer::ROUND_HALF_UP,
            null,
            '1,124',
        ];

        yield [
            3,
            '1.12357',
            DecimalNumberToLocalizedStringTransformer::ROUND_HALF_UP,
            null,
            '1,124',
        ];

        yield [
            3,
            '1.12357',
            DecimalNumberToLocalizedStringTransformer::ROUND_HALF_DOWN,
            null,
            '1,123',
        ];

        yield [
            3,
            '1.12357',
            DecimalNumberToLocalizedStringTransformer::ROUND_UP,
            null,
            '1,124',
        ];

        yield [
            3,
            '1.12357',
            DecimalNumberToLocalizedStringTransformer::ROUND_DOWN,
            null,
            '1,123',
        ];

        yield [
            3,
            '42.89',
            DecimalNumberToLocalizedStringTransformer::ROUND_FLOOR,
            new DecimalNumber('1.12365'),
            '1,123',
        ];

        yield [
            3,
            '2.0',
            DecimalNumberToLocalizedStringTransformer::ROUND_CEILING,
            new DecimalNumber('1.12332'),
            '1,124',
        ];
    }

    /**
     * @dataProvider getReverseTransformations
     *
     * @param int|null $scale
     * @param string $emptyData
     * @param int $roundingMode
     * @param string|float|int $input
     * @param DecimalNumber|null $expectedNumber
     */
    public function testReverseTransform(?int $scale, string $emptyData, int $roundingMode, $input, ?DecimalNumber $expectedNumber)
    {
        $transformer = new DecimalNumberToLocalizedStringTransformer($scale, false, $roundingMode, $emptyData);
        $reversedNumber = $transformer->reverseTransform($input);
        $this->assertInstanceOf(DecimalNumber::class, $reversedNumber);
        $this->assertTrue(
            $expectedNumber->equals($reversedNumber),
            sprintf(
                'Invalid value expected %s but actually got %s',
                (string) $expectedNumber,
                (string) $reversedNumber
            )
        );
    }

    public function getReverseTransformations()
    {
        yield [
            3,
            '',
            DecimalNumberToLocalizedStringTransformer::ROUND_UP,
            null,
            new DecimalNumber('0'),
        ];

        yield [
            3,
            '1.2',
            DecimalNumberToLocalizedStringTransformer::ROUND_UP,
            null,
            new DecimalNumber('1.20'),
        ];

        yield [
            3,
            '1.2',
            DecimalNumberToLocalizedStringTransformer::ROUND_UP,
            '',
            new DecimalNumber('1.20'),
        ];

        yield [
            3,
            '1.2356',
            DecimalNumberToLocalizedStringTransformer::ROUND_HALF_UP,
            '',
            new DecimalNumber('1.236'),
        ];

        yield [
            3,
            '0.0',
            DecimalNumberToLocalizedStringTransformer::ROUND_HALF_DOWN,
            '1.2354',
            new DecimalNumber('1.235'),
        ];

        yield [
            3,
            '0.0',
            DecimalNumberToLocalizedStringTransformer::ROUND_CEILING,
            '1.2354',
            new DecimalNumber('1.236'),
        ];

        yield [
            3,
            '0.0',
            DecimalNumberToLocalizedStringTransformer::ROUND_FLOOR,
            '1.2358',
            new DecimalNumber('1.235'),
        ];
    }
}
