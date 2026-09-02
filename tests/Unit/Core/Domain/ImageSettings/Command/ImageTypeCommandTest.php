<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\ImageSettings\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\AddImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\EditImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\InvalidImageFitmentException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageFitment;

class ImageTypeCommandTest extends TestCase
{
    public function testAddImageTypeCommandValidatesImageFitment(): void
    {
        $this->expectException(InvalidImageFitmentException::class);

        new AddImageTypeCommand(
            'test',
            100,
            100,
            true,
            false,
            false,
            false,
            false,
            // @phpstan-ignore-next-line argument.type (intentionally invalid value to test validation)
            'invalid'
        );
    }

    public function testEditImageTypeCommandValidatesImageFitment(): void
    {
        $command = new EditImageTypeCommand(1);

        $this->expectException(InvalidImageFitmentException::class);

        // @phpstan-ignore-next-line argument.type (intentionally invalid value to test validation)
        $command->setImageFitment('invalid');
    }

    public function testImageFitmentIsStoredInCommands(): void
    {
        $addCommand = new AddImageTypeCommand(
            'test',
            100,
            100,
            true,
            false,
            false,
            false,
            false,
            ImageFitment::CROP
        );
        $editCommand = new EditImageTypeCommand(1);
        $editCommand->setImageFitment(ImageFitment::BOUND);

        self::assertSame(ImageFitment::CROP, $addCommand->getImageFitment());
        self::assertSame(ImageFitment::BOUND, $editCommand->getImageFitment());
    }
}
