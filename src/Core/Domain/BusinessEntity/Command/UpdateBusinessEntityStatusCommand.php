<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command;

use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\UpdateBusinessEntityStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityConstraintException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

/**
 * Class UpdateBusinessEntityStatusCommand changes the status of an existing business entity,
 * with an optional comment kept for the administration log.
 *
 * @see UpdateBusinessEntityStatusHandlerInterface
 */
class UpdateBusinessEntityStatusCommand
{
    private readonly BusinessEntityId $businessEntityId;

    /**
     * @throws BusinessEntityConstraintException
     */
    public function __construct(
        int $businessEntityId,
        private readonly BusinessEntityStatus $status,
        private readonly ?string $comment = null,
    ) {
        $this->businessEntityId = new BusinessEntityId($businessEntityId);
    }

    public function getBusinessEntityId(): BusinessEntityId
    {
        return $this->businessEntityId;
    }

    public function getStatus(): BusinessEntityStatus
    {
        return $this->status;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
