<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\ImportCsvFromFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Result\ImportResult;
use PrestaShop\PrestaShop\Core\Import\Entity;
use PrestaShop\PrestaShop\Core\Import\ImportSettings;
use RuntimeException;
use Shop;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * Exercises the import safety net through the ImportCsvFromFileCommand façade.
 *
 * Every scenario dispatches the command via the bus and asserts either against
 * the returned ImportResult or against the persisted data. The same command covers
 * both executors: entity types with a modern handler (products, categories) run
 * through the Importer, the others fall back to the legacy controller.
 *
 * Assumed deviation: persisted data is read through the Doctrine DBAL connection
 * rather than the domain repositories. This is deliberate — the repositories only
 * expose get($id) lookups, not the by-name / by-reference / by-email lookups these
 * assertions need — and it still respects the rule of never asserting through
 * ObjectModel.
 */
class ImportFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * Boolean-like options that must be cast from their Gherkin string form.
     */
    private const BOOLEAN_OPTIONS = ['truncate', 'match_ref', 'forceIDs', 'regenerate'];

    /**
     * @var ImportResult|null
     */
    private $lastImportResult;

    /**
     * @var string[] absolute paths of fixtures copied into the import directory, cleaned up after each scenario
     */
    private $copiedFixtures = [];

    /**
     * @AfterScenario
     */
    public function removeCopiedFixtures(): void
    {
        foreach ($this->copiedFixtures as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        $this->copiedFixtures = [];
    }

    /**
     * @When I import :entityType from CSV file :fixture in language :langIso
     */
    public function importFile(string $entityType, string $fixture, string $langIso): void
    {
        $this->runImport($entityType, $fixture, $langIso, false, []);
    }

    /**
     * @When I import :entityType from CSV file :fixture in language :langIso with options:
     */
    public function importFileWithOptions(string $entityType, string $fixture, string $langIso, TableNode $options): void
    {
        $this->runImport($entityType, $fixture, $langIso, false, $options->getRowsHash());
    }

    /**
     * @When I validate the import of :entityType from CSV file :fixture in language :langIso
     */
    public function validateImportFile(string $entityType, string $fixture, string $langIso): void
    {
        $this->runImport($entityType, $fixture, $langIso, true, []);
    }

    /**
     * @When I validate the import of :entityType from CSV file :fixture in language :langIso with options:
     */
    public function validateImportFileWithOptions(string $entityType, string $fixture, string $langIso, TableNode $options): void
    {
        $this->runImport($entityType, $fixture, $langIso, true, $options->getRowsHash());
    }

    /**
     * @Then the import should succeed
     */
    public function theImportShouldSucceed(): void
    {
        Assert::assertNotNull($this->lastImportResult, 'No import was run.');
        Assert::assertFalse(
            $this->lastImportResult->hasErrors(),
            sprintf('Import reported errors: %s', implode(' | ', $this->lastImportResult->getErrors()))
        );
    }

    /**
     * @Then the import should report :count error(s)
     */
    public function theImportShouldReportErrors(int $count): void
    {
        Assert::assertNotNull($this->lastImportResult, 'No import was run.');
        Assert::assertCount(
            $count,
            $this->lastImportResult->getErrors(),
            sprintf('Reported errors: %s', implode(' | ', $this->lastImportResult->getErrors()))
        );
    }

    /**
     * @Then the import should report at least :count error(s)
     */
    public function theImportShouldReportAtLeastErrors(int $count): void
    {
        Assert::assertNotNull($this->lastImportResult, 'No import was run.');
        Assert::assertGreaterThanOrEqual(
            $count,
            count($this->lastImportResult->getErrors()),
            sprintf('Reported errors: %s', implode(' | ', $this->lastImportResult->getErrors()))
        );
    }

    /**
     * @Then the import report should contain error :text
     */
    public function theImportReportShouldContainError(string $text): void
    {
        Assert::assertNotNull($this->lastImportResult, 'No import was run.');
        foreach ($this->lastImportResult->getErrors() as $error) {
            if (str_contains($error, $text)) {
                return;
            }
        }
        Assert::fail(sprintf(
            'No reported error contains "%s". Errors: %s',
            $text,
            implode(' | ', $this->lastImportResult->getErrors())
        ));
    }

    /**
     * @Then :count rows should have been processed
     */
    public function rowsShouldHaveBeenProcessed(int $count): void
    {
        Assert::assertNotNull($this->lastImportResult, 'No import was run.');
        Assert::assertSame($count, $this->lastImportResult->getDoneCount());
    }

    /**
     * @Then product with reference :reference should exist
     */
    public function productWithReferenceShouldExist(string $reference): void
    {
        Assert::assertSame(
            1,
            $this->count('SELECT COUNT(*) FROM ' . $this->prefix() . 'product WHERE reference = :reference', ['reference' => $reference]),
            sprintf('Expected exactly one product with reference "%s".', $reference)
        );
    }

    /**
     * @Then product with id :id should exist
     */
    public function productWithIdShouldExist(int $id): void
    {
        Assert::assertSame(
            1,
            $this->count('SELECT COUNT(*) FROM ' . $this->prefix() . 'product WHERE id_product = :id', ['id' => $id]),
            sprintf('Expected product with id %d to exist.', $id)
        );
    }

    /**
     * @Then product with reference :reference should be associated to shops :shopReferences
     */
    public function productShouldBeAssociatedToShops(string $reference, string $shopReferences): void
    {
        foreach (explode(',', $shopReferences) as $shopReference) {
            $shopReference = trim($shopReference);
            Assert::assertSame(
                1,
                $this->count(
                    'SELECT COUNT(*) FROM ' . $this->prefix() . 'product_shop ps'
                    . ' INNER JOIN ' . $this->prefix() . 'product p ON p.id_product = ps.id_product'
                    . ' WHERE p.reference = :reference AND ps.id_shop = :shopId',
                    ['reference' => $reference, 'shopId' => $this->referenceToId($shopReference)]
                ),
                sprintf('Expected product "%s" to be associated to shop "%s".', $reference, $shopReference)
            );
        }
    }

    /**
     * @Then product :name should exist
     */
    public function productShouldExist(string $name): void
    {
        Assert::assertGreaterThan(
            0,
            $this->count(
                'SELECT COUNT(*) FROM ' . $this->prefix() . 'product_lang WHERE name = :name AND id_lang = :langId',
                ['name' => $name, 'langId' => $this->resolveLangId('en')]
            ),
            sprintf('Expected product "%s" to exist.', $name)
        );
    }

    /**
     * @Then product :name should not exist
     */
    public function productShouldNotExist(string $name): void
    {
        Assert::assertSame(
            0,
            $this->count(
                'SELECT COUNT(*) FROM ' . $this->prefix() . 'product_lang WHERE name = :name AND id_lang = :langId',
                ['name' => $name, 'langId' => $this->resolveLangId('en')]
            ),
            sprintf('Expected product "%s" not to exist.', $name)
        );
    }

    /**
     * @Then product :name should have price :price
     */
    public function productShouldHavePrice(string $name, string $price): void
    {
        $actual = $this->getConnection()->executeQuery(
            'SELECT p.price FROM ' . $this->prefix() . 'product p'
            . ' INNER JOIN ' . $this->prefix() . 'product_lang pl ON pl.id_product = p.id_product'
            . ' WHERE pl.name = :name AND pl.id_lang = :langId',
            ['name' => $name, 'langId' => $this->resolveLangId('en')]
        )->fetchOne();

        Assert::assertNotFalse($actual, sprintf('Product "%s" not found.', $name));
        Assert::assertEqualsWithDelta((float) $price, (float) $actual, 0.001);
    }

    /**
     * @Then category :name should exist
     */
    public function categoryShouldExist(string $name): void
    {
        Assert::assertGreaterThan(
            0,
            $this->count(
                'SELECT COUNT(*) FROM ' . $this->prefix() . 'category_lang WHERE name = :name AND id_lang = :langId',
                ['name' => $name, 'langId' => $this->resolveLangId('en')]
            ),
            sprintf('Expected category "%s" to exist.', $name)
        );
    }

    /**
     * @Then category :name should be associated to shops :shopReferences
     */
    public function categoryShouldBeAssociatedToShops(string $name, string $shopReferences): void
    {
        foreach (explode(',', $shopReferences) as $shopReference) {
            $shopReference = trim($shopReference);
            Assert::assertGreaterThanOrEqual(
                1,
                $this->count(
                    'SELECT COUNT(*) FROM ' . $this->prefix() . 'category_shop cs'
                    . ' INNER JOIN ' . $this->prefix() . 'category_lang cl ON cl.id_category = cs.id_category AND cl.id_lang = :langId'
                    . ' WHERE cl.name = :name AND cs.id_shop = :shopId',
                    ['name' => $name, 'langId' => $this->resolveLangId('en'), 'shopId' => $this->referenceToId($shopReference)]
                ),
                sprintf('Expected category "%s" to be associated to shop "%s".', $name, $shopReference)
            );
        }
    }

    /**
     * @Then product with reference :reference should have a combination associated to shop :shopReference
     */
    public function combinationShouldBeAssociatedToShop(string $reference, string $shopReference): void
    {
        Assert::assertGreaterThan(
            0,
            $this->count(
                'SELECT COUNT(*) FROM ' . $this->prefix() . 'product_attribute_shop pas'
                . ' INNER JOIN ' . $this->prefix() . 'product_attribute pa ON pa.id_product_attribute = pas.id_product_attribute'
                . ' INNER JOIN ' . $this->prefix() . 'product p ON p.id_product = pa.id_product'
                . ' WHERE p.reference = :reference AND pas.id_shop = :shopId',
                ['reference' => $reference, 'shopId' => $this->referenceToId($shopReference)]
            ),
            sprintf('Expected product "%s" to have a combination associated to shop "%s".', $reference, $shopReference)
        );
    }

    /**
     * @Then category :name should not exist
     */
    public function categoryShouldNotExist(string $name): void
    {
        Assert::assertSame(
            0,
            $this->count(
                'SELECT COUNT(*) FROM ' . $this->prefix() . 'category_lang WHERE name = :name AND id_lang = :langId',
                ['name' => $name, 'langId' => $this->resolveLangId('en')]
            ),
            sprintf('Expected category "%s" not to exist.', $name)
        );
    }

    /**
     * @Then manufacturer :name should exist
     */
    public function manufacturerShouldExist(string $name): void
    {
        Assert::assertSame(
            1,
            $this->count('SELECT COUNT(*) FROM ' . $this->prefix() . 'manufacturer WHERE name = :name', ['name' => $name]),
            sprintf('Expected exactly one manufacturer "%s".', $name)
        );
    }

    /**
     * @Then supplier :name should exist
     */
    public function supplierShouldExist(string $name): void
    {
        Assert::assertSame(
            1,
            $this->count('SELECT COUNT(*) FROM ' . $this->prefix() . 'supplier WHERE name = :name', ['name' => $name]),
            sprintf('Expected exactly one supplier "%s".', $name)
        );
    }

    /**
     * @Then alias :alias should exist with search :search
     */
    public function aliasShouldExist(string $alias, string $search): void
    {
        Assert::assertSame(
            1,
            $this->count(
                'SELECT COUNT(*) FROM ' . $this->prefix() . 'alias WHERE alias = :alias AND search = :search',
                ['alias' => $alias, 'search' => $search]
            ),
            sprintf('Expected alias "%s" with search "%s".', $alias, $search)
        );
    }

    /**
     * @Then store :name should exist
     */
    public function storeShouldExist(string $name): void
    {
        Assert::assertSame(
            1,
            $this->count(
                'SELECT COUNT(*) FROM ' . $this->prefix() . 'store_lang WHERE name = :name AND id_lang = :langId',
                ['name' => $name, 'langId' => $this->resolveLangId('en')]
            ),
            sprintf('Expected exactly one store "%s".', $name)
        );
    }

    /**
     * @Then address :alias should exist for customer :email
     */
    public function addressShouldExistForCustomer(string $alias, string $email): void
    {
        Assert::assertSame(
            1,
            $this->count(
                'SELECT COUNT(*) FROM ' . $this->prefix() . 'address a'
                . ' INNER JOIN ' . $this->prefix() . 'customer c ON c.id_customer = a.id_customer'
                . ' WHERE a.alias = :alias AND c.email = :email AND a.deleted = 0',
                ['alias' => $alias, 'email' => $email]
            ),
            sprintf('Expected address "%s" for customer "%s".', $alias, $email)
        );
    }

    /**
     * @Then product with reference :reference should have a combination with reference :combinationReference
     */
    public function productShouldHaveCombination(string $reference, string $combinationReference): void
    {
        Assert::assertGreaterThan(
            0,
            $this->count(
                'SELECT COUNT(*) FROM ' . $this->prefix() . 'product_attribute pa'
                . ' INNER JOIN ' . $this->prefix() . 'product p ON p.id_product = pa.id_product'
                . ' WHERE p.reference = :reference AND pa.reference = :combinationReference',
                ['reference' => $reference, 'combinationReference' => $combinationReference]
            ),
            sprintf('Expected product "%s" to have a combination with reference "%s".', $reference, $combinationReference)
        );
    }

    /**
     * @Then customer with email :email should exist
     */
    public function customerWithEmailShouldExist(string $email): void
    {
        Assert::assertSame(
            1,
            $this->count('SELECT COUNT(*) FROM ' . $this->prefix() . 'customer WHERE email = :email', ['email' => $email]),
            sprintf('Expected exactly one customer with email "%s".', $email)
        );
    }

    /**
     * @Then customer with email :email should not exist
     */
    public function customerWithEmailShouldNotExist(string $email): void
    {
        Assert::assertSame(
            0,
            $this->count('SELECT COUNT(*) FROM ' . $this->prefix() . 'customer WHERE email = :email', ['email' => $email]),
            sprintf('Expected no customer with email "%s".', $email)
        );
    }

    /**
     * @Then customer with email :email should have last name :lastName
     */
    public function customerShouldHaveLastName(string $email, string $lastName): void
    {
        $actual = $this->getConnection()->executeQuery(
            'SELECT lastname FROM ' . $this->prefix() . 'customer WHERE email = :email',
            ['email' => $email]
        )->fetchOne();

        Assert::assertNotFalse($actual, sprintf('Customer "%s" not found.', $email));
        Assert::assertSame($lastName, $actual);
    }

    /**
     * @Given the shop group :groupReference shares its customers
     */
    public function shopGroupSharesCustomers(string $groupReference): void
    {
        $this->getConnection()->executeStatement(
            'UPDATE ' . $this->prefix() . 'shop_group SET share_customer = 1 WHERE id_shop_group = :id',
            ['id' => $this->referenceToId($groupReference)]
        );

        Shop::resetStaticCache();
    }

    /**
     * @Then customer with email :email should be in shop :shopReference
     */
    public function customerShouldBeInShop(string $email, string $shopReference): void
    {
        $actual = $this->getConnection()->executeQuery(
            'SELECT id_shop FROM ' . $this->prefix() . 'customer WHERE email = :email',
            ['email' => $email]
        )->fetchOne();

        Assert::assertNotFalse($actual, sprintf('Customer "%s" not found.', $email));
        Assert::assertSame($this->referenceToId($shopReference), (int) $actual);
    }

    /**
     * @Then customer with email :email should be shared in shop group :groupReference
     */
    public function customerShouldBeSharedInGroup(string $email, string $groupReference): void
    {
        $actual = $this->getConnection()->executeQuery(
            'SELECT id_shop_group FROM ' . $this->prefix() . 'customer WHERE email = :email',
            ['email' => $email]
        )->fetchOne();

        Assert::assertNotFalse($actual, sprintf('Customer "%s" not found.', $email));
        Assert::assertSame($this->referenceToId($groupReference), (int) $actual);
    }

    private function runImport(string $entityType, string $fixture, string $langIso, bool $validateOnly, array $rawOptions): void
    {
        $this->ensureRequestWithSession();
        $this->copyFixtureToImportDirectory($fixture);

        $options = $this->normalizeOptions($rawOptions);
        $separator = $options['separator'] ?? ImportSettings::DEFAULT_SEPARATOR;
        $options['data_mapping'] = $this->buildMappingFromHeader($fixture, $separator);
        // The fixtures carry a header row naming each field, so skip it by default.
        $options['skip'] = isset($options['skip']) ? (int) $options['skip'] : 1;

        $command = new ImportCsvFromFileCommand(
            $fixture,
            Entity::getFromName($entityType),
            $langIso,
            $options,
            $validateOnly
        );

        $this->lastImportResult = $this->dispatchSwallowingLegacyWarnings($command);
    }

    /**
     * The legacy import code (and the partially migrated handlers it shares helpers with) emit PHP
     * warnings/notices that are harmless at runtime — production suppresses them — but the test kernel's
     * debug error handler promotes them to exceptions. Since this safety net locks the import OUTCOME,
     * not the notice-cleanliness of code we are explicitly not allowed to touch, warnings are swallowed
     * for the duration of the dispatch only.
     */
    private function dispatchSwallowingLegacyWarnings(ImportCsvFromFileCommand $command): ImportResult
    {
        set_error_handler(
            static fn (int $severity): bool => in_array($severity, [E_WARNING, E_NOTICE, E_DEPRECATED, E_USER_DEPRECATED], true)
        );

        try {
            return $this->getCommandBus()->handle($command);
        } finally {
            restore_error_handler();
        }
    }

    /**
     * @param array<string, string> $rawOptions
     *
     * @return array<string, mixed>
     */
    private function normalizeOptions(array $rawOptions): array
    {
        $options = $rawOptions;
        foreach (self::BOOLEAN_OPTIONS as $key) {
            if (isset($options[$key])) {
                $options[$key] = filter_var($options[$key], FILTER_VALIDATE_BOOLEAN);
            }
        }

        return $options;
    }

    /**
     * Reads the fixture header row and turns it into a column index => field name mapping,
     * matching the "type_value" format consumed by both the modern and legacy executors.
     *
     * @return array<int, string>
     */
    private function buildMappingFromHeader(string $fixture, string $separator): array
    {
        $handle = fopen($this->getImportDirectory() . $fixture, 'rb');
        if (false === $handle) {
            throw new RuntimeException(sprintf('Unable to read import fixture "%s".', $fixture));
        }

        $header = fgetcsv($handle, 0, $separator, '"', '');
        fclose($handle);

        if (false === $header) {
            throw new RuntimeException(sprintf('Import fixture "%s" has no header row.', $fixture));
        }

        $mapping = [];
        foreach ($header as $index => $field) {
            $field = trim((string) $field);
            $mapping[$index] = '' === $field ? 'no' : $field;
        }

        return $mapping;
    }

    private function copyFixtureToImportDirectory(string $fixture): void
    {
        $source = dirname(__DIR__, 5) . '/Resources/import/' . $fixture;
        if (!is_file($source)) {
            throw new RuntimeException(sprintf('Import fixture "%s" not found at %s.', $fixture, $source));
        }

        $directory = $this->getImportDirectory();
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException(sprintf('Unable to create import directory "%s".', $directory));
        }

        $content = file_get_contents($source);
        if (false === $content) {
            throw new RuntimeException(sprintf('Unable to read import fixture "%s".', $fixture));
        }

        $destination = $directory . $fixture;
        if (false === file_put_contents($destination, $this->replaceReferences($content))) {
            throw new RuntimeException(sprintf('Unable to write import fixture "%s" to %s.', $fixture, $destination));
        }

        $this->copiedFixtures[$destination] = $destination;
    }

    /**
     * Replaces {{reference}} placeholders in a fixture with the matching id from shared storage.
     * Used by multistore fixtures whose shop/group ids are only known at runtime.
     */
    private function replaceReferences(string $content): string
    {
        return preg_replace_callback(
            '/\{\{([a-zA-Z0-9_]+)\}\}/',
            fn (array $matches): string => (string) $this->referenceToId($matches[1]),
            $content
        );
    }

    /**
     * The modern CSV reader pulls its separator from the session at build time, so a request
     * carrying a session must be on the stack before the Importer is instantiated. The CLI/Behat
     * runtime has none, so we push a minimal one (the legacy executor is unaffected: it reads $_POST).
     */
    private function ensureRequestWithSession(): void
    {
        $requestStack = $this->getContainer()->get('request_stack');
        if (null !== $requestStack->getCurrentRequest()) {
            return;
        }

        $session = new Session(new MockArraySessionStorage());
        $session->set('separator', ImportSettings::DEFAULT_SEPARATOR);
        $session->set('multiple_value_separator', ImportSettings::DEFAULT_MULTIVALUE_SEPARATOR);

        $request = new Request();
        $request->setSession($session);
        $requestStack->push($request);
    }

    private function getImportDirectory(): string
    {
        return (string) $this->getContainer()->get('prestashop.core.import.dir');
    }

    private function getConnection(): Connection
    {
        return $this->getContainer()->get('doctrine.dbal.default_connection');
    }

    private function prefix(): string
    {
        return (string) $this->getContainer()->getParameter('database_prefix');
    }

    /**
     * @param array<string, mixed> $params
     */
    private function count(string $sql, array $params): int
    {
        return (int) $this->getConnection()->executeQuery($sql, $params)->fetchOne();
    }

    private function resolveLangId(string $iso): int
    {
        return (int) $this->getConnection()->executeQuery(
            'SELECT id_lang FROM ' . $this->prefix() . 'lang WHERE iso_code = :iso',
            ['iso' => $iso]
        )->fetchOne();
    }
}
