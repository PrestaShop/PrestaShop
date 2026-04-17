<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File\DataRow;

/**
 * Interface DataRowCollectionPresenterInterface describes a data row collection presenter.
 */
interface DataRowCollectionPresenterInterface
{
    /**
     * Present a data row collection into array.
     *
     * @param DataRowCollectionInterface $dataRowCollection
     *
     * @return array
     */
    public function present(DataRowCollectionInterface $dataRowCollection);
}
