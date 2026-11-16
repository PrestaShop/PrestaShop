<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Controllers;

use Cart;
use Context;
use Controller;
use Db;
use Product;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tools;
use UploadControllerCore;

/**
 * A customization file must only be served to the owner of the cart it was uploaded in, or to a
 * visitor proving ownership of the related order with its reference AND secure key. The order
 * reference alone is not a secret (GHSA-fg9q-mm4c-h5xc).
 */
class UploadControllerCoreTest extends KernelTestCase
{
    private const FILE_HASH = '0123456789abcdef0123456789abcdef';
    private const WRONG_SECURE_KEY = 'ffffffffffffffffffffffffffffffff';
    private const STRANGER_GUEST_ID = 987654;

    /**
     * @var array<string, string> id_cart, id_customer, id_guest, reference, secure_key of the victim order
     */
    private $victimOrder;

    /**
     * @var int
     */
    private $customizationId;

    /**
     * @var Cart|null
     */
    private $previousContextCart;

    protected function setUp(): void
    {
        parent::setUp();

        $victimOrder = Db::getInstance()->getRow(
            'SELECT c.id_cart, c.id_customer, c.id_guest, o.reference, o.secure_key
            FROM ' . _DB_PREFIX_ . 'orders o
            INNER JOIN ' . _DB_PREFIX_ . 'cart c ON c.id_cart = o.id_cart
            WHERE o.secure_key != ""
            ORDER BY o.id_order ASC'
        );
        if (!$victimOrder) {
            self::markTestSkipped('The test database has no order to attach a customization to.');
        }
        $this->victimOrder = $victimOrder;

        Db::getInstance()->insert('customization', [
            'id_cart' => (int) $victimOrder['id_cart'],
            'id_product' => 1,
            'quantity' => 1,
            'in_cart' => 0,
        ]);
        $this->customizationId = (int) Db::getInstance()->Insert_ID();
        Db::getInstance()->insert('customized_data', [
            'id_customization' => $this->customizationId,
            'type' => Product::CUSTOMIZE_FILE,
            'index' => 0,
            'value' => self::FILE_HASH,
        ]);

        $this->previousContextCart = Context::getContext()->cart;
        Tools::resetRequest();
        $_GET = [];
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete('customized_data', 'id_customization = ' . $this->customizationId);
        Db::getInstance()->delete('customization', 'id_customization = ' . $this->customizationId);
        Context::getContext()->cart = $this->previousContextCart;
        $_GET = [];

        parent::tearDown();
    }

    public function testTheOrderReferenceAloneDoesNotUnlockTheFile(): void
    {
        $this->browseAsStranger();
        $_GET['reference'] = $this->victimOrder['reference'];

        self::assertFalse($this->isCustomization(self::FILE_HASH));
        self::assertFalse($this->isCustomization(self::FILE_HASH . '_small'));
    }

    public function testAWrongSecureKeyDoesNotUnlockTheFile(): void
    {
        $this->browseAsStranger();
        $_GET['reference'] = $this->victimOrder['reference'];
        $_GET['secure_key'] = self::WRONG_SECURE_KEY;

        self::assertFalse($this->isCustomization(self::FILE_HASH));
    }

    public function testAStrangerWithoutAnyProofIsRefused(): void
    {
        $this->browseAsStranger();

        self::assertFalse($this->isCustomization(self::FILE_HASH));
    }

    public function testTheOrderReferenceAndSecureKeyUnlockTheFile(): void
    {
        $this->browseAsStranger();
        $_GET['reference'] = $this->victimOrder['reference'];
        $_GET['secure_key'] = $this->victimOrder['secure_key'];

        self::assertTrue($this->isCustomization(self::FILE_HASH));
        self::assertTrue($this->isCustomization(self::FILE_HASH . '_small'));
        self::assertFalse($this->isCustomization('deadbeefdeadbeefdeadbeefdeadbeef'));
    }

    public function testTheCartOwnerStillGetsTheFileWithoutAnyReference(): void
    {
        $this->browseAs((int) $this->victimOrder['id_customer'], (int) $this->victimOrder['id_guest']);

        self::assertTrue($this->isCustomization(self::FILE_HASH));
        self::assertTrue($this->isCustomization(self::FILE_HASH . '_small'));
    }

    private function browseAsStranger(): void
    {
        $this->browseAs(0, self::STRANGER_GUEST_ID);
    }

    private function browseAs(int $customerId, int $guestId): void
    {
        $cart = new Cart();
        $cart->id_customer = $customerId;
        $cart->id_guest = $guestId;
        Context::getContext()->cart = $cart;
    }

    /**
     * The controller is bound to the HTTP layer in its constructor, so its authorization check
     * is exercised directly.
     */
    private function isCustomization(string $filename): bool
    {
        $controllerReflection = new ReflectionClass(UploadControllerCore::class);
        $controller = $controllerReflection->newInstanceWithoutConstructor();

        $contextProperty = new ReflectionProperty(Controller::class, 'context');
        $contextProperty->setAccessible(true);
        $contextProperty->setValue($controller, Context::getContext());

        $filenameProperty = $controllerReflection->getProperty('filename');
        $filenameProperty->setAccessible(true);
        $filenameProperty->setValue($controller, $filename);

        $method = $controllerReflection->getMethod('isCustomization');
        $method->setAccessible(true);

        return $method->invoke($controller);
    }
}
