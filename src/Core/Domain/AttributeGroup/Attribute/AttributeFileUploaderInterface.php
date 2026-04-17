<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute;

interface AttributeFileUploaderInterface
{
    /**
     * @param string $filePath
     * @param int $id
     */
    public function upload(string $filePath, int $id): void;
}
