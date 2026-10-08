<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopUrlRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopUrlForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryHandler\GetShopUrlForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\EditableShopUrl;

#[AsQueryHandler]
final class GetShopUrlForEditingHandler implements GetShopUrlForEditingHandlerInterface
{
    public function __construct(
        private readonly ShopUrlRepository $repository,
    ) {
    }

    public function handle(GetShopUrlForEditing $query): EditableShopUrl
    {
        $shopUrlId = $query->getShopUrlId();
        $shopUrl = $this->repository->get($shopUrlId);

        return new EditableShopUrl(
            $shopUrlId->getValue(),
            (int) $shopUrl->id_shop,
            (string) $shopUrl->domain,
            (string) $shopUrl->domain_ssl,
            (string) $shopUrl->physical_uri,
            (string) $shopUrl->virtual_uri,
            (bool) $shopUrl->main,
            (bool) $shopUrl->active,
        );
    }
}
