<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File\DataRow;

/**
 * Interface DataRowPresenterInterface describes a data row presenter.
 *
 * @deprecated since 9.3, part of the legacy DataRow reading layer — the import
 * engine reads records as plain string arrays
 */
interface DataRowPresenterInterface
{
    /**
     * Present a data row.
     *
     * @param DataRowInterface $dataRow
     *
     * @return array
     */
    public function present(DataRowInterface $dataRow);
}
