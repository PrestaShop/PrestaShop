<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopUrlRepository;
use PrestaShop\PrestaShop\Adapter\Shop\ShopUrlChangeHandler;
use PrestaShop\PrestaShop\Adapter\Shop\Validate\ShopUrlValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\EditShopUrlCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\EditShopUrlHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopUrlConstraintException;

#[AsCommandHandler]
final class EditShopUrlHandler implements EditShopUrlHandlerInterface
{
    public function __construct(
        private readonly ShopUrlRepository $repository,
        private readonly ShopRepository $shopRepository,
        private readonly ShopUrlValidator $validator,
        private readonly ShopUrlChangeHandler $shopUrlChangeHandler,
    ) {
    }

    public function handle(EditShopUrlCommand $command): void
    {
        $shopUrl = $this->repository->get($command->getShopUrlId());
        $propertiesToUpdate = [];

        if (null !== $command->getShopId() && $command->getShopId()->getValue() !== (int) $shopUrl->id_shop) {
            if ($shopUrl->main) {
                throw new ShopUrlConstraintException(
                    sprintf('Shop url %d is a main url, set another url as main before moving it to another shop', $shopUrl->id),
                    ShopUrlConstraintException::MAIN_URL_CANNOT_BE_UNSET
                );
            }
            $this->shopRepository->assertShopExists($command->getShopId());
            $shopUrl->id_shop = $command->getShopId()->getValue();
            $propertiesToUpdate[] = 'id_shop';
        }
        if (null !== $command->getDomain()) {
            $shopUrl->domain = $command->getDomain();
            $propertiesToUpdate[] = 'domain';
        }
        if (null !== $command->getDomainSsl()) {
            $shopUrl->domain_ssl = $command->getDomainSsl();
            $propertiesToUpdate[] = 'domain_ssl';
        }
        if (null !== $command->getPhysicalUri()) {
            $shopUrl->physical_uri = $command->getPhysicalUri();
            $propertiesToUpdate[] = 'physical_uri';
        }
        if (null !== $command->getVirtualUri()) {
            $shopUrl->virtual_uri = $command->getVirtualUri();
            $propertiesToUpdate[] = 'virtual_uri';
        }
        if (null !== $command->isMain()) {
            if ($shopUrl->main && !$command->isMain()) {
                throw new ShopUrlConstraintException(
                    sprintf('Shop url %d is a main url, set another url as main instead', $shopUrl->id),
                    ShopUrlConstraintException::MAIN_URL_CANNOT_BE_UNSET
                );
            }
            $shopUrl->main = $command->isMain();
            $propertiesToUpdate[] = 'main';
        }
        if (null !== $command->isActive()) {
            $shopUrl->active = $command->isActive();
            $propertiesToUpdate[] = 'active';
        }

        $this->validator->validate($shopUrl);
        $this->repository->partialUpdate($shopUrl, $propertiesToUpdate);

        if ($shopUrl->main) {
            $this->repository->setMain($shopUrl);
        }
        $this->shopUrlChangeHandler->handle();
    }
}
