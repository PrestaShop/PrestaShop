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
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\AddShopUrlCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\AddShopUrlHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopUrlId;
use ShopUrl;

#[AsCommandHandler]
final class AddShopUrlHandler implements AddShopUrlHandlerInterface
{
    public function __construct(
        private readonly ShopUrlRepository $repository,
        private readonly ShopRepository $shopRepository,
        private readonly ShopUrlValidator $validator,
        private readonly ShopUrlChangeHandler $shopUrlChangeHandler,
    ) {
    }

    public function handle(AddShopUrlCommand $command): ShopUrlId
    {
        $shopId = $command->getShopId();
        $this->shopRepository->assertShopExists($shopId);

        $shopUrl = new ShopUrl();
        $shopUrl->id_shop = $shopId->getValue();
        $shopUrl->domain = $command->getDomain();
        $shopUrl->domain_ssl = $command->getDomainSsl();
        $shopUrl->physical_uri = $command->getPhysicalUri();
        $shopUrl->virtual_uri = $command->getVirtualUri();
        $shopUrl->main = $command->isMain() || !$this->repository->hasMainUrl($shopId);
        $shopUrl->active = $command->isActive();

        $this->validator->validate($shopUrl);
        $shopUrlId = $this->repository->add($shopUrl);

        if ($shopUrl->main) {
            $this->repository->setMain($shopUrl);
        }
        $this->shopUrlChangeHandler->handle();

        return $shopUrlId;
    }
}
