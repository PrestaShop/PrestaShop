<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty\Value;

use Configuration;
use Contact;
use Db;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyReaderInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriterInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreConfig;
use Shop;
use ShopGroup;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\Resetter\ShopResetter;

/**
 * Multishop behaviour of the extra property value services against real tables, on a
 * 3-shop installation: the default shop (1) plus a second shop in the default group,
 * and a third shop alone in a second group.
 *
 * Covers the ShopConstraint contract of the value services end to end: fan-out writes
 * (group / all shops / collection), representative-shop reads, toggle uniformization,
 * the non-multishop lang table shape (contact), and the per-shop cleanup on partial
 * ObjectModel deletion. The shop list resolver itself is covered by
 * Tests\Integration\Core\Shop\ShopListResolverTest, and the legacy context constraint
 * mapping by Tests\Integration\Classes\ContextTest.
 */
class ExtraPropertyMultiShopTest extends KernelTestCase
{
    private const MODULE = 'extrapropertymultishoptest';
    private const DEFAULT_SHOP_ID = 1;
    private const DEFAULT_LANG_ID = 1;

    /**
     * Arbitrary entity ids used as extra-property storage keys, one per test so their rows
     * never overlap. Deliberately far above the ids of the installed fixtures to avoid any
     * collision (no actual product/contact row is needed: the value tables only reference
     * the id).
     */
    private const GROUP_WRITE_PRODUCT_ID = 201;
    private const ALL_SHOPS_WRITE_PRODUCT_ID = 202;
    private const COLLECTION_WRITE_PRODUCT_ID = 203;
    private const DIVERGENT_VALUES_PRODUCT_ID = 204;
    private const TOGGLE_PRODUCT_ID = 205;
    private const SHARED_LANG_CONTACT_ID = 301;

    private static int $secondShopId;
    private static int $thirdShopId;
    private static int $defaultGroupId;
    private static int $secondGroupId;

    private static ExtraPropertyReaderInterface $reader;
    private static ExtraPropertyWriterInterface $writer;
    private static ExtraPropertyRegistryInterface $registry;
    private static ExtraPropertyDefinitionRepositoryInterface $definitionRepository;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        // Global var read by legacy code resolving the container (SymfonyContainer::getInstance).
        global $kernel;
        $kernel = self::$kernel;

        ShopResetter::resetShops();
        Configuration::updateGlobalValue(MultistoreConfig::FEATURE_STATUS, 1);

        // Default group already holds shop 1; add a sibling shop and a second group with its own shop.
        self::$defaultGroupId = (int) Shop::getGroupFromShop(self::DEFAULT_SHOP_ID, true);

        $secondShop = new Shop();
        $secondShop->name = 'Extra Property Shop 2';
        $secondShop->id_shop_group = self::$defaultGroupId;
        $secondShop->id_category = 2;
        $secondShop->save();
        self::$secondShopId = (int) $secondShop->id;

        $secondGroup = new ShopGroup();
        $secondGroup->name = 'Extra Property Group 2';
        $secondGroup->save();
        self::$secondGroupId = (int) $secondGroup->id;

        $thirdShop = new Shop();
        $thirdShop->name = 'Extra Property Shop 3';
        $thirdShop->id_shop_group = self::$secondGroupId;
        $thirdShop->id_category = 2;
        $thirdShop->save();
        self::$thirdShopId = (int) $thirdShop->id;

        Shop::resetStaticCache();
        Shop::resetContext();
        Shop::setContext(Shop::CONTEXT_ALL);

        $container = self::getContainer();
        self::$reader = $container->get(ExtraPropertyReaderInterface::class);
        self::$writer = $container->get(ExtraPropertyWriterInterface::class);
        self::$registry = $container->get(ExtraPropertyRegistryInterface::class);
        self::$definitionRepository = $container->get(ExtraPropertyDefinitionRepositoryInterface::class);

        foreach (self::definitions() as $definition) {
            self::$registry->register($definition);
        }
    }

    public static function tearDownAfterClass(): void
    {
        foreach (self::definitions() as $definition) {
            self::$registry->unregister($definition, true);
        }
        ShopResetter::resetShops();

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        // KernelTestCase shuts the kernel down after every test; ObjectModel resolves its
        // services through ContainerFinder/SymfonyContainer, which need a booted kernel.
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        // Only the tables of the registered scopes exist (no COMMON definition → no {e}_extra).
        foreach (['product_extra_lang', 'product_extra_shop', 'contact_extra_lang', 'contact_extra_shop'] as $table) {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . $table . '`');
        }
    }

    public function testGroupConstraintFansOutToTheGroupShopsOnly(): void
    {
        self::$writer->writeAll('product', 'id_product', self::GROUP_WRITE_PRODUCT_ID, [self::MODULE => [
            'ms_shop' => 'group-1-value',
            'ms_lang' => [self::DEFAULT_LANG_ID => 'group-1-lang'],
        ]], ShopConstraint::shopGroup(self::$defaultGroupId));

        $this->assertSame('group-1-value', $this->readShopValue(self::GROUP_WRITE_PRODUCT_ID, ShopConstraint::shop(self::DEFAULT_SHOP_ID)));
        $this->assertSame('group-1-value', $this->readShopValue(self::GROUP_WRITE_PRODUCT_ID, ShopConstraint::shop(self::$secondShopId)));
        // The third shop is outside the group: nothing was written for it.
        $this->assertNull($this->readShopValue(self::GROUP_WRITE_PRODUCT_ID, ShopConstraint::shop(self::$thirdShopId)));

        // product_extra_lang is shop-aware (product is multilang-multishop): one row per group shop.
        $this->assertSame('group-1-lang', $this->readLangValue(self::GROUP_WRITE_PRODUCT_ID, ShopConstraint::shop(self::$secondShopId)));
        $this->assertSame(2, $this->countRows('product_extra_lang', 'id_product', self::GROUP_WRITE_PRODUCT_ID));
        $this->assertSame(2, $this->countRows('product_extra_shop', 'id_product', self::GROUP_WRITE_PRODUCT_ID));
    }

    public function testAllShopsAndCollectionConstraintsWriteTheirScopes(): void
    {
        self::$writer->writeAll('product', 'id_product', self::ALL_SHOPS_WRITE_PRODUCT_ID, [self::MODULE => ['ms_shop' => 'everywhere']], ShopConstraint::allShops());
        $this->assertSame(3, $this->countRows('product_extra_shop', 'id_product', self::ALL_SHOPS_WRITE_PRODUCT_ID));

        self::$writer->writeAll('product', 'id_product', self::COLLECTION_WRITE_PRODUCT_ID, [self::MODULE => ['ms_shop' => 'third-only']], ShopCollection::shops([self::$thirdShopId]));
        $this->assertSame(1, $this->countRows('product_extra_shop', 'id_product', self::COLLECTION_WRITE_PRODUCT_ID));
        $this->assertSame('third-only', $this->readShopValue(self::COLLECTION_WRITE_PRODUCT_ID, ShopConstraint::shop(self::$thirdShopId)));
    }

    public function testNonSingleConstraintReadsTheRepresentativeShopValue(): void
    {
        self::$writer->writeAll('product', 'id_product', self::DIVERGENT_VALUES_PRODUCT_ID, [self::MODULE => ['ms_shop' => 'shop-1']], ShopConstraint::shop(self::DEFAULT_SHOP_ID));
        self::$writer->writeAll('product', 'id_product', self::DIVERGENT_VALUES_PRODUCT_ID, [self::MODULE => ['ms_shop' => 'shop-2']], ShopConstraint::shop(self::$secondShopId));
        self::$writer->writeAll('product', 'id_product', self::DIVERGENT_VALUES_PRODUCT_ID, [self::MODULE => ['ms_shop' => 'shop-3']], ShopConstraint::shop(self::$thirdShopId));

        // Default shop belongs to the default group and to all-shops: its value represents both.
        $this->assertSame('shop-1', $this->readShopValue(self::DIVERGENT_VALUES_PRODUCT_ID, ShopConstraint::shopGroup(self::$defaultGroupId)));
        $this->assertSame('shop-1', $this->readShopValue(self::DIVERGENT_VALUES_PRODUCT_ID, ShopConstraint::allShops()));
        // The second group does not contain the default shop: its lowest shop represents it.
        $this->assertSame('shop-3', $this->readShopValue(self::DIVERGENT_VALUES_PRODUCT_ID, ShopConstraint::shopGroup(self::$secondGroupId)));
    }

    public function testToggleUniformizesTheConstraintScope(): void
    {
        $flagDefinition = self::$definitionRepository->findDefinitionByModuleAndField('product', self::MODULE, 'ms_flag');
        $this->assertNotNull($flagDefinition);

        // First toggle in group context: no row anywhere → target is enabled, for both group shops.
        self::$writer->toggleExtraProperty($flagDefinition, self::TOGGLE_PRODUCT_ID, ShopConstraint::shopGroup(self::$defaultGroupId));
        $this->assertSame(true, $this->readValue('ms_flag', self::TOGGLE_PRODUCT_ID, ShopConstraint::shop(self::DEFAULT_SHOP_ID)));
        $this->assertSame(true, $this->readValue('ms_flag', self::TOGGLE_PRODUCT_ID, ShopConstraint::shop(self::$secondShopId)));

        // Diverge the second shop, then toggle in group context again: the representative
        // (default) shop's value decides the target and the scope is re-aligned.
        self::$writer->toggleExtraProperty($flagDefinition, self::TOGGLE_PRODUCT_ID, ShopConstraint::shop(self::$secondShopId));
        $this->assertSame(false, $this->readValue('ms_flag', self::TOGGLE_PRODUCT_ID, ShopConstraint::shop(self::$secondShopId)));

        self::$writer->toggleExtraProperty($flagDefinition, self::TOGGLE_PRODUCT_ID, ShopConstraint::shopGroup(self::$defaultGroupId));
        $this->assertSame(false, $this->readValue('ms_flag', self::TOGGLE_PRODUCT_ID, ShopConstraint::shop(self::DEFAULT_SHOP_ID)));
        $this->assertSame(false, $this->readValue('ms_flag', self::TOGGLE_PRODUCT_ID, ShopConstraint::shop(self::$secondShopId)));
    }

    public function testContactLangValuesAreSharedAcrossShops(): void
    {
        // contact_lang has no id_shop: the mirrored extra table must not have one either,
        // writes must not fan out per shop, and reads must work from any shop context.
        $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'contact_extra_lang`');
        $this->assertNotContains('id_shop', array_column($columns, 'Field'));

        self::$writer->writeAll('contact', 'id_contact', self::SHARED_LANG_CONTACT_ID, [self::MODULE => [
            'ms_contact_lang' => [self::DEFAULT_LANG_ID => 'Shared title'],
        ]], ShopConstraint::allShops());

        $this->assertSame(1, $this->countRows('contact_extra_lang', 'id_contact', self::SHARED_LANG_CONTACT_ID));

        $values = self::$reader->getExtraProperties('contact', 'id_contact', self::SHARED_LANG_CONTACT_ID, self::DEFAULT_LANG_ID, ShopConstraint::shop(self::$thirdShopId));
        $this->assertSame('Shared title', $values[self::MODULE]['ms_contact_lang']);
    }

    public function testPartialObjectModelDeleteRemovesOnlyTheDeletedShopsRows(): void
    {
        $contact = new Contact();
        $contact->name = [self::DEFAULT_LANG_ID => 'Multishop contact'];
        $contact->email = 'extra-property-multishop@test.com';
        $contact->id_shop_list = [self::DEFAULT_SHOP_ID, self::$secondShopId];
        // Legacy add()/delete() build their result with a bitwise &=, so they return int 1.
        $this->assertTrue((bool) $contact->add());
        $contactId = (int) $contact->id;

        self::$writer->writeAll('contact', 'id_contact', $contactId, [self::MODULE => [
            'ms_contact_shop' => 'per-shop-value',
        ]], ShopCollection::shops([self::DEFAULT_SHOP_ID, self::$secondShopId]));
        $this->assertSame(2, $this->countRows('contact_extra_shop', 'id_contact', $contactId));

        // Delete from the second shop only: the contact survives on shop 1, and so must its rows.
        $contact->id_shop_list = [self::$secondShopId];
        $this->assertTrue((bool) $contact->delete());
        $this->assertSame(1, $this->countRows('contact_extra_shop', 'id_contact', $contactId));
        $this->assertTrue((bool) Contact::existsInDatabase($contactId));

        // Full delete: every remaining extra row goes away with the entity.
        $contact = new Contact($contactId);
        $contact->id_shop_list = [self::DEFAULT_SHOP_ID];
        $this->assertTrue((bool) $contact->delete());
        $this->assertSame(0, $this->countRows('contact_extra_shop', 'id_contact', $contactId));
    }

    /**
     * @return ExtraPropertyDefinition[]
     */
    private static function definitions(): array
    {
        return [
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'ms_shop', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::SHOP, moduleName: self::MODULE),
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'ms_lang', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::LANG, moduleName: self::MODULE),
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'ms_flag', type: ExtraPropertyType::BOOL, scope: ExtraPropertyScope::SHOP, moduleName: self::MODULE),
            new ExtraPropertyDefinition(entityName: 'contact', propertyName: 'ms_contact_lang', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::LANG, moduleName: self::MODULE),
            new ExtraPropertyDefinition(entityName: 'contact', propertyName: 'ms_contact_shop', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::SHOP, moduleName: self::MODULE),
        ];
    }

    private function readShopValue(int $productId, ShopConstraint $shopConstraint): ?string
    {
        return $this->readValue('ms_shop', $productId, $shopConstraint);
    }

    private function readLangValue(int $productId, ShopConstraint $shopConstraint): ?string
    {
        $values = self::$reader->getExtraProperties('product', 'id_product', $productId, self::DEFAULT_LANG_ID, $shopConstraint);

        return $values[self::MODULE]['ms_lang'] ?? null;
    }

    private function readValue(string $propertyName, int $productId, ShopConstraint $shopConstraint): mixed
    {
        $values = self::$reader->getExtraProperties('product', 'id_product', $productId, self::DEFAULT_LANG_ID, $shopConstraint);

        return $values[self::MODULE][$propertyName] ?? null;
    }

    private function countRows(string $table, string $primaryKey, int $entityId): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . $table . '` WHERE `' . $primaryKey . '` = ' . $entityId
        );
    }
}
