<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\Repository;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\DefaultCustomerGroups;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Reads and writes the visitors, guests and customers default groups stored in the configuration.
 */
final class DefaultCustomerGroupsRepository
{
    private const VISITORS_GROUP_KEY = 'PS_UNIDENTIFIED_GROUP';
    private const GUESTS_GROUP_KEY = 'PS_GUEST_GROUP';
    private const CUSTOMERS_GROUP_KEY = 'PS_CUSTOMER_GROUP';

    public function __construct(
        private readonly ShopConfigurationInterface $configuration,
        private readonly ShopRepository $shopRepository,
    ) {
    }

    public function get(ShopConstraint $shopConstraint): DefaultCustomerGroups
    {
        return new DefaultCustomerGroups(
            (int) $this->configuration->get(self::VISITORS_GROUP_KEY, null, $shopConstraint),
            (int) $this->configuration->get(self::GUESTS_GROUP_KEY, null, $shopConstraint),
            (int) $this->configuration->get(self::CUSTOMERS_GROUP_KEY, null, $shopConstraint)
        );
    }

    public function update(
        GroupId $visitorsGroupId,
        GroupId $guestsGroupId,
        GroupId $customersGroupId,
        ShopConstraint $shopConstraint
    ): void {
        $this->configuration->set(self::VISITORS_GROUP_KEY, $visitorsGroupId->getValue(), $shopConstraint);
        $this->configuration->set(self::GUESTS_GROUP_KEY, $guestsGroupId->getValue(), $shopConstraint);
        $this->configuration->set(self::CUSTOMERS_GROUP_KEY, $customersGroupId->getValue(), $shopConstraint);
    }

    /**
     * Deleting a group affects every shop, so the value of every shop is checked, not only the context one.
     */
    public function isUsedAsDefaultGroup(GroupId $groupId): bool
    {
        $shopConstraints = array_map(
            static fn (int $shopId): ShopConstraint => ShopConstraint::shop($shopId),
            $this->shopRepository->getAllShopIds()
        );
        $shopConstraints[] = ShopConstraint::allShops();

        foreach ($shopConstraints as $shopConstraint) {
            $defaultGroups = $this->get($shopConstraint);
            if (in_array($groupId->getValue(), [
                $defaultGroups->getVisitorsGroupId(),
                $defaultGroups->getGuestsGroupId(),
                $defaultGroups->getCustomersGroupId(),
            ], true)) {
                return true;
            }
        }

        return false;
    }
}
