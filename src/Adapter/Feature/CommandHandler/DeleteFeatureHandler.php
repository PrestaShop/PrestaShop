<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Feature\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\DeleteFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler\DeleteFeatureHandlerInterface;

#[AsCommandHandler]
class DeleteFeatureHandler implements DeleteFeatureHandlerInterface
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

    public function handle(DeleteFeatureCommand $command): void
    {
        $this->featureRepository->delete($command->getFeatureId());
    }
}
