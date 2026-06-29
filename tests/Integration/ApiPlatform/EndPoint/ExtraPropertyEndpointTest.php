<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform\EndPoint;

use Db;
use Language;
use Module;
use Symfony\Component\HttpFoundation\Response;
use Tests\Resources\DatabaseDump;
use Tests\Resources\Resetter\LanguageResetter;
use Tests\Resources\Resetter\ProductResetter;
use Tools;

/**
 * Integration tests for the "Extra Properties on the Admin API" feature.
 *
 * Two test modules (extrapropertytest, extrapropertytest2) register extra properties scoped to specific API
 * endpoints. The tests assert the runtime contract of the dedicated extraProperties sub-object (single items) and
 * the inline grid/list values (collections), validating the EXACT content so an unexpected field would fail, that
 * two modules cohabit without clashing, and that a CQRS-paginated list (combinations) is enriched too.
 */
final class ExtraPropertyEndpointTest extends ApiTestCase
{
    private const MODULE_NAME = 'extrapropertytest';
    private const MODULE_2_NAME = 'extrapropertytest2';

    /**
     * @var string[]
     */
    private const TEST_MODULES = [self::MODULE_NAME, self::MODULE_2_NAME];

    private const PRODUCT_READ = 'product_read';
    private const PRODUCT_WRITE = 'product_write';
    private const CUSTOMER_READ = 'customer_read';
    private const CUSTOMER_WRITE = 'customer_write';

    /**
     * Existing product fixture id from the standard test fixtures (has combinations).
     */
    private const PRODUCT_ID = 1;

    /**
     * Extra-property value tables created by the test modules, cleared before each test for deterministic content.
     */
    private const EXTRA_VALUE_TABLES = ['product_extra', 'product_extra_lang', 'customer_extra'];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // The localized extra property (api_note) is written/read in several locales, so install a second language
        // (the default install only has en-US).
        LanguageResetter::resetLanguages();
        $frenchLanguageId = self::addLanguageByLocale('fr-FR');

        ProductResetter::resetProducts();
        DatabaseDump::restoreTables(['customer', 'customer_group']);

        // ProductResetter restores products from an English-only dump, dropping the French translations the new
        // language created. Copy them back (en → fr for every *_lang table) so the product list can be requested in
        // either language: the grid joins the *_extra_lang table on the base *_lang row, which must therefore exist.
        Language::copyLanguageData((int) Language::getIdByLocale('en-US'), $frenchLanguageId);

        foreach (self::TEST_MODULES as $moduleName) {
            // Copy the test module into the test modules directory (mirrors ModuleManagerBuilderTest). The copy must
            // happen before the defensive uninstall below so the module class can always be loaded.
            $sourceModuleDir = dirname(__DIR__, 3) . '/Resources/modules_tests/' . $moduleName;
            if (is_dir($sourceModuleDir)) {
                Tools::recurseCopy($sourceModuleDir, self::moduleDir($moduleName));
            }

            // Self-heal: a previous interrupted run may have left the module installed in the test DB.
            self::uninstallTestModuleIfInstalled($moduleName);

            $module = Module::getInstanceByName($moduleName);
            self::assertInstanceOf(Module::class, $module);
            self::assertTrue((bool) $module->install(), sprintf('The %s module could not be installed', $moduleName));
        }
    }

    public static function tearDownAfterClass(): void
    {
        // Always clean up — even if the install or a test failed midway — so nothing leaks into the shared test
        // modules directory or the test database (a module left here is installed by every integration run).
        foreach (self::TEST_MODULES as $moduleName) {
            self::uninstallTestModuleIfInstalled($moduleName);
        }

        // Order matters: LanguageResetter::resetLanguages() below calls ResourceResetter::resetTestModules(), which
        // mirrors tests/Resources/modules/ back from a temp backup. So we run that FIRST and only then drop the
        // module dirs — otherwise the mirror would restore them right after we deleted them.
        ProductResetter::resetProducts();
        DatabaseDump::restoreTables(['customer', 'customer_group']);
        LanguageResetter::resetLanguages();

        foreach (self::TEST_MODULES as $moduleName) {
            if (is_dir(self::moduleDir($moduleName))) {
                Tools::deleteDirectory(self::moduleDir($moduleName));
            }
        }

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Clear all extra-property values so each test starts from a known, empty state (exact-content assertions
        // would otherwise depend on values persisted by earlier tests).
        foreach (self::EXTRA_VALUE_TABLES as $table) {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . $table . '`');
        }
    }

    /**
     * Absolute path of a test module once copied into the test modules directory.
     */
    private static function moduleDir(string $moduleName): string
    {
        return _PS_MODULE_DIR_ . $moduleName;
    }

    /**
     * Uninstalls a test module when installed (dropping its definitions, extra tables and roles). Safe to call
     * defensively before install and unconditionally on teardown.
     */
    private static function uninstallTestModuleIfInstalled(string $moduleName): void
    {
        if (!Module::isInstalled($moduleName)) {
            return;
        }

        $module = Module::getInstanceByName($moduleName);
        if ($module instanceof Module) {
            $module->uninstall();
        }
    }

    public function getProtectedEndpoints(): iterable
    {
        yield 'get product endpoint' => ['GET', '/products/' . self::PRODUCT_ID];
        yield 'update product endpoint' => ['PATCH', '/products/' . self::PRODUCT_ID];
        yield 'get customer endpoint' => ['GET', '/customers/1'];
        yield 'update customer endpoint' => ['PATCH', '/customers/1'];
    }

    /**
     * Round-trip + cohabitation: a PATCH writing properties of BOTH modules must round-trip, and the response must
     * carry EXACTLY those properties keyed per module (an unexpected/leaked field would fail the assertion).
     */
    public function testWriteAndReadProductExtraProperties(): void
    {
        $patchedProduct = $this->partialUpdateItem(
            '/products/' . self::PRODUCT_ID,
            [
                'extraProperties' => [
                    self::MODULE_NAME => [
                        'api_flag' => true,
                        'api_note' => ['en-US' => 'hello', 'fr-FR' => 'bonjour'],
                    ],
                    self::MODULE_2_NAME => [
                        'extra_tag' => 'tagged',
                        'api_only_note' => ['en-US' => 'note-en', 'fr-FR' => 'note-fr'],
                    ],
                ],
            ],
            [self::PRODUCT_WRITE]
        );

        $expected = [
            self::MODULE_NAME => [
                'api_flag' => true,
                'api_note' => ['en-US' => 'hello', 'fr-FR' => 'bonjour'],
            ],
            self::MODULE_2_NAME => [
                'extra_tag' => 'tagged',
                'api_only_note' => ['en-US' => 'note-en', 'fr-FR' => 'note-fr'],
            ],
        ];
        $this->assertArrayHasKey('extraProperties', $patchedProduct);
        $this->assertEquals($expected, $patchedProduct['extraProperties']);

        // The same values must be persisted and returned, exactly, on a plain GET.
        $fetchedProduct = $this->getItem('/products/' . self::PRODUCT_ID, [self::PRODUCT_READ]);
        $this->assertArrayHasKey('extraProperties', $fetchedProduct);
        $this->assertEquals($expected, $fetchedProduct['extraProperties']);
    }

    /**
     * The product list (grid-backed collection) exposes the API-associated properties of BOTH modules INLINE at the
     * item root under their field name (extra_<module>_<field>), not in a nested extraProperties sub-object. This
     * includes an API-only property (no grid association), which the grid never fetches and is therefore surfaced
     * through the batch-reader fallback. The localized values follow the langId query parameter, so we assert the
     * exact value per language.
     */
    public function testListInjectsExtraProperties(): void
    {
        $this->partialUpdateItem(
            '/products/' . self::PRODUCT_ID,
            [
                'extraProperties' => [
                    self::MODULE_NAME => [
                        'api_flag' => true,
                        'api_note' => ['en-US' => 'hello', 'fr-FR' => 'bonjour'],
                    ],
                    self::MODULE_2_NAME => [
                        'extra_tag' => 'tagged',
                        'api_only_note' => ['en-US' => 'note-en', 'fr-FR' => 'note-fr'],
                    ],
                ],
            ],
            [self::PRODUCT_WRITE]
        );

        $flagKey = 'extra_' . self::MODULE_NAME . '_api_flag';
        $noteKey = 'extra_' . self::MODULE_NAME . '_api_note';
        $tagKey = 'extra_' . self::MODULE_2_NAME . '_extra_tag';
        $apiOnlyNoteKey = 'extra_' . self::MODULE_2_NAME . '_api_only_note';

        $expectations = [
            'en-US' => ['note' => 'hello', 'apiOnlyNote' => 'note-en'],
            'fr-FR' => ['note' => 'bonjour', 'apiOnlyNote' => 'note-fr'],
        ];
        foreach ($expectations as $locale => $expected) {
            $list = $this->listItems('/products?langId=' . (int) Language::getIdByLocale($locale), [self::PRODUCT_READ]);
            $writtenItem = $this->findListItem($list['items'], 'productId', self::PRODUCT_ID);
            $this->assertNotNull($writtenItem, 'The product written to was not present in the list');

            // List items carry the inline grid values, not the nested extraProperties object.
            $this->assertArrayNotHasKey('extraProperties', $writtenItem);
            // Grid-associated properties, reused from the grid-record collector.
            $this->assertTrue($writtenItem[$flagKey]);
            $this->assertSame('tagged', $writtenItem[$tagKey]);
            $this->assertSame($expected['note'], $writtenItem[$noteKey]);
            // API-only property (no grid association): never fetched by the grid query, so it is surfaced through
            // the batch-reader fallback. Its localized value still follows the requested langId.
            $this->assertSame($expected['apiOnlyNote'], $writtenItem[$apiOnlyNoteKey]);
        }
    }

    /**
     * Per-field constraint validation: a single PATCH with several bad values must produce ONE 422 listing every
     * violation, each at its precise merged path (`extraProperties.<module>.<field>[.<locale>]`) with the real
     * constraint message — including one violation PER language for a localized (Assert\All) field. Asserted with
     * assertValidationErrors so both the messages and the exact set of paths are checked (no missing/extra error).
     */
    public function testValidationErrorsReturn422WithPerFieldMessages(): void
    {
        $response = $this->partialUpdateItem(
            '/products/' . self::PRODUCT_ID,
            [
                'extraProperties' => [
                    self::MODULE_NAME => [
                        'api_flag' => 'not-a-bool',                                       // Assert\Type('bool')
                        'api_note' => ['en-US' => 'bad<tag>', 'fr-FR' => 'also<bad>'],     // Assert\All([TypedRegex]) per locale
                    ],
                    self::MODULE_2_NAME => [
                        'extra_tag' => 'has<angle>brackets',                              // TypedRegex (generic_name)
                    ],
                ],
            ],
            [self::PRODUCT_WRITE],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        $this->assertValidationErrors(
            [
                [
                    'propertyPath' => 'extraProperties.' . self::MODULE_NAME . '.api_flag',
                    'message' => 'This value should be of type bool.',
                ],
                // One violation per language for the localized field (per-locale path), each carrying the
                // per-locale value in the TypedRegex message.
                [
                    'propertyPath' => 'extraProperties.' . self::MODULE_NAME . '.api_note.en-US',
                    'message' => '"bad<tag>" is invalid',
                ],
                [
                    'propertyPath' => 'extraProperties.' . self::MODULE_NAME . '.api_note.fr-FR',
                    'message' => '"also<bad>" is invalid',
                ],
                // A field declared by the other module on the same entity, kept under its own module key.
                [
                    'propertyPath' => 'extraProperties.' . self::MODULE_2_NAME . '.extra_tag',
                    'message' => '"has<angle>brackets" is invalid',
                ],
            ],
            $response
        );
    }

    /**
     * A property registered on one entity must never appear on another. The product and customer responses must
     * each contain EXACTLY their own module properties.
     */
    public function testCrossEntityIsolation(): void
    {
        $customerId = $this->getExistingCustomerId();

        $this->partialUpdateItem(
            '/products/' . self::PRODUCT_ID,
            ['extraProperties' => [self::MODULE_NAME => ['api_flag' => true]]],
            [self::PRODUCT_WRITE]
        );

        $patchedCustomer = $this->partialUpdateItem(
            '/customers/' . $customerId,
            ['extraProperties' => [self::MODULE_NAME => ['api_score' => 42]]],
            [self::CUSTOMER_WRITE]
        );
        // The customer carries exactly its own (customer-scoped) property — no product property leaks in.
        $this->assertEquals(
            [self::MODULE_NAME => ['api_score' => 42]],
            $patchedCustomer['extraProperties']
        );

        // The product carries exactly its product-scoped properties (api_flag set, the rest at their default), and
        // none of the customer-only property.
        $product = $this->getItem('/products/' . self::PRODUCT_ID, [self::PRODUCT_READ]);
        $this->assertEquals(
            [
                self::MODULE_NAME => ['api_flag' => true, 'api_note' => []],
                self::MODULE_2_NAME => ['extra_tag' => null, 'api_only_note' => []],
            ],
            $product['extraProperties']
        );

        $customer = $this->getItem('/customers/' . $customerId, [self::CUSTOMER_READ]);
        $this->assertEquals(
            [self::MODULE_NAME => ['api_score' => 42]],
            $customer['extraProperties']
        );
    }

    /**
     * An unknown extra property key (no matching definition) must be silently ignored: the write succeeds and the
     * unknown field is absent from the response.
     */
    public function testUnknownExtraPropertyKeyIsTolerated(): void
    {
        $patchedProduct = $this->partialUpdateItem(
            '/products/' . self::PRODUCT_ID,
            ['extraProperties' => [self::MODULE_NAME => ['does_not_exist' => 'x']]],
            [self::PRODUCT_WRITE]
        );

        // The unknown key is filtered out; only the real (default-valued) properties remain.
        $this->assertEquals(
            [
                self::MODULE_NAME => ['api_flag' => null, 'api_note' => []],
                self::MODULE_2_NAME => ['extra_tag' => null, 'api_only_note' => []],
            ],
            $patchedProduct['extraProperties']
        );
    }

    /**
     * @param array<int, array<string, mixed>> $items
     *
     * @return array<string, mixed>|null
     */
    private function findListItem(array $items, string $idField, int $id): ?array
    {
        foreach ($items as $item) {
            if (isset($item[$idField]) && (int) $item[$idField] === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Returns a real, existing customer id, created through the API (the customer API exposes no GET collection).
     */
    private function getExistingCustomerId(): int
    {
        $customer = $this->createItem(
            '/customers',
            [
                'firstName' => 'Extra',
                'lastName' => 'Property',
                'email' => 'extra.property.api@example.com',
                'password' => 'TestPassword123!',
                'defaultGroupId' => 3,
                'groupIds' => [3],
                'genderId' => 1,
                'enabled' => true,
                'partnerOffersSubscribed' => false,
                'birthday' => '1990-01-15',
                'guest' => false,
            ],
            [self::CUSTOMER_WRITE]
        );
        $this->assertArrayHasKey('customerId', $customer);

        return (int) $customer['customerId'];
    }
}
