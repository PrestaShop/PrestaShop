<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Converter;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PrestaShop\PrestaShop\Core\File\Converter\FileConverterInterface;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ExcelToCsvFileConverter converts excel files to CSV.
 */
final class ExcelToCsvFileConverter implements FileConverterInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $excelDirectory;

    /**
     * @param Filesystem $filesystem
     * @param string $excelDirectory path to excel files directory
     */
    public function __construct(Filesystem $filesystem, $excelDirectory)
    {
        $this->filesystem = $filesystem;
        $this->excelDirectory = $excelDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(SplFileInfo $sourceFile)
    {
        if (preg_match('#(.*?)\.(csv)#is', $sourceFile->getFilename())) {
            // File is already in CSV
            return $sourceFile;
        }

        if (!$this->filesystem->exists($this->excelDirectory)) {
            $this->filesystem->mkdir($this->excelDirectory);
        }

        $destinationFilename = basename($sourceFile->getFilename(), $sourceFile->getExtension()) . '.csv';
        $destinationFilePath = $this->excelDirectory . $destinationFilename;

        if (!$this->filesystem->exists($destinationFilePath)) {
            $excelReader = IOFactory::createReaderForFile($sourceFile->getFilename());
            $excelReader->setReadDataOnly(true);
            $excelFile = $excelReader->load($sourceFile->getFilename() . $destinationFilename);
            /** @var Csv $csvWriter */
            $csvWriter = IOFactory::createWriter($excelFile, 'Csv');
            $csvWriter->setSheetIndex(0);
            $csvWriter->setDelimiter(';');
            $csvWriter->save($destinationFilePath);
        }

        return new SplFileInfo($destinationFilePath);
    }
}
