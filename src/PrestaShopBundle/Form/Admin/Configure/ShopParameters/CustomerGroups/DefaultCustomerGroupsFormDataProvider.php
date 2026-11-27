<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\CustomerGroups;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\UpdateDefaultCustomerGroupsCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetDefaultCustomerGroups;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\DefaultCustomerGroups;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Reads and saves the default groups through CQRS, so the back office gets the same validation as the other callers.
 */
final class DefaultCustomerGroupsFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly CommandBusInterface $commandBus,
        private readonly ShopContext $shopContext,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getData(): array
    {
        /** @var DefaultCustomerGroups $defaultGroups */
        $defaultGroups = $this->queryBus->handle(new GetDefaultCustomerGroups($this->shopContext->getShopConstraint()));

        return [
            'visitors_group' => $defaultGroups->getVisitorsGroupId(),
            'guests_group' => $defaultGroups->getGuestsGroupId(),
            'customers_group' => $defaultGroups->getCustomersGroupId(),
        ];
    }

    public function setData(array $data): array
    {
        try {
            $this->commandBus->handle(new UpdateDefaultCustomerGroupsCommand(
                (int) $data['visitors_group'],
                (int) $data['guests_group'],
                (int) $data['customers_group'],
                $this->shopContext->getShopConstraint()
            ));
        } catch (GroupNotFoundException) {
            return [$this->translator->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error')];
        } catch (GroupConstraintException $e) {
            if (GroupConstraintException::GROUP_NOT_ASSOCIATED_TO_SHOP === $e->getCode()) {
                return [$this->translator->trans('The selected group is not associated to the current store.', [], 'Admin.Shopparameters.Notification')];
            }

            return [$this->translator->trans('The object cannot be loaded (the identifier is missing or invalid)', [], 'Admin.Notifications.Error')];
        } catch (GroupException) {
            return [$this->translator->trans('An unexpected error occurred.', [], 'Admin.Notifications.Error')];
        }

        return [];
    }
}
