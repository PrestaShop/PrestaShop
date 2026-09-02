<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Feature\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\BulkDeleteFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler\BulkDeleteFeatureHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\BulkFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;

#[AsCommandHandler]
class BulkDeleteFeatureHandler extends AbstractBulkCommandHandler implements BulkDeleteFeatureHandlerInterface
{
    /**
     * @var FeatureRepository
     */
    private $featureRepository;

    public function __construct(
        FeatureRepository $featureRepository
    ) {
        $this->featureRepository = $featureRepository;
    }

    public function handle(BulkDeleteFeatureCommand $command): void
    {
        $this->handleBulkAction($command->getFeatureIds(), FeatureException::class);
    }

    /**
     * @param FeatureId $id
     * @param BulkDeleteFeatureCommand $command
     *
     * @return void
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $this->featureRepository->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkFeatureException(
            $caughtExceptions,
            'Errors occurred during Feature bulk delete action',
            BulkFeatureException::FAILED_BULK_DELETE
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function supports($id): bool
    {
        return $id instanceof FeatureId;
    }
}
