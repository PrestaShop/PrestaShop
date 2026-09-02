<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Feature\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\BulkDeleteFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler\BulkDeleteFeatureValueHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\BulkFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;

#[AsCommandHandler]
class BulkDeleteFeatureValueHandler extends AbstractBulkCommandHandler implements BulkDeleteFeatureValueHandlerInterface
{
    public function __construct(
        protected readonly FeatureValueRepository $featureValueRepository
    ) {
    }

    public function handle(BulkDeleteFeatureValueCommand $command): void
    {
        $this->handleBulkAction($command->getFeatureValueIds(), FeatureValueException::class);
    }

    /**
     * @param FeatureValueId $id
     * @param mixed $command
     *
     * @return void
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $this->featureValueRepository->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkFeatureValueException(
            $caughtExceptions,
            'Errors occurred during Feature value bulk delete action',
            BulkFeatureValueException::FAILED_BULK_DELETE
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function supports($id): bool
    {
        return $id instanceof FeatureValueId;
    }
}
