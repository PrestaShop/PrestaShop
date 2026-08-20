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
 * Resolutions are cached for the service lifetime (one batch request) in their
 * QUIET form: the creation/ambiguity information is returned once, on first
 * resolution, so callers emit each warning once per run, not once per row.
 */
class ManufacturerResolver
{
    /**
     * @var array<string, ResolvedEntity> quiet resolutions, keyed by name
     */
    protected array $cache = [];

    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly ManufacturerRepository $manufacturerRepository,
        protected readonly RunShopIdsProvider $runShopIdsProvider,
    ) {
    }

    public function resolve(string $name, ImportRunContext $context): ResolvedEntity
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $manufacturerIds = $this->manufacturerRepository->getManufacturerIdsByName($name);
        if ([] === $manufacturerIds) {
            $manufacturerId = $this->commandBus->handle(
                new AddManufacturerCommand($name, true, [], [], [], [], $this->runShopIdsProvider->getRunShopIds($context))
            )->getValue();
            $resolved = new ResolvedEntity($manufacturerId, true);
        } else {
            $resolved = new ResolvedEntity($manufacturerIds[0], false, count($manufacturerIds));
        }
        $this->cache[$name] = new ResolvedEntity($resolved->id);

        return $resolved;
    }
}
