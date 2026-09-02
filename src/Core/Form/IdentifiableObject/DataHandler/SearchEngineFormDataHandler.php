<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\AddSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\EditSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\ValueObject\SearchEngineId;

/**
 * Handles submitted search engine form data.
 */
final class SearchEngineFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        $command = new AddSearchEngineCommand(
            $data['server'],
            $data['query_key']
        );

        /** @var SearchEngineId $searchEngineId */
        $searchEngineId = $this->commandBus->handle($command);

        return $searchEngineId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): void
    {
        $command = (new EditSearchEngineCommand($id))
            ->setServer($data['server'])
            ->setQueryKey($data['query_key']);

        $this->commandBus->handle($command);
    }
}
