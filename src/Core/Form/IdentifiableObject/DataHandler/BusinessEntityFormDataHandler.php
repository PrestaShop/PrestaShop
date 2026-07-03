<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler\AddBusinessEntityHandler;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityGeneralInformation;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;

final class BusinessEntityFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly ShopContext $shopContext,
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
            $generalInformation->getExternalRef(),
            $generalInformation->isDeliveryAuthorized(),
            $generalInformation->getStatus(),
            $this->resolveShopId($generalInformation->getShopId()),
            $generalInformation->getCustomerGroupId(),
            $data['billing_address_as_shipping_address'],
            $data['billing_address'],
            $data['shipping_address'],
        );

        return $this->commandBus->handle($command);
    }

    private function resolveShopId(int $submittedShopId): int
    {
        if ($this->shopContext->isSingleShopContext()) {
            return $this->shopContext->getId();
        }

        return $submittedShopId;
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data)
    {
        // TODO: US2.1.3
    }
}
