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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller;

use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\PrestaShopBundle\Controller\FormFiller\FormFiller;
use Tests\Integration\Utility\ContextMockerTrait;
use Tests\Resources\DatabaseDump;

abstract class GridControllerTestCase extends WebTestCase
{
    use ContextMockerTrait;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var FormFiller
     */
    protected $formFiller;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['admin_filter']);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['admin_filter']);
    }

    public function setUp(): void
    {
        self::mockContext();
        $this->client = static::createClient();
        $this->router = $this->client->getContainer()->get('router');
        $this->formFiller = new FormFiller();
    }

    /**
     * Calls the grid page and return the parsed entities it contains, based on the parseEntityFromRow that each
     * sub-class must implement.
     *
     * @param array $routeParams
     *
     * @return TestEntityDTOCollection
     */
    protected function getEntitiesFromGrid(array $routeParams = []): TestEntityDTOCollection
    {
        $gridUrl = $this->generateGridUrl($routeParams);
        $crawler = $this->client->request('GET', $gridUrl);
        $this->assertResponseIsSuccessful();

        return $this->parseEntitiesFromGridTable($crawler);
    }

    /**
     * Parses all the entities' data from the grid table, based on the parseEntityFromRow that each sub-class must
     * implement.
     *
     * @param Crawler $crawler
     *
     * @return TestEntityDTOCollection
     */
    protected function parseEntitiesFromGridTable(Crawler $crawler): TestEntityDTOCollection
    {
        $testEntityDTOCollection = new TestEntityDTOCollection();
        $grid = $crawler->filter($this->getGridSelector());
        if (empty($grid->count())) {
            throw new InvalidArgumentException(sprintf(
                'Could not find a grid matching CSS selector "%s"',
                $this->getGridSelector()
            ));
        }

        // Get rows but filter the one that is used to indicate there is no result
        $entitiesRows = $grid->filter('tbody tr:not(.empty_row)');

        // If no rows are found the collection is empty
        if ($entitiesRows->count()) {
            $entities = $entitiesRows->each(function ($tr, $i) {
                return $this->parseEntityFromRow($tr, $i);
            });

            // Fill the collection
            foreach ($entities as $entity) {
                $testEntityDTOCollection->add($entity);
            }
        }

        return $testEntityDTOCollection;
    }

    /**
     * Calls the grid page with specific filters and return the parsed entities it contains, based on the
     * parseEntityFromRow that each sub-class must implement.
     *
     * @param array $testFilters
     * @param array $routeParams
     *
     * @return TestEntityDTOCollection
     */
    protected function getFilteredEntitiesFromGrid(array $testFilters, array $routeParams = []): TestEntityDTOCollection
    {
        $gridUrl = $this->generateGridUrl($routeParams);
        $crawler = $this->client->request('GET', $gridUrl);
        $this->assertResponseIsSuccessful();
        $gridRoute = $this->client->getRequest()->attributes->get('_route');

        $filterForm = $this->fillFiltersForm($crawler, $testFilters);

        // Filter url applies the search filter and then redirects to the grid
        $this->client->submit($filterForm);
        $this->assertResponseRedirects();

        // Then we manually request the url that was used as redirection, and finally return the parsed entities
        $redirectUrl = $this->client->getResponse()->headers->get('Location');
        $crawler = $this->client->request('GET', $redirectUrl);
        $this->assertResponseIsSuccessful();

        // We check that the redirection happened successfully to the same route
        $redirectionRoute = $this->client->getRequest()->attributes->get('_route');
        $this->assertEquals($gridRoute, $redirectionRoute);

        return $this->parseEntitiesFromGridTable($crawler);
    }

    /**
     * @param Crawler $crawler
     * @param array $formModifications
     *
     * @return Form
     */
    protected function fillFiltersForm(Crawler $crawler, array $formModifications): Form
    {
        $filtersForm = $this->getFormByButton($crawler, $this->getFilterSearchButtonSelector());
        $this->formFiller->fillForm($filtersForm, $formModifications);

        return $filtersForm;
    }

    /**
     * @param Crawler $crawler
     * @param string $formButtonSelector
     *
     * @return Form
     */
    protected function getFormByButton(Crawler $crawler, string $formButtonSelector): Form
    {
        $submitButton = $crawler->selectButton($formButtonSelector);
        try {
            $form = $submitButton->form();
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(sprintf(
                'Could not find form in the page, maybe the button selector "%s" is not adapted, usually you can use the button id (without the #) or its name',
                $formButtonSelector
            ), $e->getCode(), $e);
        }

        return $form;
    }

    /**
     * Asserts collection contains the entity matching the provided ID
     *
     * @param TestEntityDTOCollection $entities
     * @param int $searchEntityId
     */
    protected function assertCollectionContainsEntity(TestEntityDTOCollection $entities, int $searchEntityId): void
    {
        $ids = array_map(function ($entity) {
            return $entity->getId();
        }, iterator_to_array($entities));

        $this->assertContains($searchEntityId, $ids);
    }

    /**
     * Returns the selector allowing to get the grid's search button.
     *
     * @return string
     */
    abstract protected function getFilterSearchButtonSelector(): string;

    /**
     * @param array $routeParams
     *
     * @return string
     */
    abstract protected function generateGridUrl(array $routeParams = []): string;

    /**
     * Returns the selector of the tested grid, for example: #products_grid_table
     *
     * @return string
     */
    abstract protected function getGridSelector(): string;

    /**
     * This method parse a row from the grid and returns a TestEntityDTO which contains, at the minimum, the ID of the
     * entity plus additional variables that you could wish to test.
     *
     * @param Crawler $tr
     * @param int $i
     *
     * @return TestEntityDTO
     */
    abstract protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO;
}
