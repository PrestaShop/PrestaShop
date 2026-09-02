<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\EditFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;

class FeatureValueFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        protected readonly CommandBusInterface $commandBus
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        /** @var FeatureValueId $featureValueId */
        $featureValueId = $this->commandBus->handle(new AddFeatureValueCommand(
            $data['feature_id'],
            $data['value']
        ));

        return $featureValueId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): void
    {
        $command = (new EditFeatureValueCommand($id))
            ->setLocalizedValues($data['value'])
        ;

        $this->commandBus->handle($command);
    }
}
