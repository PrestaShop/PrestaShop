<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Query;

use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryHandler\GetVirtualProductFileForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\ValueObject\VirtualProductFileId;

/**
 * Provides the details of a single virtual product file, without loading the whole product.
 *
 * @see GetVirtualProductFileForEditingHandlerInterface
 */
class GetVirtualProductFileForEditing
{
    private VirtualProductFileId $virtualProductFileId;

    /**
     * @throws VirtualProductFileConstraintException
     */
    public function __construct(int $virtualProductFileId)
    {
        $this->virtualProductFileId = new VirtualProductFileId($virtualProductFileId);
    }

    public function getVirtualProductFileId(): VirtualProductFileId
    {
        return $this->virtualProductFileId;
    }
}
