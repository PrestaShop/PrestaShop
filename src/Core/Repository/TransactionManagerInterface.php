<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Repository;

interface TransactionManagerInterface
{
    /**
     * Initiate a transaction
     */
    public function beginTransaction();

    /**
     * Commit a transaction
     */
    public function commit();

    /**
     * Rollback a transaction
     */
    public function rollback();
}
