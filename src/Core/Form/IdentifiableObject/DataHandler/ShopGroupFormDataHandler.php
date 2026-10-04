<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\AddShopGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\EditShopGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;

final class ShopGroupFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function create(array $data): int
    {
        /** @var ShopGroupId $shopGroupId */
        $shopGroupId = $this->commandBus->handle(new AddShopGroupCommand(
            $data['name'],
            $data['color'],
            (bool) $data['share_customer'],
            (bool) $data['share_stock'],
            (bool) $data['share_order'],
            (bool) $data['active'],
        ));

        return $shopGroupId->getValue();
    }

    public function update($id, array $data): void
    {
        $this->commandBus->handle(
            (new EditShopGroupCommand((int) $id))
                ->setName($data['name'])
                ->setColor($data['color'])
                ->setShareCustomer((bool) $data['share_customer'])
                ->setShareStock((bool) $data['share_stock'])
                ->setShareOrder((bool) $data['share_order'])
                ->setActive((bool) $data['active'])
        );
    }
}
