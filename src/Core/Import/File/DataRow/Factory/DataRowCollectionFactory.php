<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File\DataRow\Factory;

use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowCollection;
use PrestaShop\PrestaShop\Core\Import\File\FileReaderInterface;
use SplFileInfo;

/**
 * Class DataRowCollectionFactory defines a data row collection factory.
 */
final class DataRowCollectionFactory implements DataRowCollectionFactoryInterface
{
    /**
     * @var FileReaderInterface
     */
    private $fileReader;

    /**
     * @param FileReaderInterface $fileReader
     */
    public function __construct(FileReaderInterface $fileReader)
    {
        $this->fileReader = $fileReader;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFromFile(SplFileInfo $file, $maxRowsInCollection = null)
    {
        $dataRowCollection = new DataRowCollection();
        $rowIndex = 0;

        foreach ($this->fileReader->read($file) as $dataRow) {
            if (null !== $maxRowsInCollection && $rowIndex >= $maxRowsInCollection) {
                break;
            }

            $dataRowCollection->addDataRow($dataRow);
            ++$rowIndex;
        }

        return $dataRowCollection;
    }
}
