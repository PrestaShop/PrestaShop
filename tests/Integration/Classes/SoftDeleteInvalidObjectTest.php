<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Address;
use Configuration;
use Db;
use PHPUnit\Framework\TestCase;
use PrestaShopException;

/**
 * An address left behind by an import, an old version or a checkout module can hold a value that no
 * longer passes validation. Editing it in the back office soft deletes the previous row, and that
 * used to validate the whole object, so the merchant could neither correct the address nor remove
 * it.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/37917
 */
class SoftDeleteInvalidObjectTest extends TestCase
{
    private const FAULTY_PHONE = 'not a phone <<>>';

    private int $addressId;

    protected function setUp(): void
    {
        parent::setUp();

        $address = new Address();
        $address->id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
        $address->alias = 'Soft delete fixture';
        $address->lastname = 'Doe';
        $address->firstname = 'John';
        $address->address1 = '1 street';
        $address->city = 'City';
        $address->add();

        $this->addressId = (int) $address->id;

        // Straight to the column: the point is an object that is already stored in an invalid state.
        Db::getInstance()->update('address', ['phone' => self::FAULTY_PHONE], 'id_address = ' . $this->addressId);
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete('address', 'id_address = ' . $this->addressId);

        parent::tearDown();
    }

    public function testTheFixtureIsInvalidToBeginWith(): void
    {
        $address = new Address($this->addressId);

        $this->assertFalse($address->validateFields(false));
    }

    public function testAnInvalidObjectCanStillBeSoftDeleted(): void
    {
        $address = new Address($this->addressId);

        try {
            $address->softDelete();
        } catch (PrestaShopException $e) {
            $this->fail('Soft deleting an invalid object must not be blocked: ' . $e->getMessage());
        }

        $this->assertSame('1', (string) Db::getInstance()->getValue(
            'SELECT deleted FROM ' . _DB_PREFIX_ . 'address WHERE id_address = ' . $this->addressId
        ));
    }

    public function testSoftDeletingLeavesTheOtherColumnsAlone(): void
    {
        (new Address($this->addressId))->softDelete();

        $this->assertSame(self::FAULTY_PHONE, (string) Db::getInstance()->getValue(
            'SELECT phone FROM ' . _DB_PREFIX_ . 'address WHERE id_address = ' . $this->addressId
        ));
    }
}
