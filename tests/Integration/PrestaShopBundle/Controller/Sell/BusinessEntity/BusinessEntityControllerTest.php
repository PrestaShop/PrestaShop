<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Sell\BusinessEntity;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\PrestaShopBundle\Controller\GridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\Resetter\BusinessEntityResetter;

class BusinessEntityControllerTest extends GridControllerTestCase
{
    private const DEFAULT_SHOP_ID = 1;
    private const DEFAULT_CUSTOMER_GROUP_ID = 1;
    private const DEFAULT_COUNTRY_ID = 8;

    private const SAVE_BUTTON_SELECTOR = 'save-button';

    private const ACTIVE_COMPANY_NAME = 'Grid active company';
    private const PENDING_COMPANY_NAME = 'Grid pending company';

    private static int $activeBusinessEntityId;

    private static int $pendingBusinessEntityId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        BusinessEntityResetter::resetBusinessEntities();

        $commandBus = self::bootKernel()->getContainer()->get('prestashop.core.command_bus');
        self::$activeBusinessEntityId = self::createBusinessEntity(
            $commandBus,
            self::ACTIVE_COMPANY_NAME,
            'Grid active legal name',
            BusinessEntityStatus::ACTIVE
        );
        self::$pendingBusinessEntityId = self::createBusinessEntity(
            $commandBus,
            self::PENDING_COMPANY_NAME,
            'Grid pending legal name',
            BusinessEntityStatus::PENDING
        );
        self::ensureKernelShutdown();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        BusinessEntityResetter::resetBusinessEntities();
    }

    public function testIndex(): void
    {
        $businessEntities = $this->getEntitiesFromGrid();

        $this->assertCount(2, $businessEntities);
        $this->assertCollectionContainsEntity($businessEntities, self::$activeBusinessEntityId);
        $this->assertCollectionContainsEntity($businessEntities, self::$pendingBusinessEntityId);
    }

    /**
     * @depends testIndex
     */
    public function testFilters(): void
    {
        $testCases = [
            [
                ['business_entity[id_business_entity]' => self::$activeBusinessEntityId],
                self::$activeBusinessEntityId,
            ],
            [
                ['business_entity[name]' => self::PENDING_COMPANY_NAME],
                self::$pendingBusinessEntityId,
            ],
            [
                ['business_entity[legal_name]' => 'Grid active legal name'],
                self::$activeBusinessEntityId,
            ],
            [
                ['business_entity[status]' => BusinessEntityStatus::ACTIVE->value],
                self::$activeBusinessEntityId,
            ],
        ];

        foreach ($testCases as [$testFilter, $expectedBusinessEntityId]) {
            $this->resetGridFilters();
            $businessEntities = $this->getFilteredEntitiesFromGrid($testFilter);
            $this->assertCount(1, $businessEntities, sprintf(
                'Expected exactly one business entity with filters %s',
                var_export($testFilter, true)
            ));
            $this->assertCollectionContainsEntity($businessEntities, $expectedBusinessEntityId);
        }

        $this->resetGridFilters();
        $this->assertTrue($this->getFilteredEntitiesFromGrid([
            'business_entity[name]' => 'No business entity bears this name',
        ])->isEmpty());

        $this->resetGridFilters();
    }

    /**
     * @depends testIndex
     */
    public function testEditPageIsPrefilled(): void
    {
        $crawler = $this->client->request('GET', $this->generateEditUrl(self::$activeBusinessEntityId));
        $this->assertResponseIsSuccessful();

        $formValues = $this->getFormByButton($crawler, self::SAVE_BUTTON_SELECTOR)->getValues();

        // AC3 lists six in-scope fields; asserting a subset would let a provider regression through.
        $this->assertSame(self::ACTIVE_COMPANY_NAME, $formValues[$this->fieldName('name')]);
        $this->assertSame('Grid active legal name', $formValues[$this->fieldName('legal_name')]);
        $this->assertSame(BusinessEntityStatus::ACTIVE->value, $formValues[$this->fieldName('status')]);
        $this->assertSame(
            (string) self::DEFAULT_CUSTOMER_GROUP_ID,
            $formValues[$this->fieldName('customer_group_id')]
        );
        $this->assertSame('', $formValues[$this->fieldName('external_ref')]);
        $this->assertSame('1', $formValues[$this->fieldName('delivery_authorized')]);
    }

    /**
     * @depends testEditPageIsPrefilled
     */
    public function testEditRedirectsToViewPageAndPersists(): void
    {
        $this->client->disableReboot();

        $editUrl = $this->generateEditUrl(self::$activeBusinessEntityId);
        $crawler = $this->client->request('GET', $editUrl);
        $this->assertResponseIsSuccessful();

        $form = $this->getFormByButton($crawler, self::SAVE_BUTTON_SELECTOR);
        $form[$this->fieldName('name')] = 'Grid renamed company';
        $form[$this->fieldName('legal_name')] = 'Grid renamed legal name';
        $form[$this->fieldName('external_ref')] = 'EXT-HTTP-1';
        $form[$this->fieldName('delivery_authorized')] = '0';
        $form[$this->fieldName('status')] = BusinessEntityStatus::PENDING->value;
        $this->client->submit($form);

        $this->assertResponseRedirects($this->router->generate(
            'admin_business_entities_view',
            ['businessEntityId' => self::$activeBusinessEntityId]
        ));

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Grid renamed company', $crawler->filter('body')->text());
        // AC5 also asks for a success message. Target the flash node AND its text: the admin layout
        // renders an empty #ajax_confirmation.alert-success on every page, so matching the class
        // alone would pass even with no flash at all.
        $this->assertStringContainsString(
            'Successful update.',
            $crawler->filter('.alert-success .alert-text')->text(),
            'A success message must be shown after a successful edit'
        );

        // Every edited field must survive the round-trip, not only the one that shows in the H1.
        $formValues = $this->getFormByButton(
            $this->client->request('GET', $editUrl),
            self::SAVE_BUTTON_SELECTOR
        )->getValues();
        $this->assertSame('Grid renamed company', $formValues[$this->fieldName('name')]);
        $this->assertSame('Grid renamed legal name', $formValues[$this->fieldName('legal_name')]);
        $this->assertSame('EXT-HTTP-1', $formValues[$this->fieldName('external_ref')]);
        $this->assertSame('0', $formValues[$this->fieldName('delivery_authorized')]);
        $this->assertSame(BusinessEntityStatus::PENDING->value, $formValues[$this->fieldName('status')]);

        $this->restoreActiveBusinessEntity($editUrl);
    }

    /**
     * @depends testEditRedirectsToViewPageAndPersists
     */
    public function testEditClearsTheExternalReference(): void
    {
        $this->client->disableReboot();

        $editUrl = $this->generateEditUrl(self::$activeBusinessEntityId);

        $form = $this->getFormByButton($this->client->request('GET', $editUrl), self::SAVE_BUTTON_SELECTOR);
        $form[$this->fieldName('external_ref')] = 'EXT-TO-CLEAR';
        $this->client->submit($form);
        $this->client->followRedirect();

        $form = $this->getFormByButton($this->client->request('GET', $editUrl), self::SAVE_BUTTON_SELECTOR);
        $form[$this->fieldName('external_ref')] = '';
        $this->client->submit($form);
        $this->client->followRedirect();

        $formValues = $this->getFormByButton(
            $this->client->request('GET', $editUrl),
            self::SAVE_BUTTON_SELECTOR
        )->getValues();
        $this->assertSame('', $formValues[$this->fieldName('external_ref')]);

        $this->restoreActiveBusinessEntity($editUrl);
    }

    /**
     * The edit tests share one fixture, so each of them puts it back the way it found it.
     */
    private function restoreActiveBusinessEntity(string $editUrl): void
    {
        $form = $this->getFormByButton($this->client->request('GET', $editUrl), self::SAVE_BUTTON_SELECTOR);
        $form[$this->fieldName('name')] = self::ACTIVE_COMPANY_NAME;
        $form[$this->fieldName('legal_name')] = 'Grid active legal name';
        $form[$this->fieldName('external_ref')] = '';
        $form[$this->fieldName('delivery_authorized')] = '1';
        $form[$this->fieldName('status')] = BusinessEntityStatus::ACTIVE->value;
        $this->client->submit($form);
        $this->client->followRedirect();
    }

    /**
     * @depends testEditPageIsPrefilled
     */
    public function testEditRejectsABlankRequiredField(): void
    {
        $this->client->disableReboot();

        $editUrl = $this->generateEditUrl(self::$activeBusinessEntityId);
        $form = $this->getFormByButton(
            $this->client->request('GET', $editUrl),
            self::SAVE_BUTTON_SELECTOR
        );
        $form[$this->fieldName('name')] = '';
        $this->client->submit($form);

        $this->assertResponseIsSuccessful();

        $formValues = $this->getFormByButton(
            $this->client->request('GET', $editUrl),
            self::SAVE_BUTTON_SELECTOR
        )->getValues();
        $this->assertSame(self::ACTIVE_COMPANY_NAME, $formValues[$this->fieldName('name')]);
    }

    /**
     * @depends testEditPageIsPrefilled
     */
    public function testEditPageOffersACancelLinkBackToTheViewPage(): void
    {
        $crawler = $this->client->request('GET', $this->generateEditUrl(self::$activeBusinessEntityId));
        $this->assertResponseIsSuccessful();

        $viewUrl = $this->router->generate(
            'admin_business_entities_view',
            ['businessEntityId' => self::$activeBusinessEntityId]
        );

        $this->assertNotEmpty(
            $crawler->filter(sprintf('a[href="%s"]', $viewUrl)),
            'The edit page must offer a link back to the view page'
        );
    }

    /**
     * @depends testIndex
     */
    public function testTheListAndTheViewPageBothLinkToTheEditPage(): void
    {
        // Admin urls carry a per-generation CSRF token, so the path is what identifies the target.
        $editPath = sprintf('/sell/business-entities/%d/edit', self::$activeBusinessEntityId);

        $listCrawler = $this->client->request('GET', $this->generateGridUrl());
        $this->assertResponseIsSuccessful();
        $this->assertCount(
            1,
            $listCrawler->filter(sprintf('%s a[href*="%s"]', $this->getGridSelector(), $editPath)),
            'The list row action must link to the edit page'
        );
        // AC1 asks for Edit to sit in the three-dots menu. The action column renders the FIRST
        // regular action outside the dropdown and the rest inside it, so a reorder in the grid
        // definition factory would silently promote Edit to the visible button: pin the dropdown.
        $this->assertCount(
            1,
            $listCrawler->filter(sprintf('%s .dropdown-menu a[href*="%s"]', $this->getGridSelector(), $editPath)),
            'AC1: the Edit action must sit inside the kebab menu, not as the visible button'
        );

        $viewCrawler = $this->client->request('GET', $this->router->generate(
            'admin_business_entities_view',
            ['businessEntityId' => self::$activeBusinessEntityId]
        ));
        $this->assertResponseIsSuccessful();
        $this->assertCount(
            1,
            $viewCrawler->filter(sprintf('.toolbar-icons a[href*="%s"]', $editPath)),
            'The view page toolbar must link to the edit page'
        );
    }

    private function generateEditUrl(int $businessEntityId): string
    {
        return $this->router->generate('admin_business_entities_edit', ['businessEntityId' => $businessEntityId]);
    }

    private function fieldName(string $field): string
    {
        $sections = [
            'name' => 'identity',
            'legal_name' => 'identity',
            'external_ref' => 'settings',
            'status' => 'settings',
            'customer_group_id' => 'settings',
            'delivery_authorized' => 'settings',
        ];

        return sprintf('business_entity[general_information][%s][%s]', $sections[$field], $field);
    }

    private static function createBusinessEntity(
        CommandBusInterface $commandBus,
        string $name,
        string $legalName,
        BusinessEntityStatus $status
    ): int {
        $businessEntityId = $commandBus->handle(new AddBusinessEntityCommand(
            $name,
            $legalName,
            null,
            true,
            $status,
            self::DEFAULT_SHOP_ID,
            self::DEFAULT_CUSTOMER_GROUP_ID,
            true,
            [
                new BusinessEntityBillingAddress(
                    'Billing',
                    '123 Main St',
                    null,
                    'Paris',
                    '75001',
                    self::DEFAULT_COUNTRY_ID,
                    true,
                    null
                ),
            ]
        ));

        return $businessEntityId->getValue();
    }

    protected function getFilterSearchButtonSelector(): string
    {
        return 'business_entity[actions][search]';
    }

    protected function generateGridUrl(array $routeParams = []): string
    {
        if (empty($routeParams)) {
            $routeParams = [
                'business_entity[offset]' => 0,
                'business_entity[limit]' => 100,
            ];
        }

        return $this->router->generate('admin_business_entities_list', $routeParams);
    }

    protected function getGridSelector(): string
    {
        return '#business_entity_grid_table';
    }

    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_business_entity')->text()),
            []
        );
    }
}
