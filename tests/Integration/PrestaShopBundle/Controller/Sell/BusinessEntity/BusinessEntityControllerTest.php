<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Sell\BusinessEntity;

use DateTime;
use PrestaShop\PrestaShop\Adapter\LegacyLogger;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\CannotDeleteBusinessEntityException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\CannotUpdateBusinessEntityException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetBusinessEntityForEditing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\EditableBusinessEntity;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\Sell\BusinessEntity\BusinessEntitiesController;
use PrestaShopBundle\Entity\B2B\B2bRole;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\B2B\BusinessEntityCustomerB2b;
use PrestaShopBundle\Entity\B2B\CustomerB2b;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Entity\Enum\CustomerB2bStatus;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\EditBusinessEntityStatusType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use ReflectionMethod;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Session\Session;
use Tests\Integration\PrestaShopBundle\Controller\GridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\Resetter\BusinessEntityResetter;
use Throwable;

class BusinessEntityControllerTest extends GridControllerTestCase
{
    private const DEFAULT_SHOP_ID = 1;
    private const DEFAULT_CUSTOMER_GROUP_ID = 1;
    private const DEFAULT_COUNTRY_ID = 8;

    private const SAVE_BUTTON_SELECTOR = 'save-button';

    private const UNKNOWN_BUSINESS_ENTITY_ID = 999999;

    private const LOGGED_EMPLOYEE_ID = 1;

    private const CREATION_SAMPLE_VALUES = ['FR12345678901', '123 456 789 00012', '12-345-6789'];

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
     * @depends testIndex
     */
    public function testTheCreationPageShowsTheSampleIdentifiers(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate('admin_business_entities_create'));
        $this->assertResponseIsSuccessful();

        foreach (self::CREATION_SAMPLE_VALUES as $sampleValue) {
            $this->assertCount(
                1,
                $crawler->filter(sprintf('input[placeholder="%s"]', $sampleValue)),
                sprintf('The creation page must show the sample "%s".', $sampleValue)
            );
        }
    }

    /**
     * @depends testIndex
     */
    public function testTheEditPageOfAnEntityWithoutIdentifiersShowsNoSampleValues(): void
    {
        $crawler = $this->client->request('GET', $this->generateEditUrl(self::$activeBusinessEntityId));
        $this->assertResponseIsSuccessful();

        foreach (self::CREATION_SAMPLE_VALUES as $sampleValue) {
            $this->assertCount(
                0,
                $crawler->filter(sprintf('input[placeholder="%s"]', $sampleValue)),
                sprintf('The creation sample "%s" must not appear on an edit page.', $sampleValue)
            );
        }
    }

    /**
     * @depends testIndex
     */
    public function testEditingAnUnknownEntityRedirectsToTheListingWithAnError(): void
    {
        $this->client->request('GET', $this->generateEditUrl(self::UNKNOWN_BUSINESS_ENTITY_ID));

        $this->assertResponseRedirects($this->router->generate('admin_business_entities_list'));

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $messages = $session->getFlashBag()->all();
        $this->assertArrayHasKey('error', $messages);
        $this->assertContains(
            'The object cannot be loaded (or found).',
            $messages['error'],
            print_r($messages['error'], true)
        );
    }

    /**
     * @depends testIndex
     */
    public function testAFailedWriteLeavesTheEditPageInsteadOfRedisplayingIt(): void
    {
        $this->client->disableReboot();

        $formHandler = $this->createMock(FormHandlerInterface::class);
        $formHandler->method('handleFor')->willThrowException(
            new CannotUpdateBusinessEntityException('Could not update business entity')
        );

        self::$kernel->getContainer()->set(
            'prestashop.core.form.identifiable_object.business_entity_form_handler',
            $formHandler
        );

        $this->client->request('POST', $this->generateEditUrl(self::$activeBusinessEntityId));

        $this->assertResponseRedirects($this->router->generate('admin_business_entities_list'));

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $messages = $session->getFlashBag()->all();
        $this->assertArrayHasKey('error', $messages);
        $this->assertContains(
            'An error occurred while updating the business entity.',
            $messages['error'],
            print_r($messages['error'], true)
        );
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

    public function testTheListOffersDeleteInTheKebabWithItsConfirmationModal(): void
    {
        $crawler = $this->client->request('GET', $this->generateGridUrl());
        $this->assertResponseIsSuccessful();

        $deleteAction = $crawler->filter(sprintf(
            '%s .dropdown-menu a[data-url*="/%d/delete"]',
            $this->getGridSelector(),
            self::$activeBusinessEntityId
        ));

        $this->assertCount(1, $deleteAction, 'AC1: Delete must sit inside the kebab menu');
        $this->assertSame('Delete this business entity', $deleteAction->attr('data-title'));
        $this->assertSame('Yes, I want to delete this entity', $deleteAction->attr('data-confirm-button-label'));
        $this->assertSame('btn-danger', $deleteAction->attr('data-confirm-button-class'));
        $this->assertSame(
            'Cancel',
            $deleteAction->attr('data-close-button-label'),
            'AC2 mandates a Cancel button; its label comes from a shared trait, so pin it against upstream drift'
        );
        $confirmMessage = (string) $deleteAction->attr('data-confirm-message');

        $this->assertStringContainsString(
            self::ACTIVE_COMPANY_NAME,
            $confirmMessage,
            'AC2: the confirmation message must name the entity'
        );
        $this->assertStringContainsString(
            '<strong>' . self::ACTIVE_COMPANY_NAME . '</strong>',
            $confirmMessage,
            'the emphasis must survive Twig and the DOM as live markup: escaping the whole message would break AC2'
        );
        $this->assertStringContainsString('<small class="text-muted">', $confirmMessage);
    }

    public function testTheBulkDeletionWarnsThatLinkedCustomersAreKept(): void
    {
        $crawler = $this->client->request('GET', $this->generateGridUrl());
        $this->assertResponseIsSuccessful();

        $bulkDelete = $crawler->filter('#business_entity_grid_bulk_action_delete_selection');

        $this->assertCount(1, $bulkDelete, 'the bulk delete action must be rendered');
        $this->assertSame('Delete selected', trim($bulkDelete->text()));
        $this->assertStringContainsString(
            'Linked B2B customers will be kept.',
            (string) $bulkDelete->attr('data-confirm-message'),
            'AC6: the bulk path must state the impact on linked customers too'
        );
    }

    public function testTheBulkCheckboxCarriesTheKeyTheControllerReads(): void
    {
        $crawler = $this->client->request('GET', $this->generateGridUrl());
        $this->assertResponseIsSuccessful();

        $this->assertGreaterThan(
            0,
            $crawler->filter(sprintf('%s input[name="business_entity_business_entities_bulk[]"]', $this->getGridSelector()))->count(),
            'The bulk checkbox name must match the key read by BusinessEntitiesController::bulkDeleteAction()'
        );

        $firstCell = $crawler->filter(sprintf('%s tbody tr td', $this->getGridSelector()))->first();
        $checkbox = $firstCell->filter('input.js-bulk-action-checkbox');

        $this->assertCount(
            1,
            $checkbox,
            'the bulk column must stay leftmost: a reorder in the definition factory would move the checkboxes'
        );

        $renderedIds = $crawler
            ->filter(sprintf('%s input.js-bulk-action-checkbox', $this->getGridSelector()))
            ->each(static fn (Crawler $input): ?string => $input->attr('value'));

        $this->assertContains(
            (string) self::$activeBusinessEntityId,
            $renderedIds,
            'the checkbox must carry the entity id: a wrong bulk_field renders an empty value and silently breaks every bulk deletion'
        );
    }

    public function testDeletingAnEntityRemovesItFromTheList(): void
    {
        $this->client->disableReboot();

        $commandBus = $this->client->getContainer()->get('prestashop.core.command_bus');
        $doomedId = self::createBusinessEntity($commandBus, 'Doomed company', 'Doomed legal name', BusinessEntityStatus::ACTIVE);

        $this->assertCollectionContainsEntity($this->getEntitiesFromGrid(), $doomedId);

        $this->deleteEntityFromPage('admin_business_entities_delete', ['businessEntityId' => $doomedId]);

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString(
            'Successful deletion.',
            $crawler->filter('.alert-success .alert-text')->text(),
            'AC4: a success message must be shown'
        );

        $remainingIds = [];
        foreach ($this->getEntitiesFromGrid() as $entity) {
            $remainingIds[] = $entity->getId();
        }

        $this->assertNotContains($doomedId, $remainingIds, 'AC4: the deleted entity must be gone from the list');
        $this->assertContains(
            self::$activeBusinessEntityId,
            $remainingIds,
            'a deletion that emptied the whole grid would otherwise satisfy the assertion above'
        );
    }

    public function testTheDeletionWarningOfAnEntityWithLinkedCustomersReachesTheList(): void
    {
        $this->client->disableReboot();

        $container = $this->client->getContainer();
        $entityId = self::createBusinessEntity(
            $container->get('prestashop.core.command_bus'),
            'Linked company',
            'Linked legal name',
            BusinessEntityStatus::ACTIVE
        );

        self::linkB2bCustomers($container->get('doctrine.orm.entity_manager'), $entityId, 2);

        $crawler = $this->client->request('GET', $this->generateGridUrl());
        $this->assertResponseIsSuccessful();

        $deleteAction = $crawler->filter(sprintf(
            '%s .dropdown-menu a[data-url*="/%d/delete"]',
            $this->getGridSelector(),
            $entityId
        ));

        $this->assertCount(1, $deleteAction);
        $this->assertStringContainsString(
            '2 linked B2B customer(s) will be kept.',
            (string) $deleteAction->attr('data-confirm-message'),
            'AC6: the count must reach the rendered attribute, not just the template source'
        );
    }

    public function testTheDeletionWarningIsAbsentWhenNoCustomerIsLinked(): void
    {
        $crawler = $this->client->request('GET', $this->generateGridUrl());
        $this->assertResponseIsSuccessful();

        $deleteAction = $crawler->filter(sprintf(
            '%s .dropdown-menu a[data-url*="/%d/delete"]',
            $this->getGridSelector(),
            self::$pendingBusinessEntityId
        ));

        $this->assertCount(1, $deleteAction);
        $this->assertStringNotContainsString(
            'will be kept',
            (string) $deleteAction->attr('data-confirm-message'),
            'an entity without linked customers must carry no AC6 warning'
        );
    }

    public function testAPartlyUnknownBulkSelectionFlashesBothTheSuccessAndTheSkippedCount(): void
    {
        $this->client->disableReboot();

        $container = $this->client->getContainer();
        $doomedId = self::createBusinessEntity(
            $container->get('prestashop.core.command_bus'),
            'Partly doomed company',
            'Partly doomed legal name',
            BusinessEntityStatus::ACTIVE
        );

        $this->bulkDeleteEntitiesFromPage(
            'admin_business_entities_bulk_delete',
            ['business_entity_business_entities_bulk' => [$doomedId, self::UNKNOWN_BUSINESS_ENTITY_ID]]
        );

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $messages = $session->getFlashBag()->all();

        $this->assertSame(
            ['The selection has been successfully deleted.'],
            $messages['success'] ?? [],
            'exactly one success must be announced, for the one entity that could be deleted'
        );
        $this->assertArrayHasKey('warning', $messages);
        $this->assertContains(
            '1 of the selected business entities could not be deleted.',
            $messages['warning'],
            print_r($messages['warning'], true)
        );

        $remainingIds = [];
        foreach ($this->getEntitiesFromGrid() as $entity) {
            $remainingIds[] = $entity->getId();
        }
        $this->assertNotContains($doomedId, $remainingIds);
    }

    public function testAFullyUnknownBulkSelectionFlashesTheSkippedCountAndNoSuccess(): void
    {
        $this->client->disableReboot();

        $this->bulkDeleteEntitiesFromPage(
            'admin_business_entities_bulk_delete',
            ['business_entity_business_entities_bulk' => [self::UNKNOWN_BUSINESS_ENTITY_ID, self::UNKNOWN_BUSINESS_ENTITY_ID - 1]]
        );

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $messages = $session->getFlashBag()->all();

        $this->assertArrayNotHasKey('success', $messages, 'nothing was deleted, so nothing may be announced as deleted');
        $this->assertContains(
            '2 of the selected business entities could not be deleted.',
            $messages['warning'] ?? [],
            print_r($messages, true)
        );
    }

    public function testAMerchantNameCarryingMarkupReachesTheModalAsTextNotAsHtml(): void
    {
        $this->client->disableReboot();

        $entityId = self::createBusinessEntity(
            $this->client->getContainer()->get('prestashop.core.command_bus'),
            'Tom & Jerry <script>alert(1)</script> "Ltd"',
            'Trapped legal name',
            BusinessEntityStatus::ACTIVE
        );

        $crawler = $this->client->request('GET', $this->generateGridUrl());
        $this->assertResponseIsSuccessful();

        $confirmMessage = (string) $crawler
            ->filter(sprintf('%s .dropdown-menu a[data-url*="/%d/delete"]', $this->getGridSelector(), $entityId))
            ->attr('data-confirm-message');

        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $confirmMessage);
        $this->assertStringNotContainsString('<script>', $confirmMessage, 'the modal assigns this with innerHTML');
        $this->assertStringContainsString('Tom &amp; Jerry', $confirmMessage);
    }

    public function testAFailedDeletionFlashesTheMappedDomainMessage(): void
    {
        $this->client->disableReboot();

        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->method('handle')->willThrowException(
            new CannotDeleteBusinessEntityException('Could not delete business entity')
        );

        self::$kernel->getContainer()->set('prestashop.core.command_bus', $commandBus);

        $this->deleteEntityFromPage(
            'admin_business_entities_delete',
            ['businessEntityId' => self::$activeBusinessEntityId]
        );

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $messages = $session->getFlashBag()->all();

        $this->assertContains(
            'An error occurred while deleting the business entity.',
            $messages['error'] ?? [],
            print_r($messages, true)
        );
    }

    public function testAnInfrastructureFailureStillFlashesInsteadOfReaching500(): void
    {
        $this->client->disableReboot();

        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->method('handle')->willThrowException(new RuntimeException('Connection lost'));

        self::$kernel->getContainer()->set('prestashop.core.command_bus', $commandBus);

        $this->deleteEntityFromPage(
            'admin_business_entities_delete',
            ['businessEntityId' => self::$activeBusinessEntityId]
        );

        $this->assertResponseRedirects($this->router->generate('admin_business_entities_list'));

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();

        $this->assertArrayHasKey(
            'error',
            $session->getFlashBag()->all(),
            'a failure outside the domain must reach the merchant as a message, not as a 500'
        );
    }

    public function testTheDeletionIsRecordedInTheAdministrationLog(): void
    {
        $this->client->disableReboot();

        $container = $this->client->getContainer();
        $entityId = self::createBusinessEntity(
            $container->get('prestashop.core.command_bus'),
            'Logged company',
            'Logged legal name',
            BusinessEntityStatus::ACTIVE
        );

        $this->deleteEntityFromPage('admin_business_entities_delete', ['businessEntityId' => $entityId]);

        $rows = $container->get('doctrine.dbal.default_connection')->fetchAllAssociative(
            sprintf(
                'SELECT message, object_type, object_id FROM %slog WHERE object_type = ? AND object_id = ? AND message = ?',
                _DB_PREFIX_
            ),
            ['BusinessEntity', $entityId, 'Business entity deleted successfully']
        );

        $this->assertCount(1, $rows, 'AC5: the deletion must reach the administration log itself');
        $this->assertSame((string) $entityId, (string) $rows[0]['object_id']);
    }

    public function testTheBulkDeletionIsRecordedInTheAdministrationLogToo(): void
    {
        $this->client->disableReboot();

        $container = $this->client->getContainer();
        $commandBus = $container->get('prestashop.core.command_bus');
        $firstId = self::createBusinessEntity($commandBus, 'Bulk logged one', 'Bulk logged legal one', BusinessEntityStatus::ACTIVE);
        $secondId = self::createBusinessEntity($commandBus, 'Bulk logged two', 'Bulk logged legal two', BusinessEntityStatus::ACTIVE);

        $this->bulkDeleteEntitiesFromPage(
            'admin_business_entities_bulk_delete',
            ['business_entity_business_entities_bulk' => [$firstId, $secondId]]
        );

        $loggedIds = $container->get('doctrine.dbal.default_connection')->fetchFirstColumn(
            sprintf(
                'SELECT object_id FROM %slog WHERE object_type = ? AND message = ? AND object_id IN (?, ?) ORDER BY object_id ASC',
                _DB_PREFIX_
            ),
            ['BusinessEntity', 'Business entity deleted successfully', $firstId, $secondId]
        );

        $this->assertSame(
            [$firstId, $secondId],
            array_map('intval', $loggedIds),
            'AC5: the bulk path must reach ps_log as well, once per deleted entity'
        );
    }

    public function testDeletingAnUnknownEntityRedirectsToTheListingWithAnError(): void
    {
        $this->deleteEntityFromPage(
            'admin_business_entities_delete',
            ['businessEntityId' => self::UNKNOWN_BUSINESS_ENTITY_ID]
        );

        $this->assertResponseRedirects($this->router->generate('admin_business_entities_list'));

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $messages = $session->getFlashBag()->all();

        $this->assertArrayHasKey('error', $messages);
        $this->assertContains(
            'The object cannot be loaded (or found).',
            $messages['error'],
            print_r($messages['error'], true)
        );
    }

    public function testBulkDeletingAnEmptySelectionAsksForASelectionInsteadOfClaimingSuccess(): void
    {
        $this->bulkDeleteEntitiesFromPage(
            'admin_business_entities_bulk_delete',
            ['business_entity_business_entities_bulk' => []]
        );

        $this->assertResponseRedirects($this->router->generate('admin_business_entities_list'));

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $messages = $session->getFlashBag()->all();

        $this->assertArrayNotHasKey(
            'success',
            $messages,
            'an empty selection deletes nothing, so it must never report a success'
        );
        $this->assertArrayHasKey('error', $messages, print_r($messages, true));
        $this->assertContains(
            'You must select at least one element to delete.',
            $messages['error'],
            print_r($messages['error'], true)
        );
    }

    public function testBulkDeletingEntitiesRemovesThemFromTheList(): void
    {
        $this->client->disableReboot();

        $commandBus = $this->client->getContainer()->get('prestashop.core.command_bus');
        $firstId = self::createBusinessEntity($commandBus, 'Bulk doomed one', 'Bulk legal one', BusinessEntityStatus::ACTIVE);
        $secondId = self::createBusinessEntity($commandBus, 'Bulk doomed two', 'Bulk legal two', BusinessEntityStatus::ACTIVE);

        $this->bulkDeleteEntitiesFromPage(
            'admin_business_entities_bulk_delete',
            ['business_entity_business_entities_bulk' => [$firstId, $secondId]]
        );

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $messages = $session->getFlashBag()->all();

        $this->assertSame(['The selection has been successfully deleted.'], $messages['success'] ?? []);
        $this->assertArrayNotHasKey('warning', $messages, 'nothing was skipped, so nothing may be reported as skipped');

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $remainingIds = [];
        foreach ($this->getEntitiesFromGrid() as $entity) {
            $remainingIds[] = $entity->getId();
        }

        $this->assertNotContains($firstId, $remainingIds);
        $this->assertNotContains($secondId, $remainingIds);
        $this->assertContains(
            self::$activeBusinessEntityId,
            $remainingIds,
            'a bulk delete that emptied the whole grid would otherwise satisfy both assertions above'
        );
    }

    private static function linkB2bCustomers(object $entityManager, int $businessEntityId, int $count): void
    {
        $businessEntity = $entityManager->find(BusinessEntity::class, $businessEntityId);

        $role = new B2bRole();
        $role->setRole(sprintf('grid-member-%d', $businessEntityId));
        $entityManager->persist($role);

        for ($i = 1; $i <= $count; ++$i) {
            // ps_customer_b2b carries a unique index on id_customer, so the ids are offset far
            // above the fixtures to stay distinct from whatever the other tests created.
            $customerB2b = new CustomerB2b();
            $customerB2b->setIdCustomer(900000 + $businessEntityId * 10 + $i);
            $customerB2b->setStatus(CustomerB2bStatus::ACTIVE);
            $customerB2b->setCreatedAt(new DateTime());
            $customerB2b->setUpdatedAt(new DateTime());
            $entityManager->persist($customerB2b);

            $link = new BusinessEntityCustomerB2b();
            $link->setBusinessEntity($businessEntity);
            $link->setCustomerB2b($customerB2b);
            $link->setB2bRole($role);
            $link->setCreatedAt(new DateTime());
            $link->setUpdatedAt(new DateTime());
            $entityManager->persist($link);
        }

        $entityManager->flush();
    }

    public function testTheListOffersEditStatusInTheKebabWithAStableHook(): void
    {
        $crawler = $this->client->request('GET', $this->generateGridUrl());
        $this->assertResponseIsSuccessful();

        $editStatus = $crawler->filter(sprintf(
            '%s .dropdown-menu a[data-status-form-url*="/%d/status"]',
            $this->getGridSelector(),
            self::$activeBusinessEntityId
        ));

        $this->assertCount(1, $editStatus, 'AC1: Edit status must sit inside the kebab menu');

        // aria-label rather than text(): the link also carries the material icon ligature.
        $this->assertSame(
            'Edit status',
            $editStatus->attr('aria-label'),
            'AC1: the kebab action must read "Edit status"'
        );

        // The JavaScript hooks on this class and never on the one built from the translated
        // label, which changes with the language while the hook must not.
        $this->assertStringContainsString('js-edit-business-entity-status', (string) $editStatus->attr('class'));

        // FormIframeModal is handed its wording through these, and has no wording of its own:
        // a missing label leaves the dialog without the button, which nothing else would catch.
        $this->assertSame('Edit status', $editStatus->attr('data-modal-title'));
        $this->assertSame('Update status', $editStatus->attr('data-confirm-button-label'));
        $this->assertSame('Cancel', $editStatus->attr('data-cancel-button-label'));
    }

    /**
     * FormIframeModal reaches into the loaded page for `form[name="edit_business_entity_status"]`,
     * and silently does nothing when it finds none: the confirm button would stop submitting with
     * no error anywhere. The form name comes from the type's block prefix, which a rename changes.
     */
    public function testTheStatusPageCarriesTheFormTheModalLooksFor(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate(
            'admin_business_entities_status',
            ['businessEntityId' => self::$activeBusinessEntityId, 'liteDisplaying' => 1]
        ));
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('form[name="edit_business_entity_status"]'));
    }

    public function testAStatusSubmissionForAnUnknownEntityIsNotFound(): void
    {
        $this->client->disableReboot();

        $crawler = $this->client->request('GET', $this->router->generate(
            'admin_business_entities_status',
            ['businessEntityId' => self::$activeBusinessEntityId, 'liteDisplaying' => 1]
        ));
        $this->assertResponseIsSuccessful();

        $this->client->request(
            'POST',
            $this->router->generate(
                'admin_business_entities_status',
                ['businessEntityId' => self::UNKNOWN_BUSINESS_ENTITY_ID, 'liteDisplaying' => 1]
            ),
            ['edit_business_entity_status' => [
                'status' => BusinessEntityStatus::ACTIVE->value,
                '_token' => $crawler->filter('#edit_business_entity_status__token')->attr('value'),
            ]]
        );

        // The entity is read before the form is even built, so the write answers like the read.
        $this->assertResponseStatusCodeSame(404);
        $this->assertSame(
            'The object cannot be loaded (or found).',
            trim($this->client->getCrawler()->filter('.alert-danger .alert-text')->text()),
            'the frame must show a rendered message, not a bare sentence'
        );
    }

    public function testTheStatusPageIsPrefilledWithTheCurrentStatus(): void
    {
        // Two fixtures holding two different statuses: against a single one, a hard coded
        // pre-selection would satisfy the assertion by coincidence.
        $this->assertStatusPagePreselects(
            self::$activeBusinessEntityId,
            BusinessEntityStatus::ACTIVE,
            self::ACTIVE_COMPANY_NAME
        );
        $this->assertStatusPagePreselects(
            self::$pendingBusinessEntityId,
            BusinessEntityStatus::PENDING,
            self::PENDING_COMPANY_NAME
        );
    }

    private function assertStatusPagePreselects(
        int $entityId,
        BusinessEntityStatus $expectedStatus,
        string $expectedName
    ): void {
        $crawler = $this->client->request('GET', $this->router->generate(
            'admin_business_entities_status',
            ['businessEntityId' => $entityId, 'liteDisplaying' => 1]
        ));

        $this->assertResponseIsSuccessful();

        $selected = $crawler->filter('#edit_business_entity_status_status option[selected]');
        $this->assertCount(1, $selected, 'AC2: exactly one status must be pre-selected');
        $this->assertSame(
            $expectedStatus->value,
            $selected->attr('value'),
            'AC2: the pre-selected status must be the one the entity currently holds'
        );

        // Pinning the whole sentence rather than the name alone: dropping |raw_purified would
        // render the markup literally, and a containment check would stay green.
        $this->assertSame(
            sprintf('Change the status of %s', $expectedName),
            trim($crawler->filter('p.mb-3')->text()),
            'AC2: the context line must name the entity'
        );
        $this->assertSame(
            $expectedName,
            trim($crawler->filter('p.mb-3 strong')->text()),
            'AC2: the entity name must stand out in the context line'
        );
        $this->assertSame(
            '* Status',
            trim($crawler->filter('label[for="edit_business_entity_status_status"]')->text()),
            'AC2: the dropdown must carry the label the other screens use, marked required'
        );
        $this->assertSame(
            ['Pending', 'Active', 'Inactive', 'Rejected'],
            $crawler->filter('#edit_business_entity_status_status option')->each(
                static fn (Crawler $option): string => trim($option->text())
            ),
            'AC2: the four statuses must be offered, translated, in the order the story lists'
        );
        // AC2 lists the status as the only input of the modal. Counting the visible controls keeps
        // a field re-added to the type from slipping back into the modal unnoticed.
        $this->assertCount(
            0,
            $crawler->filter('form[name="edit_business_entity_status"] input:not([type="hidden"]),'
                . ' form[name="edit_business_entity_status"] textarea'),
            'AC2: the modal must offer the status dropdown and nothing else'
        );
    }

    public function testTheStatusPageOfAnUnknownEntityIsNotFound(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate(
            'admin_business_entities_status',
            ['businessEntityId' => self::UNKNOWN_BUSINESS_ENTITY_ID, 'liteDisplaying' => 1]
        ));

        $this->assertResponseStatusCodeSame(404);
        $this->assertSame(
            'The object cannot be loaded (or found).',
            trim($crawler->filter('.alert-danger .alert-text')->text()),
            'the frame must show a rendered message, not a bare sentence'
        );
    }

    /**
     * The 404 page is a modal body, with no back-office navigation around it: outside the frame the
     * merchant is sent back where the edit form sends them.
     */
    public function testTheStatusPageOfAnUnknownEntityOutsideTheModalRedirectsToTheListing(): void
    {
        $this->client->request('GET', $this->router->generate(
            'admin_business_entities_status',
            ['businessEntityId' => self::UNKNOWN_BUSINESS_ENTITY_ID]
        ));

        $this->assertResponseRedirects($this->router->generate('admin_business_entities_list'));

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $messages = $session->getFlashBag()->all();
        $this->assertArrayHasKey('error', $messages);
        $this->assertContains(
            'The object cannot be loaded (or found).',
            $messages['error'],
            print_r($messages['error'], true)
        );
    }

    /**
     * The confirm button belongs to the modal. Opened as a page, the same route has to carry a
     * button of its own, or the form it renders cannot be submitted at all.
     */
    public function testTheStatusPageOutsideTheModalCarriesItsOwnSubmitButton(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate(
            'admin_business_entities_status',
            ['businessEntityId' => self::$activeBusinessEntityId]
        ));
        $this->assertResponseIsSuccessful();

        $this->assertCount(
            1,
            $crawler->filter('form[name="edit_business_entity_status"] button[type="submit"]'),
            'the page must offer the button the modal would otherwise provide'
        );
        $this->assertCount(
            1,
            $crawler->filter('form[name="edit_business_entity_status"] .cancel-btn'),
            'and a way out that does not go through the browser history'
        );
    }

    public function testUpdatingTheStatusFlashesAndShowsTheNewBadgeInTheList(): void
    {
        $this->client->disableReboot();

        $entityId = self::createBusinessEntity(
            $this->client->getContainer()->get('prestashop.core.command_bus'),
            'Status change company',
            'Status change legal name',
            BusinessEntityStatus::PENDING
        );

        $this->submitStatusChange($entityId, BusinessEntityStatus::ACTIVE);

        $this->assertResponseRedirects($this->router->generate('admin_business_entities_list'));

        $crawler = $this->client->request('GET', $this->generateGridUrl());

        $flash = $crawler->filter('.alert-success .alert-text');
        $this->assertCount(
            1,
            $flash,
            'AC4: the success flash must reach the page the browser lands on, not just the flash bag'
        );
        $this->assertStringContainsString('Successful update.', $flash->text());

        $row = $crawler->filter(sprintf('%s tbody tr:contains("Status change company")', $this->getGridSelector()));

        $this->assertCount(1, $row);
        $this->assertSame(
            'Active',
            trim($row->filter('.column-status')->text()),
            'AC4: the listing must reflect the new status'
        );
    }

    /**
     * The modal reloads the lite page after the write, and the layout *consumes* the flash bag
     * when it renders its alerts: shown there, inside a frame nobody reads, the message would be
     * gone by the time the merchant is back on the list.
     */
    public function testTheSuccessMessageSurvivesTheReloadOfTheModalAndReachesTheList(): void
    {
        $this->client->disableReboot();

        $entityId = self::createBusinessEntity(
            $this->client->getContainer()->get('prestashop.core.command_bus'),
            'Lite display company',
            'Lite display legal name',
            BusinessEntityStatus::PENDING
        );

        $crawler = $this->client->request('GET', $this->router->generate(
            'admin_business_entities_status',
            ['businessEntityId' => $entityId, 'liteDisplaying' => 1]
        ));
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name="edit_business_entity_status"]')->form();
        $form['edit_business_entity_status[status]'] = BusinessEntityStatus::ACTIVE->value;
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertCount(
            0,
            $crawler->filter('.alert-success'),
            'the reloaded modal must not display the alert, which would consume it'
        );
        // One write, one success alert: what the modal reads to know it can close.
        $this->assertSame(
            '1',
            $crawler->filter('form[name="edit_business_entity_status"]')->attr('data-alerts-success'),
            'AC4: the reloaded form must carry exactly the one success alert the modal waits for'
        );

        $crawler = $this->client->request('GET', $this->generateGridUrl());

        $this->assertStringContainsString(
            'Successful update.',
            $crawler->filter('.alert-success .alert-text')->text(),
            'the message must still be in the bag when the list reloads'
        );
    }

    public function testTheStatusChangeIsRecordedInTheAdministrationLog(): void
    {
        $this->client->disableReboot();

        $container = $this->client->getContainer();
        $entityId = self::createBusinessEntity(
            $container->get('prestashop.core.command_bus'),
            'Status logged company',
            'Status logged legal name',
            BusinessEntityStatus::PENDING
        );

        $this->submitStatusChange($entityId, BusinessEntityStatus::REJECTED);

        $entries = $this->statusLogEntriesFor($entityId);

        $this->assertCount(1, $entries, 'AC6: the status change must reach the administration log');
        $this->assertStringContainsString('"old":"pending"', $entries[0]['message']);
        $this->assertStringContainsString('"new":"rejected"', $entries[0]['message']);
        $this->assertSame(
            self::LOGGED_EMPLOYEE_ID,
            (int) $entries[0]['id_employee'],
            'AC6: the log must name the employee who changed the status'
        );
    }

    /**
     * Pins AC6's "every change" against a handler that would start logging conditionally. It does
     * NOT prove the allow_duplicate flag: PrestaShopLogger::isPresent() compares id_shop_group to
     * (int) null while the column holds NULL, so its deduplication never fires here and removing
     * the flag leaves this green. The flag is guarded by the unit test on the logger context.
     */
    public function testEveryStatusChangeOfTheSameEntityIsLogged(): void
    {
        $this->client->disableReboot();

        $entityId = self::createBusinessEntity(
            $this->client->getContainer()->get('prestashop.core.command_bus'),
            'Status repeated company',
            'Status repeated legal name',
            BusinessEntityStatus::PENDING
        );

        $this->submitStatusChange($entityId, BusinessEntityStatus::ACTIVE);
        $this->submitStatusChange($entityId, BusinessEntityStatus::PENDING);
        $this->submitStatusChange($entityId, BusinessEntityStatus::ACTIVE);

        $this->assertCount(
            3,
            $this->statusLogEntriesFor($entityId),
            'AC6: every status change must be logged, including one repeating an earlier pair'
        );
    }

    public function testResubmittingTheStatusItAlreadyHoldsWritesNoAuditEntry(): void
    {
        $this->client->disableReboot();

        $entityId = self::createBusinessEntity(
            $this->client->getContainer()->get('prestashop.core.command_bus'),
            'Status unchanged company',
            'Status unchanged legal name',
            BusinessEntityStatus::PENDING
        );

        // The modal preselects the current status, so this is what one click on "Update status"
        // sends. AC6 traces status changes: an entry naming none would be noise.
        $this->submitStatusChange($entityId, BusinessEntityStatus::PENDING);

        $this->assertResponseRedirects($this->router->generate('admin_business_entities_list'));
        $this->assertSame(
            [],
            $this->statusLogEntriesFor($entityId),
            'AC6: a submission that changes nothing must leave the administration log alone'
        );
    }

    public function testAStatusSubmissionWithoutItsTokenIsRejected(): void
    {
        $this->client->disableReboot();

        $container = $this->client->getContainer();
        $entityId = self::createBusinessEntity(
            $container->get('prestashop.core.command_bus'),
            'Status untokened company',
            'Status untokened legal name',
            BusinessEntityStatus::PENDING
        );

        $this->client->request(
            'POST',
            $this->router->generate('admin_business_entities_status', ['businessEntityId' => $entityId]),
            ['edit_business_entity_status' => ['status' => BusinessEntityStatus::ACTIVE->value]]
        );

        $this->assertResponseIsSuccessful();
        $this->assertArrayNotHasKey(
            'success',
            $this->flashes(),
            'a submission without its token must not report a success'
        );

        // Root errors belong to the form, not to any field: without form_errors the merchant
        // reads nothing at all and the modal looks like it did nothing.
        $this->assertCount(
            1,
            $this->client->getCrawler()->filter('.business-entity-status-form .alert-danger'),
            'a rejected token must be reported in the modal'
        );

        $this->assertSame(
            BusinessEntityStatus::PENDING->value,
            $this->persistedStatusOf($entityId),
            'a submission without its token must leave the status untouched'
        );
    }

    public function testAnInvalidSubmissionIsRejectedWithoutTouchingTheStatus(): void
    {
        $this->client->disableReboot();

        $entityId = self::createBusinessEntity(
            $this->client->getContainer()->get('prestashop.core.command_bus'),
            'Status kept input company',
            'Status kept input legal name',
            BusinessEntityStatus::PENDING
        );

        $crawler = $this->client->request('GET', $this->router->generate(
            'admin_business_entities_status',
            ['businessEntityId' => $entityId]
        ));
        $this->assertResponseIsSuccessful();

        $this->client->request(
            'POST',
            $this->router->generate('admin_business_entities_status', ['businessEntityId' => $entityId]),
            ['edit_business_entity_status' => [
                EditBusinessEntityStatusType::FIELD_STATUS => 'not-a-status',
                '_token' => $crawler->filter('#edit_business_entity_status__token')->attr('value'),
            ]]
        );

        // Symfony renders the rejection in place, the way it does on every other admin form.
        $this->assertResponseIsSuccessful();
        $form = $this->client->getCrawler()->filter('form[name="edit_business_entity_status"]');

        $this->assertSame('1', $form->attr('data-form-submitted'));
        $this->assertSame(
            '0',
            $form->attr('data-form-valid'),
            'the refused submission must come back as an invalid form'
        );
        $this->assertSame(
            '0',
            $form->attr('data-alerts-success'),
            'and must not look successful to the modal, which watches this attribute'
        );

        $this->assertSame(
            BusinessEntityStatus::PENDING->value,
            $this->persistedStatusOf($entityId),
            'nothing must be written when the submission is rejected'
        );
    }

    /**
     * The PageVoter of the test environment grants every permission, so a denial never reaches the
     * listener here and the attributes themselves are all there is to check.
     */
    public function testTheStatusActionIsGuardedAsAWrite(): void
    {
        $action = new ReflectionMethod(BusinessEntitiesController::class, 'statusAction');

        $security = $action->getAttributes(AdminSecurity::class);
        $this->assertCount(1, $security, 'the status action must stay guarded');
        $this->assertStringContainsString(
            "is_granted('update', 'AdminBusinessEntities')",
            (string) $security[0]->getArguments()[0],
            'reading the form is already a step towards writing: it asks for the update permission'
        );

        $this->assertCount(
            1,
            $action->getAttributes(DemoRestricted::class),
            'the action writes, so it must stay closed to the demo mode'
        );
    }

    public function testAFailedStatusUpdateAnswersWithTheMappedDomainMessage(): void
    {
        $this->client->disableReboot();
        $this->substituteBusFailingWith(
            new CannotUpdateBusinessEntityException('Could not update business entity')
        );

        // In the modal, where the layout renders no alert: a flashed message would survive the
        // request and surface on whatever page the merchant loads next.
        $this->submitStatusChange(self::$activeBusinessEntityId, BusinessEntityStatus::REJECTED, true);

        $this->assertResponseIsSuccessful();

        $this->assertStringContainsString(
            'An error occurred while updating the business entity.',
            (string) $this->client->getResponse()->getContent(),
            'the merchant must read the mapped domain message, not a generic one'
        );
        $this->assertSame(
            '0',
            $this->client->getCrawler()->filter('form[name="edit_business_entity_status"]')->attr('data-alerts-success'),
            'a rejected update must not leave a success alert on the form the modal watches'
        );
        $this->assertArrayNotHasKey(
            'error',
            $this->flashes(),
            'the message belongs to the modal, not to the next page the merchant loads'
        );
    }

    public function testAnInfrastructureFailureDuringAStatusUpdateIsReportedInTheModal(): void
    {
        $this->client->disableReboot();
        $this->substituteBusFailingWith(new RuntimeException('Connection lost'));

        $this->submitStatusChange(self::$activeBusinessEntityId, BusinessEntityStatus::REJECTED);

        $this->assertResponseIsSuccessful();
        $this->assertCount(
            1,
            $this->client->getCrawler()->filter('.business-entity-status-form .alert-danger'),
            'a failure outside the domain must reach the merchant as a message, not as a 500'
        );
    }

    /**
     * The handler takes its logger from the container, and anything it could report the failure to
     * ends up in the same table through the legacy Monolog handler. Only the real wiring shows that
     * an unwritable administration log leaves the status change alone.
     */
    public function testAnUnwritableAdministrationLogStillPersistsTheStatus(): void
    {
        $this->client->disableReboot();

        // Narrowed to the update entry so the fixture below can still be created and logged: the
        // substitution has to be in place before any request, the service being shared.
        $refusedWrites = 0;
        $logger = $this->createMock(LegacyLogger::class);
        $logger->method('info')->willReturnCallback(static function (string $message) use (&$refusedWrites): void {
            if (str_starts_with($message, 'Business entity updated successfully')) {
                ++$refusedWrites;

                throw new RuntimeException('ps_log is unwritable');
            }
        });

        self::$kernel->getContainer()->set('prestashop.adapter.legacy.logger', $logger);

        $entityId = self::createBusinessEntity(
            $this->client->getContainer()->get('prestashop.core.command_bus'),
            'Unwritable log company',
            'Unwritable log legal name',
            BusinessEntityStatus::PENDING
        );

        $this->submitStatusChange($entityId, BusinessEntityStatus::REJECTED);

        $this->assertResponseRedirects($this->router->generate('admin_business_entities_list'));
        $this->assertSame(
            1,
            $refusedWrites,
            'the update entry must have reached the failing logger, or this test proves nothing'
        );

        $flashes = $this->flashes();
        $this->assertArrayHasKey(
            'success',
            $flashes,
            'an unwritable log must not turn a saved edit into a reported failure'
        );
        $this->assertArrayNotHasKey('error', $flashes);
        $this->assertSame(
            BusinessEntityStatus::REJECTED->value,
            $this->persistedStatusOf($entityId),
            'the status must stay saved even though its log entry could not be written'
        );
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function flashes(): array
    {
        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();

        return $session->getFlashBag()->all();
    }

    private function persistedStatusOf(int $entityId): string
    {
        return (string) $this->client->getContainer()->get('doctrine.dbal.default_connection')->fetchOne(
            sprintf('SELECT status FROM %sbusiness_entity WHERE id_business_entity = ?', _DB_PREFIX_),
            [$entityId]
        );
    }

    private function submitStatusChange(int $entityId, BusinessEntityStatus $status, bool $inModal = false): Crawler
    {
        $crawler = $this->client->request('GET', $this->router->generate(
            'admin_business_entities_status',
            array_filter(['businessEntityId' => $entityId, 'liteDisplaying' => $inModal ? 1 : null])
        ));
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name="edit_business_entity_status"]')->form();
        $form['edit_business_entity_status[status]'] = $status->value;

        return $this->client->submit($form);
    }

    /**
     * The modal reads the entity through the same bus it writes through, so the substitute has to
     * keep answering the query for the status page to be rendered at all.
     */
    private function substituteBusFailingWith(Throwable $failure): void
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->method('handle')->willReturnCallback(
            static function (object $message) use ($failure): EditableBusinessEntity {
                if ($message instanceof GetBusinessEntityForEditing) {
                    return new EditableBusinessEntity(
                        1,
                        'Failing Corp',
                        null,
                        null,
                        true,
                        BusinessEntityStatus::ACTIVE,
                        1,
                        self::DEFAULT_SHOP_ID
                    );
                }

                throw $failure;
            }
        );

        self::$kernel->getContainer()->set('prestashop.core.command_bus', $commandBus);
    }

    /**
     * @return array<int, array{message: string, id_employee: int}>
     */
    private function statusLogEntriesFor(int $entityId): array
    {
        return $this->client->getContainer()->get('doctrine.dbal.default_connection')->fetchAllAssociative(
            sprintf(
                'SELECT message, id_employee FROM %slog WHERE object_type = ? AND object_id = ? AND message LIKE ? ORDER BY id_log ASC',
                _DB_PREFIX_
            ),
            ['BusinessEntity', $entityId, 'Business entity updated successfully%']
        );
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
