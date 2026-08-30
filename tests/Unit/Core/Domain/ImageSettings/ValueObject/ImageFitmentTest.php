<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\ImageSettings\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\InvalidImageFitmentException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageFitment;

class ImageFitmentTest extends TestCase
{
    /**
     * @dataProvider getValidImageFitments
     */
    public function testValidImageFitment(string $imageFitment): void
    {
        ImageFitment::assertIsValid($imageFitment);

        self::addToAssertionCount(1);
    }

    /**
     * @return iterable<array{string}>
     */
    public function getValidImageFitments(): iterable
    {
        yield [ImageFitment::FIT];
        yield [ImageFitment::CROP];
        yield [ImageFitment::BOUND];
    }

    public function testInvalidImageFitment(): void
    {
        $this->expectException(InvalidImageFitmentException::class);

        ImageFitment::assertIsValid('invalid');
    }
}
