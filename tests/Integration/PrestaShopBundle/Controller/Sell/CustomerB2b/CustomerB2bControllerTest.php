<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Sell\CustomerB2b;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Command\AddCustomerB2bCommand;
use PrestaShopBundle\Entity\B2B\CustomerB2b;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Entity\Enum\CustomerB2bStatus;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\Utility\LoginTrait;
use Tests\Resources\DatabaseDump;
use Tests\Resources\Resetter\BusinessEntityResetter;
use Tests\TestCase\SymfonyIntegrationTestCase;

class CustomerB2bControllerTest extends SymfonyIntegrationTestCase
{
    use LoginTrait;

    private const SHOP_ID = 1;
    private const SHOP_CUSTOMER_GROUP_ID = 3;
    private const BUSINESS_ENTITY_GROUP_ID = 1;
    private const COUNTRY_ID = 8;
    private const BUYER_ROLE_ID = 3;

    private const FIXTURE_CUSTOMER_EMAIL = 'pub@prestashop.com';

    private const SAVE_BUTTON_SELECTOR = 'save-button';
    // The empty customer search has no named input, so the field is found by its container
    private const EXISTING_CUSTOMER_WIDGET_SELECTOR = '#customer_b2b_existing_customer';

    private static int $businessEntityId;

    private RouterInterface $router;
    private Connection $connection;
    private EntityManagerInterface $entityManager;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::bootKernel();
        self::$businessEntityId = self::createBusinessEntity();
        self::ensureKernelShutdown();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        BusinessEntityResetter::resetBusinessEntities();
        DatabaseDump::restoreTables(['customer', 'customer_b2b', 'customer_group', 'log']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        // A reboot between the display and the submission of the form would invalidate its CSRF token
        $this->client->disableReboot();
        $this->loginUser($this->client);
        $this->router = $this->client->getContainer()->get('router');
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $this->connection = $this->entityManager->getConnection();
    }

    public function testCreationPageDefaultsToNewCustomer(): void
    {
        $crawler = $this->client->request('GET', $this->generateCreateUrl());
        $this->assertResponseIsSuccessful();

        $formValues = $crawler->selectButton(self::SAVE_BUTTON_SELECTOR)->form()->getValues();
        $this->assertSame('0', $formValues['customer_b2b[use_existing_customer]']);
        $this->assertSame('1', $formValues['customer_b2b[send_password_setup_link]'], 'The password setup link should be sent by default');
        $this->assertSame(CustomerB2bStatus::ACTIVE->value, $formValues['customer_b2b[status]']);
    }

    public function testNewCustomerIsCreatedWithProfile(): void
    {
        $email = 'new@prestashop.com';

        $this->submitCreationForm(
            [
                'use_existing_customer' => '0',
                'customer' => [
                    'gender_id' => '1',
                    'first_name' => 'new',
                    'last_name' => 'customer',
                    'email' => $email,
                ],
                'business_entity_id' => (string) self::$businessEntityId,
                'role_id' => (string) self::BUYER_ROLE_ID,
                'status' => CustomerB2bStatus::PENDING->value,
            ],
            ['existing_customer', 'send_password_setup_link'],
        );

        $this->assertResponseRedirects($this->router->generate('admin_customer_b2b_list'));
        $this->assertSame(['Successful creation'], $this->getFlashMessages()['success'] ?? []);

        $customer = $this->findCustomerByEmail($email);
        $this->assertNotNull($customer);
        $this->assertSame(self::SHOP_ID, (int) $customer['id_shop']);

        $profile = $this->findProfile((int) $customer['id_customer']);
        $this->assertNotNull($profile);
        $this->assertSame(CustomerB2bStatus::PENDING, $profile->getStatus());
        $this->assertLinkedTo(self::$businessEntityId, $profile);

        $logs = $this->findLogs($profile->getId());
        $this->assertCount(1, $logs, 'The creation should have been logged once');
        $this->assertStringStartsWith('B2B customer created', $logs[0]['message']);
        $this->assertSame($this->getLoggedInEmployeeId(), (int) $logs[0]['id_employee'], 'The current user ID was not logged');
    }

    public function testExistingCustomerGetsProfile(): void
    {
        $customerId = $this->createCustomer('existing@prestashop.com');
        $customerCount = $this->countRows('customer');

        $this->submitCreationForm(
            [
                'use_existing_customer' => '1',
                'existing_customer' => [['id_customer' => (string) $customerId]],
                'business_entity_id' => (string) self::$businessEntityId,
                'role_id' => (string) self::BUYER_ROLE_ID,
            ],
            ['customer', 'send_password_setup_link'],
        );

        $this->assertResponseRedirects($this->router->generate('admin_customer_b2b_list'));
        $this->assertSame($customerCount, $this->countRows('customer'));

        $profile = $this->findProfile($customerId);
        $this->assertNotNull($profile);
        $this->assertSame(CustomerB2bStatus::ACTIVE, $profile->getStatus());
        $this->assertLinkedTo(self::$businessEntityId, $profile);
    }

    public function testTakenEmailIsReportedAtEmailField(): void
    {
        $customerCount = $this->countRows('customer');
        $profileCount = $this->countRows('customer_b2b');

        $crawler = $this->submitCreationForm(
            [
                'use_existing_customer' => '0',
                'customer' => [
                    'first_name' => 'duplicate',
                    'last_name' => 'customer',
                    'email' => self::FIXTURE_CUSTOMER_EMAIL,
                ],
                'business_entity_id' => (string) self::$businessEntityId,
                'role_id' => (string) self::BUYER_ROLE_ID,
            ],
            ['existing_customer', 'send_password_setup_link'],
        );

        $this->assertResponseStatusCodeSame(422);
        $emailRow = $this->getFieldRowText($crawler, '[name="customer_b2b[customer][email]"]');
        $this->assertStringContainsString(
            \sprintf('You can\'t create a registered customer with email "%s"', self::FIXTURE_CUSTOMER_EMAIL),
            $emailRow,
        );
        $this->assertNoErrorFlash($crawler);

        $this->assertSame($customerCount, $this->countRows('customer'));
        $this->assertSame($profileCount, $this->countRows('customer_b2b'));
    }

    public function testExistingProfileIsReportedAtCustomerSearch(): void
    {
        $customerId = $this->createCustomer('profiled@prestashop.com');
        $this->getCommandBus()->handle(new AddCustomerB2bCommand($customerId, self::$businessEntityId, self::BUYER_ROLE_ID));
        $profileCount = $this->countRows('customer_b2b');
        $linkCount = $this->countRows('business_entity_customer_b2b');

        $crawler = $this->submitCreationForm(
            [
                'use_existing_customer' => '1',
                'existing_customer' => [['id_customer' => (string) $customerId]],
                'business_entity_id' => (string) self::$businessEntityId,
                'role_id' => (string) self::BUYER_ROLE_ID,
            ],
            ['customer', 'send_password_setup_link'],
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertStringContainsString(
            'This customer already has a B2B profile.',
            $this->getFieldRowText($crawler, self::EXISTING_CUSTOMER_WIDGET_SELECTOR),
        );
        $this->assertNoErrorFlash($crawler);

        $this->assertSame($profileCount, $this->countRows('customer_b2b'));
        $this->assertSame($linkCount, $this->countRows('business_entity_customer_b2b'));
    }

    public function testExistingCustomerIsRequired(): void
    {
        $customerCount = $this->countRows('customer');
        $profileCount = $this->countRows('customer_b2b');

        $crawler = $this->submitCreationForm(
            [
                'use_existing_customer' => '1',
                'business_entity_id' => (string) self::$businessEntityId,
                'role_id' => (string) self::BUYER_ROLE_ID,
            ],
            ['customer', 'existing_customer', 'send_password_setup_link'],
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertStringContainsString(
            'This field cannot be empty.',
            $this->getFieldRowText($crawler, self::EXISTING_CUSTOMER_WIDGET_SELECTOR),
        );
        $this->assertSame('1', $crawler->selectButton(self::SAVE_BUTTON_SELECTOR)->form()->getValues()['customer_b2b[use_existing_customer]']);

        $this->assertSame($customerCount, $this->countRows('customer'));
        $this->assertSame($profileCount, $this->countRows('customer_b2b'));
    }

    /**
     * Posts the rendered creation form, with only the fields a browser would send.
     *
     * @param array<string, mixed> $values
     * @param string[] $omittedFields
     */
    private function submitCreationForm(array $values, array $omittedFields): Crawler
    {
        $crawler = $this->client->request('GET', $this->generateCreateUrl());
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton(self::SAVE_BUTTON_SELECTOR)->form();

        $formData = $form->getPhpValues()['customer_b2b'];
        $formData = array_diff_key($formData, array_flip($omittedFields));

        return $this->client->request($form->getMethod(), $form->getUri(), [
            'customer_b2b' => array_replace_recursive($formData, $values),
        ]);
    }

    private function getFieldRowText(Crawler $crawler, string $widgetSelector): string
    {
        $widget = $crawler->filter($widgetSelector);
        $this->assertCount(1, $widget, \sprintf('Field "%s" was not found', $widgetSelector));

        $row = $widget->closest('.form-group');
        $this->assertNotNull($row);

        return $row->text();
    }

    private function assertNoErrorFlash(Crawler $crawler): void
    {
        $this->assertCount(0, $crawler->filter('.alert-danger'));
    }

    /**
     * @return array<string, string>|null
     */
    private function findCustomerByEmail(string $email): ?array
    {
        $customer = $this->connection
            ->createQueryBuilder()
            ->select('c.id_customer', 'c.id_shop')
            ->from(_DB_PREFIX_ . 'customer', 'c')
            ->where('c.email = :email')
            ->setParameter('email', $email)
            ->executeQuery()
            ->fetchAssociative();

        return false === $customer ? null : $customer;
    }

    private function findProfile(int $customerId): ?CustomerB2b
    {
        return $this->entityManager->getRepository(CustomerB2b::class)->findByCustomerId($customerId);
    }

    private function assertLinkedTo(int $businessEntityId, CustomerB2b $profile): void
    {
        $links = $profile->getBusinessEntityCustomerB2bs();

        $this->assertCount(1, $links);
        $this->assertSame($businessEntityId, $links->first()->getBusinessEntity()->getId());
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function findLogs(int $customerB2bId): array
    {
        return $this->connection
            ->createQueryBuilder()
            ->select('l.message', 'l.id_employee')
            ->from(_DB_PREFIX_ . 'log', 'l')
            ->where('l.object_type = :objectType')
            ->andWhere('l.object_id = :objectId')
            ->setParameter('objectType', 'CustomerB2b')
            ->setParameter('objectId', $customerB2bId)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    private function countRows(string $table): int
    {
        return (int) $this->connection
            ->createQueryBuilder()
            ->select('COUNT(*)')
            ->from(_DB_PREFIX_ . $table)
            ->executeQuery()
            ->fetchOne();
    }

    private function createCustomer(string $email): int
    {
        /** @var CustomerId $id */
        $id = $this->getCommandBus()->handle(new AddCustomerCommand(
            'existing',
            'customer',
            $email,
            'PrestaShopForever1_!',
            self::SHOP_CUSTOMER_GROUP_ID,
            [self::SHOP_CUSTOMER_GROUP_ID],
            self::SHOP_ID,
        ));

        return $id->getValue();
    }

    /**
     * @return array<string, string[]>
     */
    private function getFlashMessages(): array
    {
        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();

        return $session->getFlashBag()->all();
    }

    private function getLoggedInEmployeeId(): int
    {
        /** @var Employee $employee */
        $employee = $this->client->getContainer()->get(Security::class)->getUser();

        return $employee->getId();
    }

    private function generateCreateUrl(): string
    {
        return $this->router->generate('admin_customer_b2b_create');
    }

    private function getCommandBus(): CommandBusInterface
    {
        return $this->client->getContainer()->get(CommandBusInterface::class);
    }

    private static function createBusinessEntity(): int
    {
        /** @var CommandBusInterface $commandBus */
        $commandBus = self::getContainer()->get(CommandBusInterface::class);
        /** @var BusinessEntityId $id */
        $id = $commandBus->handle(new AddBusinessEntityCommand(
            'company',
            'legal name',
            null,
            true,
            BusinessEntityStatus::ACTIVE,
            self::SHOP_ID,
            self::BUSINESS_ENTITY_GROUP_ID,
            true,
            [new BusinessEntityBillingAddress('Billing', '123 Street', null, 'Paris', '75001', self::COUNTRY_ID, true, null)],
        ));

        return $id->getValue();
    }
}
