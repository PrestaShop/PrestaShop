<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\GridView\CommandHandler;

use PrestaShop\PrestaShop\Adapter\GridView\GridViewProvider;
use PrestaShop\PrestaShop\Adapter\GridView\GridViewValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\GridView\Command\AddGridViewCommand;
use PrestaShop\PrestaShop\Core\Domain\GridView\CommandHandler\AddGridViewHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\GridView\ValueObject\GridViewId;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewDataSanitizer;
use PrestaShopBundle\Entity\AdminGridView;
use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use PrestaShopBundle\Entity\Repository\AdminGridConfigurationRepository;
use PrestaShopBundle\Entity\Repository\AdminGridViewRepository;

#[AsCommandHandler]
final class AddGridViewHandler implements AddGridViewHandlerInterface
{
    public function __construct(
        private readonly AdminGridConfigurationRepository $configurationRepository,
        private readonly AdminGridViewRepository $gridViewRepository,
        private readonly AdminFilterRepository $adminFilterRepository,
        private readonly GridViewProvider $gridViewProvider,
        private readonly GridViewValidator $gridViewValidator,
        private readonly GridViewDataSanitizer $dataSanitizer,
        private readonly ShopContext $shopContext,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddGridViewCommand $command): GridViewId
    {
        $employeeId = $this->gridViewProvider->getCurrentEmployeeId();
        $shopId = $this->shopContext->getId();
        $this->gridViewValidator->assertRouteExists($command->getControllerRoute());

        // The saved criteria are always read from the filters persisted server-side, never from the client
        $adminFilter = $this->adminFilterRepository->findByEmployeeAndFilterId($employeeId, $shopId, $command->getFilterId());
        $searchCriteria = null !== $adminFilter ? (json_decode($adminFilter->getFilter(), true) ?: []) : [];
        unset($searchCriteria['offset']);

        $configuration = $this->configurationRepository->findOrCreateForEmployee(
            $employeeId,
            $shopId,
            $command->getGridId(),
            $command->getFilterId(),
            $command->getControllerRoute()
        );
        $this->gridViewValidator->assertViewLimitIsNotReached($configuration);

        $gridView = new AdminGridView();
        $gridView
            ->setName($command->getName())
            ->setFilterId($command->getFilterId())
            ->setShared($command->isShared())
            ->setFilters((string) json_encode($searchCriteria))
            ->setDynamicDateRules($this->dataSanitizer->sanitizeDateRules($command->getDynamicDateRules(), $searchCriteria))
            ->setGridState($this->dataSanitizer->sanitizeGridState($command->getGridState()))
        ;
        $configuration->addView($gridView);

        $this->gridViewRepository->save($gridView);

        return new GridViewId($gridView->getId());
    }
}
