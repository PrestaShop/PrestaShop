<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Customer;
use Db;
use ObjectModel;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * Marking a field required while the setting that shows it is off used to make registration
 * impossible: the form never renders the field, nothing is submitted for it, and
 * ObjectModel::validateFieldsRequiredDatabase() refuses the account.
 */
class CustomerRequiredFieldsTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousConfiguration = [];

    /** @var int[] */
    private array $insertedRequiredFields = [];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['PS_CUSTOMER_OPTIN', 'PS_CUSTOMER_BIRTHDATE'] as $key) {
            $this->previousConfiguration[$key] = Configuration::get($key);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->previousConfiguration as $key => $value) {
            Configuration::updateValue($key, $value);
        }

        foreach ($this->insertedRequiredFields as $id) {
            Db::getInstance()->delete('required_field', 'id_required_field = ' . (int) $id);
        }
        $this->insertedRequiredFields = [];

        parent::tearDown();
    }

    /**
     * @dataProvider hiddenFields
     */
    public function testAFieldTheShopDoesNotCollectIsNotRequired(string $field, string $setting): void
    {
        $this->requireField($field);

        Configuration::updateValue($setting, 0);
        $this->forgetTheRequiredFieldCache();
        $this->assertNotContains(
            $field,
            (new Customer())->getCachedFieldsRequiredDatabase(),
            $field . ' cannot be required while ' . $setting . ' hides it'
        );

        Configuration::updateValue($setting, 1);
        $this->forgetTheRequiredFieldCache();
        $this->assertContains(
            $field,
            (new Customer())->getCachedFieldsRequiredDatabase(),
            $field . ' stays required once the shop collects it again'
        );
    }

    public function hiddenFields(): array
    {
        return [
            'partner offers' => ['optin', 'PS_CUSTOMER_OPTIN'],
            'birthdate' => ['birthday', 'PS_CUSTOMER_BIRTHDATE'],
        ];
    }

    /**
     * The required fields are cached in a static, so a row inserted by this test is invisible until
     * the cache is dropped.
     */
    private function forgetTheRequiredFieldCache(): void
    {
        $cache = new ReflectionProperty(ObjectModel::class, 'fieldsRequiredDatabase');
        $cache->setAccessible(true);
        $cache->setValue(null, null);
    }

    private function requireField(string $field): void
    {
        $existing = (int) Db::getInstance()->getValue(
            'SELECT id_required_field FROM ' . _DB_PREFIX_ . "required_field
             WHERE object_name = 'Customer' AND field_name = '" . pSQL($field) . "'"
        );

        if ($existing) {
            return;
        }

        Db::getInstance()->insert('required_field', ['object_name' => 'Customer', 'field_name' => $field]);
        $this->insertedRequiredFields[] = (int) Db::getInstance()->Insert_ID();
    }
}
