<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File\DataRow;

use PrestaShop\PrestaShop\Core\Import\File\DataCell\EmptyDataCell;

/**
 * Class DataRowCollectionPresenter presents a data row collection.
 */
final class DataRowCollectionPresenter implements DataRowCollectionPresenterInterface
{
    /**
     * @var DataRowPresenterInterface
     */
    private $dataRowPresenter;

    /**
     * @param DataRowPresenterInterface $dataRowPresenter
     */
    public function __construct(DataRowPresenterInterface $dataRowPresenter)
    {
        $this->dataRowPresenter = $dataRowPresenter;
    }

    /**
     * {@inheritdoc}
     */
    public function present(DataRowCollectionInterface $dataRowCollection)
    {
        $presentedCollection = [
            'rows' => [],
            'row_size' => $dataRowCollection->getLargestRowSize(),
        ];

        /** @var DataRowInterface $dataRow */
        foreach ($dataRowCollection as $dataRow) {
            $this->normalizeDataRow($dataRow, $dataRowCollection->getLargestRowSize());
            $presentedCollection['rows'][] = $this->dataRowPresenter->present($dataRow);
        }

        return $presentedCollection;
    }

    /**
     * Normalize the data row by adding empty data cells until the expected row size is reached.
     * This allows all rows to be equal in size.
     *
     * @param DataRowInterface $dataRow
     * @param int $expectedRowSize number of columns this row will reach
     */
    private function normalizeDataRow(DataRowInterface $dataRow, $expectedRowSize)
    {
        while (count($dataRow) < $expectedRowSize) {
            $dataRow->addCell(new EmptyDataCell());
        }
    }
}
