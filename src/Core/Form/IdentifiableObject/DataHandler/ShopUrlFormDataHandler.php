<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\AddShopUrlCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\EditShopUrlCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopUrlId;

final class ShopUrlFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function create(array $data): int
    {
        /** @var ShopUrlId $shopUrlId */
        $shopUrlId = $this->commandBus->handle(new AddShopUrlCommand(
            (int) $data['shop_id'],
            $data['domain'],
            $data['domain_ssl'],
            $data['physical_uri'],
            $data['virtual_uri'],
            (bool) $data['main'],
            (bool) $data['active'],
        ));

        return $shopUrlId->getValue();
    }

    public function update($id, array $data): void
    {
        $this->commandBus->handle(
            (new EditShopUrlCommand((int) $id))
                ->setShopId((int) $data['shop_id'])
                ->setDomain($data['domain'])
                ->setDomainSsl($data['domain_ssl'])
                ->setPhysicalUri($data['physical_uri'])
                ->setVirtualUri($data['virtual_uri'])
                ->setMain((bool) $data['main'])
                ->setActive((bool) $data['active'])
        );
    }
}
