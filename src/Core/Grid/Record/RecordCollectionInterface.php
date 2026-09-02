<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Record;

use Countable;
use Iterator;

/**
 * Interface RecordCollectionInterface defines interface for raw rows wrapper.
 */
interface RecordCollectionInterface extends Countable, Iterator
{
    /**
     * Get raw rows.
     *
     * @return array
     */
    public function all();
}
