<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Support;

/**
 * Interface ContactRepositoryInterface defines contract for shop contact repository.
 */
interface ContactRepositoryInterface
{
    /**
     * Get shop contacts.
     *
     * @param int $langId Language ID in which contacts should be returned
     *
     * @return array
     */
    public function findAllByLangId($langId);
}
