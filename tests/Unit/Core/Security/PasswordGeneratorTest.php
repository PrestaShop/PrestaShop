<?php

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
