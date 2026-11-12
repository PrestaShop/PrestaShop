<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Search\Command\SearchIndexationCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\AddShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\EditShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

final class ShopFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function create(array $data): int
    {
        $importedData = $data['use_import_data'] ? $data['imported_data'] : [];

        /** @var ShopId $shopId */
        $shopId = $this->commandBus->handle(new AddShopCommand(
            $data['name'],
            $data['color'],
            (int) $data['shop_group_id'],
            (int) $data['root_category_id'],
            array_map('intval', $data['category_ids'] ?? []),
            $data['theme_name'],
            (int) $data['source_shop_id'],
            $importedData,
        ));

        if (in_array('product', $importedData, true)) {
            $this->commandBus->handle(new SearchIndexationCommand(true, ShopConstraint::shop($shopId->getValue())));
        }

        return $shopId->getValue();
    }

    public function update($id, array $data): void
    {
        $this->commandBus->handle(
            (new EditShopCommand((int) $id))
                ->setName($data['name'])
                ->setColor($data['color'])
                ->setShopGroupId((int) $data['shop_group_id'])
                ->setRootCategoryId((int) $data['root_category_id'])
                ->setCategoryIds(array_map('intval', $data['category_ids'] ?? []))
                ->setThemeName($data['theme_name'])
        );
    }
}
