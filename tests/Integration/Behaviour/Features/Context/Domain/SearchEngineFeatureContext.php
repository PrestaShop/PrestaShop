<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\AddSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\BulkDeleteSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\DeleteSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\EditSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Query\GetSearchEngineForEditing;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\ValueObject\SearchEngineId;
use RuntimeException;
use SearchEngine;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class SearchEngineFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add a new search engine :searchEngineReference with following properties:
     *
     * @param string $searchEngineReference
     * @param TableNode $table
     */
    public function createSearchEngine(string $searchEngineReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        $command = new AddSearchEngineCommand($data['server'], $data['queryKey']);

        /** @var SearchEngineId $searchEngineId */
        $searchEngineId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($searchEngineReference, new SearchEngine($searchEngineId->getValue()));
    }

    /**
     * @When I edit the search engine :searchEngineReference with following properties:
     *
     * @param string $searchEngineReference
     * @param TableNode $table
     */
    public function editSearchEngine(string $searchEngineReference, TableNode $table): void
    {
        /** @var SearchEngine $searchEngine */
        $searchEngine = SharedStorage::getStorage()->get($searchEngineReference);

        $data = $table->getRowsHash();
        $command = new EditSearchEngineCommand((int) $searchEngine->id);

        if (isset($data['server'])) {
            $command->setServer($data['server']);
        }

        if (isset($data['queryKey'])) {
            $command->setQueryKey($data['queryKey']);
        }

        $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($searchEngineReference, new SearchEngine((int) $searchEngine->id));
    }

    /**
     * @When I delete the search engine :searchEngineReference
     *
     * @param string $searchEngineReference
     */
    public function deleteSearchEngine(string $searchEngineReference): void
    {
        /** @var SearchEngine $searchEngine */
        $searchEngine = SharedStorage::getStorage()->get($searchEngineReference);

        $this->getCommandBus()->handle(new DeleteSearchEngineCommand((int) $searchEngine->id));
    }

    /**
     * @When I delete search engines: :searchEngineReferences using bulk action.
     *
     * @param string $searchEngineReferences
     */
    public function bulkDeleteSearchEngine(string $searchEngineReferences): void
    {
        $searchEngineIds = [];
        foreach (PrimitiveUtils::castStringArrayIntoArray($searchEngineReferences) as $searchEngineReference) {
            $searchEngineIds[] = (int) SharedStorage::getStorage()->get($searchEngineReference)->id;
        }

        $this->getCommandBus()->handle(new BulkDeleteSearchEngineCommand($searchEngineIds));
    }

    /**
     * @Then the search engine :searchEngineReference server value should be :server
     *
     * @param string $searchEngineReference
     * @param string $server
     */
    public function assertSearchEngineServer(string $searchEngineReference, string $server): void
    {
        /** @var SearchEngine $searchEngine */
        $searchEngine = SharedStorage::getStorage()->get($searchEngineReference);

        if ($searchEngine->server !== $server) {
            throw new RuntimeException(sprintf('Search engine "%s" has server field value "%s", but "%s" was expected.', $searchEngineReference, $searchEngine->server, $server));
        }
    }

    /**
     * @Then the search engine :searchEngineReference query key value should be :queryKey
     *
     * @param string $searchEngineReference
     * @param string $queryKey
     */
    public function assertSearchEngineQueryKey(string $searchEngineReference, string $queryKey): void
    {
        /** @var SearchEngine $searchEngine */
        $searchEngine = SharedStorage::getStorage()->get($searchEngineReference);

        if ($searchEngine->getvar !== $queryKey) {
            throw new RuntimeException(sprintf('Search engine "%s" has query key field value "%s", but "%s" was expected.', $searchEngineReference, $searchEngine->getvar, $queryKey));
        }
    }

    /**
     * @Then the search engine :searchEngineReference should be deleted
     *
     * @param string $searchEngineReference
     */
    public function assertSearchEngineIsDeleted(string $searchEngineReference): void
    {
        /** @var SearchEngine $searchEngine */
        $searchEngine = SharedStorage::getStorage()->get($searchEngineReference);

        try {
            $query = new GetSearchEngineForEditing((int) $searchEngine->id);
            $this->getQueryBus()->handle($query);

            throw new NoExceptionAlthoughExpectedException(sprintf('Search engine "%s" exists, but it was expected to be deleted.', $searchEngineReference));
        } catch (SearchEngineNotFoundException $e) {
            SharedStorage::getStorage()->clear($searchEngineReference);
        }
    }

    /**
     * @Then search engines: :searchEngineReferences should be deleted.
     *
     * @param string $searchEngineReferences
     */
    public function assertSearchEnginesAreDeleted(string $searchEngineReferences): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($searchEngineReferences) as $searchEngineReference) {
            $this->assertSearchEngineIsDeleted($searchEngineReference);
        }
    }
}
