<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Command;

use PrestaShop\PrestaShop\Core\Domain\B2bRole\Exception\B2bRoleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\B2bRole\ValueObject\B2bRoleId;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\CommandHandler\AddCustomerB2bHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CustomerB2bConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\ValueObject\ExternalReference;
use PrestaShopBundle\Entity\Enum\CustomerB2bStatus;

/**
 * Creates a new B2B customer.
 *
 * @see AddCustomerB2bHandlerInterface
 */
final class AddCustomerB2bCommand
{
    public readonly CustomerId $customerId;
    public readonly BusinessEntityId $businessEntityId;
    public readonly B2bRoleId $roleId;
    public readonly ?ExternalReference $externalRef;

    /**
     * @param CustomerB2bStatus|null $status null keeps the default status of a new B2B customer
     *
     * @throws CustomerB2bConstraintException
     * @throws B2bRoleConstraintException
     * @throws CustomerConstraintException
     */
    public function __construct(
        int $customerId,
        int $businessEntityId,
        int $roleId,
        public readonly ?CustomerB2bStatus $status = null,
        ?string $externalRef = null,
    ) {
        $this->customerId = new CustomerId($customerId);
        $this->businessEntityId = new BusinessEntityId($businessEntityId);
        $this->roleId = new B2bRoleId($roleId);
        $this->externalRef = null === $externalRef ? null : new ExternalReference($externalRef);
    }
}
