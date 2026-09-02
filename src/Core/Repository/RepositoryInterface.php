<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Repository;

/**
 * Define the contract to access entities.
 *
 * A repository should only contains methods for querying the data.
 */
interface RepositoryInterface
{
    /**
     * Returns the complete list of items.
     *
     * @return array
     */
    public function findAll();
}
