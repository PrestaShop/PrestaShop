<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\CommandHandler;

use Language;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Adapter\Shop\ShopCategoriesUpdater;
use PrestaShop\PrestaShop\Adapter\Shop\Validate\ShopValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\AddShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\AddShopHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotAddShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use Shop;

#[AsCommandHandler]
final class AddShopHandler implements AddShopHandlerInterface
{
    public function __construct(
        private readonly ShopRepository $repository,
        private readonly ShopValidator $validator,
        private readonly ShopCategoriesUpdater $categoriesUpdater,
        private readonly ShopConfigurationInterface $configuration,
    ) {
    }

    public function handle(AddShopCommand $command): ShopId
    {
        $sourceShopId = $command->getSourceShopId() ?? new ShopId((int) $this->configuration->get('PS_SHOP_DEFAULT', null, ShopConstraint::allShops()));
        $this->repository->assertShopExists($sourceShopId);

        $categoryIds = array_map(static fn (CategoryId $categoryId): int => $categoryId->getValue(), $command->getCategoryIds());
        $this->validator->assertRootCategory($command->getRootCategoryId(), $categoryIds);

        $shop = new Shop();
        $shop->name = $command->getName();
        $shop->color = $command->getColor();
        $shop->id_shop_group = $command->getShopGroupId()->getValue();
        $shop->id_category = $command->getRootCategoryId()->getValue();
        $shop->theme_name = $command->getThemeName();

        $this->validator->validate($shop);
        $shopId = $this->repository->add($shop);

        $this->importData($shop, $shopId, $sourceShopId, $command->getDataToImport(), $command->getCategoryIds());
        $shop->associateSuperAdmins();

        if (!$this->categoriesUpdater->associateCategories($shopId, $categoryIds)) {
            throw new CannotAddShopException(sprintf('Cannot associate categories to shop %d', $shopId->getValue()));
        }
        $this->categoriesUpdater->setHomeCategory($shopId, $command->getRootCategoryId());

        return $shopId;
    }

    /**
     * @param string[] $dataToImport
     * @param CategoryId[] $categoryIds
     */
    private function importData(Shop $shop, ShopId $shopId, ShopId $sourceShopId, array $dataToImport, array $categoryIds): void
    {
        $tablesToImport = array_fill_keys($dataToImport, true);
        $shop->copyShopData($sourceShopId->getValue(), $tablesToImport);

        if (isset($tablesToImport['product']) && 1 === count($categoryIds)) {
            $this->repository->setProductsDefaultCategory($shopId, reset($categoryIds));
        }
        if (!isset($tablesToImport['group'])) {
            $this->repository->addCustomerGroups($shopId, array_map(
                fn (string $groupKey): int => (int) $this->configuration->get($groupKey, null, ShopConstraint::allShops()),
                ['PS_UNIDENTIFIED_GROUP', 'PS_GUEST_GROUP', 'PS_CUSTOMER_GROUP']
            ));
        }
        if (!isset($tablesToImport['cms'])) {
            $this->repository->addCmsRootCategory($shopId, array_map('intval', Language::getIDs(false)));
        }
    }
}
