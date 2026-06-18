<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\EditCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Group\Provider\CustomerGroupLegacyDataProviderInterface;

class CustomerGroupFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly array $contextShopIds,
        private readonly CustomerGroupLegacyDataProviderInterface $legacyDataProvider,
    ) {
    }

    public function create(array $data): int
    {
        /** @var GroupId $groupId */
        $groupId = $this->commandBus->handle(new AddCustomerGroupCommand(
            array_filter($data['name'], static fn (?string $name): bool => $name !== null && $name !== ''),
            new DecimalNumber((string) ($data['reduction'] ?? '0')),
            (bool) $data['price_display_method'],
            (bool) ($data['show_prices'] ?? true),
            $data['shop_association'] ?? $this->contextShopIds
        ));

        $id = $groupId->getValue();
        $this->saveCategoryReductions($id, $data);
        $this->saveModuleRestrictions($id, $data);

        return $id;
    }

    public function update($id, array $data): void
    {
        $command = (new EditCustomerGroupCommand((int) $id))
            ->setLocalizedNames(array_filter($data['name'], static fn (?string $name): bool => $name !== null && $name !== ''))
            ->setReductionPercent(new DecimalNumber((string) ($data['reduction'] ?? '0')))
            ->setDisplayPriceTaxExcluded((bool) $data['price_display_method'])
            ->setShowPrice((bool) ($data['show_prices'] ?? true))
        ;

        if (isset($data['shop_association'])) {
            $command->setShopIds(array_map('intval', $data['shop_association']));
        }

        $this->commandBus->handle($command);

        $this->saveCategoryReductions((int) $id, $data);
        $this->saveModuleRestrictions((int) $id, $data);
    }

    private function saveCategoryReductions(int $groupId, array $data): void
    {
        $reductions = json_decode($data['category_reductions'] ?? '[]', true);
        if (empty($reductions)) {
            $this->legacyDataProvider->saveCategoryReductions($groupId, []);

            return;
        }

        $this->legacyDataProvider->saveCategoryReductions($groupId, $reductions);
    }

    private function saveModuleRestrictions(int $groupId, array $data): void
    {
        $authorizedIds = json_decode($data['authorized_modules'] ?? '[]', true);
        if (!is_array($authorizedIds)) {
            return;
        }

        $this->legacyDataProvider->saveModuleRestrictions($groupId, array_map('intval', $authorizedIds), $this->contextShopIds);
    }
}
