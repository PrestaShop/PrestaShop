<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File\DataRow;

use PrestaShop\PrestaShop\Core\Import\File\DataCell\DataCellInterface;

/**
 * Class DataRowPresenter defines a data row presenter.
 */
final class DataRowPresenter implements DataRowPresenterInterface
{
    /**
     * {@inheritdoc}
     */
    public function present(DataRowInterface $dataRow)
    {
        $presentedRow = [];

        /** @var DataCellInterface $dataCell */
        foreach ($dataRow as $dataCell) {
            $presentedRow[] = [
                'value' => $dataCell->getValue(),
            ];
        }

        return $presentedRow;
    }
}
