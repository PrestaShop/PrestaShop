<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command;

use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\DeleteBusinessEntityHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;

/**
 * Class DeleteBusinessEntityCommand is used to soft delete an existing business entity.
 *
 * @see DeleteBusinessEntityHandlerInterface
 */
class DeleteBusinessEntityCommand
{
    private readonly BusinessEntityId $businessEntityId;

    public function __construct(
        int $businessEntityId,
    ) {
        $this->businessEntityId = new BusinessEntityId($businessEntityId);
    }

    public function getBusinessEntityId(): BusinessEntityId
    {
        return $this->businessEntityId;
    }
}
