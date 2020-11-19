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

namespace Tests\Unit\Tests\Integration\Behaviur\Features\Context\Util;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\LocalizedArrayParser;

class LocalizedArrayParserTest extends TestCase
{
    /**
     * @var LocalizedArrayParser
     */
    private $localizedArrayParser;

    protected function setUp()
    {
        $this->localizedArrayParser = new LocalizedArrayParser([
            'en-US' => 1,
            'fr-FR' => 2,
            'lt-LT' => 3,
        ]);
        parent::setUp();
    }

    /**
     * @dataProvider getValidDataForParsingStringToArray
     *
     * @param string $string
     * @param array $expectedResult
     */
    public function testItParsesStringToArray(string $string, array $expectedResult): void
    {
        $result = $this->localizedArrayParser->parseStringToArray($string);

        Assert::assertEquals($expectedResult, $result);
    }

    /**
     * @return Generator
     */
    public function getValidDataForParsingStringToArray(): Generator
    {
        yield ['en-US:hello;fr-FR:goodbye;lt-LT:labas', [1 => 'hello', 2 => 'goodbye', 3 => 'labas']];
        yield ['en-US:hello;fr-FR:goodbye', [1 => 'hello', 2 => 'goodbye']];
        yield ['en-US:hello world', [1 => 'hello world']];
        yield ['fr-FR:hello world 55', [2 => 'hello world 55']];
        yield ['fr-FR:hello world 55;en-US:', [2 => 'hello world 55', 1 => '']];
        yield ['fr-FR:hello world 55;en-US:', [2 => 'hello world 55', 1 => '']];
    }

    /**
     * @dataProvider getInvalidDataForParsingStringToArray
     *
     * @param string $string
     * @param string $exception
     */
    public function testItFailsToParseStringToArray(string $string, string $exception)
    {
        $this->expectException($exception);
        $this->localizedArrayParser->parseStringToArray($string);
    }

    /**
     * @return Generator
     */
    public function getInvalidDataForParsingStringToArray(): Generator
    {
        yield ['en-UShello;fr-FR:goodbye;lt-LT:labas', RuntimeException::class];
        yield ['en-US:hello;', RuntimeException::class];
        yield ['hello world', RuntimeException::class];
    }
}
