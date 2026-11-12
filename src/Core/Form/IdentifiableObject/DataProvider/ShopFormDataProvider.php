<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\EditableShop;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

final class ShopFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly ShopConfigurationInterface $configuration,
        private readonly ShopContext $shopContext,
        private readonly FormChoiceProviderInterface $categoryTreeChoiceProvider,
        private readonly FormChoiceProviderInterface $importDataChoiceProvider,
    ) {
    }

    public function getData($id): array
    {
        /** @var EditableShop $shop */
        $shop = $this->queryBus->handle(new GetShopForEditing((int) $id));

        return [
            'name' => $shop->getName(),
            'color' => $shop->getColor(),
            'shop_group_id' => $shop->getShopGroupId(),
            'root_category_id' => $shop->getRootCategoryId(),
            'category_ids' => $shop->getCategoryIds(),
            'theme_name' => $shop->getThemeName(),
        ];
    }

    public function getDefaultData(): array
    {
        $rootCategoryId = $this->shopContext->getCategoryId();

        return [
            'root_category_id' => $rootCategoryId,
            'category_ids' => $this->getCategoryTreeIds($this->categoryTreeChoiceProvider->getChoices(), $rootCategoryId),
            'theme_name' => $this->shopContext->getThemeName(),
            'use_import_data' => true,
            'source_shop_id' => (int) $this->configuration->get('PS_SHOP_DEFAULT', null, ShopConstraint::allShops()),
            'imported_data' => array_values($this->importDataChoiceProvider->getChoices()),
        ];
    }

    /**
     * @return int[] the category and all its children
     */
    private function getCategoryTreeIds(array $categoryTree, int $categoryId, bool $inSubTree = false): array
    {
        $categoryIds = [];
        foreach ($categoryTree as $category) {
            $isInSubTree = $inSubTree || (int) $category['id_category'] === $categoryId;
            if ($isInSubTree) {
                $categoryIds[] = (int) $category['id_category'];
            }
            $categoryIds = array_merge($categoryIds, $this->getCategoryTreeIds($category['children'] ?? [], $categoryId, $isInSubTree));
        }

        return $categoryIds;
    }
}
