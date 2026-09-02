<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Email;

/**
 * Interface EmailLogEraserInterface defines contract for email logs eraser.
 */
interface EmailLogEraserInterface
{
    /**
     * Erase given email logs.
     *
     * @param int[] $mailLogIds
     *
     * @return array
     */
    public function erase(array $mailLogIds): array;

    /**
     * Erase all email logs.
     *
     * @return bool TRUE if email logs where erased successfully or FALSE otherwise
     */
    public function eraseAll(): bool;
}
