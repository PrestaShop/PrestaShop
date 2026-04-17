<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\File\Validator;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\File\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\File\Exception\InvalidFileException;

/**
 * Validates virtual product file (the actual file itself & not the domain model)
 */
class VirtualProductFileValidator
{
    /**
     * 1kb squared equals 1MB. (1kb = 1024 bytes)
     * 1024^2 = 1048576
     */
    private const MEGABYTE_TO_BYTE_MULTIPLIER = '1048576';

    /**
     * @var DecimalNumber
     */
    private $maxFileSizeInMegabytes;

    /**
     * @param string $maxFileSizeInMegabytes
     */
    public function __construct(
        string $maxFileSizeInMegabytes
    ) {
        $this->maxFileSizeInMegabytes = new DecimalNumber($maxFileSizeInMegabytes);
    }

    /**
     * @param string $filePath
     *
     * @throws InvalidFileException
     */
    public function validate(string $filePath): void
    {
        $this->assertIsFile($filePath);

        $megabyteToByteMultiplier = new DecimalNumber(self::MEGABYTE_TO_BYTE_MULTIPLIER);
        $maxFileSizeInBytes = $this->maxFileSizeInMegabytes->times($megabyteToByteMultiplier);
        $actualSizeInBytes = new DecimalNumber((string) filesize($filePath));

        if ($maxFileSizeInBytes->isLowerThan($actualSizeInBytes)) {
            throw new InvalidFileException(
                sprintf(
                    'Maximum allowed file size "%s" exceeded. Given "%s"',
                    (string) $maxFileSizeInBytes,
                    (string) $actualSizeInBytes
                ),
                InvalidFileException::INVALID_SIZE
            );
        }
    }

    /**
     * @param string $filePath
     *
     * @throws InvalidFileException
     */
    private function assertIsFile(string $filePath): void
    {
        if (!is_file($filePath)) {
            throw new FileNotFoundException(sprintf('"%s" is not a file', $filePath));
        }
    }
}
