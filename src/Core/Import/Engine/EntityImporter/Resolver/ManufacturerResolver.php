<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver;

use PrestaShop\PrestaShop\Adapter\Manufacturer\Repository\ManufacturerRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\AddManufacturerCommand;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;

/**
 * Resolves a manufacturer NAME to an id, creating the manufacturer (associated
 * with the run's shops) when the name matches nothing. Numeric ids are not this
 * resolver's business: an id is a MATCH-ONLY concern the caller probes through
 * ImportEntityExistenceChecker (creating a brand named "123" from an unknown id
 * would be nonsense).
 *
 * Caching and once-per-batch reporting come from QuietResolutionTrait.
 */
class ManufacturerResolver implements EntityResolverInterface
{
    use QuietResolutionTrait;

    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly ManufacturerRepository $manufacturerRepository,
        protected readonly RunShopIdsProvider $runShopIdsProvider,
    ) {
    }

    public function resolve(string $value, ImportRunContext $context): ResolvedEntity
    {
        return $this->resolveThroughCache(
            $value,
            fn (): array => $this->manufacturerRepository->getManufacturerIdsByName($value),
            fn (): int => $this->commandBus->handle(
                new AddManufacturerCommand($value, true, [], [], [], [], $this->runShopIdsProvider->getRunShopIds($context))
            )->getValue()
        );
    }
}
