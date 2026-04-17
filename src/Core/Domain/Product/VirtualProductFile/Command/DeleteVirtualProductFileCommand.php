<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\ValueObject\VirtualProductFileId;

class DeleteVirtualProductFileCommand
{
    /**
     * @var VirtualProductFileId
     */
    private $virtualProductFileId;

    /**
     * @param int $virtualProductFileId
     */
    public function __construct(
        int $virtualProductFileId
    ) {
        $this->virtualProductFileId = new VirtualProductFileId($virtualProductFileId);
    }

    /**
     * @return VirtualProductFileId
     */
    public function getVirtualProductFileId(): VirtualProductFileId
    {
        return $this->virtualProductFileId;
    }
}
