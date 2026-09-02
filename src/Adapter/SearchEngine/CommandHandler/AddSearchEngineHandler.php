<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SearchEngine\CommandHandler;

use PrestaShop\PrestaShop\Adapter\SearchEngine\AbstractSearchEngineHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\AddSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\CommandHandler\AddSearchEngineHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineException;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\ValueObject\SearchEngineId;
use PrestaShopException;
use SearchEngine;

/**
 * Handles command what is responsible for creating new search engine.
 */
#[AsCommandHandler]
final class AddSearchEngineHandler extends AbstractSearchEngineHandler implements AddSearchEngineHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SearchEngineException
     */
    public function handle(AddSearchEngineCommand $command): SearchEngineId
    {
        $searchEngine = new SearchEngine();

        $searchEngine->server = $command->getServer();
        $searchEngine->getvar = $command->getQueryKey();

        try {
            if (false === $searchEngine->validateFields(false)) {
                throw new SearchEngineException('Search engine contain invalid field values');
            }

            if (false === $searchEngine->add()) {
                throw new SearchEngineException(sprintf('Failed to add new search engine "%s"', $command->getServer()));
            }
        } catch (PrestaShopException) {
            throw new SearchEngineException(sprintf('Failed to add new search engine "%s"', $command->getServer()));
        }

        return new SearchEngineId((int) $searchEngine->id);
    }
}
