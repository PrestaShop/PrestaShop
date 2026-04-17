<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderState;

interface OrderStateFileUploaderInterface
{
    /**
     * @param string $filePath
     * @param int $id
     * @param int $fileSize
     */
    public function upload(string $filePath, int $id, int $fileSize): void;
}
