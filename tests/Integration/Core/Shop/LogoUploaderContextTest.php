<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Shop;

use Configuration;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfigurationInterface;
use PrestaShop\PrestaShop\Core\Shop\LogoUploader;
use ReflectionClass;
use ReflectionMethod;
use Shop;

/**
 * updateInMultiShopContext() reads the logo of three shop scopes. It used to do that by switching the
 * global shop context three times and never restoring it, which had two consequences:
 *
 *  - the last switch handed the context shop id straight to the int-typed Shop::getGroupIdFromShopId(),
 *    so with no shop in context the whole request died with a TypeError;
 *  - because the method never restored what it changed, everything after it ran with a context that no
 *    longer described the shop being edited, and Adapter\Configuration::get() then threw
 *    "Shop group id 0 is invalid" from the error page - hiding the original failure.
 *
 * Reading each scope through Configuration::get()'s shop parameters removes both.
 */
class LogoUploaderContextTest extends TestCase
{
    /**
     * @var array{0: mixed, 1: mixed, 2: mixed}
     */
    private $savedContext;

    protected function setUp(): void
    {
        parent::setUp();
        $this->savedContext = [
            self::readShopStatic('context'),
            self::readShopStatic('context_id_shop'),
            self::readShopStatic('context_id_shop_group'),
        ];
    }

    protected function tearDown(): void
    {
        // Shop's context lives in statics, so it outlives the test unless it is put back by hand.
        self::writeShopStatic('context', $this->savedContext[0]);
        self::writeShopStatic('context_id_shop', $this->savedContext[1]);
        self::writeShopStatic('context_id_shop_group', $this->savedContext[2]);

        parent::tearDown();
    }

    /**
     * The reported state: a shop context whose shop id never got resolved, which is what a merchant hit
     * when saving a logo for a newly added shop.
     */
    public function testItSurvivesSaveWithAnUnresolvedShopContext(): void
    {
        // Built while the context is still healthy, as it is in a real request: constructing a Shop
        // itself reads configuration, which is not possible once the context shop id is gone.
        $uploader = $this->buildUploader();

        self::writeShopStatic('context', Shop::CONTEXT_SHOP);
        self::writeShopStatic('context_id_shop', null);
        self::writeShopStatic('context_id_shop_group', null);

        $idShop = null;
        $idShopGroup = null;
        $this->invokeUpdateInMultiShopContext($uploader, $idShop, $idShopGroup);

        // Reaching here at all is the assertion: this used to raise
        // "getGroupIdFromShopId(): Argument #1 ($shopId) must be of type int, null given".
        $this->addToAssertionCount(1);
    }

    /**
     * Whatever happens inside, the shop context the rest of the request depends on has to come out of
     * the call exactly as it went in. Without that, an aborted logo save leaves every later
     * Configuration::get() building a ShopConstraint from a context that no longer makes sense.
     */
    public function testItLeavesTheGlobalShopContextUntouched(): void
    {
        $uploader = $this->buildUploader();

        self::writeShopStatic('context', Shop::CONTEXT_SHOP);
        self::writeShopStatic('context_id_shop', null);
        self::writeShopStatic('context_id_shop_group', null);

        $idShop = null;
        $idShopGroup = null;
        $this->invokeUpdateInMultiShopContext($uploader, $idShop, $idShopGroup);

        $this->assertSame(Shop::CONTEXT_SHOP, self::readShopStatic('context'), 'The shop context type was changed.');
        $this->assertNull(self::readShopStatic('context_id_shop'), 'The context shop id was changed.');
        $this->assertNull(self::readShopStatic('context_id_shop_group'), 'The context shop group id was changed.');
    }

    private function invokeUpdateInMultiShopContext(LogoUploader $uploader, &$idShop, &$idShopGroup): void
    {
        $method = new ReflectionMethod($uploader, 'updateInMultiShopContext');
        $method->setAccessible(true);
        $arguments = [&$idShop, &$idShopGroup, 'PS_LOGO'];
        $method->invokeArgs($uploader, $arguments);
    }

    private function buildUploader(): LogoUploader
    {
        return new LogoUploader(
            new Shop((int) Configuration::get('PS_SHOP_DEFAULT')),
            $this->createMock(ImageFormatConfigurationInterface::class),
            _PS_IMG_DIR_
        );
    }

    /**
     * @return mixed
     */
    private static function readShopStatic(string $property)
    {
        $reflected = (new ReflectionClass(Shop::class))->getProperty($property);
        $reflected->setAccessible(true);

        return $reflected->getValue();
    }

    /**
     * @param mixed $value
     */
    private static function writeShopStatic(string $property, $value): void
    {
        $reflected = (new ReflectionClass(Shop::class))->getProperty($property);
        $reflected->setAccessible(true);
        $reflected->setValue(null, $value);
    }
}
