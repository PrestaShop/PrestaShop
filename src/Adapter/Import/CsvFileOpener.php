<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Import;

use PrestaShop\PrestaShop\Core\File\Converter\FileConverterInterface;
use PrestaShop\PrestaShop\Core\Import\Exception\UnreadableFileException;
use PrestaShop\PrestaShop\Core\Import\File\FileOpenerInterface;
use SplFileInfo;

/**
 * Class CsvFileOpener is responsible for opening the CSV import file.
 */
final class CsvFileOpener implements FileOpenerInterface
{
    /**
     * @var FileConverterInterface
     */
    private $excelToCsvConverter;

    /**
     * @param FileConverterInterface $excelToCsvConverter
     */
    public function __construct(FileConverterInterface $excelToCsvConverter)
    {
        $this->excelToCsvConverter = $excelToCsvConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function open(SplFileInfo $file)
    {
        $importFile = $this->excelToCsvConverter->convert($file);
        $filePath = $importFile->getPathname();
        $isReadableFile = is_file($filePath) && is_readable($filePath);

        if (!$isReadableFile || !($handle = fopen($filePath, 'r'))) {
            throw new UnreadableFileException();
        }

        $this->rewindBomAware($handle);

        return $handle;
    }

    /**
     * Rewind the file handle, skipping BOM signature.
     *
     * @param resource $handle
     */
    private function rewindBomAware($handle)
    {
        if (!is_resource($handle)) {
            return;
        }

        rewind($handle);

        if (($bom = fread($handle, 3)) != "\xEF\xBB\xBF") {
            rewind($handle);
        }
    }
}
