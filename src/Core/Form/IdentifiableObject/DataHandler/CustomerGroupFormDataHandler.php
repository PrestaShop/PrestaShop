<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\EditCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\CustomerGroupId;

class CustomerGroupFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly ShopContext $shopContext,
    ) {
    }

    public function create(array $data): int
    {
        $command = new AddCustomerGroupCommand(
            array_filter($data['name'], static fn (?string $name): bool => $name !== null && $name !== ''),
            new DecimalNumber((string) ($data['reduction'] ?? '0')),
            (bool) $data['price_display_method'],
            (bool) ($data['show_prices'] ?? true),
            $data['shop_association'] ?? $this->shopContext->getAssociatedShopIds()
        );

        $command->setCategoryReductions($this->extractCategoryReductions($data));
        $command->setAuthorizedModuleIds($this->extractAuthorizedModuleIds($data));

        /** @var CustomerGroupId $groupId */
        $groupId = $this->commandBus->handle($command);

        return $groupId->getValue();
    }

    public function update($id, array $data): void
    {
        $command = (new EditCustomerGroupCommand((int) $id))
            ->setLocalizedNames(array_filter($data['name'], static fn (?string $name): bool => $name !== null && $name !== ''))
            ->setReductionPercent(new DecimalNumber((string) ($data['reduction'] ?? '0')))
            ->setDisplayPriceTaxExcluded((bool) $data['price_display_method'])
            ->setShowPrice((bool) ($data['show_prices'] ?? true))
            ->setCategoryReductions($this->extractCategoryReductions($data))
            ->setAuthorizedModuleIds($this->extractAuthorizedModuleIds($data))
        ;

        if (isset($data['shop_association'])) {
            $command->setShopIds(array_map('intval', $data['shop_association']));
        }

        $this->commandBus->handle($command);
    }

    /** @return array<int, float> category id => reduction percent */
    private function extractCategoryReductions(array $data): array
    {
        $reductions = [];
        foreach ($data['category_reductions'] ?? [] as $entry) {
            $categoryId = (int) ($entry['id_category'] ?? 0);
            $reduction = (float) ($entry['reduction'] ?? 0);
            if ($categoryId > 0) {
                $reductions[$categoryId] = $reduction;
            }
        }

        return $reductions;
    }

    /** @return int[] */
    private function extractAuthorizedModuleIds(array $data): array
    {
        $ids = [];
        foreach ($data['module_restrictions'] ?? [] as $entry) {
            if (!empty($entry['authorized'])) {
                $ids[] = (int) $entry['id'];
            }
        }

        return $ids;
    }
}
