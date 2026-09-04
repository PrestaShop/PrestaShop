<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use Configuration;
use ObjectModel;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class ObjectModelTest extends TestCase
{
    protected function setUp(): void
    {
        // Configure the default language without loading configuration from the database
        Configuration::set('PS_LANG_DEFAULT', 1, 0, 0);
        (new ReflectionProperty(Configuration::class, '_initialized'))->setValue(true);
    }

    protected function tearDown(): void
    {
        // Reset the temporary configuration for the following tests
        Configuration::clearConfigurationCacheForTesting();
    }

    public function testItPreservesZeroMultilangValueWhenFormattingFields(): void
    {
        // Prepare a required multilingual field with zero in a non-default language
        $defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');
        $secondLanguageId = $defaultLanguageId + 1;
        $objectModel = new MultilangObjectModelForTesting();
        $objectModel->name = [
            $defaultLanguageId => '10',
            $secondLanguageId => '0',
        ];

        // Verify that zero is preserved instead of being replaced by the default value
        $this->assertSame(['name' => '0'], $objectModel->formatLanguageFields($secondLanguageId));
    }
}

class MultilangObjectModelForTesting extends ObjectModel
{
    public $name;

    public static $definition = [
        'table' => 'multilang_object_model_for_testing',
        'primary' => 'id_multilang_object_model_for_testing',
        'multilang' => true,
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => true],
        ],
    ];

    /**
     * Formats the multilingual fields for the requested language.
     *
     * @param int $languageId
     *
     * @return array
     */
    public function formatLanguageFields(int $languageId): array
    {
        return $this->formatFields(self::FORMAT_LANG, $languageId);
    }
}
