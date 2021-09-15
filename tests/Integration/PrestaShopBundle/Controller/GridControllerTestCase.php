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

use PrestaShop\PrestaShop\Core\Exception\TypeException;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Tests\Integration\PrestaShopBundle\Controller\FormFiller\FormFiller;

abstract class GridControllerTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Route to the grid you are testing
     *
     * @var string
     */
    protected $gridRoute;

    /**
     * The id of the test entity with which filters will be tested
     * Should be set during SetUp
     *
     * @var int
     */
    protected $testEntityId;

    /**
     * The name of entity you are testing, e.g.,address.
     *
     * @var string
     */
    protected $testEntityName;

    /**
     * The route to create entity
     *
     * @var string
     */
    protected $createEntityRoute;

    /**
     * The route to delete entity
     *
     * @var string
     */
    protected $deleteEntityRoute;

    /**
     * Amount of entities in starting list
     *
     * @var int
     */
    protected $initialEntityCount;

    /**
     * @var FormFiller
     */
    protected $formFiller;

    /**
     * Service id form form handler
     *
     * @var string
     */
    protected $formHandlerServiceId;

    /**
     * Save button id should always be the same, but can be overridden if needed
     *
     * @var string
     */
    protected $saveButtonId = 'save-button';

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->formFiller = new FormFiller();
    }

    /**
     * Creates a test entity and ensures asserts that amount of entities in the list got increased by one
     *
     * @throws TypeException
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects(true);

        /** Asserts that list contains as many entities as expected */
        $crawler = $this->client->request('GET', $this->getIndexRoute($this->client->getKernel()->getContainer()->get('router')));
        $this->assertResponseIsSuccessful();

        $entities = $this->getEntityList($crawler);
        $this->initialEntityCount = $entities->count();

        $this->createTestEntity();

        /** Asserts amount of entities in the list increased by one and test entity exists */
        $crawler = $this->client->request('GET', $this->getIndexRoute($this->client->getKernel()->getContainer()->get('router')));
        $entities = $this->getEntityList($crawler);
        /* If this fails it means entity was not created correctly */
        self::assertCount($this->initialEntityCount + 1, $entities);
        $this->assertTestEntityExists($entities);
    }

    /**
     * Removes the created test entity and asserts that it was successfully removed from the list.
     *
     * @throws TypeException
     */
    public function tearDown(): void
    {
        $this->client->followRedirects(true);
        $router = $this->client->getContainer()->get('router');

        /**
         * Assumes that deletion route only requires id param and that id has format is $this->testEntityName . 'Id'
         * If it's not the case you can always override tearDown with logic specific to grid you are testing
         */
        $deleteUrl = $router->generate($this->deleteEntityRoute, [$this->testEntityName . 'Id' => $this->testEntityId]);
        $this->client->request('POST', $deleteUrl);
        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', $this->getIndexRoute($this->client->getKernel()->getContainer()->get('router')));

        $entities = $this->getEntityList($crawler);

        /* If this fails it means entity deletion did not work as intended */
        self::assertCount($this->initialEntityCount, $entities);
    }

    /**
     * @return void
     */
    protected function createTestEntity(): void
    {
        $router = $this->client->getContainer()->get('router');
        $createEntityUrl = $router->generate($this->createEntityRoute);

        $crawler = $this->client->request('GET', $createEntityUrl);
        $this->assertResponseIsSuccessful();

        $submitButton = $crawler->selectButton($this->saveButtonId);
        /** If you get "InvalidArgumentException: The current node list is empty" error here it means save button was not found */
        $entityForm = $submitButton->form();

        $entityForm = $this->formFiller->fillForm($entityForm, $this->getCreateEntityFormModifications());

        /*
         * Without changing followRedirects to false when submitting the form
         * $dataChecker->getLastCreatedId() returns null.
         */
        $this->client->followRedirects(false);
        $this->client->submit($entityForm);
        $this->client->followRedirects(true);
        $formHandlerChecker = $this->client->getContainer()->get($this->formHandlerServiceId);
        $this->testEntityId = $formHandlerChecker->getLastCreatedId();
        self::assertNotNull($this->testEntityId);
    }

    /**
     * If this test fails it's likely problem with filters being incorrect or filtering not working
     * Asserts that there is only one entity left in the list after using filters
     *
     * @param array $testFilters
     *
     * @throws TypeException
     */
    protected function assertFiltersFindOnlyTestEntity(array $testFilters): void
    {
        $crawler = $this->client->request('GET', $this->getIndexRoute($this->client->getKernel()->getContainer()->get('router')));
        $this->assertResponseIsSuccessful();

        /** Assert that list contains all entities and thus not affected by anything */
        $entities = $this->getEntityList($crawler);
        self::assertCount($this->initialEntityCount + 1, $entities);

        /**
         * Submit filters
         */
        $filterForm = $this->fillFiltersForm($crawler, $testFilters);
        $this->client->followRedirects(true);

        $crawler = $this->client->submit($filterForm);

        /**
         * Assert that there is only test entity left in the list after using filters
         */
        $entities = $this->getEntityList($crawler);

        self::assertCount(1, $entities);
        $this->assertTestEntityExists($entities);
    }

    /**
     * @param Crawler $crawler
     * @param array $formModifications
     *
     * @return Form
     */
    protected function fillFiltersForm(Crawler $crawler, array $formModifications): Form
    {
        $button = $crawler->selectButton($this->testEntityName . '[actions][search]');
        $filtersForm = $button->form();
        $this->formFiller->fillForm($filtersForm, $formModifications);

        return $filtersForm;
    }

    /**
     * Asserts test entity exists with the list
     *
     * @param TestEntityDTOCollection $entities
     */
    protected function assertTestEntityExists(TestEntityDTOCollection $entities): void
    {
        $ids = array_map(function ($entity) {
            return $entity->getId();
        }, iterator_to_array($entities));
        self::assertContains($this->getTestEntity()->getId(), $ids);
    }

    /**
     * @param Crawler $crawler
     *
     * @return TestEntityDTOCollection
     *
     * @throws TypeException
     */
    protected function getEntityList(Crawler $crawler): TestEntityDTOCollection
    {
        $testEntityDTOCollection = new TestEntityDTOCollection();
        $entities = $crawler->filter('#' . $this->testEntityName . '_grid_table')->filter('tbody tr')->each(function ($tr, $i) {
            return $this->getEntity($tr, $i);
        });
        foreach ($entities as $entity) {
            $testEntityDTOCollection->add($entity);
        }

        return $testEntityDTOCollection;
    }

    /**
     * Can be overridden if you need to manipulate route in in any way
     *
     * @param Router $router
     *
     * @return string
     */
    protected function getIndexRoute(Router $router): string
    {
        return $router->generate($this->gridRoute);
    }

    abstract protected function getTestEntity(): TestEntityDTO;

    abstract protected function getCreateEntityFormModifications(): array;

    abstract protected function getEntity(Crawler $tr, int $i): TestEntityDTO;
}
