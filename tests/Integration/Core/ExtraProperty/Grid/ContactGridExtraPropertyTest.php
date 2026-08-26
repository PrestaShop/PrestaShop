<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty\Grid;

use Db;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriterInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ContactFilters;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Regression test for the contact grid with a LANG-scoped extra property
 * (https://github.com/PrestaShop/PrestaShop/issues/41568): contact_lang has NO id_shop
 * column, so ps_contact_extra_lang mirrors a 2-column PK (id_contact, id_lang). The grid
 * join must not reference extra_lang.id_shop — doing so raised
 * "Unknown column 'extra_lang.id_shop' in 'on clause'" and 500'd the whole
 * Shop Parameters → Contact page.
 */
class ContactGridExtraPropertyTest extends KernelTestCase
{
    private const MODULE = 'extrapropertycontactgridtest';
    private const DEFAULT_LANG_ID = 1;

    private static ExtraPropertyRegistryInterface $registry;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        $container = self::getContainer();

        // The grid modifier consumes the ShopContext: give the builder the minimal state a
        // kernel test lacks (no HTTP request ran, so no context listener initialized it).
        $shopContextBuilder = $container->get(ShopContextBuilder::class);
        $shopContextBuilder->setShopId(1);
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop(1));

        self::$registry = $container->get(ExtraPropertyRegistryInterface::class);
        self::$registry->register(self::langDefinition());
    }

    public static function tearDownAfterClass(): void
    {
        self::$registry->unregister(self::langDefinition(), true);

        parent::tearDownAfterClass();
    }

    public function testContactGridRendersWithLangScopedExtraProperty(): void
    {
        $contactId = (int) Db::getInstance()->getValue('SELECT MIN(id_contact) FROM `' . _DB_PREFIX_ . 'contact`');
        $this->assertGreaterThan(0, $contactId, 'The test installation must provide at least one contact');

        /** @var ExtraPropertyWriterInterface $writer */
        $writer = self::getContainer()->get(ExtraPropertyWriterInterface::class);
        $writer->writeAll('contact', 'id_contact', $contactId, [self::MODULE => [
            'job_title' => [self::DEFAULT_LANG_ID => 'Chief Testing Officer'],
        ]], ShopConstraint::allShops());

        /** @var GridDataFactoryInterface $gridDataFactory */
        $gridDataFactory = self::getContainer()->get('prestashop.core.grid.data_provider.contacts');
        $gridData = $gridDataFactory->getData(new ContactFilters(ContactFilters::getDefaults()));

        $this->assertGreaterThan(0, $gridData->getRecordsTotal());

        $records = iterator_to_array($gridData->getRecords());
        $recordsById = array_column($records, null, 'id_contact');
        $this->assertArrayHasKey($contactId, $recordsById);
        $this->assertSame(
            'Chief Testing Officer',
            $recordsById[$contactId][self::langDefinition()->getFieldName()]
        );
    }

    private static function langDefinition(): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'contact',
            propertyName: 'job_title',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::LANG,
            moduleName: self::MODULE,
            size: 128,
            associatedGrids: ['contact'],
            labelWording: 'Job title',
            labelDomain: 'Modules.Extrapropertycontactgridtest.Admin',
        );
    }
}
