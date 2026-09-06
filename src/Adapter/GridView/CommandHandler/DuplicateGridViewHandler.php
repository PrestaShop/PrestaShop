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
use PrestaShop\PrestaShop\Core\Domain\GridView\Command\DuplicateGridViewCommand;
use PrestaShop\PrestaShop\Core\Domain\GridView\CommandHandler\DuplicateGridViewHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\GridView\ValueObject\GridViewId;
use PrestaShopBundle\Entity\AdminGridView;
use PrestaShopBundle\Entity\Repository\AdminGridConfigurationRepository;
use PrestaShopBundle\Entity\Repository\AdminGridViewRepository;

#[AsCommandHandler]
final class DuplicateGridViewHandler implements DuplicateGridViewHandlerInterface
{
    public function __construct(
        private readonly AdminGridConfigurationRepository $configurationRepository,
        private readonly AdminGridViewRepository $gridViewRepository,
        private readonly GridViewProvider $gridViewProvider,
        private readonly GridViewValidator $gridViewValidator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DuplicateGridViewCommand $command): GridViewId
    {
        $source = $this->gridViewProvider->getAccessibleGridView($command->getGridViewId());
        $sourceConfiguration = $source->getGridConfiguration();

        // The copy always becomes a private view of the current employee
        $configuration = $this->configurationRepository->findOrCreateForEmployee(
            $this->gridViewProvider->getCurrentEmployeeId(),
            $sourceConfiguration->getShopId(),
            $sourceConfiguration->getGridId(),
            $sourceConfiguration->getFilterId(),
            $sourceConfiguration->getControllerRoute()
        );
        $this->gridViewValidator->assertViewLimitIsNotReached($configuration);

        $copy = new AdminGridView();
        $copy
            ->setName($command->getCopyName())
            ->setFilterId($source->getFilterId())
            ->setShared(false)
            ->setFilters($source->getFilters())
            ->setDynamicDateRules($source->getDynamicDateRules())
            ->setGridState($source->getGridState())
        ;
        $configuration->addView($copy);

        $this->gridViewRepository->save($copy);

        return new GridViewId($copy->getId());
    }
}
