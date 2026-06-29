<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\QueryHandler;

use Group as CustomerGroupModel;
use GroupReduction;
use Module;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryHandler\GetCustomerGroupForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;

#[AsQueryHandler]
class GetCustomerGroupForEditingHandler implements GetCustomerGroupForEditingHandlerInterface
{
    public function __construct(
        private readonly GroupRepository $customerGroupRepository,
        private readonly LanguageContext $languageContext,
    ) {
    }

    /**
     * @throws GroupNotFoundException
     */
    public function handle(GetCustomerGroupForEditing $query): EditableCustomerGroup
    {
        $customerGroupId = $query->getCustomerGroupId();
        $customerGroup = $this->customerGroupRepository->get($customerGroupId);
        $groupId = $customerGroupId->getValue();

        return new EditableCustomerGroup(
            $groupId,
            $customerGroup->name,
            new DecimalNumber($customerGroup->reduction),
            (bool) $customerGroup->price_display_method,
            (bool) $customerGroup->show_prices,
            $customerGroup->getAssociatedShops(),
            $this->getCategoryReductions($groupId),
            $this->getAuthorizedModuleIds($groupId, $customerGroup),
        );
    }

    /** @return array<int, array{name: string, reduction: DecimalNumber}> */
    private function getCategoryReductions(int $groupId): array
    {
        $rows = GroupReduction::getGroupReductions($groupId, $this->languageContext->getId());
        if (!is_array($rows)) {
            return [];
        }

        $reductions = [];
        foreach ($rows as $row) {
            $reductions[(int) $row['id_category']] = [
                'name' => (string) ($row['category_name'] ?? ''),
                'reduction' => new DecimalNumber((string) ((float) $row['reduction'] * 100)),
            ];
        }

        return $reductions;
    }

    /** @return int[] */
    private function getAuthorizedModuleIds(int $groupId, CustomerGroupModel $customerGroup): array
    {
        $shopIds = $customerGroup->getAssociatedShops();
        $authorizedModules = Module::getAuthorizedModules($groupId, $shopIds);

        if (!is_array($authorizedModules)) {
            return array_column(Module::getModulesInstalled() ?: [], 'id_module');
        }

        return array_map('intval', array_column($authorizedModules, 'id_module'));
    }
}
