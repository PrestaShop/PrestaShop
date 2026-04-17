<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\File\Validator;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\File\Validator\VirtualProductFileValidator;
use PrestaShop\PrestaShop\Core\File\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\File\Exception\InvalidFileException;
use Tests\Resources\DummyFileUploader;

class VirtualProductFileValidatorTest extends TestCase
{
    /**
     * @dataProvider getInvalidPaths
     *
     * @param string $filePath
     */
    public function testItThrowsExceptionWhenProvidedPathIsNotLeadingToAFile(string $filePath): void
    {
        $this->expectException(FileNotFoundException::class);
        $validator = new VirtualProductFileValidator('1');
        $validator->validate($filePath);
    }

    public function testItThrowsExceptionWhenFileSizeIsTooBig(): void
    {
        $this->expectException(InvalidFileException::class);
        $this->expectExceptionCode(InvalidFileException::INVALID_SIZE);

        $validator = new VirtualProductFileValidator('0.000019');
        $validator->validate(DummyFileUploader::getDummyFilesPath() . 'app_icon.png');
    }

    /**
     * @return Generator
     */
    public function getInvalidPaths(): Generator
    {
        yield [__DIR__];
        yield [__DIR__ . '/notexistingfile.csv'];
    }
}
