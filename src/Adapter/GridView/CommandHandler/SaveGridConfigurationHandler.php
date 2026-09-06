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
use PrestaShop\PrestaShop\Core\Domain\GridView\Command\SaveGridConfigurationCommand;
use PrestaShop\PrestaShop\Core\Domain\GridView\CommandHandler\SaveGridConfigurationHandlerInterface;
use PrestaShopBundle\Entity\Repository\AdminGridConfigurationRepository;

#[AsCommandHandler]
final class SaveGridConfigurationHandler implements SaveGridConfigurationHandlerInterface
{
    public function __construct(
        private readonly AdminGridConfigurationRepository $configurationRepository,
        private readonly GridViewProvider $gridViewProvider,
        private readonly GridViewValidator $gridViewValidator,
        private readonly ShopContext $shopContext,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SaveGridConfigurationCommand $command): void
    {
        $this->gridViewValidator->assertRouteExists($command->getControllerRoute());

        $configuration = $this->configurationRepository->findOrCreateForEmployee(
            $this->gridViewProvider->getCurrentEmployeeId(),
            $this->shopContext->getId(),
            $command->getGridId(),
            $command->getFilterId(),
            $command->getControllerRoute()
        );

        $configuration
            ->setDisplaySharedFilters($command->displaySharedFilters())
            ->setDisplayTotals($command->displayTotals())
        ;

        $this->configurationRepository->save($configuration);
    }
}
