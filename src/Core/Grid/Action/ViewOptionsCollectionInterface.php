<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action;

use Iterator;

/**
 * Interface ViewOptionsCollectionInterface defines contract for view options collection.
 */
interface ViewOptionsCollectionInterface extends Iterator
{
    /**
     * Add view option to collection.
     *
     * @param string $action
     * @param mixed $value
     *
     * @return self
     */
    public function add(string $action, $value);

    /**
     * Get view options as array.
     *
     * @return array
     */
    public function all();
}
