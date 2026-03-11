<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Classes;

use Configuration;
use ObjectModel;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Minimal ObjectModel subclass used to test formatFields behavior.
 */
class TestableObjectModel extends ObjectModel
{
    /** @var string|array */
    public $name;

    /** @var array */
    public static $definition = [
        'table' => 'testable_object',
        'primary' => 'id_testable_object',
        'multilang' => true,
        'fields' => [
            'name' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'required' => false,
            ],
        ],
    ];

    public function formatFieldsPublic(int $type, ?int $id_lang = null): array
    {
        return $this->formatFields($type, $id_lang);
    }
}

class ObjectModelTest extends TestCase
{
    private const DEFAULT_LANG_ID = 1;
    private const OTHER_LANG_ID = 2;

    protected function setUp(): void
    {
        // Pre-populate Configuration static cache to avoid database calls during testing
        $reflection = new ReflectionClass(Configuration::class);

        $initialized = $reflection->getProperty('_initialized');
        $initialized->setAccessible(true);
        $initialized->setValue(null, true);

        $newCacheGlobal = $reflection->getProperty('_new_cache_global');
        $newCacheGlobal->setAccessible(true);
        $newCacheGlobal->setValue(null, [
            'PS_LANG_DEFAULT' => [0 => (string) self::DEFAULT_LANG_ID],
        ]);

        $newCacheShop = $reflection->getProperty('_new_cache_shop');
        $newCacheShop->setAccessible(true);
        $newCacheShop->setValue(null, null);

        $newCacheGroup = $reflection->getProperty('_new_cache_group');
        $newCacheGroup->setAccessible(true);
        $newCacheGroup->setValue(null, null);
    }

    protected function tearDown(): void
    {
        Configuration::clearConfigurationCacheForTesting();
        ObjectModel::resetStaticCache();
    }

    /**
     * Regression test for https://github.com/PrestaShop/PrestaShop/issues/40101
     *
     * A non-required multilang field (e.g. cart rule name) must fall back to the
     * default language value when its value is empty for the requested language.
     * Previously the fallback was incorrectly gated on the `required` flag.
     */
    public function testNonRequiredMultilangFieldFallsBackToDefaultLanguage(): void
    {
        $model = new TestableObjectModel();
        $model->name = [
            self::DEFAULT_LANG_ID => 'My Discount',
            self::OTHER_LANG_ID => '',
        ];

        $fields = $model->formatFieldsPublic(ObjectModel::FORMAT_LANG, self::OTHER_LANG_ID);

        $this->assertSame(
            'My Discount',
            $fields['name'],
            'A non-required multilang field should fall back to the default language value when its value is empty for the requested language.'
        );
    }

    public function testMultilangFieldReturnsValueWhenSetForRequestedLanguage(): void
    {
        $model = new TestableObjectModel();
        $model->name = [
            self::DEFAULT_LANG_ID => 'My Discount',
            self::OTHER_LANG_ID => 'Mon bon de réduction',
        ];

        $fields = $model->formatFieldsPublic(ObjectModel::FORMAT_LANG, self::OTHER_LANG_ID);

        $this->assertSame('Mon bon de réduction', $fields['name']);
    }

    public function testMultilangFieldReturnsEmptyStringWhenBothLanguagesAreEmpty(): void
    {
        $model = new TestableObjectModel();
        $model->name = [
            self::DEFAULT_LANG_ID => '',
            self::OTHER_LANG_ID => '',
        ];

        $fields = $model->formatFieldsPublic(ObjectModel::FORMAT_LANG, self::OTHER_LANG_ID);

        $this->assertSame('', $fields['name']);
    }

    public function testDefaultLanguageFieldReturnsItsOwnValue(): void
    {
        $model = new TestableObjectModel();
        $model->name = [
            self::DEFAULT_LANG_ID => 'My Discount',
            self::OTHER_LANG_ID => '',
        ];

        $fields = $model->formatFieldsPublic(ObjectModel::FORMAT_LANG, self::DEFAULT_LANG_ID);

        $this->assertSame('My Discount', $fields['name']);
    }
}
