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

namespace Tests\Unit\Core\Security;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Security\OpenSsl\OpenSSLInterface;
use PrestaShop\PrestaShop\Core\Security\PasswordGenerator;

class PasswordGeneratorTest extends TestCase
{
    /**
     * @var PasswordGenerator
     */
    private $passwordGenerator;

    /**
     * @var OpenSSLInterface&MockObject
     */
    private $cryptographyMock;

    protected function setUp(): void
    {
        $this->cryptographyMock = $this->createMock(OpenSSLInterface::class);
        $this->passwordGenerator = new PasswordGenerator($this->cryptographyMock);
    }

    /**
     * @dataProvider getInvalidLengthProvider
     */
    public function testGeneratePasswordReturnsFalseIfLengthIsInvalid(int $length): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->passwordGenerator->generatePassword($length);
    }

    public function getInvalidLengthProvider(): iterable
    {
        yield '0' => [0];
        yield '-10' => [-10];
    }

    /**
     * @dataProvider getTestData
     */
    public function testGeneratePasswordGeneratesRandomPassword(int $length, string $flag, string $expected): void
    {
        $this->cryptographyMock
            ->expects($this->once())
            ->method('getBytes')
            ->willReturn('randombytesrandombytesrandombytesrandombytes');

        self::assertSame($this->passwordGenerator->generatePassword($length, $flag), $expected);
    }

    public function getTestData(): iterable
    {
        yield '16-random' => [16, PasswordGenerator::PASSWORDGEN_FLAG_RANDOM, 'cmFuZG9tYnl0ZXNy'];
        yield '8-random' => [8, PasswordGenerator::PASSWORDGEN_FLAG_RANDOM, 'cmFuZG9t'];
        yield '3-random' => [3, PasswordGenerator::PASSWORDGEN_FLAG_RANDOM, 'cmF'];
        yield '8-alpha' => [8, PasswordGenerator::PASSWORDGEN_FLAG_ALPHANUMERIC, 'S3rUJ6hg'];
        yield '4-alpha' => [4, PasswordGenerator::PASSWORDGEN_FLAG_ALPHANUMERIC, 'S3rU'];
        yield '2-alpha' => [2, PasswordGenerator::PASSWORDGEN_FLAG_ALPHANUMERIC, 'S3'];
        yield '16-nonumeric' => [16, PasswordGenerator::PASSWORDGEN_FLAG_NO_NUMERIC, 'KDJFMRLCOLWGZFBI'];
        yield '8-nonumeric' => [8, PasswordGenerator::PASSWORDGEN_FLAG_NO_NUMERIC, 'KDJFMRLC'];
        yield '4-nonumeric' => [4, PasswordGenerator::PASSWORDGEN_FLAG_NO_NUMERIC, 'KDJF'];
        yield '16-numeric' => [16, PasswordGenerator::PASSWORDGEN_FLAG_NUMERIC, '4111219067263334'];
        yield '8-numeric' => [8, PasswordGenerator::PASSWORDGEN_FLAG_NUMERIC, '41112190'];
        yield '4-numeric' => [4, PasswordGenerator::PASSWORDGEN_FLAG_NUMERIC, '4111'];
    }

    public function testWithDefaultArguments(): void
    {
        $this->cryptographyMock
            ->expects($this->once())
            ->method('getBytes')
            ->willReturn('randombytes');

        self::assertSame($this->passwordGenerator->generatePassword(), 'S3rUJ6hg');
    }
}
