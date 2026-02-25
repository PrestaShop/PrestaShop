<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\AddBusinessEntityHandler;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityGeneralInformation;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;

final class BusinessEntityFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        protected readonly CommandBusInterface $commandBus,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @see AddBusinessEntityHandler::handle
     */
    public function create(array $data): BusinessEntityId
    {
        /** @var BusinessEntityGeneralInformation $generalInformation */
        $generalInformation = $data['general_information'];

        $command = new AddBusinessEntityCommand(
            $generalInformation->getName(),
            $generalInformation->getLegalName(),
            $generalInformation->getEnterpriseId(),
            $generalInformation->getExternalRef(),
            $generalInformation->isFlagDeliveryAuthorized(),
            $generalInformation->getStatus(),
            $data['billingAddressAsShippingAddress'],
            $data['billing_address'],
            $data['shipping_address'],
        );

        return $this->commandBus->handle($command);
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data)
    {
        // TODO: US2.1.3
    }
}
