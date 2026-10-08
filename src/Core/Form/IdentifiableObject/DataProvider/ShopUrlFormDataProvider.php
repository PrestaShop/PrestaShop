<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopUrlForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\EditableShopUrl;

final class ShopUrlFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly ShopContext $shopContext,
    ) {
    }

    public function getData($id): array
    {
        /** @var EditableShopUrl $shopUrl */
        $shopUrl = $this->queryBus->handle(new GetShopUrlForEditing((int) $id));

        return [
            'shop_id' => $shopUrl->getShopId(),
            'main' => $shopUrl->isMain(),
            'active' => $shopUrl->isActive(),
            'domain' => $shopUrl->getDomain(),
            'domain_ssl' => $shopUrl->getDomainSsl(),
            'physical_uri' => $shopUrl->getPhysicalUri(),
            'virtual_uri' => $shopUrl->getVirtualUri(),
        ];
    }

    public function getDefaultData(): array
    {
        return [
            'shop_id' => $this->shopContext->getId(),
            'main' => false,
            'active' => true,
            'domain' => $this->shopContext->getDomain(),
            'domain_ssl' => $this->shopContext->getDomainSSL(),
            'physical_uri' => $this->shopContext->getPhysicalUri(),
        ];
    }
}
