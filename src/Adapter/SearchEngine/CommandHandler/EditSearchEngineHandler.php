<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SearchEngine\CommandHandler;

use PrestaShop\PrestaShop\Adapter\SearchEngine\AbstractSearchEngineHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\EditSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\CommandHandler\EditSearchEngineHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineException;
use PrestaShopException;

/**
 * Handles command what edits search engine.
 */
#[AsCommandHandler]
final class EditSearchEngineHandler extends AbstractSearchEngineHandler implements EditSearchEngineHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SearchEngineException
     */
    public function handle(EditSearchEngineCommand $command): void
    {
        $searchEngine = $this->getSearchEngine($command->getSearchEngineId());

        if (null !== $command->getServer()) {
            $searchEngine->server = $command->getServer();
        }

        if (null !== $command->getQueryKey()) {
            $searchEngine->getvar = $command->getQueryKey();
        }

        try {
            if (false === $searchEngine->validateFields(false)) {
                throw new SearchEngineException('Search engine contain invalid field values.');
            }

            if (!$searchEngine->update()) {
                throw new SearchEngineException(sprintf('Cannot update search engine with id "%d"', $searchEngine->id));
            }
        } catch (PrestaShopException) {
            throw new SearchEngineException(sprintf('Cannot update search engine with id "%d"', $searchEngine->id));
        }
    }
}
