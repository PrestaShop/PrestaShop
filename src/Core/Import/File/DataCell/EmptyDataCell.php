<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File\DataCell;

/**
 * Class EmptyDataCell defines an empty data cell.
 *
 * @deprecated since 9.3, part of the legacy DataRow reading layer — the import
 * engine reads records as plain string arrays
 */
final class EmptyDataCell implements DataCellInterface
{
    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return '';
    }
}
